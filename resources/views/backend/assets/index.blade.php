@extends('backend.layouts.master')
@section('title', 'إدارة الأصول والعهد')
@section('title_page', 'إدارة الأصول والعهد')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
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
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
        }

        .btn-primary-modern:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
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

        .table-erp thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
            white-space: nowrap;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
            padding: 14px;
            white-space: nowrap;
            text-align: center;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        .val-total {
            color: #2563eb;
            font-weight: 800;
            font-size: 15px;
            font-family: 'Courier New', Courier, monospace;
            background: #eff6ff;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid #bfdbfe;
        }

        .val-tax,
        .val-basic {
            font-weight: 600;
            font-family: monospace;
        }

        .val-tax {
            color: #ef4444;
        }

        .val-basic {
            color: #475569;
        }

        .badge-soft-primary {
            background-color: #eff6ff;
            color: #3b82f6;
            border: 1px solid #bfdbfe;
        }

        .badge-soft-info {
            background-color: #ecfeff;
            color: #0ea5e9;
            border: 1px solid #bae6fd;
        }

        .badge-soft-dark {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: 1px solid transparent;
            background: #f1f5f9;
            color: #64748b;
            transition: all 0.2s;
        }

        .action-icon:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .action-icon.edit:hover {
            background: #fffbeb;
            color: #d97706;
            border-color: #fde68a;
        }

        .action-icon.delete:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .action-icon.custody:hover {
            background: #ecfeff;
            color: #0891b2;
            border-color: #a5f3fc;
        }

        .action-icon.return:hover {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }

        .modal-header-clean {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            border-radius: 12px 12px 0 0;
            padding: 20px 24px;
        }

        .modal-title-clean {
            color: #0f172a;
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

        .section-title {
            font-size: 14px;
            font-weight: 800;
            color: #3b82f6;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px dashed #e2e8f0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Dark Mode Adjustments */
        body[data-layout-color="dark"] .card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .card-header {
            background-color: #323a46 !important;
            border-color: #424e5a !important;
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

        body[data-layout-color="dark"] .fw-bold.text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .text-muted {
            color: #adb5bd !important;
        }

        body[data-layout-color="dark"] .bg-white {
            background-color: #323a46 !important;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }

        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="card card-premium mb-4">
            <div class="card-body d-flex justify-content-between align-items-center p-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary shadow-sm">
                        <i class="bx bx-buildings fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">إدارة الأصول الثابتة والعهد</h4>
                        <p class="text-muted mb-0 font-13">تسجيل الأصول، إدارة العهد للموظفين، ومتابعة الإهلاكات.</p>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @can('create_assets')
                        <button type="button" class="btn btn-modern btn-secondary-modern" data-bs-toggle="modal"
                            data-bs-target="#addMainCategoryModal">
                            <i class="bx bx-category text-primary"></i> قسم رئيسي
                        </button>
                        <button type="button" class="btn btn-modern btn-secondary-modern" data-bs-toggle="modal"
                            data-bs-target="#addSubCategoryModal">
                            <i class="bx bx-git-repo-forked text-info"></i> قسم فرعي
                        </button>
                        <button type="button" class="btn btn-modern btn-primary-modern ms-2" data-bs-toggle="modal"
                            data-bs-target="#addAssetModal">
                            <i class="bx bx-plus-circle"></i> تسجيل أصل جديد
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <h5 class="fw-bold mb-1"><i class="bx bx-error-circle align-middle"></i> لم يتم الحفظ بسبب الأخطاء التالية:
                </h5>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-premium">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom-0">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-list-ul me-1 align-middle"></i> سجل الأصول الثابتة</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3 pt-0">
                    <table class="table table-striped table-bordered table-hover table-erp align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>تاريخ الشراء</th>
                                <th>الفرع</th>
                                <th>طريقة الدفع</th>
                                <th class="text-end">الإجمالي</th>
                                <th style="min-width: 180px;" class="text-start">بيان وصف الأصل</th>
                                <th>القسم الفرعي</th>
                                <th>القسم الرئيسي</th>
                                <th>المورد</th>
                                <th>رقم الفاتورة</th>
                                <th class="text-end">الأساسي</th>
                                <th class="text-end">الضريبة</th>
                                <th>كود الأصل</th>
                                <th>حالة العهدة</th>
                                <th>المستخدم</th>
                                @canany(['edit_assets', 'manage_asset_custodies'])
                                    <th style="min-width: 150px;">الإجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assets as $index => $asset)
                                @php
                                    $locale = app()->getLocale();
                                    $catName =
                                        $locale == 'en' && $asset->category->name_en
                                            ? $asset->category->name_en
                                            : $asset->category->name_ar ?? '-';
                                    $subCatName =
                                        $locale == 'en' && $asset->subCategory && $asset->subCategory->name_en
                                            ? $asset->subCategory->name_en
                                            : $asset->subCategory->name_ar ?? '-';
                                    $branchName =
                                        $locale == 'en' && $asset->branch && $asset->branch->name_en
                                            ? $asset->branch->name_en
                                            : $asset->branch->name_ar ?? 'الفرع الرئيسي';
                                    $supplierName =
                                        $locale == 'en' && $asset->supplier && $asset->supplier->name_en
                                            ? $asset->supplier->name_en
                                            : $asset->supplier->name_ar ?? ($asset->supplier->name ?? '-');
                                @endphp
                                <tr>
                                    <td><span
                                            class="badge bg-light text-secondary border px-2 py-1">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="fw-bold text-dark"><i
                                            class="bx bx-calendar text-muted me-1"></i>{{ \Carbon\Carbon::parse($asset->transaction_date)->format('Y-m-d') }}
                                    </td>
                                    <td><span class="badge badge-soft-dark px-2 py-1"><i class='bx bx-store-alt'></i>
                                            {{ $branchName }}</span></td>
                                    <td>
                                        @if ($asset->payment_type == 'cash')
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1"><i
                                                    class="bx bx-money me-1"></i>كاش</span>
                                        @elseif($asset->payment_type == 'installment')
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1"><i
                                                    class="bx bx-calendar me-1"></i>أقساط</span>
                                        @else
                                            <span
                                                class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1"><i
                                                    class="bx bx-archive me-1"></i>افتتاحي</span>
                                        @endif
                                    </td>
                                    <td class="text-end val-total">{{ number_format($asset->total_amount, 3) }}</td>
                                    <td class="text-start fw-bold text-dark">{{ $asset->transaction_details }}</td>
                                    <td><span class="badge badge-soft-info px-2 py-1">{{ $subCatName }}</span></td>
                                    <td><span class="badge badge-soft-primary px-2 py-1">{{ $catName }}</span></td>
                                    <td class="fw-bold text-secondary">{{ $supplierName }}</td>
                                    <td class="text-muted font-monospace">{{ $asset->supplier_invoice_no ?? '-' }}</td>
                                    <td class="text-end val-basic">{{ number_format($asset->amount, 3) }}</td>
                                    <td class="text-end val-tax">{{ number_format($asset->tax_amount, 3) }}</td>
                                    <td class="fw-bold text-primary font-monospace">{{ $asset->asset_code }}</td>
                                    <td>
                                        @if ($asset->current_employee_id && $asset->employee)
                                            <a href="{{ route('admin.hr.employees.show', $asset->current_employee_id) }}"
                                                class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1 text-decoration-none fw-bold"
                                                title="الملف الشخصي">
                                                <i class="bx bx-user-pin me-1"></i>
                                                {{ $asset->employee->full_name_ar ?? 'الموظف' }}
                                            </a>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1"><i
                                                    class="bx bx-building-house me-1"></i>بالمدرسة</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted"><i
                                                class="bx bx-user-circle me-1"></i>{{ $asset->user->name ?? 'Admin' }}</small>
                                    </td>
                                    @canany(['edit_assets', 'manage_asset_custodies'])
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                @if ($asset->attachment)
                                                    <a href="{{ asset('storage/' . $asset->attachment) }}" target="_blank"
                                                        class="action-icon" title="عرض المرفق"><i
                                                            class="bx bx-link-external"></i></a>
                                                @endif
                                                @can('edit_assets')
                                                    <button type="button" class="action-icon edit edit-btn"
                                                        data-id="{{ $asset->id }}" data-branch="{{ $asset->branch_id }}"
                                                        data-cat="{{ $asset->asset_category_id }}"
                                                        data-subcat="{{ $asset->asset_sub_category_id }}"
                                                        data-supplier="{{ $asset->supplier_id }}"
                                                        data-inv="{{ $asset->supplier_invoice_no }}"
                                                        data-details="{{ $asset->transaction_details }}"
                                                        data-dep="{{ $asset->depreciation_rate }}"
                                                        data-date="{{ \Carbon\Carbon::parse($asset->transaction_date)->format('Y-m-d') }}"
                                                        data-check="{{ $asset->check_number }}" data-notes="{{ $asset->notes }}"
                                                        title="تعديل الأصل" data-bs-toggle="modal"
                                                        data-bs-target="#editAssetModal">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                @endcan
                                                @can('manage_asset_custodies')
                                                    @if (!$asset->current_employee_id)
                                                        <button type="button" class="action-icon custody assign-custody-btn"
                                                            data-id="{{ $asset->id }}"
                                                            data-details="{{ $asset->transaction_details }}"
                                                            data-bs-toggle="modal" data-bs-target="#assignCustodyModal"
                                                            title="تسليم الأصل كعهدة"><i class="bx bx-user-plus"></i></button>
                                                    @else
                                                        <a href="{{ route('admin.assets.custody.print', $asset->id) }}"
                                                            target="_blank" class="action-icon print"
                                                            title="طباعة محضر التسليم"><i class="bx bx-printer"></i></a>
                                                        <button type="button" class="action-icon return return-custody-btn"
                                                            data-id="{{ $asset->id }}"
                                                            data-employee="{{ $asset->employee->full_name_ar ?? 'الموظف' }}"
                                                            data-details="{{ $asset->transaction_details }}"
                                                            data-price="{{ $asset->total_amount }}" data-bs-toggle="modal"
                                                            data-bs-target="#returnCustodyModal"
                                                            title="استلام الأصل (إخلاء طرف)"><i
                                                                class="bx bx-check-shield"></i></button>
                                                    @endif
                                                @endcan
                                                @can('edit_assets')
                                                    <button type="button" class="action-icon delete delete-btn"
                                                        data-id="{{ $asset->id }}" data-paid="{{ $asset->paid_amount }}"
                                                        data-remaining="{{ $asset->remaining_amount }}"
                                                        data-details="{{ $asset->transaction_details }}" data-bs-toggle="modal"
                                                        data-bs-target="#deleteAssetModal" title="حذف الأصل"><i
                                                            class="bx bx-trash"></i></button>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="16" class="text-muted py-5 text-center fs-5">
                                        <i class="bx bx-folder-open fs-1 d-block mb-2 text-black-50"></i> لا توجد أصول
                                        مسجلة حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- النوافذ المنبثقة (Modals) --}}
    {{-- ================================================= --}}

    @can('create_assets')
        {{-- 1. مودال تسجيل أصل جديد --}}
        <div class="modal fade" id="addAssetModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean"><i class="bx bx-plus-circle text-primary me-2"></i> تسجيل
                            أصل جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.assets.store') }}" method="POST" enctype="multipart/form-data"
                        id="assetForm" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body modal-body-clean">
                            <div class="section-title"><i class="bx bx-category"></i> التصنيف والمكان</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label text-danger">الفرع *</label>
                                    <select name="branch_id" class="form-select select2-init" required style="width: 100%;">
                                        <option value="" disabled selected>-- اختر الفرع --</option>
                                        @foreach ($branches ?? [] as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-danger">القسم الرئيسي *</label>
                                    <select name="asset_category_id" id="main_cat_select" class="form-select select2-init"
                                        required style="width: 100%;">
                                        <option value="" disabled selected>-- اختر القسم --</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">القسم الفرعي</label>
                                    <select name="asset_sub_category_id" id="sub_cat_select" class="form-select select2-init"
                                        style="width: 100%;">
                                        <option value="" selected>اختر القسم الرئيسي أولاً</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">المورد</label>
                                    <select name="supplier_id" class="form-select select2-init" style="width: 100%;">
                                        <option value="" selected>بدون مورد محدد</option>
                                        @foreach ($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-money"></i> القيمة المالية</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label text-danger">قيمة الأصل (الأساسي) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bx bx-money text-muted"></i></span>
                                        <input type="number" step="0.001" name="amount" id="asset_amount"
                                            class="form-control text-center fw-bold font-monospace" value="0.000" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">ضريبة القيمة المضافة (%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">%</span>
                                        <input type="number" step="0.01" name="tax_percent" id="asset_tax"
                                            class="form-control text-center fw-bold" value="5">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-success">القيمة الإجمالية</label>
                                    <input type="text" id="asset_total"
                                        class="form-control text-center bg-success bg-opacity-10 text-success fw-bold fs-5 border-success font-monospace"
                                        value="0.000" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">رقم فاتورة الشراء</label>
                                    <input type="text" name="supplier_invoice_no" class="form-control text-center">
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-wallet"></i> السداد وحساب الدفع</div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-danger fs-6">آلية الدفع</label>
                                <div class="d-flex flex-wrap gap-4 p-3 rounded"
                                    style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div class="form-check">
                                        <input class="form-check-input payment-type-radio" type="radio" name="payment_type"
                                            id="pay_cash" value="cash" checked>
                                        <label class="form-check-label fw-bold text-success" for="pay_cash">نقداً / تحويل
                                            بنكي</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input payment-type-radio" type="radio" name="payment_type"
                                            id="pay_install" value="installment">
                                        <label class="form-check-label fw-bold text-warning" for="pay_install">تقسيط</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input payment-type-radio" type="radio" name="payment_type"
                                            id="pay_opening" value="opening">
                                        <label class="form-check-label fw-bold text-primary" for="pay_opening">رصيد
                                            افتتاحي</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-danger">تاريخ الشراء *</label>
                                    <input type="date" name="transaction_date"
                                        class="form-control text-center fw-bold text-dark" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                                {{-- الخزنة بتختفي/تظهر وتكون إجبارية أوتوماتيك بالـ JS --}}
                                <div class="col-md-4 financial-account-section">
                                    <label class="form-label text-danger">حساب الدفع / الخزينة *</label>
                                    <select name="financial_account_id" id="financial_account_id"
                                        class="form-select select2-init border-primary fw-bold text-primary" required
                                        style="width: 100%;">
                                        <option value="" disabled selected>-- اختر الحساب --</option>
                                        @foreach ($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 financial-account-section">
                                    <label class="form-label">رقم الشيك / الحوالة</label>
                                    <input type="text" name="check_number" class="form-control text-center">
                                </div>
                            </div>

                            <div class="col-md-12 installment-section p-4 rounded mt-3 mb-4"
                                style="display: none; background-color: #fffbeb; border: 1px solid #fde68a;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold m-0 text-warning"><i class="bx bx-calendar-event"></i> تفاصيل الجدولة
                                        (سيتم خصم الدفعة المقدمة من الخزنة إن وُجدت)</h6>
                                    <div class="w-25">
                                        <select name="schedule_type" id="schedule_type"
                                            class="form-select form-select-sm border-warning fw-bold text-dark">
                                            <option value="auto" selected>جدولة تلقائية</option>
                                            <option value="manual">جدولة يدوية</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3" id="auto_schedule_section">
                                    <div class="col-md-4">
                                        <label class="form-label">الدفعة المقدمة (إن وجدت)</label>
                                        <input type="number" step="0.001" name="down_payment_auto" id="down_payment_auto"
                                            class="form-control text-center fw-bold down-payment-input" value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-danger">عدد الأشهر (الأقساط) *</label>
                                        <input type="number" name="months" id="months"
                                            class="form-control text-center fw-bold">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-danger">تاريخ استحقاق أول قسط *</label>
                                        <input type="date" name="installment_start_date" id="installment_start_date"
                                            class="form-control text-center fw-bold">
                                    </div>
                                </div>
                                <div class="row g-3" id="manual_schedule_section" style="display: none;">
                                    <div class="col-md-4">
                                        <label class="form-label">الدفعة المقدمة (إن وجدت)</label>
                                        <input type="number" step="0.001" name="down_payment_manual"
                                            id="down_payment_manual"
                                            class="form-control text-center fw-bold down-payment-input" value="0">
                                    </div>
                                    <div class="col-md-8 d-flex align-items-end">
                                        <div
                                            class="alert alert-warning py-2 mb-0 w-100 fw-bold border-warning d-flex justify-content-between align-items-center bg-white shadow-sm">
                                            <span><i class="bx bx-info-circle"></i> المتبقي للجدولة</span>
                                            <div><span id="manual_remaining" class="fs-4 text-danger">0.000</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <table class="table table-sm table-bordered bg-white text-center mb-0"
                                            id="manual_table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>قيمة القسط</th>
                                                    <th>تاريخ الاستحقاق</th>
                                                    <th><button type="button"
                                                            class="btn btn-sm btn-success rounded-circle shadow-sm"
                                                            id="add_manual_row"><i class="bx bx-plus"></i></button></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="number" step="0.001" name="manual_amount[]"
                                                            class="form-control text-center manual-calc fw-bold"></td>
                                                    <td><input type="date" name="manual_date[]"
                                                            class="form-control text-center fw-bold"></td>
                                                    <td><button type="button"
                                                            class="btn btn-sm btn-danger rounded-circle remove_row shadow-sm"><i
                                                                class="bx bx-trash"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-detail"></i> مواصفات الأصل</div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label text-danger">البيان (وصف الأصل) *</label>
                                    <input type="text" name="transaction_details" class="form-control"
                                        placeholder="مثال: لابتوب Dell..." required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">نسبة الإهلاك السنوي (%)</label>
                                    <input type="number" step="0.1" name="depreciation_rate"
                                        class="form-control text-center" placeholder="مثال: 10">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">المرفقات</label>
                                    <input type="file" name="attachment" class="form-control" accept="image/*,.pdf">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">ملاحظات إضافية</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-clean pt-3 pb-3">
                            <button type="button" class="btn btn-secondary-modern px-4 fw-bold"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary-modern px-5 fw-bold shadow-sm btn-submit"><i
                                    class="bx bx-save me-1"></i> حفظ وتوليد كود الأصل</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- باقي الموديلات (إضافة قسم رئيسي، فرعي، تعديل، حذف، العهد) متواجدة في الكود ولم يتم تغييرها لأنها تعمل 100% --}}

        {{-- 2. مودال إضافة قسم رئيسي --}}
        <div class="modal fade" id="addMainCategoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean"><i class="bx bx-category text-primary me-2"></i> إضافة قسم
                            رئيسي</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.assets.category.store') }}" method="POST">
                        @csrf
                        <div class="modal-body modal-body-clean row g-3">
                            <div class="col-md-12"><label class="form-label text-danger">اسم القسم (عربي) *</label><input
                                    type="text" name="name_ar" class="form-control" required></div>
                            <div class="col-md-12"><label class="form-label">اسم القسم (إنجليزي)</label><input type="text"
                                    name="name_en" class="form-control"></div>
                        </div>
                        <div class="modal-footer modal-footer-clean"><button type="submit"
                                class="btn btn-primary-modern px-4 w-100 fw-bold">حفظ القسم</button></div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. مودال إضافة قسم فرعي --}}
        <div class="modal fade" id="addSubCategoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean"><i class="bx bx-git-repo-forked text-info me-2"></i> إضافة
                            قسم فرعي</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.assets.subcategory.store') }}" method="POST">
                        @csrf
                        <div class="modal-body modal-body-clean row g-3">
                            <div class="col-md-12">
                                <label class="form-label text-danger">القسم الرئيسي *</label>
                                <select name="asset_category_id" class="form-select select2-init" required
                                    style="width: 100%;">
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12"><label class="form-label text-danger">اسم القسم الفرعي (عربي)
                                    *</label><input type="text" name="name_ar" class="form-control" required></div>
                        </div>
                        <div class="modal-footer modal-footer-clean"><button type="submit"
                                class="btn btn-primary-modern px-4 w-100 fw-bold">حفظ القسم الفرعي</button></div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('edit_assets')
        {{-- 4. مودال تعديل بيانات الأصل --}}
        <div class="modal fade" id="editAssetModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean text-warning"><i class="bx bx-edit me-2"></i> تعديل بيانات
                            الأصل</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data" id="editAssetForm">
                        @csrf @method('PUT')
                        <div class="modal-body modal-body-clean">
                            <div
                                class="alert alert-warning fw-bold text-center border-warning bg-warning bg-opacity-10 shadow-sm rounded-3">
                                <i class='bx bx-info-circle fs-5 align-middle me-1'></i> لتعديل المبالغ المالية يجب حذف الأصل
                                وإعادة تسجيله.
                            </div>
                            <div class="section-title"><i class="bx bx-category"></i> التصنيف والمكان</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label text-danger">الفرع *</label>
                                    <select name="branch_id" id="edit_branch_id" class="form-select select2-edit" required
                                        style="width: 100%;">
                                        @foreach ($branches ?? [] as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-danger">القسم الرئيسي *</label>
                                    <select name="asset_category_id" id="edit_main_cat_select"
                                        class="form-select select2-edit" required style="width: 100%;">
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">القسم الفرعي</label>
                                    <select name="asset_sub_category_id" id="edit_sub_cat_select"
                                        class="form-select select2-edit" style="width: 100%;">
                                        <option value="" selected>اختر القسم</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">المورد</label>
                                    <select name="supplier_id" id="edit_supplier_id" class="form-select select2-edit"
                                        style="width: 100%;">
                                        <option value="" selected>بدون مورد</option>
                                        @foreach ($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="section-title"><i class="bx bx-detail"></i> مواصفات الأصل</div>
                            <div class="row g-3">
                                <div class="col-md-8"><label class="form-label text-danger">البيان *</label><input
                                        type="text" name="transaction_details" id="edit_transaction_details"
                                        class="form-control" required></div>
                                <div class="col-md-4"><label class="form-label">نسبة الإهلاك (%)</label><input type="number"
                                        step="0.1" name="depreciation_rate" id="edit_depreciation_rate"
                                        class="form-control text-center"></div>
                                <div class="col-md-4"><label class="form-label">رقم الفاتورة</label><input type="text"
                                        name="supplier_invoice_no" id="edit_supplier_invoice_no"
                                        class="form-control text-center"></div>
                                <div class="col-md-4"><label class="form-label">رقم الشيك</label><input type="text"
                                        name="check_number" id="edit_check_number" class="form-control text-center"></div>
                                <div class="col-md-4"><label class="form-label">المرفقات</label><input type="file"
                                        name="attachment" class="form-control" accept="image/*,.pdf"></div>
                                <div class="col-md-12"><label class="form-label">ملاحظات إضافية</label>
                                    <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-clean pt-3 pb-3">
                            <button type="button" class="btn btn-secondary-modern px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-success fw-bold px-5 shadow-sm btn-submit"><i
                                    class="bx bx-save me-1"></i> حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 5. مودال تسوية وحذف أصل مالي --}}
        <div class="modal fade" id="deleteAssetModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white rounded-top">
                        <h5 class="modal-title fw-bold"><i class="bx bx-error-circle me-2"></i> حذف أصل وتسوية حساباته</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="deleteAssetForm" class="prevent-double-submit">
                        @csrf @method('DELETE')
                        <div class="modal-body p-4 bg-white">
                            <div class="alert alert-danger fw-bold border-danger text-center shadow-sm">
                                أنت على وشك حذف هذا الأصل: <br><span id="del_asset_details"
                                    class="fs-5 text-dark mt-2 d-block"></span>
                            </div>
                            <div class="mb-3 text-start">
                                <label class="form-label fw-bold text-danger">سبب الإلغاء / الاسترجاع *</label>
                                <textarea name="return_reason" class="form-control border-danger fw-bold" rows="2"
                                    placeholder="اكتب سبب حذف الأصل من النظام..." required></textarea>
                            </div>
                            <div class="row g-3 mb-4 text-center">
                                <div class="col-6">
                                    <div class="p-3 bg-success bg-opacity-10 border border-success rounded-3">
                                        <small class="text-success d-block fw-bold mb-1">المبلغ المدفوع (مسبقاً)</small>
                                        <span class="fw-bold fs-4 text-success font-monospace" id="del_asset_paid">0</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded-3">
                                        <small class="text-warning d-block fw-bold mb-1">الأقساط المتبقية</small>
                                        <span class="fw-bold fs-4 text-warning font-monospace"
                                            id="del_asset_remaining">0</span>
                                    </div>
                                </div>
                            </div>
                            <div id="refund_section" style="display: none;">
                                <label class="form-label text-primary fw-bold fs-6">طريقة التعامل مع المبلغ المدفوع؟</label>
                                <select name="refund_action" id="refund_action"
                                    class="form-select border-primary fw-bold mb-3 shadow-sm">
                                    <option value="suspend" selected>إسقاط المبلغ (عدم استرجاع)</option>
                                    <option value="refund">استرجاع المبلغ وإيداعه في الخزنة</option>
                                </select>
                                <div id="refund_account_div" style="display: none;">
                                    <label class="form-label text-success fw-bold">اختر الخزنة لإيداع المبلغ المسترجع</label>
                                    <select name="refund_account_id" id="refund_account_id"
                                        class="form-select border-success mb-3 shadow-sm">
                                        @foreach ($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm btn-submit"><i
                                    class="bx bx-trash me-1"></i> تأكيد الحذف</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('manage_asset_custodies')
        {{-- 6. مودال محضر تسليم العهدة --}}
        <div class="modal fade" id="assignCustodyModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header text-white rounded-top"
                        style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                        <h5 class="modal-title fw-bold"><i class="bx bx-user-pin me-2"></i> تسليم الأصل كعهدة لموظف</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="assignCustodyForm">
                        @csrf
                        <div class="modal-body p-4 bg-white">
                            <div class="alert alert-info fw-bold text-center border-info shadow-sm bg-info bg-opacity-10">
                                سيتم تسليم الأصل التالي: <br><span id="custody_asset_details"
                                    class="fs-5 text-dark mt-2 d-block"></span>
                            </div>
                            <div class="row mb-3 g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-primary">حالة الأصل عند التسليم *</label>
                                    <select name="asset_condition" class="form-select border-primary shadow-sm" required>
                                        <option value="جديد (New)">جديد بالكرتونة</option>
                                        <option value="مستعمل - بحالة ممتازة">مستعمل - بحالة ممتازة</option>
                                        <option value="مستعمل - به خدوش بسيطة">مستعمل - به خدوش/ملاحظات بسيطة</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-danger fw-bold">الموظف المستلم *</label>
                                    <select name="employee_id" class="form-select select2-init border-danger" required
                                        style="width: 100%;">
                                        <option value="" disabled selected>-- ابحث باسم الموظف --</option>
                                        @foreach (\App\Models\HrEmployee::where('status', 'active')->get() as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->full_name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">تاريخ التسليم *</label>
                                    <input type="date" name="assigned_date"
                                        class="form-control text-center fw-bold shadow-sm" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">ملاحظات العهدة وملحقاتها</label>
                                <textarea name="notes" class="form-control shadow-sm" rows="2" placeholder="أي ملحقات إضافية..."></textarea>
                            </div>
                            <div class="section-title"><i class="bx bx-pen text-primary"></i> التوقيع الإلكتروني</div>
                            <div class="row mb-3">
                                <div class="col-md-6 text-center">
                                    <label class="form-label text-primary fw-bold mb-2">توقيع الموظف *</label>
                                    <div
                                        class="border border-2 border-primary rounded-3 d-inline-block w-100 bg-white shadow-sm overflow-hidden">
                                        <canvas id="signature-pad-employee" class="w-100" height="150"
                                            style="cursor: crosshair;"></canvas>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-link text-danger mt-1 fw-bold"
                                        id="clear-signature-emp"><i class="bx bx-eraser"></i> مسح</button>
                                    <input type="hidden" name="signature_base64" id="signature_base64">
                                </div>
                                <div class="col-md-6 text-center">
                                    <label class="form-label text-dark fw-bold mb-2">توقيع المسؤول *</label>
                                    <div
                                        class="border border-2 border-dark rounded-3 d-inline-block w-100 bg-white shadow-sm overflow-hidden">
                                        <canvas id="signature-pad-admin" class="w-100" height="150"
                                            style="cursor: crosshair;"></canvas>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-link text-danger mt-1 fw-bold"
                                        id="clear-signature-admin"><i class="bx bx-eraser"></i> مسح</button>
                                    <input type="hidden" name="admin_signature_base64" id="admin_signature_base64">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0 justify-content-between">
                            <button type="button" class="btn btn-dark px-4 fw-bold shadow-sm" onclick="window.print()"><i
                                    class="bx bx-printer me-1"></i> طباعة</button>
                            <div>
                                <button type="button" class="btn btn-secondary px-4 fw-bold"
                                    data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-info text-white fw-bold px-5 shadow-sm btn-submit"><i
                                        class="bx bx-check-shield me-1"></i> حفظ وتسليم كعهدة</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 7. مودال استرداد وإخلاء طرف عهدة --}}
        <div class="modal fade" id="returnCustodyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white rounded-top">
                        <h5 class="modal-title fw-bold"><i class="bx bx-check-shield me-2"></i> استلام الأصل وإخلاء طرف الموظف
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="returnCustodyForm" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 bg-white">
                            <div
                                class="alert alert-success fw-bold text-center border-success shadow-sm bg-success bg-opacity-10">
                                سيتم استرجاع الأصل: <br><span id="return_asset_details"
                                    class="fs-5 text-dark mt-1 d-block"></span>
                                <hr class="my-2 opacity-25">
                                من الموظف: <span id="return_employee_name" class="text-primary fs-5"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-danger fw-bold">حالة الأصل عند الاسترجاع *</label>
                                <select name="return_condition" id="return_condition"
                                    class="form-select border-danger shadow-sm" required>
                                    <option value="" disabled selected>-- حدد الحالة الآن --</option>
                                    <option value="سليم">سليم وبحالة جيدة</option>
                                    <option value="تالف جزئيا">تالف جزئياً</option>
                                    <option value="تالف كليا">تالف كلياً</option>
                                    <option value="مفقود">مفقود</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">ملاحظات الفحص</label>
                                <textarea name="return_notes" class="form-control shadow-sm" rows="2" placeholder="اكتب وصف التلف..."></textarea>
                            </div>
                            <div id="deduction_section" style="display: none;"
                                class="p-3 bg-danger bg-opacity-10 border border-danger rounded-3 mb-3 shadow-sm">
                                <div class="form-check form-switch mb-2 d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0" type="checkbox" id="apply_deduction"
                                        name="apply_deduction" value="1" style="transform: scale(1.2);">
                                    <label class="form-check-label fw-bold text-danger" for="apply_deduction">توقيع غرامة
                                        مالية (خصم من الراتب)</label>
                                </div>
                                <div id="deduction_amount_container" style="display: none;" class="mt-3">
                                    <label class="form-label fw-bold text-dark">قيمة الخصم المطلوبة *</label>
                                    <input type="number" step="0.001" name="deduction_amount" id="deduction_amount"
                                        class="form-control fw-bold border-danger text-center fs-5 font-monospace">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm btn-submit"><i
                                    class="bx bx-check-double me-1"></i> تأكيد الاستلام</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        $(document).ready(function() {
            let currentLocale = "{{ app()->getLocale() }}";
            let isRtl = currentLocale === 'ar';

            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            $('.modal').on('shown.bs.modal', function(e) {
                $(e.currentTarget).find('select').each(function() {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        dir: isRtl ? 'rtl' : 'ltr',
                        dropdownParent: $(e.currentTarget)
                    });
                });
            });

            // الحسابات المالية اللحظية
            $(document).on('input', '#asset_amount, #asset_tax', function() {
                let amount = parseFloat($('#asset_amount').val()) || 0;
                let tax = parseFloat($('#asset_tax').val()) || 0;
                $('#asset_total').val((amount + (amount * tax / 100)).toFixed(3));
                calculateManualRemaining();
            });

            let allSubCats = @json($subCategories ?? []);

            function filterSubCategories(mainSelectId, subSelectId) {
                $(document).on('change', mainSelectId, function() {
                    let mainId = $(this).val();
                    let subSelect = $(subSelectId);
                    subSelect.empty().append('<option value="" selected>اختر القسم الفرعي</option>');
                    allSubCats.filter(sub => sub.asset_category_id == mainId).forEach(sub => {
                        subSelect.append(
                            `<option value="${sub.id}">${isRtl ? sub.name_ar : (sub.name_en || sub.name_ar)}</option>`
                            );
                    });
                });
            }
            filterSubCategories('#main_cat_select', '#sub_cat_select');
            filterSubCategories('#edit_main_cat_select', '#edit_sub_cat_select');

            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                let updateUrl = "{{ route('admin.assets.update', ':id') }}".replace(':id', id);
                $('#editAssetForm').attr('action', updateUrl);
                $('#edit_branch_id').val($(this).data('branch')).trigger('change');
                $('#edit_supplier_id').val($(this).data('supplier')).trigger('change');
                $('#edit_main_cat_select').val($(this).data('cat')).trigger('change');
                setTimeout(() => {
                    $('#edit_sub_cat_select').val($(this).data('subcat')).trigger('change');
                }, 250);
                $('#edit_transaction_details').val($(this).data('details'));
                $('#edit_supplier_invoice_no').val($(this).data('inv'));
                $('#edit_depreciation_rate').val($(this).data('dep'));
                $('#edit_check_number').val($(this).data('check'));
                $('#edit_notes').val($(this).data('notes'));
            });

            // 🌟 السحر كله هنا: إخفاء وإظهار الخزنة وإزالة ה-Required لو مش مطلوبة 🌟
            function toggleFinancialAccount() {
                let pType = $('.payment-type-radio:checked').val();
                let schedType = $('#schedule_type').val();
                let downPayment = 0;

                if (schedType === 'auto') {
                    downPayment = parseFloat($('#down_payment_auto').val()) || 0;
                } else {
                    downPayment = parseFloat($('#down_payment_manual').val()) || 0;
                }

                if (pType === 'cash') {
                    $('.financial-account-section').slideDown();
                    $('#financial_account_id').attr('required', true);
                    $('.installment-section').slideUp();
                } else if (pType === 'opening') {
                    $('.financial-account-section').slideUp();
                    $('#financial_account_id').attr('required', false).val('').trigger('change');
                    $('.installment-section').slideUp();
                } else if (pType === 'installment') {
                    $('.installment-section').slideDown();
                    if (downPayment > 0) {
                        $('.financial-account-section').slideDown();
                        $('#financial_account_id').attr('required', true);
                    } else {
                        $('.financial-account-section').slideUp();
                        $('#financial_account_id').attr('required', false).val('').trigger('change');
                    }
                }
            }

            $(document).on('change', '.payment-type-radio, #schedule_type', function() {
                toggleFinancialAccount();
            });

            $(document).on('input', '#down_payment_auto, #down_payment_manual', function() {
                toggleFinancialAccount();
                calculateManualRemaining();
            });

            // 🌟 الجدولة اليدوية 🌟
            $(document).on('click', '#add_manual_row', function() {
                let html = `
                <tr>
                    <td><input type="number" step="0.001" name="manual_amount[]" class="form-control text-center fw-bold manual-calc" required></td>
                    <td><input type="date" name="manual_date[]" class="form-control text-center fw-bold" required></td>
                    <td><button type="button" class="btn btn-sm btn-danger rounded-circle shadow-sm remove_row"><i class="bx bx-trash"></i></button></td>
                </tr>`;
                $('#manual_table tbody').append(html);
            });

            $(document).on('click', '.remove_row', function() {
                $(this).closest('tr').remove();
                calculateManualRemaining();
            });

            $(document).on('input', '.manual-calc', function() {
                calculateManualRemaining();
            });

            function calculateManualRemaining() {
                let total = parseFloat($('#asset_total').val()) || 0;
                let downPayment = parseFloat($('#down_payment_manual').val()) || 0;
                let sumManual = 0;
                $('.manual-calc').each(function() {
                    sumManual += parseFloat($(this).val()) || 0;
                });
                let rem = total - downPayment - sumManual;
                $('#manual_remaining').text(rem.toFixed(3));
            }

            $(document).on('click', '.delete-btn', function() {
                let id = $(this).data('id');
                let paid = parseFloat($(this).data('paid')) || 0;
                let remaining = parseFloat($(this).data('remaining')) || 0;
                let deleteUrl = "{{ route('admin.assets.destroy', ':id') }}".replace(':id', id);
                $('#deleteAssetForm').attr('action', deleteUrl);
                $('#del_asset_details').text($(this).data('details'));
                $('#del_asset_paid').text(paid.toFixed(3));
                $('#del_asset_remaining').text(remaining.toFixed(3));
                if (paid > 0) {
                    $('#refund_section').slideDown();
                    $('#refund_action').attr('required', true);
                } else {
                    $('#refund_section').slideUp();
                    $('#refund_action').attr('required', false);
                }
            });

            $(document).on('change', '#refund_action', function() {
                if ($(this).val() === 'refund') {
                    $('#refund_account_div').slideDown();
                    $('#refund_account_id').attr('required', true);
                } else {
                    $('#refund_account_div').slideUp();
                    $('#refund_account_id').attr('required', false);
                }
            });

            $(document).on('click', '.assign-custody-btn', function() {
                let id = $(this).data('id');
                $('#custody_asset_details').text($(this).data('details'));
                let assignUrl = "{{ route('admin.assets.custody.assign', ':id') }}".replace(':id', id);
                $('#assignCustodyForm').attr('action', assignUrl);
            });

            $(document).on('click', '.return-custody-btn', function() {
                let id = $(this).data('id');
                let price = parseFloat($(this).data('price')).toFixed(3);
                $('#return_asset_details').text($(this).data('details'));
                $('#return_employee_name').text($(this).data('employee'));
                $('#return_asset_price').text(price);
                $('#return_condition').val('');
                $('#deduction_section').hide();
                $('#apply_deduction').prop('checked', false);
                $('#deduction_amount_container').hide();
                $('#deduction_amount').val('').prop('required', false);
                let returnUrl = "{{ route('admin.assets.custody.return', ':id') }}".replace(':id', id);
                $('#returnCustodyForm').attr('action', returnUrl);
            });

            $(document).on('change', '#return_condition', function() {
                let condition = $(this).val();
                if (condition === 'تالف جزئيا' || condition === 'تالف كليا' || condition === 'مفقود') {
                    $('#deduction_section').slideDown();
                } else {
                    $('#deduction_section').slideUp();
                    $('#apply_deduction').prop('checked', false).trigger('change');
                }
            });

            $(document).on('change', '#apply_deduction', function() {
                if ($(this).is(':checked')) {
                    $('#deduction_amount_container').slideDown();
                    $('#deduction_amount').prop('required', true);
                } else {
                    $('#deduction_amount_container').slideUp();
                    $('#deduction_amount').prop('required', false).val('');
                }
            });

            $('#assetForm').on('submit', function(e) {
                let type = $('.payment-type-radio:checked').val();
                if (type === 'installment') {
                    let sched = $('#schedule_type').val();
                    if (sched === 'manual') {
                        let rem = parseFloat($('#manual_remaining').text()) || 0;
                        if (Math.abs(rem) > 0.01) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ في الجدولة',
                                text: 'مجموع الأقساط والدفعة المقدمة لا يساوي الإجمالي المطلوب!'
                            });
                            return false;
                        }
                    }
                }
            });

            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    "<i class='bx bx-loader-alt bx-spin me-1'></i> جاري التنفيذ...");
            });

            let canvasEmp = document.getElementById('signature-pad-employee');
            let canvasAdmin = document.getElementById('signature-pad-admin');

            if (canvasEmp && canvasAdmin) {
                let signaturePadEmp = new SignaturePad(canvasEmp, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(37, 99, 235)'
                });
                let signaturePadAdmin = new SignaturePad(canvasAdmin, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(15, 23, 42)'
                });

                $('#clear-signature-emp').click(() => signaturePadEmp.clear());
                $('#clear-signature-admin').click(() => signaturePadAdmin.clear());

                function resizeCanvas() {
                    let ratio = Math.max(window.devicePixelRatio || 1, 1);
                    [canvasEmp, canvasAdmin].forEach(canvas => {
                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = canvas.offsetHeight * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);
                    });
                    signaturePadEmp.clear();
                    signaturePadAdmin.clear();
                }

                window.onresize = resizeCanvas;
                $('#assignCustodyModal').on('shown.bs.modal', resizeCanvas);

                $('#assignCustodyForm').submit(function(e) {
                    if (signaturePadEmp.isEmpty() || signaturePadAdmin.isEmpty()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه هام!',
                            text: 'يجب توقيع الطرفين قبل الحفظ!',
                            confirmButtonColor: '#f59e0b'
                        });
                        $(this).find('.btn-submit').prop('disabled', false).html(
                            "<i class='bx bx-check-shield me-1'></i> حفظ وتسليم كعهدة");
                        return false;
                    }
                    $('#signature_base64').val(signaturePadEmp.toDataURL('image/png'));
                    $('#admin_signature_base64').val(signaturePadAdmin.toDataURL('image/png'));
                });
            }

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'نجاح الإجراء',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#10b981',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ بالنظام',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>
@endpush
