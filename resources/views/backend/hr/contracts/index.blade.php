@extends('backend.layouts.master')
@section('title', 'سجل العقود الوظيفية')
@section('title_page', 'الموارد البشرية - إدارة العقود')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

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
            padding: 20px;
            color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-decoration: none;
            display: block;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .stat-card.active-filter {
            border: 3px solid #1e293b !important;
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .bg-gradient-total {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .bg-gradient-active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .stat-card i.bg-icon {
            position: absolute;
            left: -10px;
            bottom: -20px;
            font-size: 110px;
            opacity: 0.15;
            transform: rotate(15deg);
        }

        .stat-label {
            font-size: 13px;
            font-weight: 700;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 900;
            font-family: 'Courier New', monospace;
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
            border-top: 5px solid #3b82f6;
        }

        .table-contracts {
            margin-bottom: 0;
        }

        .table-contracts thead th {
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

        .table-contracts tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
            transition: background-color 0.2s ease;
        }

        .table-contracts tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        /* 🌟 الشارات 🌟 */
        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-valid {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .badge-expiring {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-expired {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .badge-terminated {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .badge-order {
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-original {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .badge-renewal {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
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

        .employee-service {
            background: #eff6ff;
            color: #2563eb;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* 🌟 المبالغ 🌟 */
        .amount-display {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 15px;
            color: #10b981;
        }

        .allowances-display {
            font-size: 11px;
            color: #059669;
            font-weight: 700;
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

        .action-btn-view {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .action-btn-view:hover {
            background: #2563eb;
            color: #fff;
        }

        .action-btn-details {
            background: #f5f3ff;
            color: #7c3aed;
            border-color: #ddd6fe;
        }

        .action-btn-details:hover {
            background: #7c3aed;
            color: #fff;
        }

        .action-btn-renew {
            background: #f0fdf4;
            color: #059669;
            border-color: #a7f3d0;
        }

        .action-btn-renew:hover {
            background: #059669;
            color: #fff;
        }

        .action-btn-terminate {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .action-btn-terminate:hover {
            background: #dc2626;
            color: #fff;
        }

        .action-btn-download {
            background: #fef3c7;
            color: #d97706;
            border-color: #fde68a;
        }

        .action-btn-download:hover {
            background: #d97706;
            color: #fff;
        }

        .action-btn-custody {
            background: #fff7ed;
            color: #ea580c;
            border-color: #fed7aa;
        }

        .action-btn-custody:hover {
            background: #ea580c;
            color: #fff;
        }

        /* 🌟 أزرار العمليات 🌟 */
        .btn-add {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
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

        /* 🌟 عنصر العهد 🌟 */
        .custody-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            transition: all 0.2s ease;
            cursor: pointer;
            background: #fff;
        }

        .custody-item:hover {
            background-color: #f8fafc;
            border-color: #3b82f6;
        }

        .custody-item input[type="checkbox"] {
            transform: scale(1.3);
            margin-left: 10px;
        }

        /* 🌟 مودال التفاصيل 🌟 */
        .detail-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
            font-weight: 800;
            font-size: 16px;
            color: #2563eb;
        }

        .detail-label {
            color: #64748b;
            font-weight: 600;
        }

        .detail-value {
            font-family: 'Courier New', monospace;
            font-weight: 800;
        }

        .allowance-item {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* 🌟 Dark Mode 🌟 */
        body[data-layout-color="dark"] {
            background-color: #2b313a;
        }

        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .filter-card,
        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .employee-name {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .table-contracts thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-contracts tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-contracts tbody tr:hover td {
            background-color: #38414e !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select,
        body[data-layout-color="dark"] .filter-input {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .detail-box {
            background: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .detail-label {
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .detail-value {
            color: #f8fafc;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 العنوان وأزرار العمليات 🌟 --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                    <i class='bx bx-file text-primary fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">سجل العقود الوظيفية</h4>
                    <p class="text-muted mb-0 font-13">إدارة عقود الموظفين وتجديداتها</p>
                </div>
            </div>

            @can('manage_hr_contracts')
                <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addContractModal"
                    id="btnOpenAddModal">
                    <i class='bx bx-plus-circle me-1'></i> توثيق عقد جديد
                </button>
            @endcan
        </div>

        {{-- 🌟 كروت الإحصائيات (تعمل كفلاتر) 🌟 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <a href="{{ route('admin.hr.contracts.index') }}"
                    class="stat-card bg-gradient-total {{ request('filter') == '' ? 'active-filter' : '' }}">
                    <div class="position-relative z-1">
                        <div class="stat-label">إجمالي العقود</div>
                        <div class="stat-value">{{ $totalContracts ?? $activeCount + $expiredCount }}</div>
                    </div>
                    <i class='bx bx-file bg-icon'></i>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('admin.hr.contracts.index', ['filter' => 'active']) }}"
                    class="stat-card bg-gradient-active {{ request('filter') == 'active' ? 'active-filter' : '' }}">
                    <div class="position-relative z-1">
                        <div class="stat-label">عقود سارية</div>
                        <div class="stat-value">{{ $activeCount }}</div>
                    </div>
                    <i class='bx bx-shield-check bg-icon'></i>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('admin.hr.contracts.index', ['filter' => 'expiring_soon']) }}"
                    class="stat-card bg-gradient-warning {{ request('filter') == 'expiring_soon' ? 'active-filter' : '' }}">
                    <div class="position-relative z-1">
                        <div class="stat-label">تنتهي قريباً</div>
                        <div class="stat-value">{{ $expiringSoon }}</div>
                        <small class="opacity-75">خلال 30 يوم</small>
                    </div>
                    <i class='bx bx-time-five bg-icon'></i>
                </a>
            </div>

            <div class="col-md-3">
                <a href="{{ route('admin.hr.contracts.index', ['filter' => 'expired']) }}"
                    class="stat-card bg-gradient-danger {{ request('filter') == 'expired' ? 'active-filter' : '' }}">
                    <div class="position-relative z-1">
                        <div class="stat-label">منتهية / مفسوخة</div>
                        <div class="stat-value">{{ $expiredCount }}</div>
                    </div>
                    <i class='bx bx-block bg-icon'></i>
                </a>
            </div>
        </div>

        {{-- 🌟 كارت الفلاتر 🌟 --}}
        <div class="filter-card mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.hr.contracts.index') }}" class="row align-items-end g-3">
                    @if (request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif

                    <div class="col-md-4">
                        <label class="filter-label"><i class='bx bx-search me-1'></i> بحث بالاسم أو الكود</label>
                        <input type="text" name="search" class="form-control filter-input" placeholder="ابحث عن موظف..."
                            value="{{ $search ?? '' }}">
                    </div>

                    <div class="col-md-3">
                        <label class="filter-label"><i class='bx bx-buildings me-1'></i> القسم</label>
                        <select name="department_id" class="form-select filter-input">
                            <option value="">-- جميع الأقسام --</option>
                            @foreach ($departments ?? [] as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5 d-flex gap-2">
                        <button type="submit" class="btn btn-dark flex-grow-1 fw-bold py-2 shadow-sm rounded-3">
                            <i class='bx bx-filter-alt me-1'></i> تصفية
                        </button>
                        <a href="{{ route('admin.hr.contracts.index') }}"
                            class="btn btn-light border fw-bold px-3 py-2 shadow-sm rounded-3" title="إعادة ضبط">
                            <i class='bx bx-refresh fs-5'></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 جدول العقود 🌟 --}}
        <div class="card-premium">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                        <i class='bx bx-list-ul text-primary fs-4'></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">سجل العقود الوظيفية</h5>
                        <small class="text-muted">عرض {{ $contracts->total() }} عقد</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-contracts table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th style="width: 10%;">رقم العقد</th>
                                <th class="text-start ps-4" style="width: 18%;">الموظف</th>
                                <th style="width: 10%;">البداية</th>
                                <th style="width: 12%;">الانتهاء</th>
                                <th style="width: 10%;">الراتب</th>
                                <th style="width: 8%;">المرفق</th>
                                <th style="width: 8%;">العهد</th>
                                <th style="width: 12%;">الحالة</th>
                                @can('manage_hr_contracts')
                                    <th style="width: 12%;">الإجراءات</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contracts as $contract)
                                @php
                                    // 🔥 استخدام Accessors من الموديل 🔥
                                    $isFinished = $contract->is_expired || $contract->is_terminated;

                                    // ترتيب العقد
                                    $contractOrder =
                                        \App\Models\HrContract::where('employee_id', $contract->employee_id)
                                            ->where('start_date', '<', $contract->start_date)
                                            ->count() + 1;
                                    $orderText =
                                        $contractOrder == 1 ? 'العقد الأساسي' : 'تجديد #' . ($contractOrder - 1);
                                    $orderClass = $contractOrder == 1 ? 'badge-original' : 'badge-renewal';

                                    // العهد
                                    $pendingCustodies = $contract->employee
                                        ? $contract->employee->custodies->where('status', 'with_employee')->values()
                                        : collect();
                                    $pendingCustodyCount = $pendingCustodies->count();
                                @endphp
                                <tr>
                                    {{-- رقم العقد --}}
                                    <td>
                                        <div class="fw-bold text-dark font-monospace">#{{ $contract->contract_number }}
                                        </div>
                                        <span class="badge-order {{ $orderClass }}">
                                            <i class='bx {{ $contractOrder == 1 ? 'bx-file-blank' : 'bx-refresh' }}'></i>
                                            {{ $orderText }}
                                        </span>
                                    </td>

                                    {{-- الموظف 🔥 محسّن 🔥 --}}
                                    <td class="text-start ps-4">
                                        <div class="employee-name">
                                            <i class='bx bx-user-circle text-primary me-1'></i>
                                            {{ $contract->employee->full_name_ar ?? 'موظف محذوف' }}
                                        </div>
                                        <div class="employee-dept">
                                            <i class='bx bx-buildings me-1'></i>
                                            {{ $contract->employee->department->name_ar ?? 'بدون قسم' }}
                                        </div>
                                        @if ($contract->employee && $contract->employee->join_date)
                                            <div class="employee-service mt-1">
                                                <i class='bx bx-time-five'></i>
                                                {{ $contract->employee->join_date->diffForHumans(null, true) }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- تاريخ البداية --}}
                                    <td>
                                        <span class="font-monospace text-muted">
                                            {{ $contract->start_date->format('Y-m-d') }}
                                        </span>
                                        <small class="d-block text-muted" style="font-size: 10px;">
                                            {{ $contract->duration_text }}
                                        </small>
                                    </td>

                                    {{-- تاريخ الانتهاء 🔥 محسّن 🔥 --}}
                                    <td>
                                        <span
                                            class="font-monospace {{ $contract->is_expired ? 'text-danger fw-bold' : ($contract->is_expiring_soon ? 'text-warning fw-bold' : 'text-muted') }}">
                                            {{ $contract->end_date->format('Y-m-d') }}
                                        </span>
                                        @if ($contract->is_expiring_soon && !$contract->is_expired)
                                            <br>
                                            <small class="badge badge-expiring mt-1" style="font-size: 10px;">
                                                <i class='bx bx-time-five'></i> باقي {{ $contract->days_remaining }} يوم
                                            </small>
                                        @endif
                                    </td>

                                    {{-- الراتب 🔥 مع البدلات 🔥 --}}
                                    <td>
                                        <span class="amount-display">
                                            {{ number_format($contract->total_salary, 3) }}
                                        </span>
                                        <small class="text-muted d-block">ر.ع</small>
                                        @if ($contract->total_allowances_amount > 0)
                                            <span class="allowances-display">
                                                + {{ number_format($contract->total_allowances_amount, 3) }} بدلات
                                            </span>
                                        @endif
                                    </td>

                                    {{-- المرفق --}}
                                    <td>
                                        @if ($contract->attachment)
                                            <a href="{{ asset('storage/' . $contract->attachment) }}" target="_blank"
                                                class="action-btn action-btn-download" title="تحميل المرفق">
                                                <i class='bx bx-download'></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- العهد --}}
                                    <td>
                                        @if ($isFinished)
                                            @if ($pendingCustodyCount > 0)
                                                @can('manage_custody')
                                                    <button type="button" class="action-btn action-btn-custody btn-handover"
                                                        title="إخلاء طرف من العهد" data-bs-toggle="modal"
                                                        data-bs-target="#handoverCustodyModal"
                                                        data-empname="{{ $contract->employee->full_name_ar ?? 'غير معروف' }}"
                                                        data-custodies='@json($pendingCustodies)'>
                                                        <i class='bx bx-share'></i>
                                                    </button>
                                                    <div class="mt-1">
                                                        <small class="badge badge-expired">
                                                            {{ $pendingCustodyCount }} عهدة
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="badge badge-expired">
                                                        {{ $pendingCustodyCount }} عهدة
                                                    </span>
                                                @endcan
                                            @else
                                                <span class="badge badge-valid">
                                                    <i class='bx bx-check-double'></i> تم
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- الحالة 🔥 استخدام Accessors 🔥 --}}
                                    <td>
                                        <span class="badge-status badge-{{ $contract->status_color }}">
                                            <i class='bx {{ $contract->status_icon }}'></i>
                                            {{ $contract->status_text }}
                                        </span>
                                    </td>

                                    {{-- الإجراءات 🔥 محسّن 🔥 --}}
                                    @can('manage_hr_contracts')
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                {{-- عرض التفاصيل --}}
                                                <button type="button" class="action-btn action-btn-details btn-view-details"
                                                    data-contract-id="{{ $contract->id }}" title="تفاصيل العقد">
                                                    <i class='bx bx-show'></i>
                                                </button>

                                                {{-- ملف الموظف --}}
                                                <a href="{{ route('admin.hr.employees.show', $contract->employee_id) }}"
                                                    class="action-btn action-btn-view" title="ملف الموظف">
                                                    <i class='bx bx-user-circle'></i>
                                                </a>

                                                {{-- تجديد العقد --}}
                                                @if ($isFinished)
                                                    <button type="button"
                                                        class="action-btn action-btn-renew btn-renew-contract"
                                                        title="تجديد العقد" data-bs-toggle="modal"
                                                        data-bs-target="#addContractModal"
                                                        data-employee-id="{{ $contract->employee_id }}">
                                                        <i class='bx bx-refresh'></i>
                                                    </button>
                                                @endif

                                                {{-- إنهاء العقد --}}
                                                @if (!$isFinished)
                                                    <form
                                                        action="{{ route('admin.hr.contracts.status', [$contract->id, 'terminated']) }}"
                                                        method="POST" class="d-inline terminate-form">
                                                        @csrf
                                                        <button type="button"
                                                            class="action-btn action-btn-terminate btn-terminate-contract"
                                                            title="إنهاء العقد">
                                                            <i class='bx bx-block'></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->can('manage_hr_contracts') ? 9 : 8 }}"
                                        class="p-5 text-center">
                                        <div class="py-4">
                                            <i class='bx bx-file text-muted opacity-25' style="font-size: 80px;"></i>
                                            <h5 class="text-muted fw-bold mt-3">لا توجد عقود مسجلة</h5>
                                            <p class="text-muted mb-0">اضغط على "توثيق عقد جديد" لبدء الإضافة</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- الترقيم --}}
                @if ($contracts->hasPages())
                    <div class="d-flex justify-content-center p-3 border-top">
                        {{ $contracts->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- 🔥 مودال تفاصيل العقد 🔥 --}}
    <div class="modal fade" id="contractDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                    <h5 class="modal-title fw-bold">
                        <i class='bx bx-file text-primary me-2'></i>
                        تفاصيل العقد - <span id="detailContractNumber"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="detail-box">
                                <h6 class="fw-bold mb-3"><i class='bx bx-info-circle me-1'></i> معلومات العقد</h6>
                                <div class="detail-row">
                                    <span class="detail-label">الموظف:</span>
                                    <span class="detail-value" id="detailEmployee"></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">القسم:</span>
                                    <span class="detail-value" id="detailDepartment"></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">تاريخ البداية:</span>
                                    <span class="detail-value" id="detailStartDate"></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">تاريخ الانتهاء:</span>
                                    <span class="detail-value" id="detailEndDate"></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">مدة العقد:</span>
                                    <span class="detail-value" id="detailDuration"></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">الأيام المتبقية:</span>
                                    <span class="detail-value" id="detailDaysRemaining"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="detail-box">
                                <h6 class="fw-bold mb-3"><i class='bx bx-money me-1'></i> المعلومات المالية</h6>
                                <div class="detail-row">
                                    <span class="detail-label">الراتب الأساسي:</span>
                                    <span class="detail-value text-success" id="detailSalary"></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">إجمالي البدلات:</span>
                                    <span class="detail-value text-success" id="detailAllowances"></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">الراتب الشامل:</span>
                                    <span class="detail-value" id="detailTotalCompensation"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" id="allowancesSection" style="display: none;">
                            <div class="detail-box">
                                <h6 class="fw-bold mb-3"><i class='bx bx-plus-circle me-1'></i> البدلات والعلاوات</h6>
                                <div id="allowancesList"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class='bx bx-x me-1'></i> إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔥 مودال إخلاء طرف من العهد 🔥 --}}
    @can('manage_custody')
        <div class="modal fade" id="handoverCustodyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-shield-check text-danger me-2'></i>
                            إخلاء طرف من العهد - <span id="handoverEmpName"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.custodies.return_multiple') }}" method="POST"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class='bx bx-info-circle me-1'></i>
                                حدد العهد التي قام الموظف بتسليمها:
                            </div>
                            <div id="custodiesCheckboxesContainer"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4 btn-submit">
                                <i class='bx bx-check-double me-1'></i> تأكيد إخلاء الطرف
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- 🔥 مودال إضافة / تجديد عقد 🔥 --}}
    @can('manage_hr_contracts')
        <div class="modal fade" id="addContractModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                        <h5 class="modal-title fw-bold" id="contractModalTitle">
                            <i class='bx bx-file-blank text-primary me-2'></i>
                            توثيق عقد جديد
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.contracts.store') }}" method="POST" enctype="multipart/form-data"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-3">
                                {{-- الموظف --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-user text-primary me-1'></i> الموظف
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="employee_id" id="modal_employee_id" class="form-select select2-init"
                                        required style="width: 100%;">
                                        <option value="" disabled selected>-- اختر الموظف --</option>
                                        @foreach ($employees ?? [] as $emp)
                                            <option value="{{ $emp->id }}">
                                                {{ $emp->full_name_ar }} ({{ $emp->employee_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- رقم العقد --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-hash text-primary me-1'></i> رقم العقد
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="contract_number" class="form-control font-monospace fw-bold"
                                        dir="ltr" value="CONT-{{ time() }}" required maxlength="50">
                                </div>

                                {{-- تاريخ البداية --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-calendar text-primary me-1'></i> تاريخ البداية
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="start_date" class="form-control fw-bold text-center"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>

                                {{-- تاريخ الانتهاء --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-calendar text-primary me-1'></i> تاريخ الانتهاء
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="end_date" class="form-control fw-bold text-center" required>
                                </div>

                                {{-- الراتب --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-money text-success me-1'></i> الراتب الأساسي
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="total_salary"
                                            class="form-control border-success fw-bold text-success fs-5 text-center font-monospace"
                                            step="0.001" min="0" required placeholder="0.000">
                                        <span
                                            class="input-group-text bg-success bg-opacity-10 text-success border-success fw-bold">ر.ع</span>
                                    </div>
                                </div>

                                {{-- المرفق --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-paperclip text-primary me-1'></i> نسخة العقد الموقعة
                                    </label>
                                    <input type="file" name="attachment" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">اختياري - للرجوع إليه وقت الحاجة</small>
                                </div>

                                {{-- ملاحظات 🔥 جديد 🔥 --}}
                                <div class="col-12 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-comment-detail text-primary me-1'></i> ملاحظات
                                    </label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات على العقد..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 btn-submit">
                                <i class='bx bx-check-circle me-1'></i> حفظ واعتماد العقد
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

            // تفعيل Select2 في المودال
            $('#addContractModal').on('shown.bs.modal', function() {
                $('.select2-init').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#addContractModal')
                });
            });

            // حماية الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري الحفظ...');
            });

            // 🔥 عرض تفاصيل العقد 🔥
            $(document).on('click', '.btn-view-details', function() {
                let contractId = $(this).data('contract-id');

                if (!contractId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'رقم العقد غير موجود'
                    });
                    return;
                }

                // عرض مؤشر تحميل
                Swal.fire({
                    title: 'جاري التحميل...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/admin/hr/contracts/${contractId}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.success) {
                            let data = response.data;

                            $('#detailContractNumber').text(data.contract_number);
                            $('#detailEmployee').text(data.employee_name);
                            $('#detailDepartment').text(data.department);
                            $('#detailStartDate').text(data.start_date);
                            $('#detailEndDate').text(data.end_date);
                            $('#detailDuration').text(data.duration_text);
                            $('#detailDaysRemaining').text(data.days_remaining + ' يوم');
                            $('#detailSalary').text(data.total_salary + ' ر.ع');
                            $('#detailAllowances').text(data.total_allowances + ' ر.ع');
                            $('#detailTotalCompensation').text(data.total_compensation +
                            ' ر.ع');

                            // عرض البدلات
                            if (data.allowances && data.allowances.length > 0) {
                                let allowancesHtml = '';
                                data.allowances.forEach(function(allowance) {
                                    allowancesHtml += `
                            <div class="allowance-item">
                                <span class="fw-bold">${allowance.name}</span>
                                <span class="text-success fw-bold">${allowance.amount} ر.ع</span>
                            </div>
                        `;
                                });
                                $('#allowancesList').html(allowancesHtml);
                                $('#allowancesSection').show();
                            } else {
                                $('#allowancesList').html(
                                    '<p class="text-muted text-center">لا توجد بدلات</p>');
                                $('#allowancesSection').show();
                            }

                            $('#contractDetailsModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: response.message || 'فشل في تحميل البيانات'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        console.error('Error:', error);
                        console.error('Response:', xhr.responseText);

                        let errorMessage = 'فشل في تحميل تفاصيل العقد';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 403) {
                            errorMessage = 'غير مصرح لك بعرض هذه البيانات';
                        } else if (xhr.status === 404) {
                            errorMessage = 'العقد غير موجود';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: errorMessage
                        });
                    }
                });
            });

            // تأكيد إنهاء العقد
            $('.btn-terminate-contract').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد إنهاء العقد؟',
                    html: 'سيتم:<br>• إنهاء العقد فوراً<br>• إيقاف حساب الموظف<br>• طلب إخلاء الطرف من العهد<br><br><small class="text-danger">لا يمكن التراجع عن هذه الخطوة!</small>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، قم بالإنهاء',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // مودال العهد
            $('.btn-handover').click(function() {
                let empName = $(this).data('empname');
                let custodies = $(this).data('custodies');
                let container = $('#custodiesCheckboxesContainer');

                $('#handoverEmpName').text(empName);
                container.empty();

                if (custodies && custodies.length > 0) {
                    custodies.forEach(function(custody) {
                        let html = `
                            <label class="custody-item d-block">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" name="custody_ids[]" value="${custody.id}">
                                    <div class="ms-3">
                                        <h6 class="mb-0 fw-bold text-dark">${custody.item_name}</h6>
                                        <small class="text-muted">
                                            <i class='bx bx-calendar'></i> 
                                            استلمت في: ${custody.receive_date}
                                        </small>
                                    </div>
                                </div>
                            </label>
                        `;
                        container.append(html);
                    });
                } else {
                    container.append(
                        `<div class="alert alert-success text-center fw-bold">
                            <i class='bx bx-check-circle fs-4 align-middle'></i>
                            لا توجد عهد معلقة
                        </div>`
                    );
                }
            });

            // زر تجديد العقد
            $(document).on('click', '.btn-renew-contract', function() {
                let empId = $(this).data('employee-id');
                $('#modal_employee_id').val(empId).trigger('change.select2');
                $('#contractModalTitle').html(
                    "<i class='bx bx-refresh text-success me-2'></i> تجديد العقد الوظيفي"
                );
            });

            // زر عقد جديد
            $(document).on('click', '#btnOpenAddModal', function() {
                $('#modal_employee_id').val("").trigger('change.select2');
                $('#contractModalTitle').html(
                    "<i class='bx bx-file-blank text-primary me-2'></i> توثيق عقد جديد"
                );
            });

            // 🌟 رسائل النظام 🌟
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

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            @endif

            @if ($errors->any())
                let errorHtml =
                    '<ul class="text-start text-danger fw-bold mb-0" style="list-style: none; padding:0;">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li><i class="bx bx-error-circle me-1"></i> {{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    html: errorHtml,
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>
@endpush
