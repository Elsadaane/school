@extends('backend.layouts.master')
@section('title', 'مقارنة الأداء المالي')
@section('title_page', 'التقارير التحليلية - مقارنة السنوات')

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

        .comparison-header {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
            padding-bottom: 15px;
            border-bottom: 2px dashed #e2e8f0;
            margin-bottom: 20px;
            text-align: center;
        }

        .compare-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .compare-label {
            font-weight: 700;
            color: #64748b;
            font-size: 14px;
            width: 30%;
        }

        .compare-val {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 16px;
            width: 35%;
            text-align: center;
            border-radius: 8px;
            padding: 8px;
        }

        .trend-up {
            color: #10b981;
            background: rgba(16, 185, 129, 0.1);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 800;
        }

        .trend-down {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 800;
        }

        .growth-card {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .growth-card::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .vs-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #0f172a;
            color: #fff;
            font-weight: 900;
            font-size: 20px;
            align-items: center;
            justify-content: center;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            border: 5px solid #f4f7fc;
            z-index: 10;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .winner-crown {
            color: #f59e0b;
            font-size: 30px;
            position: absolute;
            top: -15px;
            right: -15px;
            transform: rotate(15deg);
        }

        .val-b1 {
            text-align: right;
            color: #64748b;
            background: #f8fafc;
        }

        .val-b2 {
            text-align: left;
            color: #64748b;
            background: #f8fafc;
        }

        .val-winner {
            background: #d1fae5 !important;
            color: #059669 !important;
            border: 1px solid #10b981;
        }

        .val-loser {
            opacity: 0.6;
        }

        /* 🌙 Dark Mode */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
        }

        body[data-layout-color="dark"] .comparison-header {
            color: #f8fafc;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .compare-row {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .val-b1,
        body[data-layout-color="dark"] .val-b2 {
            background: #3b4652;
            color: #cbd5e1;
        }

        body[data-layout-color="dark"] .vs-circle {
            border-color: #323a46;
        }

        body[data-layout-color="dark"] .val-winner {
            background: rgba(16, 185, 129, 0.15) !important;
            color: #10c469 !important;
        }

        /* 🖨️ إعدادات الطباعة 🖨️ */
        @media print {
            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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

            .growth-card {
                break-inside: avoid;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 الهيدر وزر الطباعة --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 d-print-none">
            <h4 class="page-title font-bold mb-0 text-dark">
                <i class="mdi mdi-finance text-primary me-1 fs-4 align-middle"></i> مقارنة الأداء المالي
            </h4>
            <button class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm" onclick="window.print()">
                <i class="mdi mdi-printer me-1"></i> طباعة التقرير
            </button>
        </div>

        {{-- 🌟 فلتر اختيار السنوات --}}
        <div class="card card-premium mb-4 border-top border-primary border-4 d-print-none shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.comparison') }}" method="GET"
                    class="row g-3 align-items-end justify-content-center prevent-double-submit">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">سنة الأساس (القديمة)</label>
                        <select name="year1"
                            class="form-select form-select-lg bg-light border-0 fw-bold text-center shadow-sm" required>
                            <option value="" disabled selected>-- اختر السنة --</option>
                            @foreach ($availableYears ?? [] as $y)
                                <option value="{{ $y }}" {{ ($year1 ?? '') == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 text-center d-none d-md-block">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                            style="width: 45px; height: 45px;">
                            <i class="mdi mdi-swap-horizontal fs-4"></i>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">سنة المقارنة (الأحدث)</label>
                        <select name="year2"
                            class="form-select form-select-lg bg-light border-0 fw-bold text-center shadow-sm" required>
                            <option value="" disabled selected>-- اختر السنة --</option>
                            @foreach ($availableYears ?? [] as $y)
                                <option value="{{ $y }}" {{ ($year2 ?? '') == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm rounded-3 btn-submit">
                            <i class="mdi mdi-poll me-1"></i> إجراء المقارنة
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if (isset($dataY1) && isset($dataY2))
            {{-- ✅ حساب الفائز محلياً لأن الـ Controller لا يرسله --}}
            @php
                $winner = null;
                if (($dataY1['netProfit'] ?? 0) > ($dataY2['netProfit'] ?? 0)) {
                    $winner = 'y1';
                } elseif (($dataY2['netProfit'] ?? 0) > ($dataY1['netProfit'] ?? 0)) {
                    $winner = 'y2';
                }
            @endphp

            {{-- 🌟 كارت معدل النمو الشامل (Growth KPI) --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="growth-card shadow-sm d-flex justify-content-around align-items-center flex-wrap gap-4">
                        <div>
                            <h5 class="text-white-50 fw-bold mb-1">معدل نمو الإيرادات</h5>
                            <div
                                class="display-6 fw-bolder {{ ($revenueGrowth ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                <i
                                    class="mdi {{ ($revenueGrowth ?? 0) >= 0 ? 'mdi-trending-up' : 'mdi-trending-down' }}"></i>
                                {{ number_format(abs($revenueGrowth ?? 0), 1) }}%
                            </div>
                        </div>
                        <div class="text-center z-1">
                            <h3 class="text-white fw-bold mb-0">مقارنة الأداء المالي العام</h3>
                            <p class="text-white-50 mb-0 font-14">مقارنة شاملة بين عام {{ $year1 }} وعام
                                {{ $year2 }}</p>
                        </div>
                        <div>
                            <h5 class="text-white-50 fw-bold mb-1">معدل نمو صافي الربح</h5>
                            <div
                                class="display-6 fw-bolder {{ ($profitGrowth ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                <i
                                    class="mdi {{ ($profitGrowth ?? 0) >= 0 ? 'mdi-trending-up' : 'mdi-trending-down' }}"></i>
                                {{ number_format(abs($profitGrowth ?? 0), 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                {{-- 🌟 جدول المقارنة التفصيلي (Side by Side) --}}
                <div class="col-xl-7 position-relative">
                    <div class="vs-circle d-none d-xl-flex">VS</div>

                    <div class="card card-premium h-100 p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="compare-label text-start opacity-0 d-none d-sm-block">البند</div>
                            <div class="compare-val text-muted fs-5">عام {{ $year1 }}</div>
                            <div class="compare-val text-primary fs-5">عام {{ $year2 }}</div>
                        </div>

                        <div class="comparison-header text-success"><i class="mdi mdi-cash-plus me-1"></i> إيرادات المدرسة
                            (التدفقات النقدية)</div>

                        <div class="compare-row">
                            <div class="compare-label text-start"><i
                                    class="mdi mdi-school-outline me-1 fs-5 text-primary align-middle"></i> أقساط الطلاب
                            </div>
                            <div
                                class="compare-val val-b1 {{ ($dataY1['contracts'] ?? 0) > ($dataY2['contracts'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataY1['contracts'] ?? 0, 3) }}
                            </div>
                            <div
                                class="compare-val val-b2 {{ ($dataY2['contracts'] ?? 0) > ($dataY1['contracts'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataY2['contracts'] ?? 0, 3) }}
                            </div>
                        </div>
                        <div class="compare-row">
                            <div class="compare-label text-start"><i
                                    class="mdi mdi-store-outline me-1 fs-5 text-warning align-middle"></i> مبيعات المقصف
                            </div>
                            <div
                                class="compare-val val-b1 {{ ($dataY1['storeSales'] ?? 0) > ($dataY2['storeSales'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataY1['storeSales'] ?? 0, 3) }}
                            </div>
                            <div
                                class="compare-val val-b2 {{ ($dataY2['storeSales'] ?? 0) > ($dataY1['storeSales'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataY2['storeSales'] ?? 0, 3) }}
                            </div>
                        </div>
                        <div class="compare-row bg-light rounded-3 px-2 mt-2 border-0 shadow-sm">
                            <div class="compare-label text-success fs-6 text-start"><i class="mdi mdi-sigma me-1"></i>
                                إجمالي الإيرادات</div>
                            <div class="compare-val text-success fs-5">{{ number_format($dataY1['totalRevenue'] ?? 0, 3) }}
                            </div>
                            <div class="compare-val text-success fs-5">{{ number_format($dataY2['totalRevenue'] ?? 0, 3) }}
                            </div>
                        </div>

                        <div class="comparison-header text-danger mt-4"><i class="mdi mdi-cash-minus me-1"></i> مصروفات
                            وتكاليف التشغيل</div>

                        <div class="compare-row">
                            <div class="compare-label text-start"><i
                                    class="mdi mdi-account-group-outline me-1 fs-5 text-danger align-middle"></i> الرواتب
                                والأجور</div>
                            <div
                                class="compare-val val-b1 {{ ($dataY1['payrolls'] ?? 0) < ($dataY2['payrolls'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataY1['payrolls'] ?? 0, 3) }}
                            </div>
                            <div
                                class="compare-val val-b2 {{ ($dataY2['payrolls'] ?? 0) < ($dataY1['payrolls'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataY2['payrolls'] ?? 0, 3) }}
                            </div>
                        </div>
                        <div class="compare-row">
                            <div class="compare-label text-start"><i
                                    class="mdi mdi-cart-outline me-1 fs-5 text-info align-middle"></i> المشتريات</div>
                            <div
                                class="compare-val val-b1 {{ ($dataY1['purchases'] ?? 0) < ($dataY2['purchases'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataY1['purchases'] ?? 0, 3) }}
                            </div>
                            <div
                                class="compare-val val-b2 {{ ($dataY2['purchases'] ?? 0) < ($dataY1['purchases'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataY2['purchases'] ?? 0, 3) }}
                            </div>
                        </div>
                        <div class="compare-row bg-light rounded-3 px-2 mt-2 border-0 shadow-sm">
                            <div class="compare-label text-danger fs-6 text-start"><i class="mdi mdi-sigma me-1"></i> إجمالي
                                المصروفات</div>
                            <div class="compare-val text-danger fs-5">{{ number_format($dataY1['totalExpenses'] ?? 0, 3) }}
                            </div>
                            <div class="compare-val text-danger fs-5">{{ number_format($dataY2['totalExpenses'] ?? 0, 3) }}
                            </div>
                        </div>

                        <div class="comparison-header text-primary mt-4"><i class="mdi mdi-scale-balance me-1"></i> الخلاصة
                            المالية (المركز المالي)</div>

                        <div
                            class="compare-row bg-primary bg-opacity-10 rounded-3 px-3 border-0 py-4 shadow-sm position-relative">
                            <div class="compare-label text-primary fs-5 text-start fw-black">صافي الربح</div>

                            <div class="compare-val text-dark fs-4 position-relative">
                                {{ number_format($dataY1['netProfit'] ?? 0, 3) }}
                                @if ($winner == 'y1')
                                    <i class="mdi mdi-crown winner-crown"></i>
                                @endif
                            </div>

                            <div class="compare-val text-primary fs-4 position-relative">
                                {{ number_format($dataY2['netProfit'] ?? 0, 3) }}
                                @if ($winner == 'y2')
                                    <i class="mdi mdi-crown winner-crown"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🌟 الرسم البياني المزدوج --}}
                <div class="col-xl-5">
                    <div class="card card-premium h-100 p-4 text-center">
                        <h6 class="fw-bold text-dark mb-4 border-bottom pb-3"><i
                                class="mdi mdi-chart-areaspline me-1 text-primary"></i> المقارنة البصرية الشاملة</h6>
                        <div style="height: 300px; width: 100%;">
                            <canvas id="comparisonChart"></canvas>
                        </div>
                        <div class="mt-4 text-start">
                            @if (($profitGrowth ?? 0) > 0)
                                <div class="alert alert-success border-0 fw-bold font-13 shadow-sm mb-0">
                                    <i class="mdi mdi-lightbulb-on-outline me-1 fs-5 align-middle"></i> استنتاج: أداء مالي
                                    ممتاز! هناك نمو ملحوظ في أرباح المدرسة بنسبة {{ number_format($profitGrowth, 1) }}%
                                    مقارنة بالعام الماضي.
                                </div>
                            @elseif (($profitGrowth ?? 0) < 0)
                                <div class="alert alert-danger border-0 fw-bold font-13 shadow-sm mb-0">
                                    <i class="mdi mdi-alert-outline me-1 fs-5 align-middle"></i> تنبيه: يوجد تراجع في صافي
                                    أرباح المدرسة بنسبة {{ number_format(abs($profitGrowth), 1) }}%. يجب مراجعة بنود
                                    المصروفات وتحفيز التسجيل.
                                </div>
                            @else
                                <div class="alert alert-secondary border-0 fw-bold font-13 shadow-sm mb-0">
                                    <i class="mdi mdi-minus-circle-outline me-1 fs-5 align-middle"></i> استقرار مالي: لا
                                    يوجد تغير ملحوظ في صافي الأرباح بين العامين المحددين.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info text-center fw-bold fs-5 py-5 border-0 shadow-sm rounded-4 bg-white mt-4">
                <i class="mdi mdi-chart-bell-curve-cumulative fs-1 d-block mb-3 text-primary opacity-50"></i>
                يرجى اختيار سنتين ماليتين من الفلتر أعلاه لإجراء المقارنة وعرض التقرير التحليلي.
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    {{-- ✅ تم نقل Chart.js إلى هنا --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ✅ تأمين الفورم ضد الضغط المتكرر (باستخدام Vanilla JS بدلاً من jQuery)
            const form = document.querySelector('.prevent-double-submit');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('.btn-submit');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري التحليل...';
                });
            }

            @if (isset($dataY1) && isset($dataY2))
                const canvas = document.getElementById('comparisonChart');
                if (canvas) {
                    // 🌙 كشف الوضع الليلي لتكييف ألوان الشارت
                    const isDarkMode = document.body.getAttribute('data-layout-color') === 'dark';
                    const textColor = isDarkMode ? '#adb5bd' : '#475569';
                    const gridColor = isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

                    Chart.defaults.font.family = "'Segoe UI', Tahoma, sans-serif";
                    Chart.defaults.font.weight = 'bold';

                    new Chart(canvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['إجمالي الإيرادات', 'إجمالي المصروفات', 'صافي الربح'],
                            datasets: [{
                                    label: 'عام {{ $year1 ?? '' }}',
                                    backgroundColor: '#94a3b8',
                                    borderRadius: 6,
                                    data: [
                                        {{ $dataY1['totalRevenue'] ?? 0 }},
                                        {{ $dataY1['totalExpenses'] ?? 0 }},
                                        {{ $dataY1['netProfit'] ?? 0 }}
                                    ]
                                },
                                {
                                    label: 'عام {{ $year2 ?? '' }}',
                                    backgroundColor: '#3b82f6',
                                    borderRadius: 6,
                                    data: [
                                        {{ $dataY2['totalRevenue'] ?? 0 }},
                                        {{ $dataY2['totalExpenses'] ?? 0 }},
                                        {{ $dataY2['netProfit'] ?? 0 }}
                                    ]
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        color: textColor
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: textColor,
                                        font: {
                                            weight: 'bold'
                                        }
                                    },
                                    grid: {
                                        borderDash: [5, 5],
                                        color: gridColor
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: textColor,
                                        font: {
                                            weight: 'bold'
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }
            @endif
        });
    </script>
@endpush
