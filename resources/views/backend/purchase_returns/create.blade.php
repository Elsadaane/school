@extends('backend.layouts.master')
@section('title', 'إنشاء مرتجع مشتريات')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        .form-section-title {
            font-size: 15px;
            font-weight: 800;
            color: #dc3545;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .invoice-table {
            white-space: nowrap;
        }

        .invoice-table th {
            background-color: #f8f9fa;
            font-size: 13px;
            font-weight: 800;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            text-align: center;
        }

        .invoice-table td {
            vertical-align: middle;
            padding: 10px 5px;
        }

        .invoice-table .form-control {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            text-align: center;
        }

        .defective-box {
            background-color: #fff3f3;
            border: 1px dashed #dc3545;
            padding: 10px;
            border-radius: 6px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }

        /* Dark Mode Adjustments */
        body[data-layout-color="dark"] .form-section-title {
            color: #ff5b5b;
            border-bottom-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
            border: 1px solid #424e5a !important;
            color: #fff;
        }

        body[data-layout-color="dark"] .invoice-table th {
            background-color: #323a46;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .defective-box {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: #ff5b5b;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h4 class="page-title font-bold mb-0 text-danger">
                <i class="mdi mdi-keyboard-return me-1 fs-4 align-middle"></i> إرجاع بضاعة للمورد (Purchase Return)
            </h4>
            <a href="{{ route('admin.purchase-returns.index') }}"
                class="btn btn-light rounded-pill shadow-sm border fw-bold px-4">
                <i class="mdi mdi-arrow-left me-1"></i> رجوع للقائمة
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-3 fw-bold">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 border-top border-danger border-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.purchase-returns.store') }}" method="POST" id="returnForm"
                class="prevent-double-submit">
                @csrf

                <h5 class="form-section-title"><i class="mdi mdi-file-document-outline me-1"></i> بيانات الفاتورة المرتجعة
                </h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold">المورد / التاجر <span
                                class="text-danger">*</span></label>
                        <select class="form-select select2-init bg-light" name="supplier_id" id="supplier_select" required
                            style="width: 100%;">
                            <option value="" disabled selected>-- اختر المورد أولاً --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold">الفاتورة الأصلية المرتبطة <span
                                class="text-danger">*</span></label>
                        <select class="form-select select2-init bg-light" name="purchase_invoice_id" id="invoice_select"
                            required style="width: 100%;" disabled>
                            <option value="" disabled selected>-- اختر الفاتورة --</option>
                            @foreach ($invoices as $invoice)
                                <option value="{{ $invoice->id }}" data-supplier="{{ $invoice->supplier_id }}">
                                    {{ $invoice->invoice_number }} - بتاريخ
                                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold">تاريخ المرتجع <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control bg-light text-center fw-bold" name="return_date"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-4 flex-wrap gap-2">
                    <h5 class="form-section-title mb-0 border-0"><i class="mdi mdi-package-variant-closed me-1"></i> البضاعة
                        المسترجعة (محملة آلياً من الفاتورة)</h5>
                </div>

                <div class="table-responsive mb-4" style="min-height: 250px; overflow-x: visible;">
                    <table class="table invoice-table table-nowrap" id="returnTable">
                        <thead>
                            <tr>
                                <th style="width: 25%;" class="text-start ps-3">المنتج المسترجع</th>
                                <th style="width: 15%;">سعر الشراء (OMR)</th>
                                <th style="width: 15%;">الكمية المرتجعة</th>
                                <th style="width: 15%;">إجمالي الصف</th>
                                <th style="width: 25%;">السبب / حالة التلف</th>
                                <th style="width: 5%;">إزالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="empty_state_row">
                                <td colspan="6" class="text-center text-muted py-5 fw-bold bg-light">
                                    <i class="mdi mdi-receipt-text-search-outline fs-1 d-block mb-2 opacity-50"></i>
                                    يرجى اختيار المورد ثم اختيار الفاتورة لعرض المنتجات هنا تلقائياً...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4 align-items-center bg-light p-3 rounded-4 border">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label text-muted fw-bold">ملاحظات على المرتجع</label>
                        <textarea class="form-control bg-white shadow-sm border-0" name="notes" rows="2"
                            placeholder="أية تفاصيل إضافية حول سبب الإرجاع..."></textarea>
                    </div>
                    <div class="col-md-6 text-md-end text-center">
                        <h5 class="text-muted mb-2 fw-bold">إجمالي قيمة المرتجع (تُخصم من رصيد المورد)</h5>
                        <h2
                            class="text-danger font-bold mb-0 font-monospace border border-danger d-inline-block px-4 py-2 rounded-3 bg-white shadow-sm">
                            <span id="display_grand_total">0.000</span> OMR
                        </h2>
                        <input type="hidden" name="total_return_amount" id="input_grand_total" value="0">
                    </div>
                </div>

                <div class="text-end mt-4 border-top pt-4">
                    <button type="submit"
                        class="btn btn-danger btn-lg rounded-pill px-5 fw-bold shadow-sm btn-submit w-100 w-md-auto"
                        disabled id="final_submit_btn">
                        <i class="mdi mdi-check-decagram-outline me-1"></i> حفظ المرتجع وخصم المخزون
                    </button>
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
        // 🌟 تجهيز داتا الفواتير بالمنتجات من السيرفر 🌟
        const allInvoices = @json($invoices);
        let rowIdx = 0;

        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // 1️⃣ فلترة الفواتير لما تختار المورد
            $('#supplier_select').on('change', function() {
                let supplierId = $(this).val();
                let invoiceSelect = $('#invoice_select');

                invoiceSelect.val('').trigger('change');
                invoiceSelect.prop('disabled', false);

                invoiceSelect.find('option').each(function() {
                    if ($(this).val() === '') return;
                    if ($(this).data('supplier') == supplierId) {
                        $(this).show();
                        $(this).prop('disabled', false);
                    } else {
                        $(this).hide();
                        $(this).prop('disabled', true);
                    }
                });

                // تفريغ الجدول لو غير المورد
                $('#returnTable tbody').html(
                    '<tr id="empty_state_row"><td colspan="6" class="text-center text-muted py-5 fw-bold bg-light"><i class="mdi mdi-receipt-text-search-outline fs-1 d-block mb-2 opacity-50"></i>يرجى اختيار المورد ثم اختيار الفاتورة لعرض المنتجات هنا تلقائياً...</td></tr>'
                );
                calculateGrandTotal();
            });

            // 2️⃣ السحر البرمجي: لما يختار الفاتورة، افرش المنتجات في الجدول!
            $('#invoice_select').on('change', function() {
                let invoiceId = $(this).val();
                if (!invoiceId) return;

                let selectedInvoice = allInvoices.find(inv => inv.id == invoiceId);
                let tbody = $('#returnTable tbody');

                tbody.empty(); // مسح رسالة "الجدول فارغ"
                rowIdx = 0;

                if (selectedInvoice && selectedInvoice.items && selectedInvoice.items.length > 0) {
                    selectedInvoice.items.forEach(item => {
                        let productName = item.product ? item.product.name_ar : 'منتج غير معروف';
                        let productSku = item.product && item.product.sku ? item.product.sku :
                            '---';
                        let maxQty = item.quantity; // الكمية اللي اشتراها بالظبط
                        let unitCost = item.unit_price || item.unit_cost ||
                            0; // حسب المسمى في الداتابيز

                        let tr = `
                            <tr id="row_${rowIdx}">
                                <td class="text-start ps-3">
                                    <input type="text" class="form-control bg-light fw-bold text-dark border-0 shadow-sm" value="${productName} (${productSku})" readonly>
                                    <input type="hidden" name="items[${rowIdx}][product_id]" value="${item.product_id}">
                                </td>
                                <td>
                                    <div class="input-group input-group-sm shadow-sm">
                                        <input type="number" step="0.001" class="form-control item-cost text-center bg-light border-0" name="items[${rowIdx}][unit_cost]" value="${unitCost}" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-center">
                                        <input type="number" step="1" class="form-control item-qty border-danger text-danger text-center shadow-sm" name="items[${rowIdx}][quantity]" value="${maxQty}" min="1" max="${maxQty}" required>
                                        <small class="text-muted font-11 mt-1 fw-bold">شراء: ${maxQty}</small>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" step="0.001" class="form-control item-total bg-light fw-bold text-danger border-0 text-center" name="items[${rowIdx}][row_total]" value="${(unitCost * maxQty).toFixed(3)}" readonly>
                                </td>
                                <td class="text-start">
                                    <div class="form-check form-switch mb-1 d-flex align-items-center">
                                        <input class="form-check-input defective-toggle ms-0 me-2 shadow-sm border-danger" type="checkbox" name="items[${rowIdx}][is_defective]" id="def_${rowIdx}" value="1">
                                        <label class="form-check-label font-13 fw-bold text-danger cursor-pointer ms-2" for="def_${rowIdx}">بضاعة تالفة؟</label>
                                    </div>
                                    <input type="text" class="form-control form-control-sm defective-reason shadow-sm mt-2 border-danger" name="items[${rowIdx}][return_reason]" placeholder="اكتب سبب التلف لخصمها من المخزن..." style="display: none;">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-soft-secondary rounded-circle shadow-sm remove-row" title="إزالة من قائمة المرتجع"><i class="mdi mdi-close-thick font-18"></i></button>
                                </td>
                            </tr>
                        `;
                        tbody.append(tr);
                        rowIdx++;
                    });

                    $('#final_submit_btn').prop('disabled', false);
                } else {
                    tbody.html(
                        '<tr id="empty_state_row"><td colspan="6" class="text-center text-danger py-5 fw-bold bg-light"><i class="mdi mdi-alert-circle-outline fs-1 d-block mb-2"></i>هذه الفاتورة لا تحتوي على أي منتجات يمكن إرجاعها!</td></tr>'
                    );
                    $('#final_submit_btn').prop('disabled', true);
                }

                calculateGrandTotal();
            });

            // 3️⃣ حذف سطر لو مش عايز يرجعه
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').fadeOut(300, function() {
                    $(this).remove();
                    calculateGrandTotal();

                    if ($('#returnTable tbody tr').length === 0) {
                        $('#returnTable tbody').html(
                            '<tr id="empty_state_row"><td colspan="6" class="text-center text-danger py-5 fw-bold bg-light"><i class="mdi mdi-cart-remove fs-1 d-block mb-2"></i>تم إزالة جميع المنتجات. يرجى إعادة اختيار الفاتورة.</td></tr>'
                        );
                        $('#final_submit_btn').prop('disabled', true);
                    }
                });
            });

            // 4️⃣ تحديث الإجمالي تلقائياً لما يغير الكمية
            $(document).on('input', '.item-qty', function() {
                let tr = $(this).closest('tr');
                let cost = parseFloat(tr.find('.item-cost').val()) || 0;
                let qty = parseFloat($(this).val()) || 0;

                // منع تجاوز الكمية المشتراة
                let maxQty = parseFloat($(this).attr('max'));
                if (qty > maxQty) {
                    $(this).val(maxQty);
                    qty = maxQty;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: 'warning',
                        title: 'لا يمكن إرجاع كمية أكبر من المشتراة!'
                    });
                }

                let rowTotal = cost * qty;
                tr.find('.item-total').val(rowTotal.toFixed(3));
                calculateGrandTotal();
            });

            // 5️⃣ إظهار سبب التلف
            $(document).on('change', '.defective-toggle', function() {
                let td = $(this).closest('td');
                let reasonInput = td.find('.defective-reason');

                if ($(this).is(':checked')) {
                    reasonInput.slideDown(200).prop('required', true);
                    td.addClass('defective-box shadow-sm');
                } else {
                    reasonInput.slideUp(200).val('').prop('required', false);
                    td.removeClass('defective-box shadow-sm');
                }
            });

            // 6️⃣ الحسابات الختامية
            function calculateGrandTotal() {
                let subTotal = 0;
                $('#returnTable tbody tr:not(#empty_state_row)').each(function() {
                    let rowTotal = parseFloat($(this).find('.item-total').val()) || 0;
                    subTotal += rowTotal;
                });

                $('#display_grand_total').text(subTotal.toFixed(3));
                $('#input_grand_total').val(subTotal.toFixed(3));
            }

            // منع الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function(e) {
                if ($('#returnTable tbody tr#empty_state_row').length > 0) {
                    e.preventDefault();
                    Swal.fire('خطأ!', 'لا يوجد منتجات لإرجاعها.', 'error');
                    return false;
                }

                let submitBtn = $(this).find('.btn-submit');
                submitBtn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري حفظ السند...');
            });
        });
    </script>
@endpush
