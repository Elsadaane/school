@extends('backend.layouts.master')
@section('title', 'الحسابات المالية')
@section('title_page', 'النظام المالي - الحسابات والخزائن')

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
            border-top: 5px solid #004b87;
        }

        .table-accounts {
            margin-bottom: 0;
        }

        .table-accounts thead th {
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

        .table-accounts tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
            transition: background-color 0.2s ease;
        }

        .table-accounts tbody tr:hover td {
            background-color: #fbfccb !important;
        }

        .table-accounts tbody tr.row-inactive {
            background-color: #f8fafc;
            opacity: 0.7;
        }

        .table-accounts tbody tr.row-inactive:hover {
            background-color: #f1f5f9 !important;
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
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .badge-inactive {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* 🌟 شارات النوع 🌟 */
        .badge-type {
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-asset {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .badge-liability {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .badge-equity {
            background: #ede9fe;
            color: #5b21b6;
            border: 1px solid #c4b5fd;
        }

        .badge-revenue {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .badge-expense {
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

        .amount-positive {
            color: #10b981;
        }

        .amount-negative {
            color: #ef4444;
        }

        .amount-zero {
            color: #94a3b8;
        }

        /* 🌟 معلومات الحساب 🌟 */
        .account-code {
            font-family: 'Courier New', monospace;
            font-weight: 800;
            color: #64748b;
            font-size: 13px;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .account-name {
            font-weight: 800;
            color: #1e293b;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .account-name-en {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            direction: ltr;
            text-align: right;
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

        .action-btn-transfer {
            background: #fef3c7;
            color: #d97706;
            border-color: #fde68a;
        }

        .action-btn-transfer:hover {
            background: #d97706;
            color: #fff;
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

        .action-btn-ledger {
            background: #f0fdf4;
            color: #059669;
            border-color: #a7f3d0;
        }

        .action-btn-ledger:hover {
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

        .btn-transfer {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            transition: all 0.3s ease;
        }

        .btn-transfer:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
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

        /* 🌟 تنبيه الرصيد 🌟 */
        .balance-alert {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 1px solid #3b82f6;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #1e40af;
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
        body[data-layout-color="dark"] .account-name {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .table-accounts thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-accounts tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-accounts tbody tr:hover td {
            background-color: #38414e !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select,
        body[data-layout-color="dark"] .filter-input {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .account-code {
            background: #3b4652;
            color: #adb5bd;
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
                    <h4 class="fw-bold mb-1 text-dark">الحسابات المالية</h4>
                    <p class="text-muted mb-0 font-13">إدارة الحسابات والخزائن والتحويلات المالية</p>
                </div>
            </div>

            @can('manage_financial_accounts')
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-transfer" data-bs-toggle="modal" data-bs-target="#transferModal">
                        <i class='bx bx-transfer me-1'></i> تحويل مالي
                    </button>
                    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                        <i class='bx bx-plus-circle me-1'></i> إضافة حساب
                    </button>
                </div>
            @endcan
        </div>

        {{-- 🌟 كروت الإحصائيات 🌟 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card stat-primary p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي الحسابات</div>
                            <div class="stat-value text-primary">{{ $totalAccounts ?? $accounts->count() }}</div>
                            <small class="text-muted">{{ $activeAccounts ?? $accounts->where('is_active', 1)->count() }}
                                نشط</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-folder'></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-success p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي الأرصدة</div>
                            <div class="stat-value text-success">
                                {{ number_format($totalBalance ?? $accounts->sum('balance'), 3) }}</div>
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
                            <div class="stat-label">حسابات الأصول</div>
                            <div class="stat-value text-warning">
                                {{ $byType['asset'] ?? $accounts->where('account_type', 'asset')->count() }}</div>
                            <small class="text-muted">خزائن وبنوك</small>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-building-house'></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-danger p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">حسابات المصروفات</div>
                            <div class="stat-value text-danger">
                                {{ $byType['expense'] ?? $accounts->where('account_type', 'expense')->count() }}</div>
                            <small class="text-muted">مصروفات وتكاليف</small>
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
                <form method="GET" action="{{ route('admin.financial-accounts.index') }}" class="row align-items-end g-3">
                    <div class="col-md-3">
                        <label class="filter-label"><i class='bx bx-search me-1'></i> بحث</label>
                        <input type="text" name="search" class="form-control filter-input"
                            placeholder="اسم الحساب أو الكود..." value="{{ $search ?? '' }}">
                    </div>

                    <div class="col-md-3">
                        <label class="filter-label"><i class='bx bx-category me-1'></i> نوع الحساب</label>
                        <select name="account_type" class="form-select filter-input">
                            <option value="">-- جميع الأنواع --</option>
                            <option value="asset" {{ ($accountType ?? '') == 'asset' ? 'selected' : '' }}>🏦 أصول
                                (خزائن/بنوك)</option>
                            <option value="liability" {{ ($accountType ?? '') == 'liability' ? 'selected' : '' }}>📋 خصوم
                            </option>
                            <option value="equity" {{ ($accountType ?? '') == 'equity' ? 'selected' : '' }}>💎 حقوق ملكية
                            </option>
                            <option value="revenue" {{ ($accountType ?? '') == 'revenue' ? 'selected' : '' }}>📈 إيرادات
                            </option>
                            <option value="expense" {{ ($accountType ?? '') == 'expense' ? 'selected' : '' }}>📉 مصروفات
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-toggle-left me-1'></i> الحالة</label>
                        <select name="is_active" class="form-select filter-input">
                            <option value="">-- الكل --</option>
                            <option value="1" {{ ($isActive ?? '') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ ($isActive ?? '') === '0' ? 'selected' : '' }}>معطل</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-dark flex-grow-1 fw-bold py-2 shadow-sm rounded-3">
                            <i class='bx bx-filter-alt me-1'></i> تصفية
                        </button>
                        <a href="{{ route('admin.financial-accounts.index') }}"
                            class="btn btn-light border fw-bold px-3 py-2 shadow-sm rounded-3" title="إعادة ضبط">
                            <i class='bx bx-refresh fs-5'></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 جدول الحسابات 🌟 --}}
        <div class="card-premium">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                        <i class='bx bx-list-ul text-primary fs-4'></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">دليل الحسابات المالية</h5>
                        <small class="text-muted">عرض {{ $accounts->count() }} حساب</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-accounts table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th style="width: 8%;">الكود</th>
                                <th class="text-start ps-4" style="width: 22%;">اسم الحساب</th>
                                <th style="width: 12%;">النوع</th>
                                <th style="width: 12%;">الرصيد الافتتاحي</th>
                                <th style="width: 12%;">الرصيد الحالي</th>
                                <th style="width: 10%;">الحالة</th>
                                @can('manage_financial_accounts')
                                    <th style="width: 14%;">الإجراءات</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $account)
                                @php
                                    $rowClass = !$account->is_active ? 'row-inactive' : '';
                                    $typeLabels = [
                                        'asset' => ['أصول', 'asset', 'bx-building-house'],
                                        'liability' => ['خصوم', 'liability', 'bx-file'],
                                        'equity' => ['حقوق ملكية', 'equity', 'bx-diamond'],
                                        'revenue' => ['إيرادات', 'revenue', 'bx-trending-up'],
                                        'expense' => ['مصروفات', 'expense', 'bx-trending-down'],
                                    ];
                                    $typeInfo = $typeLabels[$account->account_type] ?? [
                                        'غير محدد',
                                        'secondary',
                                        'bx-help-circle',
                                    ];
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    {{-- الكود --}}
                                    <td>
                                        <span class="account-code">{{ $account->account_code }}</span>
                                    </td>

                                    {{-- اسم الحساب --}}
                                    <td class="text-start ps-4">
                                        <div class="account-name">{{ $account->name_ar }}</div>
                                        @if ($account->name_en)
                                            <div class="account-name-en">{{ $account->name_en }}</div>
                                        @endif
                                    </td>

                                    {{-- النوع --}}
                                    <td>
                                        <span class="badge-type badge-{{ $typeInfo[1] }}">
                                            <i class='bx {{ $typeInfo[2] }}'></i>
                                            {{ $typeInfo[0] }}
                                        </span>
                                    </td>

                                    {{-- الرصيد الافتتاحي --}}
                                    <td>
                                        <span class="amount-display text-muted">
                                            {{ number_format($account->opening_balance ?? 0, 3) }}
                                        </span>
                                    </td>

                                    {{-- الرصيد الحالي --}}
                                    <td>
                                        @php
                                            $balanceClass =
                                                $account->balance > 0
                                                    ? 'amount-positive'
                                                    : ($account->balance < 0
                                                        ? 'amount-negative'
                                                        : 'amount-zero');
                                        @endphp
                                        <span class="amount-display {{ $balanceClass }}">
                                            {{ number_format($account->balance, 3) }}
                                        </span>
                                        <small class="text-muted d-block">ر.ع</small>
                                    </td>

                                    {{-- الحالة --}}
                                    <td>
                                        @if ($account->is_active)
                                            <span class="badge-status badge-active">
                                                <i class='bx bx-check-circle'></i> نشط
                                            </span>
                                        @else
                                            <span class="badge-status badge-inactive">
                                                <i class='bx bx-x-circle'></i> معطل
                                            </span>
                                        @endif
                                    </td>

                                    {{-- الإجراءات --}}
                                    @can('manage_financial_accounts')
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                {{-- زر التحويل --}}
                                                @if ($account->is_active && $account->balance > 0 && $account->account_type === 'asset')
                                                    <button type="button"
                                                        class="action-btn action-btn-transfer open-transfer-modal"
                                                        data-id="{{ $account->id }}" data-balance="{{ $account->balance }}"
                                                        title="تحويل من هذا الحساب">
                                                        <i class='bx bx-transfer'></i>
                                                    </button>
                                                @endif

                                                {{-- زر دفتر الأستاذ --}}
                                                <a href="{{ route('admin.financial-accounts.ledger', $account->id) }}"
                                                    class="action-btn action-btn-ledger" title="دفتر الأستاذ">
                                                    <i class='bx bx-book-open'></i>
                                                </a>

                                                {{-- زر التعديل --}}
                                                <button type="button" class="action-btn action-btn-edit open-edit-modal"
                                                    data-id="{{ $account->id }}" data-code="{{ $account->account_code }}"
                                                    data-name-ar="{{ $account->name_ar }}"
                                                    data-name-en="{{ $account->name_en }}"
                                                    data-type="{{ $account->account_type }}"
                                                    data-normal-balance="{{ $account->normal_balance }}"
                                                    data-parent-id="{{ $account->parent_id }}"
                                                    data-active="{{ $account->is_active }}" title="تعديل">
                                                    <i class='bx bx-edit'></i>
                                                </button>

                                                {{-- زر الحذف --}}
                                                <form action="{{ route('admin.financial-accounts.destroy', $account->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="action-btn action-btn-delete btn-delete-account"
                                                        title="حذف">
                                                        <i class='bx bx-trash'></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('manage_financial_accounts') ? 7 : 6 }}"
                                        class="p-5 text-center">
                                        <div class="py-4">
                                            <i class='bx bx-wallet text-muted opacity-25' style="font-size: 80px;"></i>
                                            <h5 class="text-muted fw-bold mt-3">لا توجد حسابات مالية مسجلة</h5>
                                            <p class="text-muted mb-0">اضغط على "إضافة حساب" لبدء إنشاء دليل الحسابات</p>
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

    {{-- 🔥 مودال إضافة حساب 🔥 --}}
    @can('manage_financial_accounts')
        <div class="modal fade" id="addAccountModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-plus-circle text-primary me-2'></i>
                            إضافة حساب مالي جديد
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.financial-accounts.store') }}" method="POST"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-3">
                                {{-- الكود المحاسبي --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-hash text-primary me-1'></i> الكود المحاسبي
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="account_code" class="form-control font-monospace fw-bold"
                                        placeholder="مثال: 1101" required maxlength="50">
                                    <small class="text-muted">كود فريد للحساب</small>
                                </div>

                                {{-- اسم الحساب بالعربي --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-font text-primary me-1'></i> اسم الحساب (عربي)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name_ar" class="form-control fw-bold"
                                        placeholder="مثال: الصندوق الرئيسي" required>
                                </div>

                                {{-- اسم الحساب بالإنجليزي --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-font text-primary me-1'></i> اسم الحساب (إنجليزي)
                                    </label>
                                    <input type="text" name="name_en" class="form-control" placeholder="مثال: Main Cash"
                                        dir="ltr">
                                </div>
                            </div>

                            <div class="row g-3">
                                {{-- نوع الحساب --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-category text-primary me-1'></i> نوع الحساب
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="account_type" class="form-select" required>
                                        <option value="" disabled selected>-- اختر النوع --</option>
                                        <option value="asset">🏦 أصول (خزائن/بنوك)</option>
                                        <option value="liability">📋 خصوم</option>
                                        <option value="equity">💎 حقوق ملكية</option>
                                        <option value="revenue">📈 إيرادات</option>
                                        <option value="expense">📉 مصروفات</option>
                                    </select>
                                </div>

                                {{-- الطبيعة الطبيعية --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-transfer text-primary me-1'></i> الطبيعة الطبيعية
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="normal_balance" class="form-select" required>
                                        <option value="" disabled selected>-- اختر --</option>
                                        <option value="debit">مدين (Debit)</option>
                                        <option value="credit">دائن (Credit)</option>
                                    </select>
                                </div>

                                {{-- الرصيد الافتتاحي --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-money text-primary me-1'></i> الرصيد الافتتاحي
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.001" name="opening_balance"
                                            class="form-control font-monospace fw-bold text-center" value="0.000"
                                            min="0">
                                        <span class="input-group-text fw-bold">ر.ع</span>
                                    </div>
                                </div>
                            </div>

                            {{-- الحساب الأب --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-git-branch text-primary me-1'></i> الحساب الأب (اختياري)
                                </label>
                                <select name="parent_id" class="form-select">
                                    <option value="">-- لا يوجد حساب أب --</option>
                                    @foreach ($accounts as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->name_ar }}
                                            ({{ $parent->account_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- حالة النشاط --}}
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="addIsActive"
                                    value="1" checked>
                                <label class="form-check-label fw-bold" for="addIsActive">
                                    الحساب نشط (مفعّل)
                                </label>
                            </div>

                            <div class="balance-alert mt-3">
                                <i class='bx bx-info-circle me-1'></i>
                                <strong>تنبيه:</strong> الرصيد الافتتاحي يُسجل مرة واحدة فقط. بعد الحفظ، يتم تحديث الرصيد فقط من
                                خلال القيود المحاسبية.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 btn-submit">
                                <i class='bx bx-check me-1'></i> حفظ الحساب
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔥 مودال تعديل حساب 🔥 --}}
        <div class="modal fade" id="editAccountModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-edit text-primary me-2'></i>
                            تعديل حساب مالي
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editAccountForm" class="prevent-double-submit">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row g-3">
                                {{-- الكود المحاسبي --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-hash text-primary me-1'></i> الكود المحاسبي
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="account_code" id="edit_account_code"
                                        class="form-control font-monospace fw-bold" required maxlength="50">
                                </div>

                                {{-- اسم الحساب بالعربي --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-font text-primary me-1'></i> اسم الحساب (عربي)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name_ar" id="edit_name_ar" class="form-control fw-bold"
                                        required>
                                </div>

                                {{-- اسم الحساب بالإنجليزي --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-font text-primary me-1'></i> اسم الحساب (إنجليزي)
                                    </label>
                                    <input type="text" name="name_en" id="edit_name_en" class="form-control"
                                        dir="ltr">
                                </div>
                            </div>

                            <div class="row g-3">
                                {{-- نوع الحساب --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-category text-primary me-1'></i> نوع الحساب
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="account_type" id="edit_account_type" class="form-select" required>
                                        <option value="asset">🏦 أصول</option>
                                        <option value="liability">📋 خصوم</option>
                                        <option value="equity">💎 حقوق ملكية</option>
                                        <option value="revenue">📈 إيرادات</option>
                                        <option value="expense">📉 مصروفات</option>
                                    </select>
                                </div>

                                {{-- الطبيعة الطبيعية --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-transfer text-primary me-1'></i> الطبيعة الطبيعية
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="normal_balance" id="edit_normal_balance" class="form-select" required>
                                        <option value="debit">مدين (Debit)</option>
                                        <option value="credit">دائن (Credit)</option>
                                    </select>
                                </div>

                                {{-- الحساب الأب --}}
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">
                                        <i class='bx bx-git-branch text-primary me-1'></i> الحساب الأب
                                    </label>
                                    <select name="parent_id" id="edit_parent_id" class="form-select">
                                        <option value="">-- لا يوجد --</option>
                                        @foreach ($accounts as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- حالة النشاط --}}
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active"
                                    value="1">
                                <label class="form-check-label fw-bold" for="edit_is_active">
                                    الحساب نشط (مفعّل)
                                </label>
                            </div>

                            <div class="balance-alert mt-3">
                                <i class='bx bx-info-circle me-1'></i>
                                <strong>ملاحظة:</strong> الرصيد الحالي لا يمكن تعديله من هنا. يتم تحديثه فقط من خلال القيود
                                المحاسبية.
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

        {{-- 🔥 مودال التحويل المالي 🔥 --}}
        <div class="modal fade" id="transferModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-transfer text-warning me-2'></i>
                            تحويل بين الحسابات
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.financial-accounts.transfer') }}" method="POST" id="transferForm"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            {{-- الحساب المصدر --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-danger">
                                    <i class='bx bx-log-out me-1'></i> من حساب (المصدر)
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="from_account_id" id="modal_from_account" class="form-select select2-transfer"
                                    required style="width: 100%;">
                                    <option value="" disabled selected>-- اختر الحساب المصدر --</option>
                                    @foreach ($accounts->where('is_active', 1)->where('account_type', 'asset') as $acc)
                                        <option value="{{ $acc->id }}" data-balance="{{ $acc->balance }}">
                                            {{ $acc->name_ar }} ({{ $acc->account_code }}) - متاح:
                                            {{ number_format($acc->balance, 3) }} ر.ع
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">
                                    الرصيد المتاح: <span id="modal_balance_display"
                                        class="fw-bold text-danger font-monospace">0.000</span> ر.ع
                                </small>
                            </div>

                            {{-- سهم التحويل --}}
                            <div class="text-center my-3">
                                <i class='bx bx-arrow-down text-muted' style="font-size: 32px;"></i>
                            </div>

                            {{-- الحساب الوجهة --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-success">
                                    <i class='bx bx-log-in me-1'></i> إلى حساب (الوجهة)
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="to_account_id" id="modal_to_account" class="form-select select2-transfer"
                                    required style="width: 100%;">
                                    <option value="" disabled selected>-- اختر الحساب الوجهة --</option>
                                    @foreach ($accounts->where('is_active', 1)->where('account_type', 'asset') as $acc)
                                        <option value="{{ $acc->id }}">
                                            {{ $acc->name_ar }} ({{ $acc->account_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- المبلغ --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class='bx bx-money me-1'></i> مبلغ التحويل
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.001" min="0.001" name="amount"
                                        id="modal_transfer_amount"
                                        class="form-control fw-bold text-center font-monospace fs-5" placeholder="0.000"
                                        required>
                                    <span class="input-group-text fw-bold">ر.ع</span>
                                </div>
                            </div>

                            {{-- ملاحظات --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class='bx bx-comment-detail text-primary me-1'></i> البيان / ملاحظات
                                </label>
                                <input type="text" name="notes" class="form-control"
                                    placeholder="مثال: إيداع نهاية اليوم في البنك">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إلغاء
                            </button>
                            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 btn-submit">
                                <i class='bx bx-check me-1'></i> تنفيذ التحويل
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

            // تفعيل Select2 في مودال التحويل
            $('#transferModal').on('shown.bs.modal', function() {
                $('.select2-transfer').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#transferModal')
                });
            });

            // حماية الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري المعالجة...');
            });

            // 🌟 منطق التعديل 🌟
            $('.open-edit-modal').click(function() {
                let id = $(this).data('id');
                $('#edit_account_code').val($(this).data('code'));
                $('#edit_name_ar').val($(this).data('name-ar'));
                $('#edit_name_en').val($(this).data('name-en'));
                $('#edit_account_type').val($(this).data('type'));
                $('#edit_normal_balance').val($(this).data('normal-balance'));
                $('#edit_parent_id').val($(this).data('parent-id') || '');
                $('#edit_is_active').prop('checked', $(this).data('active') == 1);

                let url = "{{ route('admin.financial-accounts.update', ':id') }}".replace(':id', id);
                $('#editAccountForm').attr('action', url);
            });

            // 🌟 منطق التحويل 🌟
            $('.open-transfer-modal').click(function() {
                let accountId = $(this).data('id');
                $('#modal_from_account').val(accountId).trigger('change');
                $('#transferModal').modal('show');
            });

            $('#modal_from_account').change(function() {
                let selectedFromId = $(this).val();
                let balance = parseFloat($(this).find(':selected').data('balance')) || 0;

                $('#modal_balance_display').text(balance.toFixed(3));
                $('#modal_transfer_amount').attr('max', balance);

                // إخفاء الحساب المصدر من القائمة الوجهة
                $('#modal_to_account option').each(function() {
                    if ($(this).val() === selectedFromId) {
                        $(this).attr('disabled', 'disabled');
                    } else {
                        $(this).removeAttr('disabled');
                    }
                });

                if ($('#modal_to_account').val() === selectedFromId) {
                    $('#modal_to_account').val('').trigger('change');
                }

                if ($('#modal_to_account').hasClass("select2-hidden-accessible")) {
                    $('#modal_to_account').select2("destroy").select2({
                        theme: 'bootstrap-5',
                        dir: isRtl ? 'rtl' : 'ltr',
                        dropdownParent: $('#transferModal')
                    });
                }
            });

            // 🌟 تأكيد التحويل 🌟
            $('#transferForm').submit(function(e) {
                let from = $('#modal_from_account').val();
                let to = $('#modal_to_account').val();
                let amount = parseFloat($('#modal_transfer_amount').val());
                let maxBalance = parseFloat($('#modal_from_account').find(':selected').data('balance')) ||
                    0;

                if (!from || !to) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى تحديد الحساب المصدر والوجهة',
                        confirmButtonColor: '#f59e0b'
                    });
                    return false;
                } else if (from === to) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'لا يمكن التحويل لنفس الحساب!',
                        confirmButtonColor: '#f59e0b'
                    });
                    return false;
                } else if (amount > maxBalance) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'المبلغ أكبر من الرصيد المتاح!',
                        confirmButtonColor: '#ef4444'
                    });
                    return false;
                }
            });

            // 🌟 تأكيد الحذف 🌟
            $('.btn-delete-account').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد الحذف؟',
                    text: "سيتم حذف الحساب نهائياً. إذا كان الحساب يحتوي على حركات مالية، سيرفض النظام الحذف.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف',
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
