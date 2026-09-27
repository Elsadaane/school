@extends('backend.layouts.master')
@section('title', 'تعديل بيانات المعلم')
@section('title_page', 'إدارة المعلمين والهيئة التدريسية')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .page-wrapper {
            padding: 25px;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
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

        .bg-soft-primary {
            background-color: rgba(59, 130, 246, 0.05);
        }

        .bg-soft-warning {
            background-color: rgba(245, 158, 11, 0.05);
        }

        .bg-soft-success {
            background-color: rgba(16, 185, 129, 0.05);
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

        /* Select2 Tweaks */
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

        .select2-container--bootstrap-5 .select2-selection--multiple {
            min-height: 42px;
            padding: 4px 8px;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            color: #d97706;
            font-weight: bold;
            border-radius: 6px;
            margin-top: 5px;
        }

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

        .input-group-text {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .image-upload-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
            border-radius: 50%;
            border: 4px dashed #cbd5e1;
            padding: 5px;
            transition: 0.3s;
            background: #fff;
        }

        .image-upload-wrapper:hover {
            border-color: #f59e0b;
        }

        .image-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .upload-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #f59e0b;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
            transition: 0.2s;
        }

        .upload-btn:hover {
            transform: scale(1.1);
            background: #d97706;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="row mb-4 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold text-warning mb-1">
                        <i class="bx bx-edit text-warning me-1 fs-3 align-middle"></i> تعديل بيانات المعلم
                    </h4>
                    <p class="text-muted font-14 mb-0">تحديث الملف التعريفي، وتعديل التسكين الأكاديمي والإداري للمعلم: <span
                            class="fw-bold text-dark font-15">{{ $teacher->name_ar }}</span></p>
                </div>
                <a href="{{ route('admin.teachers.index') }}"
                    class="btn btn-light rounded-pill px-4 shadow-sm border fw-bold">
                    <i class="bx bx-arrow-back me-1 align-middle"></i> إلغاء العودة للقائمة
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 rounded-3 fw-bold mb-4 border-start border-danger border-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="bx bx-error-circle fs-4 me-2 text-danger"></i>
                    <strong class="font-15 text-danger">عذراً، يرجى مراجعة الأخطاء التالية وتصحيحها:</strong>
                </div>
                <ul class="mb-0 ps-4 font-13">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data"
            id="teacherForm" class="prevent-double-submit" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-4">
                {{-- ================= العمود الأول (اليمين - البيانات) ================= --}}
                <div class="col-xl-8">

                    {{-- 1. البيانات الشخصية --}}
                    <div class="card card-premium border-top border-primary border-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="form-section-title text-primary"><i class="bx bx-id-card fs-4 align-middle"></i>
                                البيانات الشخصية</h5>

                            {{-- رفع وتحديث الصورة --}}
                            <div class="text-center mb-4">
                                <label class="form-label d-block text-muted">تحديث الصورة الشخصية</label>
                                <div class="image-upload-wrapper">
                                    <img id="image_preview"
                                        src="{{ $teacher->hrEmployee && $teacher->hrEmployee->profile_image ? asset('storage/' . $teacher->hrEmployee->profile_image) : asset('ltr/assets/images/avatars/avatar-1.png') }}"
                                        class="image-preview" alt="Preview">
                                    <label for="profile_image" class="upload-btn" title="تغيير الصورة">
                                        <i class='bx bxs-camera fs-5'></i>
                                    </label>
                                    <input type="file" name="profile_image" id="profile_image" class="d-none"
                                        accept="image/*">
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">اسم المعلم الرباعي (عربي) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name_ar') input-error @enderror"
                                        name="name_ar" value="{{ old('name_ar', $teacher->name_ar) }}" required>
                                    @error('name_ar')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">رقم الهوية / الجواز <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control font-monospace @error('national_id') input-error @enderror"
                                        name="national_id" value="{{ old('national_id', $teacher->national_id) }}" required>
                                    @error('national_id')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">رقم الهاتف <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control font-monospace @error('phone') input-error @enderror"
                                        name="phone" value="{{ old('phone', $teacher->phone) }}" dir="ltr" required>
                                    @error('phone')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">الجنسية</label>
                                    <select name="nationality" class="form-select select2-init">
                                        <option value="عماني"
                                            {{ old('nationality', $teacher->nationality) == 'عماني' ? 'selected' : '' }}>
                                            عُماني</option>
                                        <option value="وافد"
                                            {{ old('nationality', $teacher->nationality) == 'وافد' ? 'selected' : '' }}>
                                            وافد</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">العنوان السكني</label>
                                    <input type="text" class="form-control" name="address"
                                        value="{{ old('address', $teacher->address) }}" placeholder="المدينة، الحي...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. البيانات الإدارية والمالية (مزامنة الـ HR) --}}
                    <div class="card card-premium border-top border-success border-4 mb-4 bg-soft-success">
                        <div class="card-body p-4">
                            <h5 class="form-section-title text-success"><i class="bx bx-briefcase fs-4 align-middle"></i>
                                البيانات الإدارية والمالية (HR)</h5>

                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">الرقم الوظيفي (كود الموظف)</label>
                                    <input type="text"
                                        class="form-control fw-bold text-muted bg-light font-monospace border-0 shadow-sm"
                                        value="{{ $teacher->hrEmployee->employee_code ?? 'غير مرتبط' }}" disabled
                                        readonly>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">الفرع (مكان العمل) <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2-init @error('branch_id') input-error @enderror"
                                        name="branch_id" id="branch_select" required>
                                        <option value="" disabled selected>-- اختر الفرع للتسكين --</option>
                                        @foreach ($branches ?? [] as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id', $teacher->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name_ar }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">القسم الأكاديمي <span
                                            class="text-danger">*</span></label>
                                    <select
                                        class="form-select select2-init @error('hr_department_id') input-error @enderror"
                                        name="hr_department_id" required>
                                        <option value="" disabled selected>-- اختر القسم --</option>
                                        @foreach ($departments ?? [] as $dept)
                                            <option value="{{ $dept->id }}"
                                                {{ old('hr_department_id', $teacher->hrEmployee->hr_department_id ?? '') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name_ar }}</option>
                                        @endforeach
                                    </select>
                                    @error('hr_department_id')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">الوردية (مواعيد العمل) <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2-init @error('hr_shift_id') input-error @enderror"
                                        name="hr_shift_id" required>
                                        <option value="" disabled selected>-- اختر الوردية --</option>
                                        @foreach ($shifts ?? [] as $shift)
                                            <option value="{{ $shift->id }}"
                                                {{ old('hr_shift_id', $teacher->hrEmployee->hr_shift_id ?? '') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->name_ar }}</option>
                                        @endforeach
                                    </select>
                                    @error('hr_shift_id')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold text-primary">المدير المباشر
                                        (للهيكل التنظيمي)</label>
                                    <select class="form-select select2-init" name="manager_id">
                                        <option value="" selected>-- يتبع الإدارة العليا مباشرة --</option>
                                        @foreach ($managers ?? [] as $manager)
                                            <option value="{{ $manager->id }}"
                                                {{ old('manager_id', $teacher->hrEmployee->manager_id ?? '') == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->full_name_ar }} ({{ $manager->job_title }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">الراتب الأساسي للعقد <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <input type="number" step="0.001" name="basic_salary"
                                            class="form-control text-success font-monospace text-center fs-5"
                                            value="{{ old('basic_salary', $teacher->hrEmployee->basic_salary ?? 0) }}"
                                            required>
                                        <span class="input-group-text fw-bold text-success border-start-0">ر.ع</span>
                                    </div>
                                    @error('basic_salary')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. التسكين الأكاديمي وبيانات الدخول --}}
                    <div class="card card-premium border-top border-warning border-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="form-section-title text-warning text-darken"><i
                                    class="bx bx-chalkboard fs-4 align-middle"></i> التسكين الأكاديمي وبيانات الدخول</h5>

                            <div
                                class="row g-4 mb-4 bg-soft-warning p-3 rounded-4 border border-warning border-opacity-25">
                                <div class="col-md-4">
                                    <label class="form-label text-dark font-13 fw-bold">البريد الإلكتروني (اختياري)</label>
                                    <input type="email" class="form-control font-monospace" name="email"
                                        value="{{ old('email', $teacher->email) }}" dir="ltr"
                                        placeholder="teacher@school.com">
                                    @error('email')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-dark font-13 fw-bold">كلمة المرور للبوابة <span
                                            class="text-info font-11">(اتركها فارغة للاحتفاظ بالقديمة)</span></label>
                                    <input type="password"
                                        class="form-control font-monospace @error('password') input-error @enderror"
                                        name="password" placeholder="********">
                                    @error('password')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-dark font-13 fw-bold">المواد التي يدرسها <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2-multiple @error('subjects') input-error @enderror"
                                        name="subjects[]" multiple="multiple" data-placeholder="اختر مادة أو أكثر..."
                                        required>
                                        @foreach (\App\Models\Subject::where('is_active', 1)->get() as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ (is_array(old('subjects')) && in_array($subject->id, old('subjects'))) || (isset($teacher) && $teacher->subjects->contains($subject->id)) ? 'selected' : '' }}>
                                                {{ $subject->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subjects')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- شجرة الفصول الأكاديمية (AJAX) --}}
                            <div class="accordion border-0 shadow-sm rounded-4" id="academicYearsAccordion">
                                <div
                                    class="text-center p-4 bg-light rounded-4 border border-dashed border-secondary border-opacity-25">
                                    <i class="bx bx-loader-alt bx-spin fs-1 text-warning opacity-75 d-block mb-2"></i>
                                    <h6 class="fw-bold text-dark">جاري استرجاع السجل الأكاديمي والتسكين الحالي...</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ================= العمود الثاني (اليسار - العقد) ================= --}}
                <div class="col-xl-4">

                    {{-- كارت الحفظ والتأكيد (Sticky) --}}
                    <div class="card card-premium shadow-sm border-0 mb-4 border-top border-success border-4"
                        style="position: sticky; top: 20px;">
                        <div class="card-body p-4 bg-light rounded-4 border text-center">
                            <i class="bx bx-save text-success font-36 d-block mb-2" style="font-size: 40px;"></i>
                            <h5 class="form-section-title border-0 text-dark mb-2 justify-content-center">حفظ التعديلات
                                واعتمادها</h5>
                            <p class="text-muted font-13 mb-4 fw-bold">تأكد من مراجعة البيانات جيداً. تعديل الراتب الأساسي
                                أو التسكين سيتم مزامنته تلقائياً مع نظام شؤون الموظفين (HR) ومسير الرواتب.</p>

                            <button type="submit"
                                class="btn w-100 btn-lg rounded-pill shadow font-bold btn-submit text-white"
                                style="background: linear-gradient(135deg, #10b981, #059669); border: none; transition: transform 0.2s;">
                                <i class="bx bx-check-shield me-2 fs-4 align-middle"></i> تحديث ومزامنة البيانات
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. Select2 Init
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: "rtl"
            });
            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
                dir: "rtl",
                width: '100%',
                allowClear: true
            });

            // 2. AJAX Branch -> Sections Tree (With Pre-selection logic)
            let branchSelect = $('#branch_select');
            branchSelect.on('change', function() {
                let branchId = $(this).val();
                if (!branchId) return;

                let teacherId = '{{ $teacher->id }}';
                let url = `{{ url('ar/admin/get-branch-sections-tree') }}/${branchId}/${teacherId}`;

                $('#academicYearsAccordion').html(
                    '<div class="text-center py-5"><i class="bx bx-loader-alt bx-spin fs-1 text-primary mb-3 d-block"></i><p class="text-dark fw-bold font-14">جاري بناء وتجهيز شجرة الفصول والشعب الأكاديمية...</p></div>'
                    );

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(htmlData) {
                        $('#academicYearsAccordion').html(htmlData);
                    },
                    error: function() {
                        $('#academicYearsAccordion').html(
                            '<div class="alert alert-danger text-center shadow-sm rounded-3 fw-bold"><i class="bx bx-error-circle me-1"></i> حدث خطأ أثناء الاتصال بالخادم. يرجى المحاولة مرة أخرى.</div>'
                            );
                    }
                });
            });

            // Trigger AJAX on page load to fetch the current teacher's sections
            if (branchSelect.val()) {
                branchSelect.trigger('change');
            }

            // 3. Image Preview Logic
            $('#profile_image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview').attr('src', e.target.result);
                }
                if (this.files[0]) {
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // 4. تأمين الفورم ضد الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="bx bx-loader-alt bx-spin me-2 fs-5 align-middle"></i> جاري الحفظ والتحديث...'
                    );
            });
        });
    </script>
@endpush
