@extends('backend.layouts.master')
@section('title', 'حركة الخزينة والتدفقات النقدية')
@section('title_page', 'التقارير المالية - كشف حركة الصندوق/البنك')

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
        }

        .table-erp {
            width: 100%;
            margin-bottom: 0;
            white-space: nowrap;
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px;
            text-align: center;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        .amount-in {
            color: #10b981;
            font-weight: 800;
            font-family: 'Courier New', Courier, monospace;
        }

        .amount-out {
            color: #ef4444;
            font-weight: 800;
            font-family: 'Courier New', Courier, monospace;
        }

        .amount-balance {
            color: #0f172a;
            font-weight: 900;
            font-family: 'Courier New', Courier, monospace;
            background-color: #f8fafc;
            border-right: 1px solid #f1f5f9;
        }

        .summary-card {
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            background: #fff;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
        }

        .summary-title {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 5px;
        }

        .summary-value {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 22px;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .summary-card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .table-erp tbody tr:hover td {
            background-color: #3b4652;
        }

        body[data-layout-color="dark"] .amount-balance {
            background-color: #282f36;
            color: #f8fafc;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .summary-value {
            color: #f8fafc;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        /* 🖨️ تنسيق الطباعة النظيفة 🖨️ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }

            body {
                background: #fff !important;
            }

            .no-print,
            .d-print-none,
            .left-side-menu,
            .navbar-custom,
            .page-title-box,
            .footer {
                display: none !important;
            }

            .card-premium {
                box-shadow: none !important;
                border: none !important;
            }

            .summary-card {
                border: 1px solid #cbd5e1 !important;
                break-inside: avoid;
            }

            .table-erp {
                border: 1px solid #cbd5e1;
            }

            .table-erp th,
            .table-erp td {
                border: 1px solid #cbd5e1 !important;
                white-space: normal !important;
            }

            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 فلتر كشف الحساب --}}
        <div class="card card-premium mb-4 d-print-none border-top border-primary border-4 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.cash_flow') }}" method="GET"
                    class="row g-3 align-items-end justify-content-center">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark mb-2"><i class="mdi mdi-bank-outline me-1"></i> اختر
                            الخزينة / الحساب البنكي <span class="text-danger">*</span></label>
                        <select name="financial_account_id"
                            class="form-select form-select-lg bg-light border-0 fw-bold shadow-sm" required>
                            <option value="" disabled selected>-- حدد الحساب لعرض حركته --</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ ($accountId ?? '') == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">من تاريخ</label>
                        <input type="date" name="start_date"
                            class="form-control form-control-lg bg-light border-0 text-center fw-bold shadow-sm"
                            value="{{ request('start_date', $startDate ?? date('Y-m-01')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">إلى تاريخ</label>
                        <input type="date" name="end_date"
                            class="form-control form-control-lg bg-light border-0 text-center fw-bold shadow-sm"
                            value="{{ request('end_date', $endDate ?? date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm rounded-3">
                            <i class="mdi mdi-file-document-outline me-1"></i> استعلام
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 عرض كشف الحساب --}}
        @if (isset($accountId) && $accountId)
            @php
                // ✅ تأمين جلب الاسم باستخدام data_get لتجنب أخطاء Null
                $accountName = data_get($accounts->where('id', $accountId)->first(), 'name_ar', 'حساب غير محدد');
                $totalIn = collect($transactions ?? [])->sum('inflow');
                $totalOut = collect($transactions ?? [])->sum('outflow');
                $openingBalanceValue = $openingBalance ?? 0;
                $closingBalance = $openingBalanceValue + $totalIn - $totalOut;
                $runningBalance = $openingBalanceValue;
            @endphp

            <div class="card card-premium p-4 p-md-5 border-top border-dark border-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4 flex-wrap gap-3">
                    <div>
                        <h3 class="fw-bold text-dark mb-2"><i
                                class="mdi mdi-swap-horizontal text-primary me-1 fs-3 align-middle"></i> كشف حركة حساب مفصل
                            (General Ledger)</h3>
                        <p class="text-muted mb-0 font-14">الفترة من: <span
                                class="fw-bold text-dark font-monospace mx-1">{{ $startDate }}</span> | إلى: <span
                                class="fw-bold text-dark font-monospace mx-1">{{ $endDate }}</span></p>
                    </div>
                    <div class="text-md-end text-center d-flex align-items-center gap-3">
                        <div
                            class="bg-primary bg-opacity-10 p-3 rounded-3 border border-primary border-opacity-25 shadow-sm text-start">
                            <small class="text-primary fw-bold d-block mb-1">اسم الحساب / الخزينة المحددة:</small>
                            <h4 class="fw-bold text-primary mb-0"><i class="mdi mdi-safe-square-outline me-1"></i>
                                {{ $accountName }}</h4>
                        </div>
                        <button type="button" class="btn btn-dark btn-lg border fw-bold shadow-sm d-print-none rounded-3"
                            onclick="window.print()" title="طباعة كشف الحساب">
                            <i class="mdi mdi-printer me-1"></i> طباعة
                        </button>
                    </div>
                </div>

                {{-- 🌟 كروت ملخص الحركة --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card shadow-sm border-start border-secondary border-4 bg-light">
                            <div class="summary-title text-secondary">الرصيد الافتتاحي (أول المدة)</div>
                            <div class="summary-value text-dark">{{ number_format($openingBalanceValue, 3) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card shadow-sm border-start border-success border-4"
                            style="background-color: #f0fdf4;">
                            <div class="summary-title text-success"><i class="mdi mdi-arrow-down-thick"></i> إجمالي
                                المقبوضات</div>
                            <div class="summary-value text-success">{{ number_format($totalIn, 3) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card shadow-sm border-start border-danger border-4"
                            style="background-color: #fef2f2;">
                            <div class="summary-title text-danger"><i class="mdi mdi-arrow-up-thick"></i> إجمالي المدفوعات
                            </div>
                            <div class="summary-value text-danger">{{ number_format($totalOut, 3) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card shadow-sm border-start border-primary border-4"
                            style="background-color: #eff6ff;">
                            <div class="summary-title text-primary"><i class="mdi mdi-scale-balance"></i> الرصيد الختامي
                            </div>
                            <div class="summary-value text-primary">{{ number_format($closingBalance, 3) }}</div>
                        </div>
                    </div>
                </div>

                {{-- 🌟 جدول الحركة اليومية --}}
                <div class="table-responsive border rounded-3 shadow-sm">
                    <table class="table table-erp text-center mb-0">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>نوع الحركة</th>
                                <th class="text-start ps-3">البيان / التفاصيل</th>
                                <th class="bg-success bg-opacity-10 text-success border-bottom-0">وارد (+)</th>
                                <th class="bg-danger bg-opacity-10 text-danger border-bottom-0">منصرف (-)</th>
                                <th class="bg-light border-bottom-0">الرصيد التراكمي (=)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-light">
                                <td class="text-muted fw-bold font-monospace">{{ $startDate }}</td>
                                <td><span class="badge bg-secondary shadow-sm px-2 py-1">رصيد افتتاحي</span></td>
                                <td class="text-start text-muted ps-3">رصيد الحساب المرحل من الفترات السابقة</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="amount-balance fs-5">{{ number_format($runningBalance, 3) }}</td>
                            </tr>

                            @forelse($transactions ?? [] as $t)
                                @php
                                    $runningBalance = $runningBalance + $t->inflow - $t->outflow;
                                @endphp
                                <tr>
                                    <td class="text-muted font-13 font-monospace">
                                        {{ \Carbon\Carbon::parse($t->date)->format('Y-m-d') }}</td>
                                    <td>
                                        {{-- ✅ تم إضافة 'مقبوضات' و 'مدفوعات' لتطابق استعلام جدول payments في الـ Controller --}}
                                        @if (str_contains($t->type, 'إيراد') || str_contains($t->type, 'قبض') || str_contains($t->type, 'مقبوضات'))
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 shadow-sm">{{ $t->type }}</span>
                                        @elseif(str_contains($t->type, 'مصروف') ||
                                                str_contains($t->type, 'مشتريات') ||
                                                str_contains($t->type, 'صرف') ||
                                                str_contains($t->type, 'مدفوعات'))
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 shadow-sm">{{ $t->type }}</span>
                                        @else
                                            <span
                                                class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 shadow-sm">{{ $t->type }}</span>
                                        @endif
                                    </td>
                                    <td class="text-start ps-3 fw-bold text-dark font-13 text-wrap"
                                        style="max-width: 280px; line-height: 1.6;">
                                        {{ $t->notes ?? '---' }}
                                    </td>
                                    <td class="amount-in">{{ $t->inflow > 0 ? number_format($t->inflow, 3) : '-' }}</td>
                                    <td class="amount-out">{{ $t->outflow > 0 ? number_format($t->outflow, 3) : '-' }}</td>
                                    <td class="amount-balance fs-6">{{ number_format($runningBalance, 3) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-muted fs-5 fw-bold bg-white">
                                        <i
                                            class="mdi mdi-text-box-remove-outline fs-1 d-block mb-2 text-black-50 opacity-50"></i>
                                        لا توجد حركات مالية مسجلة على هذا الحساب خلال الفترة المحددة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if (isset($transactions) && $transactions->count() > 0)
                            <tfoot class="bg-light fw-bold border-top border-dark">
                                <tr>
                                    <td colspan="3" class="text-end text-dark fs-6 pe-4">إجمالي الحركة خلال هذه الفترة:
                                    </td>
                                    <td class="amount-in fs-6">{{ number_format($totalIn, 3) }}</td>
                                    <td class="amount-out fs-6">{{ number_format($totalOut, 3) }}</td>
                                    <td class="amount-balance fs-5 text-primary">{{ number_format($closingBalance, 3) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <div class="row mt-5 pt-4 border-top text-center font-14 fw-bold text-dark d-none d-print-flex">
                    <div class="col-4"><strong>المحاسب:</strong><br><br>....................................</div>
                    <div class="col-4"><strong>ختم الإدارة المالية:</strong><br><br>....................................
                    </div>
                    <div class="col-4"><strong>المدير المالي:</strong><br><br>....................................</div>
                </div>
            </div>
        @else
            <div class="card card-premium text-center py-5 border-top border-primary border-4 shadow-sm">
                <div class="card-body py-5">
                    <i class="mdi mdi-safe-square-outline text-primary opacity-25" style="font-size: 80px;"></i>
                    <h4 class="fw-bold text-dark mt-3">سجل حركة الخزائن والبنوك (Ledger)</h4>
                    <p class="text-muted font-14">يرجى تحديد الخزينة أو الحساب البنكي والفترة الزمنية من الفلتر أعلاه لعرض
                        الحركات المالية بشكل مفصل.</p>
                </div>
            </div>
        @endif
    </div>
@endsection
