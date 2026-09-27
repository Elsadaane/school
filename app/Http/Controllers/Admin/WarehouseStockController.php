<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Branche;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WarehouseStockController extends Controller
{
    // ==========================================
    // 1. عرض أرصدة المخازن للفروع (الجرد)
    // ==========================================
    public function index(Request $request)
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_warehouse'), 403, 'غير مصرح لك بالاطلاع على جرد المخازن.');

        $branch_id = $request->branch_id;
        $low_stock = $request->low_stock;
        $search = $request->search;

        $query = Product::with('branch')->where('type', 'product');

        if ($branch_id) {
            $query->where('branch_id', $branch_id);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name_ar', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        if ($low_stock == '1') {
            $query->whereColumn('quantity', '<=', 'alert_quantity');
        } else {
            $query->where('quantity', '>', 0);
        }

        // 🌟 إضافة withQueryString للحفاظ على الفلاتر أثناء التنقل بين الصفحات 🌟
        $stocks = $query->paginate(20)->withQueryString();
        $all_branches = Branche::where('is_active', 1)->get();

        return view('backend.warehouse_stocks.index', compact('stocks', 'all_branches'));
    }

    // ==========================================
    // 2. عرض تفاصيل منتج معين في فرع (كارت الصنف)
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('manage_warehouse'), 403);

        $product = Product::with('branch')->findOrFail($id);
        
        return view('backend.warehouse_stocks.show', compact('product'));
    }

    // ==========================================
    // 3. التحويل المخزني السريع المباشر (Direct Transfer) 🔥
    // ==========================================
    public function transferStock(Request $request)
    {
        abort_if(!auth()->user()->can('manage_warehouse'), 403);

        $request->validate([
            'from_branch_id'    => 'required|exists:branches,id',
            'to_branch_id'      => 'required|exists:branches,id|different:from_branch_id',
            'product_id'        => 'required|exists:products,id',
            'transfer_quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                
                // 🌟 1. حماية التزامن المزدوجة (Locking Source Product) 🌟
                $sourceProduct = Product::where('branch_id', $request->from_branch_id)
                                        ->where('id', $request->product_id)
                                        ->lockForUpdate()
                                        ->firstOrFail();

                if ($sourceProduct->quantity < $request->transfer_quantity) {
                    throw new \Exception('عفواً، الرصيد المتاح لا يكفي للتحويل في الفرع المُرسل!');
                }

                // 🌟 2. حماية التزامن للمنتج المستقبِل (Locking Destination Product) 🌟
                $destinationProduct = Product::where('branch_id', $request->to_branch_id)
                                             ->where('sku', $sourceProduct->sku)
                                             ->lockForUpdate()
                                             ->first();

                // 3. خصم الكمية من الفرع المُرسل
                $sourceProduct->decrement('quantity', $request->transfer_quantity);

                // 4. إضافة الكمية للفرع المُستقبل
                if ($destinationProduct) {
                    $destinationProduct->increment('quantity', $request->transfer_quantity);
                } else {
                    $newProduct = $sourceProduct->replicate();
                    $newProduct->branch_id = $request->to_branch_id;
                    $newProduct->quantity = $request->transfer_quantity;
                    $newProduct->sold_quantity = 0;
                    $newProduct->save();
                }

                // 5. توثيق العملية في جدول التحويلات للحفاظ على الـ Audit Trail
                if (class_exists(\App\Models\StockTransfer::class)) {
                    $transfer = \App\Models\StockTransfer::create([
                        'transfer_number'  => 'TRF-DIR-' . strtoupper(Str::random(6)),
                        'from_branch_id'   => $request->from_branch_id,
                        'to_branch_id'     => $request->to_branch_id,
                        'transfer_date'    => now(),
                        'status'           => 'completed', // مكتمل فوراً لأنه تحويل مباشر
                        'user_id'          => auth()->id(),
                        'received_at'      => now(),
                        'notes'            => 'تم التحويل المباشر من شاشة جرد المخازن',
                    ]);
                    
                    \App\Models\StockTransferItem::create([
                        'stock_transfer_id' => $transfer->id,
                        'product_id'        => $sourceProduct->id,
                        'quantity'          => $request->transfer_quantity,
                    ]);
                }
            });

            return response()->json(['status' => 'success', 'message' => 'تم التحويل المخزني الفوري بنجاح بين الفروع!']);

        } catch (\Exception $e) {
            // كود 422 عشان مكتبة الـ SweetAlert أو الـ Axios تقدر تمسكه كخطأ وتطلعه لليوزر
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }
}