@extends('backend.layouts.master')
@section('title', 'إنشاء فاتورة مشتريات جديدة')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .wizard-nav {
            display: flex;
            justify-content: space-around;
            background: #fff;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #e9ecef;
            flex-wrap: wrap;
            gap: 10px;
        }

        .step-item {
            color: #98a6ad;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .step-item.active {
            color: #10c469;
            border-bottom: 2px solid #10c469;
            padding-bottom: 5px;
        }

        .step-item.passed {
            color: #5b69bc;
        }

        .step-number {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .form-section-title {
            font-size: 15px;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .invoice-table {
            white-space: nowrap;
        }

        .invoice-table th {
            background-color: #f8f9fa;
            font-size: 13px;
            font-weight: 700;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            text-align: center;
        }

        .invoice-table td {
            vertical-align: middle;
            padding: 10px 5px;
        }

        .invoice-table .form-control {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            font-size: 13px;
            text-align: center;
        }

        .totals-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 1px dashed #cbd5e1;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            align-items: center;
        }

        .grand-total-row {
            font-size: 18px;
            font-weight: 900;
            color: #10c469;
            border-top: 2px dashed #dee2e6;
            padding-top: 15px;
            margin-top: 15px;
        }

        .payment-option {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: 0.2s;
            text-align: center;
            background: #fff;
            height: 100%;
        }

        .payment-option:hover {
            border-color: #94a3b8;
        }

        .payment-option.selected {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        .payment-option i {
            font-size: 24px;
            display: block;
            margin-bottom: 5px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.4s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dark Mode */
        body[data-layout-color="dark"] .wizard-nav,
        body[data-layout-color="dark"] .payment-option {
            background: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .payment-option.selected {
            background-color: rgba(59, 130, 246, 0.1);
        }

        body[data-layout-color="dark"] .step-item.active {
            color: #10c469;
            border-color: #10c469;
        }

        body[data-layout-color="dark"] .form-section-title,
        body[data-layout-color="dark"] .total-row {
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .invoice-table th,
        body[data-layout-color="dark"] .totals-box {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #ced4da;
        }

        body[data-layout-color="dark"] .invoice-table .form-control,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection,
        body[data-layout-color="dark"] .bg-light {
            background-color: #323a46 !important;
            border-color: #424e5a !important;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold mb-1 text-success"><i class="mdi mdi-cart-arrow-down me-1"></i> فاتورة
                    مشتريات جديدة</h4>
                <p class="text-muted font-13 mb-0">تسجيل المنتجات والمشتريات وتحديث المخزون وحساب المورد.</p>
            </div>
            <a href="{{ route('admin.purchase-invoices.index') }}"
                class="btn btn-light rounded-pill shadow-sm border font-bold px-4" id="btnBack">
                <i class="mdi mdi-arrow-left me-1"></i> السجل والعودة
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3 fw-bold border-0">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 border-top border-success border-4">
        <div class="card-body p-4">

            {{-- شريط التوجيه (Wizard) --}}
            <div class="wizard-nav">
                <div class="step-item active" id="step1-nav"><span class="step-number">1</span> بيانات الفاتورة والمورد
                </div>
                <div class="step-item" id="step2-nav"><span class="step-number">2</span> إضافة المنتجات والتكاليف</div>
                <div class="step-item" id="step3-nav"><span class="step-number">3</span> طريقة الدفع والاعتماد</div>
            </div>

            <form action="{{ route('admin.purchase-invoices.store') }}" method="POST" id="purchaseForm"
                class="prevent-double-submit">
                @csrf

                {{-- ================= الخطوة 1 ================= --}}
                <div class="tab-pane active" id="step1">
                    <h5 class="form-section-title"><i class="mdi mdi-truck-delivery text-primary me-1"></i> التفاصيل
                        الأساسية</h5>
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-bold">الفرع (المخزن) <span
                                    class="text-danger">*</span></label>
                            <select class="form-select bg-light select2-init" name="branch_id" required>
                                <option value="" selected disabled>-- اختر الفرع --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ $selectedBranch == $branch->id ? 'selected' : '' }}>{{ $branch->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-muted fw-bold">المورد / التاجر <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2-init bg-light" name="supplier_id" required
                                style="width: 100%;">
                                <option value="" selected disabled>-- ابحث عن اسم المورد --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted fw-bold">تاريخ الفاتورة <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control bg-light text-center fw-bold" name="invoice_date"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted fw-bold">رقم فاتورة المورد</label>
                            <input type="text" class="form-control bg-light font-monospace fw-bold text-center"
                                name="supplier_invoice_number" placeholder="مثال: INV-998">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted fw-bold">ملاحظات على المشتريات</label>
                            <textarea class="form-control bg-light fw-bold" name="notes" rows="2"
                                placeholder="أية ملاحظات إضافية حول الاستلام..."></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-4 border-top pt-3">
                        <button type="button" class="btn btn-success px-5 rounded-pill btn-next fw-bold shadow-sm">المنتجات
                            والمبالغ <i class="mdi mdi-arrow-left ms-1"></i></button>
                    </div>
                </div>

                {{-- ================= الخطوة 2 ================= --}}
                <div class="tab-pane" id="step2">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <h5 class="form-section-title border-0 mb-0 pb-0"><i
                                class="mdi mdi-package-variant-closed text-primary me-1"></i> بنود المنتجات وتكاليف الشراء
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill font-bold px-3 shadow-sm"
                            id="addRowBtn"><i class="mdi mdi-plus me-1"></i> إضافة صنف آخر</button>
                    </div>

                    <div class="table-responsive mb-3" style="min-height: 250px;">
                        <table class="table invoice-table table-nowrap" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 35%;" class="text-start ps-3">الصنف / المنتج</th>
                                    <th style="width: 15%;">سعر الشراء (للوحدة)</th>
                                    <th style="width: 15%;">الكمية الواردة</th>
                                    <th style="width: 25%;">إجمالي الصف</th>
                                    <th style="width: 10%;">حذف</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-4 border-top pt-3">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold btn-prev"><i
                                class="mdi mdi-arrow-right me-1"></i> السابق</button>
                        <button type="button" class="btn btn-success px-5 rounded-pill btn-next fw-bold shadow-sm">طريقة
                            الدفع والاعتماد <i class="mdi mdi-arrow-left ms-1"></i></button>
                    </div>
                </div>

                {{-- ================= الخطوة 3 ================= --}}
                <div class="tab-pane" id="step3">
                    <h5 class="form-section-title"><i class="mdi mdi-cash-multiple text-primary me-1"></i> ملخص الفاتورة
                        وطريقة الدفع</h5>

                    <div class="row g-4">
                        <div class="col-md-5">
                            <div class="totals-box shadow-sm h-100">
                                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i
                                        class="mdi mdi-calculator"></i> حسابات الفاتورة</h6>
                                <div class="total-row">
                                    <span>إجمالي قيمة المنتجات:</span>
                                    <span><span id="display_sub_total" class="font-monospace fs-5 fw-bold">0.000</span>
                                        OMR</span>
                                    <input type="hidden" name="sub_total" id="input_sub_total" value="0">
                                </div>
                                <div class="total-row">
                                    <span class="text-danger">الخصم المكتسب (-):</span>
                                    <div class="input-group input-group-sm" style="width: 130px;">
                                        <input type="number" step="0.001"
                                            class="form-control text-center text-danger font-monospace fw-bold"
                                            name="discount" id="input_discount" value="0" min="0">
                                    </div>
                                </div>
                                <div class="total-row">
                                    <span class="text-warning text-dark">الضريبة المضافة (+):</span>
                                    <div class="input-group input-group-sm" style="width: 130px;">
                                        <input type="number" step="0.001"
                                            class="form-control text-center text-warning text-dark font-monospace fw-bold"
                                            name="tax_amount" id="input_tax_amount" value="0" min="0">
                                    </div>
                                </div>
                                <div class="grand-total-row d-flex justify-content-between">
                                    <span>الإجمالي المطلوب:</span>
                                    <span><span id="display_grand_total" class="font-monospace">0.000</span> OMR</span>
                                    <input type="hidden" name="grand_total" id="input_grand_total" value="0">
                                </div>

                                <div class="total-row mt-3 pt-3 border-top border-primary">
                                    <span class="text-primary font-bold">الدفعة المقدمة / المسدد:</span>
                                    <div class="input-group" style="width: 150px;">
                                        <input type="number" step="0.001"
                                            class="form-control text-center border-primary font-bold text-primary font-monospace fs-5"
                                            name="paid_amount" id="input_paid_amount" value="0" min="0">
                                    </div>
                                </div>

                                {{-- 🔥 التحديث السحري: خزينة الدفع تظهر فقط عند دفع مبالغ 🔥 --}}
                                <div class="total-row mt-3 d-none p-2 rounded" id="treasury_section"
                                    style="background-color: #e0f2fe; border: 1px dashed #3b82f6;">
                                    <span class="text-primary font-bold"><i class="mdi mdi-safe"></i> سحب الدفعة
                                        من:</span>
                                    <div style="width: 180px;">
                                        <select name="financial_account_id" id="financial_account_id"
                                            class="form-select form-select-sm border-primary fw-bold text-dark">
                                            <option value="" disabled selected>-- اختر الخزينة / البنك --</option>
                                            @if (isset($accounts))
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}">{{ $account->name }}
                                                        ({{ number_format($account->balance, 3) }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="total-row mt-3 text-danger font-bold">
                                    <span>المتبقي في ذمتنا للمورد:</span>
                                    <span><span id="display_due_amount" class="font-monospace fs-5">0.000</span>
                                        OMR</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <h6 class="fw-bold text-dark mb-3"><i class="mdi mdi-help-circle-outline"></i> كيف سيتم سداد
                                المتبقي؟</h6>
                            {{-- الافتراضي كاش --}}
                            <input type="hidden" name="payment_type" id="payment_type_input" value="cash">

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="payment-option selected shadow-sm" data-type="cash">
                                        <i class="mdi mdi-cash text-success"></i> كاش / دفع فوري
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="payment-option shadow-sm" data-type="auto_installment">
                                        <i class="mdi mdi-calendar-clock text-primary"></i> تقسيط شهري آلي
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="payment-option shadow-sm" data-type="manual_installment">
                                        <i class="mdi mdi-clipboard-text-clock-outline text-warning text-dark"></i> دفعات
                                        مخصصة (يدوي)
                                    </div>
                                </div>
                            </div>

                            <div id="auto_installment_settings" class="bg-light p-3 rounded-4 border d-none shadow-sm">
                                <h6 class="text-primary fw-bold mb-3"><i class="mdi mdi-cogs me-1"></i> إعدادات التقسيط
                                    الآلي</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label text-muted font-12 fw-bold">عدد الشهور (الأقساط)</label>
                                        <input type="number" name="months_count" id="months_count"
                                            class="form-control text-center fw-bold font-monospace fs-5 border-primary"
                                            value="1" min="1">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted font-12 fw-bold">تاريخ استحقاق أول قسط</label>
                                        <input type="date" name="start_date"
                                            class="form-control text-center fw-bold border-primary"
                                            value="{{ date('Y-m-d', strtotime('+1 month')) }}">
                                    </div>
                                </div>
                                <div class="alert alert-info mt-3 mb-0 py-2 font-13 fw-bold text-center border-0 shadow-sm"
                                    id="auto_preview_msg">
                                    سيتم تقسيط المتبقي آلياً بالتساوي.
                                </div>
                            </div>

                            <div id="manual_installment_settings" class="bg-light p-3 rounded-4 border d-none shadow-sm">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                    <h6 class="text-warning text-dark fw-bold mb-0"><i
                                            class="mdi mdi-hand-extended me-1"></i> الدفعات المجدولة يدوياً</h6>
                                    <button type="button"
                                        class="btn btn-sm btn-warning text-dark font-bold py-1 px-3 rounded-pill shadow-sm"
                                        id="addManualInstBtn">
                                        <i class="mdi mdi-plus me-1"></i> إضافة دفعة
                                    </button>
                                </div>
                                <div id="manual_installments_container"></div>
                                <div class="alert alert-danger mt-2 mb-0 py-2 font-12 fw-bold text-center d-none border-0 shadow-sm"
                                    id="manual_diff_msg"></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between border-top mt-4 pt-4">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold btn-prev"><i
                                class="mdi mdi-arrow-right me-1"></i> السابق</button>
                        <button type="submit"
                            class="btn btn-success btn-lg px-5 rounded-pill font-bold shadow-sm btn-submit"
                            id="final_submit_btn">
                            <i class="mdi mdi-check-decagram me-1"></i> اعتماد الفاتورة وتحديث المخزن
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? "rtl" : "ltr"
            });

            // التنقل بين الخطوات
            $('.btn-next').off('click').on('click', function(e) {
                e.preventDefault();
                let currentTabBtn = $('.wizard-nav div.active');

                if (currentTabBtn.attr('id') === 'step1-nav') {
                    if (!$('select[name="branch_id"]').val() || !$('select[name="supplier_id"]').val()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'بيانات ناقصة',
                            text: 'يرجى اختيار الفرع والمورد أولاً.',
                            confirmButtonColor: '#1e2b3c'
                        });
                        return;
                    }
                }

                if (currentTabBtn.attr('id') === 'step2-nav') {
                    if ($('#itemsTable tbody tr').length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'فاتورة فارغة',
                            text: 'يرجى إضافة منتج واحد على الأقل للفاتورة.',
                            confirmButtonColor: '#1e2b3c'
                        });
                        return;
                    }
                    let hasEmptyItems = false;
                    $('.item-select').each(function() {
                        if (!$(this).val()) hasEmptyItems = true;
                    });
                    if (hasEmptyItems) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'بيانات ناقصة',
                            text: 'يوجد سطر بدون تحديد المنتج.',
                            confirmButtonColor: '#1e2b3c'
                        });
                        return;
                    }
                }

                let nextTabBtn = currentTabBtn.next('.step-item');
                if (nextTabBtn.length > 0) {
                    currentTabBtn.addClass('passed');
                    let targetPane = nextTabBtn.attr('id').replace('-nav', '');
                    $('.tab-pane').removeClass('show active');
                    $('#' + targetPane).addClass('show active');
                    currentTabBtn.removeClass('active');
                    nextTabBtn.addClass('active');
                }
            });

            $('.btn-prev').off('click').on('click', function(e) {
                e.preventDefault();
                let currentTabBtn = $('.wizard-nav div.active');
                let prevTabBtn = currentTabBtn.prev('.step-item');
                if (prevTabBtn.length > 0) {
                    currentTabBtn.removeClass('passed');
                    let targetPane = prevTabBtn.attr('id').replace('-nav', '');
                    $('.tab-pane').removeClass('show active');
                    $('#' + targetPane).addClass('show active');
                    currentTabBtn.removeClass('active');
                    prevTabBtn.addClass('active').removeClass('passed');
                }
            });

            // المنتجات
            let products = @json($products);
            let rowIdx = 0;

            function addRow() {
                let options = '<option value="" disabled selected>ابحث عن الصنف بالاسم أو الباركود...</option>';
                products.forEach(p => {
                    options +=
                        `<option value="${p.id}" data-cost="${p.cost_price ?? 0}">${p.name_ar} (${p.sku || 'بدون كود'})</option>`;
                });

                let tr = `
                    <tr id="row_${rowIdx}">
                        <td class="text-start ps-3"><select class="form-select item-select select2-dynamic border-primary" name="items[${rowIdx}][product_id]" style="width:100%; text-align:right;" required>${options}</select></td>
                        <td><input type="number" step="0.001" class="form-control item-cost text-primary fw-bold font-monospace text-center" name="items[${rowIdx}][unit_cost]" value="0" required></td>
                        <td><input type="number" class="form-control item-qty fw-bold text-center font-monospace" name="items[${rowIdx}][quantity]" value="1" min="1" required></td>
                        <td><input type="number" step="0.001" class="form-control item-total bg-light fw-bolder text-success border-0 font-monospace text-center" name="items[${rowIdx}][row_total]" value="0" readonly></td>
                        <td class="text-center"><button type="button" class="btn btn-sm text-danger border-0 bg-transparent remove-row shadow-none"><i class="mdi mdi-trash-can-outline font-20"></i></button></td>
                    </tr>`;

                $('#itemsTable tbody').append(tr);
                $('#row_' + rowIdx + ' .select2-dynamic').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? "rtl" : "ltr"
                });
                rowIdx++;
                calculateGrandTotal();
            }

            addRow();
            $('#addRowBtn').click(addRow);

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                calculateGrandTotal();
            });

            $(document).on('change', '.item-select', function() {
                let currentSelect = $(this);
                let selectedVal = currentSelect.val();
                let isDuplicate = false;
                $('.item-select').not(currentSelect).each(function() {
                    if ($(this).val() == selectedVal && selectedVal != null) isDuplicate = true;
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'صنف مكرر!',
                        text: 'هذا الصنف مدرج بالفعل.',
                        confirmButtonColor: '#f59e0b'
                    });
                    currentSelect.val('').trigger('change');
                    return false;
                }

                let cost = currentSelect.find(':selected').data('cost');
                currentSelect.closest('tr').find('.item-cost').val(cost);
                calculateRow(currentSelect.closest('tr'));
            });

            $(document).on('input', '.item-cost, .item-qty', function() {
                calculateRow($(this).closest('tr'));
            });
            $('#input_discount, #input_tax_amount, #input_paid_amount').on('input', function() {
                // منع التعديل اليدوي على المدفوع لو كان الدفع كاش
                if ($('#payment_type_input').val() === 'cash') {
                    calculateGrandTotal(); // سيعيد ضبط القيمة للإجمالي تلقائياً
                } else {
                    calculateGrandTotal();
                }
            });
            $('#months_count').on('input', calculateGrandTotal);

            function calculateRow(tr) {
                let cost = parseFloat(tr.find('.item-cost').val()) || 0;
                let qty = parseFloat(tr.find('.item-qty').val()) || 0;
                tr.find('.item-total').val((cost * qty).toFixed(3));
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let subTotal = 0;
                $('#itemsTable tbody tr').each(function() {
                    subTotal += parseFloat($(this).find('.item-total').val()) || 0;
                });

                let discount = parseFloat($('#input_discount').val()) || 0;
                let tax = parseFloat($('#input_tax_amount').val()) || 0;
                let grandTotal = (subTotal - discount) + tax;
                if (grandTotal < 0) grandTotal = 0;

                let paymentType = $('#payment_type_input').val();
                let paidInput = $('#input_paid_amount');
                let paid = 0;

                // 🔥 سحر الكاش: لو الدفع كاش، المدفوع يساوى الإجمالي ويتقفل التعديل فيه 🔥
                if (paymentType === 'cash') {
                    paid = grandTotal;
                    paidInput.val(grandTotal.toFixed(3));
                    paidInput.prop('readonly', true);
                    paidInput.removeClass('border-primary').addClass('border-success text-success bg-light');
                } else {
                    paidInput.prop('readonly', false);
                    paidInput.removeClass('border-success text-success bg-light').addClass('border-primary');
                    paid = parseFloat(paidInput.val()) || 0;
                    if (paid > grandTotal) {
                        paid = grandTotal;
                        paidInput.val(paid.toFixed(3));
                    }
                }

                let due = grandTotal - paid;

                $('#display_sub_total').text(subTotal.toFixed(3));
                $('#input_sub_total').val(subTotal.toFixed(3));
                $('#display_grand_total').text(grandTotal.toFixed(3));
                $('#input_grand_total').val(grandTotal.toFixed(3));
                $('#display_due_amount').text(due.toFixed(3));

                // 🌟 إظهار أو إخفاء حقل الخزينة 🌟
                if (paid > 0) {
                    $('#treasury_section').removeClass('d-none');
                    $('#financial_account_id').prop('required', true);
                } else {
                    $('#treasury_section').addClass('d-none');
                    $('#financial_account_id').prop('required', false);
                }

                let months = parseInt($('#months_count').val()) || 1;
                if (due > 0 && months > 0) {
                    $('#auto_preview_msg').html(
                        `<i class="mdi mdi-information-outline me-1"></i> سيتم تقسيط مبلغ <b>${due.toFixed(3)}</b> على <b>${months}</b> شهور. قيمة القسط: <b>${(due/months).toFixed(3)} OMR</b>`
                    );
                }
                validateManualInstallments(due);
            }

            // خيارات الدفع
            $('.payment-option').click(function() {
                $('.payment-option').removeClass('selected shadow-sm').addClass('shadow-none');
                $(this).addClass('selected shadow-sm').removeClass('shadow-none');

                let type = $(this).data('type');
                $('#payment_type_input').val(type);

                $('#auto_installment_settings, #manual_installment_settings').addClass('d-none');
                $('#auto_installment_settings input, #manual_installment_settings input').prop('disabled',
                    true);

                if (type === 'auto_installment') {
                    $('#auto_installment_settings').removeClass('d-none');
                    $('#auto_installment_settings input').prop('disabled', false);
                }
                if (type === 'manual_installment') {
                    $('#manual_installment_settings').removeClass('d-none');
                    $('#manual_installment_settings input').prop('disabled', false);
                }

                // 🌟 هنا السر: أول ما يغير طريقة الدفع، نعمل Recalculate 🌟
                if (type !== 'cash') {
                    $('#input_paid_amount').val(0); // تصفير المدفوع لو اختار آجل
                }
                calculateGrandTotal();
            });

            // الدفعات اليدوية
            let manualIdx = 0;
            $('#addManualInstBtn').click(function() {
                let isDisabled = $('#manual_installment_settings').hasClass('d-none') ? 'disabled' : '';
                let html = `
                <div class="row g-2 mb-2 manual-row">
                    <div class="col-5">
                        <input type="number" step="0.001" name="manual_installments[${manualIdx}][amount]" class="form-control text-center font-monospace fw-bold manual-amount-input border-warning" placeholder="المبلغ (OMR)" required ${isDisabled}>
                    </div>
                    <div class="col-6">
                        <input type="date" name="manual_installments[${manualIdx}][date]" class="form-control text-center fw-bold" required ${isDisabled}>
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-outline-danger w-100 py-1 px-0 remove-manual-btn shadow-sm" title="حذف القسط"><i class="mdi mdi-close"></i></button>
                    </div>
                </div>`;
                $('#manual_installments_container').append(html);
                manualIdx++;
            });

            $(document).on('click', '.remove-manual-btn', function() {
                $(this).closest('.manual-row').remove();
                calculateGrandTotal();
            });
            $(document).on('input', '.manual-amount-input', function() {
                calculateGrandTotal();
            });

            $('#addManualInstBtn').click();
            $('#addManualInstBtn').click();

            // Validation
            function validateManualInstallments(dueAmount) {
                if ($('#payment_type_input').val() === 'manual_installment') {
                    let totalManual = 0;
                    $('.manual-amount-input').each(function() {
                        totalManual += parseFloat($(this).val()) || 0;
                    });
                    let diff = dueAmount - totalManual;
                    let msgBox = $('#manual_diff_msg');

                    if (Math.abs(diff) > 0.01) {
                        msgBox.html(
                            `<i class="mdi mdi-alert-octagon me-1"></i> مجموع الأقساط (${totalManual.toFixed(3)}) لا يساوي المبلغ المتبقي (${dueAmount.toFixed(3)}). الفرق: ${diff.toFixed(3)}`
                        ).removeClass('d-none');
                        return false;
                    } else {
                        msgBox.addClass('d-none');
                        return true;
                    }
                }
                return true;
            }

            $('#btnBack').click(function(e) {
                if ($('#itemsTable tbody tr').length > 0 && $('#itemsTable tbody tr:first .item-cost')
                    .val() > 0) {
                    e.preventDefault();
                    let url = $(this).attr('href');
                    Swal.fire({
                        title: 'تأكيد المغادرة؟',
                        text: 'لديك بيانات فاتورة غير محفوظة.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'نعم',
                        cancelButtonText: 'تراجع'
                    }).then((result) => {
                        if (result.isConfirmed) window.location.href = url;
                    });
                }
            });

            // تأمين الفورم
            $('.prevent-double-submit').on('submit', function(e) {
                let due = parseFloat($('#display_due_amount').text());

                // تحقق الخزنة
                let paid = parseFloat($('#input_paid_amount').val()) || 0;
                if (paid > 0 && !$('#financial_account_id').val()) {
                    e.preventDefault();
                    Swal.fire('بيانات ناقصة', 'يجب تحديد خزينة الدفع لسحب الدفعة النقدية منها.', 'error');
                    return false;
                }

                if (!validateManualInstallments(due)) {
                    e.preventDefault();
                    Swal.fire('خطأ محاسبي', 'مجموع الأقساط اليدوية المجدولة لا يطابق المبلغ المتبقي!',
                        'error');
                    return false;
                }

                let submitBtn = $(this).find('.btn-submit');
                submitBtn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري الحفظ...');
            });

            // تهيئة مبدئية
            calculateGrandTotal();
        });
    </script>
@endpush
