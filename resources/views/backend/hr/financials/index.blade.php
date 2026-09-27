@extends('backend.layouts.master')
@section('title', 'العلاوات والخصومات')
@section('title_page', 'المالية والرواتب - إدارة العلاوات والخصومات')

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

        .stat-info {
            background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);
            border-left: 5px solid #06b6d4;
        }

        .stat-info::before {
            background: #0891b2;
        }

        .stat-info .icon-box {
            color: #06b6d4;
            background: #a5f3fc;
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

        .table-financial {
            margin-bottom: 0;
        }

        .table-financial thead th {
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

        .table-financial tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
            transition: background-color 0.2s ease;
        }

        .table-financial tbody tr:hover td {
            background-color: #fbfccb !important;
        }

        .table-financial tbody tr.row-processed {
            background-color: #f0fdf4;
        }

        .table-financial tbody tr.row-processed:hover {
            background-color: #dcfce7 !important;
        }

        /* 🌟 شارات النوع 🌟 */
        .badge-type {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-allowance {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .badge-deduction {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
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

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-processed {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        /* 🌟 المبلغ 🌟 */
        .amount-display {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 16px;
        }

        .amount-allowance {
            color: #10b981;
        }

        .amount-deduction {
            color: #ef4444;
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
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

        body[data-layout-color="dark"] .stat-success {
            background: #064e3b;
            border-color: #10b981;
        }

        body[data-layout-color="dark"] .stat-danger {
            background: #7f1d1d;
            border-color: #ef4444;
        }

        body[data-layout-color="dark"] .stat-warning {
            background: #451a03;
            border-color: #f59e0b;
        }

        body[data-layout-color="dark"] .stat-info {
            background: #164e63;
            border-color: #06b6d4;
        }

        body[data-layout-color="dark"] .stat-value,
        body[data-layout-color="dark"] .employee-name {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .table-financial thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-financial tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-financial tbody tr:hover td {
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
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 العنوان وزر الإضافة 🌟 --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                    <i class='bx bx-transfer text-primary fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">الحركات المالية للموظفين</h4>
                    <p class="text-muted mb-0 font-13">إدارة العلاوات والخصومات الشهرية للموظفين</p>
                </div>
            </div>

            @can('manage_payroll_financials')
                <button class="btn btn-add shadow-sm" data-bs-toggle="modal" data-bs-target="#addFinancialModal"
                    id="btnOpenAddModal">
                    <i class='bx bx-plus-circle me-1 fs-5'></i> تسجيل حركة جديدة
                </button>
            @endcan
        </div>

        {{-- 🌟 كروت الإحصائيات 🌟 --}}
        <div class="row g-4 mb-4">
            {{-- إجمالي العلاوات --}}
            <div class="col-md-3">
                <div class="stat-card stat-success p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي العلاوات</div>
                            <div class="stat-value text-success">+{{ number_format($totalAllowances, 3) }} <small
                                    class="fs-6 opacity-75">ر.ع</small></div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-trending-up'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- إجمالي الخصومات --}}
            <div class="col-md-3">
                <div class="stat-card stat-danger p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي الخصومات</div>
                            <div class="stat-value text-danger">-{{ number_format($totalDeductions, 3) }} <small
                                    class="fs-6 opacity-75">ر.ع</small></div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-trending-down'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الحركات المعلقة --}}
            <div class="col-md-3">
                <div class="stat-card stat-warning p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">حركات معلقة</div>
                            <div class="stat-value text-warning">{{ $pendingCount ?? 0 }}</div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-time-five'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الحركات المُعالجة --}}
            <div class="col-md-3">
                <div class="stat-card stat-info p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">حركات مُعالجة</div>
                            <div class="stat-value text-info">{{ $processedCount ?? 0 }}</div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-check-circle'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 كارت الفلاتر 🌟 --}}
        <div class="filter-card mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.hr.financials.index') }}" class="row align-items-end g-3">
                    {{-- الشهر --}}
                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-calendar me-1'></i> الشهر</label>
                        <select name="month" class="form-select filter-input">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>
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
                            value="{{ $year }}">
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

                    {{-- النوع --}}
                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-category me-1'></i> النوع</label>
                        <select name="type" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            <option value="allowance" {{ ($type ?? '') == 'allowance' ? 'selected' : '' }}>علاوات</option>
                            <option value="deduction" {{ ($type ?? '') == 'deduction' ? 'selected' : '' }}>خصومات</option>
                        </select>
                    </div>

                    {{-- الحالة --}}
                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-flag me-1'></i> الحالة</label>
                        <select name="status" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>معلقة</option>
                            <option value="processed" {{ ($status ?? '') == 'processed' ? 'selected' : '' }}>مُعالجة
                            </option>
                        </select>
                    </div>

                    {{-- أزرار الفلترة --}}
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-dark w-100 fw-bold py-2 shadow-sm rounded-3">
                            <i class='bx bx-filter-alt'></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 جدول الحركات 🌟 --}}
        <div class="card-premium">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                        <i class='bx bx-list-ul text-warning fs-4'></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">سجل الحركات المالية</h5>
                        <small class="text-muted">شهر {{ $month }} / {{ $year }}</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-financial table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="text-start ps-4" style="width: 22%;">الموظف</th>
                                <th style="width: 15%;">نوع الحركة</th>
                                <th style="width: 12%;">المبلغ</th>
                                <th style="width: 12%;">التاريخ</th>
                                <th style="width: 10%;">الحالة</th>
                                <th style="width: 20%;">الملاحظات</th>
                                @can('manage_payroll_financials')
                                    <th style="width: 9%;">الإجراءات</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trans)
                                @php
                                    $rowClass = ($trans->status ?? 'pending') === 'processed' ? 'row-processed' : '';
                                    $isAllowance = $trans->financialType && $trans->financialType->type == 'allowance';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    {{-- الموظف --}}
                                    <td class="text-start ps-4">
                                        <div class="employee-name">
                                            <i class='bx bx-user-circle text-primary me-1'></i>
                                            {{ $trans->employee->full_name_ar ?? 'موظف محذوف' }}
                                        </div>
                                        <div class="employee-dept">
                                            <i class='bx bx-building me-1'></i>
                                            {{ $trans->employee->department->name_ar ?? 'بدون قسم' }}
                                        </div>
                                    </td>

                                    {{-- نوع الحركة --}}
                                    <td>
                                        <div class="fw-bold text-dark mb-1">
                                            {{ $trans->financialType->name_ar ?? 'غير محدد' }}
                                        </div>
                                        @if ($isAllowance)
                                            <span class="badge-type badge-allowance">
                                                <i class='bx bx-arrow-up'></i> علاوة
                                            </span>
                                        @else
                                            <span class="badge-type badge-deduction">
                                                <i class='bx bx-arrow-down'></i> خصم
                                            </span>
                                        @endif
                                    </td>

                                    {{-- المبلغ --}}
                                    <td>
                                        <span
                                            class="amount-display {{ $isAllowance ? 'amount-allowance' : 'amount-deduction' }}">
                                            {{ $isAllowance ? '+' : '-' }}{{ number_format($trans->amount, 3) }}
                                        </span>
                                        <small class="text-muted d-block">ر.ع</small>
                                    </td>

                                    {{-- التاريخ --}}
                                    <td>
                                        <span class="font-monospace fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($trans->action_date)->format('Y-m-d') }}
                                        </span>
                                    </td>

                                    {{-- الحالة --}}
                                    <td>
                                        @if (($trans->status ?? 'pending') === 'processed')
                                            <span class="badge-status badge-processed">
                                                <i class='bx bx-check-circle'></i> مُعالجة
                                            </span>
                                        @else
                                            <span class="badge-status badge-pending">
                                                <i class='bx bx-time-five'></i> معلقة
                                            </span>
                                        @endif
                                    </td>

                                    {{-- الملاحظات --}}
                                    <td>
                                        <span class="text-muted text-wrap"
                                            style="max-width: 200px; display: inline-block;">
                                            {{ $trans->notes ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- الإجراءات --}}
                                    @can('manage_payroll_financials')
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                @if (($trans->status ?? 'pending') === 'pending')
                                                    {{-- زر التعديل --}}
                                                    <button type="button"
                                                        class="action-btn action-btn-edit btn-edit-financial"
                                                        data-id="{{ $trans->id }}"
                                                        data-employee="{{ $trans->employee_id }}"
                                                        data-type="{{ $trans->financial_type_id }}"
                                                        data-amount="{{ $trans->amount }}"
                                                        data-date="{{ \Carbon\Carbon::parse($trans->action_date)->format('Y-m-d') }}"
                                                        data-notes="{{ $trans->notes }}" data-bs-toggle="modal"
                                                        data-bs-target="#editFinancialModal" title="تعديل">
                                                        <i class='bx bx-edit'></i>
                                                    </button>

                                                    {{-- زر الحذف --}}
                                                    <form action="{{ route('admin.hr.financials.destroy', $trans->id) }}"
                                                        method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="action-btn action-btn-delete btn-delete"
                                                            title="حذف">
                                                            <i class='bx bx-trash'></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    {{-- أزرار معطلة للحركات المُعالجة --}}
                                                    <span class="action-btn action-btn-disabled"
                                                        title="لا يمكن التعديل - مُعالجة في مسير رواتب">
                                                        <i class='bx bx-lock-alt'></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('manage_payroll_financials') ? 7 : 6 }}"
                                        class="p-5 text-center">
                                        <div class="py-4">
                                            <i class='bx bx-folder-open text-muted opacity-25'
                                                style="font-size: 80px;"></i>
                                            <h5 class="text-muted fw-bold mt-3">لا توجد حركات مالية مسجلة في هذا الشهر</h5>
                                            <p class="text-muted mb-0">قم بتسجيل حركة جديدة أو تغيير فترة الفلترة</p>
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

    {{-- 🔥 مودال إضافة حركة مالية 🔥 --}}
    @can('manage_payroll_financials')
        <div class="modal fade" id="addFinancialModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-plus-circle text-success me-2'></i>
                            تسجيل حركة مالية جديدة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.financials.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            {{-- الموظف --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-user text-primary me-1'></i> الموظف
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="employee_id" id="modal_employee_id" class="form-select select2-add" required
                                    style="width: 100%;">
                                    <option value="" disabled selected>-- اختر الموظف --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->full_name_ar }} ({{ $emp->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- نوع الحركة --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-category text-primary me-1'></i> نوع الحركة
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="financial_type_id" class="form-select select2-add" required
                                    style="width: 100%;">
                                    <option value="" disabled selected>-- اختر النوع --</option>
                                    @foreach ($financialTypes as $type)
                                        <option value="{{ $type->id }}" data-type="{{ $type->type }}">
                                            {{ $type->type == 'allowance' ? '🟢' : '🔴' }} {{ $type->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- المبلغ والتاريخ --}}
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-money text-primary me-1'></i> المبلغ
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.001" name="amount"
                                            class="form-control fw-bold text-center font-monospace" required min="0.001">
                                        <span class="input-group-text fw-bold">ر.ع</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-calendar text-primary me-1'></i> تاريخ الإجراء
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="action_date" class="form-control fw-bold text-center"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            {{-- الملاحظات --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-comment-detail text-primary me-1'></i> البيان / الملاحظات
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="اكتب تفاصيل أو أسباب الحركة..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 btn-submit">
                                <i class='bx bx-check me-1'></i> حفظ الحركة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔥 مودال تعديل حركة مالية 🔥 --}}
        <div class="modal fade" id="editFinancialModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-edit text-primary me-2'></i>
                            تعديل حركة مالية
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editFinancialForm" class="prevent-double-submit">
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

                            {{-- نوع الحركة --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-category text-primary me-1'></i> نوع الحركة
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="financial_type_id" id="edit_financial_type_id" class="form-select select2-edit"
                                    required style="width: 100%;">
                                    @foreach ($financialTypes as $type)
                                        <option value="{{ $type->id }}">
                                            {{ $type->type == 'allowance' ? '🟢' : '🔴' }} {{ $type->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- المبلغ والتاريخ --}}
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-money text-primary me-1'></i> المبلغ
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.001" name="amount" id="edit_amount"
                                            class="form-control fw-bold text-center font-monospace" required min="0.001">
                                        <span class="input-group-text fw-bold">ر.ع</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-calendar text-primary me-1'></i> تاريخ الإجراء
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="action_date" id="edit_action_date"
                                        class="form-control fw-bold text-center" required>
                                </div>
                            </div>

                            {{-- الملاحظات --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-comment-detail text-primary me-1'></i> البيان / الملاحظات
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea name="notes" id="edit_notes" class="form-control" rows="2" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 btn-submit">
                                <i class='bx bx-check me-1'></i> تحديث البيانات
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
            $('#addFinancialModal').on('shown.bs.modal', function() {
                $('.select2-add').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#addFinancialModal')
                });
            });

            // تفعيل Select2 داخل مودال التعديل
            $('#editFinancialModal').on('shown.bs.modal', function() {
                $('.select2-edit').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#editFinancialModal')
                });
            });

            // منع الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري المعالجة...');
            });

            // تمرير البيانات لمودال التعديل
            $('.btn-edit-financial').click(function() {
                let id = $(this).data('id');

                $('#edit_employee_id').val($(this).data('employee')).trigger('change');
                $('#edit_financial_type_id').val($(this).data('type')).trigger('change');
                $('#edit_amount').val($(this).data('amount'));
                $('#edit_action_date').val($(this).data('date'));
                $('#edit_notes').val($(this).data('notes'));

                let url = "{{ route('admin.hr.financials.update', ':id') }}".replace(':id', id);
                $('#editFinancialForm').attr('action', url);
            });

            // إعادة تعيين حقل الموظف في مودال الإضافة
            $('#btnOpenAddModal').click(function() {
                $('#modal_employee_id').val("").trigger('change');
            });

            // تأكيد الحذف
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "سيتم إلغاء هذه الحركة المالية ولن يتم احتسابها في مسير رواتب الموظف!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، قم بالحذف',
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
                    title: 'نجاح',
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
                    title: 'تنبيه',
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
                    title: 'خطأ في البيانات!',
                    html: errorHtml,
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>
@endpush
