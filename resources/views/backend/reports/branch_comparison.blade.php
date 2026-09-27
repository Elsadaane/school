@extends('backend.layouts.master')
@section('title', 'مقارنة أداء الفروع')
@section('title_page', 'التقارير التحليلية - تحدي الفروع')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
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

        .compare-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .compare-label {
            font-weight: 800;
            color: #475569;
            font-size: 14px;
            text-align: center;
            width: 30%;
        }

        .compare-val {
            font-family: 'Courier New', monospace;
            font-weight: 900;
            font-size: 17px;
            width: 35%;
            padding: 10px;
            border-radius: 8px;
        }

        .val-b1 {
            text-align: right;
            color: #0284c7;
            background: #f0f9ff;
        }

        .val-b2 {
            text-align: left;
            color: #9333ea;
            background: #faf5ff;
        }

        .val-winner {
            background: #d1fae5 !important;
            color: #059669 !important;
            border: 1px solid #10b981;
        }

        .val-loser {
            opacity: 0.6;
        }

        .header-bg {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 30px;
            border-radius: 16px;
            position: relative;
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
        }

        body[data-layout-color="dark"] .vs-circle {
            border-color: #323a46;
        }

        body[data-layout-color="dark"] .compare-row {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .compare-label {
            color: #cbd5e1;
        }

        body[data-layout-color="dark"] .val-b1 {
            background: rgba(2, 132, 199, 0.1);
        }

        body[data-layout-color="dark"] .val-b2 {
            background: rgba(147, 51, 234, 0.1);
        }

        body[data-layout-color="dark"] .val-winner {
            background: rgba(16, 185, 129, 0.15) !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        /* 🌙 دعم القائمة المنسدلة لـ Select2 في الوضع الليلي */
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-dropdown {
            background-color: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-results__option {
            color: #fff;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-results__option--highlighted,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #424e5a !important;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        {{-- 🌟 فلتر اختيار الفروع --}}
        <div class="card card-premium mb-4 border-top border-primary border-4 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.branch_comparison') }}" method="GET" id="compareForm"
                    class="row g-3 align-items-end justify-content-center prevent-double-submit">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase text-info">الفرع الأول
                            (الأزرق)</label>
                        <select name="branch1" class="form-select select2-init bg-light border-0 fw-bold" required>
                            <option value="" disabled selected>-- اختر الفرع --</option>
                            @foreach ($branches ?? [] as $b)
                                <option value="{{ $b->id }}" {{ ($branch1 ?? '') == $b->id ? 'selected' : '' }}>
                                    {{ $b->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 text-center">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">تحديد السنة</label>
                        <select name="year" class="form-select bg-dark text-white border-0 fw-bold text-center">
                            @foreach ($availableYears ?? [] as $y)
                                <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase" style="color: #9333ea;">الفرع
                            الثاني (البنفسجي)</label>
                        <select name="branch2" class="form-select select2-init bg-light border-0 fw-bold" required>
                            <option value="" disabled selected>-- اختر الفرع --</option>
                            @foreach ($branches ?? [] as $b)
                                <option value="{{ $b->id }}" {{ ($branch2 ?? '') == $b->id ? 'selected' : '' }}>
                                    {{ $b->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm rounded-3 btn-submit py-2">
                            <i class="mdi mdi-sword-cross me-1"></i> إطلاق التحدي
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if (isset($branch1) && isset($branch2) && $branch1 != $branch2)
            {{-- 🌟 كارت الفائز --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div
                        class="header-bg shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-4 text-center">
                        <div class="flex-grow-1">
                            <h4 class="text-white fw-bold mb-1">الفرع الأعلى ربحية لعام {{ $year }}</h4>
                            @if (isset($winner) && $winner)
                                <div class="display-6 fw-bolder text-warning mt-2">
                                    <i class="mdi mdi-trophy-variant"></i>
                                    {{ $winner == 'b1' ? $b1Name ?? 'الفرع الأول' : $b2Name ?? 'الفرع الثاني' }}
                                </div>
                                <p class="text-white-50 mt-2 font-15">
                                    يتفوق بصافي ربح قدره:
                                    <span
                                        class="text-success fw-bold font-monospace">{{ number_format($profitDiff ?? 0, 3) }}
                                        OMR</span>
                                </p>
                            @else
                                <div class="display-6 fw-bolder text-white mt-2">تعادل مالي!</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                {{-- 🌟 جدول المقارنة التفصيلي (VS Mode) --}}
                <div class="col-xl-7 position-relative">
                    <div class="vs-circle d-none d-xl-flex">VS</div>
                    <div class="card card-premium h-100 p-4">
                        <div class="d-flex justify-content-between mb-4 border-bottom pb-3">
                            <div class="text-info fw-bolder fs-5 position-relative">
                                {{ $b1Name ?? 'الفرع الأول' }}
                                @if (isset($winner) && $winner == 'b1')
                                    <i class="mdi mdi-crown winner-crown"></i>
                                @endif
                            </div>
                            <div class="fw-bolder fs-5 position-relative" style="color: #9333ea;">
                                {{ $b2Name ?? 'الفرع الثاني' }}
                                @if (isset($winner) && $winner == 'b2')
                                    <i class="mdi mdi-crown winner-crown"></i>
                                @endif
                            </div>
                        </div>

                        {{-- الإيرادات والأقساط --}}
                        <div class="compare-row">
                            <div
                                class="compare-val val-b1 {{ ($dataB1['contracts'] ?? 0) > ($dataB2['contracts'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataB1['contracts'] ?? 0, 3) }}
                            </div>
                            <div class="compare-label"><i class="mdi mdi-school-outline d-block fs-4 text-primary"></i>
                                أقساط الطلاب</div>
                            <div
                                class="compare-val val-b2 {{ ($dataB2['contracts'] ?? 0) > ($dataB1['contracts'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataB2['contracts'] ?? 0, 3) }}
                            </div>
                        </div>

                        <div class="compare-row">
                            <div
                                class="compare-val val-b1 {{ ($dataB1['storeSales'] ?? 0) > ($dataB2['storeSales'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataB1['storeSales'] ?? 0, 3) }}
                            </div>
                            <div class="compare-label"><i class="mdi mdi-store-outline d-block fs-4 text-warning"></i>
                                مبيعات المقصف</div>
                            <div
                                class="compare-val val-b2 {{ ($dataB2['storeSales'] ?? 0) > ($dataB1['storeSales'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataB2['storeSales'] ?? 0, 3) }}
                            </div>
                        </div>

                        <div class="compare-row bg-light rounded-3 px-2 mt-2 border-0 shadow-sm">
                            <div class="compare-val val-b1 fw-bolder fs-5 text-dark bg-transparent">
                                {{ number_format($dataB1['totalRevenue'] ?? 0, 3) }}
                            </div>
                            <div class="compare-label text-success fs-6">إجمالي الإيرادات</div>
                            <div class="compare-val val-b2 fw-bolder fs-5 text-dark bg-transparent">
                                {{ number_format($dataB2['totalRevenue'] ?? 0, 3) }}
                            </div>
                        </div>

                        <div class="my-4"></div>

                        {{-- المصروفات --}}
                        <div class="compare-row">
                            <div
                                class="compare-val val-b1 {{ ($dataB1['payrolls'] ?? 0) < ($dataB2['payrolls'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataB1['payrolls'] ?? 0, 3) }}
                            </div>
                            <div class="compare-label"><i
                                    class="mdi mdi-account-group-outline d-block fs-4 text-danger"></i> الرواتب والأجور
                            </div>
                            <div
                                class="compare-val val-b2 {{ ($dataB2['payrolls'] ?? 0) < ($dataB1['payrolls'] ?? 0) ? 'val-winner' : 'val-loser' }}">
                                {{ number_format($dataB2['payrolls'] ?? 0, 3) }}
                            </div>
                        </div>

                        <div class="compare-row bg-light rounded-3 px-2 mt-2 border-0 shadow-sm">
                            <div class="compare-val val-b1 fw-bolder fs-5 text-dark bg-transparent">
                                {{ number_format($dataB1['totalExpenses'] ?? 0, 3) }}
                            </div>
                            <div class="compare-label text-danger fs-6">إجمالي المصروفات</div>
                            <div class="compare-val val-b2 fw-bolder fs-5 text-dark bg-transparent">
                                {{ number_format($dataB2['totalExpenses'] ?? 0, 3) }}
                            </div>
                        </div>

                        <div class="my-4"></div>

                        {{-- صافي الربح --}}
                        <div class="compare-row bg-dark rounded-3 px-3 border-0 py-4 shadow">
                            <div
                                class="compare-val text-white bg-transparent fs-4 {{ isset($winner) && $winner == 'b1' ? 'text-warning' : '' }}">
                                {{ number_format($dataB1['netProfit'] ?? 0, 3) }}
                            </div>
                            <div class="compare-label text-white fs-5 opacity-75">صافي الربح</div>
                            <div
                                class="compare-val text-white bg-transparent fs-4 {{ isset($winner) && $winner == 'b2' ? 'text-warning' : '' }}">
                                {{ number_format($dataB2['netProfit'] ?? 0, 3) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🌟 الرسم البياني --}}
                <div class="col-xl-5">
                    <div class="card card-premium h-100 p-4 text-center">
                        <h6 class="fw-bold text-dark mb-4 border-bottom pb-2"><i
                                class="mdi mdi-chart-bar me-1 text-primary"></i> مقارنة المركز المالي بيانياً</h6>
                        <div style="height: 350px; width: 100%;">
                            <canvas id="branchComparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(request()->filled('branch1'))
            <div class="alert alert-warning fw-bold border-0 shadow-sm rounded-3">
                <i class="mdi mdi-alert-circle-outline me-1 fs-5 align-middle"></i> يرجى اختيار فرعين مختلفين لإجراء
                المقارنة بشكل صحيح.
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    {{-- ✅ تم نقل Chart.js إلى هنا لتسريع تحميل الصفحة وتجنب أخطاء الـ Canvas --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // تفعيل Select2
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
            });

            // تأمين الفورم ضد الضغط المتكرر
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري المقارنة...');
            });

            @if (isset($branch1) && isset($branch2) && $branch1 != $branch2)
                const canvas = document.getElementById('branchComparisonChart');
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
                                    label: '{{ $b1Name ?? 'الفرع الأول' }}',
                                    backgroundColor: '#0ea5e9',
                                    borderRadius: 6,
                                    data: [
                                        {{ $dataB1['totalRevenue'] ?? 0 }},
                                        {{ $dataB1['totalExpenses'] ?? 0 }},
                                        {{ $dataB1['netProfit'] ?? 0 }}
                                    ]
                                },
                                {
                                    label: '{{ $b2Name ?? 'الفرع الثاني' }}',
                                    backgroundColor: '#9333ea',
                                    borderRadius: 6,
                                    data: [
                                        {{ $dataB2['totalRevenue'] ?? 0 }},
                                        {{ $dataB2['totalExpenses'] ?? 0 }},
                                        {{ $dataB2['netProfit'] ?? 0 }}
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
