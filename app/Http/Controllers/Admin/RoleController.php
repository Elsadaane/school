<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    // ==========================================
    // 🔥 1. عرض قائمة الأدوار (الصلاحيات) 🔥
    // ==========================================
    public function index()
    {
        // 🔒 الحماية: يجب أن يكون المستخدم نفسه لديه صلاحية إدارة الصلاحيات
        abort_if(!auth()->user()->can('manage_roles'), 403, 'غير مصرح لك بإدارة أدوار النظام.');

        $roles = Role::with('permissions')->get();
        return view('backend.roles.index', compact('roles'));
    }

    // ==========================================
    // 🔥 2. شاشة إضافة دور جديد 🔥
    // ==========================================
    public function create()
    {
        abort_if(!auth()->user()->can('manage_roles'), 403);

        $permissionsGrouped = Permission::all()->groupBy('group_name');
        return view('backend.roles.create', compact('permissionsGrouped'));
    }

    // ==========================================
    // 🔥 3. حفظ الدور الجديد وإعطاؤه الصلاحيات 🔥
    // ==========================================
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('manage_roles'), 403);

        $request->validate([
            'name'        => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id' // 🌟 حماية ضد حقن صلاحيات وهمية 🌟
        ], [
            'permissions.required' => 'يجب تحديد صلاحية واحدة على الأقل لهذا الدور.'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

            $permissionsIds = array_map('intval', $request->permissions);
            $role->syncPermissions($permissionsIds);

            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'تم إنشاء الدور وتخصيص الصلاحيات بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ الصلاحيات: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 4. شاشة تعديل دور موجود 🔥
    // ==========================================
    public function edit($id)
    {
        abort_if(!auth()->user()->can('manage_roles'), 403);

        $role = Role::with('permissions')->findOrFail($id);
        
        // 🌟 منع التعديل على الإدارة العليا للمدرسة 🌟
        if ($role->id == 1 || in_array($role->name, ['Admin', 'Super Admin'])) {
            return redirect()->route('admin.roles.index')->with('error', 'مرفوض أمنياً: لا يمكن التعديل على صلاحيات مدير النظام الأساسي!');
        }

        $permissionsGrouped = Permission::all()->groupBy('group_name');
        $rolePermissions = $role->permissions->pluck('id')->toArray(); 

        return view('backend.roles.edit', compact('role', 'permissionsGrouped', 'rolePermissions'));
    }

    // ==========================================
    // 🔥 5. حفظ التعديلات على الدور 🔥
    // ==========================================
    public function update(Request $request, $id)
    {
        abort_if(!auth()->user()->can('manage_roles'), 403);

        $role = Role::findOrFail($id);

        if ($role->id == 1 || in_array($role->name, ['Admin', 'Super Admin'])) {
            return redirect()->route('admin.roles.index')->with('error', 'مرفوض أمنياً: لا يمكن تعديل صلاحيات مدير النظام الأساسي!');
        }

        $request->validate([
            'name'        => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ], [
            'permissions.required' => 'يجب إبقاء صلاحية واحدة على الأقل لهذا الدور.'
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $request->name]);

            $permissionsIds = array_map('intval', $request->permissions);
            $role->syncPermissions($permissionsIds);

            DB::commit();
            return redirect()->route('admin.roles.index')->with('success', 'تم تحديث الدور وتعديل صلاحياته بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 🔥 6. حذف دور (صلاحية) مع الحماية الذكية 🔥
    // ==========================================
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('manage_roles'), 403);

        $role = Role::findOrFail($id);
        
        if ($role->id == 1 || in_array($role->name, ['Admin', 'Super Admin'])) {
            return redirect()->back()->with('error', 'مرفوض أمنياً: النظام يمنع حذف صلاحية مدير النظام الأساسي!');
        }

        $usersCount = $role->users()->count();
        if ($usersCount > 0) {
            return redirect()->back()->with('error', 'عفواً! لا يمكن حذف هذا الدور لأن هناك (' . $usersCount . ') مستخدم مرتبط به. يرجى نقل المستخدمين لدور آخر أولاً.');
        }

        $role->delete();
        return redirect()->back()->with('success', 'تم حذف الدور والصلاحيات المرتبطة به بنجاح!');
    }
}