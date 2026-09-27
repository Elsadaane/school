<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Branche;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransferController extends Controller
{
    // ==========================================
    // 1. عرض قائمة التحويلات
    // ==========================================
    public function index(Request $request)
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_stock_transfers'), 403, 'غير مصرح لك بإدارة التحويلات المخزنية.');

        $query = StockTransfer::with(['fromBranch', 'toBranch'])->latest();

        if ($request->filled('branch_id')) {
            $query->where(function($q) use ($request) {
                $q->where('from_branch_id', $request->branch_id)
                  ->orWhere('to_branch_id', $request->branch_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('transfer_number', 'like', '%' . $request->search . '%');
        }

        $transfers = $query->paginate(15)->withQueryString();
        $branches = Branche::where('is_active', 1)->get();

        return view('backend.stock_transfers.index', compact('transfers', 'branches'));
    }

    // ==========================================
    // 2. عرض شاشة الإضافة
    // ==========================================
    public function create()
    {
        abort_if(!auth()->user()->can('manage_stock_transfers'), 403);

        $branches = Branche::where('is_active', 1)->get();
        return view('backend.stock_transfers.create', compact('branches'));
    }

    // ==========================================
    // 3. دالة AJAX لجلب منتجات ملموسة لفرع معين
    // ==========================================
    public function getBranchProducts(Request $request)
    {
        abort_if(!auth()->user()->can('manage_stock_transfers'), 403);

        $branchId = $request->branch_id;
        $search = $request->q;

        if (!$branchId) {
            return response()->json([]);
        }

        $products = Product::where('branch_id', $branchId)
            ->where('type', 'product')
            ->where('quantity', '>', 0)
            ->when($search, function($q) use ($search) {
                $q->where(function($subQ) use ($search) {
                    $subQ->where('name_ar', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                });
            })->limit(20)->get();

        $formatted = $products->map(function($prod) {
            return [
                'id' => $prod->id,
                'text' => $prod->name_ar . ' | كود: ' . ($prod->sku ?? '-'),
                'available_qty' => $prod->quantity
            ];
        });

        return response()->json($formatted);
    }

    // ==========================================
    // 4. حفظ التحويل المخزني الجديد
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_stock_transfers'), 403);

        $request->validate([
            'from_branch_id'     => 'required|exists:branches,id',
            'to_branch_id'       => 'required|exists:branches,id|different:from_branch_id',
            'transfer_date'      => 'required|date',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $transferNumber = 'TRF-' . date('Ymd') . '-' . strtoupper(Str::random(4));

                $transfer = StockTransfer::create([
                    'transfer_number'  => $transferNumber,
                    'from_branch_id'   => $request->from_branch_id,
                    'to_branch_id'     => $request->to_branch_id,
                    'user_id'          => auth()->id(),
                    'transfer_date'    => $request->transfer_date,
                    'status'           => 'pending', // 🔥 تم التعديل لتكون قيد الانتظار أولاً 🔥
                    'notes'            => $request->notes,
                ]);

                foreach ($request->items as $item) {
                    // 🔥 حماية التزامن لمنع بيع المنتج أثناء تحويله 🔥
                    $product = Product::where('branch_id', $request->from_branch_id)
                                      ->where('id', $item['product_id'])
                                      ->lockForUpdate()
                                      ->firstOrFail();

                    if ($product->quantity < $item['quantity']) {
                        throw new \Exception("رصيد المنتج ({$product->name_ar}) غير كافٍ في فرع الإرسال!");
                    }

                    StockTransferItem::create([
                        'stock_transfer_id' => $transfer->id,
                        'product_id'        => $product->id,
                        'quantity'          => $item['quantity'],
                    ]);

                    // خصم الكمية لحجزها من الفرع المُرسل
                    $product->decrement('quantity', $item['quantity']);
                }
            });

            return response()->json([
                'status' => 'success', 
                'message' => 'تم إنشاء طلب التحويل بنجاح، وهو الآن بانتظار الشحن!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ==========================================
    // 5. عرض تفاصيل التحويل (Show)
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('manage_stock_transfers'), 403);

        $transfer = StockTransfer::with([
            'fromBranch', 
            'toBranch', 
            'user', 
            'shippedBy', 
            'receivedBy', 
            'items.product'
        ])->findOrFail($id);

        return view('backend.stock_transfers.show', compact('transfer'));
    }

    // ==========================================
    // 6. شحن التحويل 🔥
    // ==========================================
    public function ship($id)
    {
        abort_if(!auth()->user()->can('manage_stock_transfers'), 403);

        try {
            $transfer = StockTransfer::findOrFail($id);

            if ($transfer->status !== 'pending') {
                throw new \Exception('مرفوض: لا يمكن شحن إلا الطلبات التي بحالة "قيد الانتظار".');
            }

            $transfer->update([
                'status'     => 'shipped',
                'shipped_at' => now(),
                'shipped_by' => auth()->id()
            ]);

            return response()->json([
                'status' => 'success', 
                'message' => 'تم تحويل حالة الطلب إلى جاري الشحن بنجاح!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ==========================================
    // 7. استلام التحويل (إضافة الكميات للفرع الوجهة)
    // ==========================================
    public function receive($id)
    {
        abort_if(!auth()->user()->can('manage_stock_transfers'), 403);

        try {
            DB::transaction(function () use ($id) {
                $transfer = StockTransfer::with('items.product')->lockForUpdate()->findOrFail($id);
                
                if ($transfer->status === 'completed') {
                    throw new \Exception('تم استلام هذه الشحنة مسبقاً.');
                }
                if ($transfer->status !== 'shipped') {
                    throw new \Exception('لا يمكن استلام شحنة ليست في حالة "جاري الشحن".');
                }

                foreach ($transfer->items as $item) {
                    $sourceProduct = $item->product;
                    
                    // 🔥 حماية التزامن للفرع المُستلم 🔥
                    $destinationProduct = Product::where('branch_id', $transfer->to_branch_id)
                                                 ->where('sku', $sourceProduct->sku)
                                                 ->lockForUpdate()
                                                 ->first();

                    if ($destinationProduct) {
                        $destinationProduct->increment('quantity', $item->quantity);
                    } else {
                        $newProduct = $sourceProduct->replicate();
                        $newProduct->branch_id = $transfer->to_branch_id;
                        $newProduct->quantity = $item->quantity;
                        $newProduct->sold_quantity = 0;
                        $newProduct->save();
                    }
                }

                $transfer->update([
                    'status'      => 'completed', 
                    'received_at' => now(), 
                    'received_by' => auth()->id()
                ]);
            });

            return response()->json([
                'status' => 'success', 
                'message' => 'تم استلام الشحنة وإضافة الكميات لمخزن الفرع بنجاح!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ==========================================
    // 8. إلغاء التحويل (إرجاع الكميات للفرع المُرسِل)
    // ==========================================
    public function cancel($id)
    {
        abort_if(!auth()->user()->can('manage_stock_transfers'), 403);

        try {
            DB::transaction(function () use ($id) {
                $transfer = StockTransfer::with('items.product')->lockForUpdate()->findOrFail($id);
                
                if ($transfer->status === 'cancelled') {
                    throw new \Exception('تم إلغاء هذه الشحنة مسبقاً واسترجاع عُهدتها.');
                }
                if ($transfer->status === 'completed') {
                    throw new \Exception('مرفوض أمنياً: لا يمكن إلغاء التحويل بعد استلامه. يمكنك عمل طلب تحويل عكسي.');
                }

                foreach ($transfer->items as $item) {
                    if ($item->product) {
                        // 🔥 حماية التزامن أثناء الاسترجاع 🔥
                        $productToReturn = Product::lockForUpdate()->find($item->product->id);
                        if ($productToReturn) {
                            $productToReturn->increment('quantity', $item->quantity);
                        }
                    }
                }

                $transfer->update(['status' => 'cancelled']);
            });

            return response()->json([
                'status' => 'success', 
                'message' => 'تم إلغاء الشحنة واسترجاع الكميات للفرع بنجاح!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ==========================================
    // 9. الحذف النهائي (Destroy)
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_stock_transfers'), 403);

        try {
            DB::transaction(function () use ($id) {
                $transfer = StockTransfer::with('items.product')->lockForUpdate()->findOrFail($id);

                if ($transfer->status === 'completed') {
                    throw new \Exception('لا يمكن حذف تحويل مكتمل وتم استلامه.');
                }

                if ($transfer->status === 'shipped' || $transfer->status === 'pending') {
                    foreach ($transfer->items as $item) {
                        if ($item->product) {
                            $productToReturn = Product::lockForUpdate()->find($item->product->id);
                            if ($productToReturn) {
                                $productToReturn->increment('quantity', $item->quantity);
                            }
                        }
                    }
                }

                $transfer->delete();
            });

            return response()->json([
                'status' => 'success', 
                'message' => 'تم حذف سجل التحويل نهائياً واسترجاع الكميات المعلقة!'
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    // ==========================================
    // 10. طباعة إذن التحويل (Print)
    // ==========================================
    public function print($id)
    {
        abort_if(!auth()->user()->can('manage_stock_transfers'), 403);

        $transfer = StockTransfer::with([
            'fromBranch', 
            'toBranch', 
            'user', 
            'items.product'
        ])->findOrFail($id);

        return view('backend.stock_transfers.print', compact('transfer'));
    }
}