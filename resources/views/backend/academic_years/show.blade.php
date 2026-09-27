@extends('backend.layouts.master')
@section('title', 'تفاصيل السنة الدراسية: ' . $academicYear->name)

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .page-wrapper {
            padding: 25px;
        }

        /* 🌟 شريط العنوان 🌟 */
        .header-bar {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            border-radius: 16px;
            padding: 25px 30px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.2);
        }

        .header-bar::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 40%);
            transform: rotate(30deg);
            pointer-events: none;
        }

        .header-bar h4 {
            font-weight: 900;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .header-bar .dates {
            opacity: 0.9;
            font-weight: 600;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }

        .header-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
        }

        .header-badge.current {
            background: #fff;
            color: #1e40af;
        }

        .header-badge.closed {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 800;
            font-size: 13px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-back:hover {
            background: #fff;
            color: #1e40af;
            transform: translateY(-2px);
        }

        /* 🌟 التبويبات 🌟 */
        .nav-pills-custom {
            background: #f8fafc;
            border-radius: 12px;
            padding: 6px;
            border: 2px solid #e2e8f0;
        }

        .nav-pills-custom .nav-link {
            color: #64748b;
            font-weight: 800;
            border-radius: 8px;
            padding: 12px 20px;
            transition: all 0.3s ease;
            border: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-pills-custom .nav-link:hover:not(.active) {
            background: #e2e8f0;
            color: #1e293b;
        }

        .nav-pills-custom .nav-link.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* 🌟 كروت الإحصائيات 🌟 */
        .stat-card {
            border-radius: 16px;
            padding: 25px;
            background: #fff;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border-color: #3b82f6;
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 15px;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 900;
            font-family: 'Courier New', monospace;
            margin-bottom: 5px;
        }

        .stat-card .stat-label {
            font-size: 13px;
            color: #64748b;
            font-weight: 700;
        }

        /* ألوان الكروت */
        .stat-card.primary .stat-icon {
            background: #dbeafe;
            color: #1e40af;
            border: 2px solid #3b82f6;
        }

        .stat-card.primary .stat-value {
            color: #1e40af;
        }

        .stat-card.primary:hover {
            border-color: #3b82f6;
        }

        .stat-card.success .stat-icon {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }

        .stat-card.success .stat-value {
            color: #065f46;
        }

        .stat-card.success:hover {
            border-color: #10b981;
        }

        .stat-card.info .stat-icon {
            background: #dbeafe;
            color: #1e40af;
            border: 2px solid #0ea5e9;
        }

        .stat-card.info .stat-value {
            color: #0369a1;
        }

        .stat-card.info:hover {
            border-color: #0ea5e9;
        }

        .stat-card.danger .stat-icon {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }

        .stat-card.danger .stat-value {
            color: #991b1b;
        }

        .stat-card.danger:hover {
            border-color: #ef4444;
        }

        .stat-card.warning .stat-icon {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #f59e0b;
        }

        .stat-card.warning .stat-value {
            color: #92400e;
        }

        .stat-card.warning:hover {
            border-color: #f59e0b;
        }

        .stat-card.secondary .stat-icon {
            background: #e2e8f0;
            color: #475569;
            border: 2px solid #64748b;
        }

        .stat-card.secondary .stat-value {
            color: #475569;
        }

        .stat-card.secondary:hover {
            border-color: #64748b;
        }

        .stat-card.dark .stat-icon {
            background: #f1f5f9;
            color: #1e293b;
            border: 2px solid #334155;
        }

        .stat-card.dark .stat-value {
            color: #1e293b;
        }

        .stat-card.dark:hover {
            border-color: #334155;
        }

        /* 🌟 كارت الفرع 🌟 */
        .branch-card {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .branch-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        }

        .branch-card .branch-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            border: 2px solid #3b82f6;
        }

        .branch-card .branch-name {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .branch-card .branch-name-en {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 15px;
        }

        .branch-card .stats-row {
            display: flex;
            justify-content: space-around;
            padding-top: 15px;
            border-top: 2px dashed #e2e8f0;
        }

        .branch-card .mini-stat {
            text-align: center;
        }

        .branch-card .mini-stat .value {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: 900;
            display: block;
        }

        .branch-card .mini-stat .label {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
        }

        .mini-stat.students .value {
            color: #2563eb;
        }

        .mini-stat.sections .value {
            color: #059669;
        }

        /* 🌟 كارت الصف 🌟 */
        .grade-card {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .grade-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border-color: #0ea5e9;
            background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%);
        }

        .grade-card .grade-icon {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            border: 2px solid #0ea5e9;
            flex-shrink: 0;
        }

        .grade-card .grade-name {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .grade-card .grade-stats {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
        }

        .grade-card .grade-stats strong {
            font-weight: 800;
        }

        .grade-card .grade-stats .sections-count {
            color: #059669;
        }

        .grade-card .grade-stats .students-count {
            color: #dc2626;
        }

        /* 🌟 الجدول 🌟 */
        .table-custom {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            padding: 14px 16px;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        /* 🌟 شارات الجدول 🌟 */
        .branch-badge {
            background: #e2e8f0;
            color: #475569;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 12px;
            border: 1px solid #cbd5e1;
        }

        .section-name {
            color: #1e40af;
            font-weight: 800;
            font-size: 14px;
        }

        .capacity-text {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #64748b;
            font-size: 13px;
        }

        .students-count {
            font-family: 'Courier New', monospace;
            font-weight: 800;
            color: #1e293b;
            font-size: 15px;
        }

        /* 🌟 شريط التقدم 🌟 */
        .progress-custom {
            height: 24px;
            border-radius: 12px;
            background-color: #e2e8f0;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .progress-custom .progress-bar {
            border-radius: 12px;
            font-weight: 800;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: width 0.5s ease-in-out;
        }

        .progress-bar.success {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        }

        .progress-bar.warning {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }

        .progress-bar.danger {
            background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
        }

        /* 🌟 Print 🌟 */
        @media print {

            .no-print,
            .nav-pills-custom,
            .btn-back {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            .tab-pane {
                display: block !important;
                opacity: 1 !important;
                page-break-inside: avoid;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 شريط العنوان 🌟 --}}
        <div class="header-bar mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="mb-2">
                        <i class='bx bx-calendar me-2'></i> السنة الدراسية: {{ $academicYear->name }}
                    </h4>
                    <p class="dates mb-0">
                        <i class='bx bx-calendar-event me-1'></i>
                        من {{ \Carbon\Carbon::parse($academicYear->start_date)->format('Y-m-d') }}
                        إلى {{ \Carbon\Carbon::parse($academicYear->end_date)->format('Y-m-d') }}
                    </p>
                </div>
                <div class="text-end">
                    @if ($academicYear->is_current)
                        <span class="header-badge current d-block">
                            <i class='bx bx-check-circle'></i> سنة نشطة حالياً
                        </span>
                    @endif
                    @if ($academicYear->is_closed)
                        <span class="header-badge closed d-block">
                            <i class='bx bx-lock'></i> مؤرشفة
                        </span>
                    @endif
                    <a href="{{ route('admin.academic-years.index') }}" class="btn-back mt-2">
                        <i class='bx bx-arrow-back'></i>
                        <span>العودة للقائمة</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- 🌟 التبويبات 🌟 --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <ul class="nav nav-pills nav-pills-custom nav-justified mb-4 no-print" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pills-branches">
                            <i class='bx bx-buildings'></i> الفروع المربوطة
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-grades">
                            <i class='bx bx-book-open'></i> الصفوف النشطة
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-sections">
                            <i class='bx bx-group'></i> الشعب والفصول
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-students">
                            <i class='bx bx-user-pin'></i> إحصائيات الطلاب
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">

                    {{-- 1. تبويب الفروع --}}
                    <div class="tab-pane fade show active" id="pills-branches">
                        <div class="row g-4">
                            @forelse($academicYear->branches ?? [] as $branch)
                                <div class="col-md-4">
                                    <div class="branch-card">
                                        <div class="branch-icon">
                                            <i class='bx bx-building-house'></i>
                                        </div>
                                        <h5 class="branch-name">{{ $branch->name_ar }}</h5>
                                        @if ($branch->name_en)
                                            <small class="branch-name-en d-block">{{ $branch->name_en }}</small>
                                        @endif
                                        <div class="stats-row">
                                            <div class="mini-stat students">
                                                <span class="value">{{ $branch->students_count ?? 0 }}</span>
                                                <span class="label">طالب</span>
                                            </div>
                                            <div class="mini-stat sections">
                                                <span class="value">{{ $branch->sections_count ?? 0 }}</span>
                                                <span class="label">شعبة</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <div class="py-4">
                                        <i class='bx bx-buildings text-muted opacity-25' style="font-size: 80px;"></i>
                                        <h5 class="fw-bold text-muted mt-3">لا توجد فروع مربوطة بهذه السنة الدراسية</h5>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 2. تبويب الصفوف --}}
                    <div class="tab-pane fade" id="pills-grades">
                        <div class="row g-4">
                            @forelse($activeGrades ?? [] as $grade)
                                <div class="col-md-4">
                                    <div class="grade-card">
                                        <div class="grade-icon">
                                            <i class='bx bx-book-reader'></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="grade-name mb-1">{{ $grade->name_ar }}</h5>
                                            <p class="grade-stats mb-0">
                                                الشعب: <strong
                                                    class="sections-count">{{ $grade->sections_count ?? 0 }}</strong>
                                                |
                                                الطلاب: <strong
                                                    class="students-count">{{ $grade->students_count ?? 0 }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <div class="py-4">
                                        <i class='bx bx-book-open text-muted opacity-25' style="font-size: 80px;"></i>
                                        <h5 class="fw-bold text-muted mt-3">لم يتم تفعيل أي صفوف في هذه السنة</h5>
                                        <p class="text-muted">يجب إضافة شعب أولاً</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 3. تبويب الشعب --}}
                    <div class="tab-pane fade" id="pills-sections">
                        <div class="table-responsive">
                            <table class="table table-hover table-custom text-center align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>الفرع</th>
                                        <th>الصف (المرحلة)</th>
                                        <th>اسم الشعبة</th>
                                        <th>السعة القصوى</th>
                                        <th>عدد الطلاب</th>
                                        <th>نسبة الإشغال</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($academicYear->sections ?? [] as $section)
                                        @php
                                            $capacity = $section->max_capacity ?? 25;
                                            $studentsCount = $section->students_count ?? 0;
                                            $occupancy = $capacity > 0 ? round(($studentsCount / $capacity) * 100) : 0;
                                            $occupancyClass =
                                                $occupancy >= 90
                                                    ? 'danger'
                                                    : ($occupancy >= 70
                                                        ? 'warning'
                                                        : 'success');
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="branch-badge">
                                                    <i class='bx bx-buildings me-1'></i>
                                                    {{ $section->branch->name_ar ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="fw-bold">{{ $section->grade->name_ar ?? '-' }}</td>
                                            <td class="section-name">
                                                <i class='bx bx-door-open me-1'></i>
                                                {{ $section->name_ar }}
                                            </td>
                                            <td>
                                                <span class="capacity-text">
                                                    <i class='bx bx-chair me-1'></i>
                                                    {{ $capacity }} طالب
                                                </span>
                                            </td>
                                            <td>
                                                <span class="students-count">{{ $studentsCount }}</span>
                                            </td>
                                            <td>
                                                <div class="progress-custom">
                                                    <div class="progress-bar {{ $occupancyClass }}" role="progressbar"
                                                        style="width: {{ $occupancy }}%"
                                                        aria-valuenow="{{ $occupancy }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        {{ $occupancy }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="py-4">
                                                    <i class='bx bx-group text-muted opacity-25'
                                                        style="font-size: 80px;"></i>
                                                    <h5 class="fw-bold text-muted mt-3">لا توجد شعب دراسية مضافة لهذه السنة
                                                    </h5>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 4. تبويب إحصائيات الطلاب --}}
                    <div class="tab-pane fade" id="pills-students">
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="stat-card primary text-center">
                                    <div class="stat-icon">
                                        <i class='bx bx-user-pin'></i>
                                    </div>
                                    <div class="stat-value">{{ $studentsStats['total'] ?? 0 }}</div>
                                    <div class="stat-label">إجمالي الطلاب</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card success text-center">
                                    <div class="stat-icon">
                                        <i class='bx bx-user-check'></i>
                                    </div>
                                    <div class="stat-value">{{ $studentsStats['active'] ?? 0 }}</div>
                                    <div class="stat-label">نشط</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card info text-center">
                                    <div class="stat-icon">
                                        <i class='bx bx-male'></i>
                                    </div>
                                    <div class="stat-value">{{ $studentsStats['male'] ?? 0 }}</div>
                                    <div class="stat-label">ذكور</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card danger text-center">
                                    <div class="stat-icon">
                                        <i class='bx bx-female'></i>
                                    </div>
                                    <div class="stat-value">{{ $studentsStats['female'] ?? 0 }}</div>
                                    <div class="stat-label">إناث</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="stat-card warning text-center">
                                    <div class="stat-icon">
                                        <i class='bx bx-graduation'></i>
                                    </div>
                                    <div class="stat-value">{{ $studentsStats['graduated'] ?? 0 }}</div>
                                    <div class="stat-label">متخرج</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card secondary text-center">
                                    <div class="stat-icon">
                                        <i class='bx bx-pause-circle'></i>
                                    </div>
                                    <div class="stat-value">{{ $studentsStats['suspended'] ?? 0 }}</div>
                                    <div class="stat-label">موقوف</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card info text-center">
                                    <div class="stat-icon">
                                        <i class='bx bx-transfer'></i>
                                    </div>
                                    <div class="stat-value">{{ $studentsStats['transferred'] ?? 0 }}</div>
                                    <div class="stat-label">محول</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card dark text-center">
                                    <div class="stat-icon">
                                        <i class='bx bx-log-out'></i>
                                    </div>
                                    @php
                                        $withdrawn =
                                            ($studentsStats['total'] ?? 0) -
                                            ($studentsStats['active'] ?? 0) -
                                            ($studentsStats['graduated'] ?? 0) -
                                            ($studentsStats['suspended'] ?? 0) -
                                            ($studentsStats['transferred'] ?? 0);
                                    @endphp
                                    <div class="stat-value">{{ $withdrawn }}</div>
                                    <div class="stat-label">منسحب/أخرى</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
