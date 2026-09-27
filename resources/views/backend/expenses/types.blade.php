@extends('backend.layouts.master')
@section('title', 'أنواع وأقسام الأصول')
@section('title_page', 'إعدادات تصنيفات الأصول')

@push('css')
    <link href="{{ asset('ltr/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
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
            color: #e74c3c;
        }

        .custom-tabs .nav-link.active {
            color: #e74c3c;
            background-color: transparent;
            border-bottom: 3px solid #e74c3c;
        }

        .card-custom {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        /* Dark Mode */
        body[data-layout-color="dark"] .card-custom {
            background-color: #323a46;
        }

        body[data-layout-color="dark"] .bg-white {
            background-color: #323a46 !important;
        }

        body[data-layout-color="dark"] .text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            color: #fff;
        }

        body[data-layout-color="dark"] .table {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-light {
            background-color: #3b4652;
            color: #ced4da;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-danger bg-opacity-10 p-2 rounded-circle text-danger">
                    <i class="bx bx-category-alt fs-3"></i>
                </div>
                <h5 class="mb-0 fw-bold text-dark">
                    إدارة تصنيفات الأصول الثابتة
                </h5>
            </div>
            <div class="d-flex gap-2">
                @can('create_assets')
                    <button type="button" class="btn btn-danger fw-bold px-4 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#addMainCategoryModal">
                        <i class="bx bx-plus"></i> إضافة قسم رئيسي
                    </button>
                    <button type="button" class="btn btn-outline-danger fw-bold px-4 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#addSubCategoryModal">
                        <i class="bx bx-git-repo-forked"></i> إضافة قسم فرعي
                    </button>
                @endcan
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 rounded-3 fw-bold">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="bx bx-error-circle me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-custom">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <ul class="nav nav-tabs custom-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="main-tab" data-bs-toggle="tab"
                            data-bs-target="#main-categories" type="button" role="tab">
                            <i class="bx bx-folder"></i> الأقسام الرئيسية
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sub-tab" data-bs-toggle="tab" data-bs-target="#sub-categories"
                            type="button" role="tab">
                            <i class="bx bx-file"></i> الأقسام الفرعية
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="myTabContent">

                    {{-- التاب الأول: الأقسام الرئيسية --}}
                    <div class="tab-pane fade show active" id="main-categories" role="tabpanel">
                        <div class="table-responsive w-100">
                            <table
                                class="table table-striped table-bordered table-hover align-middle text-center datatable-init w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">#</th>
                                        <th>اسم القسم الرئيسي</th>
                                        <th>تاريخ الإضافة</th>
                                        @can('create_assets')
                                            <th width="15%">الإجراءات</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $index => $cat)
                                        <tr>
                                            <td><span class="badge bg-light text-dark border">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-bold text-danger">{{ $cat->name_ar }}</td>
                                            <td class="text-muted fw-bold">{{ $cat->created_at->format('Y-m-d') }}</td>
                                            @can('create_assets')
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn shadow-sm"
                                                        data-url="{{ route('admin.assets.category.destroy', $cat->id) }}"
                                                        title="حذف القسم">
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
                            <table
                                class="table table-striped table-bordered table-hover align-middle text-center datatable-init w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">#</th>
                                        <th>اسم القسم الفرعي</th>
                                        <th>القسم الرئيسي التابع له</th>
                                        <th>تاريخ الإضافة</th>
                                        @can('create_assets')
                                            <th width="15%">الإجراءات</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subCategories as $index => $sub)
                                        <tr>
                                            <td><span class="badge bg-light text-dark border">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-bold text-dark">{{ $sub->name_ar }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 border border-danger">
                                                    <i class="bx bx-folder"></i> {{ $sub->category->name_ar ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-muted fw-bold">{{ $sub->created_at->format('Y-m-d') }}</td>
                                            @can('create_assets')
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn shadow-sm"
                                                        data-url="{{ route('admin.assets.subcategory.destroy', $sub->id) }}"
                                                        title="حذف القسم الفرعي">
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

    {{-- 🔥 مودالات الإضافة 🔥 --}}
    @can('create_assets')
        {{-- مودال إضافة قسم رئيسي --}}
        <div class="modal fade" id="addMainCategoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="bx bx-category"></i> إضافة تصنيف أصول رئيسي</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.assets.category.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label text-danger fw-bold">اسم القسم *</label>
                                <input type="text" name="name_ar" class="form-control fw-bold border-danger" required
                                    placeholder="مثال: أجهزة إلكترونية">
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm btn-submit"><i
                                    class="bx bx-save me-1"></i> حفظ القسم</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- مودال إضافة قسم فرعي --}}
        <div class="modal fade" id="addSubCategoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="bx bx-git-repo-forked"></i> إضافة تصنيف أصول فرعي</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.assets.subcategory.store') }}" method="POST"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label text-danger fw-bold">القسم الرئيسي التابع له *</label>
                                <select name="asset_category_id" class="form-select select2-modal fw-bold border-danger"
                                    required style="width: 100%;">
                                    <option value="" disabled selected>-- اختر القسم الرئيسي --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-danger fw-bold">اسم القسم الفرعي *</label>
                                <input type="text" name="name_ar" class="form-control fw-bold border-danger" required
                                    placeholder="مثال: أجهزة لابتوب">
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm btn-submit"><i
                                    class="bx bx-save me-1"></i> حفظ القسم الفرعي</button>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // تفعيل DataTables باللغة العربية
            $('.datatable-init').DataTable({
                language: {
                    search: "بحث:",
                    lengthMenu: "عرض _MENU_ سجلات",
                    info: "عرض _START_ إلى _END_ من أصل _TOTAL_ سجل",
                    infoEmpty: "لا يوجد سجلات للعرض",
                    zeroRecords: "لم يتم العثور على أي بيانات مطابقة",
                    paginate: {
                        first: "الأول",
                        last: "الأخير",
                        next: "التالي",
                        previous: "السابق"
                    }
                }
            });

            // تفعيل Select2 داخل المودال
            $('#addSubCategoryModal').on('shown.bs.modal', function() {
                $(this).find('.select2-modal').select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl',
                    dropdownParent: $('#addSubCategoryModal')
                });
            });

            // إصلاح مشكلة أبعاد الجدول عند التبديل بين التابات
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
            });

            // منع التكرار في الحفظ
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري الحفظ...');
            });

            // سكريبت الحذف الموحد
            $(document).on('click', '.delete-btn', function() {
                let deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: 'سيتم حذف هذا القسم نهائياً من النظام!',
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

            // إشعارات السيستم
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
                    title: 'عفواً',
                    text: "{{ session('error') }}"
                });
            @endif
        });
    </script>
@endpush
