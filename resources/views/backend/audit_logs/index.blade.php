@extends('backend.layouts.master')
@section('title', 'سجل الرقابة والأمان')
@section('title_page', 'الأمان - سجل الرقابة التاريخي')

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
            padding: 25px;
            color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card .stat-icon {
            position: absolute;
            right: -10px;
            bottom: -20px;
            font-size: 120px;
            opacity: 0.15;
            transform: rotate(15deg);
        }

        .stat-card .stat-label {
            font-size: 13px;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 900;
            font-family: 'Courier New', monospace;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
            border-top: 5px solid #6366f1;
        }

        .table-audit {
            margin-bottom: 0;
        }

        .table-audit thead th {
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

        .table-audit tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
            transition: background-color 0.2s ease;
        }

        .table-audit tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        /* 🌟 شارات الأحداث 🌟 */
        .badge-event {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-created {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .badge-updated {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-deleted {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .badge-login {
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #a5b4fc;
        }

        /* 🌟 صندوق التفاصيل 🌟 */
        .log-details-box {
            background: #f8fafc;
            border-right: 3px solid #3b82f6;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .log-details-box.sensitive {
            background: #fffbeb;
            border-right-color: #f59e0b;
        }

        .diff-old {
            color: #dc2626;
            text-decoration: line-through;
            background: #fee2e2;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        .diff-new {
            color: #059669;
            background: #d1fae5;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            font-weight: 700;
        }

        .diff-arrow {
            color: #94a3b8;
            margin: 0 8px;
            font-weight: 800;
        }

        .diff-field {
            color: #2563eb;
            font-weight: 800;
            font-size: 12px;
            margin-bottom: 4px;
            font-family: 'Courier New', monospace;
        }

        /* 🌟 معلومات المستخدم 🌟 */
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            flex-shrink: 0;
        }

        .user-avatar.system {
            background: linear-gradient(135deg, #64748b, #475569);
        }

        .user-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 14px;
            line-height: 1.2;
        }

        .user-email {
            color: #64748b;
            font-size: 11px;
        }

        /* 🌟 معلومات السجل 🌟 */
        .log-subject {
            background: #eff6ff;
            color: #1e40af;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
            font-family: 'Courier New', monospace;
            text-transform: uppercase;
        }

        .log-id {
            color: #64748b;
            font-size: 11px;
            font-family: 'Courier New', monospace;
            display: block;
            margin-top: 3px;
        }

        .timestamp {
            font-family: 'Courier New', monospace;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
        }

        .timestamp-time {
            color: #3b82f6;
            font-weight: 700;
            display: block;
            margin-top: 2px;
            font-size: 12px;
        }

        /* 🌟 أزرار الإجراءات 🌟 */
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            text-decoration: none;
            cursor: pointer;
        }

        .action-btn-view {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .action-btn-view:hover {
            background: #2563eb;
            color: #fff;
            transform: translateY(-2px);
        }

        /* 🌟 زر التصدير 🌟 */
        .btn-export {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: #fff;
        }

        .btn-print {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(100, 116, 139, 0.3);
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            transform: translateY(-2px);
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

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #64748b;
            font-weight: 600;
            font-size: 13px;
        }

        .detail-value {
            color: #1e293b;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            font-size: 13px;
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

        body[data-layout-color="dark"] .user-name {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .table-audit thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-audit tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-audit tbody tr:hover td {
            background-color: #38414e !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select,
        body[data-layout-color="dark"] .filter-input {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .log-details-box {
            background: #3b4652;
            border-right-color: #3b82f6;
        }

        body[data-layout-color="dark"] .log-details-box.sensitive {
            background: #422006;
            border-right-color: #f59e0b;
        }

        /* 🌟 Print 🌟 */
        @media print {
            .no-print {
                display: none !important;
            }

            .card-premium {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            body {
                background: #fff !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 العنوان وأزرار العمليات 🌟 --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 no-print">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                    <i class='bx bx-shield-quarter text-danger fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">سجل الرقابة والأمان</h4>
                    <p class="text-muted mb-0 font-13">مراقبة وتتبع جميع الإجراءات على النظام</p>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn-print">
                    <i class='bx bx-printer me-1'></i> طباعة
                </button>
                @can('export_audit_logs')
                    <a href="{{ route('admin.audit-logs.export', request()->all()) }}" class="btn-export">
                        <i class='bx bx-export me-1'></i> تصدير CSV
                    </a>
                @endcan
            </div>
        </div>

        {{-- 🌟 كروت الإحصائيات 🌟 --}}
        <div class="row g-4 mb-4 no-print">
            <div class="col-md-3">
                <div class="stat-card bg-gradient-primary">
                    <div class="position-relative z-1">
                        <div class="stat-label">إجمالي السجلات</div>
                        <div class="stat-value">{{ number_format($totalLogs ?? $logs->total()) }}</div>
                    </div>
                    <i class='bx bx-list-ul stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-success">
                    <div class="position-relative z-1">
                        <div class="stat-label">سجلات اليوم</div>
                        <div class="stat-value">{{ $todayLogs ?? 0 }}</div>
                    </div>
                    <i class='bx bx-calendar-check stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-warning">
                    <div class="position-relative z-1">
                        <div class="stat-label">هذا الأسبوع</div>
                        <div class="stat-value">{{ $thisWeekLogs ?? 0 }}</div>
                    </div>
                    <i class='bx bx-calendar-week stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-danger">
                    <div class="position-relative z-1">
                        <div class="stat-label">أحداث حرجة</div>
                        <div class="stat-value">{{ $criticalEvents ?? 0 }}</div>
                    </div>
                    <i class='bx bx-error-circle stat-icon'></i>
                </div>
            </div>
        </div>

        {{-- 🌟 كارت الفلاتر 🌟 --}}
        <div class="filter-card mb-4 no-print">
            <div class="card-body p-4">
                <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="row align-items-end g-3">
                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-shape-square me-1'></i> نوع الحدث</label>
                        <select name="event" class="form-select filter-input">
                            <option value="">الكل</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>إضافة</option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>تعديل</option>
                            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>حذف</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-buildings me-1'></i> القسم</label>
                        <select name="log_name" class="form-select filter-input select2-init">
                            <option value="">الكل</option>
                            @foreach ($logNames ?? [] as $name)
                                <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>
                                    {{ ucfirst($name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-user me-1'></i> الموظف</label>
                        <select name="causer_id" class="form-select filter-input select2-init">
                            <option value="">الكل</option>
                            @foreach ($users ?? [] as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('causer_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-calendar me-1'></i> من تاريخ</label>
                        <input type="date" name="from_date" class="form-control filter-input"
                            value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-calendar me-1'></i> إلى تاريخ</label>
                        <input type="date" name="to_date" class="form-control filter-input"
                            value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="filter-label"><i class='bx bx-search me-1'></i> بحث</label>
                        <input type="text" name="search" class="form-control filter-input" placeholder="ابحث..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-dark flex-grow-1 fw-bold py-2 shadow-sm rounded-3">
                            <i class='bx bx-filter-alt me-1'></i> تطبيق الفلاتر
                        </button>
                        <a href="{{ route('admin.audit-logs.index') }}"
                            class="btn btn-light border fw-bold px-3 py-2 shadow-sm rounded-3" title="إعادة ضبط">
                            <i class='bx bx-refresh fs-5'></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 جدول السجلات 🌟 --}}
        <div class="card-premium">
            <div
                class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom no-print">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                        <i class='bx bx-history text-primary fs-4'></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">سجل الأحداث</h5>
                        <small class="text-muted">عرض {{ $logs->count() }} من {{ $logs->total() }} سجل</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-audit table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th style="width: 10%;">التوقيت</th>
                                <th class="text-start ps-4" style="width: 15%;">المسؤول</th>
                                <th style="width: 12%;">القسم</th>
                                <th style="width: 10%;">الإجراء</th>
                                <th class="text-start ps-4" style="width: 45%;">التفاصيل</th>
                                <th style="width: 8%;" class="no-print">عرض</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                @php
                                    $properties = $log->properties ?? [];
                                    $oldData = $properties['old'] ?? [];
                                    $newData = $properties['attributes'] ?? [];
                                    $hiddenFields = [
                                        'password',
                                        'remember_token',
                                        'password_confirmation',
                                        'guardian_signature',
                                        'signature_data',
                                    ];

                                    // تحديد شارة الحدث
                                    if ($log->event == 'created') {
                                        $eventClass = 'badge-created';
                                        $eventText = 'إضافة';
                                        $eventIcon = 'bx-plus-circle';
                                    } elseif ($log->event == 'updated') {
                                        $eventClass = 'badge-updated';
                                        $eventText = 'تعديل';
                                        $eventIcon = 'bx-edit';
                                    } elseif ($log->event == 'deleted') {
                                        $eventClass = 'badge-deleted';
                                        $eventText = 'حذف';
                                        $eventIcon = 'bx-trash';
                                    } elseif (str_contains($log->event, 'login')) {
                                        $eventClass = 'badge-login';
                                        $eventText = 'تسجيل دخول';
                                        $eventIcon = 'bx-log-in';
                                    } else {
                                        $eventClass = 'badge-updated';
                                        $eventText = $log->event;
                                        $eventIcon = 'bx-info-circle';
                                    }
                                @endphp
                                <tr>
                                    {{-- التوقيت --}}
                                    <td>
                                        <div class="timestamp">
                                            {{ $log->created_at->format('Y-m-d') }}
                                            <span class="timestamp-time">{{ $log->created_at->format('h:i A') }}</span>
                                        </div>
                                    </td>

                                    {{-- المسؤول --}}
                                    <td class="text-start ps-4">
                                        @if ($log->causer)
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    {{ mb_substr($log->causer->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="user-name">{{ $log->causer->name }}</div>
                                                    @if (isset($log->causer->email))
                                                        <div class="user-email">{{ $log->causer->email }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="user-info">
                                                <div class="user-avatar system">
                                                    <i class='bx bx-bot'></i>
                                                </div>
                                                <div>
                                                    <div class="user-name">النظام</div>
                                                    <div class="user-email">System</div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- القسم --}}
                                    <td>
                                        <span class="log-subject">{{ $log->log_name }}</span>
                                        <span class="log-id">ID: {{ $log->subject_id }}</span>
                                    </td>

                                    {{-- الإجراء --}}
                                    <td>
                                        <span class="badge-event {{ $eventClass }}">
                                            <i class='bx {{ $eventIcon }}'></i>
                                            {{ $eventText }}
                                        </span>
                                    </td>

                                    {{-- التفاصيل --}}
                                    <td class="text-start ps-4">
                                        @if ($log->event == 'updated' && !empty($oldData))
                                            <div style="max-height: 200px; overflow-y: auto;">
                                                @php $hasChanges = false; @endphp
                                                @foreach ($newData as $key => $value)
                                                    @if (array_key_exists($key, $oldData) && $oldData[$key] !== $value)
                                                        @php $hasChanges = true; @endphp
                                                        @if (in_array($key, $hiddenFields))
                                                            <div class="log-details-box sensitive">
                                                                <div class="diff-field">
                                                                    <i class='bx bx-lock-alt text-warning me-1'></i>
                                                                    {{ $key }}
                                                                </div>
                                                                <small class="text-muted fst-italic">
                                                                    تم تغيير بيانات حساسة (مخفية لأغراض أمنية)
                                                                </small>
                                                            </div>
                                                        @else
                                                            <div class="log-details-box">
                                                                <div class="diff-field">{{ $key }}</div>
                                                                <div>
                                                                    <span class="diff-old">
                                                                        {{ is_array($oldData[$key]) ? json_encode($oldData[$key], JSON_UNESCAPED_UNICODE) : $oldData[$key] ?? 'فارغ' }}
                                                                    </span>
                                                                    <span class="diff-arrow">←</span>
                                                                    <span class="diff-new">
                                                                        {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value ?? 'فارغ' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endforeach

                                                @if (!$hasChanges)
                                                    <small class="text-muted">
                                                        <i class='bx bx-info-circle me-1'></i>
                                                        لا توجد تغييرات ظاهرة
                                                    </small>
                                                @endif
                                            </div>
                                        @elseif($log->event == 'created')
                                            <small class="text-success fw-bold">
                                                <i class='bx bx-check-circle me-1'></i>
                                                تم إنشاء سجل جديد بنجاح
                                            </small>
                                        @elseif($log->event == 'deleted')
                                            <small class="text-danger fw-bold">
                                                <i class='bx bx-error-circle me-1'></i>
                                                تم حذف السجل من قاعدة البيانات
                                            </small>
                                        @else
                                            <small class="text-muted">
                                                <i class='bx bx-info-circle me-1'></i>
                                                {{ $log->description ?? 'بدون تفاصيل' }}
                                            </small>
                                        @endif
                                    </td>

                                    {{-- عرض التفاصيل --}}
                                    <td class="no-print">
                                        <button type="button" class="action-btn action-btn-view btn-view-log"
                                            data-log-id="{{ $log->id }}" title="عرض التفاصيل">
                                            <i class='bx bx-show'></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-5 text-center">
                                        <div class="py-4">
                                            <i class='bx bx-shield-quarter text-muted opacity-25'
                                                style="font-size: 80px;"></i>
                                            <h5 class="text-muted fw-bold mt-3">لا توجد سجلات مطابقة</h5>
                                            <p class="text-muted mb-0">جرب تغيير الفلاتر أو البحث</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- الترقيم --}}
            @if ($logs->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 no-print">
                    {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>

    {{-- 🔥 مودال تفاصيل السجل 🔥 --}}
    <div class="modal fade" id="logDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                    <h5 class="modal-title fw-bold">
                        <i class='bx bx-info-circle text-primary me-2'></i>
                        تفاصيل السجل #<span id="logId"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-row">
                        <span class="detail-label">التاريخ والوقت:</span>
                        <span class="detail-value" id="logCreatedAt"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">المسؤول:</span>
                        <span class="detail-value" id="logCauser"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">البريد الإلكتروني:</span>
                        <span class="detail-value" id="logEmail"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">القسم:</span>
                        <span class="detail-value" id="logName"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">الحدث:</span>
                        <span class="detail-value" id="logEvent"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">الوصف:</span>
                        <span class="detail-value" id="logDescription"></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">معرف السجل:</span>
                        <span class="detail-value" id="logSubjectId"></span>
                    </div>

                    <div class="mt-3">
                        <h6 class="fw-bold mb-2"><i class='bx bx-code-alt me-1'></i> البيانات (JSON)</h6>
                        <pre id="logProperties" class="bg-dark text-light p-3 rounded"
                            style="max-height: 300px; overflow-y: auto; font-size: 12px;"></pre>
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
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل Select2
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // 🔥 عرض تفاصيل السجل 🔥
            $('.btn-view-log').click(function() {
                let logId = $(this).data('log-id');

                $.get(`/admin/audit-logs/${logId}`, function(response) {
                    if (response.success) {
                        let data = response.data;
                        $('#logId').text(data.id);
                        $('#logCreatedAt').text(data.created_at);
                        $('#logCauser').text(data.causer_name);
                        $('#logEmail').text(data.causer_email);
                        $('#logName').text(data.log_name);
                        $('#logEvent').text(data.event);
                        $('#logDescription').text(data.description || '---');
                        $('#logSubjectId').text(data.subject_id || '---');
                        $('#logProperties').text(JSON.stringify(data.properties, null, 2));
                        $('#logDetailsModal').modal('show');
                    }
                }).fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'فشل في تحميل تفاصيل السجل'
                    });
                });
            });

            // 🌟 رسائل النظام 🌟
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

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: "{{ session('error') }}"
                });
            @endif
        });
    </script>
@endpush
