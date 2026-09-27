@extends('backend.layouts.master')
@section('title', 'أنواع وتصنيفات الأصول')
@section('title_page', 'إعدادات أنواع الأصول')

@push('css')
    <link href="{{ asset('ltr/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .page-wrapper {
            background-color: #f4f6f9;
            padding: 20px;
        }

        .custom-tabs .nav-link {
            color: #6c757d;
            font-weight: bold;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 25px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .custom-tabs .nav-link:hover {
            color: #5c6bc0;
        }

        .custom-tabs .nav-link.active {
            color: #5c6bc0;
            background-color: transparent;
            border-bottom: 3px solid #5c6bc0;
        }

        .card-custom {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .table-custom thead th {
            background-color: #f8f9fa;
            font-size: 14px;
            text-align: center;
            color: #444;
            border-bottom: 2px solid #e0e0e0;
        }

        .table-custom tbody td {
            vertical-align: middle;
            text-align: center;
            font-size: 14px;
        }

        .table-custom tbody tr:hover td {
            background-color: #f9f9fe;
        }

        .btn-custom {
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-add-main {
            background-color: #5c6bc0;
            color: #fff;
        }

        .btn-add-main:hover {
            background-color: #3f51b5;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(92, 107, 192, 0.4);
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .page-wrapper {
            background-color: #1a1d21;
        }

        body[data-layout-color="dark"] .card-custom {
            background-color: #2d3238;
        }

        body[data-layout-color="dark"] .table-custom thead th {
            background-color: #3b4248;
            color: #e0e0e0;
        }

        body[data-layout-color="dark"] .table-custom tbody td {
            color: #c0c0c0;
        }

        body[data-layout-color="dark"] .table-custom tbody tr:hover td {
            background-color: #3b4248;
        }

        body[data-layout-color="dark"] .custom-tabs .nav-link {
            color: #a0a0a0;
        }

        body[data-layout-color="dark"] .custom-tabs .nav-link.active {
            color: #7986cb;
            border-bottom-color: #7986cb;
        }

        body[data-layout-color="dark"] .bg-white {
            background-color: #2d3238 !important;
        }

        body[data-layout-color="dark"] .modal-content {
            background-color: #2d3238;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4248;
            border-color: #4a5056;
            color: #e0e0e0;
        }

        /* 🖨️ Print Styles */
        @media print {

            .no-print,
            .btn,
            .modal {
                display: none !important;
            }

            .card-custom {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        <div
            class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm flex-wrap gap-2 no-print">
            <div class="d-flex align-items-center gap-2">
                <i class="bx bx-category-alt fs-3 text-primary"></i>
                <h5 class="mb-0 fw-bold text-dark">إدارة أنواع وتصنيفات الأصول</h5>
            </div>

            @can('create_assets')
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-add-main btn-custom px-4" data-bs-toggle="modal"
                        data-bs-target="#addMainCategoryModal">
                        <i class="bx bx-plus"></i> إضافة قسم رئيسي
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-custom px-4" data-bs-toggle="modal"
                        data-bs-target="#addSubCategoryModal">
                        <i class="bx bx-git-repo-forked"></i> إضافة قسم فرعي
                    </button>
                </div>
            @endcan
        </div>

        {{-- عرض رسائل الأخطاء --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card card-custom">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <ul class="nav nav-tabs custom-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="main-tab" data-bs-toggle="tab"
                            data-bs-target="#main-categories" type="button">
                            <i class="bx bx-folder"></i> الأقسام الرئيسية
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sub-tab" data-bs-toggle="tab" data-bs-target="#sub-categories"
                            type="button">
                            <i class="bx bx-file"></i> الأقسام الفرعية
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="myTabContent">

                    {{-- التاب الأول: الأقسام الرئيسية --}}
                    <div class="tab-pane fade show active" id="main-categories" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-custom w-100 datatable-init">
                                <thead>
                                    <tr>
                                        <th width="10%">#</th>
                                        <th>اسم القسم (عربي)</th>
                                        <th>اسم القسم (إنجليزي)</th>
                                        <th>تاريخ الإضافة</th>
                                        @can('delete_assets')
                                            <th width="15%">إجراء</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $index => $cat)
                                        <tr>
                                            <td><span class="badge bg-light text-dark border">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-bold text-primary">{{ $cat->name_ar }}</td>
                                            <td class="fw-bold">{{ $cat->name_en ?? '-' }}</td>
                                            <td>{{ $cat->created_at->format('Y-m-d') }}</td>
                                            @can('delete_assets')
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn shadow-sm"
                                                        data-url="{{ route('admin.assets.category.destroy', $cat->id) }}"
                                                        data-name="{{ $cat->name_ar }}" title="حذف القسم">
                                                        <i class="bx bx-trash"></i> حذف
                                                    </button>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- التاب الثاني: الأقسام الفرعية --}}
                    <div class="tab-pane fade" id="sub-categories" role="tabpanel">
                        <div class="table-responsive w-100">
                            <table class="table table-hover table-custom w-100 datatable-init">
                                <thead>
                                    <tr>
                                        <th width="10%">#</th>
                                        <th>اسم القسم الفرعي (عربي)</th>
                                        <th>القسم الرئيسي التابع له</th>
                                        <th>الاسم الإنجليزي</th>
                                        <th>تاريخ الإضافة</th>
                                        @can('delete_assets')
                                            <th width="15%">إجراء</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subCategories as $index => $sub)
                                        <tr>
                                            <td><span class="badge bg-light text-dark border">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-bold text-info">{{ $sub->name_ar }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 border border-primary">
                                                    <i class="bx bx-folder"></i> {{ $sub->category->name_ar ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="fw-bold">{{ $sub->name_en ?? '-' }}</td>
                                            <td>{{ $sub->created_at->format('Y-m-d') }}</td>
                                            @can('delete_assets')
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn shadow-sm"
                                                        data-url="{{ route('admin.assets.subcategory.destroy', $sub->id) }}"
                                                        data-name="{{ $sub->name_ar }}" title="حذف القسم الفرعي">
                                                        <i class="bx bx-trash"></i> حذف
                                                    </button>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- فورم الحذف المخفي --}}
    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- موديلات الإضافة --}}
    @can('create_assets')
        {{-- مودال إضافة قسم رئيسي --}}
        <div class="modal fade" id="addMainCategoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background: linear-gradient(45deg, #5c6bc0, #3f51b5);">
                        <h5 class="modal-title fw-bold"><i class="bx bx-category"></i> إضافة قسم رئيسي</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.assets.category.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4 row g-3 text-start">
                            <div class="col-md-12">
                                <label class="form-label text-danger fw-bold">اسم القسم عربي *</label>
                                <input type="text" name="name_ar" class="form-control fw-bold" required
                                    placeholder="مثال: سيارات ومعدات">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">اسم القسم إنجليزي</label>
                                <input type="text" name="name_en" class="form-control" placeholder="مثال: Vehicles">
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold"
                                style="background-color: #5c6bc0; border: none;">
                                <i class="bx bx-save"></i> حفظ القسم
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- مودال إضافة قسم فرعي --}}
        <div class="modal fade" id="addSubCategoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" style="background: linear-gradient(45deg, #5c6bc0, #3f51b5);">
                        <h5 class="modal-title fw-bold"><i class="bx bx-git-repo-forked"></i> إضافة قسم فرعي</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.assets.subcategory.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4 row g-3 text-start">
                            <div class="col-md-12">
                                <label class="form-label text-danger fw-bold">القسم الرئيسي *</label>
                                <select name="asset_category_id" class="form-select fw-bold border-primary" required>
                                    <option value="" disabled selected>اختر القسم الرئيسي...</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-danger fw-bold">اسم القسم الفرعي عربي *</label>
                                <input type="text" name="name_ar" class="form-control fw-bold" required
                                    placeholder="مثال: سيارات نقل">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">اسم القسم الفرعي إنجليزي</label>
                                <input type="text" name="name_en" class="form-control" placeholder="مثال: Trucks">
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold"
                                style="background-color: #5c6bc0; border: none;">
                                <i class="bx bx-save"></i> حفظ القسم الفرعي
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="{{ asset('ltr/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('ltr/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // تفعيل DataTable
            $('.datatable-init').DataTable({
                language: {
                    search: "بحث:",
                    lengthMenu: "عرض _MENU_ سجلات",
                    info: "عرض _START_ إلى _END_ من أصل _TOTAL_ سجل",
                    paginate: {
                        first: "الأول",
                        last: "الأخير",
                        next: "التالي",
                        previous: "السابق"
                    }
                }
            });

            // إصلاح أبعاد الجدول عند التبديل بين التابات
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
            });

            // تفاعل الحذف مع SweetAlert
            $(document).on('click', '.delete-btn', function() {
                let deleteUrl = $(this).data('url');
                let name = $(this).data('name');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف القسم "${name}" نهائياً من النظام!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#95a5a6',
                    confirmButtonText: 'نعم، احذف!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-form').attr('action', deleteUrl).submit();
                    }
                });
            });

            // عرض رسائل النجاح
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'نجاح',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#e74c3c'
                });
            @endif
        });
    </script>
@endpush
