@extends('backend.layouts.master')
@section('title', 'المركز المالي الشامل (P&L)')
@section('title_page', 'تقارير الإدارة العليا - الأرباح والخسائر')

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
            overflow: hidden;
        }

        .kpi-card {
            border-radius: 16px;
            padding: 25px;
            position: relative;
            color: #fff;
            z-index: 1;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            z-index: -1;
        }

        .kpi-revenue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .kpi-expense {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        }

        .kpi-profit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .kpi-loss {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        }

        .kpi-value {
            font-family: 'Courier New', Courier, monospace;
            font-size: 32px;
            font-weight: 900;
            margin: 10px 0;
            letter-spacing: -0.5px;
        }

        .analysis-item {
            margin-bottom: 20px;
        }

        .analysis-label {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .progress-custom {
            height: 10px;
            border-radius: 50px;
            background-color: #f1f5f9;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .progress-bar-custom {
            height: 100%;
            border-radius: 50px;
            transition: width 1s ease-in-out;
        }

        .trend-badge {
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .trend-up {
            background: rgba(16, 185, 129, 0.2);
            color: #059669;
        }

        .trend-down {
            background: rgba(244, 63, 94, 0.2);
            color: #e11d48;
        }

        .insight-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-left: 4px solid #8b5cf6;
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
            color: #334155;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
        }

        body[data-layout-color="dark"] .insight-box {
            background: #3b4652;
            border-color: #818cf8;
            color: #cbd5e1;
        }

        body[data-layout-color="dark"] .analysis-label {
            color: #f8fafc;
        }

        body[data-layout-color="dark"] .progress-custom {
            background-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        /* 🖨️ إعدادات الطباعة الاحترافية 🖨️ */
        @media print {
            body {
                background: #fff !important;
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

            .kpi-card {
                break-inside: avoid;
                border: 1px solid #ddd;
            }

            .kpi-card::before {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 d-print-none">
            <h4 class="page-title font-bold mb-0 text-dark">
                <i class="mdi mdi-chart-areaspline text-primary me-1 fs-4 align-middle"></i> المركز المالي والأرباح والخسائر
                (P&L)
            </h4>
            <button class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm" onclick="window.print()">
                <i class="mdi mdi-printer me-1"></i> طباعة التقرير
            </button>
        </div>

        {{-- 🌟 فلتر التاريخ --}}
        <div class="card card-premium mb-4 border-top border-primary border-4 no-print shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.overall') }}" method="GET"
                    class="row g-3 align-items-end prevent-double-submit">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">من تاريخ</label>
                        {{-- ✅ تأمين عرض التاريخ باستخدام data_get --}}
                        <input type="date" name="start_date"
                            class="form-control form-control-lg bg-light border-0 text-center fw-bold shadow-sm"
                            value="{{ request('start_date', data_get($startDate, 'format', date('Y-m-d')) ? $startDate->format('Y-m-d') : date('Y-m-01')) }}"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">إلى تاريخ</label>
                        <input type="date" name="end_date"
                            class="form-control form-control-lg bg-light border-0 text-center fw-bold shadow-sm"
                            value="{{ request('end_date', data_get($endDate, 'format', date('Y-m-d')) ? $endDate->format('Y-m-d') : date('Y-m-d')) }}"
                            required>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold shadow-sm btn-submit">
                            <i class="mdi mdi-filter-outline me-1"></i> تحليل البيانات
                        </button>
                        <a href="{{ route('admin.reports.overall') }}" class="btn btn-light btn-lg border fw-bold shadow-sm"
                            title="إعادة ضبط"><i class="mdi mdi-refresh"></i></a>
                    </div>
                </form>
            </div>
        </div>

        @php
            $totalRev = $totalRevenue ?? 0;
            $netProf = $netProfit ?? 0;
            $profitMargin = $totalRev > 0 ? ($netProf / $totalRev) * 100 : 0;
            $isProfit = $netProf >= 0;
        @endphp

        {{-- 🌟 كروت المؤشرات العليا (KPIs) --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-4">
                <div class="kpi-card kpi-revenue shadow-sm">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold mb-0">إجمالي التدفقات النقدية (الإيرادات)</h6>
                            <div class="kpi-value">{{ number_format($totalRev, 3) }}</div>
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 shadow-sm"><i
                                    class="mdi mdi-cash-plus me-1"></i> OMR</span>
                        </div>
                        <i class="mdi mdi-arrow-up-circle-outline fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="kpi-card kpi-expense shadow-sm">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold mb-0">إجمالي التدفقات الخارجة (المصروفات)</h6>
                            <div class="kpi-value">{{ number_format($totalExpenses ?? 0, 3) }}</div>
                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 shadow-sm"><i
                                    class="mdi mdi-cash-minus me-1"></i> OMR</span>
                        </div>
                        <i class="mdi mdi-arrow-down-circle-outline fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="kpi-card {{ $isProfit ? 'kpi-profit' : 'kpi-loss' }} shadow-sm">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase fw-bold mb-0">صافي المركز المالي
                                ({{ $isProfit ? 'ربح' : 'عجز' }})</h6>
                            <div class="kpi-value">{{ number_format(abs($netProf), 3) }}</div>
                            <div class="d-flex gap-2">
                                <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 shadow-sm">OMR</span>
                                <span class="badge bg-dark bg-opacity-50 text-white rounded-pill px-2 shadow-sm">هامش:
                                    {{ round($profitMargin, 1) }}%</span>
                            </div>
                        </div>
                        <i class="mdi {{ $isProfit ? 'mdi-trending-up' : 'mdi-trending-down' }} fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 تحليل الأرباح والخسائر --}}
        <div class="row g-4 mb-4">
            {{-- الجانب الأيمن: مصادر الدخل --}}
            <div class="col-lg-6">
                <div class="card card-premium h-100 p-4 border-top border-primary border-4 shadow-sm">
                    <h5 class="fw-bolder text-dark mb-4"><i class="mdi mdi-finance text-primary me-2 fs-4 align-middle"></i>
                        تحليل مصادر الإيرادات (التدفقات الداخلة)</h5>

                    @php
                        $studPay = $studentPayments ?? 0;
                        $storeSalesVal = $storeSales ?? 0;
                        $othIncomes = $otherIncomes ?? 0;
                        $studentPct = $totalRev > 0 ? ($studPay / $totalRev) * 100 : 0;
                        $storePct = $totalRev > 0 ? ($storeSalesVal / $totalRev) * 100 : 0;
                        $otherPct = $totalRev > 0 ? ($othIncomes / $totalRev) * 100 : 0;
                    @endphp

                    <div class="analysis-item">
                        <div class="analysis-label text-dark">
                            <span><i class="mdi mdi-school-outline text-primary me-1 fs-5 align-middle"></i> أقساط ورسوم
                                الطلاب</span>
                            <div class="text-end">
                                <span class="font-monospace fs-5 fw-bold">{{ number_format($studPay, 3) }}</span>
                                <span class="trend-badge trend-up ms-2 shadow-sm"><i class="mdi mdi-arrow-top-right"></i>
                                    {{ round($studentPct) }}%</span>
                            </div>
                        </div>
                        <div class="progress-custom shadow-sm">
                            <div class="progress-bar-custom bg-primary" style="width: {{ $studentPct }}%"></div>
                        </div>
                    </div>

                    <div class="analysis-item">
                        <div class="analysis-label text-dark">
                            <span><i class="mdi mdi-store-outline text-info me-1 fs-5 align-middle"></i> مبيعات المقصف
                                والزي</span>
                            <div class="text-end">
                                <span class="font-monospace fs-5 fw-bold">{{ number_format($storeSalesVal, 3) }}</span>
                                <span class="trend-badge trend-up ms-2 shadow-sm"><i class="mdi mdi-arrow-top-right"></i>
                                    {{ round($storePct) }}%</span>
                            </div>
                        </div>
                        <div class="progress-custom shadow-sm">
                            <div class="progress-bar-custom bg-info" style="width: {{ $storePct }}%"></div>
                        </div>
                    </div>

                    <div class="analysis-item mb-4">
                        <div class="analysis-label text-dark">
                            <span><i class="mdi mdi-plus-network-outline text-success me-1 fs-5 align-middle"></i> إيرادات
                                أخرى متنوعة</span>
                            <div class="text-end">
                                <span class="font-monospace fs-5 fw-bold">{{ number_format($othIncomes, 3) }}</span>
                                <span class="trend-badge trend-up ms-2 shadow-sm"><i class="mdi mdi-arrow-top-right"></i>
                                    {{ round($otherPct) }}%</span>
                            </div>
                        </div>
                        <div class="progress-custom shadow-sm">
                            <div class="progress-bar-custom bg-success" style="width: {{ $otherPct }}%"></div>
                        </div>
                    </div>

                    <div class="insight-box mt-auto shadow-sm border-0">
                        <i class="mdi mdi-lightbulb-on-outline text-warning fs-3 align-middle me-2"></i>
                        <strong class="text-dark">المحرك الأساسي:</strong>
                        <span class="text-muted d-block mt-1">
                            @if ($studPay > $storeSalesVal)
                                تمثل أقساط الطلاب المصدر الرئيسي للسيولة بنسبة {{ round($studentPct) }}% من إجمالي
                                الإيرادات.
                            @elseif($storeSalesVal > 0)
                                مبيعات المقصف تتفوق حالياً وتعتبر مصدر السيولة الأسرع والأكثر نشاطاً.
                            @else
                                لا توجد حركات إيرادات مسجلة في هذه الفترة.
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- الجانب الأيسر: بنود الصرف --}}
            <div class="col-lg-6">
                <div class="card card-premium h-100 p-4 border-top border-danger border-4 shadow-sm">
                    <h5 class="fw-bolder text-dark mb-4"><i
                            class="mdi mdi-chart-line-down text-danger me-2 fs-4 align-middle"></i> تحليل بنود المصروفات
                        (التكاليف)</h5>

                    @php
                        $totExp = $totalExpenses ?? 0;
                        $payrlls = $payrolls ?? 0;
                        $purch = $purchases ?? 0;
                        $genExp = $generalExpenses ?? 0;
                        $payrollPct = $totExp > 0 ? ($payrlls / $totExp) * 100 : 0;
                        $purchasePct = $totExp > 0 ? ($purch / $totExp) * 100 : 0;
                        $generalPct = $totExp > 0 ? ($genExp / $totExp) * 100 : 0;
                    @endphp

                    <div class="analysis-item">
                        <div class="analysis-label text-dark">
                            <span><i class="mdi mdi-account-group-outline text-danger me-1 fs-5 align-middle"></i> رواتب
                                وأجور الموظفين</span>
                            <div class="text-end">
                                <span class="font-monospace fs-5 fw-bold">{{ number_format($payrlls, 3) }}</span>
                                <span class="trend-badge trend-down ms-2 shadow-sm"><i
                                        class="mdi mdi-arrow-bottom-right"></i> {{ round($payrollPct) }}%</span>
                            </div>
                        </div>
                        <div class="progress-custom shadow-sm">
                            <div class="progress-bar-custom bg-danger" style="width: {{ $payrollPct }}%"></div>
                        </div>
                    </div>

                    <div class="analysis-item">
                        <div class="analysis-label text-dark">
                            <span><i class="mdi mdi-cart-outline text-warning me-1 fs-5 align-middle"></i> المشتريات
                                (مخزون/أصول)</span>
                            <div class="text-end">
                                <span class="font-monospace fs-5 fw-bold">{{ number_format($purch, 3) }}</span>
                                <span class="trend-badge trend-down ms-2 shadow-sm"><i
                                        class="mdi mdi-arrow-bottom-right"></i> {{ round($purchasePct) }}%</span>
                            </div>
                        </div>
                        <div class="progress-custom shadow-sm">
                            <div class="progress-bar-custom bg-warning" style="width: {{ $purchasePct }}%"></div>
                        </div>
                    </div>

                    <div class="analysis-item mb-4">
                        <div class="analysis-label text-dark">
                            <span><i class="mdi mdi-receipt-outline text-secondary me-1 fs-5 align-middle"></i> المصروفات
                                التشغيلية والنثريات</span>
                            <div class="text-end">
                                <span class="font-monospace fs-5 fw-bold">{{ number_format($genExp, 3) }}</span>
                                <span class="trend-badge trend-down ms-2 shadow-sm"><i
                                        class="mdi mdi-arrow-bottom-right"></i> {{ round($generalPct) }}%</span>
                            </div>
                        </div>
                        <div class="progress-custom shadow-sm">
                            <div class="progress-bar-custom bg-secondary" style="width: {{ $generalPct }}%"></div>
                        </div>
                    </div>

                    <div class="insight-box mt-auto shadow-sm border-0" style="border-left-color: #ef4444;">
                        <i class="mdi mdi-alert-circle-outline text-danger fs-3 align-middle me-2"></i>
                        <strong class="text-dark">نقطة استنزاف السيولة:</strong>
                        <span class="text-muted d-block mt-1">
                            @if ($payrollPct > 50)
                                بند الرواتب يستهلك أكثر من نصف المصروفات ({{ round($payrollPct) }}%)، وهو المعدل الطبيعي
                                للمؤسسات التعليمية.
                            @elseif($purchasePct > 50)
                                عمليات الشراء وتكديس المخزون تسحب السيولة الأكبر حالياً، يرجى مراجعة إدارة المشتريات.
                            @elseif($totExp > 0)
                                المصروفات موزعة بشكل متقارب ومتوازن بين الرواتب، المشتريات، والتشغيل.
                            @else
                                لا توجد حركات مصروفات مسجلة في هذه الفترة.
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 الرسوم البيانية الدائرية --}}
        <div class="row g-4 d-print-none">
            <div class="col-md-6">
                <div class="card card-premium p-4 text-center h-100 shadow-sm border-0">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-3"><i
                            class="mdi mdi-chart-donut text-primary me-1 fs-5 align-middle"></i> التوزيع البياني لهيكل
                        الإيرادات</h6>
                    <div style="height: 300px; width: 100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-premium p-4 text-center h-100 shadow-sm border-0">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-3"><i
                            class="mdi mdi-chart-donut-variant text-danger me-1 fs-5 align-middle"></i> التوزيع البياني
                        لهيكل المصروفات</h6>
                    <div style="height: 300px; width: 100%;">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- تذييل مطبوع فقط --}}
        <div class="row mt-4 d-none d-print-block">
            <div class="col-12 text-center text-muted">
                <hr>
                <p class="font-13 mb-0 fw-bold">تم استخراج هذا التقرير المالي من خلال نظام <b>سِيدَة</b> بواسطة: <span
                        class="text-dark">{{ auth()->user()->name ?? 'مدير النظام' }}</span></p>
                <p class="font-12 mb-0">تاريخ الطباعة: <span dir="ltr">{{ now()->format('Y-m-d H:i') }}</span></p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- ✅ تم نقل Chart.js إلى هنا لتسريع تحميل الصفحة --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Chart.defaults.font.family = "'Segoe UI', Tahoma, sans-serif";
            Chart.defaults.font.weight = 'bold';

            // 🌙 كشف الوضع الليلي لتكييف ألوان الشارت
            const isDarkMode = document.body.getAttribute('data-layout-color') === 'dark';
            const textColor = isDarkMode ? '#adb5bd' : '#475569';
            const borderColor = isDarkMode ? '#323a46' : '#ffffff';

            // ✅ حماية الفورم من الإرسال المزدوج (Vanilla JS)
            const form = document.querySelector('.prevent-double-submit');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('.btn-submit');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري التحليل...';
                });
            }

            // 🟢 رسم بياني للإيرادات
            const revCanvas = document.getElementById('revenueChart');
            if (revCanvas) {
                new Chart(revCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['رسوم الطلاب', 'المقصف والزي', 'إيرادات أخرى'],
                        datasets: [{
                            data: [{{ $studentPayments ?? 0 }}, {{ $storeSales ?? 0 }},
                                {{ $otherIncomes ?? 0 }}
                            ],
                            backgroundColor: ['#3b82f6', '#0ea5e9', '#10b981'],
                            borderWidth: 2,
                            borderColor: borderColor,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    color: textColor
                                }
                            }
                        }
                    }
                });
            }

            // 🔴 رسم بياني للمصروفات
            const expCanvas = document.getElementById('expenseChart');
            if (expCanvas) {
                new Chart(expCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['الرواتب والأجور', 'المشتريات', 'مصروفات تشغيلية'],
                        datasets: [{
                            data: [{{ $payrolls ?? 0 }}, {{ $purchases ?? 0 }},
                                {{ $generalExpenses ?? 0 }}
                            ],
                            backgroundColor: ['#ef4444', '#f59e0b', '#64748b'],
                            borderWidth: 2,
                            borderColor: borderColor,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    color: textColor
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
