@extends('backend.layouts.master')
@section('title', 'الطلاب المعلقين مالياً (تجديد العقود)')
@section('title_page', 'الإدارة المالية - تجديد العقود السنوية')

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

        .pagination {
            margin-bottom: 0;
            justify-content: center;
            gap: 5px;
        }

        .page-item.active .page-link {
            background-color: #004b87;
            border-color: #004b87;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 75, 135, 0.2);
        }

        .page-item .page-link {
            color: #475569;
            font-weight: 700;
            border-radius: 8px !important;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            transition: all 0.2s;
        }

        .page-item .page-link:hover {
            background-color: #f1f5f9;
            color: #004b87;
            transform: translateY(-2px);
        }

        .page-item.disabled .page-link {
            background-color: #f8fafc;
            color: #cbd5e1;
            border-color: #e2e8f0;
        }

        /* 🆕 كرت معلومات النقل السابقة */
        .transport-history-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 8px 12px;
            margin-top: 8px;
            font-size: 11px;
        }

        .transport-history-card .transport-icon {
            color: #0284c7;
            font-size: 14px;
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

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }

        body[data-layout-color="dark"] .transport-history-card {
            background: #1e293b;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .transport-history-card .transport-icon {
            color: #38bdf8;
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
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger shadow-sm">
                        <i class="mdi mdi-account-clock-outline fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">الطلاب المعلقين وتجديد العقود</h4>
                        <p class="text-muted mb-0 font-13">قائمة بالطلاب المقيدين والذين لم يتم إصدار عقد مالي لهم في العام
                            الدراسي المحدد.</p>
                    </div>
                </div>
                <a href="{{ route('admin.student-contracts.index') }}"
                    class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm border">
                    <i class="mdi mdi-arrow-left me-1 align-middle"></i> العودة لسجل العقود
                </a>
            </div>
        </div>

        {{-- 🌟 كارت الفلترة 🌟 --}}
        <div class="card card-premium shadow-sm border-0 mb-4">
            <div class="card-body p-4 bg-light bg-opacity-50 rounded-3">
                <form action="{{ route('admin.student-contracts.pending_renewals') }}" method="GET"
                    class="prevent-double-submit">
                    <div class="row g-3 align-items-end">
                        {{-- 🆕 فلتر الفرع --}}
                        <div class="col-md-2">
                            <label class="filter-label"><i class="mdi mdi-store text-primary me-1"></i> الفرع</label>
                            <select name="branch_id" class="form-select filter-input shadow-sm p-2"
                                onchange="this.form.submit()">
                                <option value="">-- الكل --</option>
                                @foreach ($branches ?? [] as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="filter-label"><i class="mdi mdi-calendar-clock text-primary me-1"></i> العام
                                الدراسي <span class="text-danger">*</span></label>
                            <select name="academic_year_id" class="form-select filter-input shadow-sm p-2" required
                                onchange="this.form.submit()">
                                <option value="" disabled>-- اختر السنة --</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $targetYearId == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 🆕 فلتر نوع النقل --}}
                        <div class="col-md-2">
                            <label class="filter-label"><i class="mdi mdi-bus text-primary me-1"></i> نوع النقل</label>
                            <select name="transport_type" class="form-select filter-input shadow-sm p-2"
                                onchange="this.form.submit()">
                                <option value="">-- الكل --</option>
                                <option value="private" {{ request('transport_type') == 'private' ? 'selected' : '' }}>خاص
                                </option>
                                <option value="bus_go" {{ request('transport_type') == 'bus_go' ? 'selected' : '' }}>ذهاب
                                    فقط</option>
                                <option value="bus_return"
                                    {{ request('transport_type') == 'bus_return' ? 'selected' : '' }}>إياب فقط</option>
                                <option value="bus_both" {{ request('transport_type') == 'bus_both' ? 'selected' : '' }}>
                                    ذهاب وإياب</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="filter-label"><i class="mdi mdi-magnify text-primary me-1"></i> بحث سريع</label>
                            <input type="text" class="form-control filter-input shadow-sm py-2" name="search"
                                placeholder="رقم الهوية أو اسم الطالب..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary fw-bold flex-grow-1 shadow-sm btn-submit py-2"
                                style="background-color: #004b87; border:none;">
                                <i class="mdi mdi-filter-variant me-1 align-middle"></i> تصفية النتائج
                            </button>
                            <a href="{{ route('admin.student-contracts.pending_renewals') }}"
                                class="btn btn-light border text-muted shadow-sm px-3 py-2" data-bs-toggle="tooltip"
                                title="إعادة ضبط الفلاتر">
                                <i class="mdi mdi-refresh fs-5 align-middle"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 جدول الطلاب المعلقين 🌟 --}}
        <div class="card card-premium shadow-sm border-0 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="mdi mdi-account-alert text-warning me-1 fs-4 align-middle"></i> قائمة الطلاب بحاجة لتجديد
                    العقد
                </h5>
                <span class="badge bg-danger rounded-pill px-3 py-2 fs-6 shadow-sm">{{ $students->total() }} طالب
                    معلق</span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-erp table-hover text-center align-middle mb-0">
                        <thead class="sticky-top" style="z-index: 10;">
                            <tr>
                                <th style="width: 4%">#</th>
                                <th class="text-start ps-4" style="width: 22%">اسم الطالب / الرقم المدني</th>
                                <th style="width: 14%">ولي الأمر</th>
                                <th style="width: 12%">الفرع والصف</th>
                                <th style="width: 12%">نوع النقل</th>
                                <th style="width: 14%">الموقف المالي السابق</th>
                                <th style="width: 12%">إجراء الإدارة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                                @php
                                    // 🆕 جلب آخر عقد سابق للطالب (لعرض معلومات النقل السابقة)
                                    $lastContract = $student->contracts
                                        ->where('academic_year_id', '!=', $targetYearId)
                                        ->where('contract_status', 'active')
                                        ->sortByDesc('contract_date')
                                        ->first();
                                @endphp

                                <tr>
                                    <td class="fw-bold text-muted">
                                        {{ method_exists($students, 'firstItem') ? $students->firstItem() + $index : $index + 1 }}
                                    </td>

                                    <td class="text-start ps-4">
                                        <div class="fw-bold text-dark fs-6 mb-1">
                                            <i class="mdi mdi-account-school text-primary me-1"></i>
                                            {{ $student->first_name }} {{ $student->second_name ?? '' }}
                                            {{ $student->tribe }}
                                        </div>
                                        <small class="text-muted fw-bold">
                                            <i class="mdi mdi-identifier text-secondary"></i>
                                            {{ $student->parent->national_id ?? 'بدون رقم مدني' }}
                                        </small>
                                    </td>

                                    <td class="fw-bold text-dark">
                                        <i class="mdi mdi-account-tie text-muted me-1"></i>
                                        {{ $student->parent->name_ar ?? '---' }}
                                        @if ($student->parent->phone)
                                            <br><small class="text-muted font-monospace" dir="ltr">
                                                <i class="mdi mdi-phone text-success"></i> {{ $student->parent->phone }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="fw-bold text-dark">{{ $student->branch->name_ar ?? '---' }}</div>
                                        <span class="badge bg-light text-secondary border px-2 mt-1">
                                            {{ $student->grade->name_ar ?? '---' }}
                                        </span>
                                        @if ($student->section)
                                            <br><small class="text-muted">{{ $student->section->name_ar }}</small>
                                        @endif
                                    </td>

                                    {{-- 🆕 عمود نوع النقل مع معلومات النقل السابقة --}}
                                    <td>
                                        @php
                                            $transportLabels = [
                                                'private' => ['خاص', 'secondary', 'mdi-car'],
                                                'bus_go' => ['ذهاب فقط', 'info', 'mdi-arrow-right'],
                                                'bus_return' => ['إياب فقط', 'warning', 'mdi-arrow-left'],
                                                'bus_both' => ['ذهاب وإياب', 'success', 'mdi-swap-horizontal'],
                                            ];
                                            $transportType = $student->transport_type ?? 'private';
                                            $transportInfo = $transportLabels[$transportType] ?? [
                                                'غير محدد',
                                                'secondary',
                                                'mdi-help',
                                            ];
                                        @endphp
                                        <span
                                            class="badge bg-{{ $transportInfo[1] }} bg-opacity-10 text-{{ $transportInfo[1] }} border border-{{ $transportInfo[1] }} border-opacity-25 px-2 py-1 rounded-pill">
                                            <i class="mdi {{ $transportInfo[2] }} align-middle me-1"></i>
                                            {{ $transportInfo[0] }}
                                        </span>

                                        {{-- 🆕 عرض معلومات النقل السابقة --}}
                                        @if ($lastContract && $lastContract->transportRoute)
                                            <div class="transport-history-card">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="mdi mdi-history transport-icon"></i>
                                                    <div class="text-start">
                                                        <div class="fw-bold text-dark" style="font-size: 11px;">
                                                            <i class="mdi mdi-map-marker-path text-info"></i>
                                                            {{ Str::limit($lastContract->transportRoute->name, 25) }}
                                                        </div>
                                                        @if ($lastContract->morningBus || $lastContract->afternoonBus)
                                                            <div class="text-muted" style="font-size: 10px;">
                                                                @if ($lastContract->morningBus)
                                                                    <i class="mdi mdi-weather-sunny text-success"></i>
                                                                    {{ $lastContract->morningBus->bus_number }}
                                                                @endif
                                                                @if ($lastContract->afternoonBus)
                                                                    <i class="mdi mdi-weather-sunset text-warning"></i>
                                                                    {{ $lastContract->afternoonBus->bus_number }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 🔥 اللمسة المحاسبية: كشف المديونية القديمة 🔥 --}}
                                    <td>
                                        @if ($student->total_old_debt > 0)
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill fw-bold"
                                                data-bs-toggle="tooltip"
                                                title="هذا الطالب لديه متأخرات مالية من عقود سابقة">
                                                <i class="mdi mdi-alert-circle-outline fs-6 align-middle me-1"></i>
                                                مديونية:
                                                {{ number_format($student->total_old_debt, 3) }} OMR
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                                                <i class="mdi mdi-check-circle-outline fs-6 align-middle me-1"></i> خالص
                                                مالياً
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{-- 🌟 إنشاء العقد مع نقل إعدادات المواصلات 🌟 --}}
                                        @can('create_student_financials')
                                            <a href="{{ route('admin.student-contracts.create', [
                                                'student_id' => $student->id,
                                                'academic_year_id' => $targetYearId,
                                                'copy_transport' => $lastContract->id ?? null,
                                            ]) }}"
                                                class="btn btn-sm btn-primary fw-bold px-3 shadow-sm rounded-pill"
                                                style="background-color: #004b87; border:none;" data-bs-toggle="tooltip"
                                                title="إصدار عقد جديد مع نقل إعدادات المواصلات السابقة">
                                                <i class="mdi mdi-file-plus align-middle fs-5 me-1"></i> إصدار عقد
                                            </a>

                                            @if ($lastContract && $lastContract->transportRoute)
                                                <div class="mt-1">
                                                    <small class="text-info font-11">
                                                        <i class="mdi mdi-content-copy"></i> سيتم نقل المواصلات
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            <button
                                                class="btn btn-sm btn-light border text-muted fw-bold px-3 rounded-pill shadow-sm"
                                                disabled data-bs-toggle="tooltip" title="لا تملك صلاحية إنشاء العقود">
                                                <i class="mdi mdi-lock-outline"></i> محمي
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-5 text-center bg-white">
                                        <div class="avatar-xl mx-auto mb-3 d-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle"
                                            style="width: 90px; height: 90px;">
                                            <i class="mdi mdi-check-decagram-outline text-success"
                                                style="font-size: 50px;"></i>
                                        </div>
                                        <h5 class="text-success fw-bold">ممتاز! النظام محدث بالكامل.</h5>
                                        <p class="text-muted mb-0">لا يوجد أي طلاب مقيدين معلقين مالياً أو بدون عقود لهذا
                                            العام الدراسي.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 الحفاظ على الفلاتر والترقيم 🔥 --}}
            @if (method_exists($students, 'hasPages') && $students->hasPages())
                <div
                    class="card-footer bg-white border-top border-light pt-4 pb-4 rounded-bottom-4 shadow-sm d-flex justify-content-center">
                    {{ $students->appends(request()->query())->links() }}
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
            // تفعيل Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // تأمين الفورم ضد الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1 align-middle"></i> جاري التصفية...');
            });
        });
    </script>
@endpush
