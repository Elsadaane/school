<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Branche;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassSchedule;
use App\Models\EmergencyOnlineDay;
use App\Models\Grade;
use App\Notifications\TeacherScheduleUpdated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    // ==========================================
    // 🔥 1. عرض شاشة الجداول والفلاتر الأساسية
    // ==========================================
    public function index(Request $request)
    {
        // 🔒 حماية الشاشة بصلاحيات الإدارة
        abort_if(!auth()->user()->can('manage_schedules'), 403, 'غير مصرح لك بإدارة الجداول المدرسية.');

        $branches = Branche::where('is_active', 1)->get();
        $subjects = Subject::where('is_active', 1)->get();
        $teachers = Teacher::where('status', 'active')->get();
        $grades   = Grade::orderBy('level_order')->get(); 

        return view('backend.schedules.index', compact('branches', 'subjects', 'teachers', 'grades'));
    }

    // ==========================================
    // 🔥 2. جلب المعلمين المتاحين لحصة معينة
    // ==========================================
    public function getAvailableTeachers(Request $request)
    {
        abort_if(!auth()->user()->can('manage_schedules'), 403);

        $day = $request->day;
        $period = $request->period;
        $subjectId = $request->subject_id;
        $isSubstitute = $request->is_substitute; 
        $currentSectionId = $request->section_id;

        $teachersQuery = Teacher::where('is_active', 1);

        // فلترة حسب المادة إذا لم تكن حصة احتياطي
        if ($isSubstitute == 'false' && $subjectId) {
            $teachersQuery->whereHas('subjects', function($query) use ($subjectId) {
                $query->where('subject_id', $subjectId);
            });
        }

        // استبعاد المعلمين المشغولين في نفس الوقت لشعب أخرى
        $busyTeachersQuery = ClassSchedule::where('day_of_week', $day)
            ->where('period_number', $period);
            
        if ($currentSectionId) {
            $busyTeachersQuery->where('section_id', '!=', $currentSectionId);
        }
        
        $busyTeacherIds = $busyTeachersQuery->pluck('teacher_id')->toArray();

        if (!empty($busyTeacherIds)) {
            $teachersQuery->whereNotIn('id', $busyTeacherIds);
        }

        $availableTeachers = $teachersQuery->select('id', 'name_ar', 'specialization')->get();

        return response()->json($availableTeachers);
    }

    // ==========================================
    // 🔥 3. جلب السنوات التابعة لفرع معين
    // ==========================================
    public function getBranchFilters($branch_id)
    {
        abort_if(!auth()->user()->can('manage_schedules'), 403);
        $branch = Branche::with('academicYears')->findOrFail($branch_id);
        return response()->json($branch->academicYears); 
    }
    
    // ==========================================
    // 🔥 4. جلب الشعب حسب الفلاتر
    // ==========================================
    public function getSectionsAjax(Request $request)
    {
        abort_if(!auth()->user()->can('manage_schedules'), 403);
        $sections = Section::where('branch_id', $request->branch_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('grade_id', $request->grade_id)
            ->get();
            
        return response()->json($sections);
    }

    // ==========================================
    // 🔥 5. جلب جدول شعبة لعرضه
    // ==========================================
    public function getSectionSchedule(Request $request)
    {
        abort_if(!auth()->user()->can('manage_schedules'), 403);
        $request->validate(['section_id' => 'required|exists:sections,id']);

        $schedule = ClassSchedule::where('section_id', $request->section_id)->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $schedule
        ]);
    }

    // ==========================================
    // 🔥 6. حفظ الجدول (معالجة تزامن وتعارض آمنة) 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_schedules'), 403);

        $request->validate([
            'branch_id'        => 'required|exists:branches,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'grade_id'         => 'required|exists:grades,id',
            'section_id'       => 'required|exists:sections,id',
            'schedule'         => 'required|array' 
        ]);

        $sectionId = $request->section_id;
        $teacherIdsToNotify = [];
        $insertData = [];
        $now = now();

        try {
            // 🌟 إدخال الفحص بالكامل داخل Transaction لمنع تداخل البيانات 🌟
            DB::beginTransaction();

            foreach ($request->schedule as $day => $periods) {
                foreach ($periods as $periodNum => $data) {
                    if (!empty($data['subject_id']) && !empty($data['teacher_id'])) {
                        
                        // استخدام lockForUpdate لتأمين القراءة والكتابة
                        $conflict = ClassSchedule::where('teacher_id', $data['teacher_id'])
                            ->where('academic_year_id', $request->academic_year_id)
                            ->where('day_of_week', $day)
                            ->where('period_number', $periodNum)
                            ->where('section_id', '!=', $sectionId)
                            ->lockForUpdate()
                            ->first();

                        if ($conflict) {
                            DB::rollBack();
                            $teacher = Teacher::find($data['teacher_id']);
                            $conflictSection = Section::find($conflict->section_id);
                            
                            // 🌟 تجهيز المتغيرات لتفادي خطأ الـ PHP Syntax 🌟
                            $teacherName = $teacher?->name_ar ?? 'غير معروف';
                            $sectionName = $conflictSection?->name_ar ?? 'غير معروفة';
                            $dayAr = ['sunday'=>'الأحد','monday'=>'الإثنين','tuesday'=>'الثلاثاء','wednesday'=>'الأربعاء','thursday'=>'الخميس'][$day] ?? $day;
                            
                            return response()->json([
                                'status' => 'error', 
                                'message' => "⛔ توقف! المعلم ({$teacherName}) مخصص بالفعل لشعبة ({$sectionName}) يوم {$dayAr} الحصة رقم {$periodNum}."
                            ], 422);
                        }

                        $insertData[] = [
                            'branch_id'        => $request->branch_id,
                            'academic_year_id' => $request->academic_year_id,
                            'grade_id'         => $request->grade_id,
                            'section_id'       => $sectionId,
                            'day_of_week'      => $day,
                            'period_number'    => $periodNum,
                            'subject_id'       => $data['subject_id'],
                            'teacher_id'       => $data['teacher_id'],
                            'is_virtual'       => isset($data['is_virtual']) ? 1 : 0, 
                            'created_at'       => $now,
                            'updated_at'       => $now,
                        ];
                        
                        $teacherIdsToNotify[] = $data['teacher_id'];
                    }
                }
            }

            // مسح الجدول القديم واستبداله بالجديد
            ClassSchedule::where('section_id', $sectionId)->delete();

            if (!empty($insertData)) {
                ClassSchedule::insert($insertData);
            }

            DB::commit();

            // 🌟 إرسال الإشعارات خارج الـ Transaction لعدم تعطيل قاعدة البيانات 🌟
            $uniqueTeacherIds = array_unique($teacherIdsToNotify);
            if (!empty($uniqueTeacherIds)) {
                $teachers = Teacher::whereIn('id', $uniqueTeacherIds)->get();
                Notification::send($teachers, new TeacherScheduleUpdated('تم تحديث جدول الحصص الخاص بك للأسبوع الحالي. يرجى مراجعته من لوحة التحكم.'));
            }

            return response()->json([
                'status' => 'success', 
                'message' => 'تم حفظ الجدول بنجاح وإرسال الإشعارات للمعلمين!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error', 
                'message' => 'حدث خطأ داخلي أثناء الحفظ: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ==========================================
    // 🔥 7. إعلان حالة الطوارئ (تحويل اليوم أونلاين)
    // ==========================================
    public function declareEmergency(Request $request)
    {
        abort_if(!auth()->user()->can('manage_schedules'), 403, 'غير مصرح لك بإعلان حالات الطوارئ.');

        $request->validate([
            'target_date' => 'required|date',
            'branch_id'   => 'nullable|exists:branches,id',
            'reason'      => 'nullable|string|max:255',
        ]);

        EmergencyOnlineDay::updateOrCreate(
            [
                'target_date' => $request->target_date,
                'branch_id'   => $request->branch_id,
            ],
            [
                'reason'     => $request->reason,
                'created_by' => auth()->id(),
            ]
        );

        return redirect()->back()->with('success', 'تم إعلان حالة الطوارئ وتحويل كافة حصص هذا اليوم إلى أونلاين بنجاح!');
    }
}