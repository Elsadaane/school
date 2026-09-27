@extends('backend.layouts.master')
@section('title', 'سجل حضور وانصراف الموظفين')
@section('title_page', 'إدارة الحضور والغياب')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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
            font-size: 28px;
            font-weight: 900;
            font-family: 'Courier New', Courier, monospace;
            margin-top: 5px;
            color: #1e293b;
        }

        .stat-label {
            color: #64748b;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* 🌟 تصميم الجدول 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
        }

        .table-attendance {
            margin-bottom: 0;
        }

        .table-attendance thead th {
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

        .table-attendance tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
            transition: background-color 0.2s ease;
        }

        .table-attendance tbody tr:hover td {
            background-color: #fbfccb !important;
        }

        .table-attendance tbody tr.row-absent {
            background-color: #fef2f2;
        }

        .table-attendance tbody tr.row-absent:hover {
            background-color: #fee2e2 !important;
        }

        .table-attendance tbody tr.row-late {
            background-color: #fffbeb;
        }

        .table-attendance tbody tr.row-late:hover {
            background-color: #fef3c7 !important;
        }

        .table-attendance tbody tr.row-leave {
            background-color: #ecfeff;
        }

        .table-attendance tbody tr.row-leave:hover {
            background-color: #cffafe !important;
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

        .badge-present {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .badge-late {
            background: #fef9c3;
            color: #9a3412;
            border: 1px solid #fde047;
        }

        .badge-absent {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .badge-leave {
            background: #cffafe;
            color: #155e75;
            border: 1px solid #67e8f9;
        }

        /* 🌟 عرض الوقت 🌟 */
        .time-display {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 800;
            font-size: 15px;
        }

        .time-checkin {
            color: #10b981;
        }

        .time-checkout {
            color: #ef4444;
        }

        .time-empty {
            color: #94a3b8;
            font-style: italic;
        }

        /* 🌟 تنبيه التأخير 🌟 */
        .delay-badge {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 13px;
            border: 1px solid #fca5a5;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        .on-time-badge {
            background: linear-gradient(135deg, #f0fdf4 0%, #d1fae5 100%);
            color: #166534;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 13px;
            border: 1px solid #86efac;
        }

        /* 🌟 أزرار الإجراءات 🌟 */
        .action-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
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

        .btn-import {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 16px 24px;
            font-weight: 800;
            font-size: 15px;
            box-shadow: 0 4px 15px rgba(30, 41, 59, 0.3);
            transition: all 0.3s ease;
        }

        .btn-import:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(30, 41, 59, 0.4);
            color: #fff;
        }

        .btn-close-day {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
            transition: all 0.3s ease;
        }

        .btn-close-day:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
            color: #fff;
        }

        /* 🌟 معلومات الموظف 🌟 */
        .employee-name {
            font-weight: 800;
            color: #1e293b;
            font-size: 15px;
            margin-bottom: 3px;
        }

        .employee-dept {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
        }

        .shift-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #e2e8f0;
        }

        /* 🌟 تنسيق المودال 🌟 */
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

        /* 🌟 دعم الوضع الليلي 🌟 */
        body[data-layout-color="dark"] {
            background-color: #2b313a;
        }

        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .stat-card {
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

        body[data-layout-color="dark"] .table-attendance thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-attendance tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-attendance tbody tr:hover td {
            background-color: #38414e !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .shift-badge {
            background: #424e5a;
            color: #ced4da;
            border-color: #64748b;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 كروت الإحصائيات المتقدمة 🌟 --}}
        <div class="row g-4 mb-4">
            {{-- إجمالي الحضور --}}
            <div class="col-md-3">
                <div class="stat-card stat-success p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي الحضور</div>
                            <div class="stat-value text-success">
                                {{ $attendances->where('status', '!=', 'absent')->where('status', '!=', 'on_leave')->count() }}
                            </div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-user-check'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- حالات التأخير --}}
            <div class="col-md-3">
                <div class="stat-card stat-warning p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">حالات التأخير</div>
                            <div class="stat-value text-warning">{{ $attendances->where('status', 'late')->count() }}</div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-time-five'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- إجمالي دقائق التأخير --}}
            <div class="col-md-3">
                <div class="stat-card stat-danger p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">إجمالي دقائق التأخير</div>
                            <div class="stat-value text-danger">{{ $attendances->sum('delay_minutes') }} <small
                                    class="fs-6 opacity-75">دقيقة</small></div>
                        </div>
                        <div class="icon-box">
                            <i class='bx bx-alarm-exclamation'></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- زر رفع ملف البصمة --}}
            <div class="col-md-3">
                @can('import_attendance')
                    <button
                        class="btn btn-import w-100 h-100 d-flex flex-column align-items-center justify-content-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class='bx bx-cloud-upload fs-1'></i>
                        <span>رفع ملف البصمة</span>
                    </button>
                @else
                    <div class="stat-card stat-info p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">في إجازة</div>
                                <div class="stat-value text-info">{{ $attendances->where('status', 'on_leave')->count() }}</div>
                            </div>
                            <div class="icon-box">
                                <i class='bx bx-calendar-x'></i>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        {{-- 🌟 جدول الحضور الرئيسي 🌟 --}}
        <div class="card-premium">
            <div
                class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                        <i class='bx bx-calendar-check text-primary fs-4'></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            سجل حضور يوم:
                        </h5>
                        <small
                            class="text-muted font-monospace fw-bold">{{ \Carbon\Carbon::parse($date)->format('l, Y-m-d') }}</small>
                    </div>
                </div>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    {{-- اختيار التاريخ --}}
                    <input type="date" class="form-control fw-bold shadow-sm" value="{{ $date }}"
                        onchange="location.href='?date='+this.value" style="width: 180px;">

                    {{-- زر إقفال اليوم --}}
                    @can('edit_attendance')
                        <form action="{{ route('admin.hr.attendance.mark_absentees') }}" method="POST" class="m-0 p-0"
                            id="markAbsentForm">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <button type="button" class="btn btn-close-day btn-mark-absent">
                                <i class='bx bx-user-x me-1'></i> إقفال ورصد الغياب
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-attendance table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="text-start ps-4" style="width: 25%;">الموظف</th>
                                <th style="width: 12%;">الوردية</th>
                                <th style="width: 15%;">وقت الحضور</th>
                                <th style="width: 15%;">وقت الانصراف</th>
                                <th style="width: 13%;">التأخير</th>
                                <th style="width: 12%;">الحالة</th>
                                @can('edit_attendance')
                                    <th style="width: 8%;">الإجراء</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                                @php
                                    $rowClass = '';
                                    if ($att->status === 'absent') {
                                        $rowClass = 'row-absent';
                                    } elseif ($att->status === 'late') {
                                        $rowClass = 'row-late';
                                    } elseif ($att->status === 'on_leave') {
                                        $rowClass = 'row-leave';
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    {{-- الموظف --}}
                                    <td class="text-start ps-4">
                                        <div class="employee-name">
                                            <i class='bx bx-user-circle text-primary me-1'></i>
                                            {{ app()->getLocale() == 'ar' ? $att->employee->full_name_ar : $att->employee->full_name_en ?? $att->employee->full_name_ar }}
                                        </div>
                                        <div class="employee-dept">
                                            <i class='bx bx-building-house me-1'></i>
                                            {{ app()->getLocale() == 'ar' ? $att->employee->department->name_ar ?? 'بدون قسم' : $att->employee->department->name_en ?? ($att->employee->department->name_ar ?? 'بدون قسم') }}
                                        </div>
                                    </td>

                                    {{-- الوردية --}}
                                    <td>
                                        <span class="shift-badge">
                                            <i class='bx bx-time me-1'></i>
                                            {{ app()->getLocale() == 'ar' ? $att->employee->shift->name_ar ?? '-' : $att->employee->shift->name_en ?? ($att->employee->shift->name_ar ?? '-') }}
                                        </span>
                                    </td>

                                    {{-- وقت الحضور --}}
                                    <td>
                                        @if ($att->check_in)
                                            <span class="time-display time-checkin">
                                                <i class='bx bx-log-in me-1'></i>
                                                {{ \Carbon\Carbon::parse($att->check_in)->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="time-empty">
                                                <i class='bx bx-minus-circle'></i> ---
                                            </span>
                                        @endif
                                    </td>

                                    {{-- وقت الانصراف --}}
                                    <td>
                                        @if ($att->check_out)
                                            <span class="time-display time-checkout">
                                                <i class='bx bx-log-out me-1'></i>
                                                {{ \Carbon\Carbon::parse($att->check_out)->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border">
                                                <i class='bx bx-hourglass'></i>
                                                لم ينصرف
                                            </span>
                                        @endif
                                    </td>

                                    {{-- التأخير --}}
                                    <td>
                                        @if ($att->delay_minutes > 0)
                                            <span class="delay-badge">
                                                <i class='bx bx-error-circle'></i>
                                                {{ $att->delay_minutes }} دقيقة
                                            </span>
                                        @else
                                            <span class="on-time-badge">
                                                <i class='bx bx-check-circle'></i>
                                                في الموعد
                                            </span>
                                        @endif
                                    </td>

                                    {{-- الحالة --}}
                                    <td>
                                        @if ($att->status == 'late')
                                            <span class="badge-status badge-late">
                                                <i class='bx bx-time-five'></i>
                                                تأخير
                                            </span>
                                        @elseif ($att->status == 'absent')
                                            <span class="badge-status badge-absent">
                                                <i class='bx bx-user-x'></i>
                                                غياب
                                            </span>
                                        @elseif ($att->status == 'on_leave')
                                            <span class="badge-status badge-leave">
                                                <i class='bx bx-calendar-x'></i> إجازة
                                            </span>
                                        @else
                                            <span class="badge-status badge-present">
                                                <i class='bx bx-user-check'></i>
                                                حضور
                                            </span>
                                        @endif
                                    </td>

                                    {{-- الإجراءات --}}
                                    @can('edit_attendance')
                                        <td>
                                            <button type="button" class="action-btn action-btn-edit btn-edit-attendance"
                                                data-id="{{ $att->id }}"
                                                data-name="{{ app()->getLocale() == 'ar' ? $att->employee->full_name_ar : $att->employee->full_name_en ?? $att->employee->full_name_ar }}"
                                                data-checkin="{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '' }}"
                                                data-checkout="{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '' }}"
                                                data-delay="{{ $att->delay_minutes }}" data-status="{{ $att->status }}"
                                                data-bs-toggle="modal" data-bs-target="#editAttendanceModal"
                                                title="تعديل السجل">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('edit_attendance') ? 7 : 6 }}"
                                        class="p-5 text-center">
                                        <div class="py-4">
                                            <i class='bx bx-calendar-x text-muted opacity-25'
                                                style="font-size: 80px;"></i>
                                            <h5 class="text-muted fw-bold mt-3">
                                                لا توجد بيانات مسجلة في هذا اليوم</h5>
                                            <p class="text-muted mb-0">قم برفع ملف البصمة أو الانتظار لتسجيل الحضور
                                                تلقائياً</p>
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

    {{-- 🔥 مودال الاستيراد 🌟 --}}
    @can('import_attendance')
        <div class="modal fade" id="importModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-cloud-upload text-success me-2'></i>
                            استيراد سجل البصمة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.attendance.import') }}" method="POST" enctype="multipart/form-data"
                        id="importForm" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body">
                            @if ($errors->any())
                                <div class="alert alert-danger p-3 rounded-3">
                                    <ul class="mb-0 small">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="alert alert-info small fw-bold border-0 rounded-3 mb-4">
                                <i class='bx bx-info-circle me-1'></i>
                                تأكد من مطابقة كود الموظف مع ملف الجهاز. الصيغة المطلوبة: CSV أو TXT
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    اختر الملف (CSV أو TXT)
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="file" name="attendance_file" id="attendance_file" class="form-control"
                                    accept=".csv, .txt" required>
                                <small class="text-muted mt-2 d-block">
                                    <i class='bx bx-file me-1'></i> الحد الأقصى لحجم الملف: 5 ميجابايت
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إغلاق
                            </button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 btn-submit">
                                <i class='bx bx-upload me-1'></i>
                                بدء التحليل والاستيراد
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- 🔥 مودال تعديل الحضور يدوياً 🌟 --}}
    @can('edit_attendance')
        <div class="modal fade" id="editAttendanceModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class='bx bx-edit text-primary me-2'></i>
                            تعديل السجل لـ
                            <span id="edit_emp_name" class="text-danger"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editAttendanceForm" class="prevent-double-submit">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class='bx bx-log-in text-success me-1'></i>
                                        وقت الحضور
                                    </label>
                                    <input type="time" name="check_in" id="edit_check_in" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class='bx bx-log-out text-danger me-1'></i>
                                        وقت الانصراف
                                    </label>
                                    <input type="time" name="check_out" id="edit_check_out" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class='bx bx-time-five text-warning me-1'></i>
                                        دقائق التأخير
                                    </label>
                                    <input type="number" name="delay_minutes" id="edit_delay_minutes"
                                        class="form-control font-monospace text-center" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class='bx bx-flag text-primary me-1'></i>
                                        الحالة
                                    </label>
                                    <select name="status" id="edit_status" class="form-select">
                                        <option value="present">حضور</option>
                                        <option value="late">تأخير</option>
                                        <option value="absent">غياب</option>
                                        <option value="on_leave">إجازة معتمدة</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إغلاق
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 btn-submit">
                                <i class='bx bx-check me-1'></i>
                                حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // حماية النماذج من الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function(e) {
                if ($(this).attr('id') === 'importForm') {
                    if (!$('#attendance_file').val()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'يرجى اختيار ملف قبل الاستيراد!',
                            confirmButtonColor: '#3b82f6'
                        });
                        return false;
                    }
                }
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري المعالجة...');
            });

            // تمرير البيانات لمودال التعديل
            $('.btn-edit-attendance').click(function() {
                let id = $(this).data('id');
                $('#edit_emp_name').text($(this).data('name'));
                $('#edit_check_in').val($(this).data('checkin'));
                $('#edit_check_out').val($(this).data('checkout'));
                $('#edit_delay_minutes').val($(this).data('delay'));
                $('#edit_status').val($(this).data('status'));

                let url = "{{ route('admin.hr.attendance.update', ':id') }}".replace(':id', id);
                $('#editAttendanceForm').attr('action', url);
            });

            // سكريبت إقفال اليوم ورصد الغياب
            $('.btn-mark-absent').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: "هل أنت متأكد من إقفال اليوم؟",
                    html: "سيقوم النظام بتسجيل <strong class='text-danger'>غياب</strong> لجميع الموظفين الذين لم يسجلوا بصمة حضور اليوم.<br><br>سيتم أخذ هذا اليوم في الاعتبار عند رصد مسير الرواتب.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: "<i class='bx bx-check me-1'></i> نعم، إقفال ورصد الغياب",
                    cancelButtonText: "إلغاء",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'جاري المعالجة...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        form.submit();
                    }
                });
            });

            // إظهار رسائل النظام
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: "نجاح",
                    text: "{{ session('success') }}",
                    timer: 4000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif
            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: "تحديث",
                    text: "{{ session('info') }}",
                    timer: 4000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: "خطأ",
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            @endif
        });
    </script>
@endpush
