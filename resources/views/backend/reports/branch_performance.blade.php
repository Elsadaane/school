@extends('backend.layouts.master')
@section('title', 'أداء الفروع الشامل')
@section('title_page', 'التقارير التحليلية - تقييم الفروع والمدارس')

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

        .perf-card {
            border-radius: 16px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fff;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s ease;
        }

        .perf-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
        }

        .perf-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            flex-shrink: 0;
        }

        .perf-details {
            flex-grow: 1;
        }

        .perf-value {
            font-family: 'Courier New', Courier, monospace;
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .perf-label {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            margin: 0;
            text-transform: uppercase;
        }

        .table-erp {
            width: 100%;
            margin-bottom: 0;
            white-space: nowrap;
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 15px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 700;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        .branch-name {
            font-family: 'Segoe UI', Tahoma, sans-serif !important;
            font-weight: 800;
            font-size: 15px;
        }

        .top-branch-badge {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #fff;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .perf-card {
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
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .table-erp tbody tr:hover td {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .perf-value {
            color: #f8fafc;
        }

        @media print {

            .no-print,
            .left-side-menu,
            .navbar-custom,
            .page-title-box,
            .footer {
                display: none !important;
            }

            body {
                background-color: #fff;
            }

            .card-premium {
                box-shadow: none !important;
                border: 1px solid #ddd;
            }

            .table-responsive {
                overflow: visible !important;
                max-height: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        {{-- 🌟 الفلتر --}}
        <div class="card card-premium mb-4 no-print border-top border-primary border-4 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.branch_performance') }}" method="GET"
                    class="row g-3 align-items-end justify-content-center">
                    @if (isset($isAdmin) && $isAdmin)
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted font-12 text-uppercase">اختر الفرع / المدرسة</label>
                            <select name="branch_id" class="form-select form-select-lg bg-light border-0 fw-bold shadow-sm">
                                <option value="all" {{ ($branchId ?? 'all') == 'all' ? 'selected' : '' }}>جميع الفروع
                                    (نظرة شاملة)</option>
                                @foreach ($branches ?? [] as $b)
                                    <option value="{{ $b->id }}" {{ ($branchId ?? '') == $b->id ? 'selected' : '' }}>
                                        {{ $b->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">من تاريخ</label>
                        <input type="date" name="from_date"
                            class="form-control form-control-lg bg-light border-0 shadow-sm text-center fw-bold"
                            value="{{ request('from_date', $startDate ?? date('Y-m-01')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">إلى تاريخ</label>
                        <input type="date" name="to_date"
                            class="form-control form-control-lg bg-light border-0 shadow-sm text-center fw-bold"
                            value="{{ request('to_date', $endDate ?? date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm rounded-3">
                            <i class="mdi mdi-magnify me-1"></i> عرض التقرير
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @php
            $totalNetworkRevenue = collect($branchesComparison ?? [])->sum('revenue');
            $totalNetworkExpenses = collect($branchesComparison ?? [])->sum('expenses');
            $totalNetworkNet = collect($branchesComparison ?? [])->sum('net');
        @endphp

        {{-- 🌟 كروت المؤشرات العليا للشبكة (Network KPIs) --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="perf-card shadow-sm border-start border-success border-4">
                    <div class="perf-icon bg-success bg-opacity-10 text-success"><i class="mdi mdi-cash-multiple"></i></div>
                    <div class="perf-details">
                        <div class="perf-value text-dark">{{ number_format($totalNetworkRevenue, 3) }}</div>
                        <p class="perf-label">إجمالي إيرادات الشبكة</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="perf-card shadow-sm border-start border-danger border-4">
                    <div class="perf-icon bg-danger bg-opacity-10 text-danger"><i class="mdi mdi-cash-minus"></i></div>
                    <div class="perf-details">
                        <div class="perf-value text-dark">{{ number_format($totalNetworkExpenses, 3) }}</div>
                        <p class="perf-label">إجمالي تكاليف ومصروفات</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="perf-card shadow-sm border-start border-primary border-4"
                    style="{{ $totalNetworkNet < 0 ? 'border-color: #ef4444 !important;' : '' }}">
                    <div
                        class="perf-icon {{ $totalNetworkNet >= 0 ? 'bg-primary bg-opacity-10 text-primary' : 'bg-danger bg-opacity-10 text-danger' }}">
                        <i class="mdi mdi-scale-balance"></i>
                    </div>
                    <div class="perf-details">
                        <div class="perf-value {{ $totalNetworkNet >= 0 ? 'text-primary' : 'text-danger' }}">
                            {{ number_format($totalNetworkNet, 3) }}
                        </div>
                        <p class="perf-label">صافي أرباح الشبكة</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="perf-card shadow-sm border-start border-warning border-4"
                    style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: #f59e0b !important;">
                    <div class="perf-icon bg-warning text-white shadow-sm"><i class="mdi mdi-trophy-award"></i></div>
                    <div class="perf-details">
                        {{-- ✅ تم استخدام data_get() لحماية الكود من أخطاء PHP 8 في حال كان $topBranch = null --}}
                        <div class="perf-value text-dark fs-5 font-sans text-truncate">
                            {{ data_get($topBranch, 'name', 'لا يوجد') }}</div>
                        <p class="perf-label text-warning" style="color: #d97706 !important;">الفرع الأفضل أداءً (النجم)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            {{-- 🌟 جدول أداء الفروع التفصيلي --}}
            <div class="col-xl-7">
                <div class="card card-premium h-100">
                    <div
                        class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center rounded-top-4">
                        <h5 class="fw-bold text-dark mb-0"><i
                                class="mdi mdi-format-list-bulleted text-primary me-1 fs-4 align-middle"></i> كشف الأداء
                            المالي لكل فرع</h5>
                        <button class="btn btn-sm btn-dark rounded-pill fw-bold shadow-sm px-3 no-print"
                            onclick="window.print()">
                            <i class="mdi mdi-printer me-1"></i> طباعة التقرير
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                            <table class="table table-erp text-center align-middle mb-0">
                                <thead class="sticky-top" style="z-index: 1;">
                                    <tr>
                                        <th>الترتيب</th>
                                        <th class="text-start ps-4">اسم الفرع / المدرسة</th>
                                        <th class="text-success">الإيرادات المحققة</th>
                                        <th class="text-danger">المصروفات الفعلية</th>
                                        <th class="bg-light">صافي الربح / العجز</th>
                                        <th>مؤشر الأداء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($branchesComparison ?? [] as $idx => $branch)
                                        @php
                                            $margin =
                                                $branch['revenue'] > 0
                                                    ? ($branch['net'] / $branch['revenue']) * 100
                                                    : 0;
                                        @endphp
                                        <tr>
                                            <td class="text-muted fw-bold">{{ $idx + 1 }}</td>
                                            <td class="text-start ps-4 branch-name text-dark">
                                                {{ $branch['name'] }}
                                                @if ($idx == 0 && $branch['net'] > 0)
                                                    <div class="mt-1"><span class="top-branch-badge"><i
                                                                class="mdi mdi-crown"></i> الفرع المتصدر</span></div>
                                                @endif
                                            </td>
                                            <td class="text-success">{{ number_format($branch['revenue'], 3) }}</td>
                                            <td class="text-danger">{{ number_format($branch['expenses'], 3) }}</td>
                                            <td
                                                class="bg-light {{ $branch['net'] >= 0 ? 'text-primary' : 'text-danger' }} fs-5">
                                                {{ number_format($branch['net'], 3) }}
                                            </td>
                                            <td>
                                                @if ($branch['net'] > 0)
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill shadow-sm">
                                                        <i class="mdi mdi-trending-up me-1"></i>هامش
                                                        {{ round($margin, 1) }}%
                                                    </span>
                                                @elseif($branch['net'] < 0)
                                                    <span
                                                        class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill shadow-sm">
                                                        <i class="mdi mdi-trending-down me-1"></i>عجز
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill shadow-sm">تعادل</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-5 text-muted fw-bold text-center">
                                                <i
                                                    class="mdi mdi-chart-box-outline fs-1 opacity-50 d-block mb-2 text-primary"></i>
                                                لا توجد حركات مالية مسجلة للفروع في هذه الفترة المحددة.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🌟 الرسم البياني المجمع --}}
            <div class="col-xl-5">
                <div class="card card-premium h-100 p-4 text-center">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">مقارنة بصرية (إيرادات مقابل مصروفات الفروع)</h6>
                    <div style="height: 380px; width: 100%;">
                        <canvas id="branchPerformanceChart"></canvas>
                    </div>
                </div>
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
            const gridColor = isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

            const branchLabels = {!! json_encode(collect($branchesComparison ?? [])->pluck('name')) !!};
            const branchRevenues = {!! json_encode(collect($branchesComparison ?? [])->pluck('revenue')) !!};
            const branchExpenses = {!! json_encode(collect($branchesComparison ?? [])->pluck('expenses')) !!};

            const canvas = document.getElementById('branchPerformanceChart');

            if (canvas && branchLabels.length > 0) {
                new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: branchLabels,
                        datasets: [{
                                label: 'الإيرادات',
                                backgroundColor: '#10b981',
                                borderRadius: 4,
                                data: branchRevenues
                            },
                            {
                                label: 'المصروفات',
                                backgroundColor: '#ef4444',
                                borderRadius: 4,
                                data: branchExpenses
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
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
        });
    </script>
@endpush
