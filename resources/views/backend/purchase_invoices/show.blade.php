@extends('backend.layouts.master')
@section('title', 'تفاصيل فاتورة المشتريات')

@push('css')
    <style>
        .invoice-box {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .invoice-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .invoice-title {
            font-weight: 900;
            color: #004b87;
            font-size: 24px;
            text-transform: uppercase;
        }

        .info-label {
            font-weight: bold;
            color: #6c757d;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .info-value {
            font-weight: 700;
            color: #343a40;
            font-size: 15px;
        }

        .table-invoice {
            white-space: nowrap;
        }

        .table-invoice th {
            background-color: #f8f9fa;
            color: #343a40;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
            text-align: center;
        }

        .table-invoice td {
            vertical-align: middle;
            font-weight: 500;
            text-align: center;
        }

        .font-monospace-custom {
            font-family: 'Courier New', Courier, monospace !important;
            letter-spacing: -0.5px;
        }

        .summary-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border: 1px dashed #cbd5e1;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #495057;
        }

        .summary-total {
            font-size: 18px;
            font-weight: 900;
            color: #10c469;
            border-top: 2px dashed #dee2e6;
            padding-top: 15px;
            margin-top: 10px;
        }

        .summary-due {
            font-size: 16px;
            font-weight: bold;
            color: #ff5b5b;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            margin-top: 10px;
        }

        /* 🖋️ إعدادات التوقيع 🖋️ */
        .signature-area {
            margin-top: 60px;
            page-break-inside: avoid;
        }

        .signature-box {
            border-top: 1px dashed #adb5bd;
            margin-top: 40px;
            padding-top: 10px;
            font-weight: bold;
            color: #6c757d;
            font-size: 13px;
        }

        /* Dark Mode */
        body[data-layout-color="dark"] .invoice-box {
            background-color: #323a46;
        }

        body[data-layout-color="dark"] .invoice-header {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .invoice-title {
            color: #66b0ff;
        }

        body[data-layout-color="dark"] .info-value {
            color: #ced4da;
        }

        body[data-layout-color="dark"] .table-invoice th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-invoice td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .summary-box {
            background-color: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .summary-row {
            color: #ced4da;
        }

        body[data-layout-color="dark"] .signature-box {
            border-top-color: #424e5a;
        }

        /* 🖨️ إعدادات الطباعة 🖨️ */
        @media print {
            body * {
                visibility: hidden;
            }

            .invoice-box,
            .invoice-box * {
                visibility: visible;
            }

            .invoice-box {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                padding: 0;
            }

            .btn,
            .page-title-box,
            .navbar-custom,
            .left-side-menu,
            .footer,
            .d-print-none {
                display: none !important;
            }

            .summary-box {
                border: 1px solid #000 !important;
                background-color: #fff !important;
            }

            .signature-area {
                page-break-inside: avoid !important;
            }

            .badge {
                border: 1px solid #000;
                color: #000 !important;
                background: transparent !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3 mt-2 d-print-none">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h4 class="page-title font-bold mb-0">
                <i class="mdi mdi-receipt text-primary me-1 fs-4 align-middle"></i> عرض الفاتورة
            </h4>
            <div class="d-flex gap-2 flex-wrap">
                <button onclick="window.print()" class="btn btn-dark rounded-pill fw-bold shadow-sm px-4">
                    <i class="mdi mdi-printer me-1"></i> طباعة السند
                </button>
                <a href="{{ route('admin.purchase-invoices.index') }}"
                    class="btn btn-light rounded-pill border fw-bold shadow-sm px-4">
                    <i class="mdi mdi-arrow-left me-1"></i> رجوع
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="invoice-box border-top border-primary border-4">

                {{-- رأس الفاتورة (اللوجو والأرقام) --}}
                <div class="invoice-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h2 class="invoice-title mb-1">فاتورة مشتريات</h2>
                        <span class="badge bg-secondary font-14 font-monospace-custom">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="text-md-end text-center">
                        <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="Logo" height="45"
                            class="mb-2">
                        <p class="mb-0 font-bold text-muted">{{ $invoice->branch->name_ar ?? 'الفرع الرئيسي' }}</p>
                    </div>
                </div>

                {{-- بيانات المورد والتواريخ --}}
                <div class="row mb-4">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <h6 class="text-uppercase text-muted font-bold mb-3"><i class="mdi mdi-truck-delivery me-1"></i>
                            بيانات المورد (التاجر):</h6>
                        <div class="mb-2">
                            <div class="info-label">اسم الشركة:</div>
                            <div class="info-value text-primary fs-5">{{ $invoice->supplier->company_name ?? 'غير محدد' }}
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="info-label">رقم الهاتف:</div>
                            <div class="info-value font-monospace-custom" dir="ltr">
                                {{ $invoice->supplier->phone ?? '---' }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="info-label">الرقم الضريبي:</div>
                            <div class="info-value font-monospace-custom">{{ $invoice->supplier->tax_number ?? '---' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 text-sm-end">
                        <h6 class="text-uppercase text-muted font-bold mb-3"><i
                                class="mdi mdi-file-document-outline me-1"></i> تفاصيل المعاملة:</h6>
                        <div class="mb-2">
                            <div class="info-label">تاريخ الإصدار:</div>
                            <div class="info-value font-monospace-custom">
                                {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="info-label">رقم فاتورة المورد الورقية:</div>
                            <div class="info-value font-monospace-custom">{{ $invoice->supplier_invoice_number ?? '---' }}
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="info-label">حالة الدفع:</div>
                            <div class="info-value mt-1">
                                @if ($invoice->payment_status == 'paid')
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1"><i
                                            class="mdi mdi-check-circle"></i> مدفوعة بالكامل</span>
                                @elseif($invoice->payment_status == 'partial')
                                    <span
                                        class="badge bg-warning bg-opacity-10 text-warning text-dark border border-warning px-2 py-1"><i
                                            class="mdi mdi-alert-circle"></i> دفع جزئي</span>
                                @else
                                    <span
                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1"><i
                                            class="mdi mdi-close-circle"></i> آجلة (لم تُدفع)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🔥 تنبيه المرتجعات (يظهر فقط لو فيه مرتجعات مسجلة) 🔥 --}}
                @if ($invoice->returns && $invoice->returns->count() > 0)
                    <div class="alert alert-danger border-danger border-opacity-50 fw-bold shadow-sm mb-4">
                        <i class="mdi mdi-keyboard-return fs-5 align-middle me-1"></i> تنبيه محاسبي: تم استرجاع جزء أو كل من
                        بضاعة هذه الفاتورة إلى المورد. المبالغ الموضحة أدناه هي للفاتورة الأصلية قبل خصم قيمة المرتجعات.
                    </div>
                @endif

                {{-- جدول الأصناف --}}
                <div class="table-responsive mb-4">
                    <table class="table table-invoice table-nowrap border">
                        <thead>
                            <tr>
                                <th style="width: 50px;">م</th>
                                <th class="text-start">الصنف / المنتج</th>
                                <th>الكمية الواردة</th>
                                <th>سعر الوحدة</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $index => $item)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                    <td class="text-start text-primary fw-bold text-wrap" style="max-width: 250px;">
                                        {{ $item->product->name_ar ?? 'منتج محذوف' }}
                                    </td>
                                    <td class="font-monospace-custom fw-bold fs-5">{{ $item->quantity }}</td>
                                    <td class="font-monospace-custom fw-bold">{{ number_format($item->unit_cost, 3) }}</td>
                                    <td class="text-dark fw-bolder font-monospace-custom fs-5">
                                        {{ number_format($item->row_total, 3) }} OMR</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 🔥 سجل المدفوعات المتعلقة بالفاتورة 🔥 --}}
                @php
                    $invoicePayments = \App\Models\Payment::where('payable_type', 'App\Models\Supplier')
                        ->where('payable_id', $invoice->supplier_id)
                        ->where('transaction_details', 'LIKE', '%' . $invoice->invoice_number . '%')
                        ->get();
                @endphp

                @if ($invoicePayments->count() > 0)
                    <div class="mb-4 bg-light p-3 rounded-3 border">
                        <h6 class="text-muted font-bold mb-3"><i class="mdi mdi-cash-multiple me-1"></i> سجل الدفعات المسددة
                            على هذه الفاتورة:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center mb-0 bg-white table-nowrap">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th>رقم السند</th>
                                        <th>تاريخ الدفعة</th>
                                        <th>المبلغ المدفوع</th>
                                        <th>طريقة الدفع / الخزينة</th>
                                        <th class="text-start ps-3">الملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoicePayments as $payment)
                                        <tr>
                                            <td class="fw-bold font-monospace-custom text-success">
                                                {{ $payment->receipt_number }}</td>
                                            <td class="font-monospace-custom text-muted">
                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
                                            <td class="text-dark fw-bolder font-monospace-custom fs-6">
                                                {{ number_format(abs($payment->amount), 3) }} OMR</td>
                                            <td>
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1">
                                                    {{ $payment->financialAccount->name_ar ?? 'خزينة محذوفة' }}
                                                </span>
                                            </td>
                                            <td class="font-12 text-muted text-start ps-3">
                                                {{ $payment->notes ?? 'تسوية حساب مالي مورد' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- الملخص المالي والملاحظات --}}
                <div class="row mt-4">
                    <div class="col-sm-6 mb-4 mb-sm-0">
                        <div class="clearfix pt-2 border p-3 rounded-3 h-100 bg-light bg-opacity-50">
                            <h6 class="text-muted font-bold mb-2"><i class="mdi mdi-text-box-outline me-1"></i> الملاحظات:
                            </h6>
                            <p class="text-dark font-13 lh-base mb-3">
                                {{ $invoice->notes ?? 'لا توجد ملاحظات إضافية مسجلة على هذه الفاتورة.' }}
                            </p>

                            <h6 class="text-muted font-bold mb-2 border-top pt-3"><i
                                    class="mdi mdi-credit-card-outline me-1"></i> طريقة الشراء المجدولة:</h6>
                            <span class="badge bg-primary px-3 py-2 font-13 shadow-sm">
                                @if ($invoice->payment_type == 'cash')
                                    دفع نقدي / فوري
                                @elseif($invoice->payment_type == 'auto_installment')
                                    أقساط مجدولة آلياً
                                @elseif($invoice->payment_type == 'manual_installment')
                                    أقساط مخصصة
                                @else
                                    غير محدد
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="summary-box shadow-sm h-100">
                            <div class="summary-row">
                                <span>الإجمالي الفرعي:</span>
                                <span class="font-monospace-custom">{{ number_format($invoice->sub_total, 3) }} OMR</span>
                            </div>
                            <div class="summary-row text-danger">
                                <span>الخصم المكتسب (-):</span>
                                <span class="font-monospace-custom">{{ number_format($invoice->discount, 3) }} OMR</span>
                            </div>
                            <div class="summary-row text-warning text-dark">
                                <span>الضريبة (+):</span>
                                <span class="font-monospace-custom">{{ number_format($invoice->tax_amount, 3) }}
                                    OMR</span>
                            </div>
                            <div class="summary-row summary-total">
                                <span>الإجمالي المستحق للمورد:</span>
                                <span class="font-monospace-custom">{{ number_format($invoice->grand_total, 3) }}
                                    OMR</span>
                            </div>
                            <div class="summary-row mt-3 text-success">
                                <span>إجمالي ما تم سداده:</span>
                                <span class="font-monospace-custom">{{ number_format($invoice->paid_amount, 3) }}
                                    OMR</span>
                            </div>
                            <div class="summary-row summary-due">
                                <span>المتبقي (الآجل):</span>
                                <span class="font-monospace-custom">{{ number_format($invoice->due_amount, 3) }}
                                    OMR</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🖋️ منطقة التوقيعات محمية من الانقسام أثناء الطباعة 🖋️ --}}
                <div class="row signature-area text-center">
                    <div class="col-4">
                        <h6 class="font-bold text-dark">المحاسب / مدخل البيانات</h6>
                        <div class="signature-box">الاسم والتوقيع</div>
                    </div>
                    <div class="col-4">
                        <h6 class="font-bold text-dark">مدير المدرسة / الاعتماد</h6>
                        <div class="signature-box">الختم والتوقيع</div>
                    </div>
                    <div class="col-4">
                        <h6 class="font-bold text-dark">المورد / المندوب المستلم</h6>
                        <div class="signature-box">الاسم والتوقيع</div>
                    </div>
                </div>

                <div class="row mt-4 d-print-none">
                    <div class="col-12 text-center text-muted">
                        <hr>
                        <p class="font-12 mb-0">تم طباعة هذه الفاتورة من خلال النظام بواسطة:
                            <strong class="text-dark">{{ auth()->user()->name ?? 'مدير النظام' }}</strong>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
