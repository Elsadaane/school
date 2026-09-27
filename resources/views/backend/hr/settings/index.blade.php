@extends('backend.layouts.master')
@section('title', 'الهيكل التنظيمي والإعدادات')
@section('title_page', 'الموارد البشرية - الإعدادات والهيكل (HR Setup)')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .page-wrapper {
            background-color: #f8f9fa;
            padding: 25px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* تحسين التابات لتتناسب مع الشاشات الصغيرة (Mobile Scroll) */
        .nav-pills-custom {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            padding-bottom: 5px;
            /* لمساحة السكرول */
        }

        .nav-pills-custom::-webkit-scrollbar {
            height: 4px;
        }

        .nav-pills-custom::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        .nav-pills-custom .nav-link {
            color: #6c757d;
            font-weight: 700;
            padding: 15px 25px;
            border-radius: 12px;
            margin-right: 10px;
            transition: 0.3s;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            border: 1px solid #eee;
        }

        .nav-pills-custom .nav-link.active {
            background-color: #4e54c8;
            color: #fff;
            box-shadow: 0 5px 15px rgba(78, 84, 200, 0.3);
            border-color: #4e54c8;
            transform: translateY(-2px);
        }

        .nav-pills-custom .nav-link i {
            font-size: 1.2rem;
            vertical-align: middle;
            margin-left: 5px;
        }

        .card-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-top: 20px;
        }

        .card-header-custom {
            background-color: #fff;
            border-bottom: 2px solid #f1f4f8;
            padding: 20px 25px;
        }

        .table-custom {
            white-space: nowrap;
            /* منع تكسير النصوص في الجداول */
        }

        .table-custom thead th {
            background-color: #f8fafd;
            color: #333;
            font-weight: 700;
            padding: 15px;
            border: none;
            font-size: 14px;
            text-align: center;
        }

        .table-custom tbody td {
            vertical-align: middle;
            padding: 15px;
            border-bottom: 1px dashed #eee;
            font-weight: 600;
            color: #555;
            text-align: center;
        }

        .table-custom tbody tr:hover td {
            background-color: #fcfcfc;
        }

        .btn-add {
            border-radius: 10px;
            font-weight: bold;
            padding: 10px 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- Tabs Navigation --}}
        <ul class="nav nav-pills nav-pills-custom mb-4" id="hr-settings-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#departments-tab" type="button">
                    <i class='mdi mdi-office-building-outline'></i> أقسام وإدارات الشركة
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#shifts-tab" type="button">
                    <i class='mdi mdi-clock-outline'></i> ورديات ومواعيد العمل
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#grades-tab" type="button">
                    <i class='mdi mdi-medal-outline'></i> الدرجات الوظيفية والرواتب
                </button>
            </li>
        </ul>

        {{-- Tabs Content --}}
        <div class="tab-content" id="hr-settings-tabContent">

            {{-- 1. Departments Tab --}}
            <div class="tab-pane fade show active" id="departments-tab" role="tabpanel">
                <div class="card card-custom">
                    <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold text-dark"><i
                                class='mdi mdi-layers-outline text-primary me-2 align-middle fs-4'></i> الهيكل الإداري
                            والأقسام</h5>
                        @can('manage_hr_settings')
                            <button class="btn btn-primary btn-add rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#addDepartmentModal">
                                <i class='mdi mdi-plus me-1'></i> إضافة قسم جديد
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover w-100 mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-start ps-4">اسم القسم (عربي)</th>
                                        <th>الاسم (إنجليزي)</th>
                                        <th>تعداد الموظفين بالقسم</th>
                                        <th>تاريخ الإنشاء</th>
                                        @can('manage_hr_settings')
                                            <th>إجراءات</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($departments as $dept)
                                        <tr>
                                            <td class="text-muted fw-bold">{{ $loop->iteration }}</td>
                                            <td class="text-start ps-4 fw-bolder text-primary">{{ $dept->name_ar }}</td>
                                            <td class="text-muted">{{ $dept->name_en ?? '---' }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 fs-6 rounded-pill shadow-sm">
                                                    {{ $dept->employees_count }} موظف
                                                </span>
                                            </td>
                                            <td class="font-monospace text-muted">
                                                {{ $dept->created_at ? $dept->created_at->format('Y-m-d') : '-' }}</td>

                                            @can('manage_hr_settings')
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button
                                                            class="btn btn-sm btn-outline-primary rounded-circle shadow-sm btn-edit-dept"
                                                            data-id="{{ $dept->id }}" data-ar="{{ $dept->name_ar }}"
                                                            data-en="{{ $dept->name_en }}" data-bs-toggle="modal"
                                                            data-bs-target="#editDepartmentModal" title="تعديل">
                                                            <i class='mdi mdi-pencil-outline font-16'></i>
                                                        </button>
                                                        <form
                                                            action="{{ route('admin.hr.settings.departments.destroy', $dept->id) }}"
                                                            method="POST" class="d-inline delete-form">
                                                            @csrf @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger rounded-circle shadow-sm btn-delete"
                                                                title="حذف">
                                                                <i class='mdi mdi-trash-can-outline font-16'></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ Auth::user()->can('manage_hr_settings') ? 6 : 5 }}"
                                                class="p-5 text-muted fw-bold text-center">
                                                <i
                                                    class='mdi mdi-office-building-outline fs-1 opacity-50 d-block mb-2 text-primary'></i>
                                                لا توجد أقسام مسجلة حتى الآن.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Shifts Tab --}}
            <div class="tab-pane fade" id="shifts-tab" role="tabpanel">
                <div class="card card-custom">
                    <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold text-dark"><i
                                class='mdi mdi-clock-outline text-primary me-2 align-middle fs-4'></i> مواعيد وورديات العمل
                            الرسمية</h5>
                        @can('manage_hr_settings')
                            <button class="btn btn-primary btn-add rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#addShiftModal">
                                <i class='mdi mdi-plus me-1'></i> إضافة وردية
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover w-100 mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-start ps-4">اسم الوردية</th>
                                        <th>وقت الحضور</th>
                                        <th>وقت الانصراف</th>
                                        <th>فترة السماح (تأخير)</th>
                                        <th>عدد الموظفين</th>
                                        @can('manage_hr_settings')
                                            <th>إجراءات</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shifts as $shift)
                                        <tr>
                                            <td class="text-muted fw-bold">{{ $loop->iteration }}</td>
                                            <td class="text-start ps-4 fw-bolder text-primary">{{ $shift->name_ar }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 fs-6 font-monospace rounded-pill shadow-sm">
                                                    {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 fs-6 font-monospace rounded-pill shadow-sm">
                                                    {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                                </span>
                                            </td>
                                            <td class="text-muted fw-bold">{{ $shift->grace_period_minutes }} دقيقة</td>
                                            <td><span
                                                    class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $shift->employees_count }}
                                                    موظف</span></td>

                                            @can('manage_hr_settings')
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button
                                                            class="btn btn-sm btn-outline-primary rounded-circle shadow-sm btn-edit-shift"
                                                            data-id="{{ $shift->id }}" data-name="{{ $shift->name_ar }}"
                                                            data-start="{{ $shift->start_time }}"
                                                            data-end="{{ $shift->end_time }}"
                                                            data-grace="{{ $shift->grace_period_minutes }}"
                                                            data-bs-toggle="modal" data-bs-target="#editShiftModal"
                                                            title="تعديل">
                                                            <i class='mdi mdi-pencil-outline font-16'></i>
                                                        </button>
                                                        <form
                                                            action="{{ route('admin.hr.settings.shifts.destroy', $shift->id) }}"
                                                            method="POST" class="d-inline delete-form">
                                                            @csrf @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger rounded-circle shadow-sm btn-delete"
                                                                title="حذف">
                                                                <i class='mdi mdi-trash-can-outline font-16'></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ Auth::user()->can('manage_hr_settings') ? 7 : 6 }}"
                                                class="p-5 text-muted fw-bold text-center">
                                                <i
                                                    class='mdi mdi-clock-outline fs-1 opacity-50 d-block mb-2 text-primary'></i>
                                                لا توجد ورديات مسجلة.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Job Grades Tab --}}
            <div class="tab-pane fade" id="grades-tab" role="tabpanel">
                <div class="card card-custom">
                    <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-bold text-dark"><i
                                class='mdi mdi-medal-outline text-primary me-2 align-middle fs-4'></i> هيكل الدرجات
                            الوظيفية ومستويات الأجور</h5>
                        @can('manage_hr_settings')
                            <button class="btn btn-primary btn-add rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#addGradeModal">
                                <i class='mdi mdi-plus me-1'></i> إضافة درجة وظيفية
                            </button>
                        @endcan
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover w-100 mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-start ps-4">الدرجة / المسمى</th>
                                        <th>الحد الأدنى للراتب</th>
                                        <th>الحد الأقصى للراتب</th>
                                        <th>عدد الموظفين</th>
                                        @can('manage_hr_settings')
                                            <th>إجراءات</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($grades as $grade)
                                        <tr>
                                            <td class="text-muted fw-bold">{{ $loop->iteration }}</td>
                                            <td class="text-start ps-4 fw-bolder text-primary">
                                                <i class='mdi mdi-star-circle text-warning fs-5 align-middle me-1'></i>
                                                {{ $grade->name_ar }}
                                            </td>
                                            <td class="text-success fw-bold font-monospace">
                                                {{ number_format($grade->min_basic_salary, 3) }} ر.ع</td>
                                            <td class="text-danger fw-bold font-monospace">
                                                {{ number_format($grade->max_basic_salary, 3) }} ر.ع</td>
                                            <td><span
                                                    class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $grade->employees_count }}
                                                    موظف</span></td>

                                            @can('manage_hr_settings')
                                                <td>
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button
                                                            class="btn btn-sm btn-outline-primary rounded-circle shadow-sm btn-edit-grade"
                                                            data-id="{{ $grade->id }}" data-name="{{ $grade->name_ar }}"
                                                            data-min="{{ $grade->min_basic_salary }}"
                                                            data-max="{{ $grade->max_basic_salary }}" data-bs-toggle="modal"
                                                            data-bs-target="#editGradeModal" title="تعديل">
                                                            <i class='mdi mdi-pencil-outline font-16'></i>
                                                        </button>
                                                        <form
                                                            action="{{ route('admin.hr.settings.grades.destroy', $grade->id) }}"
                                                            method="POST" class="d-inline delete-form">
                                                            @csrf @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger rounded-circle shadow-sm btn-delete"
                                                                title="حذف">
                                                                <i class='mdi mdi-trash-can-outline font-16'></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ Auth::user()->can('manage_hr_settings') ? 6 : 5 }}"
                                                class="p-5 text-muted fw-bold text-center">
                                                <i
                                                    class='mdi mdi-medal-outline fs-1 opacity-50 d-block mb-2 text-primary'></i>
                                                لا توجد درجات وظيفية مسجلة.
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


    {{-- ===================================== --}}
    {{-- Modals الإضافات والتعديلات (محمية بالكامل بـ prevent-double-submit) --}}
    {{-- ===================================== --}}
    @can('manage_hr_settings')
        {{-- 1. مودال إضافة وتعديل الأقسام --}}
        <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-office-building-plus-outline me-1'></i>
                            إضافة قسم جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.hr.settings.departments.store') }}" method="POST"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم القسم (عربي) <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar"
                                    class="form-control bg-white fw-bold shadow-sm border-primary" required
                                    placeholder="مثال: إدارة الموارد البشرية">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم القسم (إنجليزي)</label>
                                <input type="text" name="name_en" class="form-control bg-light"
                                    placeholder="مثال: HR Department" dir="ltr">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary fw-bold px-4 rounded-pill shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary fw-bold px-5 rounded-pill shadow-sm btn-submit"><i
                                    class="mdi mdi-check me-1"></i> حفظ القسم</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-pencil-outline me-1'></i> تعديل قسم
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editDeptForm" class="prevent-double-submit">
                        @csrf @method('PUT')
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم القسم (عربي) <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" id="edit_dept_ar"
                                    class="form-control bg-white fw-bold shadow-sm border-primary" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم القسم (إنجليزي)</label>
                                <input type="text" name="name_en" id="edit_dept_en" class="form-control bg-light"
                                    dir="ltr">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="submit"
                                class="btn btn-primary fw-bold w-100 py-2 rounded-pill shadow-sm btn-submit">حفظ
                                التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 2. مودال إضافة وتعديل الورديات --}}
        <div class="modal fade" id="addShiftModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-clock-plus-outline me-1'></i> وردية عمل
                            جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.hr.settings.shifts.store') }}" method="POST"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم الوردية <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar"
                                    class="form-control bg-white fw-bold shadow-sm border-primary" required
                                    placeholder="مثال: الوردية الصباحية">
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">وقت الحضور <span class="text-danger">*</span></label>
                                    <input type="time" name="start_time" class="form-control bg-light fw-bold text-center"
                                        required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">وقت الانصراف <span class="text-danger">*</span></label>
                                    <input type="time" name="end_time" class="form-control bg-light fw-bold text-center"
                                        required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">فترة السماح للتأخير (بالدقائق) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="grace_period_minutes"
                                    class="form-control bg-white fw-bold text-danger border-danger shadow-sm text-center font-monospace"
                                    value="15" required min="0">
                                <small class="text-muted d-block mt-2"><i class="mdi mdi-information-outline"></i> بعد هذه
                                    الدقائق يتم احتساب الموظف متأخراً في نظام الحضور والرواتب.</small>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="submit"
                                class="btn btn-primary fw-bold w-100 py-2 rounded-pill shadow-sm btn-submit">حفظ
                                الوردية</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editShiftModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-pencil-outline me-1'></i> تعديل بيانات
                            الوردية</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST" id="editShiftForm" class="prevent-double-submit">
                        @csrf @method('PUT')
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم الوردية <span class="text-danger">*</span></label>
                                <input type="text" name="name_ar" id="edit_shift_name"
                                    class="form-control bg-white fw-bold shadow-sm border-primary" required>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">وقت الحضور <span class="text-danger">*</span></label>
                                    <input type="time" name="start_time" id="edit_shift_start"
                                        class="form-control bg-light fw-bold text-center" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">وقت الانصراف <span class="text-danger">*</span></label>
                                    <input type="time" name="end_time" id="edit_shift_end"
                                        class="form-control bg-light fw-bold text-center" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">فترة السماح (بالدقائق) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="grace_period_minutes" id="edit_shift_grace"
                                    class="form-control bg-white fw-bold text-danger border-danger shadow-sm text-center font-monospace"
                                    required min="0">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="submit"
                                class="btn btn-primary fw-bold w-100 py-2 rounded-pill shadow-sm btn-submit">حفظ
                                التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. مودال إضافة وتعديل الدرجات الوظيفية --}}
        <div class="modal fade" id="addGradeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-medal-outline me-1'></i> إضافة درجة
                            وظيفية (Grade)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.hr.settings.grades.store') }}" method="POST"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم الدرجة / المستوى <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name_ar"
                                    class="form-control bg-white fw-bold shadow-sm border-primary" required
                                    placeholder="مثال: الإدارة العليا (A)">
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-success">الحد الأدنى للراتب <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <input type="number" step="0.001" name="min_basic_salary"
                                            class="form-control bg-white fw-bold text-success font-monospace border-success"
                                            value="0" required>
                                        <span class="input-group-text bg-success text-white border-success">ر.ع</span>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-danger">الحد الأقصى للراتب <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <input type="number" step="0.001" name="max_basic_salary"
                                            class="form-control bg-white fw-bold text-danger font-monospace border-danger"
                                            value="0" required>
                                        <span class="input-group-text bg-danger text-white border-danger">ر.ع</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="submit"
                                class="btn btn-primary fw-bold w-100 py-2 rounded-pill shadow-sm btn-submit">حفظ هيكل
                                الدرجة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editGradeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-pencil-outline me-1'></i> تعديل الدرجة
                            الوظيفية</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editGradeForm" class="prevent-double-submit">
                        @csrf @method('PUT')
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم الدرجة / المستوى <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name_ar" id="edit_grade_name"
                                    class="form-control bg-white fw-bold shadow-sm border-primary" required>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-success">الحد الأدنى للراتب <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <input type="number" step="0.001" name="min_basic_salary" id="edit_grade_min"
                                            class="form-control bg-white fw-bold text-success font-monospace border-success"
                                            required>
                                        <span class="input-group-text bg-success text-white border-success">ر.ع</span>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-danger">الحد الأقصى للراتب <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <input type="number" step="0.001" name="max_basic_salary" id="edit_grade_max"
                                            class="form-control bg-white fw-bold text-danger font-monospace border-danger"
                                            required>
                                        <span class="input-group-text bg-danger text-white border-danger">ر.ع</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="submit"
                                class="btn btn-primary fw-bold w-100 py-2 rounded-pill shadow-sm btn-submit">حفظ
                                التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            // تفعيل التابز بناءً على الرابط (Hash)
            var hash = location.hash.replace(/^#/, '');
            if (hash) {
                $('.nav-pills-custom button[data-bs-target="#' + hash + '"]').tab('show');
            }

            // حماية الفورمات من التكرار
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري الحفظ...');
            });

            // 1. الداتا الخاصة بمودال الأقسام
            $('.btn-edit-dept').click(function() {
                let id = $(this).data('id');
                $('#edit_dept_ar').val($(this).data('ar'));
                $('#edit_dept_en').val($(this).data('en'));
                $('#editDeptForm').attr('action',
                    "{{ route('admin.hr.settings.departments.update', ':id') }}".replace(':id', id));
            });

            // 2. الداتا الخاصة بمودال الورديات
            $('.btn-edit-shift').click(function() {
                let id = $(this).data('id');
                $('#edit_shift_name').val($(this).data('name'));
                $('#edit_shift_start').val($(this).data('start'));
                $('#edit_shift_end').val($(this).data('end'));
                $('#edit_shift_grace').val($(this).data('grace'));
                $('#editShiftForm').attr('action', "{{ route('admin.hr.settings.shifts.update', ':id') }}"
                    .replace(':id', id));
            });

            // 3. الداتا الخاصة بمودال الدرجات الوظيفية
            $('.btn-edit-grade').click(function() {
                let id = $(this).data('id');
                $('#edit_grade_name').val($(this).data('name'));
                $('#edit_grade_min').val($(this).data('min'));
                $('#edit_grade_max').val($(this).data('max'));
                $('#editGradeForm').attr('action', "{{ route('admin.hr.settings.grades.update', ':id') }}"
                    .replace(':id', id));
            });

            // تأكيد الحذف الموحد بالـ SweetAlert2
            $('.delete-form .btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'إذا كان هذا العنصر مرتبطاً بأي موظفين أو عمليات سابقة، سيقوم النظام برفض الحذف لحماية قواعد البيانات.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، حاول الحذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // إشعارات SweetAlert2 للردود
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'عملية ناجحة',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'مرفوض أمنياً!',
                    text: "{{ session('error') }}"
                });
            @endif
            @if ($errors->any())
                let errorHtml =
                    '<ul class="text-start text-danger fw-bold mb-0" style="list-style: none; padding:0;">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li><i class="mdi mdi-close-circle-outline me-1"></i> {{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في إدخال البيانات',
                    html: errorHtml
                });
            @endif
        });
    </script>
@endpush
