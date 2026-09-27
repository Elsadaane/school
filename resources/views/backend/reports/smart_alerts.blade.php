@extends('backend.layouts.master')
@section('title', 'رادار التنبيهات الذكي')
@section('title_page', 'الإدارة العليا - رادار التنبيهات والإنذارات المبكرة')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #fff;
            height: 100%;
        }

        .alert-header {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 12px 12px 0 0;
        }

        .alert-scroll-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .alert-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .alert-list li {
            padding: 15px 20px;
            border-bottom: 1px dashed #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .alert-list li:hover {
            background-color: #f8fafc;
        }

        .alert-list li:last-child {
            border-bottom: none;
        }

        .alert-info {
            flex-grow: 1;
            margin-right: 15px;
        }

        .alert-title {
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 3px;
            display: block;
        }

        .alert-meta {
            font-size: 12px;
            color: #64748b;
            display: block;
        }

        .alert-action {
            flex-shrink: 0;
            text-align: left;
        }

        .amount-text {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 15px;
        }

        .pulse-danger {
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-red {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        .all-clear-state {
            padding: 50px 20px;
            text-align: center;
        }

        .all-clear-state i {
            font-size: 50px;
            color: #10b981;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        .all-clear-state h6 {
            color: #059669;
            font-weight: 800;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .alert-header {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .alert-list li {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .alert-list li:hover {
            background-color: #3b4652;
        }

        body[data-layout-color="dark"] .alert-title {
            color: #f8fafc;
        }

        body[data-layout-color="dark"] .all-clear-state h6 {
            color: #10b981;
        }

        /* 🖨️ إعدادات الطباعة 🖨️ */
        @media print {
            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print,
            .left-side-menu,
            .navbar-custom,
            .page-title-box,
            .footer {
                display: none !important;
            }

            .card-premium {
                box-shadow: none !important;
                border: 1px solid #ddd;
                break-inside: avoid;
            }

            .alert-scroll-container {
                max-height: none !important;
                overflow-y: visible !important;
            }
        }
    </style>
@endpush

@section('content')
    @php
        // ✅ تأمين العدادات بـ ?? 0 لمنع أخطاء Null
        $dueInvoicesCount = isset($dueInvoices) ? $dueInvoices->count() : 0;
        $expiringDocsCount = isset($expiringDocs) ? $expiringDocs->count() : 0;
        $expiringContractsCount = isset($expiringContracts) ? $expiringContracts->count() : 0;
        $lowStockCount = isset($lowStock) ? $lowStock->count() : 0;
        $dueInstallmentsCount = isset($dueInstallments) ? $dueInstallments->count() : 0;
    @endphp

    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 d-print-none">
            <div>
                <h4 class="fw-bolder text-dark mb-1">
                    <i class="mdi mdi-radar text-danger me-2 fs-3 align-middle"></i> رادار التنبيهات والمخاطر المجمعة
                </h4>
                <p class="text-muted font-13 mb-0">تحديث فوري للمخاطر والمستحقات والمهام العالقة (بيانات اليوم: <span
                        class="fw-bold font-monospace">{{ $today->format('Y-m-d') }}</span>)</p>
            </div>
            <button class="btn btn-dark fw-bold shadow-sm rounded-pill px-4" onclick="window.print()">
                <i class="mdi mdi-printer me-1"></i> طباعة التقرير التحذيري
            </button>
        </div>

        <div class="row g-4">

            {{-- 🔴 1. متأخرات أقساط الطلاب (مالية) --}}
            <div class="col-xl-6">
                <div class="card card-premium border-top border-danger border-4 shadow-sm">
                    <div class="alert-header bg-danger bg-opacity-10">
                        <h6 class="fw-bold text-danger mb-0">
                            <i class="mdi mdi-account-alert-outline fs-5 align-middle me-1"></i> أقساط طلاب متأخرة الدفع
                        </h6>
                        <span class="badge bg-danger rounded-pill fs-6 shadow-sm">{{ $dueInstallmentsCount }}</span>
                    </div>

                    @if ($dueInstallmentsCount > 0)
                        <div class="alert-scroll-container">
                            <ul class="alert-list">
                                @foreach ($dueInstallments as $inst)
                                    @php
                                        $remaining = $inst->amount - $inst->paid_amount;
                                        $daysLate = \Carbon\Carbon::parse($inst->due_date)->diffInDays($today, false);
                                        // ✅ التصحيح: استخدام studentContract بدلاً من contract
                                        $student = $inst->studentContract->student ?? null;
                                        $contractNo = $inst->studentContract->contract_number ?? '';
                                    @endphp
                                    @if ($student)
                                        <li>
                                            <div class="alert-info">
                                                <span class="alert-title">{{ $student->first_name }}
                                                    {{ $student->tribe }}</span>
                                                <span class="alert-meta">
                                                    <i class="mdi mdi-file-document-outline"></i> عقد #{{ $contractNo }} |
                                                    تاريخ الاستحقاق: <strong
                                                        class="text-danger font-monospace">{{ \Carbon\Carbon::parse($inst->due_date)->format('Y-m-d') }}</strong>
                                                </span>
                                            </div>
                                            <div class="alert-action text-end">
                                                <div class="amount-text text-danger">{{ number_format($remaining, 3) }} OMR
                                                </div>
                                                @if ($daysLate > 0)
                                                    <span
                                                        class="badge bg-danger bg-opacity-25 text-danger font-11 rounded-pill mt-1">متأخر
                                                        {{ $daysLate }} يوم</span>
                                                @else
                                                    <span
                                                        class="badge bg-warning bg-opacity-25 text-warning font-11 rounded-pill mt-1">يستحق
                                                        اليوم</span>
                                                @endif
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="all-clear-state">
                            <i class="mdi mdi-shield-check-outline"></i>
                            <h6>الوضع آمن مالياً</h6>
                            <p class="text-muted font-12 mb-0">لا توجد أقساط طلاب متأخرة حتى اليوم.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🟠 2. فواتير موردين غير مدفوعة (التزامات) --}}
            <div class="col-xl-6">
                <div class="card card-premium border-top border-warning border-4 shadow-sm">
                    <div class="alert-header bg-warning bg-opacity-10">
                        <h6 class="fw-bold text-warning mb-0" style="color: #d97706 !important;">
                            <i class="mdi mdi-receipt-text-clock-outline fs-5 align-middle me-1"></i> فواتير موردين معلقة
                        </h6>
                        <span class="badge bg-warning text-dark rounded-pill fs-6 shadow-sm">{{ $dueInvoicesCount }}</span>
                    </div>

                    @if ($dueInvoicesCount > 0)
                        <div class="alert-scroll-container">
                            <ul class="alert-list">
                                @foreach ($dueInvoices as $invoice)
                                    @php
                                        $remainingInvoice = $invoice->grand_total - $invoice->paid_amount;
                                    @endphp
                                    <li>
                                        <div class="alert-info">
                                            {{-- ✅ التصحيح: استخدام company_name بدلاً من name --}}
                                            <span
                                                class="alert-title">{{ $invoice->supplier->company_name ?? 'مورد غير معروف' }}</span>
                                            <span class="alert-meta">
                                                <i class="mdi mdi-tag-outline"></i> فاتورة #{{ $invoice->invoice_number }}
                                                | تاريخ: <strong
                                                    class="text-dark font-monospace">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</strong>
                                            </span>
                                        </div>
                                        <div class="alert-action text-end">
                                            <div class="amount-text text-warning" style="color: #d97706 !important;">
                                                {{ number_format($remainingInvoice, 3) }} OMR</div>
                                            <a href="{{ route('admin.purchase-invoices.show', $invoice->id ?? 0) }}"
                                                class="btn btn-sm btn-light border rounded-pill mt-1 font-11 fw-bold text-dark no-print shadow-sm">عرض
                                                الفاتورة</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="all-clear-state">
                            <i class="mdi mdi-shield-check-outline text-success"></i>
                            <h6>لا توجد التزامات معلقة</h6>
                            <p class="text-muted font-12 mb-0">تم تسديد جميع فواتير الموردين بنجاح.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🟣 3. وثائق الموظفين والإقامات (HR) --}}
            <div class="col-xl-4">
                <div class="card card-premium border-top border-primary border-4 shadow-sm"
                    style="border-top-color: #8b5cf6 !important;">
                    <div class="alert-header" style="background-color: #faf5ff;">
                        <h6 class="fw-bold mb-0" style="color: #7c3aed;">
                            <i class="mdi mdi-passport fs-5 align-middle me-1"></i> إقامات ووثائق ستنتهي قريباً
                        </h6>
                        <span class="badge rounded-pill fs-6 shadow-sm"
                            style="background-color: #7c3aed;">{{ $expiringDocsCount }}</span>
                    </div>

                    @if ($expiringDocsCount > 0)
                        <div class="alert-scroll-container">
                            <ul class="alert-list">
                                @foreach ($expiringDocs as $doc)
                                    @php
                                        $daysLeft = $today->diffInDays(\Carbon\Carbon::parse($doc->expiry_date), false);
                                        $isExpired = $daysLeft < 0;
                                    @endphp
                                    <li>
                                        <div class="alert-info">
                                            {{-- ✅ التصحيح: استخدام full_name_ar بدلاً من name_ar --}}
                                            <span class="alert-title">{{ $doc->employee->full_name_ar ?? '---' }}</span>
                                            <span class="alert-meta">{{ $doc->document_type ?? 'وثيقة' }} | انتهاء: <span
                                                    class="font-monospace text-dark fw-bold">{{ \Carbon\Carbon::parse($doc->expiry_date)->format('Y-m-d') }}</span></span>
                                        </div>
                                        <div class="alert-action text-end">
                                            @if ($isExpired)
                                                <span class="badge bg-danger pulse-danger">منتهية منذ {{ abs($daysLeft) }}
                                                    يوم</span>
                                            @else
                                                <span class="badge bg-warning text-dark">باقي {{ $daysLeft }}
                                                    يوم</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="all-clear-state">
                            <i class="mdi mdi-folder-check-outline text-success"></i>
                            <h6 style="color: #059669;">ملفات الموظفين سليمة</h6>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🔵 4. عقود العمل (HR) --}}
            <div class="col-xl-4">
                <div class="card card-premium border-top border-info border-4 shadow-sm">
                    <div class="alert-header bg-info bg-opacity-10">
                        <h6 class="fw-bold text-info mb-0">
                            <i class="mdi mdi-file-sign fs-5 align-middle me-1"></i> عقود عمل شارفت على الانتهاء
                        </h6>
                        <span class="badge bg-info rounded-pill fs-6 shadow-sm">{{ $expiringContractsCount }}</span>
                    </div>

                    @if ($expiringContractsCount > 0)
                        <div class="alert-scroll-container">
                            <ul class="alert-list">
                                @foreach ($expiringContracts as $contract)
                                    @php
                                        $daysLeft = $today->diffInDays(
                                            \Carbon\Carbon::parse($contract->end_date),
                                            false,
                                        );
                                    @endphp
                                    <li>
                                        <div class="alert-info">
                                            {{-- ✅ التصحيح: استخدام full_name_ar بدلاً من name_ar --}}
                                            <span
                                                class="alert-title">{{ $contract->employee->full_name_ar ?? '---' }}</span>
                                            <span class="alert-meta">المسمى:
                                                {{ $contract->employee->job_title ?? '---' }}</span>
                                        </div>
                                        <div class="alert-action text-end">
                                            <span
                                                class="badge bg-info bg-opacity-10 text-info fw-bold border border-info border-opacity-25 px-2 py-1">
                                                @if ($daysLeft < 0)
                                                    منتهي منذ {{ abs($daysLeft) }} يوم
                                                @else
                                                    باقي {{ $daysLeft }} يوم
                                                @endif
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="all-clear-state">
                            <i class="mdi mdi-file-check-outline text-success"></i>
                            <h6 style="color: #059669;">لا توجد عقود منتهية</h6>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🟢 5. نواقص المخزون والكتب (Inventory) --}}
            <div class="col-xl-4">
                <div class="card card-premium border-top border-secondary border-4 shadow-sm">
                    <div class="alert-header bg-secondary bg-opacity-10">
                        <h6 class="fw-bold text-secondary mb-0 text-dark">
                            <i class="mdi mdi-archive-alert-outline fs-5 align-middle me-1"></i> كتب وزي مدرسي وصل لحد
                            النقص
                        </h6>
                        <span class="badge bg-secondary rounded-pill fs-6 shadow-sm">{{ $lowStockCount }}</span>
                    </div>

                    @if ($lowStockCount > 0)
                        <div class="alert-scroll-container">
                            <ul class="alert-list">
                                @foreach ($lowStock as $stock)
                                    <li>
                                        <div class="alert-info">
                                            <span class="alert-title text-wrap lh-base"
                                                style="max-width: 180px;">{{ $stock->name_ar }}</span>
                                            <span class="alert-meta mt-1">حد التنبيه: <strong
                                                    class="text-dark">{{ $stock->alert_quantity }}</strong> وحدة</span>
                                        </div>
                                        <div class="alert-action text-center">
                                            <div
                                                class="amount-text {{ $stock->quantity <= 0 ? 'text-danger' : 'text-warning text-darken' }} fs-4 mb-0">
                                                {{ $stock->quantity }}</div>
                                            <small class="text-muted font-11 d-block fw-bold">المتاح حالياً</small>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="all-clear-state">
                            <i class="mdi mdi-check-box-multiple-outline text-success"></i>
                            <h6 style="color: #059669;">المخزون متوفر بالكامل</h6>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
