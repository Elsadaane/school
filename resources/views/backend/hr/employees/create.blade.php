@extends('backend.layouts.master')
@section('title', 'تعيين موظف جديد')
@section('title_page', 'الموارد البشرية - تعيين موظف')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css"
        id="select2-theme" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .page-wrapper {
            background-color: #f4f7f6;
            padding: 25px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #1e293b, #3b82f6);
            color: white;
            padding: 20px;
        }

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .input-group-text {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border-color: #e2e8f0;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
            border-color: #3b82f6;
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
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
            transition: 0.2s;
        }

        .upload-btn:hover {
            transform: scale(1.1);
            background: #2563eb;
        }

        .form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-3 fw-bold border-0 border-start border-danger border-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="bx bx-error-circle me-1"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.hr.employees.store') }}" method="POST" enctype="multipart/form-data"
            id="employeeForm">
            @csrf

            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h4 class="fw-bolder text-dark mb-1"><i class="bx bx-user-plus text-primary me-2 fs-3 align-middle"></i>
                        تعيين موظف جديد</h4>
                    <span class="text-muted fw-bold font-13"><i class='bx bx-info-circle text-info me-1'></i> سيقوم النظام
                        بإنشاء حساب دخول للموظف تلقائياً (كلمة المرور: رقم الهاتف)</span>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex justify-content-md-end gap-2 flex-wrap">
                    <a href="{{ route('admin.hr.employees.index') }}"
                        class="btn btn-light border fw-bold shadow-sm rounded-pill px-4">
                        <i class='bx bx-arrow-back me-1'></i> تراجع
                    </a>
                    @can('create_employees')
                        <button type="submit" class="btn btn-primary fw-bold px-5 shadow-sm rounded-pill btn-submit">
                            <i class='bx bx-save me-1'></i> حفظ بيانات الموظف
                        </button>
                    @endcan
                </div>
            </div>

            <div class="row g-4">
                {{-- 🌟 القسم الأول: البيانات الشخصية (العمود الجانبي) --}}
                <div class="col-xl-4 col-lg-5">
                    <div class="card card-custom h-100 border-top border-primary border-4">
                        <div class="card-header-custom text-center">
                            <h5 class="fw-bold mb-0 text-white"><i class='bx bx-user-circle fs-4 align-middle me-1'></i>
                                البيانات الشخصية</h5>
                        </div>
                        <div class="card-body p-4 bg-white">

                            {{-- رفع الصورة --}}
                            <div class="text-center mb-4">
                                <label class="form-label d-block text-muted">الصورة الشخصية</label>
                                <div class="image-upload-wrapper">
                                    <img id="image_preview" src="{{ asset('ltr/assets/images/avatars/avatar-1.png') }}"
                                        class="image-preview" alt="Preview">
                                    <label for="profile_image" class="upload-btn" title="اختيار صورة">
                                        <i class='bx bxs-camera fs-5'></i>
                                    </label>
                                    <input type="file" name="profile_image" id="profile_image" class="d-none"
                                        accept="image/*">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-danger">الاسم بالكامل (عربي) *</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text border-0"><i class='bx bx-user'></i></span>
                                    <input type="text" name="full_name_ar"
                                        class="form-control fw-bold text-dark border-0 bg-light"
                                        value="{{ old('full_name_ar') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">الاسم (إنجليزي)</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text border-0"><i class='bx bx-user'></i></span>
                                    <input type="text" name="full_name_en" class="form-control border-0 bg-light"
                                        value="{{ old('full_name_en') }}" dir="ltr">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-danger">رقم الهاتف *</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text border-0"><i class='bx bx-phone'></i></span>
                                    <input type="text" name="phone"
                                        class="form-control fw-bold border-0 bg-light font-monospace"
                                        value="{{ old('phone') }}" dir="ltr" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">البريد الإلكتروني</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text border-0"><i class='bx bx-envelope'></i></span>
                                    <input type="email" name="email"
                                        class="form-control border-0 bg-light font-monospace" value="{{ old('email') }}"
                                        dir="ltr">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-danger">رقم الهوية / الإقامة *</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text border-0"><i class='bx bx-id-card'></i></span>
                                    <input type="text" name="national_id"
                                        class="form-control fw-bold border-0 bg-light font-monospace"
                                        value="{{ old('national_id') }}" required>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-6 mb-3">
                                    <label class="form-label">الجنسية</label>
                                    <select name="nationality" class="form-select bg-light shadow-sm border-0 fw-bold">
                                        <option value="عماني" {{ old('nationality') == 'عماني' ? 'selected' : '' }}>عُماني
                                        </option>
                                        <option value="وافد" {{ old('nationality') == 'وافد' ? 'selected' : '' }}>وافد
                                        </option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">النوع</label>
                                    <select name="gender" class="form-select bg-light shadow-sm border-0 fw-bold">
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر
                                        </option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">تاريخ الميلاد</label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text border-0"><i class='bx bx-calendar-star'></i></span>
                                        <input type="date" name="birth_date"
                                            class="form-control bg-light border-0 fw-bold text-center"
                                            value="{{ old('birth_date') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🌟 القسم الثاني: البيانات الوظيفية والمالية (العمود الرئيسي) --}}
                <div class="col-xl-8 col-lg-7">
                    <div class="card card-custom h-100 border-top border-warning border-4">
                        <div class="card-header bg-white pt-4 pb-3 border-bottom">
                            <h5 class="fw-bold mb-0 text-dark"><i
                                    class='bx bx-briefcase text-warning fs-4 align-middle me-2'></i> البيانات الوظيفية
                                والمالية</h5>
                        </div>
                        <div class="card-body p-4 bg-white">

                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-danger">الرقم الوظيفي (كود الموظف) *</label>
                                    <input type="text" name="employee_code"
                                        class="form-control fw-bold text-primary bg-light font-monospace border-primary border-opacity-50"
                                        value="{{ old('employee_code', 'EMP-' . rand(1000, 9999)) }}" dir="ltr"
                                        required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-danger">الفرع (مكان العمل) *</label>
                                    <select name="branch_id" class="form-select select2-init" required>
                                        <option value="" disabled selected>اختر الفرع</option>
                                        @foreach ($branches ?? [] as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-danger">القسم الموجه إليه *</label>
                                    <select name="hr_department_id" class="form-select select2-init" required>
                                        <option value="" disabled selected>اختر القسم</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}"
                                                {{ old('hr_department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-danger">المسمى الوظيفي *</label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text border-0"><i class='bx bx-id-card'></i></span>
                                        <input type="text" name="job_title"
                                            class="form-control border-0 bg-light fw-bold" value="{{ old('job_title') }}"
                                            placeholder="مثال: محاسب، كاشير، الخ..." required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-danger">الوردية المخصصة *</label>
                                    <select name="hr_shift_id" class="form-select select2-init" required>
                                        <option value="" disabled selected>اختر الوردية</option>
                                        @foreach ($shifts as $shift)
                                            <option value="{{ $shift->id }}"
                                                {{ old('hr_shift_id') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">الدرجة الوظيفية</label>
                                    <select name="hr_job_grade_id" class="form-select select2-init">
                                        <option value="" selected>-- بدون درجة --</option>
                                        @foreach ($grades as $grade)
                                            <option value="{{ $grade->id }}"
                                                {{ old('hr_job_grade_id') == $grade->id ? 'selected' : '' }}>
                                                {{ $grade->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- 🔥 حقل المدير المباشر للهيكل التنظيمي 🔥 --}}
                                <div class="col-md-4">
                                    <label class="form-label text-primary">المدير المباشر (للهيكل التنظيمي)</label>
                                    <select name="manager_id" class="form-select select2-init">
                                        <option value="" selected>-- يتبع الإدارة العليا مباشرة --</option>
                                        @foreach ($managers ?? [] as $manager)
                                            <option value="{{ $manager->id }}"
                                                {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->full_name_ar }} - {{ $manager->job_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-danger">تاريخ التعيين *</label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text border-0"><i
                                                class='bx bx-calendar-check'></i></span>
                                        <input type="date" name="join_date"
                                            class="form-control border-0 bg-light fw-bold text-center"
                                            value="{{ old('join_date', date('Y-m-d')) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-danger">الراتب الأساسي *</label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <span class="input-group-text border-0"><i
                                                class='bx bx-money text-success'></i></span>
                                        <input type="number" step="0.001" name="basic_salary"
                                            class="form-control fw-bold text-success border-0 bg-light font-monospace fs-5 text-center"
                                            value="{{ old('basic_salary', 0) }}" required>
                                        <span class="input-group-text border-0 fw-bold">ر.ع</span>
                                    </div>
                                </div>

                                {{-- التأمينات الاجتماعية --}}
                                <div class="col-md-12">
                                    <div class="card bg-light border-0 rounded-4 mt-2">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3 text-dark"><i
                                                    class='bx bx-shield-quarter text-info me-1 fs-5 align-middle'></i>
                                                التأمينات الاجتماعية (PASI)</h6>
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <div class="form-check form-switch mt-1">
                                                        <input class="form-check-input" type="checkbox" name="is_insured"
                                                            id="isInsuredSwitch" value="1"
                                                            {{ old('is_insured') ? 'checked' : '' }}
                                                            onchange="togglePasiInput()">
                                                        <label class="form-check-label ms-2 fw-bold text-dark"
                                                            for="isInsuredSwitch">الموظف خاضع للتأمينات</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-8" id="pasi_number_div"
                                                    style="display: {{ old('is_insured') ? 'block' : 'none' }};">
                                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                                        <span class="input-group-text border-0 fw-bold text-muted">رقم
                                                            التأمينات</span>
                                                        <input type="text" name="pasi_number"
                                                            class="form-control border-0 font-monospace fw-bold"
                                                            value="{{ old('pasi_number') }}"
                                                            placeholder="أدخل الرقم المسجل في سجلات التأمينات...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
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
    <script>
        function togglePasiInput() {
            var isInsured = document.getElementById('isInsuredSwitch').checked;
            var pasiDiv = document.getElementById('pasi_number_div');
            if (isInsured) {
                $(pasiDiv).slideDown();
            } else {
                $(pasiDiv).slideUp();
                $(pasiDiv).find('input').val(''); // تفريغ الحقل عند الإغلاق
            }
        }

        $(document).ready(function() {
            // تفعيل Select2 للقوائم الطويلة
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
            });

            // تأمين الفورم من الإرسال المتكرر
            $('#employeeForm').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    "<i class='bx bx-loader bx-spin me-1'></i> جاري الحفظ والتسجيل...");
            });

            // عرض الصورة المرفوعة مباشرة (Image Preview)
            $('#profile_image').change(function() {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#image_preview').attr('src', e.target.result);
                }
                if (this.files[0]) {
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // التأكد من عرض قسم التأمينات عند تحميل الصفحة إذا كان مفعلاً
            togglePasiInput();
        });
    </script>
@endpush
