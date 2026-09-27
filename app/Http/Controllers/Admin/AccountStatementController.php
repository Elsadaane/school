<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentContract;
use App\Models\StoreInvoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AccountStatementController extends Controller
{
    // ==========================================
    // 🔥 شاشة اختيار الطالب لعرض كشف حسابه 🔥
    // ==========================================
    public function index()
    {
        // ✅ جلب الحقول الضرورية فقط لتسريع تحميل صفحة البحث
        $students = Student::with('parent:id,name_ar,phone')
            ->select('id', 'first_name', 'second_name', 'tribe', 'parent_id')
            ->get();

        return view('backend.reports.student_statement_index', compact('students'));
    }

    // ==========================================
    // 🔥 توليد كشف الحساب التفصيلي للطالب 🔥
    // ==========================================
    public function show(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        // ✅ جلب الطالب مع كل العلاقات المطلوبة
        $student = Student::with([
            'parent:id,name_ar,phone',
            'grade:id,name_ar',
            'section:id,name_ar',
            'contracts.academicYear:id,name',
            'contracts.installments.payments'
        ])->findOrFail($request->student_id);

        $transactions = collect();

        // ✅ فلترة العقود النشطة فقط (contract_status = 'active')
        $activeContracts = $student->contracts->filter(fn($c) => $c->contract_status === 'active');

        // ==========================================
        // 1. المديونيات (العقود) - مدين (Debit)
        // ==========================================
        foreach ($activeContracts as $contract) {
            $transactions->push([
                'date' => Carbon::parse($contract->contract_date)->format('Y-m-d'),
                'type' => 'contract',
                'reference' => $contract->contract_number,
                'description' => 'رسوم دراسية (عقد) - ' . ($contract->academicYear->name ?? ''),
                'debit' => (float) $contract->grand_total,
                'credit' => 0,
            ]);
        }

        // ==========================================
        // 2. فواتير المقصف والزي - مدين (Debit)
        // جدول: store_invoices
        // ==========================================
        $storeInvoices = StoreInvoice::where('student_id', $student->id)->get();
        foreach ($storeInvoices as $invoice) {
            $transactions->push([
                'date' => Carbon::parse($invoice->invoice_date)->format('Y-m-d'),
                'type' => 'store_invoice',
                'reference' => $invoice->invoice_number,
                'description' => 'مشتريات مدرسية (مقصف/زي/كتب)',
                'debit' => (float) $invoice->grand_total,
                'credit' => 0,
            ]);
        }

        // ==========================================
        // 3. دفعات الأقساط - دائن (Credit)
        // جدول: installment_payments
        // حقل: amount_paid, payment_date, receipt_number
        // ==========================================
        foreach ($activeContracts as $contract) {
            foreach ($contract->installments as $installment) {
                // ✅ استخدام العلاقة المباشرة بدلاً من DB::table
                foreach ($installment->payments as $payment) {
                    $transactions->push([
                        'date' => Carbon::parse($payment->payment_date)->format('Y-m-d'),
                        'type' => 'installment_payment',
                        'reference' => $payment->receipt_number,
                        'description' => 'سداد قسط (عقد: ' . $contract->contract_number . ')',
                        'debit' => 0,
                        'credit' => (float) $payment->amount_paid,
                    ]);
                }
            }
        }

        // ==========================================
        // 4. الدفعات المباشرة (عربون/دفعة نقدية) - دائن (Credit)
        // جدول: payments (payable_type = Student)
        // ==========================================
        $directPayments = Payment::where('payable_type', Student::class)
            ->where('payable_id', $student->id)
            ->get();

        foreach ($directPayments as $payment) {
            $transactions->push([
                'date' => Carbon::parse($payment->payment_date)->format('Y-m-d'),
                'type' => 'direct_payment',
                'reference' => $payment->receipt_number,
                'description' => $payment->transaction_details ?? 'دفعة نقدية مباشرة / عربون',
                'debit' => 0,
                'credit' => (float) $payment->amount,
            ]);
        }

        // ==========================================
        // 5. ترتيب الحركات وحساب الرصيد التراكمي
        // ==========================================
        
        // ✅ الحل الجذري: تحويل Collection إلى array عادي قبل التعديل
        $transactions = $transactions->sortBy('date')->values()->toArray();

        $runningBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($transactions as $key => $transaction) {
            $runningBalance += $transaction['debit'] - $transaction['credit'];
            $transactions[$key]['balance'] = $runningBalance;
            $totalDebit += $transaction['debit'];
            $totalCredit += $transaction['credit'];
        }

        return view('backend.reports.student_statement_show', compact(
            'student',
            'transactions',
            'totalDebit',
            'totalCredit',
            'runningBalance'
        ));
    }
}