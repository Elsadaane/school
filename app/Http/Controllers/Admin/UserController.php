<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HrEmployee; // 🔥 استدعاء موديل الموظفين لربط اليوزر بالـ HR 🔥
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // ==========================================
    // 🔥 عرض قائمة المستخدمين والإداريين 🔥
    // ==========================================
    public function index()
    {
        // 🔒 حماية الشاشة
        abort_if(!auth()->user()->can('manage_users'), 403, 'غير مصرح لك بإدارة مستخدمي النظام.');

        // جلب المستخدمين مع أدوارهم (تجنب N+1 Query) + استخدام Paginate للأداء
        $users = User::with('roles', 'employee')->orderBy('created_at', 'desc')->paginate(20);
        return view('backend.users.index', compact('users'));
    }

    // ==========================================
    // 🔥 شاشة إضافة مستخدم جديد 🔥
    // ==========================================
    public function create()
    {
        abort_if(!auth()->user()->can('manage_users'), 403);

        $roles = Role::all();
        // جلب الموظفين الذين ليس لديهم حساب يوزر بعد
        $employees = HrEmployee::whereNull('user_id')->where('status', 'active')->get(); 
        
        return view('backend.users.create', compact('roles', 'employees'));
    }

    // ==========================================
    // 🔥 حفظ المستخدم الجديد وتعيين الصلاحيات 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_users'), 403);

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:6|confirmed',
            'role'           => 'required|exists:roles,name',
            'hr_employee_id' => 'nullable|exists:hr_employees,id' // 🔥 للربط بالـ HR
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'status'   => 'active', 
            ]);

            $user->assignRole($request->role);

            // 🔥 ربط اليوزر بملف الموظف في הـ HR 🔥
            if ($request->filled('hr_employee_id')) {
                HrEmployee::where('id', $request->hr_employee_id)->update(['user_id' => $user->id]);
            }

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'تم إضافة المستخدم وتحديد صلاحياته بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة المستخدم: ' . $e->getMessage())->withInput();
        }
    }

    // ==========================================
    // 🔥 شاشة التعديل 🔥
    // ==========================================
    public function edit($id)
    {
        abort_if(!auth()->user()->can('manage_users'), 403);

        $user = User::findOrFail($id);
        
        if ($user->id == 1 && auth()->id() != 1) {
            return redirect()->route('admin.users.index')->with('error', 'مرفوض أمنياً: لا يمكنك الوصول لبيانات المدير العام.');
        }

        $roles = Role::all();
        $employees = HrEmployee::where('status', 'active')->get(); 

        return view('backend.users.edit', compact('user', 'roles', 'employees'));
    }

    // ==========================================
    // 🔥 حفظ التعديلات 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_users'), 403);

        $user = User::findOrFail($id);

        if ($user->id == 1 && auth()->id() != 1) {
            return redirect()->back()->with('error', 'مرفوض أمنياً: لا يمكنك تعديل بيانات أو صلاحيات المدير العام للنظام!');
        }

        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $id,
            'role'           => 'required|exists:roles,name',
            'password'       => 'nullable|string|min:6|confirmed', // 🔥 تأمين التأكيد 🔥
            'hr_employee_id' => 'nullable|exists:hr_employees,id' 
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $user->syncRoles($request->role);

            // تحديث ربط الـ HR
            if ($request->filled('hr_employee_id')) {
                // فك ارتباط أي موظف قديم بهذا اليوزر
                HrEmployee::where('user_id', $user->id)->update(['user_id' => null]);
                // ربط الموظف الجديد
                HrEmployee::where('id', $request->hr_employee_id)->update(['user_id' => $user->id]);
            } else {
                HrEmployee::where('user_id', $user->id)->update(['user_id' => null]);
            }

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات المستخدم وتعديل صلاحياته بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 حذف مستخدم من النظام 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_users'), 403);

        $user = User::findOrFail($id);
        
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'مرفوض أمنياً: لا يمكنك حذف حسابك الشخصي!');
        }

        if ($user->id == 1) {
            return redirect()->back()->with('error', 'مرفوض أمنياً: النظام يمنع بشكل قاطع حذف حساب المدير العام الرئيسي!');
        }

        try {
            DB::beginTransaction();
            // فك الارتباط بالـ HR قبل الحذف
            HrEmployee::where('user_id', $user->id)->update(['user_id' => null]);
            $user->delete();
            DB::commit();
            return redirect()->back()->with('success', 'تم حذف المستخدم من النظام بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 دالة التعليق / التنشيط السحرية 🔥
    // ==========================================
    public function toggleStatus($id)
    {
        abort_if(!auth()->user()->can('manage_users'), 403);

        $user = User::findOrFail($id);
        
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'مرفوض أمنياً: لا يمكنك تعليق حسابك الشخصي النشط حالياً!');
        }

        if ($user->id == 1) {
            return redirect()->back()->with('error', 'مرفوض أمنياً: لا يمكن تعليق حساب المدير العام الرئيسي!');
        }

        $user->status = ($user->status == 'active') ? 'inactive' : 'active';
        $user->save();

        $msg = ($user->status == 'active') ? 'تم تنشيط الحساب بنجاح، يمكن للمستخدم الدخول الآن.' : 'تم تعليق الحساب بنجاح، لن يتمكن المستخدم من تسجيل الدخول.';
        return redirect()->back()->with('success', $msg);
    }
}