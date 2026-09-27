@extends('backend.layouts.master')
@section('title_page', 'تعديل بيانات الفصل الافتراضي')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        .edit-badge {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
            font-weight: 800;
        }

        .form-label {
            font-size: 14px;
            margin-bottom: 8px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-premium shadow-sm border-0 rounded-4 border-top border-warning border-4">
                <div
                    class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="fw-bold mb-0 text-warning d-flex align-items-center">
                        <i class="mdi mdi-pencil-circle-outline me-2 fs-3"></i> تعديل بيانات الفصل الافتراضي
                        <span class="badge edit-badge ms-3 font-12 px-3 py-1 rounded-pill">وضع التعديل</span>
                    </h5>
                    <a href="{{ route('admin.virtual-classes.index') }}"
                        class="btn btn-light rounded-pill fw-bold shadow-sm px-4 border">
                        <i class="mdi mdi-arrow-right me-1"></i> العودة للجدول
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.virtual-classes.update', $virtualClass->id) }}" method="POST"
                        id="editVirtualClassForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-format-title text-primary me-1"></i> عنوان الحصة <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="topic"
                                    class="form-control form-control-lg shadow-sm rounded-3 border-warning @error('topic') is-invalid @enderror"
                                    value="{{ old('topic', $virtualClass->topic) }}" required>
                                @error('topic')
                                    <span class="text-danger font-12 fw-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark"><i class="mdi mdi-domain text-success me-1"></i>
                                    الفرع <span class="text-danger">*</span></label>
                                <select id="branch_id" name="branch_id"
                                    class="form-select shadow-sm rounded-3 select2 border-warning" required>
                                    <option value="" disabled>-- اختر الفرع --</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ old('branch_id', $virtualClass->section->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-calendar-range text-info me-1"></i> العام الدراسي <span
                                        class="text-danger">*</span></label>
                                <select id="academic_year_id" name="academic_year_id"
                                    class="form-select shadow-sm rounded-3 select2" required>
                                    <option value="{{ $virtualClass->section->academic_year_id }}" selected>
                                        {{ $virtualClass->section->academicYear->name ?? '---' }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark"><i class="mdi mdi-school text-warning me-1"></i>
                                    الصف <span class="text-danger">*</span></label>
                                <select id="grade_id" name="grade_id" class="form-select shadow-sm rounded-3 select2"
                                    required>
                                    @foreach ($grades as $grade)
                                        <option value="{{ $grade->id }}"
                                            {{ old('grade_id', $virtualClass->section->grade_id ?? '') == $grade->id ? 'selected' : '' }}>
                                            {{ $grade->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-danger"><i class="mdi mdi-door-open me-1"></i> الشعبة
                                    <span class="text-danger">*</span></label>
                                <select name="section_id" id="section_id"
                                    class="form-select shadow-sm rounded-3 select2 border-danger" required>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ old('section_id', $virtualClass->section_id) == $section->id ? 'selected' : '' }}>
                                            {{ $section->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-book-open-page-variant text-info me-1"></i> المادة <span
                                        class="text-danger">*</span></label>
                                <select name="subject_id" id="subject_id" class="form-select shadow-sm rounded-3 select2"
                                    required>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ old('subject_id', $virtualClass->subject_id) == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-account-tie text-secondary me-1"></i> المعلم <span
                                        class="text-danger">*</span></label>
                                <select name="teacher_id" id="teacher_id" class="form-select shadow-sm rounded-3 select2"
                                    required>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}"
                                            {{ old('teacher_id', $virtualClass->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-video-check text-purple me-1"></i> حالة الغرفة <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-select shadow-sm rounded-3 select2" required>
                                    <option value="scheduled" {{ $virtualClass->status == 'scheduled' ? 'selected' : '' }}>
                                        مجدولة (بانتظار البدء)</option>
                                    <option value="active" {{ $virtualClass->status == 'active' ? 'selected' : '' }}>نشطة
                                        (الآن)</option>
                                    <option value="completed" {{ $virtualClass->status == 'completed' ? 'selected' : '' }}>
                                        مكتملة (انتهت)</option>
                                    <option value="cancelled" {{ $virtualClass->status == 'cancelled' ? 'selected' : '' }}>
                                        ملغاة</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-calendar-clock text-danger me-1"></i> موعد البدء <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_at" class="form-control shadow-sm rounded-3"
                                    value="{{ old('start_at', \Carbon\Carbon::parse($virtualClass->start_at)->format('Y-m-d\TH:i')) }}"
                                    required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-timer-sand text-primary me-1"></i> المدة (بالدقائق) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="duration" class="form-control shadow-sm rounded-3"
                                    value="{{ old('duration', $virtualClass->duration) }}" min="15" max="180"
                                    required>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-link-variant text-secondary me-1"></i> رابط خارجي (اختياري)</label>
                                <input type="url" name="join_url" class="form-control shadow-sm rounded-3"
                                    value="{{ old('join_url', $virtualClass->join_url) }}">
                            </div>

                            <div class="col-md-12">
                                <div
                                    class="form-check form-switch mt-2 bg-light p-3 rounded-3 border w-100 shadow-sm d-flex align-items-center">
                                    <input class="form-check-input ms-2 mt-0" type="checkbox" name="is_active"
                                        id="isActive" style="transform: scale(1.3);"
                                        {{ old('is_active', $virtualClass->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold ms-3 mb-0 text-dark" for="isActive">تفعيل الحصة
                                        وإظهارها في لوحة الطالب</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="text-end">
                            <button type="submit" id="btnSubmit"
                                class="btn btn-warning rounded-pill fw-bold shadow px-5 fs-5 text-dark">
                                <i class="mdi mdi-content-save-edit me-1"></i> حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dir: isRtl ? "rtl" : "ltr"
            });

            $('#subject_id, #branch_id').on('change', function() {
                let subjectId = $('#subject_id').val();
                let branchId = $('#branch_id').val();
                let teacherSelect = $('#teacher_id');

                if (subjectId && branchId) {
                    teacherSelect.html('<option value="">⏳ جاري البحث...</option>').prop('disabled', true);
                    let teachersUrl = "{{ route('admin.get-subject-teachers', ':id') }}".replace(':id',
                        subjectId);

                    $.ajax({
                        url: teachersUrl,
                        type: "GET",
                        data: {
                            branch_id: branchId
                        },
                        success: function(data) {
                            let options =
                                '<option value="" selected disabled>-- اختر المعلم --</option>';
                            if (data && data.length > 0) {
                                $.each(data, function(index, teacher) {
                                    let branchName = teacher.branch ? teacher.branch
                                        .name_ar : 'فرع غير محدد';
                                    options +=
                                        `<option value="${teacher.id}">${teacher.name_ar} (${branchName})</option>`;
                                });
                            } else {
                                options = '<option value="" disabled>لا يوجد معلمين</option>';
                            }
                            teacherSelect.html(options).prop('disabled', false);
                        }
                    });
                }
            });

            function loadSections() {
                let branchId = $('#branch_id').val();
                let yearId = $('#academic_year_id').val();
                let gradeId = $('#grade_id').val();
                let sectionSelect = $('#section_id');

                if (branchId && yearId && gradeId) {
                    sectionSelect.html('<option value="">⏳ جاري التحميل...</option>').prop('disabled', true);
                    $.ajax({
                        url: "{{ route('admin.schedules.get-sections-ajax') }}",
                        type: "GET",
                        data: {
                            branch_id: branchId,
                            academic_year_id: yearId,
                            grade_id: gradeId
                        },
                        success: function(data) {
                            let options =
                                '<option value="" selected disabled>-- اختر الشعبة --</option>';
                            if (data && data.length > 0) {
                                $.each(data, function(index, sec) {
                                    options +=
                                        `<option value="${sec.id}">${sec.name_ar}</option>`;
                                });
                            } else {
                                options = '<option value="" disabled>لا توجد شعب</option>';
                            }
                            sectionSelect.html(options).prop('disabled', false);
                        }
                    });
                }
            }

            $('#branch_id').on('change', function() {
                let branchId = $(this).val();
                let yearSelect = $('#academic_year_id');
                let gradeSelect = $('#grade_id');
                $('#subject_id').val('').trigger('change');
                $('#teacher_id').html('<option value="">-- بانتظار المادة --</option>');

                if (branchId) {
                    yearSelect.html('<option value="">⏳ جاري التحميل...</option>').prop('disabled', true);
                    gradeSelect.html('<option value="">⏳ جاري التحميل...</option>').prop('disabled', true);

                    let yearsUrl = "{{ route('admin.get-branch-academic-years', ':id') }}".replace(':id',
                        branchId);
                    $.get(yearsUrl, function(data) {
                        let options =
                        '<option value="" disabled selected>-- اختر العام --</option>';
                        $.each(data, function(index, year) {
                            options += `<option value="${year.id}">${year.name}</option>`;
                        });
                        yearSelect.html(options).prop('disabled', false);
                        loadSections();
                    });

                    let gradesUrl = "{{ route('admin.get-branch-grades', ':id') }}".replace(':id',
                        branchId);
                    $.get(gradesUrl, function(data) {
                        let options = '<option value="" disabled selected>-- اختر الصف --</option>';
                        $.each(data, function(index, grade) {
                            options +=
                                `<option value="${grade.id}">${grade.name_ar}</option>`;
                        });
                        gradeSelect.html(options).prop('disabled', false);
                        loadSections();
                    });
                }
            });

            $('#academic_year_id, #grade_id').on('change', function() {
                loadSections();
            });

            $('#editVirtualClassForm').on('submit', function(e) {
                e.preventDefault();
                let form = this;
                Swal.fire({
                    title: 'حفظ التعديلات؟',
                    text: "سيتم تحديث بيانات الفصل الافتراضي وتطبيقها فوراً.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احفظ',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#btnSubmit').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري الحفظ...');
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
