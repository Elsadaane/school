@extends('backend.layouts.master')
@section('title', 'استخراج كشف حساب طالب')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            background-color: #fff;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 48px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        /* 🌙 Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .text-dark {
            color: #f8fafc !important;
        }

        /* ✅ إصلاح القائمة المنسدلة لـ Select2 في الوضع الليلي */
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

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-results__option--highlighted,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #424e5a !important;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
            <div class="col-lg-6 col-md-8">
                <div class="card card-premium mt-4 border-top border-primary border-4 shadow-lg">
                    <div class="card-body p-5">

                        <div class="text-center mb-4 pb-3 border-bottom">
                            <div class="bg-primary bg-opacity-10 text-primary d-inline-flex justify-content-center align-items-center rounded-circle mb-3 shadow-sm"
                                style="width: 80px; height: 80px;">
                                <i class="mdi mdi-file-account fs-1"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">كشف حساب طالب تفصيلي</h3>
                            <p class="text-muted font-14">حدد اسم الطالب من القائمة أدناه لاستعراض وطباعة السجل المالي
                                الشامل (الفواتير، المدفوعات، والمتأخرات).</p>
                        </div>

                        <form action="{{ route('admin.reports.student-statement.generate') }}" method="POST"
                            class="prevent-double-submit">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted font-13 mb-2">
                                    <i class="mdi mdi-account-search-outline me-1 fs-5 align-middle"></i> اسم الطالب أو كود
                                    التعريف <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2-init" name="student_id" required style="width: 100%;">
                                    <option value="" disabled selected>-- ابحث باسم الطالب أو كوده --</option>
                                    @foreach ($students as $student)
                                        {{-- ✅ تم إضافة كود الطالب وتوضيح ولي الأمر لتسهيل البحث --}}
                                        <option value="{{ $student->id }}">
                                            [#{{ $student->id }}] {{ $student->first_name }}
                                            {{ $student->second_name ?? '' }} {{ $student->tribe }} | ولي الأمر:
                                            {{ $student->parent->name_ar ?? 'غير مسجل' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="text-center mt-5">
                                <button type="submit"
                                    class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm w-100 btn-submit py-3">
                                    <i class="mdi mdi-text-box-search-outline me-2 fs-5 align-middle"></i> استخراج كشف
                                    الحساب
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل Select2 مع الثيم المتوافق
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? "rtl" : "ltr",
                width: '100%',
                language: 'ar'
            });

            // 🔥 تأمين الفورم ضد الضغط المتكرر (Double Submit Protection) 🔥
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-2 fs-5 align-middle"></i> جاري استخراج الكشف والبيانات...'
                );
            });
        });
    </script>
@endpush
