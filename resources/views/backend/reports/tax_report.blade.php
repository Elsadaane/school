@extends('backend.layouts.master')
@section('title', 'التقرير الضريبي (VAT)')
@section('title_page', 'التقارير المالية - الإقرار الضريبي')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        .tax-card {
            border-radius: 16px;
            padding: 25px;
            position: relative;
            overflow: hidden;
            z-index: 1;
            color: #fff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .tax-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .tax-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            z-index: -1;
        }

        .tax-output {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }

        .tax-input {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        }

        .tax-net {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .tax-value {
            font-family: 'Courier New', Courier, monospace;
            font-size: 32px;
            font-weight: 900;
            margin: 10px 0;
            letter-spacing: -1px;
        }

        .tax-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .tax-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .tax-list li:last-child {
            border-bottom: none;
        }

        .tax-label {
            font-weight: 700;
            color: #475569;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tax-amount {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 18px;
            color: #0f172a;
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border: 1px solid #424e5a;
        }

        body[data-layout-color="dark"] .tax-label {
            color: #cbd5e1;
        }

        body[data-layout-color="dark"] .tax-amount {
            color: #f8fafc;
        }

        body[data-layout-color="dark"] .tax-list li {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }

        /* 🖨️ إعدادات الطباعة النظيفة 🖨️ */
        @media print {
            body {
                background-color: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print,
            .left-side-menu,
            .navbar-custom,
            .page-title-box,
            .footer {
                display: none !important;
            }

            .card-premium {
                box-shadow: none !important;
                border: 1px solid #ddd;
                break-inside: avoid;
            }

            .tax-card {
                break-inside: avoid;
                border: 1px solid #000;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none flex-wrap gap-3">
            <h4 class="page-title font-bold mb-0 text-dark">
                <i class="mdi mdi-bank-transfer text-primary me-1 fs-4 align-middle"></i> التقرير والإقرار الضريبي (VAT)
            </h4>
            <button class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm" onclick="window.print()">
                <i class="mdi mdi-printer me-1"></i> طباعة الإقرار
            </button>
        </div>

        {{-- 🌟 فلتر التاريخ --}}
        <div class="card card-premium mb-4 border-top border-primary border-4 shadow-sm no-print">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.tax_report') }}" method="GET"
                    class="row g-3 align-items-end justify-content-center prevent-double-submit">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">الفترة الضريبية من</label>
                        <input type="date" name="start_date"
                            class="form-control form-control-lg bg-light border-0 fw-bold text-center shadow-sm"
                            value="{{ request('start_date', $startDate ?? date('Y-m-01')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">إلى تاريخ</label>
                        <input type="date" name="end_date"
                            class="form-control form-control-lg bg-light border-0 fw-bold text-center shadow-sm"
                            value="{{ request('end_date', $endDate ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm btn-submit">
                            <i class="mdi mdi-filter-variant me-1"></i> استخراج التقرير
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 الهيدر للطباعة فقط --}}
        <div class="d-none d-print-block text-center mb-5 border-bottom pb-3">
            <h2 class="fw-bolder text-dark mb-1">الإقرار الضريبي (VAT Report)</h2>
            <p class="text-muted font-15 fw-bold mb-0">الفترة من: <span dir="ltr">{{ $startDate ?? '---' }}</span> |
                إلى: <span dir="ltr">{{ $endDate ?? '---' }}</span></p>
            <p class="text-muted font-12 mt-1">تم الاستخراج بتاريخ: {{ now()->format('Y-m-d H:i') }}</p>
        </div>

        @php
            $outTax = $totalOutputTax ?? 0;
            $inTax = $totalInputTax ?? 0;
            $netTax = $netVat ?? 0;
        @endphp

        {{-- 🌟 كروت الملخص الضريبي --}}
        <div class="row g-4 mb-5">
            {{-- ضريبة المخرجات (المبيعات) --}}
            <div class="col-xl-4 col-md-6">
                <div class="tax-card tax-output shadow-sm">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold mb-0">ضريبة المخرجات (المبيعات)</h6>
                            <div class="tax-value">{{ number_format($outTax, 3) }}</div>
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 shadow-sm">OMR</span>
                        </div>
                        <i class="mdi mdi-tray-arrow-up fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            {{-- ضريبة المدخلات (المشتريات) --}}
            <div class="col-xl-4 col-md-6">
                <div class="tax-card tax-input shadow-sm">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold mb-0">ضريبة المدخلات (المشتريات)</h6>
                            <div class="tax-value">{{ number_format($inTax, 3) }}</div>
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 shadow-sm">OMR</span>
                        </div>
                        <i class="mdi mdi-tray-arrow-down fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>

            {{-- صافي الضريبة --}}
            <div class="col-xl-4 col-md-12">
                <div class="tax-card tax-net shadow-sm border-2 {{ $netTax < 0 ? 'border-warning' : 'border-success' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold mb-0">صافي الضريبة
                                {{ $netTax >= 0 ? '(مستحقة للدفع)' : '(قابلة للاسترداد)' }}</h6>
                            <div class="tax-value {{ $netTax < 0 ? 'text-warning' : 'text-success' }}">
                                {{ number_format(abs($netTax), 3) }}
                            </div>
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 shadow-sm">OMR</span>
                        </div>
                        <i class="mdi mdi-bank-outline fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            {{-- 🌟 تفاصيل المخرجات (الإيرادات/المبيعات) --}}
            <div class="col-lg-6">
                <div class="card card-premium h-100 p-4 border-top border-info border-4 shadow-sm">
                    <h5 class="fw-bolder text-info mb-4"><i
                            class="mdi mdi-arrow-top-right-thick me-2 fs-4 align-middle"></i> تفاصيل ضريبة المخرجات (Output
                        Tax)</h5>

                    <ul class="tax-list">
                        <li>
                            <div class="tax-label"><i class="mdi mdi-school text-primary fs-3 align-middle"></i> عقود ورسوم
                                الطلاب</div>
                            <div class="tax-amount">{{ number_format($contractsTax ?? 0, 3) }} OMR</div>
                        </li>
                        <li>
                            <div class="tax-label"><i class="mdi mdi-storefront text-info fs-3 align-middle"></i> مبيعات
                                المقصف والزي</div>
                            <div class="tax-amount">{{ number_format($salesTax ?? 0, 3) }} OMR</div>
                        </li>
                        <li>
                            <div class="tax-label"><i class="mdi mdi-cash-plus text-success fs-3 align-middle"></i> إيرادات
                                أخرى متنوعة</div>
                            <div class="tax-amount">{{ number_format($incomesTax ?? 0, 3) }} OMR</div>
                        </li>
                    </ul>

                    <div class="mt-auto pt-4 text-end border-top mt-3 bg-light p-3 rounded-3 shadow-sm">
                        <span class="text-muted fw-bold me-3">إجمالي ضريبة المخرجات:</span>
                        <span class="tax-amount text-info fs-4">{{ number_format($outTax, 3) }} OMR</span>
                    </div>
                </div>
            </div>

            {{-- 🌟 تفاصيل المدخلات (المصروفات/المشتريات) --}}
            <div class="col-lg-6">
                <div class="card card-premium h-100 p-4 border-top border-danger border-4 shadow-sm">
                    <h5 class="fw-bolder text-danger mb-4"><i
                            class="mdi mdi-arrow-bottom-right-thick me-2 fs-4 align-middle"></i> تفاصيل ضريبة المدخلات
                        (Input Tax)</h5>

                    <ul class="tax-list">
                        <li>
                            <div class="tax-label"><i class="mdi mdi-cart-arrow-down text-danger fs-3 align-middle"></i>
                                فواتير المشتريات (مخزون)</div>
                            <div class="tax-amount">{{ number_format($purchasesTax ?? 0, 3) }} OMR</div>
                        </li>
                        <li>
                            <div class="tax-label"><i class="mdi mdi-receipt-outline text-warning fs-3 align-middle"></i>
                                المصروفات التشغيلية</div>
                            <div class="tax-amount">{{ number_format($expensesTax ?? 0, 3) }} OMR</div>
                        </li>
                        <li>
                            <div class="tax-label"><i class="mdi mdi-domain text-secondary fs-3 align-middle"></i> شراء
                                الأصول والممتلكات</div>
                            <div class="tax-amount">{{ number_format($assetsTax ?? 0, 3) }} OMR</div>
                        </li>
                    </ul>

                    <div class="mt-auto pt-4 text-end border-top mt-3 bg-light p-3 rounded-3 shadow-sm">
                        <span class="text-muted fw-bold me-3">إجمالي ضريبة المدخلات:</span>
                        <span class="tax-amount text-danger fs-4">{{ number_format($inTax, 3) }} OMR</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 التوقيعات عند الطباعة --}}
        <div class="row mt-5 pt-5 text-center font-14 fw-bold text-dark d-none d-print-flex">
            <div class="col-4"><strong>إعداد المحاسب:</strong><br><br>.........................................</div>
            <div class="col-4"><strong>مراجعة وتدقيق المدير
                    المالي:</strong><br><br>.........................................</div>
            <div class="col-4"><strong>الختم الرسمي للمؤسسة:</strong><br><br></div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- ✅ تم الاستغناء عن jQuery وتحويل الحماية لـ Vanilla JS --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector('.prevent-double-submit');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('.btn-submit');
                    btn.disabled = true;
                    btn.innerHTML =
                        '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري استخراج البيانات...';
                });
            }
        });
    </script>
@endpush
