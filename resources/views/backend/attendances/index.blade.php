@extends('backend.layouts.master')
@section('title', 'الحضور والغياب - الشعب الدراسية')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(91, 105, 188, 0.05);
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }

        body[data-layout-color="dark"] .text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .text-muted {
            color: #adb5bd !important;
        }

        body[data-layout-color="dark"] .table {
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .table thead {
            background-color: #3b4652 !important;
            color: #ced4da !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-dropdown {
            background-color: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-results__option {
            color: #e2e8f0;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: #424e5a !important;
        }

        /* 🖨️ Print Styles */
        @media print {

            .no-print,
            .btn,
            .select2,
            .card-header {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            .table thead {
                background-color: #f1f5f9 !important;
                color: #000 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="page-title font-bold text-primary">
                <i class="mdi mdi-calendar-check-outline me-1"></i> إدارة الحضور والغياب
            </h4>
            <button onclick="window.print()" class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm no-print">
                <i class="mdi mdi-printer me-1"></i> طباعة
            </button>
        </div>
    </div>

    {{-- 🔥 فلاتر البحث العلوية 🔥 --}}
    <div class="card shadow-sm border-0 mb-4 bg-light no-print">
        <div class="card-body p-3">
            <form action="{{ route('admin.attendances.index') }}" method="GET" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label text-muted font-bold font-13"><i class="mdi mdi-office-building"></i>
                            الفرع</label>
                        <select class="form-select select2" name="branch_id" id="filter_branch">
                            <option value="">جميع الفروع</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label text-muted font-bold font-13"><i class="mdi mdi-calendar"></i> السنة
                            الدراسية</label>
                        <select class="form-select select2" name="academic_year_id" id="filter_year">
                            <option value="">جميع السنوات</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label text-muted font-bold font-13"><i class="mdi mdi-school"></i> الصف
                            الدراسي</label>
                        <select class="form-select select2" name="grade_id" id="filter_grade">
                            <option value="">جميع الصفوف</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted font-bold font-13"><i class="mdi mdi-google-classroom"></i>
                            الشعبة</label>
                        <select class="form-select select2" name="section_id" id="filter_section">
                            <option value="">جميع الشعب</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 font-bold shadow-sm" id="btn-filter">
                            <i class="mdi mdi-magnify"></i> تصفية
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('admin.attendances.index') }}" class="btn btn-warning text-dark w-100 shadow-sm"
                            title="إعادة ضبط">
                            <i class="mdi mdi-refresh"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0 text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th>شعبة/فصل</th>
                            <th>الفرع</th>
                            <th>إجمالي الطلاب</th>
                            <th>الصف الدراسي</th>
                            <th>السنة الدراسية</th>
                            <th style="width: 25%;">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sections as $index => $section)
                            <tr>
                                <td class="text-muted font-bold">{{ $index + 1 }}</td>
                                <td class="text-start">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <span class="avatar-title bg-soft-info text-info rounded">
                                                <i class="mdi mdi-account-group font-18"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="m-0 font-14 text-dark font-bold">{{ $section->name_ar }}</h5>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-dark text-dark font-13 px-2 py-1">
                                        {{ $section->branch->name_ar ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($section->students_count > 0)
                                        <span class="badge bg-soft-primary text-primary font-14 px-2 py-1 shadow-sm">
                                            <i class="mdi mdi-account-multiple"></i> {{ $section->students_count }} طالب
                                        </span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger font-13 px-2 py-1">لا يوجد طلاب</span>
                                    @endif
                                </td>
                                <td>{{ $section->grade->name_ar ?? '-' }}</td>
                                <td dir="ltr" class="text-muted font-13">{{ $section->academicYear->name ?? '-' }}
                                </td>
                                <td>
                                    @can('view_student_attendances')
                                        <a href="{{ route('admin.attendances.show_date_attendance', $section->id) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill shadow-sm px-3 font-bold">
                                            <i class="mdi mdi-calendar-text me-1"></i> سجل الحضور
                                        </a>
                                    @else
                                        <span class="badge bg-secondary p-2 rounded-pill shadow-sm">
                                            <i class="mdi mdi-lock"></i> غير مصرح
                                        </span>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="mdi mdi-folder-search-outline" style="font-size: 48px;"></i>
                                        <h5 class="mt-2 font-bold">لا توجد شعب مسجلة وفقاً لبحثك.</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentLocale = "{{ app()->getLocale() }}";
            let isRtl = currentLocale === 'ar';

            // تفعيل Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // القيم القديمة للاحتفاظ بالفلترة بعد عمل Refresh
            let oldYear = "{{ request('academic_year_id') }}";
            let oldGrade = "{{ request('grade_id') }}";
            let oldSection = "{{ request('section_id') }}";

            // 🔥 دالة جلب الشعب باستخدام الراوت العام للشبكة 🔥
            function loadSections() {
                let branchId = $('#filter_branch').val();
                let yearId = $('#filter_year').val();
                let gradeId = $('#filter_grade').val();
                let sectionSelect = $('#filter_section');

                if (branchId && yearId && gradeId) {
                    sectionSelect.html('<option value="">⏳ جاري التحميل...</option>').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('admin.schedules.get-sections-ajax') }}", // استخدام الراوت العام
                        type: "GET",
                        data: {
                            branch_id: branchId,
                            academic_year_id: yearId,
                            grade_id: gradeId
                        },
                        success: function(data) {
                            let options = '<option value="" selected>-- كل الشعب --</option>';
                            if (data && data.length > 0) {
                                $.each(data, function(index, sec) {
                                    let selected = (oldSection == sec.id) ? 'selected' : '';
                                    options +=
                                        `<option value="${sec.id}" ${selected}>${sec.name_ar}</option>`;
                                });
                            } else {
                                options = '<option value="" disabled>لا توجد شعب مطابقة</option>';
                            }
                            sectionSelect.html(options).prop('disabled', false);
                        },
                        error: function() {
                            sectionSelect.html('<option value="" disabled>خطأ في التحميل</option>')
                                .prop('disabled', false);
                        }
                    });
                } else {
                    sectionSelect.html('<option value="">-- بانتظار استكمال البيانات --</option>').prop('disabled',
                        true);
                }
            }

            // 1. عند تغيير الفرع (أو تحميل الصفحة بفرع محدد مسبقاً)
            $('#filter_branch').on('change', function() {
                let branchId = $(this).val();
                let yearSelect = $('#filter_year');
                let gradeSelect = $('#filter_grade');

                if (branchId) {
                    yearSelect.html('<option value="">⏳ جاري التحميل...</option>').prop('disabled', true);
                    gradeSelect.html('<option value="">⏳ جاري التحميل...</option>').prop('disabled', true);

                    // جلب السنوات الدراسية للفرع
                    let yearsUrl = "{{ route('admin.get-branch-academic-years', ':id') }}".replace(':id',
                        branchId);
                    $.get(yearsUrl, function(data) {
                        let options =
                            '<option value="" disabled selected>-- اختر العام الدراسي --</option>';
                        $.each(data, function(index, year) {
                            let selected = (oldYear == year.id) ? 'selected' : '';
                            options +=
                                `<option value="${year.id}" ${selected}>${year.name}</option>`;
                        });
                        yearSelect.html(options).prop('disabled', false);
                        loadSections();
                    });

                    // جلب الصفوف الدراسية للفرع
                    let gradesUrl = "{{ route('admin.get-branch-grades', ':id') }}".replace(':id',
                        branchId);
                    $.get(gradesUrl, function(data) {
                        let options = '<option value="" disabled selected>-- اختر الصف --</option>';
                        $.each(data, function(index, grade) {
                            let selected = (oldGrade == grade.id) ? 'selected' : '';
                            options +=
                                `<option value="${grade.id}" ${selected}>${grade.name_ar}</option>`;
                        });
                        gradeSelect.html(options).prop('disabled', false);
                        loadSections();
                    });
                }
            });

            // 2. عند تغيير العام الدراسي أو الصف -> نحدث الشعب
            $('#filter_year, #filter_grade').on('change', function() {
                loadSections();
            });

            // 3. تشغيل الفلتر تلقائياً عند تحميل الصفحة إذا كان هناك فرع محدد من Request قديم
            if ($('#filter_branch').val()) {
                $('#filter_branch').trigger('change');
            }
        });
    </script>
@endpush
