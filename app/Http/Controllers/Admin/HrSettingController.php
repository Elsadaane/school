<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrDepartment;
use App\Models\HrShift;
use App\Models\HrJobGrade;
use Illuminate\Support\Facades\DB;

class HrSettingController extends Controller
{
    // ==========================================
    // 1. عرض صفحة الإعدادات الشاملة للـ HR
    // ==========================================
    public function index()
    {
        abort_if(!auth()->user()->can('manage_hr_settings'), 403, 'غير مصرح لك بإدارة إعدادات الموارد البشرية.');

        $departments = HrDepartment::withCount(['employees' => function($query) {
            $query->whereNull('deleted_at');
        }])->orderBy('id', 'desc')->get();

        $shifts = HrShift::withCount(['employees' => function($query) {
            $query->whereNull('deleted_at');
        }])->orderBy('id', 'desc')->get();

        $grades = HrJobGrade::withCount(['employees' => function($query) {
            $query->whereNull('deleted_at');
        }])->orderBy('id', 'desc')->get();

        return view('backend.hr.settings.index', compact('departments', 'shifts', 'grades'));
    }

    // ==========================================
    // 🏢 أولاً: إدارة الأقسام (Departments)
    // ==========================================
    public function storeDepartment(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_settings'), 403);

        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
        ]);

        HrDepartment::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
        ]);

        return redirect()->back()->with('success', 'تم إضافة القسم بنجاح.')->with('active_tab', 'departments');
    }

    public function updateDepartment(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_hr_settings'), 403);

        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
        ]);

        $department = HrDepartment::findOrFail($id);
        $department->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
        ]);

        return redirect()->back()->with('success', 'تم تحديث بيانات القسم بنجاح.')->with('active_tab', 'departments');
    }

    public function destroyDepartment($id)
    {
        abort_if(!auth()->user()->can('manage_hr_settings'), 403);

        $department = HrDepartment::findOrFail($id);

        if ($department->employees()->exists()) {
            return redirect()->back()->withErrors(['error' => 'لا يمكن حذف القسم لارتباطه بموظفين في النظام. يرجى نقل الموظفين أولاً.'])->with('active_tab', 'departments');
        }

        $department->delete();
        return redirect()->back()->with('success', 'تم حذف القسم بنجاح.')->with('active_tab', 'departments');
    }


    // ==========================================
    // 🕒 ثانياً: إدارة الورديات (Shifts)
    // ==========================================
    public function storeShift(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_settings'), 403);

        $request->validate([
            'name_ar'              => 'required|string|max:255',
            'start_time'           => 'required|date_format:H:i',
            'end_time'             => 'required|date_format:H:i',
            'grace_period_minutes' => 'nullable|integer|min:0',
        ]);

        HrShift::create([
            'name_ar'              => $request->name_ar,
            'start_time'           => $request->start_time,
            'end_time'             => $request->end_time,
            'grace_period_minutes' => $request->grace_period_minutes ?? 0,
        ]);

        return redirect()->back()->with('success', 'تم إضافة الوردية بنجاح.')->with('active_tab', 'shifts');
    }

    public function updateShift(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_hr_settings'), 403);

        $request->validate([
            'name_ar'              => 'required|string|max:255',
            'start_time'           => 'required|date_format:H:i',
            'end_time'             => 'required|date_format:H:i',
            'grace_period_minutes' => 'nullable|integer|min:0',
        ]);

        $shift = HrShift::findOrFail($id);
        $shift->update([
            'name_ar'              => $request->name_ar,
            'start_time'           => $request->start_time,
            'end_time'             => $request->end_time,
            'grace_period_minutes' => $request->grace_period_minutes ?? 0,
        ]);

        return redirect()->back()->with('success', 'تم تحديث بيانات الوردية بنجاح.')->with('active_tab', 'shifts');
    }

    public function destroyShift($id)
    {
        abort_if(!auth()->user()->can('manage_hr_settings'), 403);

        $shift = HrShift::findOrFail($id);

        if ($shift->employees()->exists()) {
            return redirect()->back()->withErrors(['error' => 'لا يمكن حذف الوردية لارتباطها بموظفين. يرجى تعديل ورديات الموظفين أولاً.'])->with('active_tab', 'shifts');
        }

        $shift->delete();
        return redirect()->back()->with('success', 'تم حذف الوردية بنجاح.')->with('active_tab', 'shifts');
    }


    // ==========================================
    // 🌟 ثالثاً: إدارة الدرجات الوظيفية (Job Grades)
    // ==========================================
    public function storeGrade(Request $request)
    {
        abort_if(!auth()->user()->can('manage_hr_settings'), 403);

        $request->validate([
            'name_ar'          => 'required|string|max:255',
            'min_basic_salary' => 'required|numeric|min:0',
            'max_basic_salary' => 'required|numeric|min:0|gte:min_basic_salary',
        ]);

        HrJobGrade::create([
            'name_ar'          => $request->name_ar,
            'min_basic_salary' => $request->min_basic_salary,
            'max_basic_salary' => $request->max_basic_salary,
        ]);

        return redirect()->back()->with('success', 'تم إضافة الدرجة الوظيفية بنجاح.')->with('active_tab', 'grades');
    }

    public function updateGrade(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_hr_settings'), 403);

        $request->validate([
            'name_ar'          => 'required|string|max:255',
            'min_basic_salary' => 'required|numeric|min:0',
            'max_basic_salary' => 'required|numeric|min:0|gte:min_basic_salary',
        ]);

        $grade = HrJobGrade::findOrFail($id);
        $grade->update([
            'name_ar'          => $request->name_ar,
            'min_basic_salary' => $request->min_basic_salary,
            'max_basic_salary' => $request->max_basic_salary,
        ]);

        return redirect()->back()->with('success', 'تم تحديث الدرجة الوظيفية بنجاح.')->with('active_tab', 'grades');
    }

    public function destroyGrade($id)
    {
        abort_if(!auth()->user()->can('manage_hr_settings'), 403);

        $grade = HrJobGrade::findOrFail($id);

        if ($grade->employees()->exists()) {
            return redirect()->back()->withErrors(['error' => 'لا يمكن حذف هذه الدرجة لارتباطها بموظفين على رأس العمل.'])->with('active_tab', 'grades');
        }

        $grade->delete();
        return redirect()->back()->with('success', 'تم حذف الدرجة الوظيفية بنجاح.')->with('active_tab', 'grades');
    }
}