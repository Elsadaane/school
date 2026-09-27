@extends('backend.layouts.master')
@section('title', 'كشف حساب المورد | ' . $supplier->company_name)
@section('title_page', 'إدارة المشتريات وحسابات الموردين')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* 🌟 Premium Profile Header 🌟 */
        .profile-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            border-radius: 16px;
            padding: 35px 30px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 10%, transparent 40%);
            transform: rotate(30deg);
            pointer-events: none;
        }

        .rating-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 18px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        .stars i {
            color: #475569;
            font-size: 24px;
            margin: 0 2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .stars i.active {
            color: #fbbf24;
        }

        /* 🌟 Statistics Cards 🌟 */
        .stat-card {
            border-radius: 16px;
            padding: 25px 20px;
            color: #fff;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.1) 100%);
            pointer-events: none;
        }

        .stat-purchases {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .stat-returns {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }

        .stat-paid {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .stat-due {
            background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
            transform: scale(1.02);
            z-index: 5;
        }

        .stat-title {
            font-size: 15px;
            font-weight: 800;
            opacity: 0.9;
            margin-bottom: 8px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: 28px;
            font-weight: 900;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: -1px;
        }

        /* 🌟 Tabs & Tables 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
            border-top: 5px solid #1e293b;
        }

        .nav-pills {
            background: #f8fafc;
            padding: 6px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .nav-pills .nav-link {
            font-weight: 800;
            color: #475569;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            transition: 0.3s;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #e2e8f0;
        }

        .nav-pills .nav-link.active {
            background-color: #1e293b;
            color: white;
            box-shadow: 0 4px 10px rgba(30, 41, 59, 0.2);
        }

        .table-custom {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
            text-align: center;
        }

        .table-custom tbody td {
            vertical-align: middle;
            padding: 14px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }

        .table-custom tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .nav-pills {
            background: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .nav-pills .nav-link {
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .nav-pills .nav-link.active {
            background-color: #60a5fa;
            color: #fff;
        }

        body[data-layout-color="dark"] .table-custom thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom tbody tr:hover td {
            background-color: #38414e;
        }

        /* 🖨️ إعدادات طباعة كشف الحساب الموحد 🖨️ */
        @media print {
            body {
                background: #fff !important;
            }

            .no-print,
            .nav-pills,
            .navbar-custom,
            .left-side-menu,
            .footer,
            .btn-light {
                display: none !important;
            }

            .tab-content>.tab-pane {
                display: block !important;
                opacity: 1 !important;
                margin-bottom: 30mm;
                page-break-inside: auto;
            }

            .tab-pane {
                border-top: 2px solid #000;
                padding-top: 15px;
            }

            .tab-pane::before {
                content: attr(id);
                font-size: 18px;
                font-weight: bold;
                color: #000;
                text-transform: uppercase;
                margin-bottom: 10px;
                display: block;
                text-align: right;
            }

            #pills-invoices::before {
                content: "1. سجل فواتير المشتريات";
            }

            #pills-returns::before {
                content: "2. سجل المرتجعات";
            }

            #pills-payments::before {
                content: "3. سجل الدفعات والسندات";
            }

            .card-premium {
                box-shadow: none !important;
                border: none !important;
            }

            .table-responsive {
                max-height: none !important;
                overflow: visible !important;
            }

            .table-custom th,
            .table-custom td {
                border: 1px solid #000 !important;
                color: #000 !important;
            }

            .stat-card {
                background: #fff !important;
                color: #000 !important;
                box-shadow: none !important;
                border: 1px solid #000 !important;
                margin-bottom: 10px;
                padding: 10px !important;
            }

            .profile-header {
                background: #fff !important;
                color: #000 !important;
                box-shadow: none !important;
                border: 2px solid #000;
                padding: 15px;
            }

            .stars i {
                color: #000;
            }

            .badge {
                border: 1px solid #000;
                color: #000 !important;
                background: transparent !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 رأس الصفحة 🌟 --}}
        <div class="row mb-4 mt-2 d-print-none">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold text-dark mb-0"><i
                            class="mdi mdi-account-details-outline text-primary me-1 fs-3 align-middle"></i> ملف وكشف حساب
                        المورد</h4>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-dark fw-bold rounded-pill px-4 shadow-sm"
                        onclick="window.print();">
                        <i class="mdi mdi-printer me-1 align-middle"></i> طباعة كشف الحساب الشامل
                    </button>
                    <a href="{{ route('admin.suppliers.index') }}"
                        class="btn btn-light rounded-pill border fw-bold shadow-sm px-4">
                        <i class="mdi mdi-arrow-left me-1 align-middle"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        {{-- 🌟 رأس البروفايل والتقييم 🌟 --}}
        <div class="profile-header shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-8 text-center text-md-start">
                    <h2 class="fw-bold mb-2">{{ $supplier->company_name }}</h2>
                    <p class="mb-3 opacity-75 font-15">
                        <i class="mdi mdi-account-tie me-1"></i> المسؤول: {{ $supplier->contact_person ?? 'غير مسجل' }}
                        <span class="mx-2">|</span>
                        <i class="mdi mdi-phone me-1"></i> <span dir="ltr">{{ $supplier->phone ?? '---' }}</span>
                    </p>
                    <div>
                        @if ($supplier->is_active)
                            <span
                                class="badge bg-success bg-opacity-25 border border-success border-opacity-50 text-white fs-6 px-4 py-2 rounded-pill"><i
                                    class="mdi mdi-check-circle me-1"></i> حساب نشط</span>
                        @else
                            <span
                                class="badge bg-danger bg-opacity-25 border border-danger border-opacity-50 text-white fs-6 px-4 py-2 rounded-pill"><i
                                    class="mdi mdi-cancel me-1"></i> حساب موقوف</span>
                        @endif
                        <span
                            class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 fs-6 px-4 py-2 rounded-pill ms-2 font-monospace"><i
                                class="mdi mdi-file-document-outline me-1"></i> الرقم الضريبي:
                            {{ $supplier->tax_number ?? '---' }}</span>
                    </div>
                </div>
                <div class="col-md-4 mt-4 mt-md-0">
                    <div class="rating-box shadow-sm">
                        <h6 class="text-uppercase font-13 fw-bold text-white opacity-75 mb-3"><i
                                class="mdi mdi-robot-outline me-1"></i> التقييم الذكي للتاجر</h6>
                        <div class="stars mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="mdi mdi-star {{ $i <= $rating ? 'active' : '' }}"></i>
                            @endfor
                        </div>
                        <span
                            class="badge bg-{{ $ratingColor }} text-white border border-white border-opacity-25 font-13 px-3 py-2 rounded-pill shadow-sm">{{ $ratingText }}</span>
                        <small class="d-block mt-3 opacity-50 font-11">مؤشر جودة التوريد بناءً على نسبة المرتجعات</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 كروت الإحصائيات المالية 🌟 --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card stat-purchases">
                    <div class="stat-title"><i class="mdi mdi-cart-arrow-down fs-5 align-middle me-1"></i> إجمالي المشتريات
                    </div>
                    <div class="stat-value">{{ number_format($totalPurchases, 3) }} <span
                            class="fs-6 font-sans opacity-75">OMR</span></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card stat-returns">
                    <div class="stat-title"><i class="mdi mdi-keyboard-return fs-5 align-middle me-1"></i> إجمالي المرتجعات
                    </div>
                    <div class="stat-value">{{ number_format($totalReturns, 3) }} <span
                            class="fs-6 font-sans opacity-75">OMR</span></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card stat-paid">
                    <div class="stat-title"><i class="mdi mdi-cash-check fs-5 align-middle me-1"></i> إجمالي المدفوع للتاجر
                    </div>
                    <div class="stat-value">{{ number_format($totalPaid, 3) }} <span
                            class="fs-6 font-sans opacity-75">OMR</span></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card stat-due">
                    <div class="stat-title"><i class="mdi mdi-scale-balance fs-5 align-middle me-1"></i> المديونية (متبقي
                        له)</div>
                    <div class="stat-value">{{ number_format($dueAmount, 3) }} <span
                            class="fs-6 font-sans opacity-75">OMR</span></div>
                    <span
                        class="badge bg-white text-dark bg-opacity-25 border border-white border-opacity-50 font-11 mt-2">متضمن
                        الرصيد الافتتاحي</span>
                </div>
            </div>
        </div>

        {{-- 🌟 التبويبات والسجلات (فواتير، دفعات، مرتجعات) 🌟 --}}
        <div class="card card-premium shadow-sm border-0">
            <div class="card-body p-4 p-md-5">

                {{-- قائمة التابات --}}
                <div class="text-center text-md-start mb-4 d-print-none">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-invoices" data-bs-toggle="pill"
                                data-bs-target="#pills-invoices" type="button" role="tab">
                                <i class="mdi mdi-receipt-text-outline fs-5 align-middle me-1"></i> فواتير المشتريات
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-returns" data-bs-toggle="pill" data-bs-target="#pills-returns"
                                type="button" role="tab">
                                <i class="mdi mdi-keyboard-return fs-5 align-middle me-1"></i> سجل المرتجعات
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-payments" data-bs-toggle="pill"
                                data-bs-target="#pills-payments" type="button" role="tab">
                                <i class="mdi mdi-cash-multiple fs-5 align-middle me-1"></i> الدفعات (سندات الصرف)
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="pills-tabContent">

                    {{-- 1️⃣ تاب الفواتير --}}
                    <div class="tab-pane fade show active" id="pills-invoices" role="tabpanel">
                        <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                            <table class="table table-custom table-hover align-middle">
                                <thead class="sticky-top" style="z-index: 10;">
                                    <tr>
                                        <th>رقم الفاتورة المرجعي</th>
                                        <th>تاريخ الفاتورة</th>
                                        <th>قيمة الفاتورة الإجمالية</th>
                                        <th>المدفوع منها</th>
                                        <th>المتبقي للدفع</th>
                                        <th>حالة السداد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseInvoices as $inv)
                                        <tr>
                                            <td class="fw-bold font-monospace">
                                                @can('view_purchase_invoices')
                                                    <a href="{{ route('admin.purchase-invoices.show', $inv->id) }}"
                                                        class="text-primary text-decoration-underline">{{ $inv->invoice_number }}</a>
                                                @else
                                                    <span class="text-dark">{{ $inv->invoice_number }}</span>
                                                @endcan
                                            </td>
                                            <td dir="ltr" class="font-monospace text-muted fw-bold">
                                                {{ $inv->invoice_date }}</td>
                                            <td class="fw-bolder text-dark font-monospace fs-6">
                                                {{ number_format($inv->grand_total, 3) }}</td>
                                            <td class="text-success fw-bold font-monospace">
                                                {{ number_format($inv->paid_amount, 3) }}</td>
                                            <td class="text-danger fw-bold font-monospace">
                                                {{ number_format($inv->due_amount, 3) }}</td>
                                            <td>
                                                @if ($inv->payment_status == 'paid')
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">خالصة</span>
                                                @elseif($inv->payment_status == 'partial')
                                                    <span
                                                        class="badge bg-warning bg-opacity-10 text-warning text-darken border border-warning border-opacity-25 px-3 py-1 rounded-pill">دفع
                                                        جزئي</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">آجلة
                                                        (مستحقة)
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-muted py-5 bg-light text-center">
                                                <i
                                                    class="mdi mdi-receipt-text-remove-outline fs-1 d-block mb-2 opacity-50"></i>
                                                <h6 class="fw-bold">لا توجد فواتير مشتريات معتمدة لهذا المورد.</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 2️⃣ تاب المرتجعات --}}
                    <div class="tab-pane fade" id="pills-returns" role="tabpanel">
                        <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                            <table class="table table-custom table-hover align-middle">
                                <thead class="sticky-top" style="z-index: 10;">
                                    <tr>
                                        <th>رقم المرتجع</th>
                                        <th>الفاتورة الأصلية المستند عليها</th>
                                        <th>تاريخ الإرجاع</th>
                                        <th>إجمالي قيمة المرتجع</th>
                                        <th class="text-start ps-3">ملاحظات وسبب الإرجاع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($returns as $ret)
                                        <tr>
                                            <td class="fw-bold text-danger font-monospace">{{ $ret->return_number }}</td>
                                            <td class="font-monospace fw-bold text-muted">
                                                {{ $ret->invoice->invoice_number ?? '---' }}</td>
                                            <td dir="ltr" class="font-monospace fw-bold text-muted">
                                                {{ $ret->return_date }}</td>
                                            <td class="fw-bolder text-dark font-monospace fs-6">
                                                {{ number_format($ret->total_return_amount, 3) }} OMR</td>
                                            <td class="text-start ps-3 fw-semibold text-muted">
                                                {{ $ret->notes ?? 'لا توجد ملاحظات مسجلة' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-muted py-5 bg-light text-center">
                                                <i class="mdi mdi-keyboard-return fs-1 d-block mb-2 opacity-50"></i>
                                                <h6 class="fw-bold">السجل نظيف. لا توجد مرتجعات مسجلة لهذا المورد.</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 3️⃣ تاب الدفعات والسندات --}}
                    <div class="tab-pane fade" id="pills-payments" role="tabpanel">
                        <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                            <table class="table table-custom table-hover align-middle">
                                <thead class="sticky-top" style="z-index: 10;">
                                    <tr>
                                        <th>رقم السند المالي</th>
                                        <th>تاريخ الدفعة</th>
                                        <th>المبلغ المدفوع الفعلي</th>
                                        <th>طريقة السداد / الخزينة</th>
                                        <th class="text-start ps-3">تفاصيل المعاملة والملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $pay)
                                        <tr>
                                            <td class="fw-bold text-success font-monospace">{{ $pay->receipt_number }}
                                            </td>
                                            <td dir="ltr" class="font-monospace fw-bold text-muted">
                                                {{ $pay->payment_date }}</td>
                                            {{-- 🔥 تم إضافة abs هنا للتخلص من السالب 🔥 --}}
                                            <td class="fw-bolder text-dark font-monospace fs-6">
                                                {{ number_format(abs($pay->amount), 3) }} OMR</td>
                                            <td>
                                                <span
                                                    class="badge bg-light text-dark border px-3 py-1 fw-bold shadow-sm"><i
                                                        class="mdi mdi-bank-transfer me-1"></i>{{ $pay->payment_type }}</span>
                                            </td>
                                            <td class="text-start ps-3 fw-semibold text-muted">
                                                {{ $pay->notes ?? 'تسوية حساب مالي مورد' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-muted py-5 bg-light text-center">
                                                <i class="mdi mdi-cash-remove fs-1 d-block mb-2 opacity-50"></i>
                                                <h6 class="fw-bold">لا توجد دفعات مالية أو سندات صرف مسجلة بشكل مستقل لهذا
                                                    المورد.</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
