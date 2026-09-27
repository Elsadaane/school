<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HrEmployee;
use App\Models\Setting; // 🔥 استدعاء جدول الإعدادات 🔥
use Illuminate\Support\Facades\Storage;

class HrOrgChartController extends Controller
{
    // ==========================================
    // 🔥 عرض الهيكل التنظيمي للشركة (Org Chart) 🔥
    // ==========================================
    public function index()
    {
        // حماية الشاشة بصلاحيات الـ HR
        abort_if(!auth()->user()->can('view_employees'), 403, 'غير مصرح لك بالاطلاع على الهيكل التنظيمي.');

        // 🔥 سحب اسم المدرسة واللوجو من الإعدادات ديناميكياً 🔥
        $settings = Setting::first();
        $schoolName = $settings->company_name_ar ?? 'مؤسستنا التعليمية';
        $schoolLogo = $settings->logo ? Storage::disk('public')->url($settings->logo) : asset('backend/assets/images/logo.png');

        // بنجيب كل الموظفين النشطين مع أقسامهم (هيجيب الإداريين والمدرسين المربوطين بالـ HR)
        $employees = HrEmployee::with('department')->where('status', 'active')->get();

        // بنبني الشجرة البرمجية (JSON) عشان نبعتها لملف الـ Blade
        $treeData = [
            'name'      => $schoolName, // الاسم ديناميكي
            'title'     => 'الإدارة العليا / مجلس الإدارة',
            'className' => 'top-level',
            'image'     => $schoolLogo, // اللوجو ديناميكي
            'children'  => $this->buildTree($employees, null)
        ];
        // dd($employees->toArray());

        return view('backend.hr.org_chart.index', compact('treeData'));
    }

    // ==========================================
    // 🔥 دالة سحرية (Recursive) لترتيب الموظفين والمدرسين 🔥
    // ==========================================
    private function buildTree($employees, $managerId = null)
    {
        $branch = [];
        foreach ($employees as $employee) {
            // لو الـ manager_id بتاع الموظف/المعلم بيساوي الـ ID اللي بندور عليه
            if ($employee->manager_id == $managerId) {
                
                // 🔥 معالجة مسار الصورة بناءً على الـ Storage Architecture اللي أسسناه 🔥
                $imageUrl = asset('backend/assets/images/users/avatar-1.png'); // صورة افتراضية
                if ($employee->profile_image) {
                    $imageUrl = Storage::disk('public')->url($employee->profile_image);
                }

                $node = [
                    'name'      => $employee->full_name_ar,
                    'title'     => $employee->job_title . ' (' . ($employee->department->name_ar ?? 'بدون قسم') . ')',
                    'className' => 'employee-node',
                    'image'     => $imageUrl,
                ];

                // بندور لو الموظف/المعلم ده تحته ناس تانيين (مرؤوسين)
                $children = $this->buildTree($employees, $employee->id);
                if (!empty($children)) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }
        return $branch;
    }
}