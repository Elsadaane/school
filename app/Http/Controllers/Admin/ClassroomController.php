<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade; 
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        // 📊 نجلب الصفوف فقط، مع عداد للشعب والطلاب عشان يظهروا في الكروت
        $classrooms = Grade::withCount(['sections', 'students'])->orderBy('level_order', 'asc')->get();

        return view('backend.classrooms.index', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grades' => 'required|array|min:1',
            'grades.*.name_ar' => 'required|string|max:255',
            'grades.*.name_en' => 'nullable|string|max:255',
        ]);

        foreach ($request->grades as $gradeData) {
            Grade::create([
                'name_ar' => $gradeData['name_ar'],
                'name_en' => $gradeData['name_en'] ?? null,
                'level_order' => Grade::max('level_order') + 1, 
            ]);
        }

        return redirect()->back()->with('success', 'تمت إضافة الصفوف بنجاح للقائمة العامة للمدارس.');
    }

    public function show($id)
    {
        // 👁️ نجلب الصف، ومعاه كل الشعب المربوطة بيه بالفروع
        $grade = Grade::with(['sections.branch', 'sections.academicYear'])->findOrFail($id);

        return view('backend.classrooms.show', compact('grade'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
        ]);

        $grade = Grade::findOrFail($id);
        
        $grade->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
        ]);

        return redirect()->back()->with('success', 'تم تعديل اسم الصف بنجاح.');
    }
    
    public function destroy($id)
    {
        $grade = Grade::findOrFail($id);

        // 🔥 منع الحذف لو الصف متفعل (ليه شعب مفتوحة في أي فرع)
        if ($grade->sections()->exists()) {
            return redirect()->back()->with('error', 'عفواً، لا يمكن حذف هذا الصف لوجود شعب دراسية (فصول) مسجلة بداخله.');
        }

        // 🔥 حماية إضافية: منع الحذف لو الصف مربوط بطلاب
        if ($grade->students()->exists()) {
            return redirect()->back()->with('error', 'عفواً، لا يمكن حذف هذا الصف لوجود طلاب مقيدين به في النظام.');
        }

        try {
            $grade->delete();
            return redirect()->back()->with('success', 'تم حذف الصف بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ في قاعدة البيانات أثناء الحذف.');
        }
    }
}