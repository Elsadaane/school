@extends('backend.layouts.master')
@section('title', 'سجل المصروفات')
@section('title_page', 'سجل المصروفات')

@push('css')
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

        .btn-danger-modern {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
        }

        .btn-danger-modern:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
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
            background-color: #fef2f2;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #fecaca;
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
            background-color: #fcf4f4;
        }

        .val-total {
            color: #dc2626;
            font-weight: 800;
            font-size: 15px;
            font-family: 'Courier New', Courier, monospace;
            background: #fef2f2;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid #fecaca;
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

        .badge-soft-danger {
            background-color: #fef2f2;
            color: #ef4444;
            border: 1px solid #fecaca;
        }

        .badge-soft-warning {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
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
            border-color: #fca5a5;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .input-group-text {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
        }

        .section-title {
            font-size: 14px;
            font-weight: 800;
            color: #dc2626;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px dashed #e2e8f0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Dark Mode Adjustments */
        body[data-layout-color="dark"] .card-premium {
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
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger shadow-sm">
                        <i class="bx bx-trending-down fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">سجل المصروفات والمدفوعات</h4>
                        <p class="text-muted mb-0 font-13">متابعة وتحديث كافة المصروفات وإدارة سندات الصرف.</p>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @can('manage_expenses')
                        <button type="button" class="btn btn-modern btn-secondary-modern" data-bs-toggle="modal"
                            data-bs-target="#addMainCategoryModal">
                            <i class="bx bx-category text-danger"></i> قسم رئيسي
                        </button>
                        <button type="button" class="btn btn-modern btn-secondary-modern" data-bs-toggle="modal"
                            data-bs-target="#addSubCategoryModal">
                            <i class="bx bx-git-repo-forked text-warning"></i> قسم فرعي
                        </button>
                        <button type="button" class="btn btn-modern btn-danger-modern ms-2" data-bs-toggle="modal"
                            data-bs-target="#addExpenseModal">
                            <i class="bx bx-plus-circle"></i> إصدار سند مصروف
                        </button>
                    @endcan
                </div>
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

        {{-- 🔥 جدول المصروفات العادي (بدون DataTables) 🔥 --}}
        <div class="card card-premium">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom-0">
                <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-list-ul me-1 align-middle"></i> جدول المصروفات العام</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3 pt-0">
                    <table
                        class="table table-striped table-bordered table-hover table-erp align-middle text-center mb-0 w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>تاريخ الصرف</th>
                                <th>الفرع</th>
                                <th class="text-end">الإجمالي</th>
                                <th style="min-width: 180px;" class="text-start">البيان / التفاصيل</th>
                                <th>القسم الرئيسي</th>
                                <th>القسم الفرعي</th>
                                <th>المورد</th>
                                <th>رقم الفاتورة</th>
                                <th class="text-end">الأساسي</th>
                                <th class="text-end">الضريبة</th>
                                <th>كود السند</th>
                                <th>حساب الصرف</th>
                                <th>المستخدم</th>
                                @can('manage_expenses')
                                    <th style="min-width: 120px;">الإجراءات</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expenses as $index => $expense)
                                @php
                                    $catName = $expense->category->name_ar ?? '-';
                                    $subCatName = $expense->subCategory->name_ar ?? '-';
                                    $branchName = $expense->branch->name_ar ?? 'الفرع الرئيسي';
                                    $supplierName = $expense->supplier->name ?? 'مصروف عام';
                                    $accName = $expense->financialAccount->name_ar ?? 'غير محدد';
                                @endphp
                                <tr>
                                    <td><span
                                            class="badge bg-light text-secondary border px-2 py-1">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="fw-bold text-dark"><i
                                            class="bx bx-calendar text-muted me-1"></i>{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}
                                    </td>
                                    <td><span class="badge badge-soft-dark px-2 py-1"><i class='bx bx-store-alt'></i>
                                            {{ $branchName }}</span></td>
                                    <td class="text-end val-total">{{ number_format($expense->total_amount, 3) }}</td>
                                    <td class="text-start fw-bold text-dark">{{ $expense->transaction_details }}</td>
                                    <td><span class="badge badge-soft-danger px-2 py-1">{{ $catName }}</span></td>
                                    <td><span
                                            class="badge badge-soft-warning px-2 py-1 text-dark">{{ $subCatName }}</span>
                                    </td>
                                    <td class="fw-bold text-secondary">{{ $supplierName }}</td>
                                    <td class="text-muted font-monospace">{{ $expense->supplier_invoice_no ?? '-' }}</td>
                                    <td class="text-end val-basic">{{ number_format($expense->amount, 3) }}</td>
                                    <td class="text-end val-tax">{{ number_format($expense->tax_amount, 3) }}</td>
                                    <td class="fw-bold text-danger font-monospace">{{ $expense->expense_code }}</td>
                                    <td><span class="badge badge-soft-dark px-2 py-1"><i
                                                class="bx bx-wallet me-1"></i>{{ $accName }}</span></td>
                                    <td><small class="text-muted"><i
                                                class="bx bx-user-circle me-1"></i>{{ $expense->user->name ?? 'Admin' }}</small>
                                    </td>
                                    @can('manage_expenses')
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                @if ($expense->attachment)
                                                    <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank"
                                                        class="action-icon" title="عرض المرفق">
                                                        <i class="bx bx-link-external"></i>
                                                    </a>
                                                @endif
                                                <button type="button" class="action-icon edit edit-btn"
                                                    data-id="{{ $expense->id }}" data-branch="{{ $expense->branch_id }}"
                                                    data-cat="{{ $expense->expense_category_id }}"
                                                    data-subcat="{{ $expense->expense_sub_category_id }}"
                                                    data-supplier="{{ $expense->supplier_id }}"
                                                    data-inv="{{ $expense->supplier_invoice_no }}"
                                                    data-details="{{ $expense->transaction_details }}"
                                                    data-date="{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}"
                                                    data-check="{{ $expense->check_number }}"
                                                    data-notes="{{ $expense->notes }}" title="تعديل المصروف"
                                                    data-bs-toggle="modal" data-bs-target="#editExpenseModal">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button" class="action-icon delete delete-btn"
                                                    data-url="{{ route('admin.expenses.destroy', $expense->id) }}"
                                                    title="إلغاء المصروف">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                                <a href="{{ route('admin.expenses.print_receipt', $expense->id) }}"
                                                    target="_blank" class="btn btn-sm btn-light text-dark border shadow-sm"
                                                    title="طباعة إيصال السند">
                                                    <i class="bx bx-printer"></i>
                                                </a>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-muted py-5 text-center fs-5">
                                        <i class="bx bx-folder-open fs-1 d-block mb-2 text-black-50"></i> لا توجد مصروفات
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

    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- ================================================= --}}
    {{-- النوافذ المنبثقة (Modals) --}}
    {{-- ================================================= --}}

    @can('manage_expenses')
        {{-- 1. مودال إضافة مصروف جديد --}}
        <div class="modal fade" id="addExpenseModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean"><i class="bx bx-minus-circle text-danger me-2"></i> إصدار
                            سند صرف (تسجيل مصروف)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body modal-body-clean">
                            <div class="section-title"><i class="bx bx-category text-danger"></i> التصنيف والمورد</div>
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
                                    <select name="expense_category_id" id="main_cat_select" class="form-select select2-init"
                                        required style="width: 100%;">
                                        <option value="" disabled selected>-- اختر القسم الرئيسي --</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">القسم الفرعي</label>
                                    <select name="expense_sub_category_id" id="sub_cat_select"
                                        class="form-select select2-init" style="width: 100%;">
                                        <option value="" selected>اختر القسم الرئيسي أولاً...</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">المورد (جهة الصرف)</label>
                                    <select name="supplier_id" class="form-select select2-init" style="width: 100%;">
                                        <option value="" selected>مصروف عام (بدون مورد)</option>
                                        @foreach ($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-money text-danger"></i> التفاصيل المالية</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label text-danger">المبلغ الأساسي *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bx bx-money text-muted"></i></span>
                                        <input type="number" step="0.001" name="amount" id="expense_amount"
                                            class="form-control text-center fw-bold text-dark fs-5 font-monospace"
                                            value="0.000" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">الضريبة المضافة (%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">%</span>
                                        <input type="number" step="0.01" name="tax_percent" id="expense_tax"
                                            class="form-control text-center fw-bold" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-danger">المبلغ الإجمالي للمصروف</label>
                                    <input type="text" id="expense_total"
                                        class="form-control text-center bg-danger bg-opacity-10 text-danger fw-bold fs-5 border-danger font-monospace"
                                        value="0.000" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">رقم الفاتورة (إن وجد)</label>
                                    <input type="text" name="supplier_invoice_no" class="form-control text-center"
                                        placeholder="رقم الفاتورة...">
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-wallet text-danger"></i> بيانات الخزنة والتاريخ</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-danger">تاريخ الصرف *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bx bx-calendar text-muted"></i></span>
                                        <input type="date" name="expense_date"
                                            class="form-control text-center fw-bold text-dark" value="{{ date('Y-m-d') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-danger">حساب الصرف (الخزنة) *</label>
                                    <select name="financial_account_id"
                                        class="form-select select2-init border-danger fw-bold text-danger" required
                                        style="width: 100%;">
                                        <option value="" disabled selected>-- اختر الخزنة لخصم المبلغ --</option>
                                        @foreach ($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم الشيك (إن وجد)</label>
                                    <input type="text" name="check_number" class="form-control text-center"
                                        placeholder="رقم الشيك...">
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-detail text-danger"></i> البيان والمرفقات</div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label text-danger">البيان (وصف المصروف) *</label>
                                    <input type="text" name="transaction_details" class="form-control"
                                        placeholder="مثال: سداد فاتورة كهرباء..." required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">المرفقات <span class="text-muted fw-normal">(صورة
                                            الفاتورة)</span></label>
                                    <input type="file" name="attachment" class="form-control" accept="image/*,.pdf">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">ملاحظات إضافية</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="أي ملاحظات..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-clean pt-3 pb-3">
                            <button type="button" class="btn btn-secondary-modern px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-danger-modern px-5 fw-bold shadow-sm btn-submit"><i
                                    class="bx bx-minus-circle me-1"></i> تسجيل وخصم من الخزنة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal: Edit Expense (تعديل مصروف) --}}
        <div class="modal fade" id="editExpenseModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean text-warning"><i class="bx bx-edit me-2"></i> تعديل بيانات
                            المصروف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data" id="editExpenseForm"
                        class="prevent-double-submit">
                        @csrf
                        @method('PUT')
                        <div class="modal-body modal-body-clean">
                            <div
                                class="alert alert-warning fw-bold text-center border-warning bg-warning bg-opacity-10 shadow-sm rounded-3">
                                <i class='bx bx-info-circle fs-5 align-middle me-1'></i> تنبيه: لتعديل المبالغ المالية يجب
                                إلغاء السند بالكامل وإصداره من جديد لضبط رصيد الخزنة بشكل صحيح. هنا يمكنك تعديل البيانات الوصفية
                                فقط.
                            </div>

                            <div class="section-title"><i class="bx bx-category"></i> التصنيف والمورد</div>
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
                                    <select name="expense_category_id" id="edit_main_cat_select"
                                        class="form-select select2-edit" required style="width: 100%;">
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">القسم الفرعي</label>
                                    <select name="expense_sub_category_id" id="edit_sub_cat_select"
                                        class="form-select select2-edit" style="width: 100%;">
                                        <option value="" selected>اختر القسم</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">المورد</label>
                                    <select name="supplier_id" id="edit_supplier_id" class="form-select select2-edit"
                                        style="width: 100%;">
                                        <option value="">مصروف عام (بدون مورد)</option>
                                        @foreach ($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-detail"></i> البيانات والتفاصيل</div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-danger">تاريخ الصرف *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i
                                                class="bx bx-calendar text-muted"></i></span>
                                        <input type="date" name="expense_date" id="edit_expense_date"
                                            class="form-control text-center fw-bold text-dark" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم الفاتورة</label>
                                    <input type="text" name="supplier_invoice_no" id="edit_supplier_invoice_no"
                                        class="form-control text-center">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم الشيك</label>
                                    <input type="text" name="check_number" id="edit_check_number"
                                        class="form-control text-center">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label text-danger">البيان (وصف المصروف) *</label>
                                    <input type="text" name="transaction_details" id="edit_transaction_details"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">المرفقات <span
                                            class="text-muted fw-normal">(اختياري)</span></label>
                                    <input type="file" name="attachment" class="form-control" accept="image/*,.pdf">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">ملاحظات إضافية</label>
                                    <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-clean pt-3 pb-3">
                            <button type="button" class="btn btn-secondary-modern px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-warning text-dark px-5 fw-bold shadow-sm btn-submit"><i
                                    class="bx bx-save me-1"></i> حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal: Add Main Category --}}
        <div class="modal fade" id="addMainCategoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean"><i class="bx bx-category text-danger me-2"></i> إضافة نوع
                            مصروف رئيسي</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.expenses.category.store') }}" method="POST"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body modal-body-clean row g-3 text-start">
                            <div class="col-md-12">
                                <label class="form-label text-danger">اسم القسم *</label>
                                <input type="text" name="name_ar" class="form-control fw-bold border-danger" required
                                    placeholder="مثال: مصروفات إدارية">
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-clean">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-danger-modern px-4 fw-bold btn-submit">حفظ القسم</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal: Add Sub Category --}}
        <div class="modal fade" id="addSubCategoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean"><i class="bx bx-git-repo-forked text-warning me-2"></i>
                            إضافة نوع مصروف فرعي</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.expenses.subcategory.store') }}" method="POST"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body modal-body-clean row g-3 text-start">
                            <div class="col-md-12">
                                <label class="form-label text-danger">القسم الرئيسي التابع له *</label>
                                <select name="expense_category_id" class="form-select select2-init border-danger fw-bold"
                                    required style="width: 100%;">
                                    <option value="" disabled selected>-- اختر القسم الرئيسي --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-danger">اسم القسم الفرعي *</label>
                                <input type="text" name="name_ar" class="form-control fw-bold border-danger" required
                                    placeholder="مثال: فواتير كهرباء">
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-clean">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-danger-modern px-4 fw-bold btn-submit">حفظ القسم
                                الفرعي</button>
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

    <script>
        $(document).ready(function() {

            // 1. إعدادات Select2 نفس طريقة الأصول بالظبط
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: 'rtl'
            });

            $('.select2-edit').select2({
                theme: 'bootstrap-5',
                dir: 'rtl'
            });

            $('.modal').on('shown.bs.modal', function(e) {
                $(e.currentTarget).find('select').each(function() {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        dir: 'rtl',
                        dropdownParent: $(e.currentTarget)
                    });
                });
            });

            // 2. الحسابات المالية اللحظية
            $(document).on('input', '#expense_amount, #expense_tax', function() {
                let amount = parseFloat($('#expense_amount').val()) || 0;
                let tax = parseFloat($('#expense_tax').val()) || 0;
                $('#expense_total').val((amount + (amount * tax / 100)).toFixed(3));
            });

            // 3. الفلترة المحلية الصافية والمضمونة 100% (نسخة الأصول)
            let allSubCats = @json($subCategories ?? []);

            function filterSubCategories(mainSelectId, subSelectId) {
                $(document).on('change', mainSelectId, function() {
                    let mainId = $(this).val();
                    let subSelect = $(subSelectId);

                    subSelect.empty().append(
                        '<option value="" selected disabled>-- اختر القسم الفرعي --</option>');

                    if (mainId) {
                        allSubCats.filter(sub => sub.expense_category_id == mainId).forEach(sub => {
                            subSelect.append(`<option value="${sub.id}">${sub.name_ar}</option>`);
                        });
                    }
                });
            }

            filterSubCategories('#main_cat_select', '#sub_cat_select');
            filterSubCategories('#edit_main_cat_select', '#edit_sub_cat_select');

            // 4. تمرير البيانات لمودال التعديل
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $('#editExpenseForm').attr('action', `/ar/admin/expenses/${id}`);

                $('#edit_branch_id').val($(this).data('branch')).trigger('change');
                $('#edit_supplier_id').val($(this).data('supplier') || '').trigger('change');

                $('#edit_supplier_invoice_no').val($(this).data('inv'));
                $('#edit_transaction_details').val($(this).data('details'));
                $('#edit_expense_date').val($(this).data('date'));
                $('#edit_check_number').val($(this).data('check'));
                $('#edit_notes').val($(this).data('notes'));

                let mainCatId = $(this).data('cat');
                let subCatId = $(this).data('subcat');

                $('#edit_main_cat_select').val(mainCatId).trigger('change');

                // تأخير بسيط عشان الخيارات تتضاف قبل ما نعمل سيلكت
                setTimeout(() => {
                    $('#edit_sub_cat_select').val(subCatId).trigger('change');
                }, 250);
            });

            // 5. الحذف
            $(document).on('click', '.delete-btn', function() {
                let deleteUrl = $(this).data('url');
                Swal.fire({
                    title: 'تأكيد الإلغاء؟',
                    text: "سيتم حذف المصروف واسترداد المبلغ الخاص به للخزنة لضبط الحسابات!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'نعم، قم بالإلغاء',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-form').attr('action', deleteUrl).submit();
                    }
                });
            });

            // 6. منع دبل كليك
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري التنفيذ...');
            });

            // 7. الإشعارات
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'عملية ناجحة',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#10b981',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'عفواً!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>
@endpush
