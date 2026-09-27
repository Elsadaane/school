<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    // ==========================================
    // 1. عرض قائمة المواد الدراسية
    // ==========================================
    public function index()
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_subjects'), 403, 'غير مصرح لك بإدارة المواد الدراسية.');

        // 🌟 استخدام paginate بدلاً من get لتحسين الأداء
        $subjects = Subject::withCount('teachers')->latest()->paginate(15);
        
        return view('backend.subjects.index', compact('subjects'));
    }

    // ==========================================
    // 2. حفظ مادة دراسية جديدة
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_subjects'), 403);

        $request->validate([
            'name_ar' => 'required|string|max:255|unique:subjects,name_ar',
            'name_en' => 'nullable|string|max:255',
            'code'    => 'nullable|string|max:50|unique:subjects,code',
        ]);

        Subject::create([
            'name_ar'   => $request->name_ar,
            'name_en'   => $request->name_en,
            'code'      => $request->code,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'تم إضافة المادة الدراسية بنجاح.');
    }

    // ==========================================
    // 3. تعديل بيانات المادة
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_subjects'), 403);

        $subject = Subject::findOrFail($id);

        $request->validate([
            'name_ar' => 'required|string|max:255|unique:subjects,name_ar,' . $subject->id,
            'name_en' => 'nullable|string|max:255',
            'code'    => 'nullable|string|max:50|unique:subjects,code,' . $subject->id,
        ]);

        $subject->update([
            'name_ar'   => $request->name_ar,
            'name_en'   => $request->name_en,
            'code'      => $request->code,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'تم تعديل المادة الدراسية بنجاح.');
    }

    // ==========================================
    // 4. الحذف مع الحماية الأكاديمية الصارمة 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_subjects'), 403);

        $subject = Subject::findOrFail($id);

        try {
            DB::beginTransaction();

            // 🌟 1. الحماية الأكاديمية: هل المادة مستخدمة في الجداول أو الامتحانات؟
            $isUsedInSchedules = DB::table('class_schedules')->where('subject_id', $id)->exists();
            $isUsedInMarks = DB::table('student_marks')->where('subject_id', $id)->exists();

            if ($isUsedInSchedules || $isUsedInMarks) {
                return redirect()->back()->withErrors(['error' => 'مرفوض أمنياً: لا يمكن حذف هذه المادة لارتباطها بجداول حصص أو درجات طلاب مسجلة بالفعل. يمكنك إلغاء تفعيلها فقط.']);
            }
            
            // 🌟 2. فك ارتباط المعلمين بالمادة قبل الحذف لمنع الأخطاء
            $subject->teachers()->detach(); 
            
            // 🌟 3. الحذف الفعلي
            $subject->delete();

            DB::commit();
            return redirect()->back()->with('success', 'تم حذف المادة الدراسية بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ أثناء محاولة حذف المادة: ' . $e->getMessage()]);
        }
    }
}