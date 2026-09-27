<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Branche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class GuardianController extends Controller
{
    // ==========================================
    // 1. عرض قائمة أولياء الأمور
    // ==========================================
    public function index()
    {
        // 🔥 حماية الصفحة بصلاحية المشاهدة 🔥
        abort_if(!auth()->user()->can('view_guardians'), 403, 'غير مصرح لك بعرض بيانات أولياء الأمور.');

        // جلب أولياء الأمور مع الفروع وعد الطلاب (مراعاة الفروع النشطة فقط)
        $guardians = Guardian::with(['branch'])->withCount('students')->latest()->get();
        
        $branches = Branche::where('is_active', 1)->get(); 

        return view('backend.guardians.index', compact('guardians', 'branches'));
    }

    // ==========================================
    // 2. عرض تفاصيل ولي الأمر وأبنائه (وملفاتهم المالية)
    // ==========================================
    public function show($id)
    {
        abort_if(!auth()->user()->can('show_guardians'), 403, 'غير مصرح لك بعرض التفاصيل.');

        $guardian = Guardian::with([
            'branch', 
            'students.grade', 
            'students.section', 
            'students.branch',
            'students.contracts.academicYear' // 🔥 الإضافة هنا: جلب العقود المالية لطلاب ولي الأمر 🔥
        ])->findOrFail($id);

        return view('backend.guardians.show', compact('guardian'));
    }

    // ==========================================
    // 3. إضافة ولي أمر جديد
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('create_guardians'), 403);

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'national_id' => 'required|string|unique:guardians,national_id',
            'national_id_expiry' => 'nullable|date',
            'phone' => 'required|string|unique:guardians,phone',
            'email' => 'required|email|unique:guardians,email',
            'nationality_ar' => 'nullable|string|max:100',
            'nationality_en' => 'nullable|string|max:100',
            'job_title_ar' => 'nullable|string|max:100',
            'job_title_en' => 'nullable|string|max:100',
            'work_place_ar' => 'nullable|string|max:255',
            'work_place_en' => 'nullable|string|max:255',
            'id_card_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except('id_card_image');
        
        // الباسورد الافتراضي هو رقم التليفون
        $data['password'] = Hash::make($request->phone);

        if ($request->hasFile('id_card_image')) {
            $data['id_card_image'] = $request->file('id_card_image')->store('guardians_ids', 'public');
        }

        Guardian::create($data);

        return redirect()->back()->with('success', 'تمت إضافة ولي الأمر (العائلة) بنجاح.');
    }

    // ==========================================
    // 4. تعديل بيانات ولي الأمر
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('edit_guardians'), 403);

        $guardian = Guardian::findOrFail($id);

        $request->validate([
            'branch_id' => 'required|exists:branches,id', 
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'national_id' => 'required|string|unique:guardians,national_id,'.$id,
            'national_id_expiry' => 'nullable|date',
            'phone' => 'required|string|unique:guardians,phone,'.$id,
            'email' => 'required|email|unique:guardians,email,'.$id,
            'nationality_ar' => 'nullable|string|max:100',
            'nationality_en' => 'nullable|string|max:100',
            'job_title_ar' => 'nullable|string|max:100',
            'job_title_en' => 'nullable|string|max:100',
            'work_place_ar' => 'nullable|string|max:255',
            'work_place_en' => 'nullable|string|max:255',
            'id_card_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'password' => 'nullable|string|min:6', // اختياري في التعديل
        ]);

        $data = $request->except(['id_card_image', 'password']);

        // تحديث الباسورد فقط إذا قام المستخدم بكتابة باسورد جديد
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('id_card_image')) {
            if ($guardian->id_card_image) {
                Storage::disk('public')->delete($guardian->id_card_image);
            }
            $data['id_card_image'] = $request->file('id_card_image')->store('guardians_ids', 'public');
        }

        $guardian->update($data);

        return redirect()->back()->with('success', 'تم تحديث بيانات ولي الأمر بنجاح.');
    }

    // ==========================================
    // 5. حذف ولي الأمر
    // ==========================================
    public function destroy($id)
    {
        // abort_if(!auth()->user()->can('delete_guardians'), 403);

        $guardian = Guardian::findOrFail($id);
        
        // حماية النظام من مسح ولي أمر عنده أبناء (الحفاظ على Data Integrity)
        if ($guardian->students()->exists()) {
            return redirect()->back()->with('error', 'عفواً، لا يمكن حذف ولي الأمر لوجود طلاب (أبناء) مسجلين باسمه في النظام. يرجى نقل الطلاب أولاً.');
        }

        if ($guardian->id_card_image) {
            Storage::disk('public')->delete($guardian->id_card_image);
        }
        
        $guardian->delete();
        
        return redirect()->route('admin.guardians.index')->with('success', 'تم حذف ملف ولي الأمر بالكامل.');
    }
}