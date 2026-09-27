@extends('backend.layouts.master')
@section('title', 'التقرير المالي للطلاب')
@section('title_page', 'الحسابات والمستحقات - مديونيات الطلاب')

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

        .summary-widget {
            padding: 18px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .summary-widget:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05) !important;
        }

        .summary-widget .icon-box {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .amount-monospace {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 16px;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #64748b;
            background: #f1f5f9;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
        }

        .action-icon.whatsapp {
            background: #e8fbf3;
            color: #10b981;
        }

        .action-icon.whatsapp:hover {
            background: #10b981;
            color: #fff;
        }

        .debtor-row {
            background-color: rgba(239, 68, 68, 0.02);
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .summary-widget {
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
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .debtor-row {
            background-color: rgba(239, 68, 68, 0.1);
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .action-icon {
            background-color: #3b4652;
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .action-icon.whatsapp {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
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
            .footer,
            .action-icon {
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

            .summary-widget {
                break-inside: avoid;
                border: 1px solid #ccc !important;
            }

            .table-erp th,
            .table-erp td {
                border: 1px solid #ccc !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none flex-wrap gap-3">
            <h4 class="page-title font-bold mb-0 text-dark">
                <i class="mdi mdi-cash-register text-primary me-1 fs-4 align-middle"></i> التقرير المالي الشامل للطلاب
            </h4>
        </div>

        {{-- 🌟 الفلتر المالي --}}
        <div class="card card-premium mb-4 border-top border-primary border-4 shadow-sm d-print-none">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.students_financial') }}" method="GET" id="filterForm"
                    class="row g-3 align-items-end prevent-double-submit">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">عقود من تاريخ</label>
                        {{-- ✅ تأمين التواريخ بـ ?? '' --}}
                        <input type="date" name="start_date"
                            class="form-control form-control-lg bg-light border-0 text-center fw-bold shadow-sm"
                            value="{{ request('start_date', $startDate ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted font-12 text-uppercase">إلى تاريخ</label>
                        <input type="date" name="end_date"
                            class="form-control form-control-lg bg-light border-0 text-center fw-bold shadow-sm"
                            value="{{ request('end_date', $endDate ?? '') }}" required>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm btn-submit">
                            <i class="mdi mdi-filter-outline me-1"></i> تصفية وتحديث
                        </button>
                        <button type="button" class="btn btn-dark border btn-lg px-4 shadow-sm" onclick="window.print()"
                            title="طباعة التقرير">
                            <i class="mdi mdi-printer"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @php
            $totalContracts = $students->sum('total_contracts_value');
            $totalCollected = $students->sum('total_paid');
            $totalOutstanding = $totalContracts - $totalCollected;
        @endphp

        <div class="row g-4 mb-4">
            {{-- 🌟 يسار الشاشة: كروت الملخص المالي السريع --}}
            <div class="col-xl-4">
                <div class="card card-premium p-4 h-100 d-flex flex-column gap-3 shadow-sm border-top border-dark border-4">
                    <h5 class="fw-bold text-dark mb-2"><i class="mdi mdi-chart-pie text-primary me-1 fs-4 align-middle"></i>
                        ملخص مديونيات الطلاب</h5>

                    <div class="summary-widget d-flex align-items-center gap-3 shadow-sm">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary"><i
                                class="mdi mdi-file-document-outline"></i></div>
                        <div>
                            <small class="text-muted fw-bold d-block">إجمالي قيمة العقود</small>
                            <span class="amount-monospace text-dark fs-5">{{ number_format($totalContracts ?? 0, 3) }}
                                OMR</span>
                        </div>
                    </div>

                    <div class="summary-widget d-flex align-items-center gap-3 shadow-sm">
                        <div class="icon-box bg-success bg-opacity-10 text-success"><i class="mdi mdi-cash-check"></i></div>
                        <div>
                            <small class="text-muted fw-bold d-block">إجمالي المبالغ المحصلة</small>
                            <span class="amount-monospace text-success fs-5">{{ number_format($totalCollected ?? 0, 3) }}
                                OMR</span>
                        </div>
                    </div>

                    {{-- ✅ تم استبدال Inline Style بـ Bootstrap Classes لتتوافق مع Dark Mode --}}
                    <div
                        class="summary-widget d-flex align-items-center gap-3 shadow-sm border-danger border-opacity-25 bg-danger bg-opacity-10 mt-auto">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger bg-white"><i
                                class="mdi mdi-alert-circle-outline"></i></div>
                        <div>
                            <small class="text-danger fw-bold d-block">إجمالي المديونيات المعلقة</small>
                            <span class="amount-monospace text-danger fs-5">{{ number_format($totalOutstanding ?? 0, 3) }}
                                OMR</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🌟 يمين الشاشة: قائمة الـ 10 طلاب الأعلى مديونية --}}
            <div class="col-xl-8">
                <div class="card card-premium h-100 shadow-sm border-top border-danger border-4">
                    <div
                        class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center rounded-top-4">
                        <h5 class="fw-bold text-danger mb-0"><i
                                class="mdi mdi-account-alert-outline me-1 fs-4 align-middle"></i> الطلاب الأكثر مديونية
                            (متابعة عاجلة)</h5>
                        <span class="badge bg-danger rounded-pill shadow-sm px-3 py-1">{{ count($topDebtors ?? []) }}
                            حالات</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 340px; overflow-y: auto;">
                            <table class="table table-erp mb-0 text-center align-middle">
                                <thead class="sticky-top" style="z-index: 1;">
                                    <tr>
                                        <th class="text-start ps-4">اسم الطالب</th>
                                        <th>الصف</th>
                                        <th>المتبقي عليه</th>
                                        <th class="d-print-none">تذكير ولي الأمر</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topDebtors ?? [] as $debtor)
                                        <tr class="debtor-row">
                                            <td class="text-start ps-4 fw-bold text-dark">
                                                <i class="mdi mdi-account-cancel-outline text-danger opacity-50 me-1"></i>
                                                {{ $debtor['name'] }}
                                            </td>
                                            {{-- ✅ إزالة bg-white لتتوافق مع Dark Mode --}}
                                            <td><span
                                                    class="badge bg-light text-dark border shadow-sm px-2 py-1">{{ $debtor['grade'] }}</span>
                                            </td>
                                            <td class="amount-monospace text-danger fw-bold fs-6">
                                                {{ number_format($debtor['debt'], 3) }} OMR
                                            </td>
                                            <td class="d-print-none">
                                                @if (!empty($debtor['parent_phone']))
                                                    @php
                                                        $msg =
                                                            'الفاضل ولي أمر الطالب: ' .
                                                            $debtor['name'] .
                                                            '، نود تذكيركم بوجود مستحقات مالية متأخرة لنظام سِيدَة بقيمة ' .
                                                            number_format($debtor['debt'], 3) .
                                                            ' ر.ع. يرجى السداد في أقرب وقت لتفادي تعليق الخدمات. شكراً لكم.';
                                                        $cleanPhone = preg_replace(
                                                            '/[^0-9\+]/',
                                                            '',
                                                            $debtor['parent_phone'],
                                                        );
                                                        $whatsappUrl =
                                                            'https://wa.me/' . $cleanPhone . '?text=' . urlencode($msg);
                                                    @endphp
                                                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                                                        class="action-icon whatsapp shadow-sm"
                                                        title="إرسال تذكير عبر الواتساب">
                                                        <i class="mdi mdi-whatsapp font-18"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted font-12 fw-bold">لا يوجد هاتف</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        {{-- ✅ إزالة bg-white لتتوافق مع Dark Mode --}}
                                        <tr>
                                            <td colspan="4" class="py-5 text-success fw-bold text-center fs-5">
                                                <i class="mdi mdi-check-decagram fs-1 d-block mb-2 opacity-50"></i> ممتاز!
                                                لا توجد مديونيات معلقة حالياً ✔️
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

        {{-- 🌟 الجدول الشامل لجميع الطلاب وعقودهم --}}
        <div class="card card-premium shadow-sm border-top border-dark border-4">
            <div class="card-header bg-white border-bottom p-4 rounded-top-4">
                <h5 class="fw-bold text-dark mb-0"><i
                        class="mdi mdi-format-list-bulleted text-primary me-1 fs-4 align-middle"></i> كشف المستحقات الشامل
                    لطلاب المدرسة</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-erp text-center align-middle mb-0">
                        <thead class="sticky-top" style="z-index: 1;">
                            <tr>
                                <th>#</th>
                                <th class="text-start ps-4">اسم الطالب</th>
                                <th>ولي الأمر</th>
                                <th>إجمالي المستحق (العقود)</th>
                                <th>إجمالي المدفوع</th>
                                <th>المتبقي</th>
                                <th>حالة الحساب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students ?? [] as $idx => $student)
                                @php
                                    $studentRemaining =
                                        ($student->total_contracts_value ?? 0) - ($student->total_paid ?? 0);
                                @endphp
                                <tr>
                                    <td class="text-muted fw-bold">
                                        {{ isset($students) && method_exists($students, 'firstItem') ? $students->firstItem() + $idx : $idx + 1 }}
                                    </td>
                                    <td class="text-start ps-4 fw-bold text-dark">
                                        {{ $student->first_name }} {{ $student->tribe }}
                                        <small class="text-muted d-block font-12 mt-1"><i
                                                class="mdi mdi-school-outline me-1"></i>{{ $student->grade->name_ar ?? 'غير محدد' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $student->parent->name_ar ?? '---' }}</div>
                                        <small class="text-muted font-monospace"><i
                                                class="mdi mdi-phone-outline me-1"></i>{{ $student->parent->phone ?? '---' }}</small>
                                    </td>
                                    <td class="amount-monospace text-dark fs-6">
                                        {{ number_format($student->total_contracts_value ?? 0, 3) }}</td>
                                    <td class="amount-monospace text-success fs-6">
                                        {{ number_format($student->total_paid ?? 0, 3) }}</td>
                                    <td
                                        class="amount-monospace fs-6 {{ $studentRemaining > 0 ? 'text-danger fw-bolder bg-danger bg-opacity-10' : 'text-muted' }}">
                                        {{ number_format($studentRemaining, 3) }}
                                    </td>
                                    <td>
                                        {{-- ✅ تحسين المنطق: إذا لم يكن هناك عقود ولا مدفوعات، اعرض "لا توجد حركة" --}}
                                        @if (($student->total_contracts_value ?? 0) == 0 && ($student->total_paid ?? 0) == 0)
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill shadow-sm">لا
                                                توجد حركة</span>
                                        @elseif($studentRemaining <= 0)
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill shadow-sm"><i
                                                    class="mdi mdi-check-all me-1"></i>خالص</span>
                                        @else
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill shadow-sm"><i
                                                    class="mdi mdi-alert-circle-outline me-1"></i>مطلوب سداد</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                {{-- ✅ إزالة bg-white لتتوافق مع Dark Mode --}}
                                <tr>
                                    <td colspan="7" class="py-5 text-muted fs-5 fw-bold text-center">
                                        <i
                                            class="mdi mdi-account-search-outline fs-1 d-block mb-2 opacity-25 text-dark"></i>
                                        لا توجد بيانات طلاب أو سجلات مالية مطابقة للبحث.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (isset($students) && method_exists($students, 'hasPages') && $students->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $students->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    {{-- ✅ تم الاستغناء عن jQuery وتحويل الحماية لـ Vanilla JS --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById('filterForm');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('.btn-submit');
                    btn.disabled = true;
                    btn.innerHTML =
                        '<i class="mdi mdi-loading mdi-spin me-2 fs-5 align-middle"></i> جاري التحليل والتحديث...';
                });
            }
        });
    </script>
@endpush
