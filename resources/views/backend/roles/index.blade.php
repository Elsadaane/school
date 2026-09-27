@extends('backend.layouts.master')
@section('title', 'إدارة الأدوار والصلاحيات')
@section('title_page', 'إعدادات النظام - صلاحيات المستخدمين')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
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

        .table-erp {
            margin-bottom: 0;
            white-space: nowrap;
            /* تأمين الجداول من التكسير */
        }

        .table-erp thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
            padding: 14px;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        .role-badge {
            font-weight: 800;
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 6px;
            background: #e0e7ff;
            color: #3730a3;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
            border: 1px solid transparent;
            color: #64748b;
            background: #fff;
            text-decoration: none;
        }

        .action-icon:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .action-icon.edit:hover {
            background: #f59e0b;
            color: #fff;
            border-color: #f59e0b;
        }

        .action-icon.delete:hover {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .table-erp tbody tr:hover td {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .role-badge {
            background: rgba(55, 48, 163, 0.2);
            color: #a5b4fc;
        }

        body[data-layout-color="dark"] .action-icon {
            background: #3b4652;
            border-color: #424e5a;
            color: #adb5bd;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 الهيدر --}}
        <div class="card card-premium mb-4 border-top border-primary border-4 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center p-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary shadow-sm">
                        <i class="mdi mdi-shield-account-outline fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">صلاحيات النظام (Roles)</h4>
                        <p class="text-muted mb-0 font-13">إدارة وتخصيص مجموعات الصلاحيات والمناصب الوظيفية في النظام.</p>
                    </div>
                </div>

                {{-- 🔥 حماية زر الإضافة 🔥 --}}
                @can('create_roles')
                    <div>
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="mdi mdi-plus-circle-outline font-18 align-middle me-1"></i> إضافة دور جديد
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        {{-- 🌟 جدول الصلاحيات --}}
        <div class="card card-premium shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-erp text-center align-middle mb-0">
                        <thead class="sticky-top" style="z-index: 1;">
                            <tr>
                                <th style="width: 80px;">#</th>
                                <th class="text-start ps-4">اسم الدور الوظيفي (Role)</th>
                                <th>عدد الصلاحيات الممنوحة</th>

                                {{-- 🔥 حماية عمود الإجراءات بالكامل 🔥 --}}
                                @canany(['edit_roles', 'delete_roles'])
                                    <th>إجراءات الإدارة</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $index => $role)
                                <tr>
                                    {{-- تظبيط الترقيم في حال استخدام Pagination --}}
                                    <td class="text-muted fw-bold">
                                        {{ method_exists($roles, 'firstItem') ? $roles->firstItem() + $index : $index + 1 }}
                                    </td>

                                    <td class="text-start ps-4">
                                        <span class="role-badge shadow-sm"><i class="mdi mdi-shield-key-outline me-1"></i>
                                            {{ $role->name }}</span>
                                    </td>

                                    <td>
                                        <span
                                            class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill fs-6 fw-bold border border-info border-opacity-25 shadow-sm">
                                            {{ $role->permissions->count() }} صلاحية
                                        </span>
                                    </td>

                                    @canany(['edit_roles', 'delete_roles'])
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center align-items-center">
                                                @if ($role->name !== 'Admin')
                                                    {{-- 🔥 حماية زر التعديل 🔥 --}}
                                                    @can('edit_roles')
                                                        <a href="{{ route('admin.roles.edit', $role->id) }}"
                                                            class="action-icon edit shadow-sm" title="تعديل الصلاحيات والمزايا">
                                                            <i class="mdi mdi-square-edit-outline font-18"></i>
                                                        </a>
                                                    @endcan

                                                    {{-- 🔥 حماية زر الحذف 🔥 --}}
                                                    @can('delete_roles')
                                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                            class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="action-icon delete btn-delete shadow-sm"
                                                                title="حذف الدور الوظيفي">
                                                                <i class="mdi mdi-trash-can-outline font-18"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @else
                                                    {{-- علامة حماية لدور الآدمن --}}
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold border border-success border-opacity-25 shadow-sm">
                                                        <i class="mdi mdi-shield-check me-1"></i> أساسي (محمي)
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted py-5 fs-5 fw-bold bg-white text-center">
                                        <i class="mdi mdi-shield-off-outline fs-1 d-block mb-2 opacity-50"></i> لا توجد
                                        أدوار أو صلاحيات مسجلة في النظام.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 الحفاظ على الـ Pagination إذا كان مستخدماً في الـ Controller 🔥 --}}
            @if (method_exists($roles, 'hasPages') && $roles->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            // إشعارات النظام التفاعلية (Toasts)
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif

            // SweetAlert لتأكيد الحذف
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'هل أنت متأكد من الحذف؟',
                    text: "سيتم حذف هذا الدور الوظيفي ولن يتمكن المستخدمون المرتبطون به من الوصول لصلاحياته!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف الدور',
                    cancelButtonText: 'تراجع وإلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
