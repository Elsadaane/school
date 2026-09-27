@extends('layout.master')
@section('title', 'تحضير الطلاب')
@section('title_page', 'سجل الحضور والغياب اليومي')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .student-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eef2f5;
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .student-card:hover {
            border-color: #673ab7;
            box-shadow: 0 6px 15px rgba(103, 58, 183, 0.1);
        }

        .status-options {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .status-options input[type="radio"] {
            display: none;
        }

        .status-options label {
            flex: 1;
            text-align: center;
            padding: 8px 5px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            border: 1.5px solid #eef2f5;
            color: #64748b;
            transition: 0.2s;
            font-size: 13px;
        }

        /* ألوان الحالات عند التحديد */
        .status-options input[value="present"]:checked+label {
            background-color: #d1e7dd;
            color: #0f5132;
            border-color: #badbcc;
        }

        .status-options input[value="absent"]:checked+label {
            background-color: #f8d7da;
            color: #842029;
            border-color: #f5c2c7;
        }

        .status-options input[value="late"]:checked+label {
            background-color: #fff3cd;
            color: #664d03;
            border-color: #ffecb5;
        }

        .status-options input[value="excused"]:checked+label {
            background-color: #cff4fc;
            color: #055160;
            border-color: #b6effb;
        }

        .avatar-initial {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #673ab7, #512da8);
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        /* تنسيق العدادات الذكية */
        .counter-box {
            padding: 10px 15px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
            min-width: 90px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">

            @if ($errors->any())
                <div class="alert alert-danger fw-bold shadow-sm rounded-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- هيدر معلومات الحصة --}}
            <div class="card shadow-sm border-0 mb-4 rounded-4 bg-primary bg-opacity-10 border-primary border-opacity-25">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold text-primary mb-1"><i class="bx bx-book-bookmark"></i> مادة:
                            {{ $schedule->subject->name_ar }}</h5>
                        <p class="mb-0 text-dark fw-bold">{{ $schedule->grade->name_ar }} -
                            {{ $schedule->section->name_ar }} | <span class="text-muted fw-normal">الحصة رقم
                                {{ $schedule->period_number }}</span></p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-white text-primary border border-primary px-3 py-2 fs-6 shadow-sm"><i
                                class="bx bx-calendar"></i> {{ $today }}</span>
                    </div>
                </div>
            </div>

            {{-- 🔥 الإحصائيات الحية وأدوات التحكم 🔥 --}}
            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-body">
                    <div class="row align-items-center g-3">
                        <div class="col-md-5">
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="bx bx-search text-muted"></i></span>
                                <input type="text" id="studentSearch" class="form-control border-start-0"
                                    placeholder="بحث سريع باسم الطالب...">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex justify-content-md-end justify-content-center gap-2 flex-wrap">
                                <div
                                    class="counter-box bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                    حاضر: <span id="count-present">0</span>
                                </div>
                                <div
                                    class="counter-box bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                    غائب: <span id="count-absent">0</span>
                                </div>
                                <div
                                    class="counter-box bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 text-dark">
                                    متأخر: <span id="count-late">0</span>
                                </div>
                                <div
                                    class="counter-box bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                    مستأذن: <span id="count-excused">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('teacher.attendance.store', $schedule->id) }}" method="POST" id="attendanceForm">
                @csrf

                <div class="d-flex justify-content-between align-items-center mb-3 px-2 flex-wrap gap-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="bx bx-group text-primary fs-4 align-middle"></i> قائمة
                        الطلاب ({{ $students->count() }})</h5>

                    <div class="d-flex gap-2">
                        <button type="button" id="mark_all_present"
                            class="btn btn-sm btn-success fw-bold rounded-pill shadow-sm">
                            <i class="bx bx-check-double"></i> تحضير الكل
                        </button>
                        <button type="button" id="mark_all_absent"
                            class="btn btn-sm btn-outline-danger fw-bold rounded-pill shadow-sm">
                            <i class="bx bx-x"></i> تغييب الكل
                        </button>
                    </div>
                </div>

                {{-- قائمة الطلاب --}}
                <div class="row g-3" id="studentsList">
                    @forelse($students as $student)
                        @php
                            // جلب السجل بالكامل لو موجود
                            $record = $attendances->get($student->id);
                            $currentStatus = $record ? $record->status : 'present';
                            $currentNote = $record ? $record->notes : '';
                        @endphp
                        <div class="col-md-6 col-xl-4 student-item">
                            <div class="card student-card h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="avatar-initial shadow-sm">
                                            {{ mb_substr($student->first_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark student-name">{{ $student->first_name }}
                                                {{ $student->second_name }} {{ $student->tribe }}</h6>
                                            <small class="text-muted">كود: {{ $student->id }}</small>
                                        </div>
                                    </div>

                                    {{-- زراير الحالات --}}
                                    <div class="status-options">
                                        <div>
                                            <input type="radio" class="status-radio"
                                                name="attendance[{{ $student->id }}][status]"
                                                id="status_p_{{ $student->id }}" value="present"
                                                {{ $currentStatus == 'present' ? 'checked' : '' }}>
                                            <label for="status_p_{{ $student->id }}"><i class="bx bx-check"></i>
                                                حاضر</label>
                                        </div>
                                        <div>
                                            <input type="radio" class="status-radio"
                                                name="attendance[{{ $student->id }}][status]"
                                                id="status_a_{{ $student->id }}" value="absent"
                                                {{ $currentStatus == 'absent' ? 'checked' : '' }}>
                                            <label for="status_a_{{ $student->id }}"><i class="bx bx-x"></i> غائب</label>
                                        </div>
                                        <div>
                                            <input type="radio" class="status-radio"
                                                name="attendance[{{ $student->id }}][status]"
                                                id="status_l_{{ $student->id }}" value="late"
                                                {{ $currentStatus == 'late' ? 'checked' : '' }}>
                                            <label for="status_l_{{ $student->id }}"><i class="bx bx-time"></i>
                                                متأخر</label>
                                        </div>
                                        <div>
                                            <input type="radio" class="status-radio"
                                                name="attendance[{{ $student->id }}][status]"
                                                id="status_e_{{ $student->id }}" value="excused"
                                                {{ $currentStatus == 'excused' ? 'checked' : '' }}>
                                            <label for="status_e_{{ $student->id }}"><i class="bx bx-envelope"></i>
                                                مستأذن</label>
                                        </div>
                                    </div>

                                    {{-- ملاحظات للطالب --}}
                                    <div class="mt-3">
                                        <input type="text" name="attendance[{{ $student->id }}][notes]"
                                            class="form-control form-control-sm border-light bg-light"
                                            placeholder="ملاحظات (اختياري)..." value="{{ $currentNote }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="bx bx-group text-muted opacity-25" style="font-size: 60px;"></i>
                            <h5 class="fw-bold mt-3 text-muted">لا يوجد طلاب مسجلين في هذه الشعبة حتى الآن!</h5>
                        </div>
                    @endforelse
                </div>

                <div class="position-sticky bottom-0 bg-white border-top p-3 text-center shadow-lg mt-4"
                    style="z-index: 10;">
                    <button type="button" id="btnSubmitForm"
                        class="btn btn-primary px-5 py-2 fw-bold fs-5 rounded-pill shadow">
                        <i class="bx bx-save"></i> حفظ واعتماد الغياب
                    </button>
                    <a href="{{ route('teacher.dashboard') }}"
                        class="btn btn-light border px-4 py-2 fw-bold rounded-pill ms-2">إلغاء</a>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            // 1. وظيفة تحديث الإحصائيات الحية
            function updateCounters() {
                $('#count-present').text($('input[type="radio"][value="present"]:checked').length);
                $('#count-absent').text($('input[type="radio"][value="absent"]:checked').length);
                $('#count-late').text($('input[type="radio"][value="late"]:checked').length);
                $('#count-excused').text($('input[type="radio"][value="excused"]:checked').length);
            }

            // تحديث عند تحميل الصفحة وعند أي تغيير
            updateCounters();
            $('.status-radio').on('change', updateCounters);

            // 2. أزرار التحضير السريعة
            $('#mark_all_present').on('click', function() {
                $('input[type="radio"][value="present"]').prop('checked', true);
                updateCounters();
            });

            $('#mark_all_absent').on('click', function() {
                $('input[type="radio"][value="absent"]').prop('checked', true);
                updateCounters();
            });

            // 3. البحث السريع في أسماء الطلاب
            $("#studentSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".student-item").filter(function() {
                    $(this).toggle($(this).find('.student-name').text().toLowerCase().indexOf(
                        value) > -1)
                });
            });

            // 4. تأكيد قبل الإرسال باستخدام SweetAlert
            $('#btnSubmitForm').on('click', function() {
                let present = $('#count-present').text();
                let absent = $('#count-absent').text();
                let late = $('#count-late').text();
                let excused = $('#count-excused').text();

                let summaryHtml = `
                    <div class="d-flex justify-content-around mt-3 mb-2" style="font-size: 14px;">
                        <span class="text-success fw-bold">حاضر: ${present}</span>
                        <span class="text-danger fw-bold">غائب: ${absent}</span>
                        <span class="text-warning fw-bold text-dark">متأخر: ${late}</span>
                        <span class="text-info fw-bold">مستأذن: ${excused}</span>
                    </div>
                `;

                Swal.fire({
                    title: 'تأكيد حفظ الغياب',
                    html: `هل أنت متأكد من حفظ سجل الحضور والغياب لهذه الحصة؟ ${summaryHtml}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، اعتمد الحفظ',
                    cancelButtonText: 'مراجعة مرة أخرى'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#attendanceForm').submit();
                    }
                });
            });

        });
    </script>
@endpush
