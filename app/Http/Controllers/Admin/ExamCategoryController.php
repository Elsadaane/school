<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamCategory;
use App\Models\AcademicTerm;
use App\Models\Branche;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamCategoryController extends Controller
{
    public function index()
    {
        // حماية الباك إند بالصلاحية
        abort_if(!auth()->user()->can('manage_evaluations'), 403, 'غير مصرح لك بإدارة توزيع الدرجات.');

        // جلب البيانات الأساسية للـ Select Boxes
        $terms = AcademicTerm::where('is_active', 1)->get();
        $branches = Branche::all();
        $grades = Grade::orderBy('level_order')->get();
        $subjects = Subject::where('is_active', 1)->get();
        
        // جلب التوزيعات الحالية وتجميعها حسب الصف ثم المادة لتسهيل العرض في الجدول
        $categories = ExamCategory::with(['term', 'grade', 'subject'])
            ->orderBy('grade_id')
            ->orderBy('subject_id')
            ->orderBy('sort_order')
            ->get()
            ->groupBy(['grade.name_ar', 'subject.name_ar']);
        
        return view('backend.grading.index', compact('categories', 'terms', 'branches', 'grades', 'subjects'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_evaluations'), 403);

        $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'branch_id'        => 'required|exists:branches,id',
            'grade_id'         => 'required|exists:grades,id',
            'subject_id'       => 'required|exists:subjects,id',
            'categories'       => 'required|array|min:1', 
            'categories.*.name'     => 'required|string|max:255',
            'categories.*.max_mark' => 'required|numeric|min:0.5',
        ]);

        try {
            DB::beginTransaction();

            // 🔥 الخطوة الأهم: مسح التوزيعة القديمة لنفس المادة والصف في هذا الترم لمنع التكرار 🔥
            ExamCategory::where('academic_term_id', $request->academic_term_id)
                ->where('branch_id', $request->branch_id)
                ->where('grade_id', $request->grade_id)
                ->where('subject_id', $request->subject_id)
                ->delete();

            // إدخال التوزيعة الجديدة
            foreach ($request->categories as $index => $cat) {
                ExamCategory::create([
                    'academic_term_id' => $request->academic_term_id,
                    'branch_id'        => $request->branch_id,
                    'grade_id'         => $request->grade_id,
                    'subject_id'       => $request->subject_id,
                    'name'             => $cat['name'],
                    'max_mark'         => $cat['max_mark'],
                    'sort_order'       => $index + 1,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'تم ضبط وتحديث توزيع درجات المادة بنجاح!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()]);
        }
    }
}