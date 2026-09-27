 <!-- <?php -->

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\FinancialAccount;
// use App\Models\Payment;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class AccountTransferController extends Controller
// {
    // ==========================================
    // 1. عرض صفحة التحويل والسجل
    // ==========================================
    // public function index()
    // {
        // بنجيب الحسابات المفعلة فقط (بناءً على حقل is_active في الكود بتاعك)
    //     $accounts = FinancialAccount::where('is_active', 1)->get(); 
        
    //     // بنجيب التحويلات الأخيرة للعرض في الجدول السفلي
    //     $transfers = Payment::with(['financialAccount', 'payable'])
    //                         ->where('payment_type', 'transfer_out')
    //                         ->latest()
    //                         ->get();

    //     return view('backend.financial_accounts.transfer', compact('accounts', 'transfers'));
    // }

    // ==========================================
    // 2. تنفيذ التحويل المالي بطريقة Transaction
    // ==========================================
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'from_account_id' => 'required|exists:financial_accounts,id',
    //         'to_account_id'   => 'required|exists:financial_accounts,id|different:from_account_id',
    //         'amount'          => 'required|numeric|min:0.001',
    //         'notes'           => 'nullable|string',
    //     ]);

        // try {
        //     DB::transaction(function () use ($request) {
        //         $fromAccount = FinancialAccount::findOrFail($request->from_account_id);
        //         $toAccount   = FinancialAccount::findOrFail($request->to_account_id);

        //         // التأكد من وجود رصيد كافي في الحساب المصدر
        //         if ($fromAccount->balance < $request->amount) {
        //             throw new \Exception("عفواً، الرصيد غير كافٍ في حساب ({$fromAccount->name_ar}) لإتمام عملية التحويل.");
        //         }

        //         $reference = 'TRF-' . strtoupper(uniqid());

                // قيد السحب (الصادر من الحساب الأول)
                // Payment::create([
                //     'payable_type'         => 'App\Models\FinancialAccount',
                //     'payable_id'           => $toAccount->id,
                //     'financial_account_id' => $fromAccount->id,
                //     'user_id'              => auth()->id() ?? 1,
                //     'payment_type'         => 'transfer_out',
                //     'amount'               => -$request->amount, // بالسالب لأنها خارجة
                //     'payment_date'         => now(),
                    // بنخزنها كـ Array عشان إنت عاملها Cast في الموديل
                //     'transaction_details'  => ['transfer_to' => $toAccount->name_ar], 
                //     'receipt_number'       => $reference . '-OUT',
                //     'notes'                => $request->notes ?? 'تحويل رصيد داخلي',
                // ]);

                // $fromAccount->decrement('balance', $request->amount);

                // قيد الإيداع (الوارد للحساب الثاني)
    //             Payment::create([
    //                 'payable_type'         => 'App\Models\FinancialAccount',
    //                 'payable_id'           => $fromAccount->id,
    //                 'financial_account_id' => $toAccount->id,
    //                 'user_id'              => auth()->id() ?? 1,
    //                 'payment_type'         => 'transfer_in',
    //                 'amount'               => $request->amount, // بالموجب لأنها داخلة
    //                 'payment_date'         => now(),
    //                 'transaction_details'  => ['transfer_from' => $fromAccount->name_ar],
    //                 'receipt_number'       => $reference . '-IN',
    //                 'notes'                => $request->notes ?? 'تحويل رصيد داخلي',
    //             ]);

    //             $toAccount->increment('balance', $request->amount);
    //         });

    //         return redirect()->back()->with('success', 'تم تحويل الأموال بنجاح وتحديث أرصدة الحسابات.');

    //     } catch (\Exception $e) {
    //         return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    //     }
    // }
    // ==========================================
    // 3. عرض تقرير وتاريخ التحويلات
    // ==========================================
//     public function history(Request $request)
//     {
//         // بنجيب حركات الـ "صادر" فقط لأنها بتحتوي على الطرفين (من وإلى)
//         $query = Payment::with(['financialAccount', 'payable'])
//                         ->where('payment_type', 'transfer_out');

//         // فلترة بتاريخ البداية
//         if ($request->filled('start_date')) {
//             $query->whereDate('payment_date', '>=', $request->start_date);
//         }

//         // فلترة بتاريخ النهاية
//         if ($request->filled('end_date')) {
//             $query->whereDate('payment_date', '<=', $request->end_date);
//         }

//         $transfers = $query->latest()->get();

//         return view('backend.financial_accounts.transfer_history', compact('transfers'));
//     }
// } -->