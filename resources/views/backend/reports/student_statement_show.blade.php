@extends('backend.layouts.master')
@section('title', 'كشف حساب | ' . $student->first_name)

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .statement-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 40px;
            margin-bottom: 30px;
        }

        .header-section {
            border-bottom: 3px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .school-logo {
            font-size: 24px;
            font-weight: 900;
            color: #004b87;
            letter-spacing: 1px;
        }

        .summary-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .summary-title {
            font-size: 13px;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 22px;
            font-weight: 900;
            font-family: 'Courier New', Courier, monospace;
        }

        .table-statement {
            white-space: nowrap;
        }

        .table-statement thead th {
            background-color: #0f172a;
            color: #fff;
            font-weight: bold;
            font-size: 13px;
            padding: 14px;
            border: none;
            text-align: center;
        }

        .table-statement tbody td {
            padding: 12px;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            text-align: center;
        }

        .val-debit {
            color: #ef4444;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
        }

        .val-credit {
            color: #10b981;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
        }

        .val-balance {
            font-weight: 900;
            font-family: 'Courier New', Courier, monospace;
            font-size: 15px;
        }

        .type-badge {
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: bold;
        }

        .badge-contract {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fecaca;
        }

        .badge-payment {
            background: #ecfdf5;
            color: #10b981;
            border: 1px solid #a7f3d0;
        }

        .badge-store {
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        /* ✅ بادج جديد لفواتير المقصف */

        /* Dark Mode Support */
        body[data-layout-color="dark"] .statement-container {
            background-color: #323a46;
            box-shadow: none;
            border: 1px solid #424e5a;
        }

        body[data-layout-color="dark"] .header-section {
            border-bottom-color: #10b981;
        }

        body[data-layout-color="dark"] .school-logo,
        body[data-layout-color="dark"] .fw-bold.text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .summary-box {
            background-color: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-statement thead th {
            background-color: #1e293b;
            color: #cbd5e1;
        }

        body[data-layout-color="dark"] .table-statement tbody td {
            border-bottom-color: #424e5a;
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .badge-contract {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
        }

        body[data-layout-color="dark"] .badge-payment {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
        }

        body[data-layout-color="dark"] .badge-store {
            background: rgba(217, 119, 6, 0.15);
            border-color: rgba(217, 119, 6, 0.3);
            color: #fbbf24;
        }

        /* 🖨️ إعدادات الطباعة الاحترافية 🖨️ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }

            body {
                background: #fff !important;
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print,
            .left-side-menu,
            .navbar-custom,
            .page-title-box,
            .footer {
                display: none !important;
            }

            .statement-container {
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                border: none !important;
            }

            .header-section {
                border-bottom: 2px solid #000 !important;
            }

            .table-statement thead th {
                background-color: #f1f5f9 !important;
                color: #000 !important;
                border: 1px solid #ccc !important;
            }

            .table-statement tbody td {
                border: 1px solid #ccc !important;
            }

            .table-statement tbody tr {
                page-break-inside: avoid !important;
            }

            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="{{ route('admin.reports.student-statement') }}"
                class="btn btn-light border rounded-pill shadow-sm fw-bold px-4">
                <i class="mdi mdi-arrow-left me-1"></i> رجوع للبحث
            </a>
            <button onclick="window.print()" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                <i class="mdi mdi-printer me-1"></i> طباعة الكشف
            </button>
        </div>

        <div class="statement-container border-top border-primary border-4">

            {{-- 🌟 الترويسة (Header) 🌟 --}}
            <div class="header-section d-flex justify-content-between align-items-end flex-wrap gap-3">
                <div>
                    <h2 class="school-logo mb-1"><i class="mdi mdi-school-outline me-1"></i> المدرسة الذكية الحديثة</h2>
                    <p class="text-muted font-14 mb-0 fw-bold">سلطنة عُمان - مسقط</p>
                    <p class="text-muted font-13 mb-0 font-monospace">تاريخ الطباعة: {{ date('Y-m-d H:i') }}</p>
                </div>
                <div class="text-md-end text-start">
                    <h3 class="fw-bold text-dark mb-3"><i class="mdi mdi-file-document-outline text-primary me-1"></i> كشف
                        حساب تفصيلي</h3>
                    <table class="table table-sm table-borderless text-md-end text-start mb-0 font-14">
                        <tr>
                            <td class="text-muted fw-bold">اسم الطالب:</td>
                            <td class="fw-bold text-primary fs-5">{{ $student->first_name }}
                                {{ $student->second_name ?? '' }} {{ $student->tribe }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">ولي الأمر:</td>
                            <td class="fw-bold text-dark">{{ $student->parent->name_ar ?? '---' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold">الصف / الشعبة:</td>
                            <td class="fw-bold text-dark">{{ $student->grade->name_ar ?? '-' }} /
                                {{ $student->section->name_ar ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- 🌟 ملخص الحساب 🌟 --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="summary-box shadow-sm bg-danger bg-opacity-10 border border-danger border-opacity-25">
                        <div class="summary-title text-danger"><i class="mdi mdi-cash-minus me-1"></i> إجمالي المستحق (مدين)
                        </div>
                        <div class="summary-value text-danger">{{ number_format($totalDebit, 3) }} <small
                                class="font-12 font-sans text-muted">OMR</small></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-box shadow-sm bg-success bg-opacity-10 border border-success border-opacity-25">
                        <div class="summary-title text-success"><i class="mdi mdi-cash-plus me-1"></i> إجمالي المدفوع (دائن)
                        </div>
                        <div class="summary-value text-success">{{ number_format($totalCredit, 3) }} <small
                                class="font-12 font-sans text-muted">OMR</small></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div
                        class="summary-box shadow-sm {{ $runningBalance > 0 ? 'bg-danger bg-opacity-10 border border-danger border-opacity-25' : 'bg-success bg-opacity-10 border border-success border-opacity-25' }}">
                        <div class="summary-title {{ $runningBalance > 0 ? 'text-danger' : 'text-success' }}">
                            <i class="mdi mdi-scale-balance me-1"></i> الرصيد المتبقي (المديونية)
                        </div>
                        <div class="summary-value {{ $runningBalance > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($runningBalance, 3) }} <small class="font-12 font-sans text-muted">OMR</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🌟 جدول الحركات (Ledger) 🌟 --}}
            <div class="table-responsive border rounded-3 shadow-sm">
                <table class="table table-statement text-center mb-0">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>رقم المرجع</th>
                            <th class="text-start ps-3">البيان / التفاصيل</th>
                            <th class="bg-danger bg-opacity-10 border-bottom-0 text-danger">مدين (عليه)</th>
                            <th class="bg-success bg-opacity-10 border-bottom-0 text-success">دائن (له/دفع)</th>
                            <th class="border-bottom-0">الرصيد التراكمي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $trans)
                            <tr>
                                <td dir="ltr" class="text-muted fw-bold font-monospace">
                                    {{ \Carbon\Carbon::parse($trans['date'])->format('Y-m-d') }}</td>
                                <td>
                                    {{-- ✅ التحسين الاحترافي: فصل أنواع الحركات بصرياً --}}
                                    @if ($trans['type'] == 'contract')
                                        <span class="type-badge badge-contract shadow-sm"><i
                                                class="mdi mdi-file-document-edit-outline me-1"></i> عقد دراسي</span>
                                    @elseif ($trans['type'] == 'store_invoice')
                                        <span class="type-badge badge-store shadow-sm"><i
                                                class="mdi mdi-store-outline me-1"></i> فاتورة مقصف/زي</span>
                                    @elseif ($trans['type'] == 'installment_payment')
                                        <span class="type-badge badge-payment shadow-sm"><i
                                                class="mdi mdi-calendar-check me-1"></i> سداد قسط</span>
                                    @else
                                        <span class="type-badge badge-payment shadow-sm"><i
                                                class="mdi mdi-receipt me-1"></i> سند قبض</span>
                                    @endif
                                </td>
                                <td class="font-monospace fw-bold text-dark">{{ $trans['reference'] }}</td>
                                <td class="text-start ps-3 fw-bold text-wrap" style="max-width: 250px; line-height: 1.6;">
                                    {{ $trans['description'] }}</td>
                                <td>
                                    @if ($trans['debit'] > 0)
                                        <span class="val-debit">{{ number_format($trans['debit'], 3) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($trans['credit'] > 0)
                                        <span class="val-credit">{{ number_format($trans['credit'], 3) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="val-balance {{ $trans['balance'] > 0 ? 'text-danger' : ($trans['balance'] < 0 ? 'text-success' : 'text-dark') }}">
                                        {{ number_format($trans['balance'], 3) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted py-5 fw-bold bg-light">
                                    <i class="mdi mdi-text-box-remove-outline fs-1 d-block mb-2 opacity-50"></i>
                                    لا توجد أي حركات مالية (فواتير أو مدفوعات) مسجلة لهذا الطالب حتى الآن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-center text-muted font-13 fw-bold no-print border-top pt-3">-- نهاية كشف الحساب --</div>

            {{-- 🌟 توقيعات للطباعة 🌟 --}}
            <div class="row mt-5 pt-5 d-none d-print-flex text-center fw-bold text-dark font-14">
                <div class="col-4">توقيع المحاسب المختص<br><br>..................................</div>
                <div class="col-4">الختم الرسمي للمدرسة<br><br></div>
                <div class="col-4">توقيع ولي الأمر بالاستلام والمطابقة<br><br>..................................</div>
            </div>

            <div class="row mt-4 d-none d-print-block">
                <div class="col-12 text-center text-muted font-12 border-top pt-2">
                    تم استخراج وطباعة هذا المستند المالي من خلال نظام <b>سِيدَة</b>
                </div>
            </div>

        </div>
    </div>
@endsection
