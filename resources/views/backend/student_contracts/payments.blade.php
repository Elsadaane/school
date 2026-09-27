@extends('backend.layouts.master')
@section('title', 'تفاصيل العقد والسداد')
@section('title_page', 'سداد أقساط العقد رقم: ' . $contract->contract_number)

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .glass-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease-in-out;
        }

        .glass-card:hover {
            transform: translateY(-2px);
        }

        .stat-box {
            padding: 24px;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-box::before {
            content: "";
            position: absolute;
            top: -20px;
            right: -20px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0.1;
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

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-amount {
            font-size: 26px;
            font-weight: 900;
            font-family: 'Courier New', Courier, monospace;
            margin-top: 5px;
            color: #1e293b;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
        }

        .info-value {
            color: #0f172a;
            font-size: 15px;
            font-weight: 800;
        }

        .progress-container {
            background-color: #e2e8f0;
            border-radius: 20px;
            height: 12px;
            width: 100%;
            overflow: hidden;
            margin: 20px 0 10px 0;
        }

        .progress-bar-custom {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg, #10b981, #34d399);
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modern-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 800;
            font-size: 13px;
            padding: 16px;
            border-bottom: 2px solid #e2e8f0;
        }

        .modern-table tbody td {
            padding: 14px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 14px;
        }

        .modern-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .modern-table tbody tr.row-overdue {
            background-color: #fef2f2;
        }

        .modern-table tbody tr.row-overdue:hover {
            background-color: #fee2e2;
        }

        .input-pay {
            width: 120px;
            text-align: center;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .input-pay:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            outline: none;
        }

        .select-pay {
            width: 140px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-weight: 700;
            color: #475569;
        }

        .btn-modern {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 800;
        }

        .receipt-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .receipt-card:hover {
            border-color: #94a3b8;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
        }

        /* 🆕 كارت المواصلات */
        .transport-info-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 16px;
            margin-top: 16px;
        }

        .transport-info-card .transport-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px dashed #bae6fd;
        }

        .transport-info-card .transport-item:last-child {
            border-bottom: none;
        }

        .transport-info-card .transport-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0284c7;
            font-size: 18px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Dark Mode Adjustments */
        body[data-layout-color="dark"] {
            background-color: #2b313a;
        }

        body[data-layout-color="dark"] .glass-card,
        body[data-layout-color="dark"] .receipt-card {
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

        body[data-layout-color="dark"] .stat-amount,
        body[data-layout-color="dark"] .info-value,
        body[data-layout-color="dark"] .text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .modern-table thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .modern-table tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .modern-table tbody tr:hover {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .modern-table tbody tr.row-overdue {
            background-color: rgba(239, 68, 68, 0.1);
        }

        body[data-layout-color="dark"] .input-pay,
        body[data-layout-color="dark"] .select-pay {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .progress-container {
            background-color: #424e5a;
        }

        body[data-layout-color="dark"] .transport-info-card {
            background: #1e293b;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .transport-info-card .transport-icon {
            background: #3b4652;
            color: #38bdf8;
        }

        body[data-layout-color="dark"] .transport-info-card .transport-item {
            border-color: #424e5a;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- زر العودة والطباعة --}}
        <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h4 class="mb-0 text-dark fw-bold"><i class="mdi mdi-cash-register text-primary me-2 fs-3 align-middle"></i>
                لوحة التحصيل المالي للطالب</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.student-contracts.show', $contract->id) }}" target="_blank"
                    class="btn btn-outline-dark fw-bold rounded-pill px-4 shadow-sm bg-white">
                    <i class="mdi mdi-printer me-1"></i> طباعة كشف حساب
                </a>
                <a href="{{ route('admin.student-contracts.index') }}"
                    class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                    <i class="mdi mdi-arrow-right me-1"></i> العودة للسجل
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success shadow-sm fw-bold rounded-3 border-0">
                <i class="mdi mdi-check-circle fs-5 align-middle me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm fw-bold rounded-3 border-0">
                <i class="mdi mdi-alert-circle fs-5 align-middle me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        @php
            $paidPercent = $contract->grand_total > 0 ? ($contract->paid_amount / $contract->grand_total) * 100 : 0;
            $remaining = $contract->grand_total - $contract->paid_amount;
        @endphp

        {{-- 🌟 الكروت العلوية (الملخص) 🌟 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-box stat-primary glass-card shadow-sm">
                    <div>
                        <span class="text-muted fw-bold d-block mb-1">إجمالي قيمة العقد</span>
                        <div class="stat-amount">{{ number_format($contract->grand_total, 3) }} <span
                                class="fs-6 text-muted font-sans">OMR</span></div>
                    </div>
                    <div class="icon-box"><i class="mdi mdi-file-document-outline"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box stat-success glass-card shadow-sm">
                    <div>
                        <span class="text-muted fw-bold d-block mb-1">المبالغ المحصلة</span>
                        <div class="stat-amount text-success">{{ number_format($contract->paid_amount, 3) }} <span
                                class="fs-6 opacity-75 font-sans">OMR</span></div>
                    </div>
                    <div class="icon-box"><i class="mdi mdi-cash-check"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box stat-danger glass-card shadow-sm">
                    <div>
                        <span class="text-muted fw-bold d-block mb-1">المديونية المتبقية</span>
                        <div class="stat-amount text-danger">{{ number_format($remaining, 3) }} <span
                                class="fs-6 opacity-75 font-sans">OMR</span></div>
                    </div>
                    <div class="icon-box"><i class="mdi mdi-alert-circle-outline"></i></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box stat-warning glass-card shadow-sm">
                    <div>
                        <span class="text-muted fw-bold d-block mb-1">المديونية المُرحلة</span>
                        <div class="stat-amount text-warning">{{ number_format($contract->previous_debt ?? 0, 3) }} <span
                                class="fs-6 opacity-75 font-sans">OMR</span></div>
                    </div>
                    <div class="icon-box"><i class="mdi mdi-history"></i></div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            {{-- 🌟 بطاقة بيانات الطالب 🌟 --}}
            <div class="col-xl-8">
                <div class="glass-card h-100 p-4 shadow-sm border-top border-primary border-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <h5 class="fw-bold text-dark mb-0"><i
                                class="mdi mdi-account-details text-primary me-2 fs-4 align-middle"></i> بيانات التعاقد
                            والطالب</h5>
                        <span
                            class="badge bg-light text-dark border px-3 py-2 font-monospace fs-6 shadow-sm">#{{ $contract->contract_number }}</span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <ul class="info-list">
                                <li>
                                    <span class="info-label"><i class="mdi mdi-account me-1"></i> اسم الطالب</span>
                                    <span class="info-value text-primary">{{ $contract->student->first_name ?? '---' }}
                                        {{ $contract->student->tribe ?? '' }}</span>
                                </li>
                                <li>
                                    {{-- ✅ تم الإصلاح: grade من الطالب وليس من العقد --}}
                                    <span class="info-label"><i class="mdi mdi-google-classroom me-1"></i> الصف
                                        الدراسي</span>
                                    <span class="info-value">{{ $contract->student->grade->name_ar ?? '---' }}</span>
                                </li>
                                <li>
                                    <span class="info-label"><i class="mdi mdi-calendar me-1"></i> تاريخ العقد</span>
                                    <span
                                        class="info-value font-monospace">{{ \Carbon\Carbon::parse($contract->contract_date)->format('Y-m-d') }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="info-list">
                                <li>
                                    <span class="info-label"><i class="mdi mdi-account-tie me-1"></i> ولي الأمر</span>
                                    <span class="info-value">{{ $contract->student->parent->name_ar ?? '---' }}</span>
                                </li>
                                <li>
                                    <span class="info-label"><i class="mdi mdi-store me-1"></i> الفرع المقيد به</span>
                                    <span class="info-value">{{ $contract->branch->name_ar ?? '---' }}</span>
                                </li>
                                <li>
                                    <span class="info-label"><i class="mdi mdi-calendar-clock me-1"></i> العام
                                        الأكاديمي</span>
                                    <span class="info-value">{{ $contract->academicYear->name ?? '---' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- 🆕 قسم المواصلات --}}
                    @if ($contract->transportRoute || $contract->morningBus || $contract->afternoonBus)
                        <div class="transport-info-card">
                            <h6 class="fw-bold text-info mb-3">
                                <i class="mdi mdi-bus me-2 fs-5 align-middle"></i> بيانات المواصلات المدرسية
                            </h6>

                            @if ($contract->transportRoute)
                                <div class="transport-item">
                                    <div class="transport-icon">
                                        <i class="mdi mdi-map-marker-path"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted font-12">خط السير</div>
                                        <div class="fw-bold text-dark">{{ $contract->transportRoute->name }}</div>
                                    </div>
                                </div>
                            @endif

                            @if ($contract->morningBus)
                                <div class="transport-item">
                                    <div class="transport-icon" style="color: #10b981;">
                                        <i class="mdi mdi-weather-sunny"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted font-12">باص الذهاب (الصباحي)</div>
                                        <div class="fw-bold text-dark">
                                            {{ $contract->morningBus->bus_number }}
                                            @if ($contract->morningBus->driver)
                                                <small class="text-muted ms-2">السائق:
                                                    {{ $contract->morningBus->driver->full_name_ar }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($contract->afternoonBus)
                                <div class="transport-item">
                                    <div class="transport-icon" style="color: #f59e0b;">
                                        <i class="mdi mdi-weather-sunset"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted font-12">باص العودة (المسائي)</div>
                                        <div class="fw-bold text-dark">
                                            {{ $contract->afternoonBus->bus_number }}
                                            @if ($contract->afternoonBus->driver)
                                                <small class="text-muted ms-2">السائق:
                                                    {{ $contract->afternoonBus->driver->full_name_ar }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🌟 بطاقة مؤشر السداد 🌟 --}}
            <div class="col-xl-4">
                <div class="glass-card h-100 p-4 d-flex flex-column justify-content-center text-center shadow-sm">
                    <h5 class="fw-bold text-dark mb-2">مؤشر التحصيل المالي</h5>
                    <div
                        class="display-3 fw-bolder {{ $paidPercent == 100 ? 'text-success' : 'text-primary' }} mb-1 font-monospace">
                        {{ round($paidPercent) }}%
                    </div>

                    @if ($paidPercent == 100)
                        <span class="text-success fw-bold"><i class="mdi mdi-check-decagram me-1 fs-5 align-middle"></i>
                            تم
                            سداد العقد بالكامل</span>
                    @else
                        <span class="text-muted fw-bold font-13">من إجمالي المستحقات المطلوبة</span>
                    @endif

                    <div class="progress-container mx-auto shadow-sm">
                        <div class="progress-bar-custom {{ $paidPercent == 100 ? 'bg-success' : '' }}"
                            style="width: {{ $paidPercent }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 جدول الأقساط والسداد 🌟 --}}
        <div class="glass-card overflow-hidden shadow-sm">
            <div class="bg-light border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i
                        class="mdi mdi-format-list-checks text-success me-2 fs-4 align-middle"></i> الجدولة الزمنية
                    وإجراءات
                    السداد</h5>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="modern-table text-center align-middle mb-0">
                    <thead class="sticky-top">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 15%">تاريخ الاستحقاق</th>
                            <th style="width: 12%">القيمة المطلوبة</th>
                            <th style="width: 12%">المسدد الفعلي</th>
                            <th style="width: 12%">المتبقي للسداد</th>
                            <th style="width: 20%" class="text-start ps-3">سجل إيصالات الدفع</th>
                            <th style="width: 10%">حالة القسط</th>
                            <th style="width: 14%">إجراء التحصيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contract->installments ?? [] as $idx => $inst)
                            @php
                                $instRemaining = $inst->amount - $inst->paid_amount;
                                $isOverdue =
                                    $inst->status != 'paid' && \Carbon\Carbon::parse($inst->due_date)->isPast();
                            @endphp
                            <tr class="{{ $isOverdue ? 'row-overdue' : '' }}">
                                <td class="fw-bold text-muted">{{ $idx + 1 }}</td>
                                <td>
                                    <span class="font-monospace fw-bold {{ $isOverdue ? 'text-danger' : 'text-dark' }}">
                                        {{ \Carbon\Carbon::parse($inst->due_date)->format('Y-m-d') }}
                                    </span>
                                    @if ($isOverdue)
                                        <br><span class="badge bg-danger mt-1 font-11 shadow-sm px-2">استحقاق متأخر</span>
                                    @endif
                                </td>
                                <td class="font-monospace fw-bold text-dark fs-6">{{ number_format($inst->amount, 3) }}
                                </td>
                                <td class="font-monospace fw-bold text-success fs-6">
                                    {{ number_format($inst->paid_amount, 3) }}</td>
                                <td class="font-monospace fw-bold text-danger fs-6">{{ number_format($instRemaining, 3) }}
                                </td>

                                {{-- 🌟 سجل الإيصالات 🌟 --}}
                                <td class="text-start ps-2 pe-2">
                                    @if ($inst->payments && $inst->payments->count() > 0)
                                        <div class="d-flex flex-column gap-1">
                                            @foreach ($inst->payments as $payment)
                                                <div class="receipt-card shadow-sm">
                                                    <div>
                                                        <i class="mdi mdi-receipt-text text-primary me-1"></i>
                                                        <span
                                                            class="font-monospace fw-bold text-dark font-12">{{ $payment->receipt_number }}</span>
                                                        <span
                                                            class="d-block text-muted font-11 mt-1">{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</span>
                                                    </div>
                                                    <div class="text-end d-flex align-items-center gap-2">
                                                        <span
                                                            class="text-success fw-bolder font-monospace font-14">{{ number_format($payment->amount, 3) }}</span>
                                                        <a href="{{ route('admin.student-contracts.receipt', $payment->id) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-light border p-1 rounded-circle"
                                                            title="طباعة الإيصال">
                                                            <i class="mdi mdi-printer text-dark font-16"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted font-12 fw-bold opacity-50"><i class="mdi mdi-minus"></i>
                                            لا توجد دفعات</span>
                                    @endif
                                </td>

                                {{-- 🌟 الحالة 🌟 --}}
                                <td>
                                    @if ($inst->status === 'paid' || $instRemaining <= 0)
                                        <span
                                            class="badge-status bg-success bg-opacity-10 text-success border border-success border-opacity-25 shadow-sm"><i
                                                class="mdi mdi-check-all me-1"></i> مكتمل</span>
                                    @elseif ($inst->paid_amount > 0)
                                        <span
                                            class="badge-status bg-warning bg-opacity-10 text-warning text-darken border border-warning border-opacity-25 shadow-sm"><i
                                                class="mdi mdi-progress-clock me-1"></i> سداد جزئي</span>
                                    @else
                                        <span
                                            class="badge-status bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 shadow-sm"><i
                                                class="mdi mdi-clock-outline me-1"></i> مستحق</span>
                                    @endif
                                </td>

                                {{-- 🌟 إجراء السداد 🌟 --}}
                                <td class="pe-3">
                                    @if ($instRemaining > 0)
                                        @can('edit_student_financials')
                                            <form action="{{ route('admin.student-contracts.pay-installment', $inst->id) }}"
                                                method="POST" class="payment-form">
                                                @csrf
                                                <div class="d-flex flex-column gap-2 align-items-center">
                                                    <div class="input-group input-group-sm shadow-sm">
                                                        <input type="number" name="amount_paid" step="0.001"
                                                            max="{{ $instRemaining }}" value="{{ $instRemaining }}"
                                                            class="form-control input-pay text-success pay-amount-input"
                                                            required placeholder="المبلغ">
                                                    </div>
                                                    <select name="financial_account_id"
                                                        class="form-select form-select-sm select-pay shadow-sm" required>
                                                        <option value="" disabled selected>-- اختر الخزينة --</option>
                                                        @foreach ($financialAccounts as $account)
                                                            <option value="{{ $account->id }}">{{ $account->name_ar }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit"
                                                        class="btn btn-success btn-sm btn-modern w-100 btn-pay shadow-sm">
                                                        <i class="mdi mdi-cash-plus me-1"></i> سداد القسط
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <button
                                                class="btn btn-light btn-sm text-muted fw-bold rounded-pill px-4 border w-100 shadow-sm"
                                                disabled title="لا تملك صلاحية التحصيل">
                                                <i class="mdi mdi-lock-outline me-1"></i> محمي
                                            </button>
                                        @endcan
                                    @else
                                        <button
                                            class="btn btn-light btn-sm text-success fw-bold rounded-pill px-4 border border-success border-opacity-25 w-100 shadow-sm bg-success bg-opacity-10"
                                            disabled>
                                            <i class="mdi mdi-check-circle me-1"></i> خالص
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-muted fw-bold fs-5 bg-white">
                                    <i class="mdi mdi-database-off-outline fs-1 d-block mb-2 opacity-50 text-danger"></i>
                                    لا توجد أي أقساط مالية مسجلة لهذا العقد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.pay-amount-input').on('input', function() {
                let max = parseFloat($(this).attr('max'));
                let val = parseFloat($(this).val());
                if (val < 0) {
                    $(this).val(0);
                }
                if (val > max) {
                    $(this).val(max);
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'لا يمكن سداد مبلغ أكبر من المستحق!',
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });

            $('.payment-form').on('submit', function(e) {
                e.preventDefault();
                let form = this;
                let amount = $(this).find('input[name="amount_paid"]').val();
                let accountName = $(this).find('select[name="financial_account_id"] option:selected')
                    .text();

                if (!amount || amount <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'يرجى إدخال مبلغ صحيح أكبر من الصفر.',
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                Swal.fire({
                    title: 'تأكيد السداد والتحصيل',
                    html: `سيتم تحصيل مبلغ <b class="text-success font-monospace fs-5">${amount} OMR</b> <br>وإيداعه في خزينة: <strong class="text-primary">${accountName}</strong>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="mdi mdi-check-bold me-1"></i> نعم، تأكيد الدفع',
                    cancelButtonText: 'تراجع',
                    customClass: {
                        confirmButton: 'px-4 py-2 rounded-pill fw-bold shadow-sm',
                        cancelButton: 'px-4 py-2 rounded-pill fw-bold text-white shadow-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(form).find('.btn-pay').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin fs-5 align-middle"></i> جاري التحصيل...'
                        );

                        Swal.fire({
                            title: 'جاري تسجيل الدفعة وإصدار الإيصال...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
