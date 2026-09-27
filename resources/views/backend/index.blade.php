@extends('backend.layouts.master')
@section('title', 'لوحة التحكم الرئيسية')
@section('title_page', 'نظرة عامة والتحليل الشامل')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .widget-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            background-color: #fff;
        }

        .widget-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .widget-icon {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            font-size: 28px;
            transition: 0.3s;
        }

        .widget-card:hover .widget-icon {
            transform: scale(1.1) rotate(5deg);
        }

        /* 🌟 Progress Bar Tweaks 🌟 */
        .progress {
            background-color: #e2e8f0;
            border-radius: 50px;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        /* 🌟 Animations 🌟 */
        .pulse-alert {
            animation: pulse-border 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-border {
            0% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5);
            }

            70% {
                box-shadow: 0 0 0 12px rgba(245, 158, 11, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            }
        }

        /* 🌟 Dark Mode Support 🌟 */
        body[data-layout-color="dark"] .widget-card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-white {
            background-color: #323a46 !important;
        }

        body[data-layout-color="dark"] .text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .progress {
            background-color: #424e5a;
        }

        /* تأمين الرسوم البيانية في الوضع الليلي */
        body[data-layout-color="dark"] .apexcharts-tooltip {
            background: #323a46 !important;
            border: 1px solid #424e5a !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        body[data-layout-color="dark"] .apexcharts-tooltip-title {
            background: #3b4652 !important;
            border-bottom: 1px solid #424e5a !important;
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .apexcharts-text tspan {
            fill: #adb5bd;
        }

        body[data-layout-color="dark"] .apexcharts-gridline {
            stroke: #424e5a;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 رأس الصفحة 🌟 --}}
        <div class="row mb-4 mt-2">
            <div class="col-12">
                <h4 class="page-title fw-bold text-dark mb-1"><i
                        class="bx bx-home-smile text-primary me-1 fs-3 align-middle"></i> اللوحة الإدارية الموحدة</h4>
                <p class="text-muted font-14 mb-0">نظرة عامة وإحصائيات فورية لأداء المدرسة، العمليات المالية، والتشغيلية.
                </p>
            </div>
        </div>

        {{-- 🔥 1. الكروت الذكية (KPIs) 🔥 --}}
        <div class="row g-4 mb-5">

            {{-- كارت الإيرادات والمصروفات (مدمج) --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card widget-card h-100 border-top border-success border-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <p class="text-muted fw-bold mb-2 font-13">حركة الخزينة اليوم (إيرادات / منصرف)</p>
                                <h4 class="text-success fw-bolder mb-1 font-monospace">+
                                    {{ number_format($todayRevenue ?? 0, 3) }} OMR</h4>
                                <h5 class="text-danger fw-bold mb-0 font-monospace">-
                                    {{ number_format($todayExpenses ?? 0, 3) }} OMR</h5>
                            </div>
                            <div
                                class="widget-icon bg-success bg-opacity-10 text-success border border-success border-opacity-25 shadow-sm">
                                <i class="bx bx-wallet-alt"></i>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php
                                $totalCash = ($todayRevenue ?? 0) + ($todayExpenses ?? 0);
                                $revPercent = $totalCash > 0 ? (($todayRevenue ?? 0) / $totalCash) * 100 : 0;
                                $expPercent = $totalCash > 0 ? (($todayExpenses ?? 0) / $totalCash) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $revPercent }}%"
                                title="الإيرادات: {{ round($revPercent) }}%"></div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $expPercent }}%"
                                title="المصروفات: {{ round($expPercent) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- كارت الطلاب --}}
            <div class="col-xl-3 col-lg-6">
                <div class="card widget-card h-100 border-top border-primary border-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="text-muted fw-bold mb-2 font-13">إجمالي الطلاب المقيدين</p>
                                <h3 class="text-primary fw-bolder mb-2 font-monospace">
                                    {{ number_format($totalActiveStudents ?? 0) }}</h3>
                            </div>
                            <div
                                class="widget-icon bg-opacity-10 text-primary border border-primary border-opacity-25 shadow-sm">
                                <i class="bx bxs-graduation"></i>
                            </div>
                        </div>
                        <span
                            class="badge bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill shadow-sm border border-primary border-opacity-25">
                            <i class="bx bx-trending-up me-1"></i> +{{ $newStudentsToday ?? 0 }} تسجيل جديد اليوم
                        </span>
                    </div>
                </div>
            </div>

            {{-- كارت طلبات الاستئذان (تنبيهات إدارية) --}}
            <div class="col-xl-2 col-md-6">
                <div
                    class="card widget-card h-100 {{ ($pendingLeaves ?? 0) > 0 ? 'border-top border-warning border-4' : 'border-top border-secondary border-opacity-25 border-4' }}">
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                        <div
                            class="widget-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3 border border-warning border-opacity-25 shadow-sm {{ ($pendingLeaves ?? 0) > 0 ? 'pulse-alert' : '' }}">
                            <i class="bx bx-envelope-open"></i>
                        </div>
                        <h3 class="fw-bolder text-dark mb-1 font-monospace">{{ $pendingLeaves ?? 0 }}</h3>
                        <p class="text-muted fw-bold mb-0 font-12">استئذانات معلقة</p>
                        @if (($pendingLeaves ?? 0) > 0)
                            <a href="{{ route('admin.leave_requests.index') }}" class="stretched-link"></a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- كارت نواقص المخزن (تنبيهات تشغيلية) --}}
            <div class="col-xl-3 col-md-6">
                <div
                    class="card widget-card h-100 {{ ($lowStockCount ?? 0) > 0 ? 'bg-danger border-0 shadow-lg' : 'border-top border-secondary border-opacity-25 border-4' }}">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <p
                                class="{{ ($lowStockCount ?? 0) > 0 ? 'text-white opacity-75' : 'text-muted' }} fw-bold mb-2 font-13">
                                تنبيهات نواقص المستودع</p>
                            <h2
                                class="fw-bolder mb-0 font-monospace {{ ($lowStockCount ?? 0) > 0 ? 'text-white' : 'text-danger' }}">
                                {{ $lowStockCount ?? 0 }} <span class="fs-6 fw-bold font-sans">صنف</span>
                            </h2>
                        </div>
                        <div
                            class="widget-icon {{ ($lowStockCount ?? 0) > 0 ? 'bg-white bg-opacity-25 text-white shadow-sm' : 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 shadow-sm' }}">
                            <i class="bx bx-package"></i>
                        </div>
                        @if (($lowStockCount ?? 0) > 0)
                            <a href="{{ route('admin.reports.inventory_shortages') }}" class="stretched-link"></a>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- 🔥 2. المخططات البيانية (Charts) 🔥 --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div
                        class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0"><i
                                class="bx bx-bar-chart-square text-primary me-2 fs-4 align-middle"></i> مؤشر التدفق النقدي
                            (آخر 7 أيام)</h5>
                    </div>
                    <div class="card-body px-2 pb-4">
                        <div id="cashFlowChart" style="height: 380px;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- استدعاء مكتبة ApexCharts لرسومات بيانية احترافية --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {
            // التحقق من الوضع الحالي للنظام لضبط ألوان الرسم البياني
            var isDarkMode = document.body.getAttribute('data-layout-color') === 'dark';
            var textColor = isDarkMode ? '#adb5bd' : '#64748b';
            var gridColor = isDarkMode ? '#424e5a' : '#e2e8f0';

            var options = {
                series: [{
                    name: 'الإيرادات المحصلة',
                    data: @json($chartRevenues ?? [])
                }, {
                    name: 'المصروفات',
                    data: @json($chartExpenses ?? [])
                }],
                chart: {
                    type: 'area',
                    height: 380,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Segoe UI, Tahoma, sans-serif',
                    background: 'transparent' // لجعل الخلفية شفافة وتأخذ لون الكارت
                },
                theme: {
                    mode: isDarkMode ? 'dark' : 'light'
                },
                colors: ['#10b981', '#ef4444'], // أخضر للإيرادات، أحمر للمصروفات
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: @json($chartDates ?? []),
                    labels: {
                        style: {
                            colors: textColor,
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return value.toLocaleString() + " OMR";
                        },
                        style: {
                            colors: textColor,
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    }
                },
                grid: {
                    borderColor: gridColor,
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    },
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                },
                tooltip: {
                    theme: isDarkMode ? 'dark' : 'light',
                    y: {
                        formatter: function(val) {
                            return val.toLocaleString() + " OMR";
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: {
                        colors: textColor
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#cashFlowChart"), options);
            chart.render();
        });
    </script>
@endpush
