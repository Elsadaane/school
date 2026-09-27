@extends('backend.layouts.master')
@section('title', 'سجل فواتير وعقود الطلاب')
@section('title_page', 'الإدارة المالية - السجل الشامل للعقود')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.03);
            background-color: #fff;
        }

        .filter-label {
            font-weight: 800;
            color: #64748b;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-input {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            font-weight: 600;
            color: #334155;
            transition: all 0.3s ease;
        }

        .filter-input:focus {
            background-color: #fff;
            border-color: #004b87;
            box-shadow: 0 0 0 3px rgba(0, 75, 135, 0.1);
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
            padding: 18px 16px;
            text-transform: uppercase;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px;
            transition: background-color 0.2s ease;
        }

        .table-erp tbody tr:hover td {
            background-color: #fbfccb !important;
        }

        .contract-badge {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 14px;
            padding: 6px 14px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #0f172a;
            border: 1px dashed #cbd5e1;
            letter-spacing: 0.5px;
        }

        /* 🌟 Payment Progress Bar 🌟 */
        .payment-progress-bg {
            background-color: #e2e8f0;
            border-radius: 50px;
            height: 10px;
            width: 120px;
            margin: 8px auto 0;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .payment-progress-bar {
            height: 100%;
            border-radius: 50px;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .progress-100 {
            background-color: #10b981;
            box-shadow: 0 2px 5px rgba(16, 185, 129, 0.4);
        }

        .progress-partial {
            background-color: #f59e0b;
            box-shadow: 0 2px 5px rgba(245, 158, 11, 0.4);
        }

        .progress-0 {
            background-color: #ef4444;
        }

        .amount-text {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 17px;
            letter-spacing: -0.5px;
        }

        /* 🌟 Action Icons 🌟 */
        .action-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            color: #64748b;
            background: #fff;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .action-icon:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        }

        .action-icon.view-details {
            background: #eff6ff;
            color: #0284c7;
            border-color: #bfdbfe;
        }

        .action-icon.view-details:hover {
            background: #0284c7;
            color: #fff;
        }

        .action-icon.edit {
            background: #fef3c7;
            color: #d97706;
            border-color: #fde68a;
        }

        .action-icon.edit:hover {
            background: #f59e0b;
            color: #fff;
        }

        .action-icon.print {
            background: #f8fafc;
            color: #475569;
            border-color: #e2e8f0;
        }

        .action-icon.print:hover {
            background: #475569;
            color: #fff;
        }

        .action-icon.delete {
            background: #fef2f2;
            color: #ef4444;
            border-color: #fecaca;
        }

        .action-icon.delete:hover {
            background: #ef4444;
            color: #fff;
        }

        .action-icon.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
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
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody tr:hover td {
            background-color: #38414e !important;
        }

        body[data-layout-color="dark"] .filter-input {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .contract-badge {
            background: #424e5a;
            color: #f8fafc;
            border-color: #64748b;
        }

        body[data-layout-color="dark"] .payment-progress-bg {
            background: #424e5a;
        }

        body[data-layout-color="dark"] .action-icon {
            background: #3b4652;
            border-color: #424e5a;
            color: #adb5bd;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 الهيدر --}}
        <div class="card card-premium mb-4 border-top border-primary border-4 shadow-sm"
            style="border-top-color: #004b87 !important;">
            <div class="card-body d-flex justify-content-between align-items-center p-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary shadow-sm"
                        style="color: #004b87 !important;">
                        <i class="mdi mdi-file-document-edit-outline fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">العقود وفواتير الطلاب</h4>
                        <p class="text-muted mb-0 font-13">إدارة المديونيات، إصدار الفواتير، ومتابعة سداد الأقساط المدرسية.
                        </p>
                    </div>
                </div>

                @can('create_student_financials')
                    <div>
                        <a href="{{ route('admin.student-contracts.create') }}"
                            class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm"
                            style="background-color: #004b87; border:none;">
                            <i class="mdi mdi-plus-circle-outline font-18 align-middle me-1"></i> إنشاء عقد مالي جديد
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success shadow-sm fw-bold border-0 rounded-3"><i
                    class="mdi mdi-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm fw-bold border-0 rounded-3"><i
                    class="mdi mdi-alert-circle me-1 fs-5 align-middle"></i> {{ $errors->first() }}</div>
        @endif

        {{-- 🌟 الفلاتر العصرية --}}
        <div class="card card-premium mb-4 shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('admin.student-contracts.index') }}" method="GET" class="prevent-double-submit">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="filter-label"><i class="mdi mdi-calendar-blank text-primary me-1"></i> السنة
                                الدراسية</label>
                            <select name="academic_year_id" class="form-select filter-input shadow-sm p-2">
                                <option value="">-- عرض الكل --</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}"
                                        {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="filter-label"><i class="mdi mdi-school-outline text-primary me-1"></i> الصف
                                الدراسي</label>
                            <select name="grade_id" class="form-select filter-input shadow-sm p-2">
                                <option value="">-- عرض الكل --</option>
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade->id }}"
                                        {{ request('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="filter-label"><i class="mdi mdi-account-search-outline text-primary me-1"></i> بحث
                                متقدم</label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="mdi mdi-magnify fs-5 text-primary"></i></span>
                                <input type="text" name="search"
                                    class="form-control filter-input border-start-0 ps-0 fw-bold py-2"
                                    placeholder="بحث برقم العقد، اسم الطالب..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-3 fw-bold shadow-sm btn-submit py-2">
                                <i class="mdi mdi-filter-variant me-1"></i> تصفية النتائج
                            </button>
                            <a href="{{ route('admin.student-contracts.index') }}"
                                class="btn btn-light border rounded-3 fw-bold text-muted px-3 shadow-sm py-2"
                                data-bs-toggle="tooltip" title="إعادة ضبط الفلاتر">
                                <i class="mdi mdi-refresh fs-5 align-middle"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 جدول العقود --}}
        <div class="card card-premium shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 650px; overflow-y: auto;">
                    <table class="table table-erp text-center align-middle mb-0">
                        <thead class="sticky-top" style="z-index: 1;">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>رقم العقد</th>
                                <th class="text-start ps-4">الطالب / ولي الأمر</th>
                                <th>تاريخ الإصدار</th>
                                {{-- 🆕 عمود بيانات النقل الجديد --}}
                                <th>بيانات النقل</th>
                                <th>المديونية الشاملة (OMR)</th>
                                <th>حالة السداد</th>

                                @canany(['view_student_financials', 'edit_student_financials', 'delete_student_financials'])
                                    <th style="width: 180px;">الإجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contracts as $index => $contract)
                                @php
                                    $paidPercent =
                                        $contract->grand_total > 0
                                            ? ($contract->paid_amount / $contract->grand_total) * 100
                                            : 0;
                                    $remaining = $contract->grand_total - $contract->paid_amount;

                                    $progressClass = 'progress-0';
                                    $statusText = 'غير مسدد';
                                    $statusBadge = 'bg-danger text-white';

                                    if ($paidPercent >= 100) {
                                        $progressClass = 'progress-100';
                                        $statusText = 'خالص الأجر';
                                        $statusBadge = 'bg-success text-white';
                                    } elseif ($paidPercent > 0) {
                                        $progressClass = 'progress-partial';
                                        $statusText = 'سداد جزئي';
                                        $statusBadge = 'bg-warning text-dark';
                                    }
                                @endphp
                                <tr>
                                    <td class="text-muted fw-bold">
                                        {{ method_exists($contracts, 'firstItem') ? $contracts->firstItem() + $index : $index + 1 }}
                                    </td>

                                    <td>
                                        <span class="contract-badge shadow-sm">#{{ $contract->contract_number }}</span>
                                    </td>

                                    <td class="text-start ps-4">
                                        <div class="fw-bold text-dark fs-6 mb-1">
                                            <i class="mdi mdi-account-school text-primary me-1"></i>
                                            {{ $contract->student->first_name ?? 'غير محدد' }}
                                            {{ $contract->student->tribe ?? '' }}
                                        </div>
                                        <div class="d-flex flex-column gap-1">
                                            <small class="text-muted fw-bold font-12"><i
                                                    class="mdi mdi-barcode text-secondary me-1"></i> كود: <span
                                                    class="text-dark">{{ $contract->student->student_code ?? '---' }}</span></small>
                                            <small class="text-muted font-12"><i
                                                    class="mdi mdi-account-tie text-secondary me-1"></i> ولي الأمر:
                                                {{ $contract->student->parent->name_ar ?? '---' }}</small>
                                        </div>
                                    </td>

                                    <td dir="ltr" class="text-muted font-13 font-monospace fw-bold">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <i class="mdi mdi-calendar-check text-success"></i>
                                            {{ \Carbon\Carbon::parse($contract->contract_date)->format('Y-m-d') }}
                                        </div>
                                    </td>

                                    {{-- 🆕 عرض بيانات النقل (خط السير والباصات) --}}
                                    <td class="text-start ps-4">
                                        @if ($contract->transportRoute)
                                            <div class="fw-bold text-info fs-6 mb-1">
                                                <i class="mdi mdi-map-marker-path text-info me-1"></i>
                                                {{ $contract->transportRoute->name }}
                                            </div>
                                            <div class="d-flex flex-column gap-1">
                                                @if ($contract->morningBus)
                                                    <small class="text-muted fw-bold font-12">
                                                        <i class="mdi mdi-bus-clock text-success me-1"></i> ذهاب: <span
                                                            class="text-dark">{{ $contract->morningBus->bus_number }}</span>
                                                    </small>
                                                @endif
                                                @if ($contract->afternoonBus)
                                                    <small class="text-muted fw-bold font-12">
                                                        <i class="mdi mdi-bus-clock text-warning me-1"></i> إياب: <span
                                                            class="text-dark">{{ $contract->afternoonBus->bus_number }}</span>
                                                    </small>
                                                @endif
                                                @if (!$contract->morningBus && !$contract->afternoonBus)
                                                    <small class="text-muted font-12 fst-italic">لا يوجد باصات
                                                        محددة</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted font-12 fst-italic">لا يستخدم مواصلات</span>
                                        @endif
                                    </td>

                                    <td dir="ltr">
                                        <div class="amount-text text-primary">
                                            {{ number_format($contract->grand_total, 3) }}</div>
                                        @if ($remaining > 0)
                                            <small class="text-danger fw-bold"><i class="mdi mdi-arrow-down-right"></i>
                                                متبقي: {{ number_format($remaining, 3) }}</small>
                                        @else
                                            <small class="text-success fw-bold"><i class="mdi mdi-check-all"></i> مسدد
                                                بالكامل</small>
                                        @endif
                                    </td>

                                    <td>
                                        <span
                                            class="badge {{ $statusBadge }} rounded-pill px-3 py-1 mb-1 shadow-sm fw-bold border">{{ $statusText }}</span>
                                        <div class="d-flex align-items-center justify-content-center gap-2 mt-1">
                                            <div class="payment-progress-bg shadow-sm">
                                                <div class="payment-progress-bar {{ $progressClass }}"
                                                    style="width: {{ $paidPercent }}%;"></div>
                                            </div>
                                            <small
                                                class="text-muted font-11 fw-bold font-monospace">{{ round($paidPercent) }}%</small>
                                        </div>
                                    </td>

                                    @canany(['view_student_financials', 'edit_student_financials',
                                        'delete_student_financials'])
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center align-items-center">
                                                @can('view_student_financials')
                                                    <a href="{{ route('admin.student-contracts.payments', $contract->id) }}"
                                                        class="action-icon view-details" data-bs-toggle="tooltip"
                                                        title="إدارة الدفعات والتحصيل">
                                                        <i class="mdi mdi-cash-register font-18"></i>
                                                    </a>
                                                    <a href="{{ route('admin.student-contracts.show', $contract->id) }}"
                                                        target="_blank" class="action-icon print" data-bs-toggle="tooltip"
                                                        title="طباعة العقد المالي">
                                                        <i class="mdi mdi-printer font-18"></i>
                                                    </a>
                                                @endcan

                                                @can('edit_student_financials')
                                                    @if ($contract->paid_amount == 0)
                                                        <a href="{{ route('admin.student-contracts.edit', $contract->id) }}"
                                                            class="action-icon edit" data-bs-toggle="tooltip"
                                                            title="تعديل تفاصيل العقد">
                                                            <i class="mdi mdi-square-edit-outline font-18"></i>
                                                        </a>
                                                    @else
                                                        <button class="action-icon disabled border-0" data-bs-toggle="tooltip"
                                                            title="مغلق: تم سداد دفعات على هذا العقد" disabled>
                                                            <i class="mdi mdi-lock-outline font-18 text-muted"></i>
                                                        </button>
                                                    @endif
                                                @endcan

                                                @if ($contract->paid_amount == 0)
                                                    @can('delete_student_financials')
                                                        <form
                                                            action="{{ route('admin.student-contracts.destroy', $contract->id) }}"
                                                            method="POST" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="action-icon delete btn-delete"
                                                                data-bs-toggle="tooltip" title="حذف العقد نهائياً">
                                                                <i class="mdi mdi-trash-can-outline font-18"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    {{-- 🔄 تحديث colspan ليتناسب مع عدد الأعمدة الجديد (8 أعمدة) --}}
                                    <td colspan="8" class="text-muted py-5 fs-5 bg-white text-center fw-bold">
                                        <div class="mb-3">
                                            <i class="mdi mdi-folder-search-outline text-primary opacity-25"
                                                style="font-size: 60px;"></i>
                                        </div>
                                        لا توجد عقود أو فواتير مسجلة مطابقة لخيارات البحث.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (method_exists($contracts, 'hasPages') && $contracts->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4 shadow-sm">
                    {{ $contracts->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1 align-middle"></i> جاري التصفية...');
            });

            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'تأكيد الحذف النهائي؟',
                    text: "سيتم حذف العقد وإلغاء الأقساط تماماً، وسيتم إرجاع أي كتب أو أزياء تم صرفها إلى المخزن! لا يمكن التراجع.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف العقد',
                    cancelButtonText: 'تراجع وإلغاء',
                    borderRadius: '12px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
