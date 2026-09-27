@extends('backend.layouts.master')
@section('title', 'أقساط الأصول')
@section('title_page', 'سجل متابعة وسداد الأقساط')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
        }

        .page-wrapper {
            padding: 20px;
        }

        /* 🌟 تنسيقات الهوية البصرية 🌟 */
        .print-banner img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .header-banner img {
            max-height: 120px;
            margin-bottom: 15px;
        }

        .footer-banner img {
            max-height: 80px;
            margin-top: 30px;
        }

        .school-logo {
            max-height: 70px;
            width: auto;
            object-fit: contain;
        }

        .report-official-header {
            border-bottom: 2px solid #334155;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .table-custom thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
            text-align: center;
            white-space: nowrap;
        }

        .table-custom tbody td {
            vertical-align: middle;
            text-align: center;
            font-size: 14px;
            color: #334155;
            padding: 12px;
            white-space: nowrap;
        }

        .row-overdue {
            background-color: rgba(239, 68, 68, 0.06) !important;
        }

        .text-overdue {
            color: #dc2626 !important;
            font-weight: 800;
        }

        .row-paid {
            background-color: rgba(16, 185, 129, 0.05) !important;
        }

        .val-amount {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 700;
        }

        /* 🌟 منطقة التوقيعات 🌟 */
        .print-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding: 0 15px;
            page-break-inside: avoid;
        }

        .sig-box {
            width: 30%;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            color: #1e293b;
            position: relative;
        }

        .sig-line {
            border-top: 1px dashed #475569;
            width: 80%;
            margin: 35px auto 8px;
        }

        .seal-img {
            max-height: 95px;
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0.85;
            mix-blend-mode: multiply;
            z-index: 10;
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .card-header {
            background-color: #323a46 !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .table-custom thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom tbody td {
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

        /* 🖨️ إعدادات الطباعة 🖨️ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 10mm;
            }

            body {
                background: #fff !important;
                padding: 0;
            }

            .page-wrapper {
                padding: 0 !important;
            }

            .no-print,
            .btn,
            .btn-close,
            .alert,
            .card-header {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
                background: transparent !important;
            }

            .table-custom tbody tr.row-paid {
                background-color: transparent !important;
                opacity: 0.6;
            }

            .table-custom tbody tr.row-overdue {
                background-color: transparent !important;
                border: 2px solid #dc2626 !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            .print-only {
                display: block !important;
            }
        }

        .print-only {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🔥 عرض رسائل أخطاء التحقق 🔥 --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4 no-print" role="alert">
                <h5 class="fw-bold mb-1"><i class="bx bx-error-circle align-middle"></i> لم يتم السداد بسبب الأخطاء التالية:
                </h5>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 🌟 زر الطباعة 🌟 --}}
        <div class="text-start mb-3 no-print">
            <button onclick="window.print()" class="btn btn-dark fw-bold px-4 rounded-pill shadow-sm">
                <i class="bx bx-printer me-1"></i> طباعة كشف الأقساط
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">

                {{-- 🌟 الترويسة الرسمية (تظهر في الطباعة) 🌟 --}}
                @if (isset($globalSettings) && $globalSettings->header_image)
                    <div class="print-banner header-banner text-center mb-4">
                        <img src="{{ asset('storage/' . $globalSettings->header_image) }}" alt="Header Banner">
                    </div>
                @endif

                <div class="report-official-header row align-items-center">
                    <div class="col-4 text-start">
                        <h5 class="fw-bold mb-1 text-dark">{{ $globalSettings->company_name_ar ?? 'مدارس الطالب الذكي' }}
                        </h5>
                        <p class="text-muted small mb-0">{{ $globalSettings->address_ar ?? 'سلطنة عمان' }}</p>
                        <p class="text-muted small mb-0">هاتف: <bdi dir="ltr">{{ $globalSettings->phone ?? '-' }}</bdi>
                        </p>
                    </div>
                    <div class="col-4 text-center">
                        <h4 class="fw-bold text-decoration-underline mb-1 text-dark">كشف متابعة أقساط الأصول</h4>
                        <small class="text-muted">(Asset Installment Ledger)</small>
                    </div>
                    <div class="col-4 text-end">
                        @if (isset($globalSettings) && $globalSettings->logo)
                            <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" class="school-logo">
                        @else
                            <h4 class="text-muted fw-bold mb-0">LOGO</h4>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-custom w-100 mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>كود الأصل</th>
                                <th style="min-width: 200px;" class="text-start">تفاصيل ووصف الأصل</th>
                                <th>المورد</th>
                                <th>قيمة القسط (ر.ع)</th>
                                <th>تاريخ الاستحقاق</th>
                                <th>حالة الدفع</th>
                                <th class="no-print">إجراء (سداد)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($installments as $index => $inst)
                                @php
                                    $isOverdue = !$inst->is_paid && \Carbon\Carbon::parse($inst->due_date)->isPast();
                                @endphp
                                <tr class="{{ $inst->is_paid ? 'row-paid' : ($isOverdue ? 'row-overdue' : '') }}">
                                    <td><span
                                            class="badge bg-light text-secondary border px-2 py-1">{{ $index + 1 }}</span>
                                    </td>
                                    <td class="fw-bold text-primary">{{ $inst->asset->asset_code ?? '-' }}</td>
                                    <td class="text-start fw-bold text-dark">{{ $inst->asset->transaction_details ?? '-' }}
                                    </td>
                                    <td class="text-muted fw-bold">
                                        {{ app()->getLocale() == 'ar' ? $inst->asset->supplier->name_ar ?? '-' : $inst->asset->supplier->name_en ?? '-' }}
                                    </td>
                                    <td class="val-amount fs-5 {{ $isOverdue ? 'text-overdue' : 'text-primary' }}">
                                        {{ number_format($inst->amount, 3) }}
                                    </td>
                                    <td class="fw-bold {{ $isOverdue ? 'text-danger' : '' }}">
                                        <i
                                            class="bx bx-calendar me-1 text-muted"></i>{{ \Carbon\Carbon::parse($inst->due_date)->format('Y-m-d') }}
                                        @if ($isOverdue)
                                            <br><span class="badge bg-danger mt-1 shadow-sm"><i
                                                    class="bx bx-error-circle"></i> متأخر</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($inst->is_paid)
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success p-2 fw-bold">
                                                <i class="bx bx-check-circle"></i> مدفوع يوم
                                                {{ \Carbon\Carbon::parse($inst->paid_date)->format('Y-m-d') }}
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning border border-warning p-2 fw-bold text-dark">
                                                <i class="bx bx-time-five"></i> قيد الانتظار
                                            </span>
                                        @endif
                                    </td>
                                    <td class="no-print">
                                        @if (!$inst->is_paid)
                                            @can('manage_payments')
                                                <button type="button"
                                                    class="btn btn-sm btn-primary fw-bold px-3 payBtn shadow-sm"
                                                    data-id="{{ $inst->id }}"
                                                    data-amount="{{ number_format($inst->amount, 3, '.', '') }}"
                                                    data-details="{{ $inst->asset->transaction_details ?? '-' }}">
                                                    <i class="bx bx-wallet"></i> سداد الآن
                                                </button>
                                            @else
                                                <span class="text-muted fw-bold font-12"><i class="bx bx-lock-alt"></i>
                                                    مقفل</span>
                                            @endcan
                                        @else
                                            <button type="button" class="btn btn-sm btn-light border text-muted px-3"
                                                disabled>
                                                <i class="bx bx-check"></i> مكتمل
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-muted py-5 text-center fs-5">
                                        <i class="bx bx-folder-open fs-1 d-block mb-2 text-black-50"></i> لا توجد أقساط
                                        مجدولة مسجلة حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 🌟 التوقيعات والأختام 🌟 --}}
                <div class="print-signatures">
                    <div class="sig-box">
                        أعده (المحاسب المسؤول)
                        <div class="sig-line"></div>
                        <small class="text-muted">{{ auth()->user()->name ?? 'مُحاسب الأصول' }}</small>
                    </div>
                    <div class="sig-box">
                        الختم الرسمي (Official Stamp)
                        <div class="sig-line" style="border: none;">
                            @if (isset($globalSettings) && $globalSettings->seal)
                                <img src="{{ asset('storage/' . $globalSettings->seal) }}" alt="Seal Stamp"
                                    class="seal-img">
                            @endif
                        </div>
                    </div>
                    <div class="sig-box">
                        يعتمد (المدير المالي / الإدارة)
                        <div class="sig-line"></div>
                    </div>
                </div>

                {{-- 🌟 الفوتر الرسمي 🌟 --}}
                @if (isset($globalSettings) && $globalSettings->contract_footer)
                    <div class="print-banner footer-banner text-center">
                        <img src="{{ asset('storage/' . $globalSettings->contract_footer) }}" alt="Footer Banner">
                    </div>
                @endif

                <div class="text-center mt-3 text-muted print-only" style="font-size: 11px;">
                    طُبع بواسطة: {{ auth()->user()->name }} | تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}
                </div>
            </div>
        </div>
    </div>

    {{-- 🔥 مودال سداد القسط 🔥 --}}
    @can('manage_payments')
        <div class="modal fade" id="payInstallmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3">
                    <div class="modal-header text-white" style="background: linear-gradient(45deg, #1abc9c, #2ecc71);">
                        <h5 class="modal-title fw-bold"><i class="bx bx-money"></i> سداد قسط مستحق وإصدار قيد</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="payForm">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="alert alert-info fw-bold text-center border-info bg-info bg-opacity-10 mb-4 shadow-sm">
                                سداد قسط خاص بـ: <br> <span id="modal_details" class="text-dark fs-6 mt-1 d-block"></span>
                                <hr class="my-2 opacity-25">
                                بمبلغ: <span id="modal_amount" class="text-danger fs-4 font-monospace">0.000</span> ر.ع
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-primary fw-bold fs-6">المبلغ المراد سداده الآن (ر.ع) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bx bx-money text-muted"></i></span>
                                    <input type="number" step="0.001" min="0.001" name="pay_amount"
                                        id="pay_amount_input"
                                        class="form-control text-center fw-bold fs-5 border-primary font-monospace" required>
                                </div>
                                <small class="text-muted d-block mt-1 fw-bold"><i class="bx bx-info-circle"></i> يمكنك تعديل
                                    الرقم في حالة السداد الجزئي للقسط.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-danger fw-bold">اختر الخزنة / الحساب المالي لخصم القسط *</label>
                                <select name="financial_account_id" class="form-select border-primary fw-bold select2-init"
                                    required style="width: 100%;">
                                    <option value="" disabled selected>-- اختر الحساب للصرف --</option>
                                    @foreach ($accounts ?? [] as $acc)
                                        <option value="{{ $acc->id }}">
                                            {{ app()->getLocale() == 'ar' ? $acc->name_ar : $acc->name_en ?? $acc->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ السداد الفعلي *</label>
                                <input type="date" name="payment_date" class="form-control text-center fw-bold text-dark"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm"><i
                                    class="bx bx-check-shield"></i> تأكيد الخصم والسداد</button>
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
            let currentLocale = "{{ app()->getLocale() }}";
            let isRtl = currentLocale === 'ar';

            // تفعيل Select2 داخل المودال
            $('#payInstallmentModal').on('shown.bs.modal', function() {
                $(this).find('.select2-init').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#payInstallmentModal')
                });
            });

            // تمرير البيانات للمودال
            $(document).on('click', '.payBtn', function() {
                let id = $(this).data('id');
                let amount = parseFloat($(this).data('amount')) || 0;
                let details = $(this).data('details');

                $('#modal_amount').text(amount.toFixed(3));
                $('#modal_details').text(details);
                $('#pay_amount_input').val(amount.toFixed(3)).attr('max', amount.toFixed(3));

                // ✅ استخدام route() من Laravel بدلاً من المسار الثابت
                let actionUrl = "{{ route('admin.assets.installments.pay', ':id') }}".replace(':id', id);
                $('#payForm').attr('action', actionUrl);
            });

            // إشعارات SweetAlert
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تمت العملية بنجاح',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#10b981',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'تنبيه نظام!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>
@endpush
