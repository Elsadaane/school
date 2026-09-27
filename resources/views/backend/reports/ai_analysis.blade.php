@extends('backend.layouts.master')
@section('title', 'التحليل الذكي لمستوى الطلاب')

@push('css')
    <style>
        .ai-header {
            background: linear-gradient(135deg, #2b1055 0%, #7597de 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .ai-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 40%);
            transform: rotate(45deg);
            pointer-events: none;
        }

        .ai-card {
            border-radius: 16px;
            border: 1px solid rgba(142, 68, 173, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .ai-card:hover {
            transform: translateY(-5px);
        }

        .risk-badge {
            background-color: #ffeaea;
            color: #ff4d4f;
            border: 1px dashed #ff4d4f;
        }

        .star-badge {
            background-color: #fffbe6;
            color: #faad14;
            border: 1px dashed #faad14;
        }

        .table-custom {
            white-space: nowrap;
        }

        /* 🖨️ إعدادات الطباعة 🖨️ */
        @media print {
            body {
                background-color: #fff;
            }

            .btn,
            .page-title-box,
            .navbar-custom,
            .left-side-menu,
            .footer,
            .d-print-none {
                display: none !important;
            }

            .ai-card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                break-inside: avoid;
            }

            .ai-header {
                background: #f8f9fa !important;
                color: #000 !important;
                border: 2px solid #000;
                padding: 15px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .ai-header h3,
            .ai-header p {
                color: #000 !important;
            }

            .table-responsive {
                overflow: visible !important;
                max-height: none !important;
            }
        }

        /* Dark Mode Adjustments */
        body[data-layout-color="dark"] .ai-card {
            background-color: #323a46 !important;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }

        body[data-layout-color="dark"] .table {
            color: #adb5bd;
        }

        body[data-layout-color="dark"] th {
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] td {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .text-dark {
            color: #fff !important;
        }

        body[data-layout-color="dark"] .list-group-item {
            background-color: #323a46;
            border-color: #424e5a;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="ai-header shadow-lg d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3 class="fw-bold mb-1 text-white"><i class="mdi mdi-brain me-2"></i> المحلل الذكي (AI Insight)</h3>
                    <p class="mb-0 text-white-50">تحليل فوري لبيانات {{ $totalStudents ?? 0 }} طالب للتنبؤ بالمستويات
                        الأكاديمية وحالات التعثر.</p>
                </div>
                <div class="d-print-none">
                    <button class="btn btn-light text-primary rounded-pill fw-bold shadow-sm px-4" onclick="window.print()">
                        <i class="mdi mdi-printer me-1"></i> طباعة التقرير
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- 🔴 قسم الطلاب المتعثرين (At-Risk) --}}
        <div class="col-xl-8">
            <div class="card ai-card h-100 bg-white">
                <div
                    class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-danger mb-0"><i class="mdi mdi-alert-circle-outline me-2"></i> مؤشر الخطر (طلاب
                        بحاجة للتدخل)</h5>
                    <span
                        class="badge bg-danger rounded-pill shadow-sm">{{ $atRiskStudents ? $atRiskStudents->count() : 0 }}
                        حالات</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-custom align-middle mb-0">
                            <thead class="bg-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th class="ps-4">اسم الطالب</th>
                                    <th>الصف / الشعبة</th>
                                    <th>سبب التصنيف (الخوارزمية)</th>
                                    <th class="d-print-none">إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($atRiskStudents as $student)
                                    <tr>
                                        <td class="fw-bold text-dark ps-4">{{ $student->first_name }} {{ $student->tribe }}
                                        </td>
                                        <td class="text-muted fw-bold">{{ $student->grade->name_ar ?? '-' }} -
                                            {{ $student->section->name_ar ?? '-' }}</td>
                                        <td>
                                            @if ($student->attendances->where('status', 'absent')->count() >= 5)
                                                <span class="badge risk-badge px-3 py-2 shadow-sm"><i
                                                        class="mdi mdi-calendar-remove me-1"></i> غياب متكرر</span>
                                            @else
                                                <span class="badge risk-badge px-3 py-2 shadow-sm"><i
                                                        class="mdi mdi-trending-down me-1"></i> تراجع أكاديمي</span>
                                            @endif
                                        </td>
                                        <td class="d-print-none">
                                            <a href="{{ route('admin.students.show', $student->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill shadow-sm px-3">
                                                <i class="mdi mdi-eye-outline me-1"></i> عرض الملف
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-success fw-bold fs-5">
                                            <i class="mdi mdi-shield-check-outline d-block fs-1 opacity-50 mb-2"></i> لا
                                            يوجد طلاب في منطقة الخطر حالياً.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🟢 قسم معدل الحضور العام (Chart) --}}
        <div class="col-xl-4">
            <div class="card ai-card h-100 bg-white">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="mdi mdi-chart-pie me-2 text-primary"></i> مؤشر الانضباط
                        العام</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="attendanceChart" style="max-height: 250px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        {{-- ⭐ قسم الطلاب المتفوقين --}}
        <div class="col-xl-6">
            <div class="card ai-card h-100 bg-white">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="fw-bold text-success mb-0"><i class="mdi mdi-star-shooting me-2"></i> لوحة الشرف (أعلى 5 طلاب
                        - بناءً على المتوسط)</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($topStudents as $top)
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <span
                                            class="avatar-title bg-success bg-opacity-10 text-success rounded-circle font-20 shadow-sm"><i
                                                class="mdi mdi-trophy"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $top->first_name }} {{ $top->tribe }}</h6>
                                        <small class="text-muted fw-bold">{{ $top->grade->name_ar ?? '' }}</small>
                                    </div>
                                </div>
                                {{-- ✅ التصحيح: استخدام marks_avg_mark_obtained لأن الـ Controller يستخدم withAvg --}}
                                <span class="badge star-badge px-3 py-2 font-13 shadow-sm font-monospace">
                                    {{ number_format($top->marks_avg_mark_obtained ?? 0, 1) }} نقطة
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-center py-5 text-muted fw-bold border-0">
                                لا توجد بيانات كافية لاستخراج لوحة الشرف.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- 📉 تحليل المواد الدراسية --}}
        <div class="col-xl-6">
            <div class="card ai-card h-100 bg-white">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="fw-bold text-warning mb-0 text-dark"><i class="mdi mdi-book-alert me-2 text-warning"></i>
                        المواد التي تحتاج لدعم أكاديمي</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover table-custom align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4">المادة</th>
                                    <th>متوسط الدرجات (نسبة مئوية)</th>
                                    <th>حالات التعثر</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjectAnalysis->take(5) as $sub)
                                    <tr>
                                        {{-- ✅ التصحيح: استخدام subject_name لأن الـ Controller يستخدم DB::table مع join --}}
                                        <td class="fw-bold text-dark ps-4">{{ $sub->subject_name ?? 'غير معروف' }}</td>
                                        <td style="min-width: 150px;">
                                            <div class="progress shadow-sm" style="height: 10px; border-radius: 5px;">
                                                <div class="progress-bar {{ ($sub->avg_mark ?? 0) < 50 ? 'bg-danger' : 'bg-primary' }}"
                                                    style="width: {{ $sub->avg_mark ?? 0 }}%"></div>
                                            </div>
                                            <small
                                                class="text-muted mt-1 d-block fw-bold font-monospace">{{ round($sub->avg_mark ?? 0, 1) }}%</small>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-1 shadow-sm font-monospace">
                                                {{ $sub->failed_count ?? 0 }} طلاب
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted fw-bold">
                                            <i class="mdi mdi-google-analytics fs-1 d-block mb-2 opacity-50"></i> لا توجد
                                            بيانات كافية لتحليل المواد حالياً.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
            // 🌙 كشف الوضع الليلي لتكييف ألوان الشارت
            const isDarkMode = document.body.getAttribute('data-layout-color') === 'dark';
            const textColor = isDarkMode ? '#adb5bd' : '#334155';
            const borderColor = isDarkMode ? '#323a46' : '#ffffff';

            const chartCanvas = document.getElementById('attendanceChart');
            if (chartCanvas) {
                const ctx = chartCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['حاضر', 'غائب', 'متأخر'],
                        datasets: [{
                            data: [
                                {{ $attendanceRates['present'] ?? 0 }},
                                {{ $attendanceRates['absent'] ?? 0 }},
                                {{ $attendanceRates['late'] ?? 0 }}
                            ],
                            backgroundColor: ['#10b981', '#ff4d4f', '#f59e0b'],
                            borderWidth: 2,
                            borderColor: borderColor, // ✅ يتكيف مع الوضع الليلي
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    color: textColor, // ✅ يتكيف مع الوضع الليلي
                                    font: {
                                        family: 'Cairo, sans-serif',
                                        weight: 'bold',
                                        size: 13
                                    }
                                }
                            },
                            tooltip: {
                                bodyFont: {
                                    family: 'Cairo, sans-serif'
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
