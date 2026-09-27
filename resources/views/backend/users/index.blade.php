@extends('backend.layouts.master')
@section('title', 'المستخدمين / المدراء')
@section('title_page', 'إعدادات النظام - إدارة المستخدمين')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            border-top: 5px solid #3b82f6;
        }

        .table-erp {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px;
            text-align: center;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        .role-badge {
            font-weight: 800;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 6px;
            background: #e0e7ff;
            color: #3730a3;
            margin-left: 3px;
            display: inline-block;
            border: 1px solid #c7d2fe;
        }

        /* الأزرار الموحدة */
        .action-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: 0.2s;
            border: 1px solid transparent;
            color: #64748b;
            background: #fff;
            padding: 0;
            cursor: pointer;
            text-decoration: none;
        }

        .action-icon:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .action-icon.edit {
            background: #fef3c7;
            color: #d97706;
            border-color: #fde68a;
        }

        .action-icon.edit:hover:not(:disabled) {
            background: #f59e0b;
            color: #fff;
        }

        .action-icon.delete {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fecaca;
        }

        .action-icon.delete:hover:not(:disabled) {
            background: #ef4444;
            color: #fff;
        }

        .action-icon.suspend {
            background: #ffedd5;
            color: #b45309;
            border-color: #fde047;
        }

        .action-icon.suspend:hover:not(:disabled) {
            background: #f59e0b;
            color: #fff;
        }

        .action-icon.activate {
            background: #dcfce7;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .action-icon.activate:hover:not(:disabled) {
            background: #10b981;
            color: #fff;
        }

        .action-icon:disabled,
        .action-icon.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .filter-bar {
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
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody tr:hover td {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .role-badge {
            background: #1e293b;
            color: #818cf8;
            border-color: #3b82f6;
        }

        body[data-layout-color="dark"] .action-icon {
            background: #3b4652;
            border-color: #424e5a;
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .bg-white {
            background-color: #323a46 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 الهيدر --}}
        <div class="row mb-3 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold text-dark mb-1"><i
                            class="mdi mdi-account-tie-outline text-primary me-1 fs-3 align-middle"></i> إدارة المستخدمين
                        والمدراء</h4>
                    <p class="text-muted mb-0 font-14">إدارة حسابات الدخول، تعيين الصلاحيات الإدارية، ومراقبة نشاط الموظفين.
                    </p>
                </div>

                {{-- 🔥 حماية زر الإضافة بصلاحية الإنشاء 🔥 --}}
                @can('create_users')
                    <div>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="mdi mdi-plus-circle-outline font-18 align-middle me-1"></i> إضافة مستخدم جديد
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        {{-- 🌟 شريط البحث (مُفعل Server-side) --}}
        <div class="card card-premium mb-4 border-0 shadow-sm border-top border-primary border-4 p-3 bg-white">
            <form action="{{ route('admin.users.index') }}" method="GET" class="prevent-double-submit">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group shadow-sm rounded-3">
                            <span class="input-group-text bg-light border-end-0"><i
                                    class="mdi mdi-magnify text-primary"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 fw-bold bg-light"
                                placeholder="ابحث باسم الموظف أو البريد الإلكتروني..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark fw-bold shadow-sm flex-grow-1 btn-submit">بحث
                            وتصفية</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm"
                            title="إعادة ضبط"><i class="mdi mdi-refresh fs-5 align-middle"></i></a>
                    </div>
                    <div class="col-md-4 text-end">
                        <span
                            class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 font-13 rounded-pill shadow-sm border border-primary border-opacity-25">
                            إجمالي الحسابات: <span
                                class="fw-bolder font-monospace fs-5 ms-1">{{ method_exists($users, 'total') ? $users->total() : $users->count() }}</span>
                        </span>
                    </div>
                </div>
            </form>
        </div>

        {{-- 🌟 الإشعارات --}}
        @if (session('success'))
            <div class="alert alert-success shadow-sm fw-bold rounded-3 border-0 mb-4"><i
                    class="mdi mdi-check-circle fs-5 align-middle me-1"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger shadow-sm fw-bold rounded-3 border-0 mb-4"><i
                    class="mdi mdi-alert-circle fs-5 align-middle me-1"></i> {{ session('error') }}</div>
        @endif

        {{-- 🌟 جدول المستخدمين --}}
        <div class="card card-premium border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-erp table-hover text-center align-middle mb-0">
                        <thead class="sticky-top" style="z-index: 10;">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th class="text-start ps-4">بيانات الحساب</th>
                                <th>الأدوار / الصلاحيات المُسندة</th>
                                <th>تاريخ الانضمام</th>
                                <th>الحالة</th>

                                {{-- 🔥 إخفاء عمود الإجراءات بالكامل لو مفيش صلاحية 🔥 --}}
                                @canany(['edit_users', 'delete_users'])
                                    <th style="width: 140px;">إجراءات الإدارة</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $index => $user)
                                <tr>
                                    <td class="text-muted fw-bold">
                                        {{ method_exists($users, 'firstItem') ? $users->firstItem() + $index : $index + 1 }}
                                    </td>

                                    <td class="text-start ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-sm">
                                                <span
                                                    class="avatar-title bg-primary bg-opacity-10 border border-primary border-opacity-25 text-primary rounded-circle fw-bold shadow-sm fs-5">
                                                    {{ mb_substr($user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="fw-bolder text-dark d-block fs-6">{{ $user->name }}</span>
                                                <small class="text-muted font-monospace d-block mt-1" dir="ltr"><i
                                                        class="mdi mdi-email-outline text-info"></i>
                                                    {{ $user->email }}</small>
                                                @if ($user->id == 1)
                                                    <small class="text-danger fw-bold font-11 mt-1 d-block"><i
                                                            class="mdi mdi-shield-crown"></i> المؤسس الأساسي
                                                        (مُحصّن)</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @forelse ($user->roles as $role)
                                            <span class="role-badge shadow-sm"><i
                                                    class="mdi mdi-shield-account-outline"></i> {{ $role->name }}</span>
                                        @empty
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill shadow-sm"><i
                                                    class="mdi mdi-account-cancel-outline me-1"></i> بدون صلاحية</span>
                                        @endforelse
                                    </td>

                                    <td class="text-muted font-13 fw-bold font-monospace" dir="ltr">
                                        {{ $user->created_at->format('Y-m-d') }}
                                    </td>

                                    <td>
                                        @if ($user->status == 'active')
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold border border-success border-opacity-25 shadow-sm">
                                                <i class="mdi mdi-check-circle-outline me-1"></i> نشط
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-bold border border-danger border-opacity-25 shadow-sm">
                                                <i class="mdi mdi-cancel me-1"></i> معلق / موقوف
                                            </span>
                                        @endif
                                    </td>

                                    @canany(['edit_users', 'delete_users'])
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center">

                                                {{-- 🌟 حماية منطق منع تعديل الذات والمؤسس 🌟 --}}
                                                @if ($user->id !== auth()->id() && $user->id != 1)
                                                    @can('edit_users')
                                                        {{-- زر التعديل --}}
                                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                                            class="action-icon edit shadow-sm"
                                                            title="تعديل بيانات وصلاحيات المستخدم">
                                                            <i class="mdi mdi-square-edit-outline font-18"></i>
                                                        </a>

                                                        {{-- زر التعليق/التنشيط --}}
                                                        <form action="{{ route('admin.users.toggleStatus', $user->id) }}"
                                                            method="POST" class="d-inline status-form">
                                                            @csrf
                                                            <button type="button"
                                                                class="action-icon shadow-sm btn-toggle-status {{ $user->status == 'active' ? 'suspend' : 'activate' }}"
                                                                title="{{ $user->status == 'active' ? 'تعليق حساب الموظف ومنعه من الدخول' : 'إعادة تنشيط الحساب' }}"
                                                                data-status="{{ $user->status }}">
                                                                <i
                                                                    class="mdi {{ $user->status == 'active' ? 'mdi-pause-circle-outline' : 'mdi-play-circle-outline' }} font-20"></i>
                                                            </button>
                                                        </form>
                                                    @endcan

                                                    @can('delete_users')
                                                        {{-- زر الحذف --}}
                                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                            class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="action-icon delete btn-delete shadow-sm"
                                                                title="حذف نهائي من قاعدة البيانات">
                                                                <i class="mdi mdi-trash-can-outline font-18"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @else
                                                    {{-- لو الحساب هو حساب المؤسس أو حسابي أنا، الأزرار تظهر مقفولة (Disabled) --}}
                                                    <button class="action-icon disabled shadow-sm" disabled
                                                        title="لا يمكنك تعديل صلاحيات حسابك الخاص من هنا"><i
                                                            class="mdi mdi-square-edit-outline font-18"></i></button>
                                                    <button class="action-icon disabled shadow-sm" disabled
                                                        title="محمي (إجراء غير مصرح به)"><i
                                                            class="mdi mdi-pause-circle-outline font-20"></i></button>
                                                    <button class="action-icon disabled shadow-sm" disabled
                                                        title="لا يمكنك حذف هذا الحساب الأساسي"><i
                                                            class="mdi mdi-trash-can-outline font-18"></i></button>
                                                @endif

                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-muted py-5 text-center bg-white">
                                        <i
                                            class="mdi mdi-account-off-outline fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-bold text-dark">لا يوجد مستخدمين مسجلين مطابقيين للبحث.</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 الحفاظ على الفلاتر في الترقيم 🔥 --}}
            @if (method_exists($users, 'hasPages') && $users->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            // منع الإرسال المزدوج لمحرك البحث
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1 align-middle"></i> جاري...');
            });

            // SweetAlert لتأكيد تغيير حالة الحساب (تعليق / تنشيط)
            $('.btn-toggle-status').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                let currentStatus = $(this).data('status');
                let titleMsg = currentStatus === 'active' ? 'تعليق الحساب؟' : 'تنشيط الحساب؟';
                let textMsg = currentStatus === 'active' ?
                    "هل أنت متأكد من تعليق هذا المستخدم؟ لن يتمكن من تسجيل الدخول للنظام مرة أخرى." :
                    "سيتم إعادة تفعيل حساب المستخدم والسماح له بالدخول للنظام مجدداً.";
                let confirmColor = currentStatus === 'active' ? '#ef4444' : '#10b981';
                let btnText = currentStatus === 'active' ? 'نعم، قم بالتعليق' : 'نعم، قم بالتنشيط';

                Swal.fire({
                    title: titleMsg,
                    text: textMsg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#64748b',
                    confirmButtonText: btnText,
                    cancelButtonText: 'تراجع وإلغاء'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // SweetAlert لتأكيد الحذف النهائي
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'تحذير أمني خطير!',
                    text: "هل أنت متأكد من مسح هذا المستخدم بشكل نهائي من قاعدة البيانات؟ لا يمكن التراجع عن هذه الخطوة وسيتم فقدان سجل الحركات المرتبط به.",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف الحساب نهائياً',
                    cancelButtonText: 'تراجع فوراً'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
