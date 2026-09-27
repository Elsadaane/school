@extends('backend.layouts.master')
@section('title', 'تقرير الخصومات والإعفاءات')
@section('title_page', 'التقارير المالية - الخصومات والمنح الطلابية')

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
            background-color: #f1f5f9;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
            padding: 12px;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        .discount-amount {
            color: #e11d48;
            font-weight: 900;
            font-family: 'Courier New', Courier, monospace;
            background-color: #ffe4e6;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
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
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .discount-amount {
            background-color: rgba(225, 29, 72, 0.15);
            color: #fda4af;
        }

        body[data-layout-color="dark"] .fw-black.text-dark {
            color: #f8fafc !important;
        }

        /* 🖨️ إعدادات الطباعة النظيفة 🖨️ */
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

            .table-responsive {
                overflow: visible !important;
                max-height: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 فلتر السنة الدراسية --}}
        <div class="card card-premium mb-4 border-top border-primary border-4 no-print shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.discounts') }}" method="GET" id="filterForm"
                    class="row g-3 align-items-end justify-content-center">
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">
                            <i class="mdi mdi-calendar-clock me-1"></i> السنة الدراسية / الأكاديمية
                        </label>
                        <select name="academic_year_id"
                            class="form-select form-select-lg bg-light border-0 fw-bold shadow-sm">
                            <option value="">-- جميع السنوات (تراكمي) --</option>
                            @foreach ($years as $year)
                                <option value="{{ $year->id }}" {{ ($yearId ?? '') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm btn-submit">
                            <i class="mdi mdi-filter me-1"></i> تصفية البيانات
                        </button>
                        <button type="button" class="btn btn-dark btn-lg border fw-bold shadow-sm" onclick="window.print()"
                            title="طباعة التقرير">
                            <i class="mdi mdi-printer"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 كروت المؤشرات العليا (KPIs) --}}
        <div class="row mb-4 g-4">
            <div class="col-md-6">
                <div
                    class="card card-premium p-4 d-flex flex-row align-items-center gap-3 border-start border-danger border-4 shadow-sm h-100">
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-3" style="font-size: 35px; line-height: 1;">
                        <i class="mdi mdi-ticket-percent-outline"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-1">إجمالي التكلفة (قيمة الخصومات الممنوحة)</h6>
                        <h3 class="fw-black text-dark mb-0 font-monospace">
                            {{-- ✅ التصحيح: استخدام المبلغ الفعلي بدلاً من عدد العقود --}}
                            {{ number_format($totalDiscounts ?? 0, 3) }} <small
                                class="text-muted fs-6 font-sans">OMR</small>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div
                    class="card card-premium p-4 d-flex flex-row align-items-center gap-3 border-start border-info border-4 shadow-sm h-100">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-3" style="font-size: 35px; line-height: 1;">
                        <i class="mdi mdi-account-star-outline"></i>
                    </div>
                    <div>
                        <h6 class="text-muted fw-bold mb-1">عدد العقود المستفيدة من الخصم</h6>
                        <h3 class="fw-black text-dark mb-0 font-monospace">
                            {{-- ✅ التصحيح: استخدام count() لأن الـ Controller يرجع Collection وليس Paginator --}}
                            {{ $discountContracts->count() ?? 0 }} <small class="text-muted fs-6 font-sans">عقد</small>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 جدول التفاصيل --}}
        <div class="card card-premium overflow-hidden">
            <div
                class="card-header bg-white border-bottom p-4 rounded-top-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i
                        class="mdi mdi-format-list-bulleted text-primary me-1 fs-4 align-middle"></i> كشف عقود الطلاب
                    الحاصلين على إعفاءات مالية</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-erp text-center mb-0 align-middle">
                        <thead class="sticky-top" style="z-index: 1;">
                            <tr>
                                <th>#</th>
                                <th class="text-start ps-4">اسم الطالب المستفيد</th>
                                <th>الفرع / المدرسة</th>
                                <th>رقم العقد</th>
                                <th>السنة الدراسية</th>
                                <th>إجمالي العقد (بعد الخصم)</th>
                                <th>قيمة الخصم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($discountContracts ?? [] as $idx => $contract)
                                <tr>
                                    {{-- ✅ التصحيح: استخدام $idx + 1 لأن الـ Collection لا تحتوي على firstItem() --}}
                                    <td class="text-muted fw-bold">{{ $idx + 1 }}</td>
                                    <td class="text-start ps-4 fw-bold text-dark">
                                        <i class="mdi mdi-account-school text-primary me-1"></i>
                                        {{ $contract->student->first_name ?? '---' }} {{ $contract->student->tribe ?? '' }}
                                        <small
                                            class="d-block text-muted font-12 mt-1">{{ $contract->student->grade->name_ar ?? 'غير محدد' }}</small>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-light text-dark border px-3 py-1 shadow-sm">{{ $contract->branch->name_ar ?? '---' }}</span>
                                    </td>
                                    <td class="font-monospace fw-bold text-primary fs-6">#{{ $contract->contract_number }}
                                    </td>
                                    <td class="text-muted fw-bold">{{ $contract->academicYear->name ?? '---' }}</td>
                                    <td class="font-monospace fw-bold text-dark fs-6">
                                        {{ number_format($contract->grand_total, 3) }}</td>
                                    <td>
                                        <span class="discount-amount shadow-sm">-
                                            {{ number_format($contract->discount, 3) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-5 text-muted fs-5 text-center fw-bold">
                                        <i class="mdi mdi-ticket-outline fs-1 d-block mb-2 text-black-50 opacity-25"></i>
                                        لا توجد أي خصومات مالية ممنوحة للطلاب خلال هذه الفترة المحددة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ✅ تم حذف كود الـ Pagination لأنه يسبب Crash مع الـ Collection الحالية --}}
        </div>

    </div>
@endsection

@push('scripts')
    {{-- ✅ تم الاستغناء عن jQuery واستخدام Vanilla JS لتسريع الصفحة --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById('filterForm');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('.btn-submit');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري التصفية...';
                });
            }
        });
    </script>
@endpush
