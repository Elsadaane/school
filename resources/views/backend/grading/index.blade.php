@extends('backend.layouts.master')
@section('title', 'ضبط توزيع الدرجات')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #fff;
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-color: #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* 🌟 تنسيق أسطر الإدخال الديناميكية 🌟 */
        .eval-row {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .eval-row:hover {
            border-color: #93c5fd;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .total-box {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
            transition: 0.3s;
        }

        .total-box.error-state {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
        }

        /* 🌟 تنسيق جدول التوزيعات الحالية 🌟 */
        .grouped-table th {
            background-color: #1e293b;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            text-align: center;
        }

        .grade-header {
            background-color: #e2e8f0;
            color: #1e293b;
            font-weight: 900;
            font-size: 15px;
        }

        .eval-badge {
            font-size: 12px;
            background-color: #fff;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 5px 10px;
            border-radius: 6px;
            display: inline-block;
            margin: 3px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 الهيدر 🌟 --}}
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary shadow-sm">
                        <i class="bx bx-slider-alt fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">ضبط أدوات التقويم وتوزيع الدرجات</h4>
                        <p class="text-muted mb-0 font-13">تأسيس هيكل الدرجات (امتحانات، أنشطة، مواظبة) لكل مادة دراسية.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- رسائل النظام --}}
        @if (session('success'))
            <div class="alert alert-success shadow-sm fw-bold rounded-3 border-0">
                <i class="bx bx-check-circle fs-5 align-middle me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-3 fw-bold border-0">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="bx bx-error-circle align-middle me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            {{-- 🔥 1. فورم إعداد التوزيعة (Dynamic Form) 🔥 --}}
            <div class="col-xl-6">
                <div class="card card-premium h-100">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold text-primary mb-0"><i class="bx bx-plus-circle me-1"></i> إضافة/تحديث توزيع مادة
                        </h5>
                    </div>

                    <form action="{{ route('admin.grading.store') }}" method="POST" id="gradingForm">
                        @csrf
                        <div class="card-body p-4">

                            {{-- القسم الأول: الاستهداف --}}
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <label class="form-label text-danger">الفصل الدراسي *</label>
                                    <select name="academic_term_id" class="form-select select2-init" required>
                                        <option value="" disabled selected>-- اختر الفصل --</option>
                                        @foreach ($terms as $t)
                                            <option value="{{ $t->id }}">{{ $t->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-danger">الفرع *</label>
                                    <select name="branch_id" class="form-select select2-init" required>
                                        <option value="" disabled selected>-- اختر الفرع --</option>
                                        @foreach ($branches as $b)
                                            <option value="{{ $b->id }}">{{ $b->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-danger">الصف الدراسي *</label>
                                    <select name="grade_id" class="form-select select2-init" required>
                                        <option value="" disabled selected>-- اختر الصف --</option>
                                        @foreach ($grades as $g)
                                            <option value="{{ $g->id }}">{{ $g->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-danger">المادة الدراسية *</label>
                                    <select name="subject_id" class="form-select select2-init" required>
                                        <option value="" disabled selected>-- اختر المادة --</option>
                                        @foreach ($subjects as $s)
                                            <option value="{{ $s->id }}">{{ $s->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- القسم الثاني: البنود --}}
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
                                <h6 class="fw-bold text-dark mb-0"><i class="bx bx-list-check text-warning me-1"></i> بنود
                                    أداة التقويم</h6>
                                <div class="total-box font-bold" id="totalBoxWrapper">
                                    المجموع: <span id="total_marks_display" class="font-20 ms-1">0</span> درجة
                                </div>
                            </div>

                            <div id="dynamic-rows">
                                {{-- الصف الافتراضي الأول --}}
                                <div class="eval-row row align-items-end g-2">
                                    <div class="col-md-7">
                                        <label class="form-label">اسم التقييم (مثال: اختبار قصير) *</label>
                                        <input type="text" name="categories[0][name]" class="form-control fw-bold"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">الدرجة *</label>
                                        <input type="number" step="0.5" min="0.5" name="categories[0][max_mark]"
                                            class="form-control text-center fw-bold font-monospace mark-input" required>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <button type="button" class="btn btn-light text-muted w-100" disabled><i
                                                class="bx bx-lock-alt"></i></button>
                                    </div>
                                </div>
                            </div>

                            @can('manage_evaluations')
                                <div class="mt-3 text-start">
                                    <button type="button"
                                        class="btn btn-soft-primary fw-bold rounded-pill px-3 shadow-sm btn-sm"
                                        onclick="addRow()">
                                        <i class="bx bx-plus me-1"></i> إضافة بند إضافي
                                    </button>
                                </div>
                            @endcan
                        </div>

                        @can('manage_evaluations')
                            <div class="card-footer bg-light border-top-0 p-3 text-center rounded-bottom-4">
                                <button type="submit" class="btn btn-success fw-bold px-5 py-2 rounded-pill shadow-sm"
                                    id="btn-submit">
                                    <i class="bx bx-save me-1"></i> اعتماد التوزيعة
                                </button>
                                <p class="text-muted font-11 mt-2 mb-0"><i class="bx bx-info-circle"></i> حفظ توزيعة جديدة لنفس
                                    المادة والصف سيقوم بالكتابة فوق التوزيعة القديمة تلقائياً.</p>
                            </div>
                        @else
                            <div class="card-footer bg-light border-top-0 p-3 text-center rounded-bottom-4">
                                <div class="alert alert-warning mb-0 fw-bold py-2"><i class="bx bx-lock-alt me-1"></i> لا تملك
                                    صلاحية التعديل.</div>
                            </div>
                        @endcan
                    </form>
                </div>
            </div>

            {{-- 🔥 2. عرض التوزيعات الحالية المعتمدة 🔥 --}}
            <div class="col-xl-6">
                <div class="card card-premium h-100">
                    <div class="card-header bg-white border-bottom p-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="bx bx-table me-1 text-success"></i> التوزيعات المعتمدة
                            حالياً</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 65vh; overflow-y: auto;">
                            <table class="table table-bordered align-middle grouped-table mb-0 text-center">
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">المادة الدراسية</th>
                                        <th style="width: 45%;">تفاصيل بنود التقويم</th>
                                        <th style="width: 20%;">الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $gradeName => $subjectsData)
                                        <tr>
                                            <td colspan="3" class="grade-header text-start ps-3"><i
                                                    class="bx bx-layer text-primary me-1"></i> {{ $gradeName }}</td>
                                        </tr>
                                        @foreach ($subjectsData as $subjectName => $items)
                                            @php $subTotal = $items->sum('max_mark'); @endphp
                                            <tr>
                                                <td class="fw-bold text-dark text-start ps-3">{{ $subjectName }}</td>
                                                <td>
                                                    @foreach ($items as $item)
                                                        <span class="eval-badge">{{ $item->name }}: <strong
                                                                class="text-primary">{{ floatval($item->max_mark) }}</strong></span>
                                                    @endforeach
                                                </td>
                                                <td
                                                    class="fw-bolder font-15 {{ $subTotal == 100 ? 'text-success' : 'text-danger' }}">
                                                    {{ floatval($subTotal) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted fw-bold">
                                                <i class="bx bx-folder-open fs-1 d-block mb-2 opacity-25"></i>
                                                لا توجد أي توزيعات درجات معتمدة في النظام حالياً.
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
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        let rowCount = 1;

        $(document).ready(function() {
            let currentLocale = "{{ app()->getLocale() }}";
            let isRtl = currentLocale === 'ar';

            // تفعيل Select2
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // حساب الإجمالي عند الكتابة
            $(document).on('input', '.mark-input', function() {
                calculateTotal();
            });

            // 🔥 منع الـ Form من الإرسال إلا لو المجموع = 100 بالظبط 🔥
            $('#gradingForm').on('submit', function(e) {
                let total = parseFloat($('#total_marks_display').text());

                if (total !== 100) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'عفواً، المجموع غير صحيح!',
                        text: 'يجب أن يكون إجمالي الدرجات الموزعة يساوي 100 درجة بالضبط لتتمكن من الاعتماد. المجموع الحالي هو ' +
                            total,
                        confirmButtonText: 'تعديل الدرجات',
                        confirmButtonColor: '#ef4444'
                    });
                    return false;
                }

                // منع التكرار في الحفظ
                $('#btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري الحفظ...');
            });
        });

        // دالة إضافة صف جديد
        function addRow() {
            let newRow = `
            <div class="eval-row row align-items-end g-2" id="row-${rowCount}">
                <div class="col-md-7">
                    <label class="form-label">اسم التقييم (مثال: الأنشطة) *</label>
                    <input type="text" name="categories[${rowCount}][name]" class="form-control fw-bold" placeholder="أدخل اسم التقييم..." required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الدرجة *</label>
                    <input type="number" step="0.5" min="0.5" name="categories[${rowCount}][max_mark]" class="form-control text-center fw-bold font-monospace mark-input" required>
                </div>
                <div class="col-md-2 text-center">
                    <button type="button" class="btn btn-outline-danger w-100 fw-bold" onclick="removeRow(${rowCount})">
                        <i class="bx bx-trash"></i> حذف
                    </button>
                </div>
            </div>`;

            $('#dynamic-rows').append(newRow);
            rowCount++;
        }

        // دالة حذف الصف
        function removeRow(id) {
            $(`#row-${id}`).slideUp(200, function() {
                $(this).remove();
                calculateTotal();
            });
        }

        // دالة الحساب التلقائي والتلوين (Live Counter)
        function calculateTotal() {
            let total = 0;
            $('.mark-input').each(function() {
                let val = parseFloat($(this).val());
                if (!isNaN(val)) {
                    total += val;
                }
            });

            $('#total_marks_display').text(total);

            // تغيير لون الصندوق للتنبيه لو المجموع لا يساوي 100
            if (total !== 100 && total > 0) {
                $('#totalBoxWrapper').addClass('error-state');
            } else {
                $('#totalBoxWrapper').removeClass('error-state');
            }
        }
    </script>
@endpush
