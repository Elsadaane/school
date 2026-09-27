@extends('backend.layouts.master')
@section('title', 'إحصائيات الحضور والغياب')
@section('title_page', 'شؤون الطلاب - تقارير الحضور والمقارنات')

@push('css')
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

        .stat-card {
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fff;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .stat-value {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 5px;
        }

        .stat-title {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            margin: 0;
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
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        .trend-up {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 800;
        }

        .trend-down {
            color: #10b981;
            background: rgba(16, 185, 129, 0.1);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 800;
        }

        /* Dark Mode */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .stat-card {
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

        body[data-layout-color="dark"] .stat-title {
            color: #cbd5e1;
        }

        body[data-layout-color="dark"] .stat-value,
        body[data-layout-color="dark"] .fw-bold.text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        {{-- 🌟 فلتر الإحصائيات والمقارنة --}}
        <div class="card card-premium mb-4 border-top border-primary border-4 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.attendance') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-12 mb-2">
                        <h6 class="fw-bold text-dark"><i
                                class="mdi mdi-filter-variant me-1 text-primary fs-5 align-middle"></i> إعدادات التقرير
                            والمقارنة الزمانية</h6>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">حضور الفترة من</label>
                        <input type="date" name="start_date" class="form-control bg-light border-0 fw-bold text-center"
                            value="{{ request('start_date', $startDate ?? date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">إلى تاريخ</label>
                        <input type="date" name="end_date" class="form-control bg-light border-0 fw-bold text-center"
                            value="{{ request('end_date', $endDate ?? date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2 border-start">
                        <label class="form-label fw-bold text-info font-12 text-uppercase">مقارنة بـ عام</label>
                        <select name="compare_year1"
                            class="form-select bg-info bg-opacity-10 text-info fw-bold border-0 text-center">
                            @foreach ($availableYears ?? [] as $y)
                                <option value="{{ $y }}" {{ ($compareYear1 ?? '') == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold font-12 text-uppercase" style="color: #9333ea;">ضد عام</label>
                        <select name="compare_year2" class="form-select fw-bold border-0 text-center"
                            style="background-color: #faf5ff; color: #9333ea;">
                            @foreach ($availableYears ?? [] as $y)
                                <option value="{{ $y }}" {{ ($compareYear2 ?? '') == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100 fw-bold shadow-sm rounded-3 py-2"><i
                                class="mdi mdi-magnify me-1"></i> تحليل وبحث</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 كروت ملخص الفترة المحددة --}}
        <h5 class="fw-bold text-dark mb-3"><i class="mdi mdi-calendar-clock text-primary me-1 fs-4 align-middle"></i>
            إحصائيات الفترة ({{ $startDate ?? '---' }} إلى {{ $endDate ?? '---' }})</h5>

        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-start border-success border-4">
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i
                            class="mdi mdi-account-check-outline"></i></div>
                    <div>
                        <div class="stat-value text-dark">
                            {{ number_format($present ?? 0) }}
                            @if (($totalRecords ?? 0) > 0)
                                <span
                                    class="badge bg-success bg-opacity-10 text-success fs-6 align-middle ms-1">{{ round(($present / $totalRecords) * 100, 1) }}%</span>
                            @endif
                        </div>
                        <p class="stat-title">إجمالي الحضور</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-start border-danger border-4">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i
                            class="mdi mdi-account-remove-outline"></i></div>
                    <div>
                        <div class="stat-value text-dark">
                            {{ number_format($absent ?? 0) }}
                            @if (($totalRecords ?? 0) > 0)
                                <span
                                    class="badge bg-danger bg-opacity-10 text-danger fs-6 align-middle ms-1">{{ round(($absent / $totalRecords) * 100, 1) }}%</span>
                            @endif
                        </div>
                        <p class="stat-title">إجمالي الغياب</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-start border-warning border-4">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i
                            class="mdi mdi-account-clock-outline"></i></div>
                    <div>
                        <div class="stat-value text-dark">{{ number_format($late ?? 0) }}</div>
                        <p class="stat-title">حالات التأخير</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-start border-info border-4">
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="mdi mdi-account-question-outline"></i>
                    </div>
                    <div>
                        <div class="stat-value text-dark">{{ number_format($excused ?? 0) }}</div>
                        <p class="stat-title">غياب بعذر (طبي/إذن)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- 🌟 رادار الهروب (أكثر الطلاب غياباً) --}}
            <div class="col-xl-5">
                <div class="card card-premium h-100">
                    <div
                        class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center rounded-top-4">
                        <h5 class="fw-bold text-danger mb-0"><i class="mdi mdi-radar me-1"></i> رادار الغياب المتكرر</h5>
                        <span class="badge bg-danger rounded-pill shadow-sm">أعلى 10 طلاب</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                            <table class="table table-erp text-center mb-0 align-middle">
                                <thead class="table-light sticky-top" style="z-index: 1;">
                                    <tr>
                                        <th class="text-start ps-4">اسم الطالب</th>
                                        <th>الفرع / المدرسة</th>
                                        <th>أيام الغياب</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topAbsentees ?? [] as $student)
                                        <tr>
                                            <td class="text-start ps-4 fw-bold text-dark">
                                                <i class="mdi mdi-account-alert text-danger me-1"></i>
                                                {{ $student->first_name }} {{ $student->tribe }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-light text-dark border px-2 py-1 shadow-sm">{{ $student->branch_name ?? '---' }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger fw-bold fs-6 font-monospace px-3 py-1 shadow-sm">
                                                    {{ $student->absent_days }} يوم
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-5 text-success fw-bold text-center">
                                                <i class="mdi mdi-shield-check-outline fs-1 d-block mb-2 opacity-50"></i>
                                                ممتاز! لا توجد حالات غياب متكرر في هذه الفترة.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🌟 مقارنة المدارس والسنوات --}}
            <div class="col-xl-7">
                <div class="card card-premium h-100">
                    <div class="card-header bg-white border-bottom p-4 rounded-top-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="mdi mdi-compare me-1 text-primary"></i> تحليل تسرب
                            وغياب الطلاب بالمدارس (مقارنة سنوية)</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive border rounded-3 mb-4 shadow-sm"
                            style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-erp text-center mb-0 align-middle">
                                <thead class="table-light sticky-top" style="z-index: 1;">
                                    <tr>
                                        <th class="text-start ps-4">الفرع / المدرسة</th>
                                        <th class="text-info">الغياب ({{ $compareYear1 ?? '-' }})</th>
                                        <th style="color: #9333ea;">الغياب ({{ $compareYear2 ?? '-' }})</th>
                                        <th>مؤشر الأداء (التغير)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($comparisonData ?? [] as $data)
                                        <tr>
                                            <td class="text-start ps-4 fw-bold text-dark">{{ $data['branch_name'] }}</td>
                                            <td class="font-monospace fs-5 text-info fw-bold">
                                                {{ number_format($data['absent_y1']) }}</td>
                                            <td class="font-monospace fs-5 fw-bold" style="color: #9333ea;">
                                                {{ number_format($data['absent_y2']) }}</td>
                                            <td>
                                                @if ($data['difference'] > 0)
                                                    <span class="trend-up shadow-sm"
                                                        title="معدل الغياب زاد، مؤشر سلبي للمدرسة">
                                                        <i
                                                            class="mdi mdi-trending-up me-1"></i>{{ number_format(abs($data['difference']), 1) }}%
                                                    </span>
                                                @elseif($data['difference'] < 0)
                                                    <span class="trend-down shadow-sm"
                                                        title="انخفض معدل الغياب، مؤشر إيجابي للمدرسة">
                                                        <i
                                                            class="mdi mdi-trending-down me-1"></i>{{ number_format(abs($data['difference']), 1) }}%
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 border shadow-sm">استقرار
                                                        (0%)
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted py-4 text-center">لا توجد بيانات مقارنة
                                                متاحة للفروع المحددة.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div style="height: 250px; width: 100%;">
                            <canvas id="absenceComparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Chart.defaults.font.family = "'Segoe UI', Tahoma, sans-serif";
            Chart.defaults.font.weight = 'bold';

            // 🛠️ كشف الوضع الليلي لتكييف ألوان الشارت
            const isDarkMode = document.body.getAttribute('data-layout-color') === 'dark';
            const textColor = isDarkMode ? '#adb5bd' : '#475569';
            const gridColor = isDarkMode ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

            const labels = {!! json_encode(collect($comparisonData ?? [])->pluck('branch_name')) !!};
            const dataY1 = {!! json_encode(collect($comparisonData ?? [])->pluck('absent_y1')) !!};
            const dataY2 = {!! json_encode(collect($comparisonData ?? [])->pluck('absent_y2')) !!};

            const canvas = document.getElementById('absenceComparisonChart');

            if (canvas && labels.length > 0) {
                new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'غياب عام {{ $compareYear1 ?? '' }}',
                                backgroundColor: '#0ea5e9',
                                borderRadius: 4,
                                data: dataY1
                            },
                            {
                                label: 'غياب عام {{ $compareYear2 ?? '' }}',
                                backgroundColor: '#9333ea',
                                borderRadius: 4,
                                data: dataY2
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
        });
    </script>
@endpush
