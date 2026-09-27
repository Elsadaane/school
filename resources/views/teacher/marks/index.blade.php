@extends('backend.layouts.master')
@section('title', 'رصد الدرجات الذكي')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        /* 🔥 حاوية الجدول عشان الـ Scroll يشتغل زي إكسيل 🔥 */
        .table-responsive-excel {
            max-height: 65vh;
            overflow-y: auto;
            overflow-x: auto;
            background: #fff;
            border-radius: 8px;
        }

        .grid-table {
            border-collapse: separate;
            border-spacing: 0;
            min-width: 100%;
        }

        /* 🌟 تثبيت رأس الجدول 🌟 */
        .grid-table thead th {
            position: sticky;
            top: 0;
            background-color: #f1f5f9;
            font-weight: 900;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            border-top: none;
            text-align: center;
            vertical-align: middle;
            z-index: 20;
            box-shadow: 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        /* 🌟 تثبيت أول عمودين (الرقم والاسم) 🌟 */
        .grid-table tbody td:nth-child(1),
        .grid-table tbody th:nth-child(1),
        .grid-table thead th:nth-child(1) {
            position: sticky;
            right: 0;
            z-index: 10;
            background-color: #f8fafc;
            border-right: none;
        }

        .grid-table tbody td:nth-child(2),
        .grid-table tbody th:nth-child(2),
        .grid-table thead th:nth-child(2) {
            position: sticky;
            right: 50px;
            /* عرض العمود الأول */
            z-index: 10;
            background-color: #fff;
            box-shadow: -3px 0 5px -2px rgba(0, 0, 0, 0.05);
            /* ظل لفصل الاسم عن الدرجات */
        }

        .grid-table thead th:nth-child(1),
        .grid-table thead th:nth-child(2) {
            z-index: 30;
            /* أعلى من الـ Top والـ Right */
            background-color: #f1f5f9;
        }

        .grid-table td {
            border: 1px solid #e2e8f0;
            vertical-align: middle;
            padding: 0;
            /* تم إزالة البادنج عشان الـ Input ياخد المساحة كلها */
        }

        .mark-input {
            width: 100%;
            height: 45px;
            border: none;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            background-color: transparent;
            outline: none;
            transition: 0.2s;
            color: #0f172a;
        }

        .mark-input:focus {
            background-color: #e0f2fe;
            box-shadow: inset 0 0 0 2px #3b82f6;
        }

        /* تأثير الحفظ الناجح والخطأ */
        .save-success {
            background-color: #dcfce7 !important;
            color: #166534 !important;
            transition: 0.5s;
        }

        .save-error {
            background-color: #fee2e2 !important;
            box-shadow: inset 0 0 0 2px #ef4444 !important;
        }

        /* 🌟 تأثير التركيز على الصف (Row Highlighting) 🌟 */
        .active-row td {
            background-color: #f0fdf4 !important;
        }

        .total-cell {
            font-size: 18px;
            font-weight: 900;
            background-color: #f1f5f9;
            color: #0f172a;
            text-align: center;
            transition: color 0.3s;
        }

        /* تلوين المجموع */
        .text-success-dark {
            color: #15803d !important;
        }

        .text-danger-dark {
            color: #b91c1c !important;
        }

        .max-mark-label {
            font-size: 11px;
            color: #64748b;
            display: block;
            margin-top: 4px;
        }

        /* إخفاء الأسهم الافتراضية للـ input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endpush

@section('content')
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1"><i class="mdi mdi-format-list-checks text-primary me-2"></i>رصد درجات:
                    {{ $subject->name_ar }}</h4>
                <p class="text-muted font-14 mb-0">
                    <i class="mdi mdi-google-classroom"></i> الصف: <strong>{{ $section->grade->name_ar ?? '' }}</strong> |
                    الشعبة: <strong>{{ $section->name_ar }}</strong> |
                    <i class="mdi mdi-calendar"></i> {{ $term->name_ar }}
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-soft-success text-success font-13 px-3 py-2 rounded-pill shadow-sm me-2">
                    <i class="mdi mdi-cloud-check me-1"></i> حفظ تلقائي (Auto-Save)
                </span>
                <button class="btn btn-outline-secondary rounded-pill shadow-sm" onclick="window.print();"
                    title="طباعة الكشف">
                    <i class="mdi mdi-printer"></i>
                </button>
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-light border rounded-pill shadow-sm">
                    <i class="mdi mdi-arrow-right"></i> رجوع
                </a>
            </div>
        </div>

        @if ($categories->count() == 0)
            <div class="alert alert-warning text-center py-5 rounded-4 shadow-sm border-0">
                <i class="mdi mdi-alert-circle-outline font-24 d-block mb-2 text-warning" style="font-size: 50px;"></i>
                <h5 class="fw-bold">لم يتم إعداد أدوات التقويم</h5>
                <p class="mb-0">الإدارة لم تقم بتحديد توزيع الدرجات لهذه المادة في هذا الصف الدراسي حتى الآن.</p>
            </div>
        @else
            <div class="table-responsive-excel shadow-sm border border-light">
                <table class="table grid-table mb-0" id="marksTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 220px; text-align: right; padding-right: 15px;">اسم الطالب</th>

                            {{-- بناء الأعمدة ديناميكياً حسب التقويمات --}}
                            @php $totalMax = 0; @endphp
                            @foreach ($categories as $cat)
                                <th>
                                    {{ $cat->name }}
                                    <span class="max-mark-label">(من {{ floatval($cat->max_mark) }})</span>
                                </th>
                                @php $totalMax += $cat->max_mark; @endphp
                            @endforeach

                            <th style="width: 120px;" class="bg-light text-primary border-start-0">
                                المجموع الكلي
                                <span class="max-mark-label" id="master-total-max" data-total="{{ $totalMax }}">(من
                                    {{ floatval($totalMax) }})</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $index => $student)
                            @php
                                $studentMarks = $marks->get($student->id); // درجات هذا الطالب
                                $rowTotal = 0;
                            @endphp
                            <tr class="student-row">
                                <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                <td class="text-end fw-bold text-dark px-3">{{ $student->first_name }}
                                    {{ $student->second_name }} {{ $student->tribe }}</td>

                                @foreach ($categories as $colIndex => $cat)
                                    @php
                                        // البحث عن درجة هذه الخلية المحددة
                                        $cellMark = $studentMarks
                                            ? $studentMarks->where('exam_category_id', $cat->id)->first()
                                            : null;
                                        $markVal =
                                            $cellMark && $cellMark->mark_obtained !== null
                                                ? floatval($cellMark->mark_obtained)
                                                : '';

                                        if ($markVal !== '') {
                                            $rowTotal += $markVal;
                                        }
                                    @endphp
                                    <td>
                                        <input type="number" step="0.5" min="0"
                                            max="{{ floatval($cat->max_mark) }}" class="mark-input"
                                            data-student="{{ $student->id }}" data-category="{{ $cat->id }}"
                                            data-max="{{ floatval($cat->max_mark) }}" data-col="{{ $colIndex }}"
                                            data-row="{{ $index }}" value="{{ $markVal }}" placeholder="-">
                                    </td>
                                @endforeach
                                <td class="total-cell" id="total-{{ $student->id }}">{{ floatval($rowTotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            // إعداد التوكن للـ AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // المتغيرات الأساسية للمجموع
            const MASTER_TOTAL = parseFloat($('#master-total-max').data('total')) || 0;
            const PASSING_MARK = MASTER_TOTAL / 2;

            // تشغيل دالة التلوين عند بداية التحميل لكل الطلاب
            @if ($categories->count() > 0)
                $('.student-row').each(function() {
                    let studentId = $(this).find('.mark-input').first().data('student');
                    if (studentId) colorizeTotal(studentId);
                });
            @endif

            // 1. تمييز الصف النشط عند التركيز
            $('.mark-input').on('focus', function() {
                $(this).select(); // تحديد الرقم لتسهيل الكتابة فوقه
                $('.student-row').removeClass('active-row');
                $(this).closest('tr').addClass('active-row');
            });

            // إزالة التمييز عند الخروج من الجدول
            $('.table-responsive-excel').on('mouseleave', function() {
                $('.student-row').removeClass('active-row');
            });

            // 2. التنقل بالأسهم (Excel Navigation)
            $('.mark-input').keydown(function(e) {
                let row = $(this).data('row');
                let col = $(this).data('col');

                switch (e.which) {
                    case 37: // سهم يمين (Right Arrow - لأن الموقع RTL)
                        $(`.mark-input[data-row='${row}'][data-col='${col-1}']`).focus();
                        break;
                    case 38: // سهم فوق (Up Arrow)
                        $(`.mark-input[data-row='${row-1}'][data-col='${col}']`).focus();
                        break;
                    case 39: // سهم يسار (Left Arrow)
                        $(`.mark-input[data-row='${row}'][data-col='${col+1}']`).focus();
                        break;
                    case 40: // سهم تحت (Down Arrow)
                    case 13: // Enter
                        $(`.mark-input[data-row='${row+1}'][data-col='${col}']`).focus();
                        break;
                }
            });

            // 3. الحفظ التلقائي عند تغيير الدرجة (Auto-Save)
            $('.mark-input').on('change', function() {
                let input = $(this);
                let markVal = input.val();
                let maxMark = parseFloat(input.data('max'));
                let studentId = input.data('student');
                let categoryId = input.data('category');

                // التحقق من تجاوز الحد الأقصى في الـ Frontend كحماية أولية
                if (markVal !== '' && parseFloat(markVal) > maxMark) {
                    handleError(input, studentId, `الدرجة القصوى هي ${maxMark}`);
                    return;
                }
                if (markVal !== '' && parseFloat(markVal) < 0) {
                    handleError(input, studentId, `لا يمكن إدخال درجة سالبة`);
                    return;
                }

                // إرسال البيانات (AJAX)
                $.ajax({
                    url: "{{ route('teacher.marks.store') }}",
                    type: "POST",
                    data: {
                        subject_id: "{{ $subject->id }}",
                        student_id: studentId,
                        exam_category_id: categoryId,
                        mark_obtained: markVal
                    },
                    success: function(response) {
                        // تأثير بصري للحفظ الناجح
                        input.removeClass('save-error').addClass('save-success');
                        setTimeout(() => input.removeClass('save-success'), 1000);

                        // تحديث المجموع وإعادة التلوين
                        calculateTotal(studentId);
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'حدث خطأ في الاتصال بالخادم!';
                        handleError(input, studentId, errorMsg);
                    }
                });
            });

            // دالة معالجة الخطأ
            function handleError(input, studentId, message) {
                input.addClass('save-error');
                input.val(''); // تفريغ الخلية الخاطئة
                calculateTotal(studentId); // إعادة حساب المجموع

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });

                setTimeout(() => input.removeClass('save-error'), 2500);
            }

            // دالة حساب المجموع للسطر (الطالب)
            function calculateTotal(studentId) {
                let total = 0;
                $(`.mark-input[data-student='${studentId}']`).each(function() {
                    let val = parseFloat($(this).val());
                    if (!isNaN(val)) {
                        total += val;
                    }
                });

                // التأكد من عدم وجود كسور طويلة
                total = parseFloat(total.toFixed(2));
                $(`#total-${studentId}`).text(total);

                // تحديث الألوان بناءً على النجاح والرسوب
                colorizeTotal(studentId, total);
            }

            // دالة تلوين المجموع (أخضر للناجح، أحمر للراسب)
            function colorizeTotal(studentId, totalVal = null) {
                let cell = $(`#total-${studentId}`);
                let total = totalVal !== null ? totalVal : parseFloat(cell.text());

                cell.removeClass('text-success-dark text-danger-dark');

                if (total > 0) {
                    if (total >= PASSING_MARK) {
                        cell.addClass('text-success-dark');
                    } else {
                        cell.addClass('text-danger-dark');
                    }
                }
            }

        });
    </script>
@endpush
