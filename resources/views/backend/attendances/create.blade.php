@extends('backend.layouts.master')

@section('title', 'تسجيل الحضور والغياب')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            text-align: right;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(91, 105, 188, 0.05);
        }

        .form-switch .form-check-input {
            cursor: pointer;
        }

        .form-switch .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        .bg-soft-danger {
            background-color: rgba(220, 53, 69, 0.05) !important;
            transition: background-color 0.3s ease;
        }

        .bg-soft-success {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .whatsapp-btn {
            transition: all 0.3s ease;
        }

        .whatsapp-btn:hover {
            filter: brightness(0.9);
            transform: scale(1.02);
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .card-header {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .table {
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .table thead {
            background-color: #3b4652 !important;
            color: #ced4da !important;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }

        body[data-layout-color="dark"] .text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .bg-soft-danger {
            background-color: rgba(220, 53, 69, 0.15) !important;
        }

        /* 🖨️ Print Styles */
        @media print {

            .no-print,
            .btn,
            .form-check,
            .whatsapp-btn,
            .form-switch {
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

            .bg-soft-danger {
                background-color: #fee2e2 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="page-title font-bold text-primary">
                <i class="mdi mdi-calendar-check"></i> قائمة حضور طلاب فصل - تاريخ:
                <span class="text-danger" dir="ltr">{{ $date ?? date('Y-m-d') }}</span>
            </h4>
            <a href="{{ url()->previous() }}" class="btn btn-secondary shadow-sm no-print">
                <i class="mdi mdi-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success fw-bold font-16 shadow-sm">
            <i class="mdi mdi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 🔥 شريط أدوات التحديد السريع 🔥 --}}
    @can('manage_attendance')
        <div class="row mb-3 no-print">
            <div class="col-12">
                <div
                    class="bg-light p-3 rounded shadow-sm border d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <label class="font-bold text-dark mb-0">
                            <i class="mdi mdi-checkbox-multiple-marked-outline text-primary"></i> إجراء للمحددين:
                        </label>
                        <select id="bulk-action-select" class="form-select form-select-sm border-primary" style="width: 160px;">
                            <option value="" selected disabled>اختر الإجراء...</option>
                            <option value="present">تسجيل حضور</option>
                            <option value="absent">تسجيل غياب</option>
                        </select>
                        <button type="button" id="apply-bulk-action"
                            class="btn btn-sm btn-dark font-bold px-4 shadow-sm">تطبيق</button>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" id="mark-all-present" class="btn btn-sm btn-success shadow-sm font-bold px-3">
                            <i class="mdi mdi-check-all"></i> الكل حضور
                        </button>
                        <button type="button" id="mark-all-absent" class="btn btn-sm btn-danger shadow-sm font-bold px-3">
                            <i class="mdi mdi-close-box-multiple"></i> الكل غياب
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- 🔥 الفورم 🔥 --}}
    <form action="{{ route('admin.attendances.store') }}" method="POST" id="attendanceForm">
        @csrf
        <input type="hidden" name="section_id" value="{{ $section_id }}">
        <input type="hidden" name="attendance_date" value="{{ $date ?? date('Y-m-d') }}">
        @if ($students->count() > 0)
            <input type="hidden" name="academic_year_id"
                value="{{ $academic_year_id ?? ($students->first()->academic_year_id ?? 1) }}">
            <input type="hidden" name="grade_id" value="{{ $students->first()->grade_id ?? 1 }}">
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light d-flex justify-content-between align-items-center border-bottom pb-3 pt-3">
                <span class="font-bold font-16">
                    إجمالي الطلاب: (<span class="text-primary">{{ $students->count() }}</span>)
                    | <span class="text-success"><i class="mdi mdi-check"></i> اليوم: (<span
                            id="present-count">0</span>)</span>
                    | <span class="text-danger"><i class="mdi mdi-close"></i> اليوم: (<span
                            id="absent-count">0</span>)</span>
                </span>

                @can('manage_attendance')
                    <button type="submit" class="btn btn-primary px-5 font-bold shadow-sm no-print">
                        <i class="mdi mdi-content-save"></i> حفظ وتحديث القائمة
                    </button>
                @endcan
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0 text-center align-middle"
                        style="white-space: nowrap;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="no-print">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input border-secondary" type="checkbox"
                                            id="select-all-checkbox" style="transform: scale(1.3);"
                                            {{ auth()->user()->can('manage_attendance') ? '' : 'disabled' }}>
                                    </div>
                                </th>
                                <th>#</th>
                                <th>اسم الطالب</th>
                                <th>إحصائيات السنة</th>
                                <th>التاريخ</th>
                                <th>الحضور والغياب</th>
                                <th>طريقة الحضور</th>
                                <th>إجراء (تنبيه واتساب)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                                @php
                                    $record = $attendances[$student->id] ?? null;
                                    $currentStatus = $record ? $record->status : 'present';
                                    $currentReason = $record ? $record->absence_reason : 'unexcused';
                                    $currentMode = $record ? $record->attendance_mode : 'in_person';
                                    $parentPhone = $student->parent->phone ?? '';
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $parentPhone);
                                @endphp
                                <tr class="{{ $currentStatus == 'absent' ? 'bg-soft-danger' : '' }}"
                                    id="row_{{ $student->id }}">
                                    <td class="no-print">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input border-secondary student-checkbox"
                                                type="checkbox" value="{{ $student->id }}" style="transform: scale(1.3);"
                                                {{ auth()->user()->can('manage_attendance') ? '' : 'disabled' }}>
                                        </div>
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold text-start">
                                        <a href="javascript:void(0);" class="text-primary text-decoration-none"
                                            onclick="showStudentProfile({{ $student->id }})">
                                            {{ $student->first_name }} {{ $student->tribe }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-success text-success p-1 px-2 me-1"
                                            title="أيام الحضور">
                                            <i class="mdi mdi-check"></i> {{ $student->present_days ?? 0 }}
                                        </span>
                                        <span class="badge bg-soft-danger text-danger p-1 px-2" title="أيام الغياب">
                                            <i class="mdi mdi-close"></i> {{ $student->absent_days ?? 0 }}
                                        </span>
                                    </td>
                                    <td dir="ltr" class="text-muted font-13">{{ $date ?? date('Y-m-d') }}</td>
                                    <td style="width: 250px;">
                                        <div class="btn-group w-100 shadow-sm mb-2" role="group">
                                            <input type="radio" class="btn-check status-radio"
                                                name="attendances[{{ $student->id }}]" id="present_{{ $student->id }}"
                                                value="present" autocomplete="off"
                                                {{ $currentStatus == 'present' ? 'checked' : '' }}
                                                {{ auth()->user()->can('manage_attendance') ? '' : 'disabled' }}>
                                            <label class="btn btn-outline-success font-bold py-1"
                                                for="present_{{ $student->id }}">
                                                <i class="mdi mdi-check"></i> حضور
                                            </label>

                                            <input type="radio" class="btn-check status-radio"
                                                name="attendances[{{ $student->id }}]" id="absent_{{ $student->id }}"
                                                value="absent" autocomplete="off"
                                                {{ $currentStatus == 'absent' ? 'checked' : '' }}
                                                {{ auth()->user()->can('manage_attendance') ? '' : 'disabled' }}>
                                            <label class="btn btn-outline-danger font-bold py-1"
                                                for="absent_{{ $student->id }}">
                                                <i class="mdi mdi-close"></i> غياب
                                            </label>
                                        </div>
                                        <div class="reason-container mt-1" id="reason_container_{{ $student->id }}"
                                            style="{{ $currentStatus == 'present' ? 'display:none;' : '' }}">
                                            <select
                                                class="form-select form-select-sm border-danger reason-select shadow-sm font-12"
                                                name="absence_reasons[{{ $student->id }}]"
                                                data-student-id="{{ $student->id }}"
                                                data-student-name="{{ $student->first_name }}"
                                                {{ auth()->user()->can('manage_attendance') ? '' : 'disabled' }}>
                                                <option value="unexcused"
                                                    {{ $currentReason == 'unexcused' ? 'selected' : '' }}>بدون عذر (إنذار)
                                                </option>
                                                <option value="sick" {{ $currentReason == 'sick' ? 'selected' : '' }}>
                                                    عذر طبي (مرضي)</option>
                                                <option value="family" {{ $currentReason == 'family' ? 'selected' : '' }}>
                                                    إذن مسبق / عائلي</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch d-flex justify-content-center align-items-center mb-0">
                                            <input class="form-check-input shadow-sm" type="checkbox" role="switch"
                                                name="mode[{{ $student->id }}]" id="mode_{{ $student->id }}"
                                                value="online" {{ $currentMode == 'online' ? 'checked' : '' }}
                                                style="width: 40px; height: 20px;"
                                                {{ auth()->user()->can('manage_attendance') ? '' : 'disabled' }}>
                                            <label class="form-check-label ms-2 fw-bold text-muted font-13"
                                                for="mode_{{ $student->id }}">أونلاين 🌐</label>
                                        </div>
                                    </td>
                                    <td style="width: 200px;">
                                        @if ($cleanPhone)
                                            @can('manage_attendance')
                                                <a href="#" target="_blank"
                                                    class="btn btn-sm px-3 py-2 font-bold shadow-sm whatsapp-btn w-100"
                                                    style="display: {{ $currentStatus == 'present' ? 'none' : 'block' }};"
                                                    id="wa_{{ $student->id }}" data-phone="{{ $cleanPhone }}">
                                                    <i class="mdi mdi-whatsapp font-18 align-middle"></i>
                                                    <span id="wa_text_{{ $student->id }}" class="align-middle">إرسال
                                                        تنبيه</span>
                                                </a>
                                            @else
                                                <span class="badge bg-secondary p-2 w-100"
                                                    style="display: {{ $currentStatus == 'present' ? 'none' : 'inline-block' }};">
                                                    <i class="mdi mdi-lock"></i> غير مصرح
                                                </span>
                                            @endcan
                                        @else
                                            <span class="badge bg-secondary p-2 mt-2 w-100"
                                                style="display: {{ $currentStatus == 'present' ? 'none' : 'inline-block' }};"
                                                id="wa_none_{{ $student->id }}">لا يوجد هاتف</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-muted py-5 text-center font-16">لا يوجد طلاب مسجلين في
                                        هذه الشعبة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>

    {{-- 🔥 النافذة المنبثقة للملف الشخصي 🔥 --}}
    <div class="modal fade" id="studentProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-bold text-white">
                        <i class="mdi mdi-account-details"></i> الملف الشخصي والتقييم
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div id="modal-loader" class="text-center py-5">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                        <h6 class="mt-3 text-muted font-bold">جاري جلب بيانات وتقييمات الطالب...</h6>
                    </div>
                    <div id="modal-data" style="display: none;">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary font-bold border-bottom pb-2 mb-3">بيانات الطالب</h6>
                                        <p class="mb-1"><strong>الاسم:</strong> <span id="modal-student-name"
                                                class="text-dark"></span></p>
                                        <p class="mb-1"><strong>الصف:</strong> <span id="modal-grade"
                                                class="text-dark"></span></p>
                                        <p class="mb-1"><strong>الشعبة:</strong> <span id="modal-section"
                                                class="text-dark"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body">
                                        <h6 class="text-primary font-bold border-bottom pb-2 mb-3">بيانات ولي الأمر</h6>
                                        <p class="mb-1"><strong>الاسم:</strong> <span id="modal-parent-name"
                                                class="text-dark"></span></p>
                                        <p class="mb-1"><strong>رقم التواصل:</strong> <span id="modal-parent-phone"
                                                dir="ltr" class="text-dark font-bold"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h6 class="font-bold text-dark mb-3"><i class="mdi mdi-chart-line text-success"></i> آخر التقييمات
                            الأكاديمية</h6>
                        <div class="table-responsive bg-white shadow-sm rounded border">
                            <table class="table table-bordered table-sm text-center mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>المستوى العام</th>
                                        <th>نقاط الضعف (مواد)</th>
                                        <th>ملاحظات المعلم</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-evaluations-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary font-bold px-4 shadow-sm"
                        data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // ✅ إعداد CSRF Token لكل طلبات AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // جعل دالة الـ AJAX مرئية عالمياً
        window.showStudentProfile = function(studentId) {
            let myModalElement = document.getElementById('studentProfileModal');
            let myModal = bootstrap.Modal.getInstance(myModalElement) || new bootstrap.Modal(myModalElement);

            // ✅ إعادة تعيين الـ loader
            $('#modal-loader').show().html(`
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <h6 class="mt-3 text-muted font-bold">جاري جلب بيانات وتقييمات الطالب...</h6>
            `);
            $('#modal-data').hide();
            $('#modal-evaluations-body').empty();
            myModal.show();

            // ✅ استخدام route() من Laravel
            let profileUrl = "{{ route('admin.attendances.student_profile', ':id') }}".replace(':id', studentId);

            $.ajax({
                url: profileUrl,
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        let student = response.data;
                        $('#modal-student-name').text(student.first_name + ' ' + (student.tribe || ''));
                        $('#modal-grade').text(student.grade ? student.grade.name_ar : 'غير محدد');
                        $('#modal-section').text(student.section ? student.section.name_ar : 'غير محدد');

                        let parentName = student.parent ? student.parent.name_ar : 'غير مسجل';
                        let parentPhone = student.parent ? student.parent.phone : '-';
                        $('#modal-parent-name').text(parentName);
                        $('#modal-parent-phone').text(parentPhone);

                        if (student.evaluations && student.evaluations.length > 0) {
                            let rows = '';
                            $.each(student.evaluations, function(index, eval) {
                                let date = new Date(eval.created_at).toLocaleDateString('ar-EG');
                                let levelBadge =
                                    `<span class="badge bg-primary px-2 py-1">${eval.overall_level || '-'}</span>`;
                                rows += `<tr>
                                    <td class="text-muted font-13">${date}</td>
                                    <td>${levelBadge}</td>
                                    <td class="text-danger font-bold">${eval.weak_subjects || '-'}</td>
                                    <td>${eval.teacher_notes || '-'}</td>
                                </tr>`;
                            });
                            $('#modal-evaluations-body').html(rows);
                        } else {
                            $('#modal-evaluations-body').html(
                                '<tr><td colspan="4" class="text-muted py-4 font-15">لا توجد تقييمات سابقة مسجلة لهذا الطالب.</td></tr>'
                            );
                        }
                        $('#modal-loader').hide();
                        $('#modal-data').fadeIn(300);
                    }
                },
                error: function(xhr) {
                    $('#modal-loader').html(
                        '<div class="alert alert-danger m-3 font-bold text-center">حدث خطأ أثناء جلب البيانات. يرجى المحاولة مرة أخرى.</div>'
                    );
                }
            });
        };

        $(document).ready(function() {
            $('#mark-all-present').click(function() {
                $('.status-radio[value="present"]:not(:disabled)').prop('checked', true).trigger('change');
            });

            $('#mark-all-absent').click(function() {
                $('.status-radio[value="absent"]:not(:disabled)').prop('checked', true).trigger('change');
            });

            $('#select-all-checkbox').change(function() {
                $('.student-checkbox:not(:disabled)').prop('checked', $(this).prop('checked'));
            });

            $('#apply-bulk-action').click(function() {
                let action = $('#bulk-action-select').val();
                let selectedCount = $('.student-checkbox:checked').length;

                if (selectedCount === 0) return alert('يرجى تحديد طالب واحد على الأقل من الجدول.');
                if (!action) return alert('يرجى اختيار الإجراء (حضور أو غياب) من القائمة المنسدلة.');

                $('.student-checkbox:checked').each(function() {
                    let studentId = $(this).val();
                    $('#' + action + '_' + studentId + ':not(:disabled)').prop('checked', true)
                        .trigger('change');
                });

                $('.student-checkbox').prop('checked', false);
                $('#select-all-checkbox').prop('checked', false);
                $('#bulk-action-select').val('');
            });

            // ✅ دالة محسّنة للعدادات (تعد فقط status radios)
            function updateCounters() {
                let presentCount = 0;
                let absentCount = 0;

                $('input.status-radio:checked').each(function() {
                    if ($(this).val() === 'present') presentCount++;
                    else if ($(this).val() === 'absent') absentCount++;
                });

                $('#present-count').text(presentCount);
                $('#absent-count').text(absentCount);
            }

            function updateWhatsappLink(studentId, studentName, reason) {
                let phone = $('#wa_' + studentId).data('phone');
                if (!phone) return;

                let message = "",
                    btnText = "",
                    btnColor = "";

                if (reason === 'sick') {
                    message =
                        `ولي أمر الطالب/ـة (${studentName})، لاحظنا غياب ابنكم اليوم لظروف صحية. تتمنى له إدارة مدرسة الطالب الذكي الشفاء العاجل والعودة سالماً.`;
                    btnText = "أمنيات بالشفاء";
                    btnColor = "#198754";
                } else if (reason === 'family') {
                    message =
                        `ولي أمر الطالب/ـة (${studentName})، تم إثبات غياب ابنكم اليوم بناءً على الإذن المسبق. شكراً لتواصلكم مع إدارة المدرسة.`;
                    btnText = "تأكيد الإذن";
                    btnColor = "#0dcaf0";
                } else {
                    message =
                        `إنذار غياب: ولي أمر الطالب/ـة (${studentName})، نفيدكم بغياب ابنكم اليوم بدون إشعار مسبق. يرجى توضيح السبب ومراجعة إدارة المدرسة تجنباً لتطبيق لائحة الغياب.`;
                    btnText = "إرسال إنذار رسمي";
                    btnColor = "#dc3545";
                }

                $('#wa_' + studentId).attr('href', 'https://wa.me/' + phone + '?text=' + encodeURIComponent(
                    message));
                $('#wa_text_' + studentId).text(btnText);
                $('#wa_' + studentId).css({
                    'background-color': btnColor,
                    'color': '#fff',
                    'border': 'none'
                });
            }

            // ✅ تحسين استخراج studentId
            $('.status-radio').change(function() {
                updateCounters();

                // استخراج studentId من name="attendances[123]"
                let name = $(this).attr('name');
                let match = name.match(/attendances\[(\d+)\]/);
                if (!match) return;

                let studentId = match[1];
                let studentName = $('#reason_container_' + studentId + ' .reason-select').data(
                    'student-name');
                let row = $('#row_' + studentId);

                if ($(this).val() === 'absent') {
                    row.addClass('bg-soft-danger');
                    $('#reason_container_' + studentId).slideDown(150);
                    $('#wa_' + studentId).fadeIn(150);
                    $('#wa_none_' + studentId).fadeIn(150);
                    updateWhatsappLink(studentId, studentName, $('#reason_container_' + studentId +
                        ' .reason-select').val());
                } else {
                    row.removeClass('bg-soft-danger');
                    $('#reason_container_' + studentId).slideUp(150);
                    $('#wa_' + studentId).fadeOut(150);
                    $('#wa_none_' + studentId).fadeOut(150);
                }
            });

            $('.reason-select').change(function() {
                updateWhatsappLink($(this).data('student-id'), $(this).data('student-name'), $(this).val());
            });

            updateCounters();

            // تهيئة روابط الواتساب عند تحميل الصفحة
            $('.reason-select').each(function() {
                if ($('#absent_' + $(this).data('student-id')).is(':checked')) {
                    updateWhatsappLink($(this).data('student-id'), $(this).data('student-name'), $(this)
                        .val());
                }
            });
        });
    </script>
@endpush
