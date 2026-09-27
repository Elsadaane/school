@extends('backend.layouts.master')
@section('title', 'تصفية ونهاية الخدمة')
@section('title_page', 'الموارد البشرية - مخالصات نهاية الخدمة')

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
            border-top: 5px solid #dc2626;
        }

        .table-eos {
            margin-bottom: 0;
        }

        .table-eos thead th {
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

        .table-eos tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
            transition: background-color 0.2s ease;
        }

        .table-eos tbody tr:hover td {
            background-color: #fef2f2 !important;
        }

        /* 🌟 شارات السبب 🌟 */
        .badge-reason {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-resignation {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-termination {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .badge-contract_end {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .badge-retirement {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .badge-other {
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

        .amount-positive {
            color: #10b981;
        }

        .amount-negative {
            color: #ef4444;
        }

        .amount-net {
            color: #2563eb;
            font-size: 17px;
        }

        /* 🌟 معلومات الموظف 🌟 */
        .employee-name {
            font-weight: 800;
            color: #1e293b;
            font-size: 14px;
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

        .action-btn-print {
            background: #f8fafc;
            color: #475569;
            border-color: #e2e8f0;
        }

        .action-btn-print:hover {
            background: #475569;
            color: #fff;
        }

        .action-btn-reverse {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .action-btn-reverse:hover {
            background: #dc2626;
            color: #fff;
        }

        /* 🌟 أزرار العمليات 🌟 */
        .btn-add {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
            color: #fff;
        }

        /* 🌟 صندوق الحساب 🌟 */
        .calculation-box {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 2px solid #3b82f6;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            margin-top: 20px;
        }

        .calculation-box .net-amount {
            font-size: 36px;
            font-weight: 900;
            color: #1e40af;
            font-family: 'Courier New', monospace;
        }

        /* 🌟 لوحة التوقيع 🌟 */
        .signature-pad-wrapper {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background-color: #fff;
            padding: 10px;
            text-align: center;
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
        body[data-layout-color="dark"] .filter-card,
        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .stat-primary {
            background: #1e293b;
            border-color: #3b82f6;
        }

        body[data-layout-color="dark"] .stat-success {
            background: #064e3b;
            border-color: #10b981;
        }

        body[data-layout-color="dark"] .stat-warning {
            background: #451a03;
            border-color: #f59e0b;
        }

        body[data-layout-color="dark"] .stat-danger {
            background: #7f1d1d;
            border-color: #ef4444;
        }

        body[data-layout-color="dark"] .stat-value,
        body[data-layout-color="dark"] .employee-name {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .table-eos thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-eos tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-eos tbody tr:hover td {
            background-color: #38414e !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select,
        body[data-layout-color="dark"] .filter-input {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 العنوان وأزرار العمليات 🌟 --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                    <i class='bx bx-door-open text-danger fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">مخلصات نهاية الخدمة</h4>
                    <p class="text-muted mb-0 font-13">إدارة تصفية حسابات الموظفين المنتهية خدمتهم</p>
                </div>
            </div>

            @can('manage_eos')
                <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addEosModal">
                    <i class='bx bx-calculator me-1'></i> إجراء تصفية جديدة
                </button>
            @endcan
        </div>

        {{-- 🌟 كروت الإحصائيات 🌟 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-primary p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي المخالصات</div>
                            <div class="stat-value text-primary">{{ $totalEOS ?? $eos_records->count() }}</div>
                            <small class="text-muted">عملية تصفية</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-file'></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-danger p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي المصروف</div>
                            <div class="stat-value text-danger">
                                {{ number_format($totalPayout ?? $eos_records->sum('net_payout'), 3) }}</div>
                            <small class="text-muted">ر.ع</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-money'></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-warning p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">هذا الشهر</div>
                            <div class="stat-value text-warning">{{ $thisMonth ?? 0 }}</div>
                            <small class="text-muted">عملية تصفية</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-calendar'></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-success p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">متوسط التصفية</div>
                            <div class="stat-value text-success">
                                @php
                                    $avg = $eos_records->count() > 0 ? $eos_records->avg('net_payout') : 0;
                                @endphp
                                {{ number_format($avg, 3) }}
                            </div>
                            <small class="text-muted">ر.ع</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-trending-up'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 جدول المخالصات 🌟 --}}
        <div class="card-premium">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-10 p-2 rounded-circle">
                        <i class='bx bx-list-ul text-danger fs-4'></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">سجل المخالصات</h5>
                        <small class="text-muted">عرض {{ $eos_records->count() }} مخالصة</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-eos table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="text-start ps-4" style="width: 18%;">الموظف</th>
                                <th style="width: 10%;">تاريخ المغادرة</th>
                                <th style="width: 12%;">السبب</th>
                                <th style="width: 10%;">مدة الخدمة</th>
                                <th style="width: 10%;">مكافأة الخدمة</th>
                                <th style="width: 10%;">بدل الإجازات</th>
                                <th style="width: 10%;">خصم السلف</th>
                                <th style="width: 12%;">الصافي</th>
                                <th style="width: 8%;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($eos_records as $eos)
                                <tr>
                                    {{-- الموظف --}}
                                    <td class="text-start ps-4">
                                        <div class="employee-name">
                                            <i class='bx bx-user-circle text-primary me-1'></i>
                                            {{ $eos->employee->full_name_ar ?? 'موظف محذوف' }}
                                        </div>
                                        @if ($eos->employee && $eos->employee->department)
                                            <div class="employee-dept">
                                                <i class='bx bx-building me-1'></i>
                                                {{ $eos->employee->department->name_ar }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- تاريخ المغادرة --}}
                                    <td>
                                        <span class="font-monospace fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($eos->termination_date)->format('Y-m-d') }}
                                        </span>
                                    </td>

                                    {{-- السبب --}}
                                    <td>
                                        <span class="badge-reason badge-{{ $eos->reason }}">
                                            <i class='bx {{ $eos->reason_icon }}'></i>
                                            {{ $eos->reason_text }}
                                        </span>
                                    </td>

                                    {{-- مدة الخدمة --}}
                                    <td>
                                        <span class="text-muted small">
                                            {{ $eos->service_years_text ?? 'غير محدد' }}
                                        </span>
                                    </td>

                                    {{-- مكافأة الخدمة --}}
                                    <td>
                                        <span class="amount-display amount-positive">
                                            +{{ number_format($eos->gratuity_amount, 3) }}
                                        </span>
                                    </td>

                                    {{-- بدل الإجازات --}}
                                    <td>
                                        <span class="amount-display amount-positive">
                                            +{{ number_format($eos->leave_encashment, 3) }}
                                        </span>
                                    </td>

                                    {{-- خصم السلف --}}
                                    <td>
                                        <span class="amount-display amount-negative">
                                            -{{ number_format($eos->loan_deductions, 3) }}
                                        </span>
                                    </td>

                                    {{-- الصافي --}}
                                    <td>
                                        <span class="amount-display amount-net">
                                            {{ number_format($eos->net_payout, 3) }}
                                        </span>
                                        <small class="text-muted d-block">ر.ع</small>
                                    </td>

                                    {{-- الإجراءات --}}
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            {{-- زر الطباعة --}}
                                            <a href="{{ route('admin.hr.eos.print', $eos->id) }}" target="_blank"
                                                class="action-btn action-btn-print" title="طباعة المخالصة">
                                                <i class='bx bx-printer'></i>
                                            </a>

                                            {{-- زر إلغاء المخالصة --}}
                                            @can('manage_eos')
                                                <form action="{{ route('admin.hr.eos.reverse', $eos->id) }}" method="POST"
                                                    class="d-inline reverse-form">
                                                    @csrf
                                                    <button type="button"
                                                        class="action-btn action-btn-reverse btn-reverse-eos"
                                                        title="إلغاء المخالصة">
                                                        <i class='bx bx-undo'></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-5 text-center">
                                        <div class="py-4">
                                            <i class='bx bx-file text-muted opacity-25' style="font-size: 80px;"></i>
                                            <h5 class="text-muted fw-bold mt-3">لا توجد مخالصات مسجلة</h5>
                                            <p class="text-muted mb-0">اضغط على "إجراء تصفية جديدة" لبدء العملية</p>
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

    {{-- 🔥 مودال إجراء تصفية جديدة 🔥 --}}
    @can('manage_eos')
        <div class="modal fade" id="addEosModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-calculator text-danger me-2'></i>
                            حاسبة تصفية نهاية الخدمة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.eos.store') }}" method="POST" id="eosForm"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-3">
                                {{-- اختيار الموظف --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-user text-primary me-1'></i> الموظف
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="employee_id" id="employeeSelect" class="form-select select2-init" required
                                        onchange="calculateDues(this.value)" style="width: 100%;">
                                        <option value="" disabled selected>-- اختر الموظف --</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}">
                                                {{ $emp->full_name_ar }} ({{ $emp->employee_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- تاريخ آخر يوم عمل --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-calendar text-primary me-1'></i> تاريخ آخر يوم عمل
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="termination_date" class="form-control text-center fw-bold"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>

                                {{-- سبب المغادرة --}}
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-help-circle text-primary me-1'></i> سبب المغادرة
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="reason" class="form-select fw-bold" required>
                                        <option value="resignation">📤 استقالة</option>
                                        <option value="termination">🚫 إنهاء عقد من طرف الشركة</option>
                                        <option value="contract_end">📄 انتهاء مدة العقد</option>
                                        <option value="retirement">👴 تقاعد</option>
                                        <option value="other">📝 أخرى</option>
                                    </select>
                                </div>
                            </div>

                            {{-- قسم الحسابات التلقائية --}}
                            <div class="row g-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-success">
                                        <i class='bx bx-plus-circle me-1'></i> مكافأة الخدمة
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.001" name="gratuity_amount" id="gratuity"
                                            class="form-control fw-bold text-success text-center font-monospace" readonly>
                                        <span class="input-group-text bg-success text-white fw-bold">ر.ع</span>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-success">
                                        <i class='bx bx-plus-circle me-1'></i> بدل الإجازات
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.001" name="leave_encashment" id="leaves"
                                            class="form-control fw-bold text-success text-center font-monospace" readonly>
                                        <span class="input-group-text bg-success text-white fw-bold">ر.ع</span>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold text-danger">
                                        <i class='bx bx-minus-circle me-1'></i> خصم السلف
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.001" name="loan_deductions" id="loans"
                                            class="form-control fw-bold text-danger text-center font-monospace" readonly>
                                        <span class="input-group-text bg-danger text-white fw-bold">ر.ع</span>
                                    </div>
                                </div>
                            </div>

                            {{-- صندوق الصافي --}}
                            <div class="calculation-box">
                                <h6 class="text-muted fw-bold mb-2">
                                    <i class='bx bx-wallet me-1'></i> الصافي النهائي المستحق
                                </h6>
                                <input type="hidden" name="net_payout" id="netInput">
                                <div class="net-amount font-monospace" id="netDisplay" dir="ltr">0.000 ر.ع</div>
                                <small class="text-muted fw-bold mt-2 d-block" id="yearsDisplay">
                                    <i class='bx bx-time me-1'></i> مدة الخدمة: لم يتم التحديد
                                </small>
                            </div>

                            {{-- اختيار حساب الصرف --}}
                            <div class="mt-4">
                                <label class="form-label">
                                    <i class='bx bx-wallet text-primary me-1'></i> حساب الصرف
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="financial_account_id" class="form-select fw-bold" required>
                                    @if ($paymentMethods->isEmpty())
                                        <option value="" disabled selected>يرجى إضافة حسابات من قسم الحسابات المالية
                                        </option>
                                    @else
                                        <option value="" disabled selected>-- اختر حساب الصرف --</option>
                                        @foreach ($paymentMethods as $pm)
                                            <option value="{{ $pm->id }}">{{ $pm->name_ar }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            {{-- ملاحظات --}}
                            <div class="mt-3">
                                <label class="form-label">
                                    <i class='bx bx-comment-detail text-primary me-1'></i> ملاحظات
                                </label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات إضافية عن التصفية..."></textarea>
                            </div>

                            {{-- التوقيع الإلكتروني --}}
                            <div class="mt-4 text-center">
                                <label class="form-label fw-bold">
                                    <i class='bx bx-pen text-primary me-1'></i> توقيع الموظف المستلم
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="signature-pad-wrapper mx-auto" style="max-width: 500px;">
                                    <canvas id="signature-pad" class="w-100" height="150"
                                        style="touch-action: none; cursor: crosshair; border-radius: 8px;"></canvas>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2 rounded-pill px-3"
                                    id="clear-signature">
                                    <i class='bx bx-eraser me-1'></i> مسح التوقيع
                                </button>
                                <input type="hidden" name="signature_base64" id="signature_base64">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4 btn-submit" id="submitEosBtn">
                                <i class='bx bx-check me-1'></i> اعتماد المخالصة
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
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل Select2 في المودال
            $('#addEosModal').on('shown.bs.modal', function() {
                $('.select2-init').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#addEosModal')
                });
            });

            // 🌟 لوحة التوقيع 🌟
            var canvas = document.getElementById('signature-pad');
            var signaturePad;

            function initSignaturePad() {
                if (canvas) {
                    var ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);

                    if (signaturePad) {
                        signaturePad.clear();
                    } else {
                        signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgb(255, 255, 255)',
                            penColor: 'rgb(0, 0, 128)'
                        });
                    }
                }
            }

            $('#addEosModal').on('shown.bs.modal', function() {
                initSignaturePad();
            });

            $('#clear-signature').click(function() {
                if (signaturePad) signaturePad.clear();
            });

            // 🌟 حساب المستحقات (AJAX) 🌟
            window.calculateDues = function(employeeId) {
                if (!employeeId) return;

                let netDisplay = document.getElementById('netDisplay');
                netDisplay.innerHTML = "<i class='bx bx-loader bx-spin'></i> جاري الحساب...";

                let url = "{{ route('admin.hr.eos.calculate', ':id') }}".replace(':id', employeeId);

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('gratuity').value = parseFloat(data.data.gratuity)
                                .toFixed(3);
                            document.getElementById('leaves').value = parseFloat(data.data.leave_encashment)
                                .toFixed(3);
                            document.getElementById('loans').value = parseFloat(data.data.loans).toFixed(3);
                            document.getElementById('netInput').value = parseFloat(data.data.net_payout)
                                .toFixed(3);

                            netDisplay.innerHTML = parseFloat(data.data.net_payout).toFixed(3) + " ر.ع";
                            document.getElementById('yearsDisplay').innerHTML =
                                "<i class='bx bx-time me-1'></i> مدة الخدمة: " + data.data
                                .years_of_service + " سنة";
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        netDisplay.innerHTML = "<span class='text-danger'>حدث خطأ في الحساب</span>";
                    });
            }

            // 🌟 تأكيد الإرسال 🌟
            $('.prevent-double-submit').on('submit', function(e) {
                e.preventDefault();
                let form = this;
                let submitBtn = $(this).find('.btn-submit');

                if (signaturePad && !signaturePad.isEmpty()) {
                    $('#signature_base64').val(signaturePad.toDataURL('image/png'));

                    Swal.fire({
                        title: 'تأكيد الاعتماد؟',
                        html: "سيتم:<br>• إغلاق ملف الموظف نهائياً<br>• صرف المستحقات من الخزينة<br>• تقفيل جميع السلف<br>• إنهاء العقد",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'نعم، اعتمد التصفية',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitBtn.prop('disabled', true).html(
                                '<i class="bx bx-loader bx-spin me-1"></i> جاري الاعتماد...');
                            form.submit();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى توقيع الموظف على المخالصة أولاً',
                        confirmButtonColor: '#f59e0b'
                    });
                }
            });

            // 🌟 تأكيد إلغاء المخالصة 🌟
            $('.btn-reverse-eos').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد إلغاء المخالصة؟',
                    html: "سيتم:<br>• إعادة المبلغ للخزينة<br>• إعادة الموظف للعمل<br>• إعادة تفعيل السلف<br><br><small class='text-danger'>هذه العملية لا يمكن التراجع عنها!</small>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، إلغاء المخالصة',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
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
