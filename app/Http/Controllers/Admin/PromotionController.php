<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Grade;
use App\Models\AcademicYear;
use App\Models\Branche;
use App\Models\StudentPromotion;
use App\Models\StudentContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    // ==========================================
    // 1. عرض شاشة الترقيات
    // ==========================================
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('manage_promotions'), 403, 'غير مصرح لك بإدارة ترقيات الطلاب.');

        $branches = Branche::where('is_active', 1)->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $grades = Grade::orderBy('level_order', 'asc')->get();
        
        $students = collect();
        $sections = collect();

        // 🔥 جلب الشعب فقط لو تم اختيار (الفرع + السنة + الصف) 🔥
        if ($request->filled('branch_id') && $request->filled('from_academic_year_id') && $request->filled('from_grade_id')) {
            
            // نجيب شعب الصف ده بس
            $sections = \App\Models\Section::where('branch_id', $request->branch_id)
                                           ->where('academic_year_id', $request->from_academic_year_id)
                                           ->where('grade_id', $request->from_grade_id)
                                           ->get();

            $query = Student::where('branch_id', $request->branch_id)
                            ->where('academic_year_id', $request->from_academic_year_id)
                            ->where('grade_id', $request->from_grade_id)
                            ->where('status', 'active'); 

            if ($request->filled('from_section_id')) {
                $query->where('section_id', $request->from_section_id);
            }

            $students = $query->get();
        }

        return view('backend.promotions.index', compact('branches', 'academicYears', 'grades', 'sections', 'students'));
    }

    // ==========================================
    // 2. دالة الأجاكس لجلب الشعب المفلترة
    // ==========================================
    public function getFilteredSections(Request $request)
    {
        $sections = \App\Models\Section::where('branch_id', $request->branch_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('grade_id', $request->grade_id)
            ->get(['id', 'name_ar']);
            
        return response()->json($sections);
    }

    // ==========================================
    // 3. تنفيذ الترقية والترحيل الذكي
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_promotions'), 403);

        $request->validate([
            'branch_id'             => 'required|exists:branches,id',
            'from_academic_year_id' => 'required|exists:academic_years,id',
            'from_grade_id'         => 'required|exists:grades,id',
            'students'              => 'required|array',
            'students.*'            => 'required|in:promoted,retained', // ناجح أو راسب
        ]);

        $currentYear = AcademicYear::findOrFail($request->from_academic_year_id);
        
        // البحث عن العام الدراسي التالي زمنياً
        $nextYear = AcademicYear::where('start_date', '>', $currentYear->start_date)
                                ->orderBy('start_date', 'asc')
                                ->first();

        if (!$nextYear) {
            return redirect()->back()->withErrors(['error' => 'لا يوجد عام دراسي تالي مسجل في النظام! يرجى إنشاء العام الدراسي الجديد أولاً من الإعدادات.']);
        }

        $currentGrade = Grade::findOrFail($request->from_grade_id);
        $nextGrade = Grade::where('level_order', '>', $currentGrade->level_order)
                          ->orderBy('level_order', 'asc')
                          ->first();

        try {
            DB::beginTransaction();

            $processedCount = 0;

            foreach ($request->students as $student_id => $status) {
                // التأكد أن الطالب يتبع الفرع المحدد
                $student = Student::where('branch_id', $request->branch_id)->find($student_id);
                if (!$student) continue;

                // 🌟 حماية ضد الترقية المزدوجة 🌟
                $alreadyPromoted = StudentPromotion::where('student_id', $student->id)
                                                   ->where('from_academic_year_id', $currentYear->id)
                                                   ->exists();
                if ($alreadyPromoted) continue;

                // 🌟 المعالجة المنطقية للتخرج والرسوب والنجاح 🌟
                if ($status == 'promoted') {
                    if ($nextGrade) {
                        // حالة النجاح الطبيعية (ينتقل للصف التالي)
                        $targetGradeId = $nextGrade->id;
                        $studentStatus = 'active';
                    } else {
                        // حالة التخرج (ناجح ولا يوجد صف تالي)
                        $targetGradeId = $currentGrade->id; 
                        $studentStatus = 'graduated'; 
                    }
                } else {
                    // حالة الرسوب (يبقى في نفس الصف ولكن يرحل للعام الجديد)
                    $targetGradeId = $currentGrade->id;
                    $studentStatus = 'active';
                }

                // 1. تسجيل العملية في السجل (مع حفظ الشعبة القديمة) 💡
                StudentPromotion::create([
                    'student_id'            => $student->id,
                    'from_academic_year_id' => $currentYear->id,
                    'from_grade_id'         => $currentGrade->id,
                    'from_section_id'       => $student->section_id, // 👈 حفظ شعبته الأصلية للحماية
                    'to_academic_year_id'   => $nextYear->id, 
                    'to_grade_id'           => $targetGradeId,
                    'status'                => $status,
                    'processed_by'          => auth()->id(),
                ]);

                // 2. تحديث بيانات الطالب الأكاديمية
                $student->update([
                    'academic_year_id' => $nextYear->id, 
                    'grade_id'         => $targetGradeId,
                    'section_id'       => null, // تصفير الشعبة للعام الجديد ليعاد تسكينه
                    'status'           => $studentStatus,
                ]);

                // 3. 🔥 الترحيل المالي الذكي وتوليد مسودة العقد 🔥
                if ($studentStatus == 'active') {
                    
                    // جلب عقد العام الماضي لمعرفة إذا كان هناك مديونية
                    $oldContract = StudentContract::where('student_id', $student->id)
                                                  ->where('academic_year_id', $currentYear->id)
                                                  ->first();
                    
                    $previousDebt = 0;
                    $contractNotes = 'عقد مسودة تم إنشاؤه آلياً بنظام الترحيل.';

                    // التحقق من وجود متبقي (ديون) على الطالب
                    if ($oldContract && $oldContract->total_remaining > 0) {
                        $previousDebt = $oldContract->total_remaining;
                        $contractNotes = "تنبيه للمحاسب: تم ترحيل مبلغ ({$previousDebt}) كمديونية سابقة ومتبقية من العام الدراسي الماضي.";
                    }

                    // توليد رقم عقد مميز: CON - السنة - ID الطالب - رقم عشوائي
                    $contractNumber = 'CON-' . date('Y') . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT) . '-' . rand(10, 99);
                    
                    StudentContract::create([
                        'contract_number'  => $contractNumber,
                        'branch_id'        => $student->branch_id,
                        'student_id'       => $student->id,
                        'academic_year_id' => $nextYear->id,
                        'grade_id'         => $targetGradeId,
                        'contract_date'    => now(),
                        'contract_status'  => 'draft', // 👈 مسودة عشان المحاسب يراجعها
                        'financial_status' => 'unpaid',
                        'sub_total'        => 0,
                        'grand_total'      => $previousDebt, // 👈 نقل المديونية الذكي
                        'paid_amount'      => 0,
                        'total_remaining'  => $previousDebt, 
                        'notes'            => $contractNotes,
                    ]);
                }

                $processedCount++;
            }

            DB::commit();

            if ($processedCount == 0) {
                return redirect()->back()->with('warning', 'لم يتم ترحيل أي طلاب (قد يكونوا مرحلين مسبقاً أو غير تابعين للفرع المحدد).');
            }

            return redirect()->route('admin.promotions.index')
                             ->with('success', "تم ترحيل ({$processedCount}) طالب، وتجهيز مسودات العقود المالية (مع فحص المديونيات السابقة) للعام الجديد بنجاح.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الترحيل: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 4. 🔥 التراجع عن الترحيل (Rollback) 🔥
    // ==========================================
    public function rollback(Request $request, $promotion_id)
    {
        abort_if(!auth()->user()->can('manage_promotions'), 403);

        try {
            DB::beginTransaction();

            $promotion = StudentPromotion::findOrFail($promotion_id);
            $student = Student::findOrFail($promotion->student_id);

            // 1. مسح مسودة عقد العام الجديد (لو لسه مسودة وماتدفعتش)
            $newContract = StudentContract::where('student_id', $student->id)
                                          ->where('academic_year_id', $promotion->to_academic_year_id)
                                          ->where('contract_status', 'draft') // تأمين عشان لو المحاسب اعتمد العقد مانمسحوش بالغلط
                                          ->first();
            
            if ($newContract) {
                $newContract->delete();
            }

            // 2. إرجاع بيانات الطالب للسنة والصف والشعبة القديمة 💡
            $student->update([
                'academic_year_id' => $promotion->from_academic_year_id,
                'grade_id'         => $promotion->from_grade_id,
                'section_id'       => $promotion->from_section_id, // 👈 استرجاع الشعبة بدقة لكي يظهر في البحث
                'status'           => 'active',
            ]);

            // 3. حذف سجل الترقية
            $promotion->delete();

            DB::commit();
            return redirect()->back()->with('success', 'تم التراجع عن ترقية الطالب وإرجاعه لصفه وشعبته السابقة بنجاح.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء التراجع: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 5. عرض سجل الترقيات
    // ==========================================
    public function history()
    {
        abort_if(!auth()->user()->can('manage_promotions'), 403);

        // 💡 تم تصحيح أسماء العلاقات لتطابق الموديل (fromAcademicYear و toAcademicYear)
        $promotions = StudentPromotion::with(['student', 'fromAcademicYear', 'toAcademicYear', 'fromGrade', 'toGrade'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backend.promotions.history', compact('promotions'));
    }
}