@extends('backend.layouts.master')
@section('title', 'كشف حساب ولي الأمر الشامل')
@section('title_page', 'التقارير المالية - الحساب الإجمالي لأولياء الأمور')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
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

        .balance-card {
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            background: #fff;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .balance-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .balance-title {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .balance-value {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 24px;
            letter-spacing: -0.5px;
        }

        .student-block {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
        }

        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .table-erp {
            white-space: nowrap;
            margin-bottom: 0;
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
            padding: 14px;
            text-align: center;
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .balance-card,
        body[data-layout-color="dark"] .student-block {
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

        body[data-layout-color="dark"] .balance-value {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        /* 🌙 دعم القائمة المنسدلة لـ Select2 في الوضع الليلي */
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-dropdown {
            background-color: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-results__option {
            color: #fff;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-results__option--highlighted,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #424e5a !important;
            color: #fff;
        }

        /* 🖨️ تنسيق الطباعة النظيفة 🖨️ */
        @media print {
            body {
                background: #fff;
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

            .card-premium,
            .student-block {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                page-break-inside: avoid;
            }

            .balance-card {
                border: 1px solid #ddd !important;
            }

            .table-erp th,
            .table-erp td {
                border: 1px solid #ddd !important;
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

        {{-- 🌟 صندوق الفلتر والبحث --}}
        <div class="card card-premium mb-4 no-print border-top border-primary border-4 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.reports.guardian_statement') }}" method="GET" id="searchForm"
                    class="row g-3 align-items-end justify-content-center">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-2"><i
                                class="mdi mdi-account-search-outline me-1 fs-5 align-middle"></i> اختر ولي الأمر لعرض كشف
                            الحساب الموحد <span class="text-danger">*</span></label>
                        <select name="guardian_id" class="form-select select2-search" required
                            data-placeholder="-- ابحث باسم ولي الأمر أو الرقم المدني --">
                            <option value=""></option>
                            @foreach ($guardians as $g)
                                {{-- ✅ التصحيح: استخدام national_id بدلاً من civil_id --}}
                                <option value="{{ $g->id }}" {{ ($guardianId ?? '') == $g->id ? 'selected' : '' }}>
                                    {{ $g->name_ar }} {{ $g->national_id ? ' - (' . $g->national_id . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm btn-submit">
                            <i class="mdi mdi-file-find-outline me-1"></i> عرض كشف الحساب
                        </button>
                        @if (isset($statementData) && $statementData)
                            <button type="button" class="btn btn-dark btn-lg border fw-bold shadow-sm px-4"
                                onclick="window.print()" title="طباعة كشف الحساب">
                                <i class="mdi mdi-printer"></i>
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 عرض كشف الحساب --}}
        @if (isset($statementData) && $statementData)
            <div class="card card-premium p-4 p-md-5 border-top border-dark border-4 shadow-sm">

                {{-- ترويسة الحساب --}}
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4 flex-wrap gap-3">
                    <div>
                        <h3 class="fw-bold text-dark mb-2"><i
                                class="mdi mdi-file-document-text-outline text-primary me-1 fs-2 align-middle"></i> كشف حساب
                            مالي موحد</h3>
                        <p class="text-muted mb-0 font-13">صادر بتاريخ: <span dir="ltr"
                                class="fw-bold text-dark">{{ now()->format('Y-m-d H:i') }}</span> | نظام سِيدَة لإدارة
                            المدارس</p>
                    </div>
                    <div class="text-md-end bg-light p-3 rounded-3 border">
                        <h5 class="fw-bold text-primary mb-2"><i class="mdi mdi-account-tie me-1"></i>
                            {{ $statementData['guardian']->name_ar }}</h5>
                        <div class="text-muted font-monospace fw-bold font-13 mb-1"><i
                                class="mdi mdi-phone-outline me-1"></i> هاتف:
                            {{ $statementData['guardian']->phone ?? '---' }}</div>
                        {{-- ✅ التصحيح: استخدام national_id --}}
                        <div class="text-muted font-monospace fw-bold font-13"><i
                                class="mdi mdi-card-account-details-outline me-1"></i> رقم مدني:
                            {{ $statementData['guardian']->national_id ?? '---' }}</div>
                    </div>
                </div>

                {{-- 🌟 كروت ملخص الحساب --}}
                <div class="row g-3 mb-5">
                    <div class="col-md-4">
                        <div class="balance-card shadow-sm border-start border-primary border-4">
                            <div class="balance-title text-primary">إجمالي الرسوم المطلوبة</div>
                            <div class="balance-value text-dark">{{ number_format($statementData['total_debt'] ?? 0, 3) }}
                                <small class="text-muted font-14 font-sans fw-bold">OMR</small></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="balance-card shadow-sm border-start border-success border-4"
                            style="background-color: #f0fdf4; border-color: #bbf7d0;">
                            <div class="balance-title text-success">إجمالي المبالغ المسددة</div>
                            <div class="balance-value text-success">
                                {{ number_format($statementData['total_paid'] ?? 0, 3) }} <small
                                    class="font-14 font-sans fw-bold">OMR</small></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="balance-card shadow-sm border-start border-danger border-4"
                            style="background-color: #fef2f2; border-color: #fecaca;">
                            <div class="balance-title text-danger">صافي المديونية المتبقية</div>
                            <div class="balance-value text-danger">{{ number_format($statementData['remaining'] ?? 0, 3) }}
                                <small class="font-14 font-sans fw-bold">OMR</small></div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-4"><i class="mdi mdi-account-child text-info me-1 fs-4 align-middle"></i>
                    تفاصيل الحساب المالي لكل تابع (الأبناء):</h5>

                {{-- 🌟 حلقة تكرار الأبناء وعقودهم --}}
                @forelse ($statementData['guardian']->students ?? [] as $student)
                    <div class="student-block shadow-sm">
                        <div class="student-header flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <i
                                    class="mdi mdi-face-man-profile text-primary fs-2 me-3 bg-primary bg-opacity-10 rounded-circle p-2"></i>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 fs-5">{{ $student->first_name }}
                                        {{ $student->tribe }}</h6>
                                    <span class="text-muted fw-bold font-13"><i class="mdi mdi-school-outline me-1"></i>
                                        {{ $student->grade->name_ar ?? 'الصف غير محدد' }}</span>
                                </div>
                            </div>
                            <span class="badge bg-light text-dark border font-13 px-4 py-2 shadow-sm"><i
                                    class="mdi mdi-file-document-multiple-outline me-1"></i> عدد العقود المسجلة:
                                {{ $student->contracts->count() }}</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-erp table-hover text-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-start ps-3">رقم العقد</th>
                                        <th>تاريخ العقد</th>
                                        <th>السنة الدراسية</th>
                                        <th class="bg-primary bg-opacity-10 text-primary border-bottom-0">قيمة العقد الشاملة
                                        </th>
                                        <th class="bg-success bg-opacity-10 text-success border-bottom-0">الرصيد المسدد</th>
                                        <th class="bg-danger bg-opacity-10 text-danger border-bottom-0">المتبقي للابن</th>
                                        <th class="no-print border-bottom-0">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($student->contracts as $contract)
                                        <tr>
                                            <td class="font-monospace fw-bold text-start ps-3 text-dark">
                                                #{{ $contract->contract_number }}</td>
                                            <td class="text-muted font-monospace">
                                                {{ \Carbon\Carbon::parse($contract->contract_date)->format('Y-m-d') }}</td>
                                            <td class="fw-bold">{{ $contract->academicYear->name ?? '---' }}</td>
                                            <td class="font-monospace text-primary fw-bolder fs-6">
                                                {{ number_format($contract->grand_total, 3) }}</td>
                                            <td class="font-monospace text-success fw-bolder fs-6">
                                                {{ number_format($contract->paid_amount, 3) }}</td>
                                            {{-- ✅ التصحيح: استخدام حقل total_remaining الجاهز من قاعدة البيانات --}}
                                            <td class="font-monospace text-danger fw-bolder fs-6">
                                                {{ number_format($contract->total_remaining, 3) }}</td>
                                            <td class="no-print">
                                                <a href="{{ route('admin.student-contracts.payments', $contract->id) }}"
                                                    class="btn btn-sm btn-outline-dark rounded-pill px-3 shadow-sm fw-bold"
                                                    title="عرض وسداد الأقساط">
                                                    <i class="mdi mdi-cash-register me-1"></i> تفاصيل
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-4 text-muted fw-bold bg-white">
                                                <i
                                                    class="mdi mdi-text-box-remove-outline fs-3 d-block mb-1 opacity-50"></i>
                                                لا توجد عقود مالية مسجلة لهذا الطالب حالياً.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning text-center py-4 border-0 shadow-sm fw-bold">
                        <i class="mdi mdi-alert-circle-outline fs-3 d-block mb-2"></i> لا يوجد أبناء مسجلين أو مرتبطين بولي
                        الأمر هذا.
                    </div>
                @endforelse

                {{-- تذييل قانوني --}}
                <div class="row mt-5 pt-4 border-top text-center font-14 fw-bold text-dark d-none d-print-flex">
                    <div class="col-4"><strong>توقيع المحاسب
                            المختص:</strong><br><br>......................................</div>
                    <div class="col-4"><strong>الختم الرسمي للمدرسة:</strong><br><br></div>
                    <div class="col-4"><strong>إقرار وتوقيع ولي
                            الأمر:</strong><br><br>......................................</div>
                </div>
            </div>
        @else
            <div class="card card-premium text-center py-5 border-top border-primary border-4 shadow-sm mt-4">
                <div class="card-body py-5">
                    <i class="mdi mdi-file-account-outline text-primary opacity-25" style="font-size: 80px;"></i>
                    <h4 class="fw-bold text-dark mt-3">لم يتم اختيار ولي أمر بعد</h4>
                    <p class="text-muted font-14">يرجى تحديد اسم ولي الأمر أو البحث بالرقم المدني من القائمة العلوية لتوليد
                        كشف الحساب المالي الموحد.</p>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل Select2
            const select2Element = document.querySelector('.select2-search');
            if (select2Element && typeof jQuery !== 'undefined') {
                $('.select2-search').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? "rtl" : "ltr",
                    width: '100%',
                    allowClear: true
                });
            }

            // ✅ حماية الفورم من الإرسال المزدوج (Vanilla JS)
            const form = document.getElementById('searchForm');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('.btn-submit');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري استخراج الحساب...';
                });
            }
        });
    </script>
@endpush
