@extends('backend.layouts.master')
@section('title', 'إدارة الحافلات المدرسية')
@section('title_page', 'إدارة الحركة والنقل')

@push('css')
    {{-- 🔥 مكتبات SweetAlert و Select2 و Boxicons 🔥 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* 🌟 Premium Table & Avatar 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            border-top: 5px solid #3b82f6;
            overflow: hidden;
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
            padding: 16px;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            font-size: 14px;
            color: #334155;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* 🌟 Filter Bar 🌟 */
        .filter-bar {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }

        .bus-number-badge {
            background-color: rgba(91, 105, 188, 0.1);
            color: #5b69bc;
            font-weight: 700;
            font-size: 15px;
            padding: 6px 14px;
            border-radius: 6px;
            border: 1px solid rgba(91, 105, 188, 0.2);
            letter-spacing: 1px;
        }

        /* 🆕 شارات المقاعد المتاحة */
        .seat-badge {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 8px;
            display: inline-block;
            min-width: 90px;
            text-align: center;
            font-weight: 700;
        }

        /* الأزرار الموحدة */
        .action-btn {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            font-size: 18px;
            margin: 0 3px;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .action-btn.view {
            background: #e0f2fe;
            color: #0284c7;
            border-color: #bae6fd;
        }

        .action-btn.view:hover {
            background: #0284c7;
            color: #fff;
            border-color: #0284c7;
        }

        .action-btn.edit {
            background: #fef3c7;
            color: #d97706;
            border-color: #fde68a;
        }

        .action-btn.edit:hover {
            background: #f59e0b;
            color: #fff;
            border-color: #f59e0b;
        }

        .action-btn.delete {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fecaca;
        }

        .action-btn.delete:hover {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }

        .action-btn.print {
            background: #f1f5f9;
            color: #475569;
            border-color: #cbd5e1;
        }

        .action-btn.print:hover {
            background: #475569;
            color: #fff;
            border-color: #475569;
        }

        .truncate-text {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .filter-bar {
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
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .bus-number-badge {
            background-color: rgba(114, 124, 245, 0.15);
            color: #727cf5;
            border-color: rgba(114, 124, 245, 0.3);
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 Page Header 🌟 --}}
        <div class="row mb-3 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold mb-1 text-dark">
                        <i class="bx bx-bus text-primary me-1 fs-3 align-middle"></i> إدارة الحافلات المدرسية
                    </h4>
                    <p class="text-muted mb-0 font-14">إدارة وتتبع مركبات النقل، السائقين، والمشرفين، والمسارات الأكاديمية.
                    </p>
                </div>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <div class="btn-group shadow-sm rounded-pill border bg-white">
                        <button class="btn btn-light border-0 rounded-pill-start px-3" type="button"
                            data-bs-toggle="tooltip" title="تحديث السجل" onclick="location.reload();">
                            <i class="bx bx-refresh fs-5 text-secondary align-middle"></i>
                        </button>
                        <button class="btn btn-light border-0 rounded-pill-end px-3" type="button" data-bs-toggle="tooltip"
                            title="طباعة القائمة" onclick="window.print();">
                            <i class="bx bx-printer fs-5 text-info align-middle"></i>
                        </button>
                    </div>

                    {{-- 🔥 زر إضافة حافلة محمي بالصلاحية 🔥 --}}
                    @can('manage_buses')
                        <a href="{{ route('admin.buses.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                            <i class="bx bx-plus-circle me-1 align-middle fs-5"></i> إضافة حافلة جديدة
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- 🌟 Smart Filter Bar 🌟 --}}
        <div class="filter-bar border-top border-primary border-4 shadow-sm d-print-none">
            <form action="{{ route('admin.buses.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label font-13 fw-bold text-muted mb-2">تصفية حسب الفرع</label>
                    <select name="branch_id" class="form-select select2-filter shadow-sm">
                        <option value="">كل الفروع</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-13 fw-bold text-muted mb-2">تصفية حسب السنة</label>
                    <select name="academic_year_id" class="form-select select2-filter shadow-sm">
                        <option value="">كل السنوات</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->id }}"
                                {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label font-13 fw-bold text-muted mb-2">البحث السريع المتقدم</label>
                    <div class="input-group shadow-sm rounded-3">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="bx bx-search text-primary fs-5"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0 fw-bold"
                            placeholder="ابحث برقم الباص، السائق، الخط..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100 shadow-sm fw-bold rounded-3" type="submit">بحث</button>
                    <a href="{{ route('admin.buses.index') }}" class="btn btn-light border shadow-sm rounded-3 px-3"
                        title="إعادة ضبط"><i class="bx bx-reset fs-5"></i></a>
                </div>
            </form>
        </div>

        {{-- 🌟 Alerts 🌟 --}}
        @if (session('success'))
            <div
                class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4 rounded-3 fw-bold">
                <i class="bx bx-check-circle me-1 align-middle fs-5"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div
                class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4 rounded-3 fw-bold">
                <i class="bx bx-error-circle me-1 align-middle fs-5"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 🌟 Data Table 🌟 --}}
        <div class="card card-premium shadow-sm border-0 mb-5">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-erp table-hover align-middle mb-0 text-center">
                        <thead class="sticky-top" style="z-index: 10;">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>رقم الحافلة</th>
                                <th class="text-start ps-3">الفرع والسنة</th>
                                <th class="text-start ps-3">النوع والخط</th>
                                <th class="text-center">مقاعد الذهاب (صباحاً)</th>
                                <th class="text-center">مقاعد العودة (مساءً)</th>
                                <th class="text-start ps-3">السائق والمشرف</th>
                                <th>ملاحظات</th>
                                <th style="width: 160px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buses as $index => $bus)
                                <tr>
                                    <td class="text-muted fw-bold font-monospace">{{ $buses->firstItem() + $index }}</td>

                                    {{-- رقم الحافلة --}}
                                    <td>
                                        <div class="bus-number-badge d-inline-block font-monospace">
                                            <i class="bx bx-bus fs-5 me-1 align-middle"></i>{{ $bus->bus_number }}
                                        </div>
                                    </td>

                                    {{-- الفرع والسنة الدراسية --}}
                                    <td class="text-start ps-3">
                                        <div class="font-13 fw-bold text-dark mb-1">
                                            <i class="bx bx-buildings text-muted me-1 align-middle"></i>
                                            {{ app()->getLocale() == 'ar' ? $bus->branch->name_ar ?? 'غير محدد' : $bus->branch->name_en ?? ($bus->branch->name_ar ?? 'Unspecified') }}
                                        </div>
                                        <div>
                                            <span
                                                class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 font-11 border border-primary border-opacity-25 rounded-2">
                                                <i
                                                    class="bx bx-calendar-event align-middle me-1"></i>{{ $bus->academicYear->name ?? '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- النوع والخط --}}
                                    <td class="text-start ps-3">
                                        <div class="font-13 fw-bold mb-1">
                                            <i
                                                class="bx bx-car text-muted me-1 align-middle"></i>{{ $bus->bus_type ?? 'غير محدد' }}
                                        </div>
                                        <div class="font-12 text-muted">
                                            <i class="bx bx-map-pin text-info me-1 align-middle"></i>
                                            {{ $bus->transportRoute->name ?? 'خط غير محدد' }}
                                        </div>
                                    </td>

                                    {{-- 🔥 مقاعد الذهاب (صباحاً) 🔥 --}}
                                    <td class="text-center">
                                        @if ($bus->available_morning_seats > 0)
                                            <div class="seat-badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"
                                                data-bs-toggle="tooltip" title="السعة الإجمالية: {{ $bus->capacity }}">
                                                متاح: {{ $bus->available_morning_seats }} / {{ $bus->capacity }}
                                            </div>
                                        @else
                                            <div
                                                class="seat-badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                <i class="bx bx-lock-alt align-middle"></i> ممتلئ
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 🔥 مقاعد العودة (مساءً) 🔥 --}}
                                    <td class="text-center">
                                        @if ($bus->available_afternoon_seats > 0)
                                            <div class="seat-badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"
                                                data-bs-toggle="tooltip" title="السعة الإجمالية: {{ $bus->capacity }}">
                                                متاح: {{ $bus->available_afternoon_seats }} / {{ $bus->capacity }}
                                            </div>
                                        @else
                                            <div
                                                class="seat-badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                <i class="bx bx-lock-alt align-middle"></i> ممتلئ
                                            </div>
                                        @endif
                                    </td>

                                    {{-- السائق والمشرف --}}
                                    <td class="text-start ps-3">
                                        @php
                                            $driverName = $bus->driver_id
                                                ? $bus->driver->full_name_ar ?? 'غير معروف'
                                                : $bus->driver_name ?? 'بدون سائق';
                                            $driverPhone = $bus->driver_id
                                                ? $bus->driver->phone ?? '---'
                                                : $bus->driver_phone ?? '---';
                                            $driverIconColor = $bus->driver_id ? 'text-primary' : 'text-muted';
                                        @endphp
                                        <div class="font-13 fw-bold text-dark mb-1">
                                            <i class="bx bx-steering {{ $driverIconColor }} me-1 align-middle"
                                                title="{{ $bus->driver_id ? 'سائق مسجل بالموارد البشرية' : 'سائق خارجي' }}"></i>
                                            {{ $driverName }}
                                        </div>

                                        {{-- المشرف (إذا وجد) --}}
                                        @if ($bus->supervisor_id && $bus->supervisor)
                                            <div class="border-top pt-2 mt-1">
                                                <div class="font-12 fw-bold text-info">
                                                    <i class="bx bx-user-check me-1 align-middle"></i> مشرف:
                                                    {{ $bus->supervisor->full_name_ar ?? 'غير معروف' }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- الملاحظات --}}
                                    <td>
                                        @if ($bus->notes)
                                            <span class="truncate-text text-muted font-13" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="{{ $bus->notes }}">
                                                <i
                                                    class="bx bx-note text-warning me-1 align-middle"></i>{{ $bus->notes }}
                                            </span>
                                        @else
                                            <span class="text-muted font-12">-</span>
                                        @endif
                                    </td>

                                    {{-- الإجراءات --}}
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('admin.buses.print_contract', $bus->id) }}" target="_blank"
                                                class="action-btn print shadow-sm" data-bs-toggle="tooltip"
                                                title="طباعة العقد">
                                                <i class="bx bx-printer"></i>
                                            </a>
                                            <a href="{{ route('admin.buses.show', $bus->id) }}"
                                                class="action-btn view shadow-sm" data-bs-toggle="tooltip"
                                                title="التفاصيل">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            @can('manage_buses')
                                                <a href="{{ route('admin.buses.edit', $bus->id) }}"
                                                    class="action-btn edit shadow-sm" data-bs-toggle="tooltip"
                                                    title="تعديل">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.buses.destroy', $bus->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="action-btn delete shadow-sm border-0 btn-delete-alert"
                                                        data-bs-toggle="tooltip" title="حذف/أرشفة">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 bg-white text-muted">
                                        <i class="bx bx-bus bg-light text-secondary rounded-circle p-4 mb-3 d-inline-block"
                                            style="font-size: 50px;"></i>
                                        <h5 class="fw-bold text-dark">لا توجد حافلات مسجلة حالياً</h5>
                                        <p class="font-13 mb-4">قم بإضافة أول حافلة للنظام لتظهر في هذه القائمة.</p>
                                        @can('manage_buses')
                                            <a href="{{ route('admin.buses.create') }}"
                                                class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                                <i class="bx bx-plus-circle me-1 align-middle"></i> إضافة حافلة جديدة
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 الـ Pagination مع الاحتفاظ بالفلاتر والبحث 🔥 --}}
            @if ($buses->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $buses->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            let currentLocale = "{{ app()->getLocale() }}";
            let isRtl = currentLocale === 'ar';

            // تفعيل Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // تفعيل Select2 للفلاتر
            $('.select2-filter').select2({
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // إخفاء الـ Alerts بعد 4 ثواني
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 4000);

            // 🔥 تفعيل الـ SweetAlert لزرار الحذف 🔥
            $('.btn-delete-alert').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "سيتم أرشفة هذه الحافلة. إذا كانت الحافلة مرتبطة بعقود طلاب حالية (ذهاب أو إياب)، سيرفض النظام الحذف لحماية البيانات المالية.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'نعم، قم بالحذف (الأرشفة)',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
