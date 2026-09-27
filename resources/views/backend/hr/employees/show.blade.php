@extends('backend.layouts.master')
@section('title', 'ملف الموظف | ' . $employee->full_name_ar)
@section('title_page', 'الموارد البشرية - الملف الشامل للموظف')

@push('css')
    <style>
        .page-wrapper {
            background-color: #f4f6f9;
            padding: 25px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .sig-img {
            height: 35px;
            object-fit: contain;
            pointer-events: none;
            user-select: none;
            -webkit-user-drag: none;
            filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.1));
        }

        .profile-header {
            background: linear-gradient(135deg, #1e2b3c, #4e54c8);
            border-radius: 15px;
            color: white;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(78, 84, 200, 0.15);
            position: relative;
            overflow: hidden;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            background-color: #fff;
        }

        .badge-status {
            font-size: 14px;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: bold;
        }

        /* تحسين التابات للموبايل */
        .nav-pills-custom {
            margin-top: -25px;
            padding: 0 30px;
            position: relative;
            z-index: 10;
            flex-wrap: nowrap;
            overflow-x: auto;
            white-space: nowrap;
            scroll-behavior: smooth;
        }

        .nav-pills-custom::-webkit-scrollbar {
            height: 0;
            display: none;
        }

        .nav-pills-custom .nav-link {
            background: #fff;
            color: #444;
            font-weight: 700;
            border-radius: 10px;
            padding: 12px 25px;
            margin-right: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #eee;
            transition: 0.3s;
        }

        .nav-pills-custom .nav-link.active {
            background-color: #f2c94c;
            color: #1e2b3c;
            border-color: #f2c94c;
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(242, 201, 76, 0.3);
        }

        .card-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            margin-top: 25px;
        }

        .info-label {
            color: #888;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .info-value {
            color: #222;
            font-size: 16px;
            font-weight: 800;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 1. Profile Header --}}
        <div class="profile-header mb-4 d-flex align-items-center flex-wrap gap-3">
            <div class="me-4">
                <img src="{{ $employee->profile_image ? asset('storage/' . $employee->profile_image) : asset('ltr/assets/images/avatars/avatar-1.png') }}"
                    class="profile-avatar" alt="Avatar"
                    onerror="this.src='{{ asset('ltr/assets/images/avatars/avatar-1.png') }}'">
            </div>
            <div class="flex-grow-1 px-3">
                <h2 class="fw-bolder mb-1">{{ $employee->full_name_ar }}</h2>
                <h5 class="mb-3 opacity-75">
                    <i class='mdi mdi-briefcase-outline me-1'></i> {{ $employee->job_title }} |
                    <i class='mdi mdi-office-building-outline ms-1 me-1'></i>
                    {{ $employee->department->name_ar ?? 'بدون قسم' }}
                </h5>
                <div>
                    <span class="badge bg-light text-dark badge-status me-2">
                        <i class='mdi mdi-card-account-details-outline me-1'></i> كود: {{ $employee->employee_code }}
                    </span>
                    @if ($employee->status == 'active')
                        <span class="badge bg-success badge-status"><i class='mdi mdi-check-circle-outline me-1'></i> على
                            رأس العمل</span>
                    @elseif($employee->status == 'on_leave')
                        <span class="badge bg-warning text-dark badge-status"><i class='mdi mdi-clock-outline me-1'></i> في
                            إجازة</span>
                    @elseif($employee->status == 'suspended')
                        <span class="badge bg-danger badge-status"><i class='mdi mdi-minus-circle-outline me-1'></i> موقوف
                            عن العمل</span>
                    @else
                        <span class="badge bg-dark badge-status"><i class='mdi mdi-cancel me-1'></i> منتهية خدماته</span>
                    @endif
                </div>
            </div>
            <div class="text-md-end mt-3 mt-md-0 w-100 w-md-auto text-end">
                <a href="{{ route('admin.hr.employees.index') }}" class="btn btn-light fw-bold rounded-3 px-4 shadow-sm">
                    <i class='mdi mdi-arrow-right me-1'></i> رجوع للقائمة
                </a>
            </div>
        </div>

        {{-- 2. Navigation Tabs --}}
        <ul class="nav nav-pills nav-pills-custom mb-3" id="employee-tabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#overview"
                    type="button"><i class='mdi mdi-information-outline me-1'></i> نظرة عامة</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#financials"
                    type="button"><i class='mdi mdi-cash-multiple me-1'></i> الماليات والعقود</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#attendance"
                    type="button"><i class='mdi mdi-calendar-check-outline me-1'></i> الحضور والغياب</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#penalties"
                    type="button"><i class='mdi mdi-alert-circle-outline me-1'></i> الجزاءات والمخالفات</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#documents"
                    type="button"><i class='mdi mdi-folder-open-outline me-1'></i> الوثائق</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#loans" type="button"><i
                        class='mdi mdi-credit-card-outline me-1'></i> السلف</button></li>
            <li class="nav-item"><button class="nav-link text-primary border-primary" data-bs-toggle="pill"
                    data-bs-target="#custody" type="button"><i class='mdi mdi-briefcase-outline me-1'></i> العُهَد</button>
            </li>
        </ul>

        {{-- 3. Tabs Content --}}
        <div class="tab-content" id="employee-tabsContent">

            {{-- التاب الأول: نظرة عامة --}}
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-custom h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-primary mb-4 border-bottom pb-2"><i
                                        class='mdi mdi-account-outline me-1'></i> البيانات الشخصية</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="info-label">رقم الهاتف</div>
                                        <div class="info-value font-monospace" dir="ltr">{{ $employee->phone }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">البريد الإلكتروني</div>
                                        <div class="info-value">{{ $employee->email ?? 'لا يوجد' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">رقم الهوية / الإقامة</div>
                                        <div class="info-value text-primary font-monospace">
                                            {{ $employee->national_id ?? 'لا يوجد' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">تاريخ التعيين</div>
                                        <div class="info-value font-monospace">
                                            {{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '---' }}
                                            @if ($employee->join_date)
                                                <small
                                                    class="text-success fw-bold font-sans-serif ms-1">({{ \Carbon\Carbon::parse($employee->join_date)->diffForHumans() }})</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-label">الجنسية</div>
                                        <div class="info-value">{{ $employee->nationality ?? 'غير محدد' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-label">رقم التأمينات (PASI)</div>
                                        <div class="info-value font-monospace">
                                            @if ($employee->is_insured)
                                                <span
                                                    class="text-success fw-bold">{{ $employee->pasi_number ?? 'مسجل' }}</span>
                                            @else
                                                <span class="text-muted font-sans-serif">غير خاضع</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-label">تاريخ الميلاد</div>
                                        <div class="info-value font-monospace">
                                            {{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('Y-m-d') : 'غير محدد' }}
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-4 pt-3 border-top">
                                        <div class="info-label">مواعيد العمل (الوردية)</div>
                                        <div class="info-value mt-2">
                                            <span
                                                class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2 fs-6 shadow-sm">
                                                <i class="mdi mdi-clock-outline me-1"></i>
                                                {{ $employee->shift->name_ar ?? 'غير محدد' }}
                                                @if ($employee->shift)
                                                    <span class="font-monospace ms-1 text-dark">
                                                        (من
                                                        {{ \Carbon\Carbon::parse($employee->shift->start_time)->format('h:i A') }}
                                                        إلى
                                                        {{ \Carbon\Carbon::parse($employee->shift->end_time)->format('h:i A') }})
                                                    </span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-custom bg-light h-100 border-0">
                            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                                <i class='mdi mdi-calendar-star text-warning mb-2' style="font-size: 60px;"></i>
                                <h5 class="fw-bold mt-3">رصيد الإجازات المتبقي</h5>
                                <h1 class="fw-bolder text-dark mb-0 font-monospace">
                                    {{ $employee->leaveBalances ? $employee->leaveBalances->sum('remaining_days') : 0 }}
                                    <span class="fs-5 fw-normal text-muted font-sans-serif">أيام</span>
                                </h1>
                                <a href="#"
                                    class="btn btn-sm btn-outline-dark mt-4 fw-bold w-100 shadow-sm rounded-pill">
                                    سجل الإجازات <i class='mdi mdi-open-in-new ms-1'></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- التاب الثاني: الماليات والعقود --}}
            <div class="tab-pane fade" id="financials" role="tabpanel">
                <div class="card card-custom">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-success mb-4 border-bottom pb-2"><i
                                class='mdi mdi-wallet-outline me-1'></i> الهيكل المالي والرواتب</h5>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="info-label">الدرجة الوظيفية</div>
                                <div class="info-value mt-1">
                                    <span class="badge bg-light text-dark border px-3 py-2 fs-6 shadow-sm">
                                        {{ $employee->jobGrade->name_ar ?? 'غير محدد' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">الراتب الأساسي</div>
                                <div class="info-value text-success fs-4 fw-black font-monospace">
                                    {{ number_format($employee->basic_salary, 3) }} <span
                                        class="fs-6 text-muted font-sans-serif">ر.ع</span>
                                </div>
                            </div>
                        </div>

                        {{-- قسم العقد المطور --}}
                        <div
                            class="d-flex justify-content-between align-items-end mt-4 border-bottom pb-2 mb-3 flex-wrap gap-2">
                            <h5 class="fw-bold text-primary mb-0"><i class='mdi mdi-file-document-outline me-1'></i> موقف
                                العقد الحالي</h5>
                            @php
                                $totalContracts = $employee->contracts ? $employee->contracts->count() : 0;
                                $yearsInCompany = $employee->join_date
                                    ? \Carbon\Carbon::parse($employee->join_date)->diffInYears(\Carbon\Carbon::now())
                                    : 0;
                                $monthsInCompany = $employee->join_date
                                    ? \Carbon\Carbon::parse($employee->join_date)->diffInMonths(\Carbon\Carbon::now()) %
                                        12
                                    : 0;
                            @endphp
                            <div class="text-md-end text-start">
                                <span class="badge bg-secondary mb-1 shadow-sm font-monospace">
                                    مدة الخدمة: {{ $yearsInCompany }} سنة و {{ $monthsInCompany }} شهر
                                </span>
                                <br>
                                <span class="badge bg-info text-dark shadow-sm">إجمالي العقود المبرمة:
                                    {{ $totalContracts }}</span>
                            </div>
                        </div>

                        @php
                            $latestContract = $employee->contracts
                                ? $employee->contracts->where('status', 'valid')->sortByDesc('end_date')->first()
                                : null;
                            if (!$latestContract && $employee->contracts) {
                                $latestContract = $employee->contracts->sortByDesc('end_date')->first();
                            }
                        @endphp

                        @if ($latestContract)
                            @php
                                $start = \Carbon\Carbon::parse($latestContract->start_date)->startOfDay();
                                $end = \Carbon\Carbon::parse($latestContract->end_date)->startOfDay();
                                $today = \Carbon\Carbon::today();

                                $totalDays = $start->diffInDays($end) + 1;
                                $passedDays =
                                    $start->diffInDays($today, false) >= 0 ? $start->diffInDays($today, false) + 1 : 0;
                                $remainingDays = $today->diffInDays($end, false);

                                $progressPercent = ($passedDays / $totalDays) * 100;
                                $progressPercent = max(0, min(100, $progressPercent));

                                $isExpired =
                                    $remainingDays < 0 ||
                                    $latestContract->status == 'expired' ||
                                    $latestContract->status == 'terminated';
                                $isExpiringSoon = !$isExpired && $remainingDays <= 30;

                                $statusColor = $isExpired ? 'danger' : ($isExpiringSoon ? 'warning' : 'success');
                                $statusText = $isExpired ? 'منتهي' : ($isExpiringSoon ? 'ينتهي قريباً' : 'ساري');
                            @endphp

                            <div class="p-4 border rounded-4 bg-white shadow-sm position-relative">
                                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-muted">
                                            رقم العقد: <span
                                                class="text-dark font-monospace">#{{ $latestContract->contract_number }}</span>
                                        </h6>
                                        <span
                                            class="badge bg-{{ $statusColor }} px-3 py-2 fs-6 shadow-sm">{{ $statusText }}</span>
                                    </div>
                                    @if ($latestContract->attachment)
                                        <a href="{{ asset('storage/' . $latestContract->attachment) }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="btn btn-sm btn-primary fw-bold shadow-sm rounded-pill px-4">
                                            <i class='mdi mdi-download me-1'></i> تحميل العقد
                                        </a>
                                    @endif
                                </div>

                                <div class="row text-center mb-4 bg-light rounded-3 p-3 mx-0">
                                    <div class="col-4">
                                        <small class="text-muted d-block fw-bold mb-1">بداية العقد</small>
                                        <span class="fw-bolder fs-5 font-monospace">{{ $start->format('Y-m-d') }}</span>
                                    </div>
                                    <div class="col-4 border-start border-end border-2">
                                        <small class="text-muted d-block fw-bold mb-1">نهاية العقد</small>
                                        <span
                                            class="fw-bolder fs-5 font-monospace {{ $isExpired ? 'text-danger' : '' }}">{{ $end->format('Y-m-d') }}</span>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block fw-bold mb-1">المتبقي</small>
                                        @if ($isExpired)
                                            <span class="fw-bolder fs-5 text-danger font-monospace">منذ
                                                {{ abs($remainingDays) }} يوم</span>
                                        @else
                                            <span
                                                class="fw-bolder fs-5 text-{{ $isExpiringSoon ? 'warning' : 'success' }} font-monospace">{{ $remainingDays }}
                                                يوم</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted fw-bold">مؤشر فترة العقد</small>
                                    <small
                                        class="text-{{ $statusColor }} fw-bold font-monospace">{{ round($progressPercent) }}%</small>
                                </div>
                                <div class="progress"
                                    style="height: 12px; border-radius: 10px; background-color: #e9ecef;">
                                    <div class="progress-bar bg-{{ $statusColor }} progress-bar-striped {{ !$isExpired ? 'progress-bar-animated' : '' }}"
                                        role="progressbar" style="width: {{ $progressPercent }}%;"></div>
                                </div>
                            </div>
                        @else
                            <div
                                class="alert alert-soft-danger text-danger border-danger fw-bold text-center mb-0 p-4 rounded-4 bg-danger bg-opacity-10 shadow-sm">
                                <i class='mdi mdi-alert-circle-outline fs-1 d-block mb-2 opacity-75'></i>
                                لا يوجد عقود مسجلة لهذا الموظف.<br>
                                @can('create_contract')
                                    <a href="{{ route('admin.hr.contracts.index') }}"
                                        class="btn btn-danger fw-bold mt-3 px-4 shadow-sm rounded-pill"><i
                                            class='mdi mdi-plus me-1'></i> توثيق عقد جديد</a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- التاب الثالث: سجل الحضور والغياب --}}
            <div class="tab-pane fade" id="attendance" role="tabpanel">
                <div class="card card-custom border-top border-info border-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                            <h5 class="fw-bold text-info text-dark mb-0"><i
                                    class='mdi mdi-calendar-check-outline me-1'></i> سجل الحضور والغياب الأخير</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center table-hover align-middle table-nowrap">
                                <thead class="bg-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>تسجيل الدخول</th>
                                        <th>تسجيل الانصراف</th>
                                        <th>تأخير (دقيقة)</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employee->attendances->sortByDesc('date')->take(30) as $att)
                                        <tr>
                                            <td class="fw-bold text-dark font-monospace">
                                                {{ \Carbon\Carbon::parse($att->date)->format('Y-m-d') }} <br>
                                                <small
                                                    class="text-muted font-sans-serif">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l') }}</small>
                                            </td>
                                            <td class="fw-bold text-primary font-monospace">
                                                {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '--:--' }}
                                            </td>
                                            <td class="fw-bold text-danger font-monospace">
                                                {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : '--:--' }}
                                            </td>
                                            <td
                                                class="font-monospace {{ $att->delay_minutes > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                                {{ $att->delay_minutes }}</td>
                                            <td>
                                                @if ($att->status == 'present')
                                                    <span class="badge bg-success px-3">حضور</span>
                                                @elseif($att->status == 'absent')
                                                    <span class="badge bg-danger px-3">غياب</span>
                                                @elseif($att->status == 'late')
                                                    <span class="badge bg-warning text-dark px-3">تأخير</span>
                                                @elseif($att->status == 'on_leave')
                                                    <span class="badge bg-info text-dark px-3">إجازة</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-5 text-muted">
                                                <i class='mdi mdi-fingerprint fs-1 opacity-50 d-block mb-2'></i>
                                                <h6 class="fw-bold mb-0">لا توجد سجلات حضور</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- التاب الرابع: الجزاءات والمخالفات --}}
            <div class="tab-pane fade" id="penalties" role="tabpanel">
                <div class="card card-custom border-top border-danger border-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                            <h5 class="fw-bold text-danger mb-0"><i class='mdi mdi-alert-circle-outline me-1'></i> سجل
                                الجزاءات والمخالفات</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center table-hover align-middle table-nowrap">
                                <thead class="bg-light">
                                    <tr>
                                        <th>تاريخ الواقعة</th>
                                        <th>نوع المخالفة</th>
                                        <th>الإجراء المتخذ</th>
                                        <th>أيام الخصم</th>
                                        <th>حالة الخصم من الراتب</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employee->penalties->sortByDesc('incident_date') as $penalty)
                                        <tr>
                                            <td class="fw-bold text-dark font-monospace">
                                                {{ \Carbon\Carbon::parse($penalty->incident_date)->format('Y-m-d') }}</td>
                                            <td class="fw-bold text-danger">{{ $penalty->violation_type }}</td>
                                            <td>
                                                @if ($penalty->penalty_action == 'verbal_warning')
                                                    <span class="badge bg-secondary px-2">إنذار شفهي</span>
                                                @elseif($penalty->penalty_action == 'written_warning')
                                                    <span class="badge bg-warning text-dark px-2">لفت نظر كتابي</span>
                                                @elseif($penalty->penalty_action == 'salary_deduction')
                                                    <span class="badge bg-danger px-2">خصم من الراتب</span>
                                                @elseif($penalty->penalty_action == 'suspension')
                                                    <span class="badge bg-dark px-2">إيقاف عن العمل</span>
                                                @endif
                                            </td>
                                            <td
                                                class="fw-bold fs-5 font-monospace {{ $penalty->deduction_days > 0 ? 'text-danger' : 'text-muted' }}">
                                                {{ $penalty->deduction_days > 0 ? $penalty->deduction_days . ' يوم' : '-' }}
                                            </td>
                                            <td>
                                                @if ($penalty->deduction_days > 0)
                                                    @if ($penalty->applied_to_payroll)
                                                        <span class="badge bg-success px-2"><i
                                                                class='mdi mdi-check-circle-outline me-1'></i> تم
                                                            الخصم</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark px-2"><i
                                                                class='mdi mdi-clock-outline me-1'></i> معلق للشهر
                                                            القادم</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-5 text-muted">
                                                <i
                                                    class='mdi mdi-shield-check-outline fs-1 opacity-50 d-block mb-2 text-success'></i>
                                                <h6 class="fw-bold mb-0 text-success">سجل الموظف نظيف</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- التاب الخامس: الوثائق --}}
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="card card-custom border-top border-primary border-4">
                    <div class="card-body p-4">
                        <div
                            class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2 flex-wrap gap-2">
                            <h5 class="fw-bold text-primary mb-0"><i class='mdi mdi-folder-open-outline me-1'></i> الوثائق
                                الرسمية للموظف</h5>
                            @can('manage_employee_documents')
                                <button class="btn btn-primary fw-bold shadow-sm px-4 py-2 rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                                    <i class='mdi mdi-upload me-1'></i> رفع وثيقة
                                </button>
                            @endcan
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center table-hover align-middle table-nowrap">
                                <thead class="bg-light">
                                    <tr>
                                        <th>نوع الوثيقة</th>
                                        <th>رقم الوثيقة</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th>الحالة</th>
                                        <th>المرفق</th>
                                        @can('manage_employee_documents')
                                            <th>الإجراءات</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employee->documents as $doc)
                                        @php
                                            $expiryDate = $doc->expiry_date
                                                ? \Carbon\Carbon::parse($doc->expiry_date)->startOfDay()
                                                : null;
                                            $today = \Carbon\Carbon::today();
                                            $isExpired = $expiryDate && $expiryDate->isBefore($today);
                                            $isExpiringSoon =
                                                $expiryDate && !$isExpired && $expiryDate->diffInDays($today) <= 30;
                                        @endphp
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $doc->document_type }}</td>
                                            <td class="text-secondary fw-bold font-monospace">{{ $doc->document_number }}
                                            </td>
                                            <td
                                                class="font-monospace {{ $isExpired ? 'text-danger fw-bold' : ($isExpiringSoon ? 'text-warning text-dark fw-bold' : '') }}">
                                                {{ $expiryDate ? $expiryDate->format('Y-m-d') : '---' }}
                                                @if ($expiryDate && $isExpiringSoon && !$isExpired)
                                                    <br><small class="text-muted font-sans-serif"
                                                        style="font-size: 11px;">ينتهي خلال
                                                        {{ $expiryDate->diffInDays($today) }} يوم</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!$expiryDate)
                                                    <span class="badge bg-secondary">غير محدد</span>
                                                @elseif ($isExpired)
                                                    <span class="badge bg-danger">منتهية</span>
                                                @elseif($isExpiringSoon)
                                                    <span class="badge bg-warning text-dark">تنتهي قريباً</span>
                                                @else
                                                    <span class="badge bg-success">سارية</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($doc->attachment)
                                                    <a href="{{ asset('storage/' . $doc->attachment) }}" target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="btn btn-sm btn-outline-primary rounded-3 shadow-sm px-3 fw-bold">
                                                        <i class='mdi mdi-eye-outline me-1'></i> عرض
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            @can('manage_employee_documents')
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary rounded-circle btn-edit-doc shadow-sm"
                                                            data-id="{{ $doc->id }}"
                                                            data-type="{{ $doc->document_type }}"
                                                            data-number="{{ $doc->document_number }}"
                                                            data-expiry="{{ $expiryDate ? $expiryDate->format('Y-m-d') : '' }}"
                                                            data-bs-toggle="modal" data-bs-target="#editDocumentModal"
                                                            title="تعديل">
                                                            <i class='mdi mdi-pencil-outline font-16'></i>
                                                        </button>
                                                        <form action="{{ route('admin.hr.documents.destroy', $doc->id) }}"
                                                            method="POST" class="d-inline-block delete-doc-form">
                                                            @csrf @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger rounded-circle btn-delete-doc shadow-sm"
                                                                title="حذف">
                                                                <i class='mdi mdi-trash-can-outline font-16'></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ Auth::user()->can('manage_employee_documents') ? 6 : 5 }}"
                                                class="p-5 text-muted">
                                                <i class='mdi mdi-folder-open-outline fs-1 opacity-50 d-block mb-2'></i>
                                                <h6 class="fw-bold mb-0">لا توجد وثائق مسجلة</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- التاب السادس: السلف والقروض --}}
            <div class="tab-pane fade" id="loans" role="tabpanel">
                <div class="card card-custom border-top border-warning border-4">
                    <div class="card-body p-4">
                        <div
                            class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2 flex-wrap gap-2">
                            <h5 class="fw-bold text-warning text-dark mb-0"><i
                                    class='mdi mdi-credit-card-outline me-1'></i> سجل السلف والقروض</h5>
                            @can('manage_payroll_financials')
                                <button class="btn btn-warning text-dark fw-bold shadow-sm px-4 py-2 rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#addLoanModal">
                                    <i class='mdi mdi-plus me-1'></i> صرف سلفة جديدة
                                </button>
                            @endcan
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center table-hover align-middle table-nowrap">
                                <thead class="bg-light">
                                    <tr>
                                        <th>قيمة السلفة</th>
                                        <th>القسط الشهري</th>
                                        <th>عدد الأشهر</th>
                                        <th>بداية الخصم</th>
                                        <th>الحالة</th>
                                        <th>متابعة وإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employee->loans ?? [] as $loan)
                                        <tr>
                                            <td class="fw-bolder text-success fs-5 font-monospace">
                                                {{ number_format($loan->loan_amount, 3) }} <small
                                                    class="text-muted fw-normal font-sans-serif">ر.ع</small>
                                            </td>
                                            <td class="fw-bold text-danger font-monospace">
                                                {{ number_format($loan->monthly_installment, 3) }} <small
                                                    class="text-muted fw-normal font-sans-serif">ر.ع</small>
                                            </td>
                                            <td class="fw-bold font-monospace">{{ $loan->months_to_pay ?? 0 }} <small
                                                    class="text-muted fw-normal font-sans-serif">شهر</small></td>
                                            <td><span
                                                    class="badge bg-light text-dark border px-2 py-1 font-monospace">{{ \Carbon\Carbon::parse($loan->start_deduction_month)->format('Y-m') }}</span>
                                            </td>
                                            <td>
                                                @if ($loan->status == 'paid')
                                                    <span class="badge bg-success px-3 py-1">تم السداد بالكامل</span>
                                                @else
                                                    <span class="badge bg-warning text-dark px-3 py-1">جاري السداد</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $paidAmount = $loan->paid_amount ?? 0;
                                                    $remainingAmount = $loan->loan_amount - $paidAmount;
                                                    $progress =
                                                        $loan->loan_amount > 0
                                                            ? ($paidAmount / $loan->loan_amount) * 100
                                                            : 0;
                                                @endphp
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-info rounded-circle btn-track-loan shadow-sm"
                                                        data-amount="{{ $loan->loan_amount }}"
                                                        data-paid="{{ $paidAmount }}"
                                                        data-remaining="{{ $remainingAmount }}"
                                                        data-progress="{{ round($progress) }}" data-bs-toggle="modal"
                                                        data-bs-target="#trackLoanModal" title="متابعة السداد">
                                                        <i class='mdi mdi-chart-pie font-16'></i>
                                                    </button>
                                                    @can('manage_payroll_financials')
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary rounded-circle btn-edit-loan shadow-sm"
                                                            data-id="{{ $loan->id }}"
                                                            data-amount="{{ $loan->loan_amount }}"
                                                            data-months="{{ $loan->months_to_pay }}"
                                                            data-start="{{ \Carbon\Carbon::parse($loan->start_deduction_month)->format('Y-m-d') }}"
                                                            data-bs-toggle="modal" data-bs-target="#editLoanModal"
                                                            title="تعديل الخطة">
                                                            <i class='mdi mdi-pencil-outline font-16'></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="p-5 text-muted">
                                                <i
                                                    class='mdi mdi-credit-card-off-outline fs-1 opacity-50 d-block mb-2'></i>
                                                <h6 class="fw-bold mb-0">لا توجد سلف مسجلة</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- التاب السابع: العهد والأصول المُسلمة --}}
            <div class="tab-pane fade" id="custody" role="tabpanel">
                <div class="card card-custom border-top border-primary border-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                            <h5 class="fw-bold text-primary mb-0"><i class='mdi mdi-briefcase-outline me-1'></i> العُهَد
                                المُسلمة للموظف</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center table-hover align-middle table-nowrap">
                                <thead class="bg-light">
                                    <tr>
                                        <th>الكود</th>
                                        <th class="text-start ps-3">وصف العهدة</th>
                                        <th>التصنيف</th>
                                        <th>تاريخ الاستلام</th>
                                        <th>حالة الإرجاع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employee->custodies as $custody)
                                        <tr>
                                            <td class="fw-bold text-secondary font-monospace">
                                                {{ $custody->asset_id ? $custody->asset->asset_code : '---' }}</td>
                                            <td class="text-dark fw-bold text-start ps-3">
                                                {{ $custody->item_name ?? ($custody->asset->transaction_details ?? 'بدون وصف') }}
                                            </td>
                                            <td>
                                                @if ($custody->asset_id)
                                                    <span
                                                        class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1">{{ $custody->asset->category->name_ar ?? '-' }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1">عهدة
                                                        عادية</span>
                                                @endif
                                            </td>
                                            <td><span
                                                    class="badge bg-light text-dark border px-2 py-1 font-monospace">{{ \Carbon\Carbon::parse($custody->receive_date)->format('Y-m-d') }}</span>
                                            </td>
                                            <td>
                                                @if ($custody->status == 'returned')
                                                    <span class="badge bg-success px-3">تم إخلاء الطرف</span>
                                                @else
                                                    <span class="badge bg-warning text-dark px-3">مع الموظف</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-5 text-muted">
                                                <i
                                                    class='mdi mdi-shield-check-outline fs-1 opacity-50 d-block mb-2 text-success'></i>
                                                <h6 class="fw-bold mb-0 text-success">لا توجد عهد مسجلة باسم الموظف</h6>
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
    </div>

    {{-- ============================== Modals المحمية ============================== --}}

    {{-- 🔥 مودال رفع وثيقة محمي 🔥 --}}
    @can('manage_employee_documents')
        <div class="modal fade" id="addDocumentModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-bottom-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-upload me-1'></i> رفع وثيقة رسمية</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.documents.store') }}" method="POST" enctype="multipart/form-data"
                        class="prevent-double-submit">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">نوع الوثيقة <span class="text-danger">*</span></label>
                                <select name="document_type" class="form-select bg-light fw-bold" required>
                                    <option value="بطاقة هوية / إقامة">بطاقة هوية / إقامة</option>
                                    <option value="جواز سفر">جواز سفر</option>
                                    <option value="بطاقة صحية / طبية">بطاقة صحية / طبية</option>
                                    <option value="أخرى">أخرى...</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">رقم الوثيقة <span class="text-danger">*</span></label>
                                <input type="text" name="document_number"
                                    class="form-control bg-light font-monospace fw-bold text-primary" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                <input type="date" name="expiry_date" class="form-control bg-light fw-bold text-center"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">صورة الوثيقة (اختياري)</label>
                                <input type="file" name="attachment" class="form-control bg-light border-dashed p-2"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm btn-submit"><i
                                    class="mdi mdi-content-save me-1"></i> حفظ الوثيقة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editDocumentModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-bottom-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-pencil-outline me-1'></i> تعديل الوثيقة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data" id="editDocumentForm"
                        class="prevent-double-submit">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">نوع الوثيقة <span class="text-danger">*</span></label>
                                <select name="document_type" id="edit_document_type" class="form-select bg-light fw-bold"
                                    required>
                                    <option value="بطاقة هوية / إقامة">بطاقة هوية / إقامة</option>
                                    <option value="جواز سفر">جواز سفر</option>
                                    <option value="بطاقة صحية / طبية">بطاقة صحية / طبية</option>
                                    <option value="أخرى">أخرى...</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">رقم الوثيقة <span class="text-danger">*</span></label>
                                <input type="text" name="document_number" id="edit_document_number"
                                    class="form-control bg-light font-monospace fw-bold text-primary" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                <input type="date" name="expiry_date" id="edit_expiry_date"
                                    class="form-control bg-light fw-bold text-center" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">تحديث صورة الوثيقة</label>
                                <input type="file" name="attachment" class="form-control bg-light border-dashed p-2"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted d-block mt-1">اتركه فارغاً إذا كنت لا تريد تغيير المرفق الحالي</small>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm btn-submit"><i
                                    class="mdi mdi-check me-1"></i> حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- 🔥 مودال السلف والقروض محمي 🔥 --}}
    @can('manage_payroll_financials')
        <div class="modal fade" id="addLoanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-bottom-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-warning"><i class='mdi mdi-cash-multiple me-1'></i> صرف سلفة جديدة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.loans.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">إجمالي مبلغ السلفة <span
                                        class="text-danger">*</span></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-success text-white border-success">OMR</span>
                                    <input type="number" step="0.001" name="loan_amount"
                                        class="form-control bg-light fw-bold text-success fs-5 text-center font-monospace border-success"
                                        required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">عدد أشهر السداد <span class="text-danger">*</span></label>
                                <input type="number" name="months_to_pay"
                                    class="form-control bg-light text-center font-monospace fw-bold" required min="1"
                                    placeholder="مثال: 5 أشهر">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">يبدأ الخصم من راتب شهر <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="start_deduction_month"
                                    class="form-control bg-light text-center fw-bold" required>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit"
                                class="btn btn-warning rounded-pill text-dark px-5 fw-bold shadow-sm btn-submit"><i
                                    class="mdi mdi-check-decagram me-1"></i> اعتماد وصرف السلفة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editLoanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-bottom-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-pencil-outline me-1'></i> تعديل خطة
                            سداد السلفة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editLoanForm" class="prevent-double-submit">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4 text-start">
                            <div class="alert alert-soft-danger text-danger border-danger fw-bold mb-4 bg-danger bg-opacity-10 p-3 text-center rounded-3 shadow-sm"
                                style="font-size: 13px;">
                                <i class='mdi mdi-shield-half-full fs-4 mb-1 d-block'></i> لأسباب أمنية ومحاسبية، لا يمكن تعديل
                                إجمالي مبلغ السلفة بعد الاعتماد. يمكنك فقط إعادة جدولة الدفعات وتأجيلها.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">إجمالي مبلغ السلفة <i
                                        class='mdi mdi-lock text-danger ms-1'></i></label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-danger text-white border-danger">OMR</span>
                                    <input type="number" step="0.001" id="edit_loan_amount"
                                        class="form-control bg-light text-danger fw-bolder fs-5 text-center font-monospace border-danger"
                                        readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">عدد الأشهر المتبقية للسداد <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="months_to_pay" id="edit_months_to_pay"
                                    class="form-control text-center font-monospace fw-bold bg-light" required min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">استئناف/بداية الخصم من شهر <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="start_deduction_month" id="edit_start_deduction_month"
                                    class="form-control text-center fw-bold bg-light" required>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm btn-submit"><i
                                    class="mdi mdi-check me-1"></i> حفظ خطة السداد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- مودال متابعة السلفة (عرض فقط) --}}
    <div class="modal fade" id="trackLoanModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-info text-white pb-3 rounded-top-4 border-bottom-0">
                    <h5 class="modal-title fw-bold"><i class='mdi mdi-chart-pie me-1'></i> متابعة سداد السلفة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light text-start">
                    <div class="row text-center mb-4 g-2">
                        <div class="col-4">
                            <div class="p-3 bg-white border rounded-3 shadow-sm h-100">
                                <small class="text-muted fw-bold d-block mb-1">الإجمالي</small>
                                <h6 class="fw-bolder text-dark mb-0 font-monospace" id="track_total_amount">0 ر.ع</h6>
                            </div>
                        </div>
                        <div class="col-4">
                            <div
                                class="p-3 bg-white border rounded-3 shadow-sm border-bottom border-success border-4 h-100">
                                <small class="text-muted fw-bold d-block mb-1">ما تم سداده</small>
                                <h6 class="fw-bolder text-success mb-0 font-monospace" id="track_paid_amount">0 ر.ع</h6>
                            </div>
                        </div>
                        <div class="col-4">
                            <div
                                class="p-3 bg-white border rounded-3 shadow-sm border-bottom border-danger border-4 h-100">
                                <small class="text-muted fw-bold d-block mb-1">المتبقي</small>
                                <h6 class="fw-bolder text-danger mb-0 font-monospace" id="track_remaining_amount">0 ر.ع
                                </h6>
                            </div>
                        </div>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">نسبة إنجاز السداد</h6>
                    <div class="progress shadow-sm" style="height: 20px; border-radius: 10px; background-color: #e9ecef;">
                        <div id="track_progress_bar"
                            class="progress-bar bg-info progress-bar-striped progress-bar-animated fw-bold text-dark"
                            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100">0%</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary px-5 rounded-pill fw-bold shadow-sm"
                        data-bs-dismiss="modal">إغلاق التقرير</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // حماية الفورم من الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري الحفظ...');
            });

            // أكواد الوثائق
            $('.btn-edit-doc').click(function() {
                let id = $(this).data('id');
                $('#edit_document_type').val($(this).data('type'));
                $('#edit_document_number').val($(this).data('number'));
                $('#edit_expiry_date').val($(this).data('expiry'));
                let url = "{{ route('admin.hr.documents.update', ':id') }}".replace(':id', id);
                $('#editDocumentForm').attr('action', url);
            });

            $('.delete-doc-form .btn-delete-doc').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: "هل أنت متأكد؟",
                    text: "سيتم حذف هذه الوثيقة نهائياً من سجل الموظف.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: "نعم، احذف",
                    cancelButtonText: "إلغاء"
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // أكواد السلف
            $('.btn-edit-loan').click(function() {
                let id = $(this).data('id');
                $('#edit_loan_amount').val($(this).data('amount'));
                $('#edit_months_to_pay').val($(this).data('months'));
                $('#edit_start_deduction_month').val($(this).data('start'));
                let url = "{{ route('admin.hr.loans.update', ':id') }}".replace(':id', id);
                $('#editLoanForm').attr('action', url);
            });

            $('.btn-track-loan').click(function() {
                let amount = parseFloat($(this).data('amount')) || 0;
                let paid = parseFloat($(this).data('paid')) || 0;
                let remaining = parseFloat($(this).data('remaining')) || 0;
                let progress = parseInt($(this).data('progress')) || 0;
                let currencyStr = " ر.ع";

                $('#track_total_amount').text(amount.toFixed(3) + currencyStr);
                $('#track_paid_amount').text(paid.toFixed(3) + currencyStr);
                $('#track_remaining_amount').text(remaining.toFixed(3) + currencyStr);

                let progressBar = $('#track_progress_bar');
                progressBar.css('width', progress + '%').text(progress + '%');
                progressBar.attr('aria-valuenow', progress);

                if (progress == 100) {
                    progressBar.removeClass('bg-info bg-secondary text-dark').addClass(
                        'bg-success text-white');
                } else if (progress > 0) {
                    progressBar.removeClass('bg-success bg-secondary text-dark').addClass(
                        'bg-info text-white');
                } else {
                    progressBar.removeClass('bg-success bg-info text-white').addClass(
                        'bg-secondary text-dark');
                }
            });

            // رسائل النظام باستخدام SweetAlert
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'عملية ناجحة',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    html: `
                        <ul class="text-start mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `
                });
            @endif
        });
    </script>
@endpush
