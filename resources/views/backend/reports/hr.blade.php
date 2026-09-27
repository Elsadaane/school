@extends('backend.layouts.master')
@section('title', 'تقرير الموارد البشرية والرواتب')
@section('title_page', 'شؤون الموظفين - الإحصائيات والرواتب')

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

        .hr-stat-card {
            border-radius: 12px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid #e2e8f0;
            background: #fff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hr-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
        }

        .hr-stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            flex-shrink: 0;
        }

        .hr-stat-details {
            flex-grow: 1;
        }

        .hr-stat-value {
            font-family: 'Courier New', Courier, monospace;
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 2px;
            letter-spacing: -0.5px;
        }

        .hr-stat-label {
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
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        .amount-text {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .hr-stat-card {
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

        body[data-layout-color="dark"] .hr-stat-value {
            color: #f8fafc;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        /* 🖨️ إعدادات الطباعة النظيفة 🖨️ */
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

            .table-responsive {
                overflow: visible !important;
                max-height: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 d-print-none">
            <h4 class="page-title font-bold mb-0 text-dark">
                <i class="mdi mdi-account-group text-primary me-1 fs-4 align-middle"></i> كشف مسير الرواتب والموارد البشرية
            </h4>
        </div>

        {{-- 🌟 الفلتر --}}
        <div class="card card-premium mb-4 border-top border-info border-4 no-print shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.hr') }}" method="GET"
                    class="row g-3 align-items-end justify-content-center prevent-double-submit">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">القسم / الإدارة</label>
                        <select name="department_id" class="form-select bg-light border-0 fw-bold shadow-sm">
                            <option value="all" {{ ($departmentId ?? 'all') == 'all' ? 'selected' : '' }}>جميع الأقسام
                            </option>
                            @foreach ($departments ?? [] as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ ($departmentId ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">الشهر</label>
                        <select name="month" class="form-select bg-light border-0 fw-bold shadow-sm text-center">
                            @for ($i = 1; $i <= 12; $i++)
                                @php $m = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                <option value="{{ $m }}" {{ ($month ?? date('m')) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }} ({{ $m }})
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">السنة</label>
                        <select name="year" class="form-select bg-light border-0 fw-bold shadow-sm text-center">
                            @for ($y = date('Y') + 1; $y >= 2022; $y--)
                                <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-info text-white w-100 fw-bold shadow-sm btn-submit">
                            <i class="mdi mdi-magnify me-1"></i> عرض الإحصائيات
                        </button>
                        <button type="button" class="btn btn-dark border fw-bold shadow-sm px-4" onclick="window.print()"
                            title="طباعة التقرير">
                            <i class="mdi mdi-printer"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 كروت المؤشرات (KPIs) --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="hr-stat-card shadow-sm border-start border-primary border-4">
                    <div class="hr-stat-icon bg-primary bg-opacity-10 text-primary"><i class="mdi mdi-account-tie"></i>
                    </div>
                    <div class="hr-stat-details">
                        <div class="hr-stat-value">{{ number_format($kpis['active_employees'] ?? 0) }}</div>
                        <p class="hr-stat-label">موظف على رأس العمل</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="hr-stat-card shadow-sm border-start border-success border-4">
                    <div class="hr-stat-icon bg-success bg-opacity-10 text-success"><i class="mdi mdi-cash-multiple"></i>
                    </div>
                    <div class="hr-stat-details">
                        <div class="hr-stat-value">{{ number_format($kpis['total_net_salaries'] ?? 0, 3) }}</div>
                        <p class="hr-stat-label">صافي الرواتب (المسددة)</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="hr-stat-card shadow-sm border-start border-danger border-4">
                    <div class="hr-stat-icon bg-danger bg-opacity-10 text-danger"><i class="mdi mdi-cash-minus"></i></div>
                    <div class="hr-stat-details">
                        <div class="hr-stat-value">{{ number_format($kpis['total_deductions'] ?? 0, 3) }}</div>
                        <p class="hr-stat-label">إجمالي الخصومات</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="hr-stat-card shadow-sm border-start border-warning border-4">
                    <div class="hr-stat-icon bg-warning bg-opacity-10 text-warning"><i class="mdi mdi-bank-transfer"></i>
                    </div>
                    <div class="hr-stat-details">
                        <div class="hr-stat-value">{{ number_format($kpis['remaining_loans'] ?? 0, 3) }}</div>
                        <p class="hr-stat-label">أرصدة السلف المتبقية</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 الرسوم البيانية --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-4">
                <div class="card card-premium h-100 p-4 text-center shadow-sm">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2"><i
                            class="mdi mdi-chart-donut text-primary me-1"></i> توزيع الكوادر على الأقسام</h6>
                    <div style="height: 250px;">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card card-premium h-100 p-4 text-center shadow-sm">
                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2"><i
                            class="mdi mdi-chart-bell-curve-cumulative text-info me-1"></i> مؤشر رواتب عام
                        {{ $year ?? date('Y') }} (Trend)</h6>
                    <div style="height: 250px;">
                        <canvas id="payrollTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 جدول الرواتب والموظفين --}}
        <div class="card card-premium shadow-sm">
            <div class="card-header bg-white border-bottom p-4 rounded-top-4">
                <h5 class="fw-bold text-dark mb-0"><i
                        class="mdi mdi-format-list-bulleted text-primary me-1 fs-4 align-middle"></i> كشف مسير الرواتب
                    المفصل لشهر ({{ $month ?? date('m') }}-{{ $year ?? date('Y') }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-erp text-center align-middle mb-0">
                        <thead class="sticky-top" style="z-index: 1;">
                            <tr>
                                <th>#</th>
                                <th class="text-start ps-4">اسم الموظف / المعلم</th>
                                <th>القسم</th>
                                <th>البدلات والمكافآت</th>
                                <th>الخصومات (والسلف)</th>
                                <th>الصافي المستحق</th>
                                <th>حالة الراتب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees ?? [] as $idx => $emp)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $idx + 1 }}</td>
                                    <td class="text-start ps-4 fw-bold text-dark">
                                        <i class="mdi mdi-account-circle-outline text-muted me-1 fs-5 align-middle"></i>
                                        {{-- ✅ التصحيح: استخدام full_name_ar بدلاً من name_ar --}}
                                        {{ $emp->full_name_ar ?? '---' }}
                                        <small
                                            class="text-muted d-block font-12 mt-1">{{ $emp->job_title ?? '---' }}</small>
                                    </td>
                                    <td><span
                                            class="badge bg-light text-dark border shadow-sm">{{ $emp->department->name_ar ?? 'بدون قسم' }}</span>
                                    </td>

                                    <td class="amount-text text-success">
                                        +{{ number_format($emp->current_allowances ?? 0, 3) }}</td>
                                    <td class="amount-text text-danger">
                                        -{{ number_format($emp->current_deductions ?? 0, 3) }}</td>
                                    <td class="amount-text text-primary fs-5 bg-light">
                                        {{ number_format($emp->current_net ?? 0, 3) }}</td>

                                    <td>
                                        @if ($emp->payroll_status == 'paid')
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill shadow-sm"><i
                                                    class="mdi mdi-check-all me-1"></i>مصروف</span>
                                            {{-- ✅ التصحيح: استخدام draft بدلاً من pending لتطابق قاعدة البيانات --}}
                                        @elseif($emp->payroll_status == 'draft' || $emp->payroll_status == 'approved')
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning text-dark px-3 py-1 rounded-pill shadow-sm"><i
                                                    class="mdi mdi-clock-outline me-1"></i>قيد المراجعة</span>
                                        @else
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill shadow-sm"><i
                                                    class="mdi mdi-progress-alert me-1"></i>لم يُولد بعد</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-5 text-muted fs-5 fw-bold bg-white text-center">
                                        <i
                                            class="mdi mdi-account-off-outline fs-1 d-block mb-2 opacity-50 text-danger"></i>
                                        لا توجد بيانات موظفين أو رواتب مسجلة لهذا القسم في هذا الشهر.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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

            // ✅ حماية الفورم من الإرسال المزدوج (Vanilla JS)
            const form = document.querySelector('.prevent-double-submit');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('.btn-submit');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري التصفية...';
                });
            }

            // 1. الدائرة المجوفة (توزيع الأقسام)
            const deptLabels = {!! json_encode($deptLabels ?? []) !!};
            const deptCounts = {!! json_encode($deptCounts ?? []) !!};

            const deptCanvas = document.getElementById('deptChart');
            if (deptCanvas && deptLabels.length > 0) {
                new Chart(deptCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: deptLabels,
                        datasets: [{
                            data: deptCounts,
                            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444',
                                '#64748b'
                            ],
                            borderWidth: 2,
                            borderColor: isDarkMode ? '#323a46' : '#ffffff',
                            hoverOffset: 5
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
                                    color: textColor
                                }
                            }
                        }
                    }
                });
            }

            // 2. الخط البياني (مؤشر الرواتب طوال السنة)
            const trendData = {!! json_encode($trendData ?? []) !!};

            const trendCanvas = document.getElementById('payrollTrendChart');
            if (trendCanvas && trendData.length > 0) {
                new Chart(trendCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس',
                            'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
                        ],
                        datasets: [{
                            label: 'إجمالي الرواتب المصروفة',
                            data: trendData,
                            borderColor: '#0ea5e9',
                            backgroundColor: 'rgba(14, 165, 233, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#0ea5e9',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
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
