@extends('backend.layouts.master')
@section('title', 'المخالفات والجزاءات')
@section('title_page', 'الموارد البشرية - سجل المخالفات والجزاءات')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .page-wrapper {
            padding: 25px;
        }

        /* 🌟 كروت الإحصائيات 🌟 */
        .stat-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            top: -20px;
            right: -20px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0.1;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .stat-primary {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 5px solid #3b82f6;
        }

        .stat-primary::before {
            background: #2563eb;
        }

        .stat-primary .icon-box {
            color: #3b82f6;
            background: #bfdbfe;
        }

        .stat-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-left: 5px solid #ef4444;
        }

        .stat-danger::before {
            background: #dc2626;
        }

        .stat-danger .icon-box {
            color: #ef4444;
            background: #fecaca;
        }

        .stat-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-left: 5px solid #f59e0b;
        }

        .stat-warning::before {
            background: #d97706;
        }

        .stat-warning .icon-box {
            color: #f59e0b;
            background: #fde68a;
        }

        .stat-success {
            background: linear-gradient(135deg, #f0fdf4 0%, #d1fae5 100%);
            border-left: 5px solid #10b981;
        }

        .stat-success::before {
            background: #059669;
        }

        .stat-success .icon-box {
            color: #10b981;
            background: #a7f3d0;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 900;
            font-family: 'Courier New', Courier, monospace;
            margin-top: 5px;
            color: #1e293b;
        }

        .stat-label {
            color: #64748b;
            font-weight: 700;
            font-size: 13px;
        }

        /* 🌟 كارت الفلاتر 🌟 */
        .filter-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            background-color: #fff;
        }

        .filter-label {
            font-weight: 800;
            color: #64748b;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-input {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            font-weight: 600;
            color: #334155;
            transition: all 0.3s ease;
        }

        .filter-input:focus {
            background-color: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* 🌟 الجدول الاحترافي 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
        }

        .table-penalty {
            margin-bottom: 0;
        }

        .table-penalty thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .table-penalty tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
            transition: background-color 0.2s ease;
        }

        .table-penalty tbody tr:hover td {
            background-color: #fbfccb !important;
        }

        .table-penalty tbody tr.row-applied {
            background-color: #fef2f2;
        }

        .table-penalty tbody tr.row-applied:hover {
            background-color: #fee2e2 !important;
        }

        /* 🌟 شارات الإجراء 🌟 */
        .badge-action {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-verbal {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-written {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-deduction {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .badge-suspension {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        /* 🌟 شارات الحالة 🌟 */
        .badge-status {
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-applied {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .badge-pending {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        /* 🌟 قيمة الخصم 🌟 */
        .deduction-value {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 15px;
            color: #ef4444;
        }

        .deduction-days {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 800;
            font-size: 16px;
            color: #dc2626;
        }

        /* 🌟 معلومات الموظف 🌟 */
        .employee-name {
            font-weight: 800;
            color: #1e293b;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .employee-dept {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
        }

        /* 🌟 أزرار الإجراءات 🌟 */
        .action-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .action-btn-edit {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .action-btn-edit:hover {
            background: #2563eb;
            color: #fff;
        }

        .action-btn-delete {
            background: #fef2f2;
            color: #ef4444;
            border-color: #fecaca;
        }

        .action-btn-delete:hover {
            background: #ef4444;
            color: #fff;
        }

        /* 🌟 زر الإضافة 🌟 */
        .btn-add {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
            color: #fff;
        }

        /* 🌟 المودال 🌟 */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 25px;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 15px 25px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 13px;
            margin-bottom: 6px;
        }

        /* 🌟 تنبيه الحساب التلقائي 🌟 */
        .calculation-alert {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 10px;
            padding: 12px 15px;
            margin-top: 10px;
            font-size: 13px;
            color: #92400e;
        }

        .calculation-alert i {
            font-size: 18px;
            margin-left: 5px;
        }

        /* 🌟 Dark Mode 🌟 */
        body[data-layout-color="dark"] {
            background-color: #2b313a;
        }

        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .stat-card,
        body[data-layout-color="dark"] .filter-card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .stat-primary {
            background: #1e293b;
            border-color: #3b82f6;
        }

        body[data-layout-color="dark"] .stat-danger {
            background: #7f1d1d;
            border-color: #ef4444;
        }

        body[data-layout-color="dark"] .stat-warning {
            background: #451a03;
            border-color: #f59e0b;
        }

        body[data-layout-color="dark"] .stat-success {
            background: #064e3b;
            border-color: #10b981;
        }

        body[data-layout-color="dark"] .stat-value,
        body[data-layout-color="dark"] .employee-name {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .table-penalty thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-penalty tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-penalty tbody tr:hover td {
            background-color: #38414e !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .filter-input {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .calculation-alert {
            background: #451a03;
            border-color: #92400e;
            color: #fde68a;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 العنوان وزر الإضافة 🌟 --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                    <i class='bx bx-error text-danger fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">المخالفات والجزاءات</h4>
                    <p class="text-muted mb-0 font-13">سجل الإجراءات التأديبية والجزاءات المالية للموظفين</p>
                </div>
            </div>

            @can('create_penalty')
                <button class="btn btn-add shadow-sm" data-bs-toggle="modal" data-bs-target="#addPenaltyModal">
                    <i class='bx bx-plus-circle me-1 fs-5'></i> تسجيل مخالفة جديدة
                </button>
            @endcan
        </div>

        {{-- 🌟 كروت الإحصائيات 🌟 --}}
        <div class="row g-4 mb-4">
            {{-- إجمالي المخالفات --}}
            <div class="col-md-3">
                <div class="stat-card stat-primary p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي المخالفات</div>
                            <div class="stat-value text-primary">{{ $totalPenalties }}</div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-shield-x'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الخصومات المالية --}}
            <div class="col-md-3">
                <div class="stat-card stat-danger p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">خصومات مالية</div>
                            <div class="stat-value text-danger">{{ $deductionCount }}</div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-money-withdraw'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الإنذارات --}}
            <div class="col-md-3">
                <div class="stat-card stat-warning p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إنذارات وتنبيهات</div>
                            <div class="stat-value text-warning">{{ $warningCount }}</div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-message-error'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- إجمالي قيم الخصم --}}
            <div class="col-md-3">
                <div class="stat-card stat-success p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي قيم الخصم</div>
                            <div class="stat-value text-success">{{ number_format($totalDeductionAmount ?? 0, 3) }} <small
                                    class="fs-6 opacity-75">ر.ع</small></div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-calculator'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 كارت الفلاتر 🌟 --}}
        <div class="filter-card mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.hr.penalties.index') }}" class="row align-items-end g-3">
                    {{-- الشهر --}}
                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-calendar me-1'></i> الشهر</label>
                        <select name="month" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ ($month ?? '') == $m ? 'selected' : '' }}>
                                    {{ $m }} -
                                    {{ ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$m - 1] }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- السنة --}}
                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-calendar-check me-1'></i> السنة</label>
                        <input type="number" name="year" class="form-control filter-input text-center"
                            value="{{ $year ?? date('Y') }}">
                    </div>

                    {{-- الموظف --}}
                    <div class="col-md-3">
                        <label class="filter-label"><i class='bx bx-user me-1'></i> الموظف</label>
                        <select name="employee_id" class="form-select filter-input select2-filter">
                            <option value="">-- جميع الموظفين --</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    {{ ($employeeId ?? '') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- نوع الإجراء --}}
                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-flag me-1'></i> نوع الإجراء</label>
                        <select name="action_type" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            <option value="verbal_warning" {{ ($actionType ?? '') == 'verbal_warning' ? 'selected' : '' }}>
                                لفت نظر شفهي</option>
                            <option value="written_warning"
                                {{ ($actionType ?? '') == 'written_warning' ? 'selected' : '' }}>إنذار كتابي</option>
                            <option value="salary_deduction"
                                {{ ($actionType ?? '') == 'salary_deduction' ? 'selected' : '' }}>خصم من الراتب</option>
                            <option value="suspension" {{ ($actionType ?? '') == 'suspension' ? 'selected' : '' }}>إيقاف
                                عن العمل</option>
                        </select>
                    </div>

                    {{-- زر الفلترة --}}
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark flex-grow-1 fw-bold py-2 shadow-sm rounded-3">
                            <i class='bx bx-filter-alt me-1'></i> تصفية
                        </button>
                        <a href="{{ route('admin.hr.penalties.index') }}"
                            class="btn btn-light border fw-bold px-3 py-2 shadow-sm rounded-3" title="إعادة ضبط">
                            <i class='bx bx-refresh fs-5'></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 جدول المخالفات 🌟 --}}
        <div class="card-premium">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                        <i class='bx bx-list-ul text-warning fs-4'></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">سجل الإجراءات التأديبية</h5>
                        <small class="text-muted">عرض {{ $penalties->count() }} مخالفة</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-penalty table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="text-start ps-4" style="width: 18%;">الموظف</th>
                                <th style="width: 10%;">التاريخ</th>
                                <th style="width: 18%;">نوع المخالفة</th>
                                <th style="width: 14%;">الإجراء</th>
                                <th style="width: 8%;">الأيام</th>
                                <th style="width: 12%;">قيمة الخصم</th>
                                <th style="width: 10%;">الحالة</th>
                                @canany(['edit_penalty', 'delete_penalty'])
                                    <th style="width: 10%;">الإجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penalties as $penalty)
                                @php
                                    $rowClass = $penalty->applied_to_payroll ? 'row-applied' : '';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    {{-- الموظف --}}
                                    <td class="text-start ps-4">
                                        <div class="employee-name">
                                            <i class='bx bx-user-circle text-primary me-1'></i>
                                            {{ $penalty->employee->full_name_ar ?? 'موظف محذوف' }}
                                        </div>
                                        <div class="employee-dept">
                                            <i class='bx bx-building me-1'></i>
                                            {{ $penalty->employee->department->name_ar ?? 'بدون قسم' }}
                                        </div>
                                    </td>

                                    {{-- التاريخ --}}
                                    <td>
                                        <span class="font-monospace fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($penalty->incident_date)->format('Y-m-d') }}
                                        </span>
                                    </td>

                                    {{-- نوع المخالفة --}}
                                    <td>
                                        <span class="text-dark fw-bold text-wrap"
                                            style="max-width: 200px; display: inline-block;">
                                            {{ $penalty->violation_type }}
                                        </span>
                                    </td>

                                    {{-- الإجراء --}}
                                    <td>
                                        @if ($penalty->penalty_action == 'verbal_warning')
                                            <span class="badge-action badge-verbal">
                                                <i class='bx bx-message-error'></i> لفت نظر شفهي
                                            </span>
                                        @elseif($penalty->penalty_action == 'written_warning')
                                            <span class="badge-action badge-written">
                                                <i class='bx bx-file'></i> إنذار كتابي
                                            </span>
                                        @elseif($penalty->penalty_action == 'salary_deduction')
                                            <span class="badge-action badge-deduction">
                                                <i class='bx bx-money'></i> خصم من الراتب
                                            </span>
                                        @else
                                            <span class="badge-action badge-suspension">
                                                <i class='bx bx-user-x'></i> إيقاف عن العمل
                                            </span>
                                        @endif
                                    </td>

                                    {{-- أيام الخصم --}}
                                    <td>
                                        @if ($penalty->deduction_days > 0)
                                            <span class="deduction-days">{{ $penalty->deduction_days }}</span>
                                            <small class="text-muted d-block">يوم</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- قيمة الخصم --}}
                                    <td>
                                        @if ($penalty->deduction_amount > 0)
                                            <span class="deduction-value">
                                                -{{ number_format($penalty->deduction_amount, 3) }}
                                            </span>
                                            <small class="text-muted d-block">ر.ع</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- الحالة --}}
                                    <td>
                                        @if ($penalty->applied_to_payroll)
                                            <span class="badge-status badge-applied">
                                                <i class='bx bx-check-circle'></i> مُطبق
                                            </span>
                                        @else
                                            <span class="badge-status badge-pending">
                                                <i class='bx bx-time-five'></i> معلق
                                            </span>
                                        @endif
                                    </td>

                                    {{-- الإجراءات --}}
                                    @canany(['edit_penalty', 'delete_penalty'])
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                @can('edit_penalty')
                                                    <button type="button" class="action-btn action-btn-edit btn-edit-penalty"
                                                        data-id="{{ $penalty->id }}"
                                                        data-employee="{{ $penalty->employee_id }}"
                                                        data-type="{{ $penalty->violation_type }}"
                                                        data-date="{{ \Carbon\Carbon::parse($penalty->incident_date)->format('Y-m-d') }}"
                                                        data-action="{{ $penalty->penalty_action }}"
                                                        data-days="{{ $penalty->deduction_days }}"
                                                        data-details="{{ $penalty->details }}"
                                                        data-notes="{{ $penalty->notes }}" data-bs-toggle="modal"
                                                        data-bs-target="#editPenaltyModal" title="تعديل">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                @can('delete_penalty')
                                                    <form action="{{ route('admin.hr.penalties.destroy', $penalty->id) }}"
                                                        method="POST" class="d-inline delete-penalty-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="action-btn action-btn-delete btn-delete-penalty"
                                                            title="حذف">
                                                            <i class='bx bx-trash'></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->canany(['edit_penalty', 'delete_penalty']) ? 8 : 7 }}"
                                        class="p-5 text-center">
                                        <div class="py-4">
                                            <i class='bx bx-shield-check text-muted opacity-25'
                                                style="font-size: 80px;"></i>
                                            <h5 class="text-muted fw-bold mt-3">لا توجد أي جزاءات أو مخالفات مسجلة</h5>
                                            <p class="text-muted mb-0">جميع الموظفين ملتزمون بأنظمة العمل</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- 🔥 مودال إضافة مخالفة 🔥 --}}
    @can('create_penalty')
        <div class="modal fade" id="addPenaltyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-error text-danger me-2'></i>
                            تسجيل إجراء تأديبي
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.penalties.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            {{-- الموظف --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-user text-primary me-1'></i> الموظف المخالف
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="employee_id" class="form-select select2-init" required style="width: 100%;">
                                    <option value="" disabled selected>-- اختر الموظف --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->full_name_ar }} ({{ $emp->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- نوع المخالفة --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-detail text-primary me-1'></i> نوع/وصف المخالفة
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="violation_type" class="form-control"
                                    placeholder="مثال: غياب بدون إذن، تأخير متكرر..." required>
                            </div>

                            {{-- التاريخ والإجراء --}}
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-calendar text-primary me-1'></i> تاريخ الواقعة
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="incident_date" class="form-control text-center fw-bold"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-flag text-primary me-1'></i> الإجراء الإداري
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="penalty_action" id="penaltyAction" class="form-select fw-bold" required
                                        onchange="toggleDeductionDays()">
                                        <option value="verbal_warning">🟡 توجيه شفهي</option>
                                        <option value="written_warning">🟠 إنذار كتابي</option>
                                        <option value="salary_deduction">🔴 خصم مالي من الراتب</option>
                                        <option value="suspension">⚫ إيقاف مؤقت عن العمل</option>
                                    </select>
                                </div>
                            </div>

                            {{-- أيام الخصم (يظهر فقط عند اختيار خصم مالي) --}}
                            <div class="mb-3" id="deductionDiv" style="display: none;">
                                <label class="form-label text-danger fw-bold">
                                    <i class='bx bx-money text-danger me-1'></i> عدد أيام الخصم
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.5" min="0" max="999" name="deduction_days"
                                    id="deductionDays"
                                    class="form-control border-danger font-monospace fs-5 text-danger text-center fw-bolder"
                                    value="0">
                                <div class="calculation-alert">
                                    <i class='bx bx-info-circle'></i>
                                    <strong>حساب تلقائي:</strong> سيقوم النظام بضرب عدد الأيام في الأجرة اليومية للموظف (الراتب
                                    الأساسي ÷ 30) وخصمها تلقائياً من مسير الرواتب.
                                </div>
                            </div>

                            {{-- التفاصيل --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-file text-primary me-1'></i> تفاصيل المخالفة
                                </label>
                                <textarea name="details" class="form-control" rows="2" placeholder="تفاصيل إضافية عن الواقعة..."></textarea>
                            </div>

                            {{-- ملاحظات --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-comment-detail text-primary me-1'></i> ملاحظات إدارية
                                </label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات داخلية (لا تظهر للموظف)..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4 btn-submit">
                                <i class='bx bx-check me-1'></i> تسجيل واعتماد العقوبة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- 🔥 مودال تعديل مخالفة 🔥 --}}
    @can('edit_penalty')
        <div class="modal fade" id="editPenaltyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-edit text-primary me-2'></i>
                            تعديل الإجراء التأديبي
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editPenaltyForm" class="prevent-double-submit">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            {{-- الموظف --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-user text-primary me-1'></i> الموظف
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="employee_id" id="edit_employee_id" class="form-select select2-edit" required
                                    style="width: 100%;">
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->full_name_ar }} ({{ $emp->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- نوع المخالفة --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-detail text-primary me-1'></i> نوع المخالفة
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="violation_type" id="edit_violation_type" class="form-control"
                                    required>
                            </div>

                            {{-- التاريخ والإجراء --}}
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-calendar text-primary me-1'></i> تاريخ الواقعة
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="incident_date" id="edit_incident_date"
                                        class="form-control text-center fw-bold" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-flag text-primary me-1'></i> الإجراء الإداري
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="penalty_action" id="edit_penalty_action" class="form-select fw-bold"
                                        required onchange="toggleEditDeductionDays()">
                                        <option value="verbal_warning">🟡 توجيه شفهي</option>
                                        <option value="written_warning">🟠 إنذار كتابي</option>
                                        <option value="salary_deduction">🔴 خصم مالي من الراتب</option>
                                        <option value="suspension">⚫ إيقاف مؤقت عن العمل</option>
                                    </select>
                                </div>
                            </div>

                            {{-- أيام الخصم --}}
                            <div class="mb-3" id="editDeductionDiv" style="display: none;">
                                <label class="form-label text-danger fw-bold">
                                    <i class='bx bx-money text-danger me-1'></i> عدد أيام الخصم
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.5" min="0" max="999" name="deduction_days"
                                    id="edit_deduction_days"
                                    class="form-control border-danger font-monospace fs-5 text-danger text-center fw-bolder"
                                    value="0">
                                <div class="calculation-alert">
                                    <i class='bx bx-info-circle'></i>
                                    <strong>تحديث تلقائي:</strong> سيتم تحديث القيمة المالية في الرواتب بناءً على هذا التعديل.
                                </div>
                            </div>

                            {{-- التفاصيل --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-file text-primary me-1'></i> تفاصيل المخالفة
                                </label>
                                <textarea name="details" id="edit_details" class="form-control" rows="2"></textarea>
                            </div>

                            {{-- ملاحظات --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-comment-detail text-primary me-1'></i> ملاحظات إدارية
                                </label>
                                <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 btn-submit">
                                <i class='bx bx-check me-1'></i> حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // دالة إظهار/إخفاء حقل أيام الخصم (المودال الأول)
        function toggleDeductionDays() {
            let action = document.getElementById('penaltyAction').value;
            let deductionDiv = document.getElementById('deductionDiv');
            let deductionInput = document.getElementById('deductionDays');

            if (action === 'salary_deduction') {
                $(deductionDiv).slideDown();
                if (deductionInput.value == 0) deductionInput.value = 1;
            } else {
                $(deductionDiv).slideUp();
                deductionInput.value = 0;
            }
        }

        // دالة إظهار/إخفاء حقل أيام الخصم (المودال الثاني)
        function toggleEditDeductionDays() {
            let action = document.getElementById('edit_penalty_action').value;
            let deductionDiv = document.getElementById('editDeductionDiv');
            let deductionInput = document.getElementById('edit_deduction_days');

            if (action === 'salary_deduction') {
                $(deductionDiv).slideDown();
            } else {
                $(deductionDiv).slideUp();
                deductionInput.value = 0;
            }
        }

        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل Select2 للفلاتر
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // تفعيل Select2 داخل مودال الإضافة
            $('#addPenaltyModal').on('shown.bs.modal', function() {
                $('.select2-init').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#addPenaltyModal')
                });
            });

            // تفعيل Select2 داخل مودال التعديل
            $('#editPenaltyModal').on('shown.bs.modal', function() {
                $('.select2-edit').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#editPenaltyModal')
                });
            });

            // حماية الفورم من الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري الحفظ...');
            });

            // تمرير البيانات لمودال التعديل
            $('.btn-edit-penalty').click(function() {
                let id = $(this).data('id');

                // تحديث قيمة الـ Select2
                $('#edit_employee_id').val($(this).data('employee')).trigger('change');

                $('#edit_violation_type').val($(this).data('type'));
                $('#edit_incident_date').val($(this).data('date'));
                $('#edit_penalty_action').val($(this).data('action'));
                $('#edit_deduction_days').val($(this).data('days'));
                $('#edit_details').val($(this).data('details'));
                $('#edit_notes').val($(this).data('notes'));

                let url = "{{ route('admin.hr.penalties.update', ':id') }}".replace(':id', id);
                $('#editPenaltyForm').attr('action', url);

                // تشغيل دالة الإظهار والإخفاء
                toggleEditDeductionDays();
            });

            // تأكيد الحذف
            $('.btn-delete-penalty').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تحذير محاسبي وإداري!',
                    html: "هل أنت متأكد من مسح هذه المخالفة؟<br><br><small class='text-danger'>إذا كان هناك خصم مالي مرتبط بها وتم صرفه في راتب سابق، سيرفض النظام الحذف لحماية القيود.</small>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، حاول الحذف',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // رسائل النجاح
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            // رسائل الخطأ
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'مرفوض',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            @endif

            // أخطاء التحقق
            @if ($errors->any())
                let errorHtml =
                    '<ul class="text-start text-danger fw-bold mb-0" style="list-style: none; padding:0;">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li><i class="bx bx-error-circle me-1"></i> {{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في إدخال البيانات',
                    html: errorHtml,
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>
@endpush
