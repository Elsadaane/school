@extends('backend.layouts.master')
@section('title', 'تفاصيل مرتجع مشتريات | ' . $purchaseReturn->return_number)
@section('title_page', 'إدارة المشتريات والمرتجعات')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .receipt-card {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border-top: 6px solid #dc3545;
            overflow: hidden;
        }

        .receipt-header {
            background-color: #fffafb;
            padding: 30px;
            border-bottom: 2px dashed #f5c2c7;
        }

        .receipt-title {
            color: #dc3545;
            font-weight: 900;
            letter-spacing: -0.5px;
            font-size: 24px;
        }

        .info-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            height: 100%;
        }

        .info-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 15px;
            color: #1e293b;
            font-weight: 700;
        }

        .table-receipt {
            margin-bottom: 0;
        }

        .table-receipt thead th {
            background-color: #dc3545;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            padding: 15px;
            border: none;
        }

        .table-receipt tbody td {
            vertical-align: middle;
            padding: 15px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 600;
        }

        .total-box {
            background: #fffafb;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #f5c2c7;
        }

        .grand-total {
            font-size: 24px;
            font-weight: 900;
            color: #dc3545;
            font-family: 'Courier New', Courier, monospace;
        }

        .defective-badge {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
        }

        /* 🖨️ إعدادات الطباعة 🖨️ */
        @media print {
            body {
                background: #fff !important;
            }

            .no-print,
            .navbar-custom,
            .left-side-menu,
            .footer {
                display: none !important;
            }

            .receipt-card {
                box-shadow: none !important;
                border: 1px solid #000 !important;
                border-top: 3px solid #000 !important;
            }

            .receipt-header {
                background: transparent !important;
                border-bottom: 2px solid #000 !important;
            }

            .table-receipt thead th {
                background-color: #f8f9fa !important;
                color: #000 !important;
                border-bottom: 2px solid #000 !important;
            }

            .total-box {
                background: transparent !important;
                border: 1px solid #000 !important;
            }

            .grand-total {
                color: #000 !important;
            }

            .info-box {
                border: 1px solid #000 !important;
            }

            * {
                color: #000 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- أزرار التحكم --}}
        <div class="row mb-4 mt-2 d-print-none">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h4 class="page-title font-bold text-dark mb-0">
                    <i class="mdi mdi-receipt-text-outline text-danger me-1 fs-3 align-middle"></i> عرض إيصال المرتجع
                </h4>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger fw-bold rounded-pill px-4 shadow-sm"
                        onclick="window.print();">
                        <i class="mdi mdi-printer me-1 align-middle"></i> طباعة الإيصال
                    </button>
                    <a href="{{ route('admin.purchase-returns.index') }}"
                        class="btn btn-light rounded-pill border fw-bold shadow-sm px-4">
                        <i class="mdi mdi-arrow-left me-1 align-middle"></i> العودة للسجل
                    </a>
                </div>
            </div>
        </div>

        {{-- إيصال المرتجع --}}
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card receipt-card">

                    {{-- رأس الإيصال --}}
                    <div class="receipt-header">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                                <h3 class="receipt-title mb-1"><i class="mdi mdi-keyboard-return me-1"></i> إشعار مرتجع
                                    مشتريات</h3>
                                <p class="text-muted fw-bold mb-0 font-monospace">Return No:
                                    {{ $purchaseReturn->return_number }}</p>
                            </div>
                            <div class="col-md-6 text-center text-md-end">
                                <h5 class="fw-bold text-dark mb-1">{{ env('APP_NAME', 'نظام إدارة المدارس') }}</h5>
                                <p class="text-muted font-13 mb-0">تاريخ الإصدار: <span
                                        class="font-monospace fw-bold">{{ $purchaseReturn->return_date }}</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        {{-- معلومات المورد والفاتورة --}}
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="info-box shadow-sm">
                                    <div class="info-label"><i class="mdi mdi-domain text-danger me-1"></i> بيانات المورد
                                        (التاجر)</div>
                                    <div class="info-value mb-1">{{ $purchaseReturn->supplier->company_name ?? '---' }}
                                    </div>
                                    <div class="text-muted font-13 fw-semibold">
                                        <i class="mdi mdi-phone me-1"></i> الهاتف: <span
                                            dir="ltr">{{ $purchaseReturn->supplier->phone ?? '---' }}</span><br>
                                        <i class="mdi mdi-identifier me-1"></i> الرقم الضريبي:
                                        {{ $purchaseReturn->supplier->tax_number ?? '---' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box shadow-sm">
                                    <div class="info-label"><i class="mdi mdi-receipt-text text-danger me-1"></i> المستند
                                        الأصلي (الفاتورة)</div>
                                    <div class="info-value mb-1">
                                        @if ($purchaseReturn->purchaseInvoice)
                                            رقم الفاتورة: <span
                                                class="font-monospace text-primary text-decoration-underline">{{ $purchaseReturn->purchaseInvoice->invoice_number }}</span>
                                        @else
                                            <span class="text-muted">غير متوفر</span>
                                        @endif
                                    </div>
                                    <div class="text-muted font-13 fw-semibold">
                                        هذا الإشعار يمثل بضاعة تم ردها للمورد وتم خصم قيمتها من مديونيته لدينا.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- جدول الأصناف المرتجعة --}}
                        <h6 class="fw-bold text-dark mb-3"><i class="mdi mdi-format-list-bulleted text-danger me-1"></i>
                            تفاصيل البضاعة المرتجعة:</h6>
                        <div class="table-responsive mb-4 rounded-3 border">
                            <table class="table table-receipt text-center">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th class="text-start ps-3" style="width: 35%;">الصنف / المنتج</th>
                                        <th style="width: 15%;">سعر الوحدة</th>
                                        <th style="width: 15%;">الكمية المرتجعة</th>
                                        <th style="width: 15%;">الإجمالي</th>
                                        <th style="width: 15%;">ملاحظات التلف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseReturn->items as $index => $item)
                                        <tr>
                                            <td class="text-muted">{{ $index + 1 }}</td>
                                            <td class="text-start ps-3 text-dark">
                                                {{ $item->product->name_ar ?? 'منتج محذوف' }}
                                                @if ($item->product && $item->product->sku)
                                                    <br><small
                                                        class="text-muted font-monospace">{{ $item->product->sku }}</small>
                                                @endif
                                            </td>
                                            <td class="font-monospace">{{ number_format($item->unit_cost, 3) }}</td>
                                            <td class="font-monospace text-danger">{{ $item->returned_quantity }}</td>
                                            <td class="font-monospace text-dark">{{ number_format($item->row_total, 3) }}
                                            </td>
                                            <td>
                                                @if ($item->is_defective)
                                                    <div class="defective-badge mb-1">بضاعة تالفة</div>
                                                    <small class="text-muted font-11 d-block"
                                                        style="white-space: normal;">{{ $item->return_reason }}</small>
                                                @else
                                                    <span class="text-success font-12"><i
                                                            class="mdi mdi-check-circle-outline"></i> سليمة</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- الإجماليات --}}
                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <div class="total-box shadow-sm text-end">
                                    <h6 class="text-muted fw-bold mb-2 text-uppercase">إجمالي قيمة الإشعار الدائن</h6>
                                    <div class="grand-total">{{ number_format($purchaseReturn->total_return_amount, 3) }}
                                        OMR</div>
                                    <small class="text-danger fw-bold mt-2 d-block">* تم خصم هذا المبلغ من حساب
                                        المورد.</small>
                                </div>
                            </div>
                        </div>

                        {{-- ملاحظات عامة --}}
                        @if ($purchaseReturn->notes)
                            <div class="mt-5 p-3 bg-light rounded-3 border">
                                <h6 class="fw-bold text-dark mb-2"><i
                                        class="mdi mdi-information-outline text-danger me-1"></i> ملاحظات الإدارة:</h6>
                                <p class="text-muted font-14 mb-0">{{ $purchaseReturn->notes }}</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
