@extends('backend.layouts.master')
@section('title', 'إدارة جدول الحصص')
@section('title_page', 'توزيع وتسكين جدول الحصص الذكي')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        /* 🌟 تخصيص Select2 للجدول 🌟 */
        .select2-container .select2-selection--single {
            height: 36px !important;
            padding-top: 3px;
            font-size: 13px;
            font-weight: 700;
            border-color: #e2e8f0;
        }

        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .select-subject+.select2-container .select2-selection {
            border-right: 4px solid #0dcaf0 !important;
        }

        .select-teacher+.select2-container .select2-selection {
            border-right: 4px solid #8b5cf6 !important;
            margin-top: 6px;
        }

        /* 🌟 حل مشكلة القص داخل الجدول 🌟 */
        .table-responsive {
            overflow-x: auto;
            overflow-y: visible;
            padding-bottom: 20px;
        }

        .schedule-grid th {
            background-color: #1e293b !important;
            color: #fff;
            text-align: center;
            vertical-align: middle;
            font-size: 14px;
            font-weight: 800;
            padding: 15px;
            white-space: nowrap;
        }

        .schedule-grid td {
            vertical-align: top;
            padding: 12px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            min-width: 180px;
        }

        .day-column {
            background-color: #334155 !important;
            color: #fff;
            font-weight: 900;
            min-width: 120px !important;
            text-align: center;
            vertical-align: middle !important;
        }

        .cell-wrapper {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .schedule-grid td {
            background-color: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .schedule-grid th {
            background-color: #1e293b !important;
            border-color: #0f172a;
        }

        body[data-layout-color="dark"] .day-column {
            background-color: #0f172a !important;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #323a46;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .form-check-label {
            color: #cbd5e1 !important;
        }

        body[data-layout-color="dark"] .select-subject+.select2-container .select2-selection {
            border-right-color: #0284c7 !important;
        }

        body[data-layout-color="dark"] .select-teacher+.select2-container .select2-selection {
            border-right-color: #7c3aed !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 1. الفلاتر الذكية --}}
        <div class="card card-premium mb-4 shadow-sm border-top border-primary border-4">
            <div class="card-header bg-white py-4 border-bottom rounded-top-4">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bx bx-calendar-edit text-primary me-1 fs-4 align-middle"></i> استخراج وتسكين جدول الحصص للشعبة
                </h5>
                <form id="filterForm" class="prevent-double-submit">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted font-12 text-uppercase">الفرع / المدرسة <span
                                    class="text-danger">*</span></label>
                            <select name="branch_id" id="branch_id" class="form-select select2-filter shadow-sm" required>
                                <option value="" disabled selected>-- اختر الفرع أولاً --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted font-12 text-uppercase">العام الدراسي <span
                                    class="text-danger">*</span></label>
                            <select name="academic_year_id" id="academic_year_id"
                                class="form-select select2-filter shadow-sm" required disabled>
                                <option value="" disabled selected>-- بانتظار تحديد الفرع --</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-muted font-12 text-uppercase">الصف <span
                                    class="text-danger">*</span></label>
                            <select name="grade_id" id="grade_id" class="form-select select2-filter shadow-sm" required>
                                <option value="" disabled selected>-- اختر الصف --</option>
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade->id }}">{{ $grade->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold text-danger font-12 text-uppercase">الشعبة المستهدفة <span
                                    class="text-danger">*</span></label>
                            <select name="section_id" id="section_id" class="form-select border-danger fw-bold shadow-sm"
                                required disabled>
                                <option value="" disabled selected>-- بانتظار البيانات --</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="btn_load_schedule"
                                class="btn btn-dark btn-lg w-100 fw-bold shadow-sm" disabled>
                                <i class="bx bx-search-alt me-1"></i> استعراض الجدول
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. شبكة الجداول (Grid) --}}
        <div class="card card-premium shadow-sm border-0 d-none" id="schedule_container">
            <div class="card-body p-0">
                <form id="form_save_schedule">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered schedule-grid mb-0">
                            <thead>
                                <tr>
                                    <th class="day-column border-0">اليوم / الحصة</th>
                                    @for ($i = 1; $i <= 7; $i++)
                                        <th>الحصة {{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $days = [
                                        'sunday' => 'الأحد',
                                        'monday' => 'الإثنين',
                                        'tuesday' => 'الثلاثاء',
                                        'wednesday' => 'الأربعاء',
                                        'thursday' => 'الخميس',
                                    ];
                                @endphp
                                @foreach ($days as $key => $dayName)
                                    <tr>
                                        <td class="day-column border-end-0">{{ $dayName }}</td>
                                        @for ($i = 1; $i <= 7; $i++)
                                            <td>
                                                <div class="cell-wrapper" data-day="{{ $key }}"
                                                    data-period="{{ $i }}">

                                                    {{-- المادة --}}
                                                    <select
                                                        name="schedule[{{ $key }}][{{ $i }}][subject_id]"
                                                        class="form-select select2-grid select-subject"
                                                        data-placeholder="-- المادة --"
                                                        {{ !auth()->user()->can('manage_schedules') ? 'disabled' : '' }}>
                                                        <option value=""></option>
                                                        @foreach ($subjects ?? [] as $subject)
                                                            <option value="{{ $subject->id }}">{{ $subject->name_ar }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    {{-- المعلم --}}
                                                    <select
                                                        name="schedule[{{ $key }}][{{ $i }}][teacher_id]"
                                                        class="form-select select2-grid select-teacher"
                                                        data-placeholder="-- المعلم المتاح --"
                                                        {{ !auth()->user()->can('manage_schedules') ? 'disabled' : '' }}>
                                                        <option value=""></option>
                                                    </select>

                                                    {{-- 🔥 صف الأزرار: بديل & أونلاين 🔥 --}}
                                                    <div
                                                        class="d-flex justify-content-between align-items-center mt-2 px-1">
                                                        {{-- زرار البث (أونلاين) --}}
                                                        <div class="form-check m-0 d-flex align-items-center">
                                                            <input
                                                                class="form-check-input virtual-checkbox ms-0 me-1 mt-0 shadow-sm"
                                                                type="checkbox" value="1"
                                                                name="schedule[{{ $key }}][{{ $i }}][is_virtual]"
                                                                id="virtual_{{ $key }}_{{ $i }}"
                                                                {{ !auth()->user()->can('manage_schedules') ? 'disabled' : '' }}>
                                                            <label
                                                                class="form-check-label text-info fw-bold font-11 cursor-pointer ms-1"
                                                                for="virtual_{{ $key }}_{{ $i }}"
                                                                title="حصة افتراضية">
                                                                🌐 بث مباشر
                                                            </label>
                                                        </div>

                                                        {{-- زرار البديل --}}
                                                        <div class="form-check m-0 d-flex align-items-center">
                                                            <input
                                                                class="form-check-input substitute-checkbox ms-0 me-1 mt-0 shadow-sm"
                                                                type="checkbox" value="1"
                                                                id="sub_{{ $key }}_{{ $i }}"
                                                                {{ !auth()->user()->can('manage_schedules') ? 'disabled' : '' }}>
                                                            <label
                                                                class="form-check-label text-danger fw-bold font-11 cursor-pointer ms-1"
                                                                for="sub_{{ $key }}_{{ $i }}"
                                                                title="معلم بديل/احتياطي">
                                                                بديل
                                                            </label>
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-white text-center py-4 border-top rounded-bottom-4">
                        {{-- 🔥 حماية زراير التعديل والحفظ بالصلاحية 🔥 --}}
                        @can('manage_schedules')
                            <button type="submit" id="btn_submit_schedule"
                                class="btn btn-success px-5 py-2 fw-bold fs-5 shadow-sm rounded-pill">
                                <i class="bx bx-save me-1 align-middle"></i> حفظ وتعميم الجدول للإشعارات
                            </button>
                            <button type="button" id="btn_clear_grid"
                                class="btn btn-outline-danger px-4 py-2 ms-2 fw-bold rounded-pill shadow-sm">
                                <i class="bx bx-trash align-middle"></i> تفريغ الشبكة
                            </button>
                            {{-- 🔥 إضافة زر الطوارئ وتحويل الجدول لأونلاين 🔥 --}}
                            <button type="button" id="btn_emergency_online"
                                class="btn btn-warning px-4 py-2 ms-2 fw-bold rounded-pill shadow-sm text-dark">
                                <i class="bx bx-broadcast align-middle"></i> تحويل أونلاين (طوارئ)
                            </button>
                        @else
                            <div class="alert alert-warning mb-0 font-bold border-0 shadow-sm d-inline-block rounded-3">
                                <i class="mdi mdi-lock me-1 fs-5 align-middle"></i> عذراً، لا تملك صلاحية إنشاء وتعديل جداول
                                الحصص المدرسية.
                            </div>
                        @endcan
                    </div>
                </form>
            </div>
        </div>

        {{-- رسالة الانتظار --}}
        <div class="card card-premium text-center py-5 shadow-sm border-0 mt-4" id="waiting_message">
            <div class="card-body py-5">
                <i class="bx bx-table text-primary opacity-25 d-block mb-3" style="font-size: 80px;"></i>
                <h4 class="text-dark fw-bold">جدول الحصص الأسبوعي</h4>
                <p class="text-muted font-14">الرجاء إكمال الفلاتر العلوية لاختيار الفرع والصف والشعبة لاستعراض أو بناء
                    الجدول الخاص بها.</p>
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

            // 1. إعدادات الأمان
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // 2. تهيئة Select2 للفلاتر العلوية
            $('.select2-filter').select2({
                theme: "bootstrap-5",
                dir: isRtl ? "rtl" : "ltr",
            });

            // دالة تهيئة Select2 للشبكة الداخلية (تُستدعى عند الحاجة فقط)
            function initGridSelect2() {
                $('.select2-grid').select2({
                    theme: "bootstrap-5",
                    dir: isRtl ? "rtl" : "ltr",
                    allowClear: true,
                    width: '100%'
                });
            }
            initGridSelect2();

            // ==========================================
            // 🔥 3. جلب المدرسين المتاحين (Smart Filtering) 🔥
            // ==========================================
            $('.select-subject, .substitute-checkbox').on('change', function(e) {
                // منع تشغيل الـ AJAX إذا كان التغيير ناتج عن الكود (برمجياً) لتفادي الـ DDOS
                if (e.originalEvent === undefined && !$(this).hasClass('trigger-ajax-forced')) {
                    return;
                }

                let cell = $(this).closest('.cell-wrapper');
                let day = cell.data('day');
                let period = cell.data('period');
                let subjectId = cell.find('.select-subject').val();
                let isSubstitute = cell.find('.substitute-checkbox').is(':checked') ? 'true' : 'false';
                let teacherSelect = cell.find('.select-teacher');
                let sectionId = $('#section_id').val();

                // لو مفيش مادة مختارة، نفضي المدرسين
                if (!subjectId) {
                    teacherSelect.empty().append('<option value=""></option>').trigger('change.select2');
                    return;
                }

                // عرض حالة تحميل للمستخدم
                teacherSelect.empty().append('<option value="">⏳ جاري البحث...</option>').prop('disabled',
                    true);
                teacherSelect.trigger('change.select2');

                // الاتصال بالسيرفر لمعرفة المتاحين
                $.ajax({
                    url: "{{ route('admin.schedules.available-teachers') }}",
                    type: 'POST',
                    data: {
                        day: day,
                        period: period,
                        subject_id: subjectId,
                        is_substitute: isSubstitute,
                        section_id: sectionId
                    },
                    success: function(data) {
                        let options = '<option value=""></option>';
                        if (data && data.length > 0) {
                            $.each(data, function(index, teacher) {
                                let spec = teacher.specialization ?
                                    ` (${teacher.specialization})` : '';
                                options +=
                                    `<option value="${teacher.id}">${teacher.name_ar}${spec}</option>`;
                            });
                        } else {
                            options +=
                                '<option value="" disabled>لا يوجد معلمين متاحين!</option>';
                        }

                        let isDisabled = !@json(auth()->user()->can('manage_schedules'));
                        teacherSelect.html(options).prop('disabled', isDisabled);

                        // استرجاع المعلم المحفوظ لو موجود
                        let savedTeacherId = teacherSelect.data('saved-teacher-id');
                        if (savedTeacherId) {
                            teacherSelect.val(savedTeacherId);
                            teacherSelect.removeData('saved-teacher-id');
                        }

                        teacherSelect.trigger('change.select2');
                    },
                    error: function() {
                        teacherSelect.html('<option value="" disabled>خطأ في التحميل</option>')
                            .prop('disabled', false).trigger('change.select2');
                    }
                });
            });

            // ==========================================
            // 4. جلب السنوات
            // ==========================================
            $('#branch_id').on('change', function() {
                let branchId = $(this).val();
                let yearSelect = $('#academic_year_id');

                yearSelect.html('<option value="">⏳ جاري التحميل...</option>').prop('disabled', true);
                $('#section_id').html('<option value="">-- بانتظار استكمال البيانات --</option>').prop(
                    'disabled', true);
                $('#btn_load_schedule').prop('disabled', true);

                if (branchId) {
                    let ajaxUrl = "{{ route('admin.schedules.get-filters', ':id') }}".replace(':id',
                        branchId);
                    $.get(ajaxUrl, function(data) {
                        let options =
                            '<option value="" selected disabled>-- اختر العام الدراسي --</option>';
                        if (data && data.length > 0) {
                            $.each(data, function(index, year) {
                                options +=
                                    `<option value="${year.id}">${year.name}</option>`;
                            });
                            yearSelect.html(options).prop('disabled', false);
                        } else {
                            yearSelect.html(
                                '<option value="" disabled>لا توجد سنوات لهذا الفرع</option>');
                        }
                        checkAndFetchSections();
                    }).fail(function() {
                        yearSelect.html('<option value="" disabled>حدث خطأ بالاتصال</option>');
                    });
                }
            });

            // ==========================================
            // 5. جلب الشعب 
            // ==========================================
            $('#academic_year_id, #grade_id').on('change', function() {
                checkAndFetchSections();
            });

            function checkAndFetchSections() {
                let branchId = $('#branch_id').val();
                let yearId = $('#academic_year_id').val();
                let gradeId = $('#grade_id').val();
                let sectionSelect = $('#section_id');

                if (branchId && yearId && gradeId) {
                    sectionSelect.html('<option value="">⏳ جاري التحميل...</option>').prop('disabled', true);
                    $('#btn_load_schedule').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('admin.schedules.get-sections-ajax') }}",
                        type: 'GET',
                        data: {
                            branch_id: branchId,
                            academic_year_id: yearId,
                            grade_id: gradeId
                        },
                        success: function(data) {
                            let options =
                                '<option value="" selected disabled>-- اختر الشعبة --</option>';
                            if (data && data.length > 0) {
                                $.each(data, function(index, section) {
                                    options +=
                                        `<option value="${section.id}">${section.name_ar}</option>`;
                                });
                                sectionSelect.html(options).prop('disabled', false);
                            } else {
                                sectionSelect.html(
                                    '<option value="" disabled>لا توجد شعب مطابقة</option>');
                            }
                        }
                    });
                }
            }

            // ==========================================
            // 🔥 6. تحميل الجدول للفرع/الشعبة المحددة 🔥
            // ==========================================
            $('#section_id').on('change', function() {
                $('#btn_load_schedule').prop('disabled', !$(this).val());
            });

            $('#btn_load_schedule').on('click', function() {
                let sectionId = $('#section_id').val();
                if (!sectionId) return;

                $('#waiting_message').addClass('d-none');
                $('#schedule_container').removeClass('d-none');

                // تفريغ محلي وسريع لتفادي طلبات الـ AJAX
                $('.select-subject').val('').trigger('change.select2');
                $('.select-teacher').empty().append('<option value=""></option>').trigger('change.select2');
                $('.virtual-checkbox, .substitute-checkbox').prop('checked', false);

                let btn = $(this);
                btn.html('<i class="bx bx-loader bx-spin me-1"></i> جاري الجلب...').prop('disabled', true);

                $.post("{{ route('admin.schedules.get-section') }}", {
                    section_id: sectionId
                }, function(response) {
                    btn.html('<i class="bx bx-search-alt me-1"></i> استعراض الجدول').prop(
                        'disabled', false);
                    if (response.status === 'success' && response.data.length > 0) {

                        response.data.forEach(function(item) {
                            let subjectDropdown = $(
                                `select[name="schedule[${item.day_of_week}][${item.period_number}][subject_id]"]`
                            );
                            let teacherDropdown = $(
                                `select[name="schedule[${item.day_of_week}][${item.period_number}][teacher_id]"]`
                            );

                            teacherDropdown.data('saved-teacher-id', item.teacher_id);

                            // إضافة كلاس لإجبار הـ AJAX على العمل لجلب المعلمين لهذه الخلية المعينة
                            subjectDropdown.addClass('trigger-ajax-forced');
                            subjectDropdown.val(item.subject_id).trigger('change');
                            subjectDropdown.removeClass('trigger-ajax-forced');

                            if (item.is_virtual) {
                                $(`input[name="schedule[${item.day_of_week}][${item.period_number}][is_virtual]"]`)
                                    .prop('checked', true);
                            }
                        });

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'تم استرجاع الجدول بنجاح',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                }).fail(function() {
                    btn.html('<i class="bx bx-search-alt me-1"></i> استعراض الجدول').prop(
                        'disabled', false);
                });
            });

            // ==========================================
            // 🔥 7. حفظ وتفريغ الجدول بأمان تام 🔥
            // ==========================================
            $('#form_save_schedule').on('submit', function(e) {
                e.preventDefault();
                let submitBtn = $('#btn_submit_schedule');
                submitBtn.prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري الحفظ والمعالجة...');

                let formData = $(this).serialize() + '&branch_id=' + $('#branch_id').val() +
                    '&academic_year_id=' + $('#academic_year_id').val() + '&grade_id=' + $('#grade_id')
                    .val() +
                    '&section_id=' + $('#section_id').val();

                $.post("{{ route('admin.schedules.store') }}", formData, function(response) {
                    submitBtn.prop('disabled', false).html(
                        '<i class="bx bx-save me-1 align-middle"></i> حفظ وتعميم الجدول للإشعارات'
                    );
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الاعتماد!',
                        text: response.message,
                        timer: 2500,
                        showConfirmButton: false
                    });
                }).fail(function(xhr) {
                    submitBtn.prop('disabled', false).html(
                        '<i class="bx bx-save me-1 align-middle"></i> حفظ وتعميم الجدول للإشعارات'
                    );
                    Swal.fire('خطأ', xhr.responseJSON ? xhr.responseJSON.message :
                        'حدث خطأ بالاتصال بالسيرفر', 'error');
                });
            });

            // تفريغ الجدول بأمان تام دون التسبب في AJAX Cascade
            $('#btn_clear_grid').on('click', function() {
                Swal.fire({
                    title: 'تأكيد التفريغ؟',
                    text: 'هل أنت متأكد من رغبتك في تفريغ الشبكة بالكامل؟ ستحتاج لترتيب المواد والمعلمين من جديد.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، أفرغ الشبكة',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('.select-subject').val('').trigger('change.select2'); // تحديث الـ UI فقط
                        $('.select-teacher').empty().append('<option value=""></option>').trigger(
                            'change.select2');
                        $('.virtual-checkbox, .substitute-checkbox').prop('checked', false);
                    }
                });
            });

            // ==========================================
            // 🔥 8. تحويل الجدول إلى أونلاين (طوارئ) 🔥
            // ==========================================
            $('#btn_emergency_online').on('click', function() {
                let sectionId = $('#section_id').val();
                if (!sectionId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يجب اختيار الشعبة واستعراض جدولها أولاً قبل إعلان الطوارئ.',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }

                Swal.fire({
                    title: 'إعلان حالة طوارئ؟',
                    text: 'سيتم تحويل جميع حصص هذا الجدول إلى نظام البث المباشر (أونلاين) فوراً.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، حوّل أونلاين',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let btn = $(this);
                        let originalText = btn.html();
                        btn.prop('disabled', true).html(
                            '<i class="bx bx-loader bx-spin align-middle"></i> جاري التحويل...'
                        );

                        // جلب تاريخ اليوم بصيغة YYYY-MM-DD
                        let today = new Date().toISOString().split('T')[0];

                        $.post("{{ route('admin.emergency_online.declare') }}", {
                            section_id: sectionId,
                            target_date: today // إرسال تاريخ اليوم لتلبية متطلبات قاعدة البيانات
                        }, function(response) {
                            btn.prop('disabled', false).html(originalText);
                            Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح',
                                text: 'تم تحويل كافة حصص الجدول إلى أونلاين بنجاح.',
                                timer: 3000,
                                showConfirmButton: false
                            });

                            // إعادة تحميل الجدول لإظهار التعديلات الجديدة
                            $('#btn_load_schedule').trigger('click');
                        }).fail(function(xhr) {
                            btn.prop('disabled', false).html(originalText);
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: xhr.responseJSON ? xhr.responseJSON.message :
                                    'حدث خطأ بالاتصال بالسيرفر'
                            });
                        });
                    }
                });
            });

        });
    </script>
@endpush
