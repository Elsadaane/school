@extends('backend.layouts.master')
@section('title', 'قائمة المعلمين والهيئة التدريسية')
@section('title_page', 'إدارة شؤون المعلمين')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* 🌟 Premium Table & Avatar 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            border-top: 5px solid #3b82f6;
        }

        .teacher-avatar {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .avatar-title {
            width: 45px;
            height: 45px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            font-size: 14px;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* 🌟 Filter Bar 🌟 */
        .filter-bar {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }

        /* 🌟 Soft UI Badges 🌟 */
        .bg-soft-primary {
            background-color: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        .bg-soft-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .bg-soft-info {
            background-color: rgba(14, 165, 233, 0.1);
            color: #0284c7;
        }

        .bg-soft-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .bg-soft-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        /* الأزرار الموحدة */
        .action-btn {
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
            text-decoration: none;
            margin: 0 2px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .action-btn.view {
            background: #e0f2fe;
            color: #0284c7;
            border-color: #bae6fd;
        }

        .action-btn.view:hover {
            background: #0284c7;
            color: #fff;
        }

        .action-btn.edit {
            background: #fef3c7;
            color: #d97706;
            border-color: #fde68a;
        }

        .action-btn.edit:hover {
            background: #f59e0b;
            color: #fff;
        }

        .action-btn.delete {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fecaca;
        }

        .action-btn.delete:hover {
            background: #ef4444;
            color: #fff;
        }

        .action-btn.assign {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }

        .action-btn.assign:hover {
            background: #10b981;
            color: #fff;
        }

        .border-dashed {
            border: 2px dashed #cbd5e1 !important;
            min-height: 150px;
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

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .action-btn {
            background: #3b4652;
            border-color: #424e5a;
            color: #adb5bd;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 Page Header 🌟 --}}
        <div class="row mb-3 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold mb-1 text-dark"><i
                            class="mdi mdi-teach text-primary me-1 fs-3 align-middle"></i> إدارة الهيئة التدريسية والمعلمين
                    </h4>
                    <p class="text-muted mb-0 font-14">إدارة ملفات المعلمين، التخصصات، وإسناد الجداول والشعب الأكاديمية.</p>
                </div>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <div class="btn-group shadow-sm rounded-pill border bg-white">
                        <button class="btn btn-light border-0 rounded-pill-start" type="button" data-bs-toggle="tooltip"
                            title="تحديث السجل" onclick="location.reload();">
                            <i class="mdi mdi-refresh font-18 text-secondary"></i>
                        </button>
                        <button class="btn btn-light border-0 rounded-pill-end" type="button" data-bs-toggle="tooltip"
                            title="طباعة القائمة" onclick="window.print();">
                            <i class="mdi mdi-printer font-18 text-info"></i>
                        </button>
                    </div>

                    {{-- 🔥 حماية زر الإضافة بصلاحية الإنشاء 🔥 --}}
                    @can('create_teachers')
                        <a href="{{ route('admin.teachers.create') }}"
                            class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                            <i class="mdi mdi-plus-circle me-1 align-middle fs-5"></i> إضافة معلم جديد
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- 🌟 Smart Filter Bar 🌟 --}}
        <div class="filter-bar border-top border-primary border-4 shadow-sm d-print-none">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label class="form-label font-13 fw-bold text-muted mb-2">البحث السريع المتقدم</label>
                    <div class="input-group shadow-sm rounded-3">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="mdi mdi-magnify text-primary"></i></span>
                        <input type="text" id="quickSearch" class="form-control border-start-0 ps-0 fw-bold"
                            placeholder="ابحث بالاسم، الهوية، أو الهاتف...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-13 fw-bold text-muted mb-2">تصفية التخصص (المواد)</label>
                    <select class="form-select shadow-sm fw-bold" id="filterSpecialization">
                        <option value="">-- كل المواد --</option>
                        @foreach (\App\Models\Subject::where('is_active', 1)->get() as $subject)
                            <option value="{{ $subject->name_ar }}">{{ $subject->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-end mt-4 pt-1">
                    <span
                        class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-4 py-2 font-14 rounded-pill shadow-sm">
                        إجمالي المعلمين: <span
                            class="font-monospace fs-5 fw-bolder ms-1">{{ method_exists($teachers, 'total') ? $teachers->total() : $teachers->count() }}</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- 🌟 Alerts 🌟 --}}
        @if (session('success'))
            <div
                class="alert alert-success alert-dismissible fade show auto-hide-alert shadow-sm rounded-3 fw-bold border-0">
                <i class="mdi mdi-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}</div>
        @endif

        {{-- 🌟 Data Table 🌟 --}}
        <div class="card card-premium shadow-sm border-0 mb-5">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-erp table-hover align-middle text-center mb-0" id="teachersTable">
                        <thead class="sticky-top" style="z-index: 10;">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th class="text-start ps-4">بيانات المعلم الأساسية</th>
                                <th>التخصص (المواد التي يدرسها)</th>
                                <th>التسكين الأكاديمي الحالي</th>
                                <th>تاريخ التعيين</th>

                                {{-- 🔥 إخفاء عمود الإجراءات بالكامل لو مفيش أي صلاحية 🔥 --}}
                                @canany(['view_teachers', 'edit_teachers', 'delete_teachers'])
                                    <th style="width: 160px;">إجراءات الإدارة</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $index => $teacher)
                                <tr class="teacher-row">
                                    <td class="text-muted fw-bold">
                                        {{ method_exists($teachers, 'firstItem') ? $teachers->firstItem() + $index : $index + 1 }}
                                    </td>
                                    <td class="text-start ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 flex-shrink-0">
                                                @if ($teacher->teacher_photo)
                                                    <img src="{{ asset('storage/' . $teacher->teacher_photo) }}"
                                                        class="rounded-circle teacher-avatar">
                                                @else
                                                    <div
                                                        class="avatar-title bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-circle font-18 fw-bold shadow-sm">
                                                        {{ mb_substr($teacher->name_ar, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="m-0 font-14 text-dark fw-bold teacher-name">
                                                    {{ $teacher->name_ar }}</h6>
                                                <div class="mt-1 d-flex flex-wrap gap-2">
                                                    <small class="text-muted font-monospace teacher-id"><i
                                                            class="mdi mdi-identifier text-info"></i>
                                                        {{ $teacher->national_id }}</small>
                                                    <small class="text-muted font-monospace teacher-phone" dir="ltr"><i
                                                            class="mdi mdi-phone text-success"></i>
                                                        {{ $teacher->phone }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-soft-info px-3 py-1 font-13 shadow-sm rounded-pill spec-cell border border-info border-opacity-25 text-wrap lh-base"
                                            style="max-width: 200px;">
                                            @if ($teacher->subjects->count() > 0)
                                                {{ implode(' ، ', $teacher->subjects->pluck('name_ar')->toArray()) }}
                                            @else
                                                <i class="mdi mdi-alert-circle-outline"></i> تخصص غير محدد
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if ($teacher->sections_count > 0)
                                            <span
                                                class="badge bg-success font-13 px-3 py-1 shadow-sm rounded-pill border border-success">
                                                <i class="mdi mdi-google-classroom me-1"></i> مسند لـ
                                                ({{ $teacher->sections_count }}) شعبة
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning text-darken border border-warning border-opacity-50 font-12 px-3 py-1 rounded-pill">
                                                <i class="mdi mdi-alert me-1"></i> غير مسكن
                                            </span>
                                        @endif
                                    </td>
                                    <td dir="ltr" class="font-13 text-muted font-monospace fw-bold">
                                        {{ $teacher->created_at->format('Y-m-d') }}</td>

                                    @canany(['view_teachers', 'edit_teachers', 'delete_teachers'])
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">

                                                {{-- 🔥 حماية زر الإسناد (التعديل الأكاديمي) بصلاحية التعديل 🔥 --}}
                                                @can('edit_teachers')
                                                    <button type="button" class="action-icon assign shadow-sm"
                                                        title="إسناد وتوزيع الشعب الأكاديمية" data-bs-toggle="modal"
                                                        data-bs-target="#assignSectionsModal{{ $teacher->id }}"
                                                        data-teacher-id="{{ $teacher->id }}"
                                                        data-branch-id="{{ $teacher->branch_id }}">
                                                        <i class="mdi mdi-link-variant fs-5"></i>
                                                    </button>
                                                @endcan

                                                {{-- 🔥 حماية زر عرض الملف بصلاحية الرؤية 🔥 --}}
                                                @can('view_teachers')
                                                    <a href="{{ route('admin.teachers.show', $teacher->id) }}"
                                                        class="action-icon view shadow-sm" title="عرض السجل الشامل"><i
                                                            class="mdi mdi-account-details-outline fs-5"></i></a>
                                                @endcan

                                                {{-- 🔥 حماية زر التعديل بصلاحية التعديل 🔥 --}}
                                                @can('edit_teachers')
                                                    <a href="{{ route('admin.teachers.edit', $teacher->id) }}"
                                                        class="action-icon edit shadow-sm" title="تعديل بيانات العقد والمعلم"><i
                                                            class="mdi mdi-pencil-outline fs-5"></i></a>
                                                @endcan

                                                {{-- 🔥 حماية زر الحذف بصلاحية الحذف 🔥 --}}
                                                @can('delete_teachers')
                                                    <form action="{{ route('admin.teachers.destroy', $teacher->id) }}"
                                                        method="POST" class="d-inline delete-form">
                                                        @csrf @method('DELETE')
                                                        <button type="button" class="action-icon delete shadow-sm btn-delete"
                                                            title="إلغاء قيد المعلم"><i
                                                                class="mdi mdi-trash-can-outline fs-5"></i></button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-muted bg-white text-center">
                                        <i
                                            class="mdi mdi-account-search-outline fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-bold text-dark">لا يوجد معلمين مسجلين حالياً.</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 الحفاظ على الفلاتر في الترقيم 🔥 --}}
            @if (method_exists($teachers, 'hasPages') && $teachers->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $teachers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- 🔥 تم نقل جميع الموديلات (Modals) خارج الجدول وحمايتها بالصلاحية 🔥 --}}
    @can('edit_teachers')
        @foreach ($teachers as $teacher)
            <div class="modal fade" id="assignSectionsModal{{ $teacher->id }}" tabindex="-1" aria-hidden="true"
                data-bs-backdrop="static">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 border-top border-success border-4">
                        <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                            <h5 class="modal-title font-bold text-success"><i
                                    class="mdi mdi-account-cog me-1 fs-4 align-middle"></i> التسكين الأكاديمي للمعلم: <span
                                    class="text-dark">{{ $teacher->name_ar }}</span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.teachers.assign_sections_quick', $teacher->id) }}" method="POST"
                            class="prevent-double-submit">
                            @csrf
                            <div class="modal-body p-4 bg-white text-start">
                                <div
                                    class="alert alert-info border-info border-start border-4 shadow-sm mb-4 text-start rounded-3 bg-info bg-opacity-10 fw-bold">
                                    <i class="mdi mdi-information-outline fs-5 align-middle me-1"></i> قم بتحديد الفصول والشعب
                                    الأكاديمية التي سيقوم المعلم بتدريسها واضغط حفظ لاعتماد الجدول.
                                </div>
                                <div class="sections-tree-container border-dashed p-4 rounded-4 text-center bg-light">
                                    <div class="spinner-border text-success" role="status"></div>
                                    <p class="mt-3 mb-0 text-dark fw-bold font-14">جاري تحميل الهيكل الأكاديمي لفرع المعلم...
                                    </p>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top-0 rounded-bottom-4 justify-content-between px-4">
                                <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                    data-bs-dismiss="modal">إلغاء الأمر</button>
                                <button type="submit"
                                    class="btn btn-success px-5 font-bold shadow-sm rounded-pill btn-submit"><i
                                        class="mdi mdi-content-save-all ms-1 fs-5 align-middle"></i> اعتماد التوزيع
                                    الأكاديمي</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endcan

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            setTimeout(function() {
                $('.auto-hide-alert').fadeOut('slow');
            }, 4000);

            // تأمين الفورم ضد الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-2 fs-5 align-middle"></i> جاري الحفظ...');
            });

            // SweetAlert Delete Confirmation
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'إلغاء قيد المعلم؟',
                    text: "هل أنت متأكد من حذف هذا المعلم؟ سيتم إلغاء ارتباطه بجميع الفصول والجداول المدرسية.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، قم بالحذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Smart Search (Client-side)
            $("#quickSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".teacher-row").filter(function() {
                    var text = $(this).find('.teacher-name').text().toLowerCase() + " " +
                        $(this).find('.teacher-id').text().toLowerCase() + " " +
                        $(this).find('.teacher-phone').text().toLowerCase();
                    $(this).toggle(text.indexOf(value) > -1);
                });
            });

            $("#filterSpecialization").on("change", function() {
                var value = $(this).val().toLowerCase();
                $(".teacher-row").filter(function() {
                    $(this).toggle($(this).find('.spec-cell').text().toLowerCase().indexOf(value) >
                        -1 || value === "");
                });
            });

            // AJAX: تحميل الشعب داخل المودال عند فتحه
            $('.modal').on('shown.bs.modal', function(e) {
                let modal = $(this);
                let button = $(e.relatedTarget);
                let teacherId = button.data('teacher-id');
                let branchId = button.data('branch-id');
                let container = modal.find('.sections-tree-container');

                // فقط لو المودال بتاع الإسناد ومفيهوش داتا لسه
                if (branchId && teacherId && container.find('.spinner-border').length > 0) {
                    let localeUrl = "{{ url(app()->getLocale()) }}";
                    let url = `${localeUrl}/admin/get-branch-sections-tree/${branchId}/${teacherId}`;

                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function(htmlData) {
                            container.removeClass('border-dashed text-center bg-light').html(
                                htmlData);
                        },
                        error: function() {
                            container.html(
                                '<div class="alert alert-danger shadow-sm fw-bold border-0"><i class="mdi mdi-alert-circle me-1"></i> خطأ في الاتصال. يرجى إغلاق النافذة والمحاولة مرة أخرى.</div>'
                                );
                        }
                    });
                }
            });
        });
    </script>
@endpush
