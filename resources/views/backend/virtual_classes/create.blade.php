@extends('backend.layouts.master')
@section('title_page', 'جدولة فصل افتراضي جديد')

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

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
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
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-premium shadow-sm border-0 rounded-4 border-top border-danger border-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-danger">
                        <i class="mdi mdi-video-plus-outline me-1 fs-4 align-middle"></i> إعداد وجدولة حصة بث مباشر
                        (أونلاين)
                    </h5>
                    <a href="{{ route('admin.virtual-classes.index') }}"
                        class="btn btn-light rounded-pill fw-bold shadow-sm px-4 border">
                        <i class="mdi mdi-arrow-right me-1"></i> العودة للجدول
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.virtual-classes.store') }}" method="POST" id="virtualClassForm">
                        @csrf

                        <div class="row g-4">
                            {{-- عنوان الحصة --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-format-title text-primary me-1"></i> عنوان الحصة (موضوع الدرس) <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="topic"
                                    class="form-control form-control-lg shadow-sm rounded-3 @error('topic') is-invalid @enderror"
                                    value="{{ old('topic') }}"
                                    placeholder="مثال: مراجعة نهائية في الجبر والهندسة الفراغية..." required>
                                @error('topic')
                                    <span class="text-danger font-12 fw-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- 🔥 الصف الأول: الفلاتر الذكية (الفرع -> السنة -> الصف -> الشعبة) 🔥 --}}
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark"><i class="mdi mdi-domain text-success me-1"></i>
                                    الفرع المدرسـي <span class="text-danger">*</span></label>
                                <select id="branch_id" name="branch_id" class="form-select shadow-sm rounded-3 select2"
                                    required>
                                    <option value="" selected disabled>-- اختر الفرع --</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-calendar-range text-info me-1"></i>
                                    العام الدراسي <span class="text-danger">*</span></label>
                                <select id="academic_year_id" name="academic_year_id"
                                    class="form-select shadow-sm rounded-3 select2" disabled required>
                                    <option value="" selected disabled>-- بانتظار الفرع --</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark"><i class="mdi mdi-school text-warning me-1"></i>
                                    الصف الدراسي <span class="text-danger">*</span></label>
                                <select id="grade_id" name="grade_id" class="form-select shadow-sm rounded-3 select2"
                                    disabled required>
                                    <option value="" selected disabled>-- بانتظار الفرع --</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-danger"><i class="mdi mdi-door-open me-1"></i>
                                    الشعبة المستهدفة <span class="text-danger">*</span></label>
                                <select name="section_id" id="section_id"
                                    class="form-select shadow-sm rounded-3 select2 border-danger @error('section_id') is-invalid @enderror"
                                    required disabled>
                                    <option value="" selected disabled>-- بانتظار استكمال البيانات --</option>
                                </select>
                                @error('section_id')
                                    <span class="text-danger font-12 fw-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- 🔥 الصف الثاني: تفاصيل الحصة 🔥 --}}
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-book-open-page-variant text-info me-1"></i> المادة الدراسية <span
                                        class="text-danger">*</span></label>
                                <select name="subject_id" id="subject_id"
                                    class="form-select shadow-sm rounded-3 select2 @error('subject_id') is-invalid @enderror"
                                    required>
                                    <option value="" selected disabled>-- اختر المادة --</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name_ar }}</option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <span class="text-danger font-12 fw-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-account-tie text-secondary me-1"></i> معلم المادة <span
                                        class="text-danger">*</span></label>
                                <select name="teacher_id" id="teacher_id"
                                    class="form-select shadow-sm rounded-3 select2 @error('teacher_id') is-invalid @enderror"
                                    required disabled>
                                    <option value="" selected disabled>-- بانتظار تحديد المادة --</option>
                                </select>
                                @error('teacher_id')
                                    <span class="text-danger font-12 fw-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-calendar-clock text-danger me-1"></i> موعد بدء الحصة <span
                                        class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_at"
                                    class="form-control shadow-sm rounded-3 @error('start_at') is-invalid @enderror"
                                    value="{{ old('start_at') }}" required>
                                @error('start_at')
                                    <span class="text-danger font-12 fw-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-timer-sand text-primary me-1"></i> مدة الحصة (بالدقائق) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="duration"
                                    class="form-control shadow-sm rounded-3 @error('duration') is-invalid @enderror"
                                    value="{{ old('duration', 45) }}" min="15" max="180" required>
                                @error('duration')
                                    <span class="text-danger font-12 fw-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- الصف الثالث: الرابط الإضافي وحالة التفعيل --}}
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-dark"><i
                                        class="mdi mdi-link-variant text-secondary me-1"></i> رابط خارجي للانضمام
                                    (اختياري)</label>
                                <input type="url" name="join_url"
                                    class="form-control shadow-sm rounded-3 @error('join_url') is-invalid @enderror"
                                    value="{{ old('join_url') }}" placeholder="https://zoom.us/j/...">
                                <span class="text-muted font-12 mt-1 d-block">اترك هذا الحقل فارغاً إذا كنت تريد الاعتماد
                                    على الخوادم الداخلية للمنصة.</span>
                                @error('join_url')
                                    <span class="text-danger font-12 fw-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4 d-flex align-items-center">
                                <div
                                    class="form-check form-switch mt-4 bg-light p-2 rounded-3 border w-100 shadow-sm d-flex align-items-center">
                                    <input class="form-check-input form-check-input-success ms-2 mt-0" type="checkbox"
                                        name="is_active" id="isActive" checked style="transform: scale(1.3);">
                                    <label class="form-check-label fw-bold ms-3 mb-0" for="isActive">تفعيل الفصل وإرسال
                                        إشعار للطلاب</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <div class="text-end">
                            <button type="submit" id="btnSubmit"
                                class="btn btn-danger rounded-pill fw-bold shadow px-5 fs-5">
                                <i class="mdi mdi-video-wireless me-1"></i> إنشاء وجدولة الفصل
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

            // تفعيل Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dir: isRtl ? "rtl" : "ltr"
            });

            // 🌟 1. استدعاء المعلمين بناءً على المادة والفرع 🌟
            $('#subject_id').on('change', function() {
                let subjectId = $(this).val();
                let branchId = $('#branch_id').val(); // 🔥 إضافة قراءة الفرع
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
                        }, // 🔥 إرسال الفرع في الطلب
                        success: function(data) {
                            let options =
                                '<option value="" selected disabled>-- اختر المعلم --</option>';
                            if (data && data.length > 0) {
                                $.each(data, function(index, teacher) {
                                    // 🌟 جلب اسم الفرع أو كتابة "غير محدد" لو مفيش 🌟
                                    let branchName = teacher.branch ? teacher.branch
                                        .name_ar : 'فرع غير محدد';

                                    options +=
                                        `<option value="${teacher.id}">${teacher.name_ar} (${branchName})</option>`;
                                });
                            } else {
                                options =
                                    '<option value="" disabled>لا يوجد معلمين لهذه المادة</option>';
                            }
                            teacherSelect.html(options).prop('disabled', false);
                        },
                        error: function() {
                            teacherSelect.html(
                                '<option value="" disabled>خطأ في التحميل</option>').prop(
                                'disabled', false);
                        }
                    });
                } else {
                    teacherSelect.html('<option value="">-- بانتظار تحديد المادة والفرع --</option>').prop(
                        'disabled', true);
                }
            });

            // دالة استدعاء الشعب
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

            // عند تغيير الفرع
            $('#branch_id').on('change', function() {
                let branchId = $(this).val();
                let yearSelect = $('#academic_year_id');
                let gradeSelect = $('#grade_id');

                // 🔥 تفريغ المعلم والمادة عند تغيير الفرع لمنع تضارب الداتا
                $('#subject_id').val('').trigger('change');
                $('#teacher_id').html('<option value="">-- بانتظار تحديد المادة --</option>').prop(
                    'disabled', true);

                if (branchId) {
                    yearSelect.html('<option value="">⏳ جاري التحميل...</option>').prop('disabled', true);
                    gradeSelect.html('<option value="">⏳ جاري التحميل...</option>').prop('disabled', true);

                    // جلب السنوات
                    let yearsUrl = "{{ route('admin.get-branch-academic-years', ':id') }}".replace(':id',
                        branchId);
                    $.get(yearsUrl, function(data) {
                        let options =
                            '<option value="" disabled selected>-- اختر العام الدراسي --</option>';
                        $.each(data, function(index, year) {
                            options += `<option value="${year.id}">${year.name}</option>`;
                        });
                        yearSelect.html(options).prop('disabled', false);
                        loadSections();
                    });

                    // جلب الصفوف
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

            // عند تغيير السنة أو الصف
            $('#academic_year_id, #grade_id').on('change', function() {
                loadSections();
            });

            // تأكيد عملية الجدولة
            $('#virtualClassForm').on('submit', function(e) {
                e.preventDefault();
                let form = this;

                Swal.fire({
                    title: 'تأكيد الجدولة',
                    text: "سيتم إنشاء رابط البث المباشر وإرسال إشعار فوري للطلاب المسجلين في هذه الشعبة، هل أنت متأكد؟",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، قم بالإنشاء',
                    cancelButtonText: 'مراجعة البيانات'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#btnSubmit').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري إعداد الغرفة...'
                        );
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
