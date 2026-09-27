@extends('backend.layouts.master')
@section('title', 'تقرير إشغال وإيرادات الحافلات')
@section('title_page', 'العمليات اللوجستية - تقرير الحافلات')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            border: none;
            background: #fff;
        }

        .bus-card {
            border-radius: 12px;
            padding: 20px;
            background: #fff;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .bus-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-val {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 22px;
            letter-spacing: -0.5px;
        }

        .table-erp {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-erp thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            padding: 16px;
            border-bottom: 2px solid #e2e8f0;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            padding: 14px;
            color: #334155;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .bus-card {
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

        body[data-layout-color="dark"] .stat-val {
            color: #f8fafc !important;
        }

        /* 🖨️ Print Styles */
        @media print {
            body {
                background: #fff;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print,
            .page-title-box,
            .navbar-custom,
            .left-side-menu,
            .footer {
                display: none !important;
            }

            .card-premium,
            .bus-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                break-inside: avoid;
            }

            .table-responsive {
                overflow: visible !important;
            }

            .table-erp th {
                background-color: #f8f9fa !important;
                color: #000 !important;
            }

            .stat-val {
                color: #000 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 رأس الصفحة وأزرار التحكم --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 no-print">
            <div>
                <h4 class="fw-bolder text-dark mb-1">
                    <i class="mdi mdi-bus-school text-warning me-1 fs-3 align-middle"></i> تقرير إشغال وإيرادات الحافلات
                </h4>
                <p class="text-muted font-13 mb-0">نظرة عامة على اشتراكات النقل المدرسي وتوقعات العوائد المالية.</p>
            </div>
            <div>
                <button class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm" onclick="window.print()">
                    <i class="mdi mdi-printer me-1"></i> طباعة التقرير
                </button>
            </div>
        </div>

        {{-- 🌟 كروت الإحصائيات العلوية --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="bus-card d-flex align-items-center gap-3 border-start border-primary border-4 shadow-sm">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle shadow-sm">
                        <i class="mdi mdi-account-group fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-1 text-uppercase">إجمالي الطلاب المشتركين بالنقل</h6>
                        <div class="stat-val text-primary">{{ number_format($totalStudentsInBuses ?? 0) }} <span
                                class="font-14 text-muted fw-normal font-sans">طالب</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bus-card d-flex align-items-center gap-3 border-start border-success border-4 shadow-sm">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle shadow-sm">
                        <i class="mdi mdi-cash-multiple fs-2"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-1 text-uppercase">الإيراد المالي المتوقع للحافلات (شهرياً)</h6>
                        <div class="stat-val text-success">{{ number_format($expectedBusRevenue ?? 0, 3) }} <span
                                class="font-14 text-muted fw-normal font-sans">OMR</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 جدول التفاصيل لكل حافلة --}}
        <div class="card card-premium overflow-hidden border-top border-dark border-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-erp table-hover table-nowrap align-middle">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th class="text-start ps-4">رقم / اسم الحافلة (المسار)</th>
                                <th>اسم السائق</th>
                                <th>عدد الطلاب (اتجاهين)</th>
                                <th>عدد الطلاب (اتجاه واحد)</th>
                                <th>تسعيرة النقل (OMR)</th>
                                <th class="bg-success bg-opacity-10 text-success border-start border-success">الإيراد
                                    المتوقع (OMR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buses ?? [] as $idx => $bus)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-dark text-start ps-4">
                                        <i class="mdi mdi-bus text-warning me-1"></i> {{ $bus->bus_number ?? '---' }}
                                    </td>
                                    <td class="fw-bold">{{ $bus->driver_name ?? '---' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-primary text-white rounded-pill px-3 py-1 shadow-sm font-13 font-monospace">
                                            {{ $bus->students_both ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-info text-white rounded-pill px-3 py-1 shadow-sm font-13 font-monospace">
                                            {{ $bus->students_single ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="font-monospace text-muted fw-bold small">
                                        {{-- ✅ عرض التسعيرتين الفعليتين بدلاً من إيجار السائق --}}
                                        <div dir="ltr">{{ number_format($bus->monthly_fare_single ?? 0, 3) }} /
                                            {{ number_format($bus->monthly_fare_both ?? 0, 3) }}</div>
                                    </td>
                                    <td
                                        class="fw-bold text-success font-monospace fs-6 bg-success bg-opacity-10 border-start border-success">
                                        {{ number_format($bus->total_revenue ?? 0, 3) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-5 text-center text-muted fw-bold">
                                        <i class="mdi mdi-bus-alert fs-1 opacity-25 d-block mb-2 text-warning"></i>
                                        لا توجد حافلات مسجلة أو مخصصة للطلاب حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (isset($buses) && $buses->count() > 0)
                            <tfoot class="table-light fw-bolder">
                                <tr>
                                    <td colspan="3" class="text-end pe-4 text-dark fs-5">الإجمالي الكلي:</td>
                                    <td class="font-monospace text-primary fs-5">
                                        {{ number_format($buses->sum('students_both') ?? 0) }}</td>
                                    <td class="font-monospace text-info fs-5">
                                        {{ number_format($buses->sum('students_single') ?? 0) }}</td>
                                    <td>-</td>
                                    <td
                                        class="font-monospace text-success fs-5 bg-success bg-opacity-10 border-start border-success">
                                        {{ number_format($expectedBusRevenue ?? 0, 3) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- تذييل مطبوع فقط --}}
        <div class="row mt-4 d-none d-print-block">
            <div class="col-12 text-center text-muted">
                <hr>
                <p class="font-12 mb-0">تم استخراج هذا التقرير من خلال نظام <b>سِيدَة</b> بواسطة: <strong
                        class="text-dark">{{ auth()->user()->name ?? 'مدير النظام' }}</strong></p>
                <p class="font-12 mb-0">تاريخ الطباعة: <span dir="ltr">{{ now()->format('Y-m-d H:i') }}</span></p>
            </div>
        </div>

    </div>
@endsection
