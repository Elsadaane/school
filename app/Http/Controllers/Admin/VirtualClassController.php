<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VirtualClass;
use App\Models\Branche;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VirtualClassController extends Controller
{
    // =======================================================
    // 🔥 عرض قائمة الفصول الافتراضية (الجدول) 🔥
    // =======================================================
    public function index()
    {
        abort_if(!auth()->user()->can('view_virtual_classes'), 403, 'غير مصرح لك بعرض الفصول الافتراضية.');

        // 💡 جلب الفرع والصف عن طريق الشعبة (Nested Relations) لتجنب أخطاء العلاقات
        $virtualClasses = VirtualClass::with(['section.branch', 'section.grade', 'subject', 'teacher'])
            ->orderBy('start_at', 'desc')
            ->paginate(15);

        return view('backend.virtual_classes.index', compact('virtualClasses'));
    }

    // =======================================================
    // 🔥 شاشة جدولة فصل افتراضي جديد 🔥
    // =======================================================
    public function create()
    {
        abort_if(!auth()->user()->can('manage_virtual_classes'), 403);

        $branches = Branche::where('is_active', 1)->get();
        $grades = Grade::orderBy('level_order')->get();
        $subjects = Subject::where('is_active', 1)->get();
        
        // 💡 لا نقوم بجلب جميع المعلمين هنا والشعب، سيتم جلبهم بالـ AJAX بناءً على الفرع والمادة
        $teachers = collect(); 

        return view('backend.virtual_classes.create', compact('branches', 'grades', 'subjects', 'teachers'));
    }

    // =======================================================
    // 🔥 حفظ بيانات الفصل الافتراضي في قاعدة البيانات 🔥
    // =======================================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_virtual_classes'), 403);

        $request->validate([
            'topic'            => 'required|string|max:255',
            'branch_id'        => 'required|exists:branches,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'grade_id'         => 'required|exists:grades,id',
            'section_id'       => 'required|exists:sections,id',
            'subject_id'       => 'required|exists:subjects,id',
            'teacher_id'       => 'required|exists:teachers,id',
            'start_at'         => 'required|date|after_or_equal:today',
            'duration'         => 'required|integer|min:15|max:180',
            'join_url'         => 'nullable|url|max:500',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 💡 استبعاد الحقول الوهمية لمنع إيرور SQL (لأنها مرتبطة بجدول الشعب وليس الفصل)
                $data = $request->except(['_token', 'is_active', 'branch_id', 'academic_year_id', 'grade_id']);
                
                // معالجة حالة التفعيل
                $data['is_active']  = $request->has('is_active') ? 1 : 0;
                
                // توليد مُعرف فريد للغرفة الافتراضية
                $data['meeting_id'] = 'VC-' . Str::random(10) . '-' . time();
                $data['status']     = 'scheduled';

                VirtualClass::create($data);
            });

            return redirect()->route('admin.virtual-classes.index')->with('success', 'تم جدولة الفصل الافتراضي بنجاح.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء جدولة الفصل: ' . $e->getMessage()]);
        }
    }

    // =======================================================
    // 🔥 شاشة تعديل الفصل الافتراضي 🔥
    // =======================================================
    public function edit($id)
    {
        abort_if(!auth()->user()->can('manage_virtual_classes'), 403);

        $virtualClass = VirtualClass::with(['section.grade', 'section.branch', 'section.academicYear', 'teacher'])->findOrFail($id);
        
        $branches = Branche::where('is_active', 1)->get();
        $grades   = Grade::orderBy('level_order')->get();
        $subjects = Subject::where('is_active', 1)->get();
        
        // 💡 جلب الشعب الخاصة بنفس الفرع والصف للفصل المراد تعديله
        $sections = Section::where('grade_id', $virtualClass->section->grade_id ?? null)
                           ->where('branch_id', $virtualClass->section->branch_id ?? null)
                           ->get();

        // 💡 جلب المعلمين التابعين لنفس المادة لتظهر في القائمة كخيارات دقيقة
        $teachers = Teacher::whereHas('subjects', function($query) use ($virtualClass) {
                               $query->where('subjects.id', $virtualClass->subject_id);
                           })->where('status', 'active')->get();

        // إذا كانت العلاقة مختلفة للمعلم والمادة، نجلب المعلم الحالي كحد أدنى لتجنب الخطأ
        if($teachers->isEmpty() && $virtualClass->teacher) {
            $teachers = collect([$virtualClass->teacher]);
        }

        return view('backend.virtual_classes.edit', compact('virtualClass', 'branches', 'grades', 'sections', 'subjects', 'teachers'));
    }

    // =======================================================
    // 🔥 تحديث بيانات الفصل الافتراضي 🔥
    // =======================================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_virtual_classes'), 403);

        $virtualClass = VirtualClass::findOrFail($id);

        $request->validate([
            'topic'            => 'required|string|max:255',
            'branch_id'        => 'required|exists:branches,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'grade_id'         => 'required|exists:grades,id',
            'section_id'       => 'required|exists:sections,id',
            'subject_id'       => 'required|exists:subjects,id',
            'teacher_id'       => 'required|exists:teachers,id',
            'start_at'         => 'required|date',
            'duration'         => 'required|integer|min:15|max:180',
            'join_url'         => 'nullable|url|max:500',
            'status'           => 'required|in:scheduled,active,completed,cancelled',
        ]);

        // 💡 استبعاد حقول الفرع والصف والعام الدراسي لأنها تُخزن في جدول الشعب فقط
        $data = $request->except(['_token', 'is_active', 'branch_id', 'academic_year_id', 'grade_id']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $virtualClass->update($data);

        return redirect()->route('admin.virtual-classes.index')->with('success', 'تم تحديث بيانات الفصل الافتراضي بنجاح.');
    }

    // =======================================================
    // 🔥 حذف الفصل الافتراضي 🔥
    // =======================================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_virtual_classes'), 403);

        $virtualClass = VirtualClass::findOrFail($id);
        $virtualClass->delete();

        return redirect()->route('admin.virtual-classes.index')->with('success', 'تم إلغاء وحذف الفصل الافتراضي بنجاح.');
    }

    // =======================================================
    // 🔥 الدخول لغرفة البث المباشر (الإشراف الإداري) 🔥
    // =======================================================
    public function joinRoom($id)
    {
        abort_if(!auth()->user()->can('view_virtual_classes'), 403, 'غير مصرح لك بدخول الفصول الافتراضية.');

        $virtualClass = VirtualClass::with(['teacher', 'subject', 'section.grade'])->findOrFail($id);

        // 💡 إذا دخل المدير/المعلم الغرفة قبل الموعد بـ 15 دقيقة، يتم تفعيلها تلقائياً للطلاب
        if ($virtualClass->status == 'scheduled' && Carbon::now()->greaterThanOrEqualTo(Carbon::parse($virtualClass->start_at)->subMinutes(15))) {
            $virtualClass->update(['status' => 'active']);
        }

        $user = auth()->user();
        $displayName = 'المدير/المشرف: ' . $user->name;

        return view('backend.virtual_classes.room', compact('virtualClass', 'displayName'));
    }
}