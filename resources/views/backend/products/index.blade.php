@extends('backend.layouts.master')
@section('title', 'المنتجات والرسوم المدرسية')

@push('css')
    {{-- 🔥 مكتبات SweetAlert و Select2 🔥 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

    <style>
        .filter-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .table-custom {
            white-space: nowrap;
            /* منع تكسير النصوص */
        }

        .table-custom th {
            font-weight: 700;
            color: #343a40;
            font-size: 13px;
            border-bottom: 2px solid #e9ecef;
            padding: 15px 10px;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 12px 10px;
            font-size: 13px;
            color: #495057;
            border-bottom: 1px solid #f1f3f5;
        }

        .col-cost {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            font-weight: 700;
        }

        .col-sell {
            background-color: #d4edda !important;
            color: #155724 !important;
            font-weight: 700;
        }

        .branch-badge {
            font-size: 11px;
            background-color: #e2e8f0;
            color: #475569;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        /* تنسيق الأزرار الجديدة */
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            padding: 0;
            transition: 0.3s;
            margin: 0 2px;
            text-decoration: none;
            border: none;
        }

        .action-btn:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }

        .btn-edit {
            background-color: transparent;
            color: #3b82f6;
        }

        .btn-delete {
            background-color: transparent;
            color: #ef4444;
        }

        .btn-transfer {
            background-color: #ff9800;
            color: white !important;
        }

        .btn-duplicate {
            background-color: transparent;
            color: #8b5cf6;
        }

        /* 🌟 تنسيقات Select2 🌟 */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #ced4da;
            border-radius: 6px;
            min-height: 38px;
        }

        /* تنسيق مفتاح التفعيل (Switch) */
        .custom-switch-lg .form-check-input {
            width: 45px;
            height: 22px;
            cursor: pointer;
        }

        .custom-switch-lg .form-check-label {
            cursor: pointer;
            padding-top: 2px;
        }

        /* Dark Mode Adjustments */
        body[data-layout-color="dark"] .filter-card {
            background-color: #323a46;
            border: 1px solid #424e5a;
        }

        body[data-layout-color="dark"] .table-custom th {
            color: #ced4da;
            background-color: #323a46 !important;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .col-cost {
            background-color: rgba(255, 91, 91, 0.15) !important;
            color: #ff5b5b !important;
        }

        body[data-layout-color="dark"] .col-sell {
            background-color: rgba(16, 196, 105, 0.15) !important;
            color: #10c469 !important;
        }

        body[data-layout-color="dark"] .branch-badge {
            background-color: #424e5a;
            color: #cbd5e1;
        }

        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .modal-header {
            border-bottom-color: #424e5a;
            background-color: #3b4652 !important;
        }

        body[data-layout-color="dark"] .modal-title {
            color: #ced4da;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h4 class="page-title font-bold mb-0"><i class="mdi mdi-package-variant-closed text-primary me-1"></i> المنتجات
                والرسوم المدرسية</h4>
            <div class="d-flex gap-2 flex-wrap">

                {{-- 🔥 نموذج الفلتر الشامل 🔥 --}}
                <form action="{{ route('admin.products.index') }}" method="GET"
                    class="d-flex gap-2 align-items-center flex-wrap">
                    <div style="min-width: 180px;">
                        <select name="branch_id" class="form-select select2-filter shadow-sm">
                            <option value="">كل الفروع</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="min-width: 160px;">
                        <select name="type" class="form-select shadow-sm fw-bold">
                            <option value="">كل الأنواع</option>
                            <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>خدمات ورسوم
                            </option>
                            <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>منتجات ملموسة
                            </option>
                        </select>
                    </div>

                    <div class="input-group shadow-sm" style="min-width: 220px;">
                        <input type="text" name="search" class="form-control" placeholder="ابحث بالاسم أو SKU..."
                            value="{{ request('search') }}">
                        <button class="btn btn-info" type="submit"><i class="mdi mdi-magnify"></i></button>
                    </div>
                </form>

                <a href="{{ route('admin.products.index') }}" class="btn btn-light border shadow-sm"
                    title="إعادة ضبط الفلاتر"><i class="mdi mdi-refresh"></i></a>

                {{-- 🔥 حماية زر الإضافة 🔥 --}}
                @can('create_products')
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary shadow-sm fw-bold">
                        <i class="mdi mdi-plus me-1"></i> إضافة بند جديد
                    </a>
                @endcan
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-3">
            <ul class="mb-0 fw-bold">
                @foreach ($errors->all() as $error)
                    <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-custom table-hover table-centered text-center mb-0 table-nowrap">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th class="text-start">الاسم / البند</th>
                        <th>الحالة</th>
                        <th>النوع</th>
                        <th class="col-cost">التكلفة</th>
                        <th class="col-sell">سعر البيع</th>
                        <th>الكمية المتوفرة</th>
                        <th>المباع</th>
                        @canany(['edit_products', 'delete_products', 'create_products', 'transfer_products'])
                            <th>إجراءات</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                        <tr>
                            <td class="text-muted fw-bold">{{ $products->firstItem() + $index }}</td>
                            <td class="fw-bold text-primary text-start">
                                <span class="text-wrap d-inline-block"
                                    style="max-width: 250px;">{{ $product->name_ar }}</span>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="text-muted font-11 font-monospace">{{ $product->sku ?? '---' }}</span>
                                    <span class="branch-badge"><i class="mdi mdi-store-marker-outline"></i>
                                        {{ $product->branch->name_ar ?? 'بدون فرع' }}</span>
                                </div>
                            </td>
                            <td>
                                @if ($product->is_active)
                                    <span class="text-success font-18" title="متاح"><i
                                            class="mdi mdi-check-circle"></i></span>
                                @else
                                    <span class="text-danger font-18" title="موقوف"><i
                                            class="mdi mdi-close-circle"></i></span>
                                @endif
                            </td>
                            <td>
                                @if ($product->type == 'service')
                                    <span class="badge bg-info text-white rounded-pill px-2 py-1 shadow-sm"><i
                                            class="mdi mdi-room-service"></i> رسم / خدمة</span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill px-2 py-1 shadow-sm"><i
                                            class="mdi mdi-package"></i> منتج ملموس</span>
                                @endif
                            </td>
                            <td class="col-cost font-monospace">{{ number_format($product->cost_price, 3) }}</td>
                            <td class="col-sell font-monospace">{{ number_format($product->selling_price, 3) }}</td>
                            <td>
                                @if ($product->type == 'service')
                                    <span class="text-muted">---</span>
                                @else
                                    @if ($product->quantity > $product->alert_quantity)
                                        <span
                                            class="badge bg-success font-14 shadow-sm font-monospace">{{ $product->quantity }}</span>
                                    @else
                                        <span class="badge bg-danger font-14 shadow-sm font-monospace"
                                            title="كمية منخفضة!">{{ $product->quantity }}</span>
                                    @endif
                                @endif
                            </td>
                            <td><span
                                    class="badge bg-secondary font-14 shadow-sm font-monospace">{{ $product->sold_quantity }}</span>
                            </td>

                            @canany(['edit_products', 'delete_products', 'create_products', 'transfer_products'])
                                <td>
                                    <div class="d-flex justify-content-center">
                                        @can('edit_products')
                                            <button type="button" class="action-btn btn-edit btn-open-edit" title="تعديل البند"
                                                data-id="{{ $product->id }}" data-name="{{ $product->name_ar }}"
                                                data-nameen="{{ $product->name_en }}" data-cost="{{ $product->cost_price }}"
                                                data-sell="{{ $product->selling_price }}" data-qty="{{ $product->quantity }}"
                                                data-alert="{{ $product->alert_quantity }}"
                                                data-descar="{{ $product->description_ar }}"
                                                data-descen="{{ $product->description_en }}"
                                                data-branch="{{ $product->branch_id }}" data-active="{{ $product->is_active }}"
                                                data-type="{{ $product->type }}">
                                                <i class="mdi mdi-square-edit-outline font-20"></i>
                                            </button>
                                        @endcan

                                        @can('create_products')
                                            <button type="button" class="action-btn btn-duplicate btn-open-duplicate"
                                                title="نسخ لفروع أخرى" data-id="{{ $product->id }}"
                                                data-name="{{ $product->name_ar }}" data-branch="{{ $product->branch_id }}">
                                                <i class="mdi mdi-content-copy font-20"></i>
                                            </button>
                                        @endcan

                                        @can('transfer_products')
                                            @if ($product->type == 'product')
                                                <button type="button" title="تحويل مخزني لفرع آخر"
                                                    class="action-btn btn-transfer btn-open-transfer shadow-sm"
                                                    data-branch-id="{{ $product->branch_id }}"
                                                    data-branch-name="{{ $product->branch->name_ar ?? '' }}"
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ $product->name_ar }}"
                                                    data-max-qty="{{ $product->quantity }}">
                                                    <i class="mdi mdi-swap-horizontal font-20"></i>
                                                </button>
                                            @endif
                                        @endcan

                                        @can('delete_products')
                                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="button" class="action-btn btn-delete btn-delete-alert"
                                                    title="حذف البند">
                                                    <i class="mdi mdi-trash-can-outline font-20"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted py-5 text-center fw-bold">
                                <i class="mdi mdi-package-variant-closed fs-1 d-block mb-2 opacity-25"></i>لا توجد منتجات
                                مسجلة مطابقة للبحث.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 🔥 الحفاظ على الفلاتر في الـ Pagination 🔥 --}}
        @if ($products->hasPages())
            <div class="card-footer bg-white border-0 pt-3 pb-3 rounded-bottom-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- ========================================================================= --}}
    {{-- 🔥 المودالات المجمعة (خارج الـ Loop لمنع الـ DOM Bloat) 🔥 --}}
    {{-- ========================================================================= --}}

    @can('edit_products')
        {{-- مودال التعديل --}}
        <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom-0 bg-light rounded-top-4">
                        <h5 class="modal-title font-bold text-primary"><i class="mdi mdi-pencil-outline me-1"></i> تعديل
                            بيانات: <span id="edit_modal_title_name" class="text-dark"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editProductForm" class="prevent-double-submit">
                        @csrf @method('PUT')
                        <div class="modal-body p-4 text-start">

                            <div
                                class="d-flex justify-content-between align-items-center bg-light p-2 rounded border mb-4 shadow-sm">
                                <label class="form-label text-muted fw-bold mb-0">حالة البند (التوفر)</label>
                                <div class="form-check form-switch custom-switch-lg mb-0 d-flex align-items-center">
                                    <input class="form-check-input ms-0 me-2 is-active-switch" type="checkbox"
                                        name="is_active" value="1" id="edit_is_active">
                                    <label class="form-check-label fw-bold font-13" for="edit_is_active"
                                        id="edit_is_active_label">متاح</label>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label text-muted fw-bold">الفرع *</label>
                                    <select name="branch_id" id="edit_branch_id" class="form-select select2-modal shadow-sm"
                                        style="width: 100%;" required>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold">الاسم (عربي) *</label>
                                    <input type="text" class="form-control shadow-sm fw-bold" name="name_ar"
                                        id="edit_name_ar" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold">الاسم (إنجليزي)</label>
                                    <input type="text" class="form-control shadow-sm fw-bold" name="name_en"
                                        id="edit_name_en" dir="ltr">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold">سعر التكلفة (شراء)</label>
                                    <input type="number" step="0.001"
                                        class="form-control text-center shadow-sm font-monospace fw-bold" name="cost_price"
                                        id="edit_cost_price" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold">سعر البيع *</label>
                                    <input type="number" step="0.001"
                                        class="form-control text-center border-success shadow-sm font-monospace fw-bold text-success"
                                        name="selling_price" id="edit_selling_price" required>
                                </div>

                                <div id="edit_inventory_section" class="row g-3 m-0 p-0 w-100">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted fw-bold">الكمية المتوفرة *</label>
                                        <input type="number"
                                            class="form-control shadow-sm font-monospace text-center fw-bold" name="quantity"
                                            id="edit_quantity">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted fw-bold">حد التنبيه</label>
                                        <input type="number"
                                            class="form-control shadow-sm font-monospace text-center fw-bold text-danger"
                                            name="alert_quantity" id="edit_alert_quantity">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold">الوصف (عربي)</label>
                                    <textarea class="form-control shadow-sm" name="description_ar" id="edit_desc_ar" rows="2"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold">الوصف (إنجليزي)</label>
                                    <textarea class="form-control shadow-sm" name="description_en" id="edit_desc_en" rows="2" dir="ltr"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-success px-5 rounded-pill font-bold shadow-sm btn-submit"><i
                                    class="mdi mdi-check me-1"></i>حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('create_products')
        {{-- مودال النسخ (Duplicate) --}}
        <div class="modal fade" id="duplicateProductModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom-0 bg-light rounded-top-4">
                        <h5 class="modal-title font-bold text-primary"><i class="mdi mdi-content-copy me-1"></i> استنساخ
                            البند: <span id="dup_modal_title_name" class="text-dark"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="duplicateProductForm" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="alert alert-info font-13 shadow-sm border-0 fw-bold">
                                <i class="mdi mdi-information-outline me-1 fs-5 align-middle"></i> سيتم إنشاء نسخة جديدة من هذا
                                البند في الفروع التي تختارها (بنفس الأسعار والوصف، ولكن برصيد 0 في المخزن لتفادي تضخم الأرصدة).
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label text-muted fw-bold mb-0">اختر الفروع المطلوبة <span
                                            class="text-danger">*</span></label>
                                    <button type="button"
                                        class="btn btn-sm btn-soft-primary rounded-pill py-0 px-3 fw-bold btn-select-all-clone">تحديد
                                        الكل</button>
                                </div>
                                <select name="branch_id[]" id="branch_select_clone"
                                    class="form-select select2-multiple-modal shadow-sm" multiple="multiple"
                                    style="width: 100%;" required>
                                    {{-- تملأ بواسطة الجافاسكريبت لتجنب عرض الفرع الحالي --}}
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-primary px-5 rounded-pill font-bold shadow-sm btn-submit"><i
                                    class="mdi mdi-content-copy me-1"></i> تأكيد النسخ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('transfer_products')
        {{-- مودال التحويل المخزني --}}
        <div class="modal fade" id="transferStockModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 border-top border-warning border-4">
                    <div class="modal-header bg-light rounded-top-4 border-bottom-0">
                        <h5 class="modal-title fw-bold text-warning"><i
                                class="mdi mdi-swap-horizontal fs-4 align-middle me-1"></i> تحويل سريع لفرع آخر</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="form_transfer_stock" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <input type="hidden" name="from_branch_id" id="transfer_from_branch_id">
                            <input type="hidden" name="product_id" id="transfer_product_id">

                            <div
                                class="bg-primary bg-opacity-10 p-3 rounded-3 border border-primary border-opacity-25 mb-4 text-center shadow-sm">
                                <h6 class="text-primary mb-0 fw-bold" id="transfer_product_name"></h6>
                            </div>

                            <div class="row mb-3 g-3">
                                <div class="col-6">
                                    <label class="form-label text-muted fw-bold">من فرع (المُرسِل)</label>
                                    <input type="text" id="transfer_from_branch_name"
                                        class="form-control bg-light text-danger fw-bold border-0 text-center" readonly>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-success">إلى فرع (المُستقبِل) *</label>
                                    <select name="to_branch_id" id="transfer_to_branch_id"
                                        class="form-select select2-modal border-success fw-bold" style="width: 100%;"
                                        required>
                                        <option value="" disabled selected>-- اختر الفرع --</option>
                                        @foreach ($branches as $br)
                                            <option value="{{ $br->id }}">{{ $br->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-2 text-center">
                                <label class="form-label fw-bold">الكمية المراد تحويلها *</label>
                                <input type="number" step="1" name="transfer_quantity" id="transfer_qty_input"
                                    class="form-control border-warning text-center fw-bold fs-4 mx-auto w-50 font-monospace shadow-sm"
                                    required>
                                <small class="text-muted mt-2 d-block" id="transfer_max_helper"></small>
                            </div>
                        </div>
                        <div class="modal-footer border-0 justify-content-center bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" id="btn_save_transfer"
                                class="btn btn-warning text-dark px-5 fw-bold shadow-sm rounded-pill btn-submit"><i
                                    class="mdi mdi-truck-fast-outline me-1"></i> تأكيد النقل</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // 🌟 1. إعدادات Select2 للفلاتر العلوية 🌟
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // 🌟 2. إعدادات Select2 بداخل المودالات لتفادي مشاكل الـ Z-Index 🌟
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.select2-modal').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $(this),
                    dir: isRtl ? 'rtl' : 'ltr'
                });

                $(this).find('.select2-multiple-modal').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $(this),
                    dir: isRtl ? 'rtl' : 'ltr',
                    placeholder: "اختر فرع أو أكثر...",
                    allowClear: true
                });
            });

            // 🌟 3. حماية الفورمات من الضغط المتكرر 🌟
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري التنفيذ...');
            });

            // 🌟 4. ديناميكية التبديل (متاح/غير متاح) في مودال التعديل 🌟
            $('.is-active-switch').change(function() {
                let label = $('#edit_is_active_label');
                if ($(this).is(':checked')) {
                    label.text('✅ متاح').removeClass('text-danger').addClass('text-success');
                } else {
                    label.text('🚫 موقوف').removeClass('text-success').addClass('text-danger');
                }
            });

            // 🌟 5. حقن البيانات في مودال التعديل (Edit) 🌟
            $('.btn-open-edit').click(function() {
                let id = $(this).data('id');
                $('#edit_modal_title_name').text($(this).data('name'));

                $('#edit_branch_id').val($(this).data('branch')).trigger('change');
                $('#edit_name_ar').val($(this).data('name'));
                $('#edit_name_en').val($(this).data('nameen'));
                $('#edit_cost_price').val($(this).data('cost'));
                $('#edit_selling_price').val($(this).data('sell'));
                $('#edit_desc_ar').val($(this).data('descar'));
                $('#edit_desc_en').val($(this).data('descen'));

                let isActive = $(this).data('active') ? true : false;
                $('#edit_is_active').prop('checked', isActive).trigger('change');

                let type = $(this).data('type');
                if (type === 'product') {
                    $('#edit_inventory_section').show();
                    $('#edit_quantity').val($(this).data('qty')).prop('required', true);
                    $('#edit_alert_quantity').val($(this).data('alert'));
                } else {
                    $('#edit_inventory_section').hide();
                    $('#edit_quantity').val(0).prop('required', false);
                    $('#edit_alert_quantity').val(0);
                }

                let url = "{{ route('admin.products.update', ':id') }}".replace(':id', id);
                $('#editProductForm').attr('action', url);
            });

            // 🌟 6. حقن البيانات في مودال الاستنساخ (Duplicate) 🌟
            let allBranches = @json($branches); // جلب كل الفروع للفلترة
            $('.btn-open-duplicate').click(function() {
                let id = $(this).data('id');
                let currentBranchId = $(this).data('branch');

                $('#dup_modal_title_name').text($(this).data('name'));
                let url = "{{ route('admin.products.duplicate', ':id') }}".replace(':id', id);
                $('#duplicateProductForm').attr('action', url);

                // تفريغ الـ Select2 وإعادة تعبئته ما عدا الفرع الحالي للمنتج
                let branchSelect = $('#branch_select_clone');
                branchSelect.empty();
                allBranches.forEach(function(branch) {
                    if (branch.id != currentBranchId) {
                        branchSelect.append(new Option(branch.name_ar, branch.id));
                    }
                });
                branchSelect.trigger('change');
            });

            // زر تحديد الكل في مودال الاستنساخ
            $('.btn-select-all-clone').click(function() {
                let targetSelect = $('#branch_select_clone');
                let allValues = [];
                targetSelect.find('option').each(function() {
                    allValues.push($(this).val());
                });
                targetSelect.val(allValues).trigger('change');
            });

            // 🌟 7. حقن البيانات في مودال التحويل المخزني (Transfer) 🌟
            $('.btn-open-transfer').on('click', function() {
                let branchId = $(this).data('branch-id');
                $('#transfer_from_branch_id').val(branchId);
                $('#transfer_product_id').val($(this).data('product-id'));
                $('#transfer_product_name').text($(this).data('product-name'));
                $('#transfer_from_branch_name').val($(this).data('branch-name'));

                let maxQty = parseFloat($(this).data('max-qty'));
                $('#transfer_qty_input').attr('max', maxQty).val(1);
                $('#transfer_max_helper').html('<span class="text-primary fw-bold">المتاح للتحويل: ' +
                    maxQty + '</span>');

                // تعطيل الفرع المرسل من قائمة المستقبلين
                $('#transfer_to_branch_id option').each(function() {
                    if ($(this).val() == branchId) $(this).prop('disabled', true);
                    else $(this).prop('disabled', false);
                });
                $('#transfer_to_branch_id').val('').trigger('change');
            });

            // إرسال طلب التحويل بالـ AJAX
            $('#form_transfer_stock').on('submit', function(e) {
                e.preventDefault();
                let qty = parseFloat($('#transfer_qty_input').val());
                let maxQty = parseFloat($('#transfer_qty_input').attr('max'));
                let submitBtn = $(this).find('.btn-submit');

                if (qty > maxQty) {
                    Swal.fire({
                        icon: 'error',
                        title: 'تجاوز الرصيد',
                        text: 'الكمية المراد تحويلها أكبر من الرصيد المتاح بالمخزن.',
                        confirmButtonColor: '#1e2b3c'
                    });
                    submitBtn.prop('disabled', false).html(
                        '<i class="mdi mdi-truck-fast-outline me-1"></i> تأكيد النقل'); // Reset Button
                    return false;
                }

                $.ajax({
                    url: "{{ route('admin.warehouse_stocks.transfer') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#transferStockModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التحويل بنجاح',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.html(
                                '<i class="mdi mdi-truck-fast-outline me-1"></i> تأكيد النقل')
                            .prop('disabled', false);
                        let errMsg = xhr.responseJSON && xhr.responseJSON.messages ? xhr
                            .responseJSON.messages.join('<br>') : 'حدث خطأ غير متوقع!';
                        Swal.fire({
                            icon: 'error',
                            title: 'فشل التحويل',
                            html: errMsg,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });

            // 🌟 8. تفعيل الـ SweetAlert لزرار الحذف 🌟
            $('.btn-delete-alert').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد الحذف؟',
                    text: "هل أنت متأكد من حذف هذا البند بشكل نهائي؟",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // رسائل الـ Session (النجاح والخطأ)
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'عملية ناجحة',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'مرفوض!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#1e2b3c'
                });
            @endif
        });
    </script>
@endpush
