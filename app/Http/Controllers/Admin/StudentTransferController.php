<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentTransfer;
use App\Models\Student;
use App\Models\FinancialAccount;
use App\Models\Payment;
use App\Models\StudentTransferPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentTransferController extends Controller
{
    // ==========================================
    // 1. عرض كل طلبات التحويل
    // ==========================================
    public function index()
    {
        abort_if(!auth()->user()->can('manage_student_transfers'), 403, 'غير مصرح لك بإدارة تحويلات الطلاب.');

        $transfers = StudentTransfer::with(['student', 'fromBranch', 'toBranch'])->latest()->paginate(15);
        return view('backend.student_transfers.index', compact('transfers'));
    }

    // ==========================================
    // عرض تفاصيل طلب النقل (Show)
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('view_student_transfers'), 403);

        $transfer = StudentTransfer::with([
            'student.grade',
            'fromBranch',
            'toBranch'
        ])->findOrFail($id);

        return view('backend.student_transfers.show', compact('transfer'));
    }

    // ==========================================
    // إلغاء طلب النقل (Cancel)
    // ==========================================
    public function cancel($id)
    {
        abort_if(!auth()->user()->can('edit_student_transfers'), 403);

        $transfer = StudentTransfer::findOrFail($id);

        if (in_array($transfer->status, ['completed', 'cancelled', 'rejected'])) {
            return back()->withErrors(['error' => 'لا يمكن إلغاء الطلب في هذه المرحلة لأنه إما مكتمل أو ملغي مسبقاً.']);
        }

        $transfer->update(['status' => 'cancelled']);

        return back()->with('success', 'تم إيقاف وإلغاء طلب النقل بنجاح.');
    }

    // ==========================================
    // 2. إنشاء طلب تحويل جديد (الخطوة 1) + استلام دفعة
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_student_transfers'), 403);

        $request->validate([
            'student_id'           => 'required|exists:students,id',
            'to_branch_id'         => 'required|exists:branches,id',
            'transfer_reason'      => 'required|string|max:500',
            'transfer_fee'         => 'required|numeric|min:0',
            'signature_data'       => 'required|string', 
            // 🔥 الفاليديشن المالي الجديد
            'payment_type'         => 'required|in:full,installment',
            'paid_amount'          => 'required|numeric|min:0',
            'financial_account_id' => 'required_if:paid_amount,>,0|nullable|exists:financial_accounts,id',
            'payment_method'       => 'required_if:paid_amount,>,0|nullable|string',
        ]);

        $student = Student::findOrFail($request->student_id);

        if ($student->branch_id == $request->to_branch_id) {
            return back()->withErrors(['error' => 'عفواً، الطالب مقيد بالفعل في هذا الفرع.']);
        }

        $existingActiveTransfer = StudentTransfer::where('student_id', $student->id)
            ->whereNotIn('status', ['completed', 'rejected', 'cancelled'])
            ->exists();

        if ($existingActiveTransfer) {
            return back()->withErrors(['error' => 'هذا الطالب لديه طلب تحويل قيد الإجراء بالفعل.']);
        }

        try {
            DB::transaction(function () use ($request, $student) {
                // حساب المدفوع والمتبقي
                $fee = $request->transfer_fee;
                $paid = ($request->payment_type == 'full') ? $fee : $request->paid_amount;
                $remaining = $fee - $paid;

                $transfer = StudentTransfer::create([
                    'student_id'       => $student->id,
                    'from_branch_id'   => $student->branch_id,
                    'to_branch_id'     => $request->to_branch_id,
                    'transfer_reason'  => $request->transfer_reason,
                    'transfer_fee'     => $fee,
                    'paid_amount'      => $paid,
                    'remaining_amount' => $remaining,
                    'payment_method'   => $paid > 0 ? $request->payment_method : 'postponed',
                    'signature_data'   => $request->signature_data,
                    'transfer_date'    => now(),
                    'status'           => 'pending_ministry', // تم التوقيع، ننتظر الوزارة
                    'is_fee_paid'      => ($remaining <= 0) ? 1 : 0,
                    'created_by'       => auth()->id(),
                ]);

                // 🔥 لو تم دفع مبلغ وقت الطلب، نسجل سند قبض ونسمع في الخزنة
                if ($paid > 0) {
                    $receiptNumber = 'TRF-FIN-' . time() . rand(10,99);

                    StudentTransferPayment::create([
                        'student_transfer_id'  => $transfer->id,
                        'financial_account_id' => $request->financial_account_id,
                        'amount'               => $paid,
                        'payment_date'         => now(),
                        'payment_method'       => $request->payment_method,
                        'receipt_number'       => $receiptNumber,
                        'notes'                => 'دفعة مقدمة لرسوم نقل الطالب',
                        'received_by'          => auth()->id(),
                    ]);

                    $account = FinancialAccount::lockForUpdate()->find($request->financial_account_id);
                    if ($account) {
                        $account->increment('balance', $paid);
                    }

                    Payment::create([
                        'financial_account_id' => $request->financial_account_id,
                        'receipt_number'       => $receiptNumber,
                        'user_id'              => auth()->id(),
                        'payment_type'         => 'transfer_fee',
                        'payable_id'           => $student->id,
                        'payable_type'         => Student::class,
                        'amount'               => $paid,
                        'payment_date'         => now(),
                        'transaction_details'  => "سداد مقدم لرسوم تحويل الطالب للفرع الجديد",
                    ]);
                }
            });

            return back()->with('success', 'تم فتح ملف تحويل للطالب وتسجيل الإجراء المالي بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 3. اعتماد موافقة الوزارة (الخطوة 2)
    // ==========================================
    public function approveMinistry(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_student_transfers'), 403);

        $request->validate([
            'ministry_approval_number' => 'required|string|max:100',
            'ministry_approval_date'   => 'required|date',
        ]);

        $transfer = StudentTransfer::findOrFail($id);
        
        if (!in_array($transfer->status, ['pending_ministry', 'pending_parent'])) {
            return back()->withErrors(['error' => 'لا يمكن تسجيل موافقة الوزارة لطلب في غير مرحلته الصحيحة.']);
        }

        // توجيه ذكي: لو الرسوم مدفوعة بالكامل مسبقاً، انقل الطالب فوراً
        $newStatus = ($transfer->remaining_amount <= 0) ? 'completed' : 'pending_payment';

        DB::transaction(function () use ($request, $transfer, $newStatus) {
            $transfer->update([
                'ministry_approval_number' => $request->ministry_approval_number,
                'ministry_approval_date'   => $request->ministry_approval_date,
                'status'                   => $newStatus, 
                'approved_by'              => auth()->id()
            ]);

            if ($newStatus === 'completed') {
                $transfer->student->update([
                    'branch_id'  => $transfer->to_branch_id,
                    'section_id' => null,
                    'status'     => 'transferred'
                ]);
            }
        });

        $msg = $newStatus === 'completed' 
             ? 'تم تسجيل موافقة الوزارة ونقل الطالب بنجاح (نظراً لتسديده كامل الرسوم مسبقاً).'
             : 'تم تسجيل موافقة الوزارة. الطلب الآن بانتظار استكمال سداد الرسوم.';

        return back()->with('success', $msg);
    }

    // ==========================================
    // 4. استلام المتبقي من الرسوم والنقل الفعلي (الخطوة 3 والأخيرة)
    // ==========================================
    public function finalizeTransfer(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit_student_transfers'), 403);

        $transfer = StudentTransfer::with(['student', 'toBranch', 'fromBranch'])->findOrFail($id);

        $request->validate([
            'payment_status'       => 'required|in:paid,partial,deferred',
            'financial_account_id' => 'required_unless:payment_status,deferred|nullable|exists:financial_accounts,id',
            'payment_method'       => 'required_unless:payment_status,deferred|nullable|string',
            'paid_amount'          => 'required_if:payment_status,partial|nullable|numeric|min:0.1'
        ]);

        try {
            DB::transaction(function () use ($request, $transfer) {
                // Lock
                $transfer = StudentTransfer::lockForUpdate()->find($transfer->id);
                
                if ($transfer->status !== 'pending_payment') {
                    throw new \Exception('الطلب ليس في مرحلة الدفع.');
                }

                $fee = $transfer->remaining_amount; // نتعامل مع المتبقي فقط

                // حساب المدفوع
                if ($request->payment_status == 'paid') {
                    $paid = $fee;
                } elseif ($request->payment_status == 'partial') {
                    $paid = $request->paid_amount;
                } else {
                    $paid = 0; // آجل
                }

                $newRemaining = $fee - $paid;
                $totalPaidSoFar = $transfer->paid_amount + $paid;

                // تحديث الطلب
                $transfer->update([
                    'paid_amount'      => $totalPaidSoFar,
                    'remaining_amount' => $newRemaining,
                    'is_fee_paid'      => ($newRemaining <= 0) ? 1 : 0,
                    'last_payment_date'=> now(),
                    'status'           => 'completed' // الدفع كان آخر خطوة فينقل الطالب بكل الحالات (لو آجل بيفضل عليه مديونية بس بينتقل)
                ]);

                // تسجيل الإيراد لو دفع فلوس فعلاً
                if ($paid > 0) {
                    $receiptNumber = 'TRF-FIN-' . time() . rand(10,99);

                    StudentTransferPayment::create([
                        'student_transfer_id'  => $transfer->id,
                        'financial_account_id' => $request->financial_account_id,
                        'amount'               => $paid,
                        'payment_date'         => now(),
                        'payment_method'       => $request->payment_method,
                        'receipt_number'       => $receiptNumber,
                        'notes'                => 'سداد للرسوم المتبقية لإتمام التحويل',
                        'received_by'          => auth()->id(),
                    ]);

                    $account = FinancialAccount::lockForUpdate()->find($request->financial_account_id);
                    if ($account) {
                        $account->increment('balance', $paid);
                    }

                    Payment::create([
                        'financial_account_id' => $request->financial_account_id,
                        'receipt_number'       => $receiptNumber,
                        'user_id'              => auth()->id(),
                        'payment_type'         => 'transfer_fee',
                        'payable_id'           => $transfer->student_id,
                        'payable_type'         => Student::class,
                        'amount'               => $paid,
                        'payment_date'         => now(),
                        'transaction_details'  => "سداد متبقي رسوم تحويل الطالب من ({$transfer->fromBranch->name_ar}) إلى ({$transfer->toBranch->name_ar})",
                    ]);
                }

                // النقل الفعلي للطالب
                $transfer->student->update([
                    'branch_id'  => $transfer->to_branch_id,
                    'section_id' => null,
                    'status'     => 'transferred'
                ]);
            });

            return back()->with('success', 'تمت التسوية المالية (سواء سداد أو آجل) ونقل الطالب للفرع الجديد بنجاح!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء إتمام العملية: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 5. طباعة طلب التحويل
    // ==========================================
    public function print($id)
    {
        abort_if(!auth()->user()->can('manage_student_transfers'), 403);
        
        $transfer = StudentTransfer::with(['student.grade', 'fromBranch', 'toBranch'])->findOrFail($id);
        return view('backend.student_transfers.print', compact('transfer'));
    }

    // ==========================================
    // حذف طلب النقل نهائياً (Destroy)
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('delete_student_transfers'), 403);

        $transfer = StudentTransfer::findOrFail($id);

        if ($transfer->status == 'completed') {
            return back()->withErrors(['error' => 'لا يمكن حذف طلب نقل تم اكتماله وتنفيذه بالفعل.']);
        }

        $transfer->delete();

        return redirect()->route('admin.student-transfers.index')->with('success', 'تم حذف طلب النقل نهائياً من النظام.');
    }
}