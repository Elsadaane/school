@extends('backend.layouts.master')
@section('title', 'مسير الرواتب')
@section('title_page', 'الموارد البشرية - مسير الرواتب الشهري')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
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
            border-top: 5px solid #004b87;
        }

        .table-payroll {
            margin-bottom: 0;
        }

        .table-payroll thead th {
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

        .table-payroll tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
            transition: background-color 0.2s ease;
        }

        .table-payroll tbody tr:hover td {
            background-color: #fbfccb !important;
        }

        .table-payroll tbody tr.row-paid {
            background-color: #f0fdf4;
        }

        .table-payroll tbody tr.row-paid:hover {
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

        .badge-paid {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .badge-posted {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .badge-draft {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        /* 🌟 المبالغ 🌟 */
        .amount-display {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 15px;
        }

        .amount-salary {
            color: #1e293b;
        }

        .amount-allowance {
            color: #10b981;
        }

        .amount-deduction {
            color: #ef4444;
        }

        .amount-loan {
            color: #f59e0b;
        }

        .amount-net {
            color: #2563eb;
            font-size: 17px;
        }

        /* 🌟 معلومات الموظف 🌟 */
        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .employee-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            background: #f1f5f9;
        }

        .employee-name {
            font-weight: 800;
            color: #1e293b;
            font-size: 14px;
            margin-bottom: 2px;
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

        .action-btn-view {
            background: #f0f9ff;
            color: #0284c7;
            border-color: #bae6fd;
        }

        .action-btn-view:hover {
            background: #0284c7;
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
        .btn-generate {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        .btn-post {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            transition: all 0.3s ease;
        }

        .btn-post:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
            color: #fff;
        }

        .btn-bulk-pay {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }

        .btn-bulk-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: #fff;
        }

        /* 🌟 شريط الدفع الجماعي 🌟 */
        .bulk-action-bar {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid #a7f3d0;
            margin-bottom: 20px;
        }

        /* 🌟 التوقيع 🌟 */
        .signature-pad-wrapper {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background-color: #fff;
            padding: 10px;
            text-align: center;
        }

        /* 🌟 تفاصيل الراتب 🌟 */
        .payroll-details-box {
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
            font-size: 14px;
        }

        .detail-row:last-child {
            border-bottom: none;
            font-weight: 900;
            font-size: 16px;
            padding-top: 12px;
            margin-top: 5px;
            border-top: 2px solid #3b82f6;
        }

        .detail-row .label {
            color: #64748b;
            font-weight: 600;
        }

        .detail-row .value {
            font-family: 'Courier New', monospace;
            font-weight: 800;
        }

        .detail-row .value.positive {
            color: #10b981;
        }

        .detail-row .value.negative {
            color: #ef4444;
        }

        .detail-row .value.total {
            color: #2563eb;
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

        body[data-layout-color="dark"] .table-payroll thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-payroll tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-payroll tbody tr:hover td {
            background-color: #38414e !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select,
        body[data-layout-color="dark"] .filter-input {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .bulk-action-bar {
            background: #1e293b;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .payroll-details-box {
            background: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .detail-row {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .detail-row .label {
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .detail-row .value {
            color: #f8fafc;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection,
        body[data-layout-color="dark"] .select2-dropdown {
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
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                    <i class='bx bx-wallet text-primary fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">مسير الرواتب الشهري</h4>
                    <p class="text-muted mb-0 font-13">إدارة رواتب الموظفين -
                        {{ ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$month - 1] }}
                        {{ $year }}</p>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                {{-- زر توليد الرواتب --}}
                @can('generate_payroll')
                    <form method="POST" action="{{ route('admin.hr.payroll.generate') }}" id="generatePayrollForm"
                        class="d-inline">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="button" id="btnGeneratePayroll" class="btn btn-generate">
                            <i class='bx bx-cog me-1'></i> توليد مسير الشهر
                        </button>
                    </form>
                @endcan

                {{-- زر ترحيل القيود --}}
                @can('post_payroll_entries')
                    <form method="POST" action="{{ route('admin.hr.payroll.post_entries') }}" id="postEntriesForm"
                        class="d-inline">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="button" id="btnPostEntries" class="btn btn-post">
                            <i class='bx bx-book-bookmark me-1'></i> ترحيل القيود
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        {{-- 🌟 كروت الإحصائيات 🌟 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-primary p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي الرواتب</div>
                            <div class="stat-value text-primary">{{ $totalPayrolls }}</div>
                            <small class="text-muted">موظف</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-group'></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-success p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">تم الصرف</div>
                            <div class="stat-value text-success">{{ $paidCount }}</div>
                            <small class="text-muted">{{ number_format($totalPaid, 3) }} ر.ع</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-check-circle'></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-warning p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">معلق الصرف</div>
                            <div class="stat-value text-warning">{{ $draftCount }}</div>
                            <small class="text-muted">{{ number_format($totalPending, 3) }} ر.ع</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-time-five'></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-danger p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي الصافي</div>
                            <div class="stat-value text-danger">{{ number_format($totalNetSalary, 3) }}</div>
                            <small class="text-muted">ر.ع</small>
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
                <form method="GET" action="{{ route('admin.hr.payroll.index') }}" class="row align-items-end g-3">
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

                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-calendar-check me-1'></i> السنة</label>
                        <input type="number" name="year" class="form-control filter-input text-center"
                            value="{{ $year }}">
                    </div>

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

                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-flag me-1'></i> الحالة</label>
                        <select name="status" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            <option value="draft" {{ ($status ?? '') == 'draft' ? 'selected' : '' }}>معلق</option>
                            <option value="paid" {{ ($status ?? '') == 'paid' ? 'selected' : '' }}>تم الصرف</option>
                            <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>معتمد</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark flex-grow-1 fw-bold py-2 shadow-sm rounded-3">
                            <i class='bx bx-filter-alt me-1'></i> تصفية
                        </button>
                        <a href="{{ route('admin.hr.payroll.index') }}"
                            class="btn btn-light border fw-bold px-3 py-2 shadow-sm rounded-3" title="إعادة ضبط">
                            <i class='bx bx-refresh fs-5'></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 شريط الدفع الجماعي 🌟 --}}
        @can('pay_payroll')
            @if ($draftCount > 0)
                <div class="bulk-action-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-success">
                            <i class='bx bx-credit-card-front me-1'></i> صرف رواتب جماعي
                        </h5>
                        <small class="text-muted">حدد الموظفين من الجدول واختر حساب الصرف</small>
                    </div>
                    <form id="bulkPayForm" action="{{ route('admin.hr.payroll.pay_bulk') }}" method="POST"
                        class="d-flex gap-2 align-items-center flex-wrap">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <div id="hiddenCheckboxes"></div>

                        <select name="financial_account_id" class="form-select border-success fw-bold shadow-sm" required
                            style="min-width: 220px;">
                            <option value="" disabled selected>-- اختر حساب الصرف --</option>
                            @foreach ($paymentMethods as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name_ar }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="btnBulkPay" class="btn btn-bulk-pay">
                            <i class="bx bx-check-double me-1"></i> صرف للمحددين
                        </button>
                    </form>
                </div>
            @endif
        @endcan

        {{-- 🌟 جدول الرواتب 🌟 --}}
        <div class="card-premium">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                        <i class='bx bx-list-ul text-primary fs-4'></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">سجل مسير الرواتب</h5>
                        <small class="text-muted">عرض {{ $payrolls->count() }} راتب</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-payroll table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                @can('pay_payroll')
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="selectAll" class="form-check-input"
                                            style="transform: scale(1.2);">
                                    </th>
                                @endcan
                                <th class="text-start ps-4" style="width: 18%;">الموظف</th>
                                <th style="width: 10%;">الأساسي</th>
                                <th style="width: 10%;">العلاوات</th>
                                <th style="width: 10%;">الخصومات</th>
                                <th style="width: 9%;">السلف</th>
                                <th style="width: 11%;">الصافي</th>
                                <th style="width: 12%;">الحالة</th>
                                <th style="width: 14%;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $pr)
                                @php
                                    $rowClass = $pr->status === 'paid' ? 'row-paid' : '';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    @can('pay_payroll')
                                        <td>
                                            @if ($pr->status != 'paid')
                                                <input type="checkbox" class="form-check-input row-checkbox"
                                                    value="{{ $pr->id }}" style="transform: scale(1.2);">
                                            @else
                                                <i class='bx bx-check-circle text-success fs-5'></i>
                                            @endif
                                        </td>
                                    @endcan

                                    {{-- الموظف --}}
                                    <td class="text-start ps-4">
                                        <div class="employee-info">
                                            <img src="{{ $pr->employee->profile_image ? asset('storage/' . $pr->employee->profile_image) : asset('ltr/assets/images/avatars/avatar-1.png') }}"
                                                alt="Avatar" class="employee-avatar"
                                                onerror="this.onerror=null; this.src='{{ asset('ltr/assets/images/avatars/avatar-1.png') }}'">
                                            <div>
                                                <div class="employee-name">{{ $pr->employee->full_name_ar ?? 'محذوف' }}
                                                </div>
                                                <div class="employee-dept">
                                                    <i class='bx bx-building me-1'></i>
                                                    {{ $pr->employee->department->name_ar ?? 'بدون قسم' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- الراتب الأساسي --}}
                                    <td>
                                        <span class="amount-display amount-salary">
                                            {{ number_format($pr->basic_salary, 3) }}
                                        </span>
                                    </td>

                                    {{-- العلاوات --}}
                                    <td>
                                        <span class="amount-display amount-allowance">
                                            +{{ number_format($pr->total_allowances, 3) }}
                                        </span>
                                    </td>

                                    {{-- الخصومات --}}
                                    <td>
                                        <span class="amount-display amount-deduction">
                                            -{{ number_format($pr->total_deductions, 3) }}
                                        </span>
                                    </td>

                                    {{-- السلف --}}
                                    <td>
                                        @if ($pr->loan_deduction > 0)
                                            <span class="amount-display amount-loan">
                                                -{{ number_format($pr->loan_deduction, 3) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- الصافي --}}
                                    <td>
                                        <span class="amount-display amount-net">
                                            {{ number_format($pr->net_salary, 3) }}
                                        </span>
                                        <small class="text-muted d-block">ر.ع</small>
                                    </td>

                                    {{-- الحالة --}}
                                    <td>
                                        @if ($pr->status == 'paid')
                                            <span class="badge-status badge-paid mb-1">
                                                <i class='bx bx-check-circle'></i> تم الصرف
                                            </span>
                                            @if ($pr->transaction_id)
                                                <br>
                                                <span class="badge-status badge-posted" style="font-size: 10px;">
                                                    <i class='bx bx-book-bookmark'></i> مُرحّل
                                                </span>
                                            @endif
                                            @if ($pr->financialAccount)
                                                <small class="d-block text-muted mt-1">
                                                    <i class='bx bx-wallet me-1'></i>{{ $pr->financialAccount->name_ar }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="badge-status badge-draft">
                                                <i class='bx bx-time-five'></i> معلق
                                            </span>
                                        @endif
                                    </td>

                                    {{-- الإجراءات --}}
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            {{-- زر عرض التفاصيل --}}
                                            <button type="button" class="action-btn action-btn-view btn-view-details"
                                                data-basic="{{ number_format($pr->basic_salary, 3) }}"
                                                data-allowances="{{ number_format($pr->total_allowances, 3) }}"
                                                data-deductions="{{ number_format($pr->total_deductions, 3) }}"
                                                data-loan="{{ number_format($pr->loan_deduction, 3) }}"
                                                data-pasi="{{ number_format($pr->pasi_deduction, 3) }}"
                                                data-working="{{ $pr->working_days }}"
                                                data-absent="{{ $pr->absent_days }}"
                                                data-net="{{ number_format($pr->net_salary, 3) }}"
                                                data-name="{{ $pr->employee->full_name_ar ?? '---' }}"
                                                data-bs-toggle="modal" data-bs-target="#detailsModal"
                                                title="عرض التفاصيل">
                                                <i class='bx bx-show'></i>
                                            </button>

                                            {{-- زر الطباعة --}}
                                            <a href="{{ route('admin.hr.payroll.print', $pr->id) }}" target="_blank"
                                                class="action-btn action-btn-print" title="طباعة مفردة الراتب">
                                                <i class='bx bx-printer'></i>
                                            </a>

                                            @if ($pr->status != 'paid')
                                                @can('pay_payroll')
                                                    {{-- زر الصرف --}}
                                                    <button type="button" class="action-btn action-btn-pay btn-pay-single"
                                                        data-id="{{ $pr->id }}"
                                                        data-net="{{ number_format($pr->net_salary, 3) }}"
                                                        data-name="{{ $pr->employee->full_name_ar ?? '' }}"
                                                        data-bank="{{ $pr->employee->bank_name ?? '' }}"
                                                        data-iban="{{ $pr->employee->iban_number ?? '' }}"
                                                        data-bs-toggle="modal" data-bs-target="#paySingleModal"
                                                        title="صرف الراتب">
                                                        <i class='bx bx-credit-card'></i>
                                                    </button>
                                                @endcan
                                            @else
                                                @can('manage_payrolls')
                                                    {{-- زر إلغاء الصرف --}}
                                                    <form action="{{ route('admin.hr.payroll.reverse', $pr->id) }}"
                                                        method="POST" class="d-inline reverse-form">
                                                        @csrf
                                                        <button type="button"
                                                            class="action-btn action-btn-reverse btn-reverse"
                                                            title="إلغاء الصرف">
                                                            <i class='bx bx-undo'></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('pay_payroll') ? 9 : 8 }}" class="p-5 text-center">
                                        <div class="py-4">
                                            <i class='bx bx-calculator text-muted opacity-25'
                                                style="font-size: 80px;"></i>
                                            <h5 class="text-muted fw-bold mt-3">لم يتم توليد مسير رواتب لهذا الشهر</h5>
                                            <p class="text-muted mb-0">اضغط على "توليد مسير الشهر" لبدء العملية</p>
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

    {{-- 🔥 مودال عرض تفاصيل الراتب 🔥 --}}
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class='bx bx-detail text-primary me-2'></i>
                        تفاصيل الراتب - <span id="details_emp_name" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="payroll-details-box">
                        <h6 class="fw-bold text-success mb-3"><i class='bx bx-plus-circle me-1'></i> الاستحقاقات</h6>
                        <div class="detail-row">
                            <span class="label">الراتب الأساسي</span>
                            <span class="value positive" id="details_basic">0.000</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">العلاوات والإضافات</span>
                            <span class="value positive" id="details_allowances">+0.000</span>
                        </div>
                    </div>

                    <div class="payroll-details-box">
                        <h6 class="fw-bold text-danger mb-3"><i class='bx bx-minus-circle me-1'></i> الخصومات</h6>
                        <div class="detail-row">
                            <span class="label">الخصومات الإدارية</span>
                            <span class="value negative" id="details_deductions">-0.000</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">التأمينات الاجتماعية</span>
                            <span class="value negative" id="details_pasi">-0.000</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">أقساط السلف</span>
                            <span class="value negative" id="details_loan">-0.000</span>
                        </div>
                    </div>

                    <div class="payroll-details-box" style="background: #eff6ff; border-color: #3b82f6;">
                        <div class="detail-row" style="border: none;">
                            <span class="label fw-bold fs-5">أيام العمل</span>
                            <span class="value" id="details_working">0</span>
                        </div>
                        <div class="detail-row" style="border: none;">
                            <span class="label fw-bold fs-5">أيام الغياب</span>
                            <span class="value negative" id="details_absent">0</span>
                        </div>
                    </div>

                    <div class="payroll-details-box"
                        style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-color: #2563eb;">
                        <div class="detail-row" style="border: none; margin: 0; padding: 0;">
                            <span class="label fs-4">الصافي المستحق</span>
                            <span class="value total fs-3" id="details_net">0.000 ر.ع</span>
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

    {{-- 🔥 مودال الصرف الفردي 🔥 --}}
    @can('pay_payroll')
        <div class="modal fade" id="paySingleModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-credit-card text-success me-2'></i>
                            صرف راتب فردي
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="paySingleForm" enctype="multipart/form-data"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            {{-- معلومات الموظف --}}
                            <div class="alert alert-info border-0 rounded-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold fs-5" id="modalEmpName"></div>
                                        <small class="text-muted">
                                            <i class='bx bx-bank me-1'></i> <span id="modalEmpBank"></span> |
                                            <i class='bx bx-barcode me-1'></i> <span id="modalEmpIban"
                                                class="font-monospace"></span>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">الصافي المستحق</div>
                                        <div class="fs-3 fw-bold text-success font-monospace">
                                            <span id="modalNetSalary"></span> <small>ر.ع</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- اختيار الحساب --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-wallet text-primary me-1'></i> حساب الصرف
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="financial_account_id" class="form-select fw-bold" required>
                                    <option value="" disabled selected>-- اختر مصدر الصرف --</option>
                                    @foreach ($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- تبويبات التوقيع/الإيصال --}}
                            <ul class="nav nav-pills mb-3 nav-justified">
                                <li class="nav-item">
                                    <button class="nav-link active fw-bold" data-bs-toggle="pill"
                                        data-bs-target="#pills-sign" type="button">
                                        <i class="bx bx-edit me-1"></i> توقيع الموظف
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#pills-upload"
                                        type="button">
                                        <i class="bx bx-cloud-upload me-1"></i> إيصال تحويل
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pills-sign">
                                    <label class="form-label fw-bold">توقيع المستلم:</label>
                                    <div class="signature-pad-wrapper">
                                        <canvas id="signature-pad" class="w-100" height="150"
                                            style="touch-action: none; cursor: crosshair; border-radius: 8px;"></canvas>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2 rounded-pill px-3"
                                        id="clear-signature">
                                        <i class='bx bx-eraser me-1'></i> مسح التوقيع
                                    </button>
                                    <input type="hidden" name="signature_base64" id="signature_base64">
                                </div>

                                <div class="tab-pane fade" id="pills-upload">
                                    <label class="form-label fw-bold">إيصال التحويل البنكي:</label>
                                    <input type="file" name="receipt_file" class="form-control"
                                        accept="image/png, image/jpeg, application/pdf">
                                    <small class="text-muted">
                                        <i class='bx bx-info-circle me-1'></i> صورة أو PDF لإيصال الإيداع
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 btn-submit">
                                <i class='bx bx-check me-1'></i> تأكيد الصرف
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

            // تفعيل Select2
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // 🌟 التوقيع الإلكتروني 🌟
            var canvas = document.getElementById('signature-pad');
            var signaturePad;

            if (canvas) {
                function initSignaturePad() {
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

                $('#paySingleModal').on('shown.bs.modal', function() {
                    initSignaturePad();
                });

                $('#clear-signature').click(function() {
                    if (signaturePad) signaturePad.clear();
                });
            }

            // حماية الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                // التحقق من التوقيع
                if ($('#pills-sign').hasClass('active') && signaturePad && signaturePad.isEmpty()) {
                    // تحقق من رفع ملف
                    if (!$('input[name="receipt_file"]').val()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'يرجى توقيع الموظف أو رفع إيصال التحويل',
                            confirmButtonColor: '#3b82f6'
                        });
                        return false;
                    }
                }

                if (signaturePad && !signaturePad.isEmpty() && $('#pills-sign').hasClass('active')) {
                    $('#signature_base64').val(signaturePad.toDataURL('image/png'));
                }

                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري الصرف...');
            });

            // 🌟 توليد الرواتب 🌟
            $('#btnGeneratePayroll').click(function() {
                let form = $('#generatePayrollForm');
                Swal.fire({
                    title: 'تأكيد توليد مسير الرواتب؟',
                    html: "سيقوم النظام بـ:<br><br>• احتساب أيام الغياب والإجازات<br>• حساب خصم التأخير<br>• إضافة العمل الإضافي<br>• خصم أقساط السلف<br>• احتساب التأمينات الاجتماعية",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bx bx-check me-1"></i> نعم، توليد',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).prop('disabled', true).html(
                            '<i class="bx bx-loader bx-spin me-1"></i> جاري التوليد...');
                        form.submit();
                    }
                });
            });

            // 🌟 ترحيل القيود 🌟
            $('#btnPostEntries').click(function() {
                let form = $('#postEntriesForm');
                Swal.fire({
                    title: 'ترحيل القيود المحاسبية؟',
                    html: "سيتم ترحيل رواتب الموظفين المصروفة كقيد مصروفات.<br><br><small class='text-danger'>لا يمكن التراجع عن هذه العملية!</small>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bx bx-check me-1"></i> نعم، ترحيل',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).prop('disabled', true).html(
                            '<i class="bx bx-loader bx-spin me-1"></i> جاري الترحيل...');
                        form.submit();
                    }
                });
            });

            // 🌟 تحديد الكل 🌟
            $('#selectAll').change(function() {
                $('.row-checkbox').prop('checked', $(this).prop('checked'));
            });

            // 🌟 الصرف الجماعي 🌟
            $('#btnBulkPay').click(function() {
                let selected = [];
                $('.row-checkbox:checked').each(function() {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى تحديد موظف واحد على الأقل',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }

                if (!$('select[name="financial_account_id"]').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى اختيار حساب الصرف',
                        confirmButtonColor: '#3b82f6'
                    });
                    return;
                }

                Swal.fire({
                    title: `تأكيد صرف ${selected.length} راتب؟`,
                    text: 'سيتم صرف الرواتب وخصم أقساط السلف تلقائياً',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bx bx-check me-1"></i> نعم، صرف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).prop('disabled', true).html(
                            '<i class="bx bx-loader bx-spin me-1"></i> جاري الصرف...');
                        $('#hiddenCheckboxes').empty();
                        selected.forEach(id => {
                            $('#hiddenCheckboxes').append(
                                `<input type="hidden" name="payroll_ids[]" value="${id}">`
                            );
                        });
                        $('#bulkPayForm').submit();
                    }
                });
            });

            // 🌟 عرض تفاصيل الراتب 🌟
            $('.btn-view-details').click(function() {
                $('#details_emp_name').text($(this).data('name'));
                $('#details_basic').text($(this).data('basic') + ' ر.ع');
                $('#details_allowances').text('+' + $(this).data('allowances') + ' ر.ع');
                $('#details_deductions').text('-' + $(this).data('deductions') + ' ر.ع');
                $('#details_pasi').text('-' + $(this).data('pasi') + ' ر.ع');
                $('#details_loan').text('-' + $(this).data('loan') + ' ر.ع');
                $('#details_working').text($(this).data('working') + ' يوم');
                $('#details_absent').text($(this).data('absent') + ' يوم');
                $('#details_net').text($(this).data('net') + ' ر.ع');
            });

            // 🌟 الصرف الفردي 🌟
            $('.btn-pay-single').click(function() {
                let id = $(this).data('id');
                $('#modalEmpName').text($(this).data('name'));
                $('#modalNetSalary').text($(this).data('net'));
                $('#modalEmpBank').text($(this).data('bank') || 'غير مسجل');
                $('#modalEmpIban').text($(this).data('iban') || '---');

                let url = "{{ route('admin.hr.payroll.pay_single', ':id') }}".replace(':id', id);
                $('#paySingleForm').attr('action', url);

                $('#signature_base64').val('');
                $('input[name="receipt_file"]').val('');
            });

            // 🌟 تأكيد إلغاء الصرف 🌟
            $('.btn-reverse').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد إلغاء الصرف؟',
                    html: "سيتم:<br>• إعادة المبلغ للحساب<br>• إعادة الحركات المالية إلى معلقة<br>• إعادة السلف لحالتها السابقة<br>• حذف القيد المحاسبي",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، إلغاء الصرف',
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

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: "{{ session('warning') }}",
                    confirmButtonColor: '#f59e0b'
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
