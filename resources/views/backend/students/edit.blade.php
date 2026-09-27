@extends('backend.layouts.master')
@section('title', 'تعديل بيانات الطالب | ' . $student->first_name)
@section('title_page', 'تحديث السجل الأكاديمي والشخصي')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* 🌟 Premium Stepper UI 🌟 */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
            padding: 0 10px;
        }

        .stepper-wrapper::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 15%;
            right: 15%;
            height: 4px;
            background: #e2e8f0;
            z-index: 1;
            border-radius: 4px;
        }

        .step-item {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 33.33%;
            color: #94a3b8;
        }

        .step-bubble {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 800;
            font-size: 18px;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            color: #94a3b8;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .step-title {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .nav-link.active .step-bubble {
            background: #3b82f6;
            border-color: #bfdbfe;
            color: #fff;
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }

        .nav-link.active .step-title {
            color: #3b82f6;
        }

        .nav-link.passed .step-bubble {
            background: #10b981;
            border-color: #a7f3d0;
            color: #fff;
        }

        .nav-link.passed .step-title {
            color: #10b981;
        }

        .nav-pills .nav-link {
            background: none;
            border: none;
            padding: 0;
            width: 100%;
            pointer-events: none;
        }

        /* 🌟 Form Tweaks & Errors 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
            border-top: 5px solid #f59e0b;
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 2px dashed #e2e8f0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control,
        .form-select {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #f59e0b;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            font-weight: 600;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }

        /* Error Styling */
        .input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        .error-msg {
            font-size: 12px;
            color: #ef4444;
            margin-top: 6px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Custom Toggle Buttons */
        .health-toggle .btn {
            padding: 8px 20px;
            font-weight: 800;
            font-size: 13px;
            border-radius: 6px;
        }

        /* File Preview Badge */
        .current-file-badge {
            background-color: #f0fdf4;
            color: #15803d;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            margin-top: 8px;
            border: 1px solid #bbf7d0;
            text-decoration: none;
            transition: 0.2s;
        }

        .current-file-badge:hover {
            background-color: #dcfce7;
            color: #166534;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.4s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Stepper Adjustments */
        @media (max-width: 768px) {
            .step-title {
                display: none;
            }

            .stepper-wrapper::before {
                top: 22px;
            }

            .step-bubble {
                width: 45px;
                height: 45px;
                font-size: 16px;
            }
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-section-title {
            color: #f8fafc;
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

        body[data-layout-color="dark"] .bg-white {
            background-color: #323a46 !important;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }

        body[data-layout-color="dark"] .stepper-wrapper::before {
            background: #424e5a;
        }

        body[data-layout-color="dark"] .step-bubble {
            background: #3b4652;
            border-color: #424e5a;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="row mb-3 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold mb-1 text-warning"><i
                            class="mdi mdi-account-edit text-warning me-1 fs-3 align-middle"></i> تعديل بيانات الطالب: <span
                            class="text-dark">{{ $student->first_name }} {{ $student->tribe }}</span></h4>
                    <p class="text-muted font-14 mb-0">تحديث البيانات الأكاديمية، الشخصية، والصحية.</p>
                </div>
                <div>
                    <a href="{{ route('admin.students.index') }}"
                        class="btn btn-light rounded-pill px-4 fw-bold shadow-sm border">
                        <i class="mdi mdi-arrow-left me-1 align-middle"></i> إلغاء التعديل والعودة
                    </a>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-3 mb-4 border-0 border-start border-danger border-4 fw-bold">
                <div class="d-flex align-items-center mb-2">
                    <i class="mdi mdi-alert-circle font-20 me-2 text-danger"></i>
                    <strong class="font-15 text-danger">عذراً، يرجى مراجعة الأخطاء التالية في الإدخالات:</strong>
                </div>
                <ul class="mb-0 ps-4 font-13">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-premium shadow-sm">
            <div class="card-body p-4 p-md-5">

                <form action="{{ route('admin.students.update', $student->id) }}" method="POST"
                    enctype="multipart/form-data" id="studentForm" class="prevent-double-submit" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- 🌟 Premium Stepper UI 🌟 --}}
                    <ul class="nav nav-pills stepper-wrapper" id="studentTab" role="tablist">
                        <li class="nav-item step-item" role="presentation">
                            <button class="nav-link {{ $errors->any() ? '' : 'active' }}" id="parent-tab"
                                data-bs-target="#parent" type="button" role="tab">
                                <div class="step-bubble">1</div>
                                <div class="step-title">ولي الأمر والفرع</div>
                            </button>
                        </li>
                        <li class="nav-item step-item" role="presentation">
                            <button class="nav-link" id="student-tab" data-bs-target="#student" type="button"
                                role="tab">
                                <div class="step-bubble">2</div>
                                <div class="step-title">البيانات الشخصية والأساسية</div>
                            </button>
                        </li>
                        <li class="nav-item step-item" role="presentation">
                            <button class="nav-link" id="health-tab" data-bs-target="#health" type="button" role="tab">
                                <div class="step-bubble">3</div>
                                <div class="step-title">الصحة والمرفقات الثبوتية</div>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-4" id="studentTabContent">

                        {{-- ================= الخطوة 1: الأكاديمية ================= --}}
                        <div class="tab-pane fade show active" id="parent" role="tabpanel">
                            <div class="mb-2">
                                <h5 class="form-section-title"><i
                                        class="mdi mdi-account-tie text-warning fs-4 align-middle"></i> 1. ارتباط الحساب
                                    بولي الأمر</h5>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label text-muted font-13 fw-bold">اختر ولي الأمر الحالي (المسؤول
                                            المالي والأكاديمي) <span class="text-danger">*</span></label>
                                        <select
                                            class="form-select select2 shadow-sm @error('parent_id') input-error @enderror"
                                            name="parent_id" id="parent_select" data-original-id="{{ $student->parent_id }}"
                                            required>
                                            @foreach ($parents as $parent)
                                                <option value="{{ $parent->id }}"
                                                    {{ old('parent_id', $student->parent_id) == $parent->id ? 'selected' : '' }}>
                                                    {{ $parent->name_ar }} | هاتف: {{ $parent->phone }} | هوية:
                                                    {{ $parent->national_id }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('parent_id')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- سبب تغيير ولي الأمر --}}
                                    <div class="col-md-12" id="reason_container"
                                        style="{{ old('guardian_change_reason') || (old('parent_id') && old('parent_id') != $student->parent_id) ? '' : 'display: none;' }}">
                                        <div
                                            class="alert alert-warning border-warning border-start border-4 shadow-sm p-4 rounded-4 mb-0 bg-warning bg-opacity-10">
                                            <label class="form-label text-dark font-bold"><i
                                                    class="mdi mdi-alert-outline fs-5 align-middle text-danger me-1"></i> تم
                                                رصد محاولة لتغيير ولي الأمر! يرجى ذكر السبب الإداري أو القانوني لاعتماده:
                                                <span class="text-danger">*</span></label>
                                            <textarea class="form-control border-warning shadow-sm mt-2 @error('guardian_change_reason') input-error @enderror"
                                                name="guardian_change_reason" id="guardian_change_reason" rows="2"
                                                placeholder="مثال: انتقال الولاية للأم، تصحيح خطأ في الإدخال القديم...">{{ old('guardian_change_reason') }}</textarea>
                                            @error('guardian_change_reason')
                                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <h5 class="form-section-title mt-5"><i
                                        class="mdi mdi-google-classroom text-warning fs-4 align-middle"></i> 2. التسكين
                                    الأكاديمي للطالب</h5>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">الفرع / المدرسة <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select shadow-sm @error('branch_id') input-error @enderror"
                                            name="branch_id" id="branch_id_add" required>
                                            @foreach (\App\Models\Branche::where('is_active', 1)->get() as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ old('branch_id', $student->branch_id) == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name_ar }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">السنة الدراسية (الدفعة) <span
                                                class="text-danger">*</span></label>
                                        <select
                                            class="form-select shadow-sm @error('academic_year_id') input-error @enderror"
                                            name="academic_year_id" id="academic_year_id_add" required>
                                            @if ($student->academicYear)
                                                <option value="{{ $student->academic_year_id }}" selected>
                                                    {{ $student->academicYear->name }}</option>
                                            @endif
                                        </select>
                                        @error('academic_year_id')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">المرحلة / الصف الدراسي <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select shadow-sm @error('grade_id') input-error @enderror"
                                            name="grade_id" required>
                                            @foreach (\App\Models\Grade::orderBy('level_order')->get() as $grade)
                                                <option value="{{ $grade->id }}"
                                                    {{ old('grade_id', $student->grade_id) == $grade->id ? 'selected' : '' }}>
                                                    {{ $grade->name_ar }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('grade_id')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="text-end border-top pt-4 mt-5">
                                <button type="button"
                                    class="btn btn-warning rounded-pill px-5 py-2 fw-bold btn-next shadow-sm text-dark">
                                    المتابعة للبيانات الشخصية <i class="mdi mdi-arrow-left ms-2 align-middle fs-5"></i>
                                </button>
                            </div>
                        </div>

                        {{-- ================= الخطوة 2: بيانات الطالب ================= --}}
                        <div class="tab-pane fade" id="student" role="tabpanel">
                            <div class="mb-2">
                                <h5 class="form-section-title"><i
                                        class="mdi mdi-card-account-details-outline text-warning fs-4 align-middle"></i>
                                    البيانات الأساسية للطالب</h5>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-3">
                                        <label class="form-label text-muted font-13 fw-bold">الاسم الأول <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold @error('first_name') input-error @enderror"
                                            name="first_name" value="{{ old('first_name', $student->first_name) }}"
                                            required>
                                        @error('first_name')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted font-13 fw-bold">اسم الأب (الثاني)</label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold @error('second_name') input-error @enderror"
                                            name="second_name" value="{{ old('second_name', $student->second_name) }}">
                                        @error('second_name')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted font-13 fw-bold">اسم الجد (الثالث)</label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold @error('third_name') input-error @enderror"
                                            name="third_name" value="{{ old('third_name', $student->third_name) }}">
                                        @error('third_name')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted font-13 fw-bold">القبيلة / اللقب (الأخير) <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold @error('tribe') input-error @enderror"
                                            name="tribe" value="{{ old('tribe', $student->tribe) }}" required>
                                        @error('tribe')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">تاريخ الميلاد <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control shadow-sm font-monospace @error('dob') input-error @enderror"
                                            name="dob" value="{{ old('dob', $student->dob) }}" required>
                                        @error('dob')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">الجنس / النوع <span
                                                class="text-danger">*</span></label>
                                        <select
                                            class="form-select shadow-sm fw-bold @error('gender') input-error @enderror"
                                            name="gender" required>
                                            <option value="male"
                                                {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>ذكر
                                            </option>
                                            <option value="female"
                                                {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>أنثى
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">الجنسية <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold @error('nationality') input-error @enderror"
                                            name="nationality" value="{{ old('nationality', $student->nationality) }}"
                                            required>
                                        @error('nationality')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted font-13 fw-bold">البريد الإلكتروني (بوابة
                                            الطالب)</label>
                                        <input type="email" name="email"
                                            class="form-control font-monospace shadow-sm"
                                            value="{{ old('email', $student->email ?? '') }}" dir="ltr">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted font-13 fw-bold">كلمة المرور <span
                                                class="text-info">(اتركها فارغة لعدم التعديل)</span></label>
                                        <input type="password" name="password"
                                            class="form-control shadow-sm font-monospace" placeholder="********">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                    <h5 class="form-section-title border-0 mb-0 pb-0"><i
                                            class="mdi mdi-human-female text-warning fs-4 align-middle"></i> معلومات
                                        وبيانات الأم</h5>
                                    <div class="form-check form-switch d-flex align-items-center gap-2">
                                        @php
                                            $isMotherMissing = empty($student->mother_name);
                                        @endphp
                                        <input class="form-check-input shadow-sm m-0" type="checkbox"
                                            id="mother_unavailable" name="mother_unavailable" value="1"
                                            {{ old('mother_unavailable', $isMotherMissing) ? 'checked' : '' }}
                                            style="width: 2.5em; height: 1.25em;">
                                        <label class="form-check-label text-danger fw-bold font-13 cursor-pointer mt-1"
                                            for="mother_unavailable">لا توجد بيانات للأم (غير متوفرة)</label>
                                    </div>
                                </div>

                                <div class="row g-4 mb-5" id="mother_fields_container"
                                    style="{{ old('mother_unavailable', $isMotherMissing) ? 'opacity: 0.4; pointer-events: none;' : '' }}">
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">اسم الأم (الثلاثي) <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold mother-input @error('mother_name') input-error @enderror"
                                            name="mother_name" value="{{ old('mother_name', $student->mother_name) }}">
                                        @error('mother_name')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">رقم هاتف الأم <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm font-monospace mother-input @error('mother_phone') input-error @enderror"
                                            name="mother_phone" value="{{ old('mother_phone', $student->mother_phone) }}"
                                            dir="ltr">
                                        @error('mother_phone')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">رقم هوية الأم <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm font-monospace mother-input @error('mother_national_id') input-error @enderror"
                                            name="mother_national_id"
                                            value="{{ old('mother_national_id', $student->mother_national_id) }}">
                                        @error('mother_national_id')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">تاريخ انتهاء الهوية <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control shadow-sm font-monospace mother-input @error('mother_id_expiry') input-error @enderror"
                                            name="mother_id_expiry"
                                            value="{{ old('mother_id_expiry', $student->mother_id_expiry) }}">
                                        @error('mother_id_expiry')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">جنسية الأم <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold mother-input @error('mother_nationality') input-error @enderror"
                                            name="mother_nationality"
                                            value="{{ old('mother_nationality', $student->mother_nationality ?? 'عماني') }}">
                                        @error('mother_nationality')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">جهة عمل الأم <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold mother-input @error('mother_workplace') input-error @enderror"
                                            name="mother_workplace"
                                            value="{{ old('mother_workplace', $student->mother_workplace) }}">
                                        @error('mother_workplace')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <h5 class="form-section-title"><i
                                        class="mdi mdi-map-marker-radius text-warning fs-4 align-middle"></i> بيانات السكن
                                    والتنقل (Logistics)</h5>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label text-muted font-13 fw-bold">المنطقة / الولاية <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold @error('region') input-error @enderror"
                                            name="region" value="{{ old('region', $student->region) }}" required>
                                        @error('region')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label text-muted font-13 fw-bold">استدلال قريب (أقرب مَعلَم واضح
                                            للسائق) <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold @error('pickup_point') input-error @enderror"
                                            name="pickup_point" value="{{ old('pickup_point', $student->pickup_point) }}"
                                            required>
                                        @error('pickup_point')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted font-13 fw-bold">الشارع / السكة / المربع <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold font-monospace @error('street_number') input-error @enderror"
                                            name="street_number"
                                            value="{{ old('street_number', $student->street_number) }}" required>
                                        @error('street_number')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted font-13 fw-bold">رقم المنزل / البناية <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control shadow-sm fw-bold font-monospace @error('house_number') input-error @enderror"
                                            name="house_number" value="{{ old('house_number', $student->house_number) }}"
                                            required>
                                        @error('house_number')
                                            <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="p-4 bg-light rounded-4 border shadow-sm mt-4">
                                    <label class="form-label fw-bold text-dark d-block mb-3"><i
                                            class="mdi mdi-bus-school text-warning fs-5 align-middle me-1"></i> نوع النقل
                                        والمواصلات للطالب <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap gap-4">
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input shadow-sm" type="radio"
                                                name="transport_type" id="t1" value="private"
                                                {{ old('transport_type', $student->transport_type) == 'private' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label fw-bold font-14 cursor-pointer mt-1"
                                                for="t1">نقل خاص (أهلي)</label>
                                        </div>
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input shadow-sm" type="radio"
                                                name="transport_type" id="t2" value="bus_both"
                                                {{ old('transport_type', $student->transport_type) == 'bus_both' ? 'checked' : '' }}
                                                required>
                                            <label
                                                class="form-check-label fw-bold font-14 cursor-pointer mt-1 text-success"
                                                for="t2">حافلة مدرسية (ذهاب وعودة)</label>
                                        </div>
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input shadow-sm" type="radio"
                                                name="transport_type" id="t3" value="bus_go"
                                                {{ old('transport_type', $student->transport_type) == 'bus_go' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label fw-bold font-14 cursor-pointer mt-1 text-info"
                                                for="t3">حافلة مدرسية (ذهاب فقط)</label>
                                        </div>
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input shadow-sm" type="radio"
                                                name="transport_type" id="t4" value="bus_return"
                                                {{ old('transport_type', $student->transport_type) == 'bus_return' ? 'checked' : '' }}
                                                required>
                                            <label
                                                class="form-check-label fw-bold font-14 cursor-pointer mt-1 text-primary"
                                                for="t4">حافلة مدرسية (عودة فقط)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between border-top pt-4 mt-5">
                                <button type="button"
                                    class="btn btn-light rounded-pill px-5 py-2 fw-bold btn-prev text-muted border shadow-sm"><i
                                        class="mdi mdi-arrow-right me-2 align-middle"></i> الرجوع لبيانات الفرع</button>
                                <button type="button"
                                    class="btn btn-warning rounded-pill px-5 py-2 fw-bold btn-next shadow-sm text-dark">المتابعة
                                    للصحة والمرفقات <i class="mdi mdi-arrow-left ms-1 align-middle"></i></button>
                            </div>
                        </div>

                        {{-- ================= الخطوة 3: الصحة والمرفقات ================= --}}
                        <div class="tab-pane fade" id="health" role="tabpanel">

                            <div class="row g-4 mb-4">
                                {{-- الطوارئ --}}
                                <div class="col-lg-6">
                                    <div
                                        class="p-4 bg-white border border-danger border-opacity-25 rounded-4 shadow-sm h-100 border-top border-danger border-4">
                                        <h5
                                            class="text-danger mb-4 fw-bold border-bottom border-danger border-opacity-25 pb-2">
                                            <i class="mdi mdi-phone-alert me-1 fs-4 align-middle"></i> جهة الاتصال في حالات
                                            الطوارئ</h5>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label text-muted font-13 fw-bold">اسم شخص الطوارئ <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control shadow-sm fw-bold @error('emergency_name') input-error @enderror"
                                                    name="emergency_name"
                                                    value="{{ old('emergency_name', $student->emergency_name) }}"
                                                    required>
                                                @error('emergency_name')
                                                    <div class="error-msg"><i class="mdi mdi-close-circle"></i>
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted font-13 fw-bold">صلة القرابة <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control shadow-sm fw-bold @error('emergency_relation') input-error @enderror"
                                                    name="emergency_relation"
                                                    value="{{ old('emergency_relation', $student->emergency_relation) }}"
                                                    required>
                                                @error('emergency_relation')
                                                    <div class="error-msg"><i class="mdi mdi-close-circle"></i>
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label text-muted font-13 fw-bold">هاتف الطوارئ (فعال)
                                                    <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control shadow-sm font-monospace fw-bold @error('emergency_phone') input-error @enderror"
                                                    name="emergency_phone"
                                                    value="{{ old('emergency_phone', $student->emergency_phone) }}"
                                                    dir="ltr" required>
                                                @error('emergency_phone')
                                                    <div class="error-msg"><i class="mdi mdi-close-circle"></i>
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- الحالة الصحية --}}
                                <div class="col-lg-6">
                                    <div
                                        class="p-4 bg-white border border-success border-opacity-25 rounded-4 shadow-sm h-100 border-top border-success border-4">
                                        <h5
                                            class="text-success mb-4 fw-bold border-bottom border-success border-opacity-25 pb-2">
                                            <i class="mdi mdi-hospital-box me-1 fs-4 align-middle"></i> التقييم والحالة
                                            الصحية</h5>

                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3 border">
                                            <span class="text-dark fw-bold font-14"><i
                                                    class="mdi mdi-peanut-outline text-warning fs-5 align-middle me-1"></i>
                                                هل يعاني الطالب من أي حساسية؟ <span class="text-danger">*</span></span>
                                            <div class="btn-group shadow-sm health-toggle" role="group">
                                                <input type="radio" class="btn-check" name="has_allergies"
                                                    id="alg1" value="0"
                                                    {{ old('has_allergies', $student->has_allergies) == 0 ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success" for="alg1">لا يعاني</label>

                                                <input type="radio" class="btn-check" name="has_allergies"
                                                    id="alg2" value="1"
                                                    {{ old('has_allergies', $student->has_allergies) == 1 ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger" for="alg2">نعم يعاني</label>
                                            </div>
                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3 border">
                                            <span class="text-dark fw-bold font-14"><i
                                                    class="mdi mdi-ear-hearing text-info fs-5 align-middle me-1"></i> هل
                                                توجد أي مشاكل في السمع؟ <span class="text-danger">*</span></span>
                                            <div class="btn-group shadow-sm health-toggle" role="group">
                                                <input type="radio" class="btn-check" name="has_hearing_issues"
                                                    id="hear1" value="0"
                                                    {{ old('has_hearing_issues', $student->has_hearing_issues) == 0 ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success" for="hear1">لا</label>

                                                <input type="radio" class="btn-check" name="has_hearing_issues"
                                                    id="hear2" value="1"
                                                    {{ old('has_hearing_issues', $student->has_hearing_issues) == 1 ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger" for="hear2">نعم</label>
                                            </div>
                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3 border">
                                            <span class="text-dark fw-bold font-14"><i
                                                    class="mdi mdi-eye-outline text-primary fs-5 align-middle me-1"></i> هل
                                                توجد أي مشاكل في النظر؟ <span class="text-danger">*</span></span>
                                            <div class="btn-group shadow-sm health-toggle" role="group">
                                                <input type="radio" class="btn-check" name="has_vision_issues"
                                                    id="vis1" value="0"
                                                    {{ old('has_vision_issues', $student->has_vision_issues) == 0 ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success" for="vis1">لا</label>

                                                <input type="radio" class="btn-check" name="has_vision_issues"
                                                    id="vis2" value="1"
                                                    {{ old('has_vision_issues', $student->has_vision_issues) == 1 ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger" for="vis2">نعم</label>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label text-muted font-13 fw-bold"><i
                                                    class="mdi mdi-note-edit-outline me-1"></i> ملاحظات طبية تفصيلية (إن
                                                وجدت)</label>
                                            <textarea class="form-control shadow-sm border bg-white" name="health_notes" rows="2">{{ old('health_notes', $student->health_notes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- المرفقات الرسمية --}}
                            <div
                                class="p-4 bg-white border border-warning border-opacity-50 rounded-4 shadow-sm border-top border-warning border-4 mb-4">
                                <h5
                                    class="text-warning text-darken mb-4 fw-bold border-bottom border-warning border-opacity-25 pb-2">
                                    <i class="mdi mdi-paperclip fs-4 align-middle me-1"></i> تحديث المستندات والمرفقات
                                    الثبوتية</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted font-13 fw-bold"><i
                                                class="mdi mdi-camera-front-variant text-info me-1"></i> صورة شخصية
                                            للطالب</label>
                                        <input type="file" class="form-control shadow-sm" name="student_photo"
                                            accept="image/*">
                                        @if ($student->student_photo)
                                            <a href="{{ Storage::url($student->student_photo) }}" target="_blank"
                                                class="current-file-badge shadow-sm"><i class="mdi mdi-eye me-1"></i>
                                                معاينة الصورة الحالية</a>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted font-13 fw-bold"><i
                                                class="mdi mdi-file-certificate-outline text-warning me-1"></i> نسخة من
                                            شهادة الميلاد</label>
                                        <input type="file" class="form-control shadow-sm"
                                            name="birth_certificate_copy" accept=".pdf,image/*">
                                        @if ($student->birth_certificate_copy)
                                            <a href="{{ Storage::url($student->birth_certificate_copy) }}"
                                                target="_blank" class="current-file-badge shadow-sm"><i
                                                    class="mdi mdi-eye me-1"></i> معاينة الملف الحالي</a>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted font-13 fw-bold"><i
                                                class="mdi mdi-file-document-multiple-outline text-danger me-1"></i>
                                            التقارير الصحية إن وجدت</label>
                                        <input type="file" class="form-control shadow-sm" name="health_report"
                                            accept=".pdf,image/*">
                                        @if ($student->health_reports)
                                            <a href="{{ Storage::url($student->health_reports) }}" target="_blank"
                                                class="current-file-badge shadow-sm"><i class="mdi mdi-eye me-1"></i>
                                                معاينة الملف الحالي</a>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-success font-13 fw-bold"><i
                                                class="mdi mdi-needle text-success me-1"></i> كرت التحصين (التطعيمات
                                            الرسمية)</label>
                                        <input type="file" class="form-control shadow-sm border-success"
                                            name="immunization_card" accept=".pdf,image/*">
                                        @if ($student->vaccination_card)
                                            <a href="{{ Storage::url($student->vaccination_card) }}" target="_blank"
                                                class="current-file-badge shadow-sm"><i class="mdi mdi-eye me-1"></i>
                                                معاينة الملف الحالي</a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between border-top pt-4 mt-4">
                                <button type="button"
                                    class="btn btn-light rounded-pill px-5 py-3 fw-bold btn-prev text-muted border shadow-sm"><i
                                        class="mdi mdi-arrow-right me-2 align-middle"></i> الرجوع للبيانات الشخصية</button>
                                <button type="submit"
                                    class="btn btn-warning px-5 py-3 rounded-pill font-bold shadow-lg btn-submit text-dark"
                                    style="font-size:16px;">
                                    <i class="mdi mdi-content-save-all me-2 fs-4 align-middle"></i> اعتماد وحفظ التعديلات
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // Select2 مع دعم الـ Theme
            $('.select2').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                width: '100%',
                dir: isRtl ? "rtl" : "ltr"
            });

            // كود إظهار سبب التغيير لولي الأمر
            let parentSelect = $('#parent_select');
            let originalParentId = parentSelect.data('original-id');
            let reasonContainer = $('#reason_container');
            let reasonInput = $('#guardian_change_reason');

            parentSelect.on('change', function() {
                if ($(this).val() != originalParentId) {
                    reasonContainer.slideDown();
                    reasonInput.prop('required', true);
                } else {
                    reasonContainer.slideUp();
                    reasonInput.prop('required', false).val('');
                }
            });

            // 🔥 AJAX: Fetch Academic Years based on Branch 🔥
            $('#branch_id_add').on('change', function() {
                let branchId = $(this).val();
                let yearSelect = $('#academic_year_id_add');
                let currentYearId = "{{ $student->academic_year_id }}";

                yearSelect.html('<option value="" selected disabled>⏳ جاري التحميل...</option>').prop(
                    'disabled', true);

                if (branchId) {
                    let localeUrl = "{{ url(app()->getLocale()) }}";
                    $.get(`${localeUrl}/admin/get-branch-academic-years/${branchId}`, function(data) {
                        let options =
                            '<option value="" selected disabled>-- اختر السنة الدراسية --</option>';
                        if (data.length > 0) {
                            $.each(data, function(key, value) {
                                // اختيار السنة الحالية لو الفرع متغيرش
                                let isSelected = (value.id == currentYearId) ? 'selected' :
                                    '';
                                options +=
                                    `<option value="${value.id}" ${isSelected}>${value.name}</option>`;
                            });
                            yearSelect.html(options).prop('disabled', false);
                        } else {
                            yearSelect.html(
                                '<option value="" disabled>لا توجد سنوات دراسية مفعلة لهذا الفرع</option>'
                                );
                        }
                    }).fail(function() {
                        yearSelect.html('<option value="" disabled>حدث خطأ في الاتصال</option>');
                    });
                }
            });

            // لو الفرع متحدد أوتوماتيك نشغل ה-AJAX علطول
            if ($('#branch_id_add').val()) {
                $('#branch_id_add').trigger('change');
            }

            // Mother Toggle Logic (إخفاء/إظهار بيانات الأم)
            function toggleMotherFields() {
                let isUnavailable = $('#mother_unavailable').is(':checked');
                let container = $('#mother_fields_container');
                let inputs = container.find('.mother-input');

                if (isUnavailable) {
                    inputs.prop('disabled', true).prop('required', false).val('');
                    container.css({
                        'opacity': '0.4',
                        'pointer-events': 'none'
                    });
                } else {
                    inputs.prop('disabled', false).prop('required', true);
                    container.css({
                        'opacity': '1',
                        'pointer-events': 'auto'
                    });
                }
            }

            toggleMotherFields(); // تشغيل عند تحميل الصفحة
            $('#mother_unavailable').on('change', toggleMotherFields);

            // Stepper Navigation Logic المُحسّن والآمن
            $('.btn-next').off('click').on('click', function(e) {
                e.preventDefault();
                let currentTabBtn = $('.stepper-wrapper button.active');
                let nextTabBtn = currentTabBtn.closest('li').next('li').find('button');

                if (nextTabBtn.length > 0) {
                    currentTabBtn.addClass('passed').removeClass('active');
                    let targetPane = nextTabBtn.attr('data-bs-target');
                    $('.tab-pane').removeClass('show active');
                    $(targetPane).addClass('show active');
                    nextTabBtn.addClass('active');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });

            $('.btn-prev').off('click').on('click', function(e) {
                e.preventDefault();
                let currentTabBtn = $('.stepper-wrapper button.active');
                let prevTabBtn = currentTabBtn.closest('li').prev('li').find('button');

                if (prevTabBtn.length > 0) {
                    currentTabBtn.removeClass('passed active');
                    let targetPane = prevTabBtn.attr('data-bs-target');
                    $('.tab-pane').removeClass('show active');
                    $(targetPane).addClass('show active');
                    prevTabBtn.addClass('active').removeClass('passed');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });

            // تأمين الفورم ضد الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin fs-4 align-middle me-2"></i> جاري الحفظ والتحديث...'
                    );
            });

            // ذكاء اصطناعي بسيط: لو الفورم رجعت بإيرور من الباك إند، افتح التابة اللي فيها الأيرور
            @if ($errors->any())
                @if (
                    $errors->has('parent_id') ||
                        $errors->has('branch_id') ||
                        $errors->has('academic_year_id') ||
                        $errors->has('grade_id') ||
                        $errors->has('guardian_change_reason'))
                    // البقاء في الخطوة 1
                @elseif ($errors->has('first_name') || $errors->has('tribe') || $errors->has('dob') || $errors->has('mother_name'))
                    $('.stepper-wrapper button.active').removeClass('active');
                    $('.tab-pane').removeClass('show active');
                    $('#student-tab').addClass('active');
                    $('#student').addClass('show active');
                    $('#parent-tab').addClass('passed');
                @else
                    $('.stepper-wrapper button.active').removeClass('active');
                    $('.tab-pane').removeClass('show active');
                    $('#health-tab').addClass('active');
                    $('#health').addClass('show active');
                    $('#parent-tab, #student-tab').addClass('passed');
                @endif
            @endif
        });
    </script>
@endpush
