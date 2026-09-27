@extends('layout.master') {{-- تأكد من مسار اللي أوت بتاعك --}}
@section('title', 'رصد التقييمات')
@section('title_page', 'رصد التقييمات الأكاديمية والملاحظات')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .student-eval-card {
            background: #fff;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            /* تم زيادة السمك لظهور التأثير اللوني */
            transition: 0.3s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .student-eval-card:hover {
            border-color: #673ab7;
            box-shadow: 0 5px 15px rgba(103, 58, 183, 0.08);
            transform: translateY(-2px);
        }

        /* تأثير الكارت المكتمل */
        .student-eval-card.evaluated {
            border-color: #10b981 !important;
            background-color: #f8fafc;
        }

        .student-avatar {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #673ab7 0%, #512da8 100%);
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .sticky-footer {
            position: sticky;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 100;
            border-top: 1px solid #e2e8f0;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #673ab7;
            box-shadow: 0 0 0 0.25rem rgba(103, 58, 183, 0.25);
        }

        /* تنسيق العدادات الذكية */
        .counter-box {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
            min-width: 120px;
        }
    </style>
@endpush

@section('content')
    <div class="row pb-5 mb-5">
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
                        <h5 class="fw-bold text-primary mb-1"><i class="bx bx-bar-chart-alt-2"></i> رصد تقييمات مادة:
                            {{ $schedule->subject->name_ar }}</h5>
                        <p class="mb-0 text-dark fw-bold">{{ $schedule->grade->name_ar }} -
                            {{ $schedule->section->name_ar }} | <span class="text-muted fw-normal">إرسال التقارير لولي
                                الأمر</span></p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-white text-primary border border-primary px-3 py-2 fs-6 shadow-sm">
                            <i class="bx bx-group"></i> إجمالي الطلاب: {{ $students->count() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 🔥 الإحصائيات الحية وأدوات البحث 🔥 --}}
            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-body">
                    <div class="row align-items-center g-3">
                        <div class="col-md-5">
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="bx bx-search text-muted"></i></span>
                                <input type="text" id="studentSearch" class="form-control border-start-0"
                                    placeholder="بحث سريع باسم الطالب أو الكود...">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex justify-content-md-end justify-content-center gap-2 flex-wrap">
                                <div
                                    class="counter-box bg-success bg-opacity-10 text-success border border-success border-opacity-25 shadow-sm">
                                    <i class="bx bx-check-double"></i> تم التقييم: <span id="count-evaluated">0</span>
                                </div>
                                <div
                                    class="counter-box bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 shadow-sm">
                                    <i class="bx bx-time"></i> متبقي: <span
                                        id="count-pending">{{ $students->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('teacher.evaluations.store', $schedule->id) }}" method="POST" id="evaluationForm">
                @csrf

                {{-- تنبيه للمعلم --}}
                <div class="alert alert-info py-2 font-13 fw-bold border-info shadow-sm rounded-3">
                    <i class="bx bx-info-circle fs-5 align-middle me-1"></i>
                    تنبيه: عند الضغط على "حفظ واعتماد"، سيقوم النظام بإرسال إشعار فوري للطلاب وأولياء الأمور بالتقييمات
                    الجديدة.
                </div>

                {{-- قائمة الطلاب --}}
                <div class="row g-3 mt-1" id="studentsList">
                    @forelse($students as $student)
                        @php
                            // جلب التقييم القديم لو المعلم كان مسجله قبل كده
                            $eval = $evaluations[$student->id] ?? null;
                        @endphp
                        <div class="col-12 student-item">
                            <div class="card student-eval-card h-100">
                                <div class="card-header bg-light border-bottom p-3 d-flex align-items-center gap-3">
                                    <div class="student-avatar shadow-sm">
                                        {{ mb_substr($student->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark student-name">{{ $student->first_name }}
                                            {{ $student->second_name }} {{ $student->tribe }}</h6>
                                        <small class="text-muted font-monospace student-id">كود:
                                            {{ $student->id }}</small>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        {{-- التقييم العام --}}
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small text-muted">التقييم العام <span
                                                    class="text-danger">*</span></label>
                                            <select name="evaluations[{{ $student->id }}][overall_level]"
                                                class="form-select fw-bold border-primary eval-select" required>
                                                <option value="" disabled {{ !$eval ? 'selected' : '' }}>-- اختر
                                                    التقييم --</option>
                                                <option value="ممتاز"
                                                    {{ ($eval->overall_level ?? '') == 'ممتاز' ? 'selected' : '' }}>🌟
                                                    ممتاز</option>
                                                <option value="جيد جداً"
                                                    {{ ($eval->overall_level ?? '') == 'جيد جداً' ? 'selected' : '' }}>👍
                                                    جيد جداً</option>
                                                <option value="جيد"
                                                    {{ ($eval->overall_level ?? '') == 'جيد' ? 'selected' : '' }}>🙂 جيد
                                                </option>
                                                <option value="مقبول"
                                                    {{ ($eval->overall_level ?? '') == 'مقبول' ? 'selected' : '' }}>😐
                                                    مقبول</option>
                                                <option value="يحتاج دعم"
                                                    {{ ($eval->overall_level ?? '') == 'يحتاج دعم' ? 'selected' : '' }}>⚠️
                                                    يحتاج دعم إضافي</option>
                                            </select>
                                        </div>

                                        {{-- الدرجة السابقة / الحالية --}}
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold small text-muted">الدرجة (من 100)</label>
                                            <input type="number" step="0.1" min="0" max="100"
                                                name="evaluations[{{ $student->id }}][previous_year_score]"
                                                class="form-control fw-bold text-center text-primary"
                                                value="{{ $eval->previous_year_score ?? '' }}" placeholder="مثال: 95.5">
                                        </div>

                                        {{-- نقاط الضعف --}}
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small text-muted">نقاط تحتاج تحسين (إن
                                                وجد)</label>
                                            <input type="text" name="evaluations[{{ $student->id }}][weak_subjects]"
                                                class="form-control bg-light" value="{{ $eval->weak_subjects ?? '' }}"
                                                placeholder="مثال: القراءة، التركيز...">
                                        </div>

                                        {{-- ملاحظات المعلم --}}
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small text-muted">ملاحظات عامة لولي
                                                الأمر</label>
                                            <textarea name="evaluations[{{ $student->id }}][teacher_notes]" class="form-control bg-light" rows="1"
                                                placeholder="أكتب ملاحظاتك التشجيعية أو التوجيهية هنا...">{{ $eval->teacher_notes ?? '' }}</textarea>
                                        </div>
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

                {{-- زر الحفظ العائم (Sticky Footer) --}}
                @if ($students->count() > 0)
                    <div class="sticky-footer p-3 text-center shadow-lg rounded-top-4 mt-4">
                        <button type="button" id="btnSubmitForm"
                            class="btn btn-primary px-5 py-2 fw-bold fs-5 rounded-pill shadow">
                            <i class="bx bx-send me-1"></i> حفظ واعتماد وإرسال الإشعارات
                        </button>
                        <a href="{{ route('teacher.dashboard') }}"
                            class="btn btn-light border px-4 py-2 fw-bold rounded-pill ms-2">رجوع للجدول</a>
                    </div>
                @endif
            </form>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            // 1. وظيفة تحديث العدادات وتغيير ألوان الكروت بناءً على التقييم
            function updateCounters() {
                let total = $('.student-eval-card').length;
                let evaluated = 0;

                $('.eval-select').each(function() {
                    let card = $(this).closest('.student-eval-card');
                    if ($(this).val()) {
                        evaluated++;
                        card.addClass('evaluated'); // تلوين الكارت للأخضر
                    } else {
                        card.removeClass('evaluated'); // إرجاع اللون الافتراضي
                    }
                });

                $('#count-evaluated').text(evaluated);
                $('#count-pending').text(total - evaluated);
            }

            // تشغيل التحديث عند تحميل الصفحة وعند أي تغيير في التقييم
            updateCounters();
            $('.eval-select').on('change', updateCounters);

            // 2. البحث السريع في أسماء الطلاب
            $("#studentSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".student-item").filter(function() {
                    var text = $(this).find('.student-name').text().toLowerCase() + " " + $(this)
                        .find('.student-id').text().toLowerCase();
                    $(this).toggle(text.indexOf(value) > -1)
                });
            });

            // 3. تأكيد قبل الإرسال باستخدام SweetAlert
            $('#btnSubmitForm').on('click', function(e) {
                let total = $('.student-eval-card').length;
                let evaluated = parseInt($('#count-evaluated').text());
                let pending = parseInt($('#count-pending').text());

                // التحقق الأساسي من الـ Required Fields في الـ Form
                let isValid = true;
                $('.eval-select').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    Swal.fire('تنبيه!', 'يرجى استكمال التقييم العام لجميع الطلاب قبل الاعتماد.', 'warning');
                    return;
                }

                let summaryHtml = `
                    <div class="mt-3 mb-2" style="font-size: 15px;">
                        <span class="text-success fw-bold d-block mb-1"><i class="bx bx-check-circle"></i> تم تقييم: ${evaluated} طالب</span>
                        ${pending > 0 ? `<span class="text-danger fw-bold d-block"><i class="bx bx-error"></i> متبقي: ${pending} طالب بدون تقييم!</span>` : ''}
                        <br>
                        <small class="text-muted">سيتم إرسال إشعارات فورية لأولياء الأمور بالنتيجة.</small>
                    </div>
                `;

                Swal.fire({
                    title: 'تأكيد اعتماد التقييمات',
                    html: summaryHtml,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#673ab7',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، اعتمد وأرسل الإشعارات',
                    cancelButtonText: 'مراجعة مرة أخرى'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // إظهار لوادر أثناء الإرسال لمنع الدبل كليك
                        Swal.fire({
                            title: 'جاري الحفظ وإرسال الإشعارات...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        $('#evaluationForm').submit();
                    }
                });
            });

        });
    </script>
@endpush
