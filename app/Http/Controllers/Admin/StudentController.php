<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentTransfer;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Branche;
use App\Models\Payment;
use App\Models\StudentInstallment;
use App\Models\FinancialAccount;
use App\Models\StudentTransferPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    // =======================================================
    // 🔥 عرض قائمة الطلاب 🔥
    // =======================================================
   public function index()
        {
            abort_if(!auth()->user()->can('view_students'), 403);

            // ✅ جلب الطلاب مع التحويلات النشطة (غير المكتملة)
            $students = Student::with([
                'parent:id,name_ar,phone', 
                'grade:id,name_ar', 
                'academicYear:id,name', 
                'branch:id,name_ar',
                // ✅ التحويلات النشطة فقط (pending أو في انتظار الدفع)
                'activeTransfers' => function($query) {
                    $query->whereIn('status', ['pending_parent', 'pending_ministry', 'pending_payment'])
                        ->latest();
                }
            ])->latest()->paginate(20);

            return view('backend.students.index', compact('students'));
        }

    // =======================================================
    // 🔥 دالة الإضافة 🔥
    // =======================================================
    public function create(Request $request)
    {
        abort_if(!auth()->user()->can('manage_students'), 403);

        $parents = Guardian::all();
        $academicYears = AcademicYear::latest()->get();
        $grades = Grade::orderBy('level_order')->get();
        $branches = Branche::where('is_active', 1)->get();
        
        $selectedGuardianId = $request->query('guardian_id'); 
        
        $copiedStudent = null;
        if ($request->has('copy_from')) {
            $copiedStudent = Student::find($request->query('copy_from'));
        }
        
        return view('backend.students.create', compact('parents', 'academicYears', 'grades', 'branches', 'selectedGuardianId', 'copiedStudent'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_students'), 403);

        $request->validate($this->validationRules());

        $data = $request->except([
            'student_photo', 'passport_copy', 'birth_certificate_copy', 
            'health_report', 'immunization_card', 'mother_unavailable', 'password'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $safeFirstName = preg_replace('/[^A-Za-z0-9\-_\p{Arabic}]/u', '', $request->first_name);
        $folderPath = 'students/' . $safeFirstName . '_' . time();

        $data = $this->handleFileUploads($request, $data, $folderPath);

        DB::transaction(function () use ($data) {
            Student::create($data);
        });

        return redirect()->route('admin.students.index')->with('success', 'تم تسجيل الطالب وإنشاء حساب الدخول الخاص به بنجاح.');
    }

    // =======================================================
    // 🔥 دالة التحويل من فرع لفرع 🔥
    // =======================================================
    // public function transfer(Request $request, $id)
    // {
    //     abort_if(!auth()->user()->can('manage_students'), 403);

    //     $student = Student::findOrFail($id);

    //     $request->validate([
    //         'to_branch_id'        => 'required|exists:branches,id',
    //         'to_academic_year_id' => 'required|exists:academic_years,id',
    //         'transfer_fee'        => 'required|numeric|min:0',
    //         'transfer_reason'     => 'required|string|max:500',
    //         'payment_type'        => 'required|in:full,installment',
    //         'signature_data'      => 'required|string'
    //     ]);

    //     if ($student->branch_id == $request->to_branch_id) {
    //         return redirect()->back()->withErrors(['error' => 'الطالب مقيد في هذا الفرع بالفعل.']);
    //     }

    //     try {
    //         DB::transaction(function () use ($request, $student) {
                
    //             // 1. تسجيل النقل
    //             $transfer = StudentTransfer::create([
    //                 'student_id'      => $student->id,
    //                 'from_branch_id'  => $student->branch_id,
    //                 'to_branch_id'    => $request->to_branch_id,
    //                 'transfer_fee'    => $request->transfer_fee,
    //                 'transfer_reason' => $request->transfer_reason,
    //                 'signature_data'  => $request->signature_data,
    //                 'transfer_date'   => now(),
    //             ]);

    //             // 2. المعالجة المالية
    //             if ($request->transfer_fee > 0) {
    //                 if ($request->payment_type == 'full') {
    //                     $request->validate(['financial_account_id' => 'required|exists:financial_accounts,id']);
                        
    //                     Payment::create([
    //                         'financial_account_id' => $request->financial_account_id,
    //                         'receipt_number'       => 'REC-TRF-' . time(),
    //                         'user_id'              => auth()->id(),
    //                         'payment_type'         => 'transfer_fee',
    //                         'payable_id'           => $student->id,
    //                         'payable_type'         => Student::class,
    //                         'amount'               => $request->transfer_fee,
    //                         'payment_date'         => now(),
    //                         'transaction_details'  => 'سداد نقدي لرسوم تحويل الطالب من فرع لفرع',
    //                         'notes'                => 'رقم عملية التحويل: ' . $transfer->id
    //                     ]);

    //                     // 🔥 تأمين الخزنة بالـ Lock أثناء توريد رسوم التحويل 🔥
    //                     $account = FinancialAccount::lockForUpdate()->find($request->financial_account_id);
    //                     if($account) {
    //                         $account->increment('balance', $request->transfer_fee);
    //                     }

    //                 } elseif ($request->payment_type == 'installment') {
    //                     $request->validate(['due_date' => 'required|date']);

    //                     StudentInstallment::create([
    //                         'student_contract_id' => null,
    //                         'student_id'          => $student->id, 
    //                         'amount'              => $request->transfer_fee,
    //                         'paid_amount'         => 0,
    //                         'due_date'            => $request->due_date,
    //                         'is_paid'             => false,
    //                         'notes'               => 'قسط رسوم تحويل إدارية للفرع الجديد'
    //                     ]);
    //                 }
    //             }

    //             // 3. تحديث بيانات الطالب
    //             $student->update([
    //                 'branch_id'        => $request->to_branch_id,
    //                 'academic_year_id' => $request->to_academic_year_id,
    //                 'section_id'       => null // تصفير الشعبة للفرع الجديد
    //             ]);

    //         });

    //         return redirect()->back()->with('success', 'تم نقل الطالب بنجاح ومعالجة الرسوم الإدارية.');

    //     } catch (\Exception $e) {
    //         return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء النقل: ' . $e->getMessage()]);
    //     }
    // }
// =======================================================
    // 🔥 دالة التحويل من فرع لفرع 🔥
    // =======================================================
    public function transfer(Request $request, $id)
    {
        abort_if(!auth()->user()->can('transfer_students'), 403);

        $student = Student::findOrFail($id);

        // ✅ Validation جديد
        $request->validate([
            'to_branch_id'        => 'required|exists:branches,id',
            'to_academic_year_id' => 'required|exists:academic_years,id',
            'transfer_fee'        => 'required|numeric|min:0',
            'paid_amount'         => 'required|numeric|min:0|max:' . $request->transfer_fee,
            'payment_type'        => 'required|in:full,installment', // 🔥 إضافة الفحص هنا
            'transfer_reason'     => 'required|string|max:500',
            'financial_account_id'=> 'required_if:paid_amount,>,0|exists:financial_accounts,id',
            'payment_method'      => 'nullable|in:cash,card,transfer',
            'due_date'            => 'nullable|date',
            'signature_data'      => 'required|string'
        ]);

        if ($student->branch_id == $request->to_branch_id) {
            return redirect()->back()->withErrors(['error' => 'الطالب مقيد في هذا الفرع بالفعل.']);
        }

        $transferFee = $request->transfer_fee;
        $paidAmount = min($request->paid_amount, $transferFee); // تأمين عدم تجاوز الرسوم
        $remainingAmount = $transferFee - $paidAmount;

        // 🔥 التعديل هنا: تحديد نوع الدفع بالقيم المسموحة فقط في الداتابيز (full أو installment) 🔥
        $paymentType = $remainingAmount > 0 ? 'installment' : 'full';

        try {
            DB::beginTransaction();

            // 1. إنشاء سجل التحويل
            $transfer = StudentTransfer::create([
                'student_id'           => $student->id,
                'from_branch_id'       => $student->branch_id,
                'to_branch_id'         => $request->to_branch_id,
                'transfer_fee'         => $transferFee,
                'paid_amount'          => $paidAmount,
                'remaining_amount'     => $remainingAmount,
                'payment_type'         => $paymentType, // 🔥 تم التصحيح هنا
                'payment_method'       => $request->payment_method ?? 'cash',
                'transfer_reason'      => $request->transfer_reason,
                'signature_data'       => $request->signature_data,
                'transfer_date'        => now(),
                'status'               => $remainingAmount <= 0 ? 'pending_ministry' : 'pending_payment',
                'is_fee_paid'          => $remainingAmount <= 0,
                'created_by'           => auth()->id(),
            ]);

            // 2. ✅ إذا في دفعة، سجلها فوراً
            if ($paidAmount > 0 && $request->financial_account_id) {
                $receiptNumber = 'TRF-' . str_pad($transfer->id, 6, '0', STR_PAD_LEFT);
                
                // ✅ إنشاء سجل الدفعة في جدول الدفعات
                \App\Models\StudentTransferPayment::create([
                    'student_transfer_id'  => $transfer->id,
                    'financial_account_id' => $request->financial_account_id,
                    'amount'               => $paidAmount,
                    'payment_date'         => now(),
                    'payment_method'       => $request->payment_method ?? 'cash',
                    'receipt_number'       => $receiptNumber,
                    'notes'                => 'دفعة تحويل طالب (رقم العملية: ' . $transfer->id . ')',
                    'received_by'          => auth()->id(),
                ]);

                // ✅ تحديث آخر دفعة في التحويل
                $transfer->update([
                    'financial_account_id' => $request->financial_account_id,
                    'last_payment_date'    => now(),
                ]);

                // ✅ تحديث رصيد الخزنة
                $account = FinancialAccount::lockForUpdate()->find($request->financial_account_id);
                if ($account) {
                    $account->increment('balance', $paidAmount);
                }

                // ✅ تسجيل سند قبض في جدول payments العام
                Payment::create([
                    'financial_account_id' => $request->financial_account_id,
                    'receipt_number'       => $receiptNumber,
                    'user_id'              => auth()->id(),
                    'payment_type'         => 'transfer_fee',
                    'payable_id'           => $student->id,
                    'payable_type'         => Student::class,
                    'amount'               => $paidAmount,
                    'payment_date'         => now(),
                    'transaction_details'  => 'دفعة رسوم تحويل طالب (رقم التحويل: ' . $transfer->id . ')',
                    'notes'                => 'من فرع ' . $student->branch->name_ar . ' إلى ' . $transfer->toBranch->name_ar ?? 'فرع جديد',
                ]);
            }

            // 3. ✅ إذا في متبقي، أنشئ قسط في جدول الأقساط
            if ($remainingAmount > 0 && $request->filled('due_date')) {
                StudentInstallment::create([
                    'student_contract_id' => null, // تحويل مش عقد
                    'student_id'          => $student->id, // 🔥 تأكد من وجود الحقل ده لو مطلوب في الداتابيز
                    'amount'              => $remainingAmount,
                    'paid_amount'         => 0,
                    'remaining_amount'    => $remainingAmount,
                    'due_date'            => $request->due_date,
                    'status'              => 'unpaid',
                    'notes'               => 'قسط رسوم تحويل طالب (رقم التحويل: ' . $transfer->id . ')',
                ]);
            }

            // 4. تحديث بيانات الطالب
            $student->update([
                'branch_id'        => $request->to_branch_id,
                'academic_year_id' => $request->to_academic_year_id,
                'section_id'       => null,
                'status'           => 'transferred',
            ]);

            DB::commit();

            // رسالة نجاح مخصصة حسب حالة الدفع
            $message = match($paymentType) {
                'full'        => 'تم نقل الطالب بنجاح ✓ تم استلام الرسوم كاملة (' . number_format($paidAmount, 3) . ' ر.ع)',
                'installment' => 'تم نقل الطالب ✓ تم استلام دفعة جزئية (' . number_format($paidAmount, 3) . ' ر.ع) والمتبقي (' . number_format($remainingAmount, 3) . ' ر.ع)',
            };

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Transfer Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء النقل: ' . $e->getMessage()]);
        }
    }
// ==========================================
// 🆕 دالة سداد دفعة من تحويل (للتقسيط)
// ==========================================
public function payTransferInstallment(Request $request, $transferId)
{
    abort_if(!auth()->user()->can('collect_installments'), 403);

    $request->validate([
        'amount' => 'required|numeric|min:0.001',
        'financial_account_id' => 'required|exists:financial_accounts,id',
        'payment_method' => 'required|in:cash,card,transfer',
        'payment_date' => 'required|date',
        'notes' => 'nullable|string|max:500',
    ]);

    $transfer = StudentTransfer::findOrFail($transferId);

    if ($transfer->isFullyPaid()) {
        return redirect()->back()->withErrors(['error' => 'هذا التحويل مدفوع بالكامل!']);
    }

    if ($request->amount > $transfer->remaining_amount) {
        return redirect()->back()->withErrors(['error' => 'المبلغ أكبر من المتبقي (' . number_format($transfer->remaining_amount, 3) . ' ر.ع)']);
    }

    try {
        DB::beginTransaction();

        $receiptNumber = 'TRF-PAY-' . str_pad(StudentTransferPayment::count() + 1, 6, '0', STR_PAD_LEFT);

        // 1. تسجيل الدفعة
        StudentTransferPayment::create([
            'student_transfer_id' => $transfer->id,
            'financial_account_id' => $request->financial_account_id,
            'amount'              => $request->amount,
            'payment_date'        => $request->payment_date,
            'payment_method'      => $request->payment_method,
            'receipt_number'      => $receiptNumber,
            'notes'               => $request->notes,
            'received_by'         => auth()->id(),
        ]);

        // 2. تحديث التحويل
        $transfer->increment('paid_amount', $request->amount);
        $transfer->decrement('remaining_amount', $request->amount);
        $transfer->update([
            'last_payment_date' => $request->payment_date,
        ]);

        // لو اتدفع بالكامل، نغير الحالة
        if ($transfer->remaining_amount <= 0) {
            $transfer->update([
                'is_fee_paid' => true,
                'status' => 'pending_ministry',
            ]);
        }

        // 3. تحديث رصيد الخزنة
        $account = FinancialAccount::lockForUpdate()->find($request->financial_account_id);
        if ($account) {
            $account->increment('balance', $request->amount);
        }

        // 4. تسجيل سند قبض عام
        Payment::create([
            'financial_account_id' => $request->financial_account_id,
            'receipt_number'       => $receiptNumber,
            'user_id'              => auth()->id(),
            'payment_type'         => 'transfer_fee',
            'payable_id'           => $transfer->student_id,
            'payable_type'         => Student::class,
            'amount'               => $request->amount,
            'payment_date'         => $request->payment_date,
            'transaction_details'  => 'دفعة من رسوم تحويل طالب (رقم التحويل: ' . $transfer->id . ')',
            'notes'                => $request->notes,
        ]);

        DB::commit();
        return redirect()->back()->with('success', 'تم تسجيل الدفعة بنجاح! رقم الإيصال: ' . $receiptNumber);

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()]);
    }
}
    // =======================================================
    // 🔥 التعديل (Edit & Update) 🔥
    // =======================================================
    public function edit($id)
    {
        abort_if(!auth()->user()->can('manage_students'), 403);

        $student = Student::findOrFail($id);
        $parents = Guardian::all();
        $academicYears = AcademicYear::latest()->get();
        $grades = Grade::orderBy('level_order')->get();
        $branches = Branche::where('is_active', 1)->get();
        
        return view('backend.students.edit', compact('student', 'parents', 'academicYears', 'grades', 'branches'));
    }

    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_students'), 403);

        $student = Student::findOrFail($id);

        $rules = $this->validationRules($student->id);
        
        if ($request->has('parent_id') && $request->parent_id != $student->parent_id) {
            $rules['guardian_change_reason'] = 'required|string|min:5|max:255';
        } else {
            $rules['guardian_change_reason'] = 'nullable|string|max:255';
        }

        $request->validate($rules);

        $data = $request->except([
            'student_photo', 'passport_copy', 'birth_certificate_copy', 
            'health_report', 'immunization_card', 'mother_unavailable', 
            'guardian_change_reason', 'password' 
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $folderPath = 'students/' . preg_replace('/[^A-Za-z0-9\-_\p{Arabic}]/u', '', $student->first_name) . '_' . time();
        $data = $this->handleFileUploads($request, $data, $folderPath, $student);
        
        DB::transaction(function () use ($request, $student, $data) {
            // توثيق تغيير ولي الأمر في السجل
            if ($request->parent_id != $student->parent_id) {
                DB::table('guardian_change_histories')->insert([
                    'student_id'      => $student->id,
                    'old_guardian_id' => $student->parent_id,
                    'new_guardian_id' => $request->parent_id,
                    'change_reason'   => $request->guardian_change_reason,
                    'changed_by'      => auth()->id(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
            $student->update($data);
        });

        return redirect()->route('admin.students.index')->with('success', 'تم تحديث بيانات الطالب وحسابه بنجاح.');
    }

// =======================================================
    // 🔥 عرض بروفايل الطالب 🔥
    // =======================================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('view_students'), 403);

        // جلب بيانات الطالب الأساسية
        $student = Student::with(['parent', 'branch', 'academicYear', 'grade', 'section', 'contracts.academicYear'])->findOrFail($id);

        // 1. إحصائيات الحضور والغياب
        $attendances = \App\Models\StudentAttendance::where('student_id', $id)
                        ->latest('attendance_date')
                        ->get();

        $attendanceStats = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent'  => $attendances->where('status', 'absent')->count(),
            'late'    => $attendances->where('status', 'late')->count(),
            'total'   => $attendances->count(),
        ];

        // 2. درجات الطالب
        $marks = \App\Models\StudentMark::with(['subject', 'category'])
                    ->where('student_id', $id)
                    ->get()
                    ->groupBy('subject_id'); 

        // 3. 🔥 الإضافة الجديدة: سجل التحويلات والحالة المالية للتحويل 🔥
        $transfers = \App\Models\StudentTransfer::with(['fromBranch', 'toBranch'])
                    ->where('student_id', $id)
                    ->latest()
                    ->get();

        return view('backend.students.show', compact('student', 'attendances', 'attendanceStats', 'marks', 'transfers'));
    }

    // =======================================================
    // 🔥 قواعد الـ Validation 🔥
    // =======================================================
    private function validationRules($studentId = null)
    {
        return [
            'parent_id'              => 'required|exists:guardians,id',
            'branch_id'              => 'required|exists:branches,id',
            'academic_year_id'       => 'required|exists:academic_years,id',
            'grade_id'               => 'required|exists:grades,id',
            'first_name'             => 'required|string|max:100',
            'tribe'                  => 'required|string|max:100',
            'dob'                    => 'required|date',
            'gender'                 => 'required|in:male,female',
            'nationality'            => 'required|string|max:100',
            
            'email'                  => 'nullable|email|max:150|unique:students,email' . ($studentId ? ',' . $studentId : ''),
            'password'               => 'nullable|string|min:6',

            'mother_unavailable'     => 'nullable',
            'mother_name'            => 'required_without:mother_unavailable|nullable|string|max:100',
            'mother_national_id'     => 'required_without:mother_unavailable|nullable|string|max:50',
            'mother_id_expiry'       => 'required_without:mother_unavailable|nullable|date',
            'mother_phone'           => 'required_without:mother_unavailable|nullable|string|max:20',
            'mother_nationality'     => 'required_without:mother_unavailable|nullable|string|max:100',
            'mother_workplace'       => 'required_without:mother_unavailable|nullable|string|max:150',
            'emergency_name'         => 'required|string|max:100',
            'emergency_relation'     => 'required|string|max:50',
            'emergency_phone'        => 'required|string|max:20',
            'region'                 => 'required|string|max:100',
            'house_number'           => 'required|string|max:50',
            'street_number'          => 'required|string|max:50',
            'pickup_point'           => 'required|string|max:255',
            'transport_type'         => 'required|in:private,bus_go,bus_return,bus_both',
            'has_allergies'          => 'required|boolean',
            'has_hearing_issues'     => 'required|boolean',
            'has_vision_issues'      => 'required|boolean',
            'health_notes'           => 'nullable|string|max:500',
            'student_photo'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'passport_copy'          => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'birth_certificate_copy' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'health_report'          => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'immunization_card'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ];
    }

    private function handleFileUploads($request, $data, $folderPath, $student = null)
    {
        $files = [
            'student_photo'          => 'student_photo',
            'passport_copy'          => 'passport_copy',
            'birth_certificate_copy' => 'birth_certificate_copy',
            'health_report'          => 'health_reports', 
            'immunization_card'      => 'vaccination_card' 
        ];

        foreach ($files as $inputName => $dbColumn) {
            if ($request->hasFile($inputName)) {
                if ($student && $student->$dbColumn) {
                    Storage::disk('public')->delete($student->$dbColumn);
                }
                $data[$dbColumn] = $request->file($inputName)->store($folderPath, 'public');
            }
        }
        return $data;
    }
    
    // =======================================================
    // 🔥 البحث السريع (AJAX) 🔥
    // =======================================================
    // public function search(Request $request)
    // {
    //     abort_if(!auth()->user()->can('view_students'), 403);

    //     $search = $request->q;
    //     $query = \App\Models\Student::query();

    //     if ($search) {
    //         $query->where('first_name', 'like', "%{$search}%")
    //               ->orWhere('tribe', 'like', "%{$search}%")
    //               ->orWhere('id', $search);
    //     }

    //     $students = $query->limit(15)->get();

    //     $response = [];
    //     foreach ($students as $student) {
    //         $response[] = [
    //             "id"         => $student->id,
    //             "first_name" => $student->first_name, 
    //             "last_name"  => $student->tribe ?? ''
    //         ];
    //     }

    //     return response()->json($response);
    // }

    // =======================================================
    // 🔥 البحث السريع (AJAX) 🔥
    // =======================================================
    public function search(Request $request)
    {
        abort_if(!auth()->user()->can('view_students'), 403);

        $search = $request->q;
        // ضفنا with('branch') عشان نجيب بيانات فرعه الحالي
        $query = \App\Models\Student::with('branch');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('tribe', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $students = $query->limit(15)->get();

        $response = [];
        foreach ($students as $student) {
            $response[] = [
                "id"          => $student->id,
                "first_name"  => $student->first_name, 
                "last_name"   => $student->tribe ?? '',
                // 🔥 ضفنا الفرع الحالي للرد 🔥
                "branch_name" => $student->branch->name_ar ?? 'غير محدد',
                "branch_id"   => $student->branch_id
            ];
        }

        return response()->json($response);
    }
}