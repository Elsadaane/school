@extends('backend.layouts.master')
@section('title', 'إعدادات أنواع الإيرادات')
@section('title_page', 'إعدادات أنواع الإيرادات')

@push('css')
    {{-- مكتبات التصميم الأساسية (بدون DataTables نهائياً) --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        .page-wrapper {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
            overflow: hidden;
        }

        .btn-modern {
            border-radius: 8px;
            font-weight: 600;
            padding: 9px 18px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #4361ee, #3a53d0);
            color: white;
            border: none;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
        }

        .btn-primary-modern:hover {
            background: linear-gradient(135deg, #3a53d0, #2f45b5);
            color: white;
            transform: translateY(-2px);
        }

        .btn-secondary-modern {
            background-color: #f8f9fa;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        .btn-secondary-modern:hover {
            background-color: #e2e8f0;
            color: #1f2937;
        }

        /* تنسيق التابات (Tabs) */
        .custom-tabs {
            border-bottom: 2px solid #f1f5f9;
            background: #fff;
            padding: 0 20px;
            margin-bottom: 0;
        }

        .custom-tabs .nav-link {
            color: #64748b;
            font-weight: 700;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 15px 25px;
            font-size: 15px;
            transition: all 0.3s;
            margin-bottom: -2px;
        }

        .custom-tabs .nav-link:hover {
            color: #4361ee;
        }

        .custom-tabs .nav-link.active {
            color: #4361ee;
            background-color: transparent;
            border-bottom: 3px solid #4361ee;
        }

        /* الجدول العادي (ERP) */
        .table-erp {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
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

        /* الشارات والأزرار */
        .badge-soft-primary {
            background-color: #eff6ff;
            color: #3b82f6;
            border: 1px solid #bfdbfe;
        }

        .badge-soft-success {
            background-color: #ecfdf5;
            color: #10b981;
            border: 1px solid #a7f3d0;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            transition: all 0.2s;
        }

        .action-icon:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .action-icon.delete:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        /* النوافذ المنبثقة */
        .modal-header-clean {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            border-radius: 12px 12px 0 0;
            padding: 20px 24px;
        }

        .modal-title-clean {
            color: #1e293b;
            font-weight: 800;
            font-size: 18px;
        }

        .modal-body-clean {
            padding: 24px;
        }

        .modal-footer-clean {
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
            border-radius: 0 0 12px 12px;
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-color: #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🔥 الهيدر المنظم والواضح 🔥 --}}
        <div class="card card-premium mb-4">
            <div class="card-body d-flex justify-content-between align-items-center p-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary shadow-sm">
                        <i class="bx bx-category-alt fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">إدارة أنواع وتصنيفات الإيرادات</h4>
                        <p class="text-muted mb-0 font-13">تنظيم الأقسام الرئيسية والفرعية الخاصة بسجل الإيرادات.</p>
                    </div>
                </div>

                @can('manage_revenue_categories')
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-modern btn-primary-modern" data-bs-toggle="modal"
                            data-bs-target="#addMainCategoryModal">
                            <i class="bx bx-plus-circle"></i> إضافة قسم رئيسي
                        </button>
                        <button type="button" class="btn btn-modern btn-secondary-modern" data-bs-toggle="modal"
                            data-bs-target="#addSubCategoryModal">
                            <i class="bx bx-git-repo-forked text-info"></i> إضافة قسم فرعي
                        </button>
                    </div>
                @endcan
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4 fw-bold">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="bx bx-error-circle align-middle me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 🔥 كارت التابات والجدول العادي (بدون DataTables) 🔥 --}}
        <div class="card card-premium">
            <ul class="nav nav-tabs custom-tabs pt-3" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#main-categories"
                        type="button" role="tab">
                        <i class="bx bx-folder me-1"></i> الأقسام الرئيسية
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sub-tab" data-bs-toggle="tab" data-bs-target="#sub-categories"
                        type="button" role="tab">
                        <i class="bx bx-file me-1"></i> الأقسام الفرعية
                    </button>
                </li>
            </ul>

            <div class="card-body p-0">
                <div class="tab-content" id="myTabContent">

                    {{-- التاب الأول: الأقسام الرئيسية --}}
                    <div class="tab-pane fade show active" id="main-categories" role="tabpanel">
                        <div class="table-responsive w-100 p-3 pt-0 mt-3">
                            <table class="table table-striped table-bordered table-hover table-erp w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>اسم القسم (عربي)</th>
                                        <th>اسم القسم (إنجليزي)</th>
                                        <th>تاريخ الإضافة</th>
                                        @can('manage_revenue_categories')
                                            <th style="width: 100px;">الإجراءات</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $index => $cat)
                                        <tr>
                                            <td><span
                                                    class="badge bg-light text-secondary border px-2 py-1">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-bold text-primary">{{ $cat->name_ar }}</td>
                                            <td class="fw-bold text-muted">{{ $cat->name_en ?? '-' }}</td>
                                            <td class="font-monospace text-muted">{{ $cat->created_at->format('Y-m-d') }}
                                            </td>
                                            @can('manage_revenue_categories')
                                                <td>
                                                    <button type="button" class="action-icon delete delete-btn"
                                                        data-url="{{ route('admin.incomes.category.destroy', $cat->id) }}"
                                                        title="حذف القسم">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-muted py-5 text-center fs-5 fw-bold">
                                                <i class="bx bx-folder-open fs-1 d-block mb-2 text-black-50 opacity-50"></i>
                                                لا توجد أقسام رئيسية مسجلة.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- التاب الثاني: الأقسام الفرعية --}}
                    <div class="tab-pane fade" id="sub-categories" role="tabpanel">
                        <div class="table-responsive w-100 p-3 pt-0 mt-3">
                            <table class="table table-striped table-bordered table-hover table-erp w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>اسم القسم الفرعي</th>
                                        <th>القسم الرئيسي التابع له</th>
                                        <th>الاسم (إنجليزي)</th>
                                        <th>تاريخ الإضافة</th>
                                        @can('manage_revenue_categories')
                                            <th style="width: 100px;">الإجراءات</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($subCategories as $index => $sub)
                                        <tr>
                                            <td><span
                                                    class="badge bg-light text-secondary border px-2 py-1">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="fw-bold text-dark">{{ $sub->name_ar }}</td>
                                            <td>
                                                <span class="badge badge-soft-primary px-3 py-2">
                                                    <i class="bx bx-folder me-1"></i>
                                                    {{ app()->getLocale() == 'en' && $sub->category->name_en ? $sub->category->name_en : $sub->category->name_ar ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="fw-bold text-muted">{{ $sub->name_en ?? '-' }}</td>
                                            <td class="font-monospace text-muted">{{ $sub->created_at->format('Y-m-d') }}
                                            </td>
                                            @can('manage_revenue_categories')
                                                <td>
                                                    <button type="button" class="action-icon delete delete-btn"
                                                        data-url="{{ route('admin.incomes.subcategory.destroy', $sub->id) }}"
                                                        title="حذف القسم الفرعي">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-muted py-5 text-center fs-5 fw-bold">
                                                <i
                                                    class="bx bx-folder-open fs-1 d-block mb-2 text-black-50 opacity-50"></i>
                                                لا توجد أقسام فرعية مسجلة.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- فورم الحذف المخفي --}}
    @can('manage_revenue_categories')
        <form id="delete-form" action="" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        {{-- 🔥 مودال إضافة قسم رئيسي 🔥 --}}
        <div class="modal fade" id="addMainCategoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean"><i class="bx bx-category text-primary me-2"></i> إضافة قسم
                            رئيسي</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.incomes.category.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body modal-body-clean row g-3 text-start">
                            <div class="col-md-12">
                                <label class="form-label text-danger">اسم القسم (عربي) *</label>
                                <input type="text" name="name_ar" class="form-control fw-bold border-primary" required
                                    placeholder="مثال: إيجارات العقارات">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">اسم القسم (إنجليزي)</label>
                                <input type="text" name="name_en" class="form-control" placeholder="e.g. Real Estate"
                                    dir="ltr">
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-clean">
                            <button type="button" class="btn btn-secondary-modern px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-primary-modern px-5 fw-bold btn-submit"><i
                                    class="bx bx-save me-1"></i> حفظ القسم</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔥 مودال إضافة قسم فرعي 🔥 --}}
        <div class="modal fade" id="addSubCategoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean"><i class="bx bx-git-repo-forked text-info me-2"></i> إضافة
                            قسم فرعي</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.incomes.subcategory.store') }}" method="POST"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body modal-body-clean row g-3 text-start">
                            <div class="col-md-12">
                                <label class="form-label text-danger">القسم الرئيسي التابع له *</label>
                                <select name="income_category_id" class="form-select select2-init border-primary fw-bold"
                                    required style="width: 100%;">
                                    <option value="" disabled selected>-- اختر القسم الرئيسي --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">
                                            {{ app()->getLocale() == 'en' && $cat->name_en ? $cat->name_en : $cat->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-danger">اسم القسم الفرعي (عربي) *</label>
                                <input type="text" name="name_ar" class="form-control fw-bold border-primary" required
                                    placeholder="مثال: إيجار شقة">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">اسم القسم الفرعي (إنجليزي)</label>
                                <input type="text" name="name_en" class="form-control" placeholder="e.g. Apartment Rent"
                                    dir="ltr">
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-clean">
                            <button type="button" class="btn btn-secondary-modern px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-primary-modern px-5 fw-bold btn-submit"><i
                                    class="bx bx-save me-1"></i> حفظ القسم الفرعي</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- 🔥 مكتبة Select2 و SweetAlert فقط 🔥 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل Select2 بداخل المودال عشان ميضربش ولا يعلق
            $('.modal').on('shown.bs.modal', function(e) {
                $(e.currentTarget).find('.select2-init').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $(e.currentTarget)
                });
            });

            // تأمين الفورمات من الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader-alt bx-spin me-1"></i> جاري الحفظ...');
            });

            // الحذف بـ SweetAlert
            $(document).on('click', '.delete-btn', function() {
                let deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: 'سيتم حذف هذا القسم نهائياً من النظام!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'نعم، احذف!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-form').attr('action', deleteUrl).submit();
                    }
                });
            });

            // إشعارات النظام
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'نجاح',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#10b981',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'عفواً',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            @endif
            @if ($errors->any())
                let errorHtml = '<ul class="text-start text-danger fw-bold mb-0 ps-3">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li>{{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'warning',
                    title: 'خطأ في البيانات',
                    html: errorHtml,
                    confirmButtonColor: '#f59e0b'
                });
            @endif
        });
    </script>
@endpush
