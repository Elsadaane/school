@extends('backend.layouts.master')
@section('title', 'أرصدة الفروع (الجرد)')
@section('title_page', 'إدارة المخزون والمستودعات')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #fff;
        }

        .filter-card {
            border-top: 4px solid #3b82f6;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-weight: 600;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }

        .form-control,
        .form-select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .table-erp {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            font-size: 14px;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* الأزرار الموحدة */
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: 0.2s;
            border: 1px solid transparent;
            color: #64748b;
            background: #fff;
            text-decoration: none;
            margin: 0 2px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .action-btn.view {
            background: #e0f2fe;
            color: #0284c7;
            border-color: #bae6fd;
        }

        .action-btn.view:hover {
            background: #0284c7;
            color: #fff;
        }

        .action-btn.transfer {
            background: #fef3c7;
            color: #d97706;
            border-color: #fde68a;
        }

        .action-btn.transfer:hover {
            background: #f59e0b;
            color: #fff;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody tr:hover td {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .action-btn {
            background: #3b4652;
            border-color: #424e5a;
            color: #adb5bd;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 Page Header 🌟 --}}
        <div class="row mb-3 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold text-dark mb-1"><i
                            class="bx bx-store-alt text-primary me-1 fs-3 align-middle"></i> الجرد وأرصدة الفروع</h4>
                    <p class="text-muted mb-0 font-14">مراقبة كميات المنتجات والكتب في كل فرع وتحويل العهد بين المخازن.</p>
                </div>
            </div>
        </div>

        {{-- 🌟 Smart Filter Bar 🌟 --}}
        <div class="card card-premium filter-card shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.warehouse_stocks.index') }}" method="GET" class="prevent-double-submit">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted font-13"><i class="bx bx-building-house me-1"></i>
                                تصفية بالفرع (المدرسة)</label>
                            <select name="branch_id" class="form-select select2 shadow-sm">
                                <option value="">-- كل الفروع والمخازن --</option>
                                @foreach ($all_branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-danger font-13"><i class="bx bx-error me-1"></i> عرض
                                النواقص فقط</label>
                            <select name="low_stock"
                                class="form-select shadow-sm border-danger text-danger fw-bold bg-danger bg-opacity-10">
                                <option value="" class="text-dark">عرض كل الأرصدة</option>
                                <option value="1" {{ request('low_stock') == '1' ? 'selected' : '' }}>نعم، عرض النواقص
                                    (أقل من الحد)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted font-13"><i class="bx bx-search me-1"></i> بحث
                                سريع</label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="bx bx-search text-primary"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control border-start-0 fw-bold ps-0"
                                    placeholder="اسم المنتج، كود الصنف (SKU)...">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-dark flex-grow-1 fw-bold shadow-sm btn-submit">
                                <i class="bx bx-filter-alt me-1"></i> تصفية
                            </button>
                            <a href="{{ route('admin.warehouse_stocks.index') }}"
                                class="btn btn-light border shadow-sm fw-bold px-3" title="إلغاء الفلاتر">
                                <i class="bx bx-reset fs-5 align-middle"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 Data Table 🌟 --}}
        <div class="card card-premium shadow-sm border-0 mb-5">
            <div class="card-body p-0">
                @if ($stocks->count() > 0)
                    <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                        <table class="table table-erp table-hover align-middle mb-0 text-center">
                            <thead class="sticky-top" style="z-index: 10;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الفرع / المخزن</th>
                                    <th class="text-start ps-4">اسم المنتج (الصنف)</th>
                                    <th>كود الترقيم (SKU)</th>
                                    <th class="text-primary font-14">الرصيد الفعلي</th>
                                    <th>حد الخطر</th>
                                    <th>الحالة</th>

                                    {{-- 🔥 إخفاء عمود الإجراءات لو مفيش صلاحية 🔥 --}}
                                    @canany(['view_products', 'transfer_products'])
                                        <th style="width: 120px;">الإجراءات</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $index => $stock)
                                    <tr>
                                        <td class="text-muted fw-bold">{{ $stocks->firstItem() + $index }}</td>
                                        <td>
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 rounded-pill shadow-sm font-12">{{ $stock->branch->name_ar ?? 'بدون فرع' }}</span>
                                        </td>
                                        <td class="text-start ps-4 fw-bold text-dark fs-6">{{ $stock->name_ar }}</td>
                                        <td dir="ltr" class="text-muted font-monospace fw-bold">
                                            {{ $stock->sku ?? '---' }}</td>
                                        <td class="text-primary font-monospace fw-bolder fs-5">{{ $stock->quantity }}</td>
                                        <td class="text-danger font-monospace fw-bold">{{ $stock->alert_quantity }}</td>
                                        <td>
                                            @if ($stock->quantity > $stock->alert_quantity)
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill shadow-sm font-11">
                                                    <i class="bx bx-check-circle me-1"></i> متوفر
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill shadow-sm font-11">
                                                    <i class="bx bx-trending-down me-1"></i> منخفض (يحتاج شراء)
                                                </span>
                                            @endif
                                        </td>

                                        @canany(['view_products', 'transfer_products'])
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    {{-- 🔥 حماية زر عرض كارت الصنف بصلاحية الرؤية 🔥 --}}
                                                    @can('view_products')
                                                        <a href="{{ route('admin.warehouse_stocks.show', $stock->id) }}"
                                                            class="action-icon view shadow-sm" title="كارت الصنف والحركات">
                                                            <i class="bx bx-show fs-5"></i>
                                                        </a>
                                                    @endcan

                                                    {{-- 🔥 حماية زر التحويل السريع بصلاحية التحويل 🔥 --}}
                                                    @can('transfer_products')
                                                        @if ($stock->quantity > 0)
                                                            <button type="button"
                                                                class="action-icon transfer btn-open-transfer shadow-sm"
                                                                data-bs-toggle="modal" data-bs-target="#transferStockModal"
                                                                data-branch-id="{{ $stock->branch_id }}"
                                                                data-branch-name="{{ $stock->branch->name_ar ?? '' }}"
                                                                data-product-id="{{ $stock->id }}"
                                                                data-product-name="{{ $stock->name_ar }}"
                                                                data-max-qty="{{ $stock->quantity }}" title="تحويل كمية لفرع آخر">
                                                                <i class="bx bx-transfer-alt fs-5"></i>
                                                            </button>
                                                        @else
                                                            <button class="action-icon disabled shadow-sm" disabled
                                                                title="الرصيد صفر، لا يمكن التحويل"><i
                                                                    class="bx bx-transfer-alt fs-5"></i></button>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 bg-white rounded-4">
                        <i class="bx bx-box text-muted opacity-50 d-block mb-3" style="font-size: 60px;"></i>
                        <h5 class="text-dark fw-bold">لا توجد أرصدة مخزنية مطابقة للبحث</h5>
                        <p class="font-14 text-muted mb-0">يرجى التأكد من استلام بضائع وفواتير مشتريات وتوجيهها للفروع.</p>
                    </div>
                @endif
            </div>

            {{-- 🔥 الحفاظ على الفلاتر عند التقليب 🔥 --}}
            @if ($stocks->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $stocks->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ================= مودال التحويل المخزني السريع ================= --}}
    @can('transfer_products')
        <div class="modal fade" id="transferStockModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 border-top border-warning border-4">
                    <div class="modal-header bg-light rounded-top-4 border-bottom-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-warning text-darken"><i
                                class="bx bx-transfer-alt me-1 fs-4 align-middle"></i> تحويل عهدة مخزنية سريعة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form_transfer_stock">
                        @csrf
                        <div class="modal-body p-4 bg-white text-start">
                            <input type="hidden" name="from_branch_id" id="transfer_from_branch_id">
                            <input type="hidden" name="product_id" id="transfer_product_id">

                            <div
                                class="bg-warning bg-opacity-10 p-3 rounded-3 border border-warning border-opacity-25 mb-4 text-center shadow-sm">
                                <h6 class="text-dark mb-0 fw-bold" id="transfer_product_name"></h6>
                            </div>

                            <div class="row mb-4 g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold font-13">من فرع (المُرسِل)</label>
                                    <input type="text" id="transfer_from_branch_name"
                                        class="form-control bg-light text-danger fw-bold border-0 shadow-sm" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success font-13">إلى فرع (المُستقبِل) <span
                                            class="text-danger">*</span></label>
                                    <select name="to_branch_id" class="form-select border-success fw-bold shadow-sm" required>
                                        <option value="" disabled selected>-- اختر الفرع --</option>
                                        @foreach ($all_branches as $br)
                                            <option value="{{ $br->id }}">{{ $br->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4 text-center p-3 bg-light rounded-4 border shadow-sm">
                                <label class="form-label fw-bold text-dark font-14">الكمية المراد تحويلها <span
                                        class="text-danger">*</span></label>
                                <div class="input-group mx-auto shadow-sm" style="max-width: 200px;">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bx bx-hash text-warning"></i></span>
                                    <input type="number" step="1" name="transfer_quantity" id="transfer_qty_input"
                                        class="form-control border-start-0 border-warning text-center fw-bold fs-4 font-monospace"
                                        required min="1">
                                </div>
                                <small class="text-muted mt-2 d-block fw-bold" id="transfer_max_helper"></small>
                            </div>

                            <div
                                class="alert alert-info py-2 font-13 fw-bold text-center mb-0 shadow-sm border-0 border-start border-info border-4">
                                <i class="bx bx-info-circle me-1 fs-5 align-middle"></i> أمر التحويل يتم تنفيذه فوراً ويُضاف
                                للمخزن المُستقبِل ويُخصم من الحالي.
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0 rounded-bottom-4 justify-content-between px-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" id="btn_save_transfer"
                                class="btn btn-warning text-dark px-5 fw-bold shadow-sm rounded-pill"><i
                                    class="bx bx-check-double me-1 fs-5 align-middle"></i> تأكيد ونقل الكمية</button>
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

            $('.select2').select2({
                theme: "bootstrap-5",
                dir: isRtl ? "rtl" : "ltr"
            });

            // تأمين الفورم ضد الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-2 fs-5 align-middle"></i> جاري التصفية...');
            });

            // إعدادات مودال التحويل السريع
            $('.btn-open-transfer').on('click', function() {
                let branchId = $(this).data('branch-id');
                $('#transfer_from_branch_id').val(branchId);
                $('#transfer_product_id').val($(this).data('product-id'));
                $('#transfer_product_name').text($(this).data('product-name'));
                $('#transfer_from_branch_name').val($(this).data('branch-name'));

                let maxQty = parseInt($(this).data('max-qty'));
                $('#transfer_qty_input').attr('max', maxQty).val(''); // مسح القيمة القديمة لمنع الخطأ
                $('#transfer_max_helper').html(
                    '<span class="text-primary font-monospace fs-6">أقصى رصيد متاح: ' + maxQty +
                    '</span>');

                // إخفاء الفرع المُرسل من قائمة الفروع المُستقبلة حتى لا يحول لنفس الفرع
                $('select[name="to_branch_id"] option').each(function() {
                    if ($(this).val() == branchId) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
                $('select[name="to_branch_id"]').val('');
            });

            // إرسال طلب التحويل السريع بالـ AJAX
            $('#form_transfer_stock').on('submit', function(e) {
                e.preventDefault();
                let submitBtn = $('#btn_save_transfer');
                submitBtn.html(
                    '<i class="bx bx-loader bx-spin me-1 fs-5 align-middle"></i> جاري التحويل...').prop(
                    'disabled', true);

                $.ajax({
                    url: "{{ route('admin.warehouse_stocks.transfer') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        submitBtn.html(
                            '<i class="bx bx-check-double me-1 fs-5 align-middle"></i> تأكيد ونقل الكمية'
                            ).prop('disabled', false);
                        if (response.status === 'success') {
                            $('#transferStockModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'عملية ناجحة',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload(); // تحديث الأرصدة في الجدول
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.html(
                            '<i class="bx bx-check-double me-1 fs-5 align-middle"></i> تأكيد ونقل الكمية'
                            ).prop('disabled', false);
                        let errMsg = xhr.responseJSON && xhr.responseJSON.messages ? xhr
                            .responseJSON.messages.join('<br>') :
                            'حدث خطأ في الخادم، يرجى المحاولة مرة أخرى!';
                        Swal.fire({
                            icon: 'error',
                            title: 'فشل النقل المخزني',
                            html: errMsg,
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            });
        });
    </script>
@endpush
