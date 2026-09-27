@extends('backend.layouts.master')
@section('title', 'سجل الإيرادات')
@section('title_page', 'سجل الإيرادات')

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

        .table-erp thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        .val-total {
            color: #10b981;
            font-weight: 800;
            font-size: 15px;
            font-family: monospace;
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

        .btn-action {
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

        .btn-action:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .btn-action.edit:hover {
            background: #fffbeb;
            color: #d97706;
            border-color: #fde68a;
        }

        .btn-action.delete:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .btn-action.print:hover {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }

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

        .input-group-text {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
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

        /* Dark Mode */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
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

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        <div class="card card-premium mb-4">
            <div class="card-body d-flex justify-content-between align-items-center p-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="bx bx-trending-up fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">سجل الإيرادات والمقبوضات</h4>
                        <p class="text-muted mb-0 font-13">متابعة كافة الحركات المالية الواردة للنظام بشكل مفصل.</p>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @can('create_incomes')
                        <button type="button" class="btn btn-modern btn-secondary-modern" data-bs-toggle="modal"
                            data-bs-target="#addMainCategoryModal">
                            <i class="bx bx-category text-primary"></i> قسم رئيسي
                        </button>
                        <button type="button" class="btn btn-modern btn-secondary-modern" data-bs-toggle="modal"
                            data-bs-target="#addSubCategoryModal">
                            <i class="bx bx-git-repo-forked text-info"></i> قسم فرعي
                        </button>
                        <button type="button" class="btn btn-modern btn-primary-modern ms-2" data-bs-toggle="modal"
                            data-bs-target="#addIncomeModal">
                            <i class="bx bx-plus-circle"></i> إضافة إيراد جديد
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm mb-4 fw-bold">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="bx bx-error-circle align-middle me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🔥 جدول الإيرادات 🔥 --}}
        <div class="card card-premium">
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table class="table table-striped table-bordered table-hover table-erp w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم الإيصال</th>
                                <th>التاريخ</th>
                                <th>الفرع</th>
                                <th class="text-end">الإجمالي</th>
                                <th class="text-start">البيان / التفاصيل</th>
                                <th>القسم الفرعي</th>
                                <th>القسم الرئيسي</th>
                                <th>الجهة / المصدر</th>
                                <th>رقم المرجع</th>
                                <th class="text-end">الأساسي</th>
                                <th class="text-end">الضريبة</th>
                                <th>حساب الإيداع</th>
                                <th>المستخدم</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($incomes as $index => $income)
                                @php
                                    $catName = $income->category->name_ar ?? '-';
                                    $subCatName = $income->subCategory->name_ar ?? '-';
                                    $accName = $income->financialAccount->name_ar ?? 'غير محدد';
                                    $branchName = $income->branch->name_ar ?? 'الفرع الرئيسي';
                                @endphp
                                <tr>
                                    <td><span
                                            class="badge bg-light text-secondary border px-2 py-1">{{ $index + 1 }}</span>
                                    </td>
                                    <td><span class="fw-bold text-dark font-monospace">{{ $income->income_code }}</span>
                                    </td>
                                    <td><i
                                            class="bx bx-calendar text-muted me-1"></i>{{ \Carbon\Carbon::parse($income->income_date)->format('Y-m-d') }}
                                    </td>
                                    <td><span class="badge badge-soft-dark px-2 py-1"><i class='bx bx-store-alt'></i>
                                            {{ $branchName }}</span></td>
                                    <td class="text-end val-total">{{ number_format($income->total_amount, 3) }}</td>
                                    <td class="text-start fw-bold text-dark">{{ $income->transaction_details }}</td>
                                    <td><span class="badge badge-soft-info px-2 py-1">{{ $subCatName }}</span></td>
                                    <td><span class="badge badge-soft-primary px-2 py-1">{{ $catName }}</span></td>
                                    <td class="fw-bold text-secondary"><i
                                            class="bx bx-user text-muted me-1"></i>{{ $income->source_name ?? 'عام' }}</td>
                                    <td class="text-muted font-monospace">{{ $income->reference_no ?? '-' }}</td>
                                    <td class="text-end val-basic">{{ number_format($income->amount, 3) }}</td>
                                    <td class="text-end val-tax">{{ number_format($income->tax_amount, 3) }}</td>
                                    <td><span
                                            class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1"><i
                                                class="bx bx-wallet me-1"></i>{{ $accName }}</span></td>
                                    <td><small class="text-muted"><i
                                                class="bx bx-user-circle me-1"></i>{{ $income->user->name ?? 'Admin' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">

                                            {{-- 🔥 زر الطباعة الجديد للإيصال 🔥 --}}
                                            <a href="{{ route('admin.incomes.print_receipt', $income->id) }}"
                                                target="_blank" class="btn-action print" title="طباعة سند القبض">
                                                <i class="bx bx-printer"></i>
                                            </a>

                                            @if ($income->attachment)
                                                <a href="{{ asset('storage/' . $income->attachment) }}" target="_blank"
                                                    class="btn-action" title="عرض المرفق">
                                                    <i class="bx bx-link-external"></i>
                                                </a>
                                            @endif

                                            @can('edit_incomes')
                                                <button type="button" class="btn-action edit edit-btn"
                                                    data-id="{{ $income->id }}" data-branch="{{ $income->branch_id }}"
                                                    data-cat="{{ $income->income_category_id }}"
                                                    data-subcat="{{ $income->income_sub_category_id }}"
                                                    data-source="{{ $income->source_name }}"
                                                    data-ref="{{ $income->reference_no }}"
                                                    data-details="{{ $income->transaction_details }}"
                                                    data-amount="{{ $income->amount }}"
                                                    data-tax="{{ $income->tax_percent ?? 0 }}"
                                                    data-total="{{ $income->total_amount }}"
                                                    data-date="{{ \Carbon\Carbon::parse($income->income_date)->format('Y-m-d') }}"
                                                    data-check="{{ $income->check_number }}" data-notes="{{ $income->notes }}"
                                                    title="تعديل الإيراد" data-bs-toggle="modal"
                                                    data-bs-target="#editIncomeModal">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                            @endcan

                                            @can('delete_incomes')
                                                <button type="button" class="btn-action delete delete-btn"
                                                    data-url="{{ route('admin.incomes.destroy', $income->id) }}"
                                                    title="إلغاء الإيراد">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-muted py-5 text-center fs-5 fw-bold">
                                        <i class="bx bx-folder-open fs-1 d-block mb-2 text-black-50 opacity-50"></i> لا
                                        توجد إيرادات مسجلة حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- أزرار التصفح (Pagination) --}}
                <div class="d-flex justify-content-center py-3">
                    {{ $incomes->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>

    {{-- =============================================== --}}
    {{-- 🔥 النوافذ المنبثقة (Modals) 🔥 --}}
    {{-- =============================================== --}}

    @can('create_incomes')
        {{-- 1. إضافة إيراد --}}
        <div class="modal fade" id="addIncomeModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean"><i class="bx bx-plus-circle text-primary me-2"></i> إضافة
                            إيراد مالي جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.incomes.store') }}" method="POST" enctype="multipart/form-data"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body modal-body-clean text-start">
                            <div class="section-title"><i class="bx bx-category"></i> التصنيف والمصدر</div>
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
                                    <select name="income_category_id" id="main_cat_select" class="form-select select2-init"
                                        required style="width: 100%;">
                                        <option value="" disabled selected>-- اختر القسم --</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">القسم الفرعي</label>
                                    <select name="income_sub_category_id" id="sub_cat_select"
                                        class="form-select select2-init" style="width: 100%;">
                                        <option value="" selected>اختر القسم الرئيسي أولاً</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">مصدر الإيراد</label>
                                    <input type="text" name="source_name" class="form-control fw-bold"
                                        placeholder="اسم العميل أو الجهة...">
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-money"></i> التفاصيل المالية</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label text-danger">المبلغ الأساسي *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-money text-muted"></i></span>
                                        <input type="number" step="0.001" name="amount" id="income_amount"
                                            class="form-control text-center fw-bold text-dark fs-5 font-monospace"
                                            value="0.000" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">نسبة الضريبة (%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">%</span>
                                        <input type="number" step="0.01" name="tax_percent" id="income_tax"
                                            class="form-control text-center fw-bold font-monospace" value="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-success">المبلغ الإجمالي</label>
                                    <input type="text" id="income_total"
                                        class="form-control text-center bg-success bg-opacity-10 text-success fw-bold fs-5 border-success font-monospace"
                                        value="0.000" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">الرقم المرجعي</label>
                                    <input type="text" name="reference_no"
                                        class="form-control text-center font-monospace fw-bold"
                                        placeholder="رقم الشيك أو الحوالة...">
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-wallet"></i> الإيداع والتاريخ</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-danger">تاريخ الإيراد *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-calendar text-muted"></i></span>
                                        <input type="date" name="income_date"
                                            class="form-control text-center fw-bold text-dark" value="{{ date('Y-m-d') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-danger">حساب الإيداع *</label>
                                    <select name="financial_account_id"
                                        class="form-select select2-init border-success fw-bold text-success" required
                                        style="width: 100%;">
                                        <option value="" disabled selected>-- اختر حساب الإيداع --</option>
                                        @foreach ($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم الشيك (إن وجد)</label>
                                    <input type="text" name="check_number" class="form-control text-center font-monospace"
                                        placeholder="رقم الشيك...">
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-detail"></i> التفاصيل والمرفقات</div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label text-danger">البيان / التفاصيل *</label>
                                    <input type="text" name="transaction_details" class="form-control fw-bold"
                                        placeholder="اكتب تفاصيل الإيراد هنا..." required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">المرفقات <span
                                            class="text-muted fw-normal">(اختياري)</span></label>
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
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-primary-modern px-5 fw-bold btn-submit"><i
                                    class="bx bx-save me-1"></i> حفظ الإيراد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 2. إضافة قسم رئيسي --}}
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
                                <label class="form-label text-danger">اسم القسم *</label>
                                <input type="text" name="name_ar" class="form-control fw-bold border-danger" required
                                    placeholder="مثال: إيجارات">
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-clean">
                            <button type="submit" class="btn btn-primary-modern px-4 w-100 fw-bold btn-submit">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. إضافة قسم فرعي --}}
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
                                <label class="form-label text-danger">القسم الرئيسي *</label>
                                <select name="income_category_id" class="form-select select2-init" required
                                    style="width: 100%;">
                                    <option value="" disabled selected>-- اختر القسم الرئيسي --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-danger">اسم القسم الفرعي *</label>
                                <input type="text" name="name_ar" class="form-control fw-bold" required
                                    placeholder="مثال: إيجار شقة">
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-clean">
                            <button type="submit" class="btn btn-primary-modern px-4 w-100 fw-bold btn-submit">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('edit_incomes')
        {{-- 4. تعديل إيراد --}}
        <div class="modal fade" id="editIncomeModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header modal-header-clean">
                        <h5 class="modal-title modal-title-clean text-warning"><i class="bx bx-edit me-2"></i> تعديل بيانات
                            الإيراد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data" id="editIncomeForm"
                        class="prevent-double-submit">
                        @csrf @method('PUT')
                        <div class="modal-body modal-body-clean text-start">
                            <div
                                class="alert alert-warning fw-bold text-center border-warning bg-warning bg-opacity-10 shadow-sm rounded-3">
                                <i class='bx bx-info-circle fs-5 align-middle me-1'></i> تنبيه: لتعديل المبالغ يجب إلغاء السند
                                وإعادة إصداره. هنا يمكنك تعديل البيانات الوصفية فقط.
                            </div>

                            <div class="section-title"><i class="bx bx-category"></i> التصنيف والمصدر</div>
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
                                    <select name="income_category_id" id="edit_main_cat_select"
                                        class="form-select select2-edit" required style="width: 100%;">
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">القسم الفرعي</label>
                                    <select name="income_sub_category_id" id="edit_sub_cat_select"
                                        class="form-select select2-edit" style="width: 100%;">
                                        <option value="" selected>اختر القسم</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">مصدر الإيراد</label>
                                    <input type="text" name="source_name" id="edit_source_name"
                                        class="form-control fw-bold">
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-money"></i> التفاصيل المالية</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label text-danger">المبلغ الأساسي *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-money text-muted"></i></span>
                                        <input type="number" step="0.001" name="amount" id="edit_amount"
                                            class="form-control text-center fw-bold text-dark fs-5 font-monospace" required
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">نسبة الضريبة (%)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">%</span>
                                        <input type="number" step="0.01" name="tax_percent" id="edit_tax"
                                            class="form-control text-center fw-bold font-monospace" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-success">المبلغ الإجمالي</label>
                                    <input type="text" id="edit_income_total"
                                        class="form-control text-center bg-success bg-opacity-10 text-success fw-bold fs-5 border-success font-monospace"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">الرقم المرجعي</label>
                                    <input type="text" name="reference_no" id="edit_reference_no"
                                        class="form-control text-center font-monospace fw-bold">
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-wallet"></i> الإيداع والتاريخ</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-danger">تاريخ الإيراد *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-calendar text-muted"></i></span>
                                        <input type="date" name="income_date" id="edit_income_date"
                                            class="form-control text-center fw-bold text-dark" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم الشيك (إن وجد)</label>
                                    <input type="text" name="check_number" id="edit_check_number"
                                        class="form-control text-center font-monospace">
                                </div>
                            </div>

                            <div class="section-title"><i class="bx bx-detail"></i> التفاصيل والمرفقات</div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label text-danger">البيان / التفاصيل *</label>
                                    <input type="text" name="transaction_details" id="edit_transaction_details"
                                        class="form-control fw-bold" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">المرفقات</label>
                                    <input type="file" name="attachment" class="form-control" accept="image/*,.pdf">
                                    <small class="text-muted mt-1 d-block">إرفاق ملف جديد سيستبدل المرفق القديم.</small>
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
    @endcan
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // 1. Select2
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

            // 2. الحسابات 
            $('#income_amount, #income_tax').on('input change keyup', function() {
                let amount = parseFloat($('#income_amount').val()) || 0;
                let tax = parseFloat($('#income_tax').val()) || 0;
                $('#income_total').val((amount + (amount * tax / 100)).toFixed(3));
            });

            // 3. الفلترة
            let rawSubCats = {!! json_encode($subCategories ?? []) !!};
            let allSubCats = Array.isArray(rawSubCats) ? rawSubCats : Object.values(rawSubCats);

            function filterSubCategories(mainSelectId, subSelectId, selectedVal = null) {
                let mainId = $(mainSelectId).val();
                let subSelect = $(subSelectId);

                subSelect.empty().append('<option value="" selected>-- اختر القسم الفرعي --</option>');

                if (mainId) {
                    allSubCats.filter(sub => String(sub.income_category_id) === String(mainId)).forEach(sub => {
                        let isSelected = (String(selectedVal) === String(sub.id)) ? 'selected' : '';
                        subSelect.append(`<option value="${sub.id}" ${isSelected}>${sub.name_ar}</option>`);
                    });
                }
                subSelect.trigger('change.select2');
            }

            $(document).on('change select2:select', '#main_cat_select', function() {
                filterSubCategories('#main_cat_select', '#sub_cat_select');
            });
            $(document).on('change select2:select', '#edit_main_cat_select', function() {
                filterSubCategories('#edit_main_cat_select', '#edit_sub_cat_select');
            });

            // 4. التعديل
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $('#editIncomeForm').attr('action', `/ar/admin/incomes/${id}`);

                $('#edit_branch_id').val($(this).data('branch')).trigger('change.select2');
                $('#edit_source_name').val($(this).data('source'));
                $('#edit_reference_no').val($(this).data('ref'));
                $('#edit_transaction_details').val($(this).data('details'));

                $('#edit_amount').val($(this).data('amount'));
                $('#edit_tax').val($(this).data('tax'));
                $('#edit_income_total').val(parseFloat($(this).data('total')).toFixed(3));

                $('#edit_income_date').val($(this).data('date'));
                $('#edit_check_number').val($(this).data('check'));
                $('#edit_notes').val($(this).data('notes'));

                let mainCatId = $(this).data('cat');
                let subCatId = $(this).data('subcat');

                $('#edit_main_cat_select').val(mainCatId).trigger('change.select2');
                setTimeout(() => {
                    filterSubCategories('#edit_main_cat_select', '#edit_sub_cat_select', subCatId);
                }, 200);
            });

            // 5. الحذف
            $(document).on('click', '.delete-btn', function() {
                let deleteUrl = $(this).data('url');
                Swal.fire({
                    title: 'تأكيد الحذف؟',
                    text: "سيتم إلغاء الإيراد وخصمه من الخزنة لضبط الحسابات!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'نعم، احذف',
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
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري الحفظ...');
            });

            // إشعارات
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'عملية ناجحة',
                    text: "{{ session('success') }}",
                    timer: 2500,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: "{{ session('error') }}"
                });
            @endif
        });
    </script>
@endpush
