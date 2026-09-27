@extends('backend.layouts.master')
@section('title', 'سجل فواتير المشتريات')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .filter-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: none;
            border-top: 4px solid #10c469;
        }

        .table-custom {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-custom th {
            font-weight: 800;
            color: #475569;
            font-size: 13px;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px 12px;
            text-align: center;
        }

        .table-custom td {
            vertical-align: middle;
            padding: 14px 12px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            transition: 0.2s;
        }

        .table-custom tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* 🎨 Financial Columns Colors 🎨 */
        .col-total {
            background-color: #eff6ff !important;
            color: #1e40af !important;
            font-weight: 800;
            font-family: 'Courier New', Courier, monospace;
        }

        .col-paid {
            background-color: #f0fdf4 !important;
            color: #166534 !important;
            font-weight: 800;
            font-family: 'Courier New', Courier, monospace;
        }

        .col-due {
            background-color: #fef2f2 !important;
            color: #991b1b !important;
            font-weight: 800;
            font-family: 'Courier New', Courier, monospace;
        }

        .font-monospace-custom {
            font-family: 'Courier New', Courier, monospace !important;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        /* Modal Adjustments */
        .modal-header-custom {
            background: linear-gradient(135deg, #10c469 0%, #089b50 100%);
            color: white;
            border-radius: 12px 12px 0 0;
        }

        /* 🌟 Pagination Premium 🌟 */
        .pagination {
            margin-bottom: 0;
            justify-content: center;
            gap: 5px;
        }

        .page-item.active .page-link {
            background-color: #10c469;
            border-color: #10c469;
            color: #fff;
            box-shadow: 0 4px 10px rgba(16, 196, 105, 0.2);
        }

        .page-item .page-link {
            color: #475569;
            font-weight: 700;
            border-radius: 8px !important;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            transition: all 0.2s;
        }

        .page-item .page-link:hover {
            background-color: #f1f5f9;
            color: #10c469;
            transform: translateY(-2px);
        }

        .page-item.disabled .page-link {
            background-color: #f8fafc;
            color: #cbd5e1;
            border-color: #e2e8f0;
        }

        /* Dark Mode Adjustments */
        body[data-layout-color="dark"] .filter-card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom th {
            color: #ced4da;
            background-color: #3b4652 !important;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .table-custom tbody tr:hover td {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .col-total {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #93c5fd !important;
        }

        body[data-layout-color="dark"] .col-paid {
            background-color: rgba(16, 196, 105, 0.15) !important;
            color: #10c469 !important;
        }

        body[data-layout-color="dark"] .col-due {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #fca5a5 !important;
        }

        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            border-color: #424e5a;
            color: #fff;
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
    <div class="row mb-3 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold mb-0 text-dark">
                    <i class="mdi mdi-receipt text-success me-1 fs-3 align-middle"></i> سجل فواتير المشتريات
                </h4>
                <p class="text-muted font-13 mb-0 mt-1">إدارة جميع الفواتير الصادرة وسداد الدفعات وتتبع المرتجعات.</p>
            </div>

            @can('create_purchase_invoices')
                <a href="{{ route('admin.purchase-invoices.create') }}"
                    class="btn btn-success fw-bold shadow-sm rounded-pill px-4 py-2">
                    <i class="mdi mdi-plus-circle-outline me-1 fs-5 align-middle"></i> إنشاء فاتورة جديدة
                </a>
            @endcan
        </div>
    </div>

    {{-- الفلاتر --}}
    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.purchase-invoices.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label font-13 fw-bold text-muted mb-2"><i
                                class="mdi mdi-magnify text-success"></i> بحث متقدم</label>
                        <div class="input-group shadow-sm rounded-3">
                            <span class="input-group-text bg-light border-end-0"><i
                                    class="mdi mdi-text-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0 fw-bold py-2"
                                placeholder="رقم الفاتورة، اسم المورد..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label font-13 fw-bold text-muted mb-2"><i
                                class="mdi mdi-check-circle-outline text-success"></i> حالة السداد</label>
                        <select name="status" class="form-select shadow-sm fw-bold py-2">
                            <option value="">-- كل الحالات --</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>خالصة (مسددة بالكامل)
                            </option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>سداد جزئي
                            </option>
                            <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>آجلة (مستحقة
                                بالكامل)</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark shadow-sm w-100 fw-bold py-2"><i
                                class="mdi mdi-filter-variant me-1"></i> تصفية</button>
                        <a href="{{ route('admin.purchase-invoices.index') }}"
                            class="btn btn-light border shadow-sm px-3 py-2" data-bs-toggle="tooltip"
                            title="إعادة ضبط الفلاتر"><i class="mdi mdi-refresh fs-5"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- الجدول --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white">
        <div class="table-responsive" style="max-height: 65vh; overflow-y: auto;">
            <table class="table table-custom table-hover table-nowrap mb-0">
                <thead class="sticky-top" style="z-index: 5;">
                    <tr>
                        <th class="text-start ps-4">رقم الفاتورة المرجعي</th>
                        <th class="text-start">المورد / التاجر</th>
                        <th>التاريخ</th>
                        <th>الإجمالي الفرعي</th>
                        <th>الضريبة</th>
                        <th class="col-total border-start">الإجمالي الشامل</th>
                        <th class="col-paid">المبلغ المدفوع</th>
                        <th class="col-due">المتبقي (الآجل)</th>
                        <th>حالة الفاتورة</th>

                        @canany(['view_purchase_invoices', 'edit_purchase_invoices', 'delete_purchase_invoices'])
                            <th style="width: 150px;">خيارات الإدارة</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="fw-bold text-success font-monospace-custom fs-6 text-start ps-4">
                                <i class="mdi mdi-file-document-outline me-1 text-muted"></i>{{ $invoice->invoice_number }}
                            </td>
                            <td class="fw-bold text-dark text-start">
                                {{ $invoice->supplier->company_name ?? '---' }}
                                <div class="text-muted font-11 mt-1"><i class="mdi mdi-account-tie"></i>
                                    {{ $invoice->supplier->contact_person ?? 'غير مسجل' }}</div>
                            </td>
                            <td class="font-monospace-custom">
                                {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</td>
                            <td class="font-monospace-custom">{{ number_format($invoice->sub_total, 3) }}</td>
                            <td class="font-monospace-custom text-warning text-dark">
                                {{ number_format($invoice->tax_amount, 3) }}</td>
                            <td class="col-total border-start fs-5">{{ number_format($invoice->grand_total, 3) }}</td>
                            <td class="col-paid fs-5">{{ number_format($invoice->paid_amount, 3) }}</td>
                            <td class="col-due fs-5">{{ number_format($invoice->due_amount, 3) }}</td>
                            <td>
                                <div class="d-flex flex-column align-items-center gap-1">
                                    @if ($invoice->payment_status == 'paid')
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success status-badge"><i
                                                class="mdi mdi-check-all me-1"></i> مدفوعة كلياً</span>
                                    @elseif($invoice->payment_status == 'partial')
                                        <span
                                            class="badge bg-warning bg-opacity-10 text-warning border border-warning text-dark status-badge"><i
                                                class="mdi mdi-clock-outline me-1"></i> سداد جزئي</span>
                                    @else
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger status-badge"><i
                                                class="mdi mdi-alert-circle-outline me-1"></i> آجلة (مستحقة)</span>
                                    @endif

                                    {{-- 🔥 إشعار وجود مرتجع 🔥 --}}
                                    @if ($invoice->returns && $invoice->returns->count() > 0)
                                        <span
                                            class="badge bg-dark bg-opacity-10 text-dark border border-dark status-badge font-11 mt-1"
                                            data-bs-toggle="tooltip" title="تم تسجيل حركة مرتجعات على هذه الفاتورة">
                                            <i class="mdi mdi-keyboard-return me-1"></i> يوجد مرتجع
                                        </span>
                                    @endif
                                </div>
                            </td>

                            @canany(['view_purchase_invoices', 'edit_purchase_invoices', 'delete_purchase_invoices'])
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @can('view_purchase_invoices')
                                            <a href="{{ route('admin.purchase-invoices.show', $invoice->id) }}"
                                                class="btn btn-sm btn-outline-info rounded-circle shadow-sm"
                                                data-bs-toggle="tooltip" title="عرض وتفاصيل الفاتورة">
                                                <i class="mdi mdi-eye font-16"></i>
                                            </a>
                                        @endcan

                                        @can('edit_purchase_invoices')
                                            @if ($invoice->due_amount > 0)
                                                <button class="btn btn-sm btn-outline-success rounded-circle shadow-sm"
                                                    data-bs-toggle="modal" data-bs-target="#payModal{{ $invoice->id }}"
                                                    data-bs-toggle="tooltip" title="تسجيل دفعة نقدية وسحب من الخزنة">
                                                    <i class="mdi mdi-cash-register font-16"></i>
                                                </button>

                                                {{-- Modal تسجيل الدفعة --}}
                                                <div class="modal fade text-start" id="payModal{{ $invoice->id }}"
                                                    tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div
                                                            class="modal-content border-0 rounded-4 shadow-lg border-top border-success border-4">
                                                            <div
                                                                class="modal-header bg-light border-bottom-0 rounded-top-4 pb-0 pt-4 px-4">
                                                                <h5 class="modal-title font-bold text-success"><i
                                                                        class="mdi mdi-cash-register me-1"></i> تسجيل دفعة للمورد
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <form
                                                                action="{{ route('admin.purchase-invoices.pay', $invoice->id) }}"
                                                                method="POST" class="prevent-double-submit">
                                                                @csrf
                                                                <div class="modal-body p-4">
                                                                    <div
                                                                        class="alert alert-warning border-warning border-opacity-25 bg-warning bg-opacity-10 shadow-sm mb-4">
                                                                        <strong class="d-block mb-1 text-dark"><i
                                                                                class="mdi mdi-information-outline me-1"></i>
                                                                            بيانات الفاتورة:</strong>
                                                                        <div class="font-monospace-custom">رقم الفاتورة:
                                                                            {{ $invoice->invoice_number }}</div>
                                                                        <div class="fw-bold">المورد:
                                                                            {{ $invoice->supplier->company_name ?? '---' }}</div>
                                                                        <div
                                                                            class="text-danger font-bold mt-2 d-block border-top border-warning border-opacity-25 pt-2">
                                                                            المبلغ المتبقي المطلوب سداده: <span
                                                                                class="font-monospace-custom fs-5">{{ number_format($invoice->due_amount, 3) }}
                                                                                OMR</span>
                                                                        </div>
                                                                    </div>

                                                                    <input type="hidden" name="payment_type"
                                                                        value="purchase_payment">
                                                                    <input type="hidden" name="payable_type"
                                                                        value="App\Models\Supplier">
                                                                    <input type="hidden" name="payable_id"
                                                                        value="{{ $invoice->supplier_id }}">
                                                                    <input type="hidden" name="invoice_id"
                                                                        value="{{ $invoice->id }}">

                                                                    <div class="mb-3">
                                                                        <label class="form-label font-bold text-muted">حساب الدفع
                                                                            (الخزنة) <span class="text-danger">*</span></label>
                                                                        <select name="financial_account_id"
                                                                            class="form-select border-success fw-bold select2-modal shadow-sm"
                                                                            required style="width: 100%;">
                                                                            <option value="" selected disabled>-- اختر الخزنة
                                                                                / الحساب البنكي --</option>
                                                                            @foreach ($accounts ?? [] as $account)
                                                                                <option value="{{ $account->id }}">
                                                                                    {{ $account->name_ar }}
                                                                                    ({{ number_format($account->balance, 3) }})
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label class="form-label font-bold text-muted">المبلغ
                                                                            المدفوع (OMR) <span
                                                                                class="text-danger">*</span></label>
                                                                        <div class="input-group shadow-sm">
                                                                            <span
                                                                                class="input-group-text bg-success text-white border-success"><i
                                                                                    class="mdi mdi-cash"></i></span>
                                                                            <input type="number" step="0.001" name="amount"
                                                                                class="form-control text-center font-bold text-success fs-4 border-success font-monospace-custom"
                                                                                max="{{ $invoice->due_amount }}" required>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row g-3">
                                                                        <div class="col-6 mb-3">
                                                                            <label class="form-label font-bold text-muted">تاريخ
                                                                                الدفع <span class="text-danger">*</span></label>
                                                                            <input type="date" name="payment_date"
                                                                                class="form-control text-center fw-bold shadow-sm bg-light"
                                                                                value="{{ date('Y-m-d') }}" required>
                                                                        </div>
                                                                        <div class="col-6 mb-3">
                                                                            <label class="form-label font-bold text-muted">ملاحظات
                                                                                (رقم المرجع)</label>
                                                                            <input type="text" name="notes"
                                                                                class="form-control shadow-sm bg-light fw-bold text-center"
                                                                                placeholder="مثال: نقدي / تحويل...">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="modal-footer border-0 p-3 bg-light rounded-bottom-4 justify-content-between">
                                                                    <button type="button"
                                                                        class="btn btn-secondary rounded-pill px-4 fw-bold"
                                                                        data-bs-dismiss="modal">إلغاء</button>
                                                                    <button type="submit"
                                                                        class="btn btn-success rounded-pill px-5 fw-bold shadow-sm btn-submit">
                                                                        <i class="mdi mdi-check-decagram-outline me-1"></i> حفظ
                                                                        وتحديث الأرصدة
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <button class="btn btn-sm btn-light rounded-circle shadow-sm border" disabled
                                                    data-bs-toggle="tooltip" title="مغلق: الفاتورة مدفوعة بالكامل">
                                                    <i class="mdi mdi-check-all font-16 text-muted"></i>
                                                </button>
                                            @endif
                                        @endcan

                                        {{-- 🔥 حماية زر الحذف بصلاحية الحذف والموقف المالي 🔥 --}}
                                        @can('delete_purchase_invoices')
                                            @if ($invoice->paid_amount > 0 || ($invoice->returns && $invoice->returns->count() > 0))
                                                <button type="button"
                                                    class="btn btn-sm btn-light border rounded-circle shadow-sm" disabled
                                                    data-bs-toggle="tooltip"
                                                    title="محمي: لا يمكن حذف فاتورة تم السداد منها أو تم إرجاع جزء منها.">
                                                    <i class="mdi mdi-trash-can-outline font-16 text-muted"></i>
                                                </button>
                                            @else
                                                <form action="{{ route('admin.purchase-invoices.destroy', $invoice->id) }}"
                                                    method="POST" class="d-inline-block delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger rounded-circle shadow-sm btn-delete"
                                                        data-bs-toggle="tooltip" title="حذف الفاتورة واسترجاع المخزون">
                                                        <i class="mdi mdi-trash-can-outline font-16"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->canany(['view_purchase_invoices', 'edit_purchase_invoices', 'delete_purchase_invoices']) ? 10 : 9 }}"
                                class="text-center py-5 text-muted fw-bold bg-white">
                                <i
                                    class="mdi mdi-receipt-text-remove-outline fs-1 d-block mb-2 opacity-25 text-success"></i>
                                <h5>لا توجد فواتير مشتريات مسجلة تطابق معايير البحث.</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 🔥 Pagination Premium 🔥 --}}
        @if ($invoices->hasPages())
            <div
                class="card-footer bg-white border-top border-light pt-4 pb-4 rounded-bottom-4 shadow-sm d-flex justify-content-center">
                {{ $invoices->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // تفعيل Select2 بداخل المودال عند فتحه
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.select2-modal').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? "rtl" : "ltr",
                    dropdownParent: $(this)
                });
            });

            // تأمين الفورم ضد الإرسال المزدوج وفحص المبالغ
            $('.prevent-double-submit').on('submit', function(e) {
                let amount = parseFloat($(this).find('input[name="amount"]').val());
                let max = parseFloat($(this).find('input[name="amount"]').attr('max'));

                if (amount > max) {
                    e.preventDefault();
                    Swal.fire('خطأ محاسبي', 'المبلغ المُدخل أكبر من المبلغ المتبقي للفاتورة!', 'error');
                    return false;
                }

                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري التنفيذ...');
            });

            // تأكيد الحذف بـ SweetAlert2
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد الحذف النهائي؟',
                    text: 'إذا تم حذف هذه الفاتورة سيتم خصم كميات المنتجات من المخازن وحذف القيود المالية المرتبطة بها! لا يمكن التراجع عن هذه الخطوة.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احذف الفاتورة',
                    cancelButtonText: 'تراجع وإلغاء',
                    borderRadius: '12px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // إشعارات النظام
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'عملية ناجحة',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif
        });
    </script>
@endpush
