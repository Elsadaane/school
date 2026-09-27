@extends('backend.layouts.master')
@section('title', 'إدارة السلف والقروض')
@section('title_page', 'الموارد البشرية - سلف الموظفين')

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
            font-size: 24px;
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

        .table-loan {
            margin-bottom: 0;
        }

        .table-loan thead th {
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

        .table-loan tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
            transition: background-color 0.2s ease;
        }

        .table-loan tbody tr:hover td {
            background-color: #fbfccb !important;
        }

        .table-loan tbody tr.row-completed {
            background-color: #f0fdf4;
        }

        .table-loan tbody tr.row-completed:hover {
            background-color: #dcfce7 !important;
        }

        /* 🌟 شارات الحالة 🌟 */
        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-active {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .badge-completed {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-canceled {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* 🌟 المبالغ 🌟 */
        .amount-display {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 15px;
        }

        .amount-loan {
            color: #ef4444;
        }

        .amount-paid {
            color: #10b981;
        }

        .amount-remaining {
            color: #f59e0b;
        }

        /* 🌟 شريط التقدم 🌟 */
        .progress-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 140px;
        }

        .progress-custom {
            flex-grow: 1;
            height: 10px;
            border-radius: 10px;
            background-color: #e2e8f0;
            overflow: hidden;
        }

        .progress-bar-custom {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .progress-partial {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .progress-full {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .progress-percent {
            font-size: 12px;
            font-weight: 800;
            color: #475569;
            font-family: 'Courier New', monospace;
            min-width: 35px;
            text-align: left;
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
            cursor: pointer;
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

        .action-btn-pay {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }

        .action-btn-pay:hover {
            background: #059669;
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

        .action-btn-disabled {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .action-btn-disabled:hover {
            transform: none;
            box-shadow: none;
        }

        /* 🌟 زر الإضافة 🌟 */
        .btn-add {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
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
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 1px solid #3b82f6;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #1e40af;
        }

        .calculation-alert i {
            font-size: 18px;
            margin-left: 5px;
        }

        /* 🌟 ملخص السلفة في مودال السداد 🌟 */
        .loan-summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 14px;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 800;
            font-size: 16px;
        }

        .summary-row .label {
            color: #64748b;
            font-weight: 600;
        }

        .summary-row .value {
            color: #1e293b;
            font-weight: 800;
            font-family: 'Courier New', monospace;
        }

        /* 🌟 Dark Mode 🌟 */
        body[data-layout-color="dark"] {
            background-color: #2b313a;
        }

        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .stat-card,
        body[data-layout-color="dark"] .filter-card,
        body[data-layout-color="dark"] .modal-content {
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

        body[data-layout-color="dark"] .table-loan thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-loan tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-loan tbody tr:hover td {
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
            background: #1e293b;
            border-color: #3b82f6;
            color: #93c5fd;
        }

        body[data-layout-color="dark"] .loan-summary-box {
            background: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .summary-row {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .summary-row .label {
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .summary-row .value {
            color: #f8fafc;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 العنوان وزر الإضافة 🌟 --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                    <i class='bx bx-wallet text-primary fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">سلف وقروض الموظفين</h4>
                    <p class="text-muted mb-0 font-13">إدارة السلف وجدولة الأقساط ومتابعة السداد</p>
                </div>
            </div>

            @can('manage_hr_loans_penalties')
                <button class="btn btn-add shadow-sm" data-bs-toggle="modal" data-bs-target="#addLoanModal">
                    <i class='bx bx-plus-circle me-1 fs-5'></i> تسجيل سلفة جديدة
                </button>
            @endcan
        </div>

        {{-- 🌟 كروت الإحصائيات 🌟 --}}
        <div class="row g-4 mb-4">
            {{-- إجمالي السلف --}}
            <div class="col-md-3">
                <div class="stat-card stat-primary p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي السلف</div>
                            <div class="stat-value text-primary">{{ $totalLoans }}</div>
                            <small class="text-muted font-monospace">{{ number_format($totalLoanAmount, 3) }} ر.ع</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-wallet-alt'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- السلف النشطة --}}
            <div class="col-md-3">
                <div class="stat-card stat-warning p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">سلف قيد السداد</div>
                            <div class="stat-value text-warning">{{ $activeLoans }}</div>
                            <small class="text-muted font-monospace">جاري الخصم</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-time-five'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- السلف المكتملة --}}
            <div class="col-md-3">
                <div class="stat-card stat-success p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">سلف مكتملة</div>
                            <div class="stat-value text-success">{{ $completedLoans }}</div>
                            <small class="text-muted font-monospace">تم السداد</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-check-circle'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- إجمالي المتبقي --}}
            <div class="col-md-3">
                <div class="stat-card stat-danger p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي المتبقي</div>
                            <div class="stat-value text-danger">{{ number_format($totalRemainingAmount, 3) }}</div>
                            <small class="text-muted font-monospace">ر.ع مستحقة</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-trending-down'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 كارت الفلاتر 🌟 --}}
        <div class="filter-card mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.hr.loans.index') }}" class="row align-items-end g-3">
                    {{-- الموظف --}}
                    <div class="col-md-3">
                        <label class="filter-label"><i class='bx bx-user me-1'></i> الموظف</label>
                        <select name="employee_id" class="form-select filter-input select2-filter">
                            <option value="">-- جميع الموظفين --</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" {{ ($employeeId ?? '') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- الحالة --}}
                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-flag me-1'></i> الحالة</label>
                        <select name="status" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>في الانتظار
                            </option>
                            <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>نشطة</option>
                            <option value="completed" {{ ($status ?? '') == 'completed' ? 'selected' : '' }}>مكتملة
                            </option>
                            <option value="canceled" {{ ($status ?? '') == 'canceled' ? 'selected' : '' }}>ملغاة</option>
                        </select>
                    </div>

                    {{-- شهر البدء --}}
                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-calendar me-1'></i> شهر البدء</label>
                        <select name="start_month" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}"
                                    {{ ($startMonth ?? '') == $m ? 'selected' : '' }}>
                                    {{ $m }} -
                                    {{ ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$m - 1] }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- السنة --}}
                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-calendar-check me-1'></i> السنة</label>
                        <input type="number" name="start_year" class="form-control filter-input text-center"
                            value="{{ $startYear ?? date('Y') }}">
                    </div>

                    {{-- أزرار الفلترة --}}
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark flex-grow-1 fw-bold py-2 shadow-sm rounded-3">
                            <i class='bx bx-filter-alt me-1'></i> تصفية
                        </button>
                        <a href="{{ route('admin.hr.loans.index') }}"
                            class="btn btn-light border fw-bold px-3 py-2 shadow-sm rounded-3" title="إعادة ضبط">
                            <i class='bx bx-refresh fs-5'></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 جدول السلف 🌟 --}}
        <div class="card-premium">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                        <i class='bx bx-list-ul text-primary fs-4'></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">سجل السلف والقروض</h5>
                        <small class="text-muted">عرض {{ $loans->count() }} سلفة</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-loan table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="text-start ps-4" style="width: 16%;">الموظف</th>
                                <th style="width: 10%;">المبلغ</th>
                                <th style="width: 10%;">القسط</th>
                                <th style="width: 10%;">بداية الخصم</th>
                                <th style="width: 9%;">المدفوع</th>
                                <th style="width: 9%;">المتبقي</th>
                                <th style="width: 14%;">نسبة السداد</th>
                                <th style="width: 10%;">الحالة</th>
                                @can('manage_hr_loans_penalties')
                                    <th style="width: 12%;">الإجراءات</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                                @php
                                    $rowClass = $loan->status === 'completed' ? 'row-completed' : '';
                                    $progress =
                                        $loan->loan_amount > 0 ? ($loan->paid_amount / $loan->loan_amount) * 100 : 0;
                                    $progressClass = $progress >= 100 ? 'progress-full' : 'progress-partial';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    {{-- الموظف --}}
                                    <td class="text-start ps-4">
                                        <div class="employee-name">
                                            <i class='bx bx-user-circle text-primary me-1'></i>
                                            {{ $loan->employee->full_name_ar ?? 'موظف محذوف' }}
                                        </div>
                                        <div class="employee-dept">
                                            <i class='bx bx-building me-1'></i>
                                            {{ $loan->employee->department->name_ar ?? 'بدون قسم' }}
                                        </div>
                                    </td>

                                    {{-- مبلغ السلفة --}}
                                    <td>
                                        <span class="amount-display amount-loan">
                                            {{ number_format($loan->loan_amount, 3) }}
                                        </span>
                                        <small class="text-muted d-block">ر.ع</small>
                                    </td>

                                    {{-- القسط الشهري --}}
                                    <td>
                                        <span class="amount-display text-primary">
                                            {{ number_format($loan->monthly_installment, 3) }}
                                        </span>
                                        <small class="text-muted d-block">شهرياً</small>
                                    </td>

                                    {{-- بداية الخصم --}}
                                    <td>
                                        <span class="font-monospace fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($loan->start_deduction_month)->format('Y-m') }}
                                        </span>
                                    </td>

                                    {{-- المدفوع --}}
                                    <td>
                                        <span class="amount-display amount-paid">
                                            {{ number_format($loan->paid_amount, 3) }}
                                        </span>
                                        <small class="text-muted d-block">ر.ع</small>
                                    </td>

                                    {{-- المتبقي --}}
                                    <td>
                                        <span class="amount-display amount-remaining">
                                            {{ number_format($loan->remaining_amount, 3) }}
                                        </span>
                                        <small class="text-muted d-block">ر.ع</small>
                                    </td>

                                    {{-- نسبة السداد --}}
                                    <td>
                                        <div class="progress-wrapper">
                                            <div class="progress-custom">
                                                <div class="progress-bar-custom {{ $progressClass }}"
                                                    style="width: {{ min($progress, 100) }}%"></div>
                                            </div>
                                            <span class="progress-percent">{{ round($progress) }}%</span>
                                        </div>
                                    </td>

                                    {{-- الحالة --}}
                                    <td>
                                        @if ($loan->status == 'active')
                                            <span class="badge-status badge-active">
                                                <i class='bx bx-loader-circle bx-spin'></i> نشطة
                                            </span>
                                        @elseif ($loan->status == 'completed')
                                            <span class="badge-status badge-completed">
                                                <i class='bx bx-check-circle'></i> مكتملة
                                            </span>
                                        @elseif ($loan->status == 'pending')
                                            <span class="badge-status badge-pending">
                                                <i class='bx bx-time-five'></i> في الانتظار
                                            </span>
                                        @else
                                            <span class="badge-status badge-canceled">
                                                <i class='bx bx-x-circle'></i> ملغاة
                                            </span>
                                        @endif
                                    </td>

                                    {{-- الإجراءات --}}
                                    @can('manage_hr_loans_penalties')
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                {{-- زر السداد اليدوي --}}
                                                @if ($loan->status == 'active' && $loan->remaining_amount > 0)
                                                    <button type="button" class="action-btn action-btn-pay btn-pay-loan"
                                                        data-id="{{ $loan->id }}"
                                                        data-employee="{{ $loan->employee->full_name_ar ?? '---' }}"
                                                        data-remaining="{{ $loan->remaining_amount }}"
                                                        data-loan-amount="{{ $loan->loan_amount }}"
                                                        data-paid="{{ $loan->paid_amount }}"
                                                        data-installment="{{ $loan->monthly_installment }}"
                                                        data-bs-toggle="modal" data-bs-target="#payLoanModal"
                                                        title="سداد دفعة">
                                                        <i class='bx bx-credit-card'></i>
                                                    </button>
                                                @endif

                                                {{-- زر إعادة الجدولة --}}
                                                @if ($loan->status == 'active' && $loan->remaining_amount > 0)
                                                    <button type="button" class="action-btn action-btn-edit btn-edit-loan"
                                                        data-id="{{ $loan->id }}"
                                                        data-months="{{ $loan->months_to_pay }}"
                                                        data-start="{{ \Carbon\Carbon::parse($loan->start_deduction_month)->format('Y-m-d') }}"
                                                        data-bs-toggle="modal" data-bs-target="#editLoanModal"
                                                        title="إعادة جدولة">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @else
                                                    <span class="action-btn action-btn-disabled" title="مكتملة السداد">
                                                        <i class='bx bx-lock-alt'></i>
                                                    </span>
                                                @endif

                                                {{-- زر الحذف --}}
                                                @if ($loan->paid_amount <= 0)
                                                    <form action="{{ route('admin.hr.loans.destroy', $loan->id) }}"
                                                        method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="action-btn action-btn-delete btn-delete"
                                                            title="حذف">
                                                            <i class='bx bx-trash'></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('manage_hr_loans_penalties') ? 9 : 8 }}"
                                        class="p-5 text-center">
                                        <div class="py-4">
                                            <i class='bx bx-wallet text-muted opacity-25' style="font-size: 80px;"></i>
                                            <h5 class="text-muted fw-bold mt-3">لا توجد سلف مسجلة حالياً</h5>
                                            <p class="text-muted mb-0">قم بتسجيل سلفة جديدة أو تغيير فلاتر البحث</p>
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

    {{-- 🔥 مودال إضافة سلفة جديدة 🔥 --}}
    @can('manage_hr_loans_penalties')
        <div class="modal fade" id="addLoanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-wallet text-primary me-2'></i>
                            تسجيل سلفة جديدة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.loans.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            {{-- الموظف --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-user text-primary me-1'></i> الموظف
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

                            {{-- مبلغ السلفة --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-money text-primary me-1'></i> مبلغ السلفة الإجمالي
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.001" name="loan_amount"
                                        class="form-control fw-bold text-center font-monospace fs-5" required min="0.001">
                                    <span class="input-group-text fw-bold">ر.ع</span>
                                </div>
                            </div>

                            {{-- عدد الأشهر وتاريخ البدء --}}
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-calendar text-primary me-1'></i> عدد أشهر السداد
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="months_to_pay"
                                        class="form-control text-center font-monospace fw-bold" placeholder="مثال: 6" required
                                        min="1" max="60">
                                    <small class="text-muted">الحد الأقصى: 60 شهراً</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-calendar-check text-primary me-1'></i> يبدأ الخصم من
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="start_deduction_month"
                                        class="form-control text-center fw-bold" required
                                        value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                                </div>
                            </div>

                            {{-- ملاحظات --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-comment-detail text-primary me-1'></i> ملاحظات
                                </label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="أسباب السلفة أو أي ملاحظات..."></textarea>
                            </div>

                            <div class="calculation-alert">
                                <i class='bx bx-info-circle'></i>
                                <strong>حساب تلقائي:</strong> سيقوم النظام بقسمة المبلغ الإجمالي على عدد الأشهر لتحديد قيمة
                                القسط الشهري تلقائياً.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 btn-submit">
                                <i class='bx bx-check me-1'></i> اعتماد السلفة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔥 مودال إعادة الجدولة 🔥 --}}
        <div class="modal fade" id="editLoanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-calendar-edit text-warning me-2'></i>
                            إعادة جدولة السلفة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editLoanForm" class="prevent-double-submit">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="calculation-alert">
                                <i class='bx bx-info-circle'></i>
                                <strong>تنبيه:</strong> النظام سيقوم بإعادة حساب القسط الشهري بناءً على المبلغ المتبقي فقط.
                            </div>

                            {{-- عدد الأشهر --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-time text-primary me-1'></i> عدد الأشهر الجديدة
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="months_to_pay" id="edit_months_to_pay"
                                    class="form-control text-center font-monospace fw-bold" required min="1"
                                    max="60">
                                <small class="text-muted">الحد الأقصى: 60 شهراً</small>
                            </div>

                            {{-- تاريخ الاستئناف --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-calendar-check text-primary me-1'></i> استئناف الخصم من
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="start_deduction_month" id="edit_start_deduction_month"
                                    class="form-control text-center fw-bold" required>
                            </div>

                            {{-- ملاحظات --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-comment-detail text-primary me-1'></i> ملاحظات التعديل
                                </label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="سبب إعادة الجدولة..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 btn-submit">
                                <i class='bx bx-refresh me-1'></i> تأكيد وإعادة الحساب
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔥 مودال السداد اليدوي 🔥 --}}
        <div class="modal fade" id="payLoanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-credit-card text-success me-2'></i>
                            تسجيل دفعة سداد
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="payLoanForm" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            {{-- اسم الموظف --}}
                            <div class="alert alert-info border-0 rounded-3 mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class='bx bx-user-circle fs-4'></i>
                                    <div>
                                        <div class="fw-bold">الموظف</div>
                                        <div id="pay_employee_name" class="fs-6"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- ملخص السلفة --}}
                            <div class="loan-summary-box">
                                <div class="summary-row">
                                    <span class="label">مبلغ السلفة الأصلي:</span>
                                    <span class="value" id="pay_loan_amount">0.000 ر.ع</span>
                                </div>
                                <div class="summary-row">
                                    <span class="label">المبلغ المدفوع مسبقاً:</span>
                                    <span class="value text-success" id="pay_paid_amount">0.000 ر.ع</span>
                                </div>
                                <div class="summary-row">
                                    <span class="label">القسط الشهري:</span>
                                    <span class="value text-primary" id="pay_installment">0.000 ر.ع</span>
                                </div>
                                <div class="summary-row">
                                    <span class="label text-danger">المبلغ المتبقي:</span>
                                    <span class="value text-danger" id="pay_remaining">0.000 ر.ع</span>
                                </div>
                            </div>

                            {{-- المبلغ المدفوع --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-money text-success me-1'></i> مبلغ الدفعة
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.001" name="amount" id="pay_amount"
                                        class="form-control fw-bold text-center font-monospace fs-5" required min="0.001">
                                    <span class="input-group-text fw-bold bg-success text-white">ر.ع</span>
                                </div>
                                <small class="text-muted">الحد الأقصى: <span id="pay_max_amount"
                                        class="fw-bold text-danger">0.000</span> ر.ع</small>
                            </div>

                            {{-- تاريخ الدفعة --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-calendar text-primary me-1'></i> تاريخ الدفعة
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="payment_date" class="form-control text-center fw-bold" required
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            {{-- ملاحظات --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-comment-detail text-primary me-1'></i> ملاحظات
                                </label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="تفاصيل الدفعة..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 btn-submit">
                                <i class='bx bx-check me-1'></i> تسجيل الدفعة
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
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل Select2 للفلاتر
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // تفعيل Select2 داخل مودال الإضافة
            $('#addLoanModal').on('shown.bs.modal', function() {
                $('.select2-init').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#addLoanModal')
                });
            });

            // حماية الفورم من الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري المعالجة...');
            });

            // تمرير البيانات لمودال التعديل
            $('.btn-edit-loan').click(function() {
                let id = $(this).data('id');
                $('#edit_months_to_pay').val($(this).data('months'));
                $('#edit_start_deduction_month').val($(this).data('start'));

                let url = "{{ route('admin.hr.loans.update', ':id') }}".replace(':id', id);
                $('#editLoanForm').attr('action', url);
            });

            // تمرير البيانات لمودال السداد اليدوي
            $('.btn-pay-loan').click(function() {
                let id = $(this).data('id');
                let remaining = parseFloat($(this).data('remaining'));
                let installment = parseFloat($(this).data('installment'));

                $('#pay_employee_name').text($(this).data('employee'));
                $('#pay_loan_amount').text(parseFloat($(this).data('loan-amount')).toFixed(3) + ' ر.ع');
                $('#pay_paid_amount').text(parseFloat($(this).data('paid')).toFixed(3) + ' ر.ع');
                $('#pay_installment').text(installment.toFixed(3) + ' ر.ع');
                $('#pay_remaining').text(remaining.toFixed(3) + ' ر.ع');
                $('#pay_max_amount').text(remaining.toFixed(3));

                // افتراضي: قيمة القسط الشهري
                $('#pay_amount').val(installment.toFixed(3)).attr('max', remaining);

                let url = "{{ route('admin.hr.loans.record_payment', ':id') }}".replace(':id', id);
                $('#payLoanForm').attr('action', url);
            });

            // التحقق من مبلغ السداد
            $('#pay_amount').on('input', function() {
                let max = parseFloat($(this).attr('max'));
                let val = parseFloat($(this).val());
                if (val > max) {
                    $(this).val(max.toFixed(3));
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'المبلغ المدفوع أكبر من المتبقي!',
                        toast: true,
                        position: 'top-end',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });

            // تأكيد الحذف
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تحذير أمني!',
                    html: "هل أنت متأكد من حذف هذه السلفة؟<br><br><small class='text-danger'>إذا كان قد تم سداد أي جزء منها، سيرفض النظام الحذف لحماية القيود المحاسبية.</small>",
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
                    timer: 4000,
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
