@extends('backend.layouts.master')
@section('title', 'سندات القبض والمدفوعات')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* الكروت الأساسية */
        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
        }

        /* الأزرار العلوية والتبويبات */
        .btn-modern {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-receipt {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }

        .btn-receipt:hover {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            transform: translateY(-2px);
        }

        .btn-payment {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
        }

        .btn-payment:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            transform: translateY(-2px);
        }

        /* فلاتر البحث */
        .filter-label {
            font-weight: 700;
            color: #64748b;
            font-size: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .filter-input {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .filter-input:focus {
            border-color: #3b82f6;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* الجدول المالي */
        .table-erp {
            white-space: nowrap;
            /* تأمين الجدول من التكسير في الشاشات الصغيرة */
        }

        .table-erp thead th {
            background-color: #f1f5f9;
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
            border-bottom: 1px solid #f8fafc;
            padding: 14px;
            text-align: center;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* تنسيق المبالغ (ديناميكي بناء على النوع) */
        .val-amount-receipt {
            color: #10b981;
            font-weight: 800;
            font-size: 16px;
            font-family: 'Courier New', Courier, monospace;
            background: #ecfdf5;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid #a7f3d0;
        }

        .val-amount-payment {
            color: #ef4444;
            font-weight: 800;
            font-size: 16px;
            font-family: 'Courier New', Courier, monospace;
            background: #fef2f2;
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid #fecaca;
        }

        .receipt-badge {
            background-color: #1e293b;
            color: #fff;
            font-family: monospace;
            padding: 4px 10px;
            border-radius: 6px;
            letter-spacing: 1px;
        }

        /* أزرار الإجراءات */
        .action-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .action-icon.print {
            background: #eff6ff;
            color: #3b82f6;
        }

        .action-icon.print:hover {
            background: #3b82f6;
            color: #fff;
            border-color: #2563eb;
        }

        .action-icon.delete {
            background: #fef2f2;
            color: #ef4444;
        }

        .action-icon.delete:hover {
            background: #ef4444;
            color: #fff;
            border-color: #dc2626;
        }

        /* نوافذ الـ Modal */
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

        .modal-footer-clean {
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
            border-radius: 0 0 12px 12px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px dashed #e2e8f0;
        }

        /* Dark Mode Adjustments */
        body[data-layout-color="dark"] {
            background-color: #2b333e;
        }

        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
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

        body[data-layout-color="dark"] .filter-input {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .modal-header-clean,
        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .modal-title-clean {
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🔥 الهيدر المنظم مع زر إضافة الدفعة 🔥 --}}
        <div class="card card-premium mb-4">
            <div class="card-body d-flex justify-content-between align-items-center p-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary shadow-sm">
                        <i class="mdi mdi-bank-transfer fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">سجل السندات (قبض وصرف)</h4>
                        <p class="text-muted mb-0 font-13">إدارة كافة المبالغ المحصلة من أولياء الأمور أو المدفوعة للموردين.
                        </p>
                    </div>
                </div>

                {{-- 🔥 حماية أزرار الإنشاء بصلاحية الإضافة 🔥 --}}
                @can('create_payments')
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-modern btn-receipt shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#addVoucherModal" onclick="setVoucherMode('receipt')">
                            <i class="mdi mdi-cash-plus font-18"></i> إنشاء سند قبض
                        </button>
                        <button type="button" class="btn btn-modern btn-payment shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#addVoucherModal" onclick="setVoucherMode('payment')">
                            <i class="mdi mdi-cash-minus font-18"></i> إنشاء سند صرف
                        </button>
                    </div>
                @endcan
            </div>
        </div>

        {{-- 🔥 فلاتر البحث العصرية 🔥 --}}
        <div class="card card-premium mb-4 shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('admin.payments.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="filter-label">نوع السند</label>
                            <select name="type" class="form-select filter-input fw-bold">
                                <option value="">الكل</option>
                                <option value="receipt" {{ request('type') == 'receipt' ? 'selected' : '' }}>سندات قبض
                                    (إيراد)</option>
                                <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>سندات صرف
                                    (مصروف)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="filter-label">حساب الخزنة/البنك</label>
                            <select name="account_id" class="form-select filter-input fw-bold">
                                <option value="">الكل</option>
                                @foreach ($financial_accounts ?? [] as $acc)
                                    <option value="{{ $acc->id }}"
                                        {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                        {{ $acc->name_ar ?? $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="filter-label">بحث حر</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="mdi mdi-magnify text-muted"></i></span>
                                <input type="text" name="search"
                                    class="form-control filter-input border-start-0 ps-0 fw-bold"
                                    placeholder="رقم السند، البيان..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-3 fw-bold shadow-sm">بحث</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🔥 جدول المدفوعات 🔥 --}}
        <div class="card card-premium overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-erp table-hover table-nowrap align-middle mb-0 text-center">
                        <thead>
                            <tr>
                                <th>رقم السند</th>
                                <th>النوع</th>
                                <th class="text-start">الجهة (ولي أمر / مورد / قسط)</th>
                                <th>حساب الإيداع/الصرف</th>
                                <th>التفاصيل / البيان</th>
                                <th>التاريخ</th>
                                <th>المبلغ</th>
                                <th>المستخدم</th>
                                {{-- 🔥 إخفاء عمود الإجراءات بالكامل لو مفيش أي صلاحية 🔥 --}}
                                @canany(['print_payments', 'delete_payments'])
                                    <th>إجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments ?? [] as $index => $payment)
                                @php
                                    $entity = $payment->payable;
                                    $entityName = 'غير محدد';
                                    $entityIcon = 'mdi-help-circle text-muted';
                                    $badgeClass = 'bg-secondary bg-opacity-10 text-secondary';
                                    $badgeText = 'سند عام';
                                    $amountClass = 'text-dark font-bold';
                                    $amountSign = '';

                                    // تحديد نوع السند
                                    if (
                                        $payment->payment_type == 'installment' ||
                                        get_class($entity) == 'App\Models\StudentInstallment'
                                    ) {
                                        $studentName = $entity->contract->student->first_name ?? 'طالب غير محدد';
                                        $entityName = 'قسط: ' . $studentName;
                                        $entityIcon = 'mdi-school text-primary';
                                        $badgeClass = 'bg-primary bg-opacity-10 text-primary';
                                        $badgeText = 'سند قبض (قسط)';
                                        $amountClass = 'val-amount-receipt';
                                        $amountSign = '+';
                                    } elseif (get_class($entity) == 'App\Models\Guardian') {
                                        $entityName = $entity->name_ar ?? ($entity->name ?? 'ولي أمر غير محدد');
                                        $entityIcon = 'mdi-account-tie text-success';
                                        $badgeClass = 'bg-success bg-opacity-10 text-success';
                                        $badgeText = 'سند قبض (عام)';
                                        $amountClass = 'val-amount-receipt';
                                        $amountSign = '+';
                                    } elseif (get_class($entity) == 'App\Models\Supplier') {
                                        $entityName = $entity->company_name ?? 'مورد غير محدد';
                                        $entityIcon = 'mdi-truck-delivery text-danger';
                                        $badgeClass = 'bg-danger bg-opacity-10 text-danger';
                                        $badgeText = 'سند صرف';
                                        $amountClass = 'val-amount-payment';
                                        $amountSign = '-';
                                    }
                                @endphp
                                <tr>
                                    <td dir="ltr"><span class="receipt-badge">{{ $payment->receipt_number }}</span>
                                    </td>
                                    <td><span class="badge {{ $badgeClass }} border px-2 py-1">{{ $badgeText }}</span>
                                    </td>
                                    <td class="text-start fw-bold text-dark">
                                        <i class="mdi {{ $entityIcon }} me-1 font-18 align-middle"></i>
                                        {{ $entityName }}
                                    </td>
                                    <td>
                                        <span class="badge bg-dark bg-opacity-10 text-dark border border-dark px-2 py-1">
                                            <i
                                                class="mdi mdi-bank me-1"></i>{{ $payment->financialAccount->name_ar ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-wrap text-start" style="max-width: 200px;">
                                        <span class="text-truncate d-inline-block w-100 fw-bold text-secondary"
                                            title="{{ $payment->transaction_details }}">
                                            {{ $payment->transaction_details }}
                                        </span>
                                    </td>
                                    <td dir="ltr" class="text-muted font-monospace">
                                        <i
                                            class="mdi mdi-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}
                                    </td>
                                    <td dir="ltr"><span class="{{ $amountClass }}">{{ $amountSign }}
                                            {{ number_format($payment->amount, 3) }}</span></td>
                                    <td>
                                        <small class="text-muted border bg-light px-2 py-1 rounded">
                                            <i
                                                class="mdi mdi-shield-account me-1"></i>{{ $payment->user->name ?? 'Admin' }}
                                        </small>
                                    </td>
                                    @canany(['print_payments', 'delete_payments'])
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                @can('print_payments')
                                                    <a href="{{ route('admin.payments.print', $payment->id) }}" target="_blank"
                                                        rel="noopener noreferrer" class="action-icon print" title="طباعة السند">
                                                        <i class="mdi mdi-printer font-18"></i>
                                                    </a>
                                                @endcan
                                                @can('delete_payments')
                                                    <form action="{{ route('admin.payments.destroy', $payment->id) }}"
                                                        method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="action-icon delete btn-delete"
                                                            title="حذف السند">
                                                            <i class="mdi mdi-trash-can-outline font-18"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-muted py-5 text-center fs-5 fw-bold">
                                        <i
                                            class="mdi mdi-inbox-remove-outline fs-1 d-block mb-2 text-black-50 opacity-25"></i>
                                        لا توجد سندات مسجلة في النظام.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 الترقيم (Pagination) 🔥 --}}
            @if (isset($payments) && $payments->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-3">
                    {{ $payments->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- =============================================== --}}
    {{-- 🔥 نافذة إضافة سند (قبض أو صرف) محمية 🔥 --}}
    {{-- =============================================== --}}
    @can('create_payments')
        <div class="modal fade" id="addVoucherModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header modal-header-clean bg-light">
                        <h5 class="modal-title modal-title-clean" id="voucherModalTitle"><i
                                class="mdi mdi-cash-register me-2"></i> إنشاء سند مالي جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    {{-- 🔥 حماية الفورم ضد الإرسال المزدوج 🔥 --}}
                    <form action="{{ route('admin.payments.store') }}" method="POST" id="voucherForm"
                        class="prevent-double-submit">
                        @csrf
                        <input type="hidden" name="voucher_type" id="input_voucher_type" value="receipt">
                        <input type="hidden" name="payable_id" id="input_payable_id" required>

                        <div class="modal-body p-4 bg-white text-start">
                            <div class="section-title" id="section_title_entity"><i class="mdi mdi-account-group"></i> بيانات
                                الجهة</div>
                            <div class="row g-3 mb-4">
                                {{-- حقل أولياء الأمور (يظهر في القبض) --}}
                                <div class="col-md-12" id="guardian_select_div">
                                    <label class="form-label text-success fw-bold">استلام من (ولي الأمر) *</label>
                                    <select class="form-select select2-init" id="guardian_select" style="width: 100%;">
                                        <option value="" disabled selected>-- ابحث باسم ولي الأمر --</option>
                                        @foreach ($guardians ?? [] as $guardian)
                                            <option value="{{ $guardian->id }}">
                                                {{ $guardian->name_ar ?? ($guardian->name ?? 'بدون اسم') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- حقل الموردين (يظهر في الصرف) --}}
                                <div class="col-md-12" id="supplier_select_div" style="display: none;">
                                    <label class="form-label text-danger fw-bold">دفع إلى (المورد / التاجر) *</label>
                                    <select class="form-select select2-init" id="supplier_select" style="width: 100%;">
                                        <option value="" disabled selected>-- ابحث باسم المورد --</option>
                                        @foreach ($suppliers ?? [] as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="section-title"><i class="mdi mdi-bank"></i> بيانات المعاملة المالية</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold" id="label_amount">المبلغ *</label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-light border-0"><i
                                                class="mdi mdi-cash text-muted"></i></span>
                                        <input type="number" step="0.001" name="amount"
                                            class="form-control text-center fw-bold fs-5 font-monospace border-0 bg-light"
                                            placeholder="0.000" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted fw-bold">تاريخ السند *</label>
                                    <input type="date" name="payment_date"
                                        class="form-control text-center fw-bold shadow-sm bg-light border-0"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted fw-bold">حساب الخزنة/البنك *</label>
                                    <select name="financial_account_id"
                                        class="form-select fw-bold shadow-sm bg-light border-0" required>
                                        <option value="" disabled selected>-- اختر الحساب --</option>
                                        @foreach ($financial_accounts ?? [] as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name_ar ?? $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted">رقم المرجع (شيك/حوالة)</label>
                                    <input type="text" name="check_number"
                                        class="form-control text-center font-monospace shadow-sm border-0 bg-light"
                                        placeholder="اختياري...">
                                </div>
                            </div>

                            <div class="section-title"><i class="mdi mdi-text-box-edit-outline"></i> البيان والملاحظات</div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">البيان (يظهر في السند المطبوع) *</label>
                                    <input type="text" name="transaction_details"
                                        class="form-control shadow-sm border-0 bg-light fw-bold" placeholder="مثال: دفعة..."
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">ملاحظات داخلية</label>
                                    <input type="text" name="notes" class="form-control shadow-sm border-0 bg-light"
                                        placeholder="اختياري...">
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer modal-footer-clean pt-3 pb-3">
                            <button type="button" class="btn btn-light border px-4 fw-bold rounded-pill shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit"
                                class="btn btn-primary-modern px-5 fw-bold rounded-pill shadow-sm btn-submit"><i
                                    class="mdi mdi-content-save me-1"></i> حفظ وتوليد السند</button>
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
        // دالة تغيير شكل المودال بناءً على الزرار المختار
        function setVoucherMode(mode) {
            $('#input_voucher_type').val(mode);
            $('#input_payable_id').val(''); // تصفير الحقل المخفي
            $('.select2-init').val('').trigger('change'); // تصفير السليكتات

            if (mode === 'receipt') {
                $('#voucherModalTitle').html('<i class="mdi mdi-cash-plus text-success me-2"></i> إنشاء سند قبض (إيراد)');
                $('#guardian_select_div').show();
                $('#supplier_select_div').hide();
                $('#label_amount').html('المبلغ المحصل *').addClass('text-success').removeClass('text-danger');
                $('.btn-submit').removeClass('btn-payment').addClass('btn-receipt').html(
                    '<i class="mdi mdi-content-save me-1"></i> إصدار سند القبض');
            } else {
                $('#voucherModalTitle').html('<i class="mdi mdi-cash-minus text-danger me-2"></i> إنشاء سند صرف (مصروف)');
                $('#guardian_select_div').hide();
                $('#supplier_select_div').show();
                $('#label_amount').html('المبلغ المدفوع *').addClass('text-danger').removeClass('text-success');
                $('.btn-submit').removeClass('btn-receipt').addClass('btn-payment').html(
                    '<i class="mdi mdi-content-save me-1"></i> إصدار سند الصرف');
            }
        }

        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل Select2 داخل المودال
            $('#addVoucherModal').on('shown.bs.modal', function() {
                $('.select2-init').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#addVoucherModal')
                });
            });

            // تمرير الـ ID للنموذج المخفي بناءً على ما يختاره المستخدم
            $('#guardian_select, #supplier_select').on('change', function() {
                $('#input_payable_id').val($(this).val());
            });

            // تأمين الفورم ضد الإرسال המزدوج
            $('.prevent-double-submit').on('submit', function(e) {
                if (!$('#input_payable_id').val()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى تحديد الجهة (ولي الأمر أو المورد) قبل إرسال السند.'
                    });
                    return false;
                }
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري إصدار السند...');
            });

            // تأكيد الحذف
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "سيتم حذف السند وإلغاء تأثيره على الخزنة!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'نعم، احذف السند!',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // إشعارات SweetAlert2 
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
                    title: 'عفواً!',
                    text: "{{ session('error') }}"
                });
            @endif
        });
    </script>
@endpush
