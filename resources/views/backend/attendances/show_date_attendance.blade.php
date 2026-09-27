@extends('backend.layouts.master')
@section('title', 'أيام الحضور والغياب')
@section('title_page', 'سجل الحضور: ' . $section->grade->name_ar . ' - ' . $section->name_ar)

@push('css')
    <style>
        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .card-header {
            background-color: #323a46 !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .bg-white {
            background-color: #323a46 !important;
        }

        body[data-layout-color="dark"] .table-light {
            background-color: #3b4652 !important;
            color: #ced4da !important;
        }

        body[data-layout-color="dark"] .text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .text-muted {
            color: #adb5bd !important;
        }

        body[data-layout-color="dark"] .border-bottom {
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        /* 🖨️ Print Styles */
        @media print {

            .no-print,
            .btn,
            .card-header {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            .table thead {
                background-color: #f1f5f9 !important;
                color: #000 !important;
            }

            .badge {
                border: 1px solid #ccc !important;
            }
        }

        /* تحسينات إضافية */
        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div
                    class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 rounded-top-4 no-print">
                    <h5 class="fw-bold text-primary mb-0">
                        <i class="bx bx-calendar-event me-2"></i>
                        سجل أيام الحضور ({{ $section->academicYear->name }})
                    </h5>
                    <div class="d-flex gap-2">
                        <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bx bx-printer me-1"></i> طباعة
                        </button>
                        <a href="{{ route('admin.attendances.index') }}"
                            class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                            <i class="bx bx-arrow-back"></i> رجوع
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- 🔥 معلومات الشعبة 🔥 --}}
                    <div
                        class="alert alert-light border d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                        <div>
                            <strong class="text-dark"><i class="bx bx-school me-1"></i> الشعبة:</strong>
                            <span class="text-primary fw-bold">{{ $section->grade->name_ar ?? '-' }} -
                                {{ $section->name_ar }}</span>
                        </div>
                        <div>
                            <strong class="text-dark"><i class="bx bx-building me-1"></i> الفرع:</strong>
                            <span class="text-info fw-bold">{{ $section->branch->name_ar ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-dark"><i class="bx bx-calendar me-1"></i> السنة:</strong>
                            <span class="text-success fw-bold">{{ $section->academicYear->name ?? '-' }}</span>
                        </div>
                    </div>

                    @if (($dates ?? collect())->isEmpty())
                        <div class="text-center py-5">
                            <i class="bx bx-calendar-x text-muted opacity-25" style="font-size: 80px;"></i>
                            <h5 class="text-muted mt-3 fw-bold">لم يتم تسجيل أي حضور لهذه الشعبة حتى الآن!</h5>

                            @can('manage_attendance')
                                <a href="{{ route('admin.attendances.create', ['section_id' => $section->id]) }}"
                                    class="btn btn-primary mt-3 rounded-pill fw-bold px-4 shadow-sm">
                                    <i class="bx bx-plus"></i> تسجيل حضور جديد
                                </a>
                            @endcan
                        </div>
                    @else
                        {{-- 🔥 كروت الإحصائيات السريعة 🔥 --}}
                        @php
                            $totalDays = $dates->count();
                            $totalPresent = $dates->sum('present_count');
                            $totalAbsent = $dates->sum('absent_count');
                            $totalLate = $dates->sum('late_count');
                            $avgAttendance =
                                $totalDays > 0
                                    ? round(($totalPresent / ($totalPresent + $totalAbsent + $totalLate)) * 100, 1)
                                    : 0;
                        @endphp

                        <div class="row g-3 mb-4">
                            <div class="col-md-3 col-6">
                                <div class="stat-card card border-0 shadow-sm bg-primary bg-opacity-10 text-center p-3">
                                    <i class="bx bx-calendar-check fs-1 text-primary"></i>
                                    <h3 class="fw-bold text-primary mb-0 mt-2">{{ $totalDays }}</h3>
                                    <small class="text-muted fw-bold">أيام مسجلة</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card card border-0 shadow-sm bg-success bg-opacity-10 text-center p-3">
                                    <i class="bx bx-check-circle fs-1 text-success"></i>
                                    <h3 class="fw-bold text-success mb-0 mt-2">{{ number_format($totalPresent) }}</h3>
                                    <small class="text-muted fw-bold">إجمالي الحضور</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card card border-0 shadow-sm bg-danger bg-opacity-10 text-center p-3">
                                    <i class="bx bx-x-circle fs-1 text-danger"></i>
                                    <h3 class="fw-bold text-danger mb-0 mt-2">{{ number_format($totalAbsent) }}</h3>
                                    <small class="text-muted fw-bold">إجمالي الغياب</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-card card border-0 shadow-sm bg-warning bg-opacity-10 text-center p-3">
                                    <i class="bx bx-time fs-1 text-warning"></i>
                                    <h3 class="fw-bold text-warning mb-0 mt-2">{{ number_format($totalLate) }}</h3>
                                    <small class="text-muted fw-bold">إجمالي التأخير</small>
                                </div>
                            </div>
                        </div>

                        {{-- 🔥 شريط نسبة الحضور 🔥 --}}
                        <div class="card border-0 shadow-sm mb-4 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-dark"><i class="bx bx-bar-chart-alt-2 me-1"></i> معدل الحضور
                                    العام</strong>
                                <span
                                    class="badge bg-{{ $avgAttendance >= 80 ? 'success' : ($avgAttendance >= 60 ? 'warning' : 'danger') }} fs-6 px-3 py-2">
                                    {{ $avgAttendance }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-{{ $avgAttendance >= 80 ? 'success' : ($avgAttendance >= 60 ? 'warning' : 'danger') }}"
                                    role="progressbar" style="width: {{ $avgAttendance }}%"
                                    aria-valuenow="{{ $avgAttendance }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        {{-- 🔥 جدول الأيام 🔥 --}}
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th class="fw-bold rounded-start-3">تاريخ اليوم</th>
                                        <th class="text-center fw-bold">إجمالي الطلاب</th>
                                        <th class="text-center fw-bold text-success">الحضور</th>
                                        <th class="text-center fw-bold text-warning">التأخير</th>
                                        <th class="text-center fw-bold text-danger">الغياب</th>
                                        <th class="text-center fw-bold">النسبة</th>
                                        @can('manage_attendance')
                                            <th class="text-end fw-bold rounded-end-3 no-print">الإجراءات</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dates as $dateRecord)
                                        @php
                                            $dayTotal =
                                                ($dateRecord->present_count ?? 0) +
                                                ($dateRecord->absent_count ?? 0) +
                                                ($dateRecord->late_count ?? 0);
                                            $dayAttendanceRate =
                                                $dayTotal > 0
                                                    ? round(
                                                        (($dateRecord->present_count + $dateRecord->late_count) /
                                                            $dayTotal) *
                                                            100,
                                                        1,
                                                    )
                                                    : 0;
                                        @endphp
                                        <tr class="border-bottom">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <span
                                                            class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle font-16 fw-bold p-2 d-inline-block text-center"
                                                            style="width: 40px; height: 40px; line-height: 1.2;">
                                                            {{ \Carbon\Carbon::parse($dateRecord->attendance_date)->format('d') }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-dark">
                                                            {{ \Carbon\Carbon::parse($dateRecord->attendance_date)->translatedFormat('l, d F Y') }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($dateRecord->attendance_date)->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-center fw-bold fs-5">{{ $dateRecord->total_students }}</td>

                                            <td class="text-center">
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success fs-6 px-3 py-2 rounded-pill border border-success border-opacity-25">
                                                    <i class="bx bx-check-circle me-1"></i>
                                                    {{ $dateRecord->present_count ?? 0 }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-warning fs-6 px-3 py-2 rounded-pill border border-warning border-opacity-25">
                                                    <i class="bx bx-time me-1"></i> {{ $dateRecord->late_count ?? 0 }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger fs-6 px-3 py-2 rounded-pill border border-danger border-opacity-25">
                                                    <i class="bx bx-x-circle me-1"></i>
                                                    {{ $dateRecord->absent_count ?? 0 }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                @if ($dayAttendanceRate >= 80)
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success px-2 py-1">{{ $dayAttendanceRate }}%</span>
                                                @elseif ($dayAttendanceRate >= 60)
                                                    <span
                                                        class="badge bg-warning bg-opacity-10 text-warning px-2 py-1">{{ $dayAttendanceRate }}%</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">{{ $dayAttendanceRate }}%</span>
                                                @endif
                                            </td>

                                            @can('manage_attendance')
                                                <td class="text-end no-print">
                                                    <a href="{{ route('admin.attendances.create', ['section_id' => $section->id, 'date' => $dateRecord->attendance_date]) }}"
                                                        class="btn btn-sm btn-info bg-opacity-10 text-info border-info fw-bold rounded-pill px-3 shadow-sm">
                                                        <i class="bx bx-edit-alt"></i> تعديل
                                                    </a>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
