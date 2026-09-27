@extends('backend.layouts.master')
@section('title', 'تعديل صلاحية')
@section('title_page', 'إعدادات النظام - تعديل صلاحية موجودة')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #fff;
        }

        .permission-group {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 20px;
            height: 100%;
            background: #fff;
            transition: all 0.3s ease;
        }

        .permission-group:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border-color: #94a3b8;
        }

        .group-title {
            font-weight: 800;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 15px;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .group-title-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .group-title i {
            color: #3b82f6;
            font-size: 20px;
            background: #eff6ff;
            padding: 6px;
            border-radius: 8px;
        }

        .form-check-label {
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            font-size: 14px;
        }

        .form-check-input {
            cursor: pointer;
            width: 1.2em;
            height: 1.2em;
            margin-top: 0.15em;
        }

        .form-check-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        /* تنسيق الـ Switch الرئيسي */
        .master-switch .form-check-input {
            width: 3em;
            height: 1.5em;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .permission-group {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .group-title {
            color: #f8fafc;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-check-label {
            color: #cbd5e1;
        }

        body[data-layout-color="dark"] .group-title i {
            background: #1e293b;
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 الهيدر --}}
        <div class="card card-premium mb-4 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center p-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info shadow-sm">
                        <i class="mdi mdi-shield-edit-outline fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">تعديل الصلاحية: <span
                                class="text-info">{{ $role->name }}</span></h4>
                        <p class="text-muted mb-0 font-13">تخصيص الشاشات والعمليات المسموح لهذا الدور بالوصول إليها.</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.roles.index') }}"
                        class="btn btn-light border rounded-pill px-4 fw-bold shadow-sm">
                        <i class="mdi mdi-arrow-right font-18 align-middle me-1"></i> العودة للخلف
                    </a>
                </div>
            </div>
        </div>

        {{-- 🔥 عرض رسائل الخطأ إن وجدت 🔥 --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 rounded-3 fw-bold mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="prevent-double-submit">
            @csrf
            @method('PUT')

            {{-- 🌟 البيانات الأساسية للصلاحية --}}
            <div class="card card-premium mb-4 border-top border-info border-4 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold fs-6 text-dark mb-2">اسم الدور الوظيفي (Role) <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name"
                                class="form-control form-control-lg bg-light border-0 shadow-none fw-bold text-primary"
                                value="{{ old('name', $role->name) }}" required
                                {{ $role->name === 'Admin' ? 'readonly' : '' }}>
                            @if ($role->name === 'Admin')
                                <small class="text-danger mt-1 d-block fw-bold"><i class="mdi mdi-lock-outline"></i> النظام
                                    لا يسمح بتغيير اسم دور المشرف العام الأساسي.</small>
                            @endif
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0 d-flex justify-content-md-end">
                            <div
                                class="form-check form-switch master-switch fs-5 bg-info bg-opacity-10 p-3 rounded-4 border border-info border-opacity-25 d-inline-flex align-items-center shadow-sm">
                                <input class="form-check-input ms-0 me-3 mt-0" type="checkbox" id="selectAll">
                                <label class="form-check-label text-info fw-bolder mb-0 cursor-pointer"
                                    for="selectAll">تحديد كل صلاحيات النظام بالكامل</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="alert alert-primary border-primary border-opacity-25 bg-primary bg-opacity-10 fw-bold rounded-3 shadow-sm d-flex align-items-center mb-4">
                <i class="mdi mdi-information fs-3 me-2 text-primary"></i>
                يمكنك تحديد أو إلغاء تحديد مجموعة كاملة بسرعة عبر تفعيل المربع الموجود بجوار اسم المجموعة.
            </div>

            <h5 class="fw-bolder mb-3 text-dark d-flex align-items-center gap-2">
                <i class="mdi mdi-checkbox-multiple-marked-outline text-success fs-3"></i>
                تخصيص شاشات النظام المسموح الدخول إليها:
            </h5>

            {{-- 🌟 شبكة الصلاحيات (Grid) --}}
            <div class="row g-4">
                @foreach ($permissionsGrouped as $groupName => $permissions)
                    @php
                        $groupId = 'group_' . md5($groupName);
                    @endphp
                    <div class="col-xl-4 col-lg-6">
                        <div class="permission-group shadow-sm">
                            <div class="group-title">
                                <div class="group-title-content">
                                    <i class="mdi mdi-folder-open-outline"></i>
                                    <span>{{ $groupName ?? 'صلاحيات عامة' }}</span>
                                </div>
                                {{-- 🔥 Checkbox تحديد المجموعة بالكامل 🔥 --}}
                                <div class="form-check m-0">
                                    <input class="form-check-input group-checkbox shadow-sm" type="checkbox"
                                        id="{{ $groupId }}" data-group="{{ $groupId }}">
                                    <label class="form-check-label font-12 fw-bold text-muted"
                                        for="{{ $groupId }}">الكل</label>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                @foreach ($permissions as $permission)
                                    <div class="col-12">
                                        <div class="form-check d-flex align-items-center gap-2 m-0">
                                            <input
                                                class="form-check-input perm-checkbox item-of-{{ $groupId }} flex-shrink-0 shadow-sm"
                                                type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                id="perm_{{ $permission->id }}" data-parent="{{ $groupId }}"
                                                {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || (!old('permissions') && $role->hasPermissionTo($permission->name)) ? 'checked' : '' }}>

                                            <label class="form-check-label w-100" for="perm_{{ $permission->id }}">
                                                @if (app()->getLocale() == 'ar')
                                                    {{ $permission->arabic_name ?? $permission->name }}
                                                @else
                                                    {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 🌟 زر الحفظ --}}
            <div class="text-center mt-5 mb-5 border-top pt-4">
                <button type="submit" class="btn btn-primary rounded-pill btn-lg px-5 fw-bold shadow-sm btn-submit">
                    <i class="mdi mdi-content-save-outline fs-4 align-middle me-1"></i> حفظ وتحديث الصلاحيات
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const groupCheckboxes = document.querySelectorAll('.group-checkbox');
            const permissionCheckboxes = document.querySelectorAll('.perm-checkbox');

            // دالة للتحقق من المجموعات
            const checkGroupSync = () => {
                groupCheckboxes.forEach(groupCheckbox => {
                    const groupId = groupCheckbox.getAttribute('data-group');
                    const childCheckboxes = document.querySelectorAll(`.item-of-${groupId}`);
                    if (childCheckboxes.length > 0) {
                        const allSiblingsChecked = Array.from(childCheckboxes).every(cb => cb.checked);
                        groupCheckbox.checked = allSiblingsChecked;
                    }
                });
            };

            // دالة للتحقق من الزر الرئيسي
            const checkMasterSync = () => {
                const allChecked = Array.from(permissionCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            };

            // تشغيل التزامن مرة واحدة عند تحميل الصفحة (تحديد المجموعات والزر الرئيسي تلقائياً إذا كانوا مختارين)
            checkGroupSync();
            checkMasterSync();

            // 1. تحديد/إلغاء تحديد النظام بالكامل (الزر الرئيسي)
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;

                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });

                groupCheckboxes.forEach(groupCheckbox => {
                    groupCheckbox.checked = isChecked;
                });
            });

            // 2. تحديد/إلغاء تحديد مجموعة معينة (Group Checkbox)
            groupCheckboxes.forEach(groupCheckbox => {
                groupCheckbox.addEventListener('change', function() {
                    const groupId = this.getAttribute('data-group');
                    const childCheckboxes = document.querySelectorAll(`.item-of-${groupId}`);

                    childCheckboxes.forEach(child => {
                        child.checked = this.checked;
                    });

                    checkMasterSync();
                });
            });

            // 3. عند تغيير أي Checkbox فردي
            permissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const parentId = this.getAttribute('data-parent');
                    const parentCheckbox = document.getElementById(parentId);
                    const siblings = document.querySelectorAll(`.item-of-${parentId}`);

                    const allSiblingsChecked = Array.from(siblings).every(cb => cb.checked);
                    parentCheckbox.checked = allSiblingsChecked;

                    checkMasterSync();
                });
            });

            // 🔥 تأمين الفورم ضد الإرسال المزدوج 🔥
            const form = document.querySelector('.prevent-double-submit');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('.btn-submit');
                    btn.disabled = true;
                    btn.innerHTML =
                        '<i class="mdi mdi-loading mdi-spin me-1 fs-4 align-middle"></i> جاري حفظ وتحديث الصلاحيات...';
                });
            }
        });
    </script>
@endpush
