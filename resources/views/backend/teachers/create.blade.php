@extends('backend.layouts.master')
@section('title', 'إضافة معلم جديد')
@section('title_page', 'إدارة المعلمين والهيئة التدريسية')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
            border-top: 5px solid #3b82f6;
        }

        /* 🌟 Premium Layout Styles 🌟 */
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
            background-color: rgba(59, 130, 246, 0.1);
        }

        .bg-soft-warning {
            background-color: rgba(245, 158, 11, 0.1);
        }

        .bg-soft-success {
            background-color: rgba(16, 185, 129, 0.1);
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
            border-color: #3b82f6;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* 🌟 Select2 Tweaks 🌟 */
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
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
            font-weight: bold;
            border-radius: 6px;
            margin-top: 5px;
        }

        /* 🔴 Error State 🔴 */
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

        /* 🌟 Signature Wrapper 🌟 */
        .signature-wrapper {
            border: 2px dashed #94a3b8;
            border-radius: 12px;
            background: #f8fafc;
            overflow: hidden;
            position: relative;
            transition: 0.3s;
        }

        canvas#signature-pad {
            cursor: crosshair;
            touch-action: none;
            width: 100%;
            height: 200px;
            display: block;
        }

        .note-editor.note-frame {
            border-radius: 10px;
            border-color: #cbd5e1;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .input-group-text {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="row mb-4 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold text-dark mb-1">
                        <i class="bx bx-user-plus text-primary me-1 fs-3 align-middle"></i> إضافة وتعيين معلم جديد
                    </h4>
                    <p class="text-muted font-14 mb-0">إنشاء ملف أكاديمي وإداري، صياغة العقد الوظيفي، وتسكين المعلم في
                        الشُعب.</p>
                </div>
                <a href="{{ route('admin.teachers.index') }}"
                    class="btn btn-light rounded-pill px-4 shadow-sm border fw-bold">
                    <i class="bx bx-arrow-back me-1 align-middle"></i> العودة للقائمة
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

        <form action="{{ route('admin.teachers.store') }}" method="POST" id="teacherForm" class="prevent-double-submit"
            novalidate>
            @csrf
            <div class="row g-4">
                {{-- ================= العمود الأول (اليمين - البيانات) ================= --}}
                <div class="col-xl-8">

                    {{-- 1. البيانات الشخصية --}}
                    <div class="card card-premium border-top border-primary border-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="form-section-title text-primary"><i class="bx bx-id-card fs-4 align-middle"></i>
                                البيانات الشخصية</h5>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">اسم المعلم الرباعي (عربي) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name_ar') input-error @enderror"
                                        name="name_ar" value="{{ old('name_ar') }}" required
                                        placeholder="اكتب الاسم كما في الهوية">
                                    @error('name_ar')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">رقم الهوية / الجواز <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control font-monospace @error('national_id') input-error @enderror"
                                        name="national_id" value="{{ old('national_id') }}" required>
                                    @error('national_id')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">رقم الهاتف <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control font-monospace @error('phone') input-error @enderror"
                                        name="phone" value="{{ old('phone') }}" dir="ltr" required>
                                    @error('phone')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">الجنسية</label>
                                    <select name="nationality" class="form-select select2-init">
                                        <option value="عماني" {{ old('nationality') == 'عماني' ? 'selected' : '' }}>عُماني
                                        </option>
                                        <option value="وافد" {{ old('nationality') == 'وافد' ? 'selected' : '' }}>وافد
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">العنوان السكني</label>
                                    <input type="text" class="form-control" name="address" value="{{ old('address') }}"
                                        placeholder="المدينة، الحي...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. البيانات الإدارية والمالية (الجديدة للـ HR) --}}
                    <div class="card card-premium border-top border-success border-4 mb-4 bg-soft-success">
                        <div class="card-body p-4">
                            <h5 class="form-section-title text-success"><i class="bx bx-briefcase fs-4 align-middle"></i>
                                البيانات الإدارية والمالية (HR)</h5>

                            <div class="row g-4">
                                {{-- 🔥 التعديل هنا: حقل الرقم الوظيفي أصبح Readonly ومقفول 🔥 --}}
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">الرقم الوظيفي (كود الموظف)</label>
                                    <input type="text"
                                        class="form-control fw-bold text-muted bg-light font-monospace border-0 shadow-sm"
                                        value="سيتم توليده تلقائياً" disabled readonly>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">الفرع (مكان العمل) <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select select2-init @error('branch_id') input-error @enderror"
                                        name="branch_id" id="branch_select" required>
                                        <option value="" disabled selected>-- اختر الفرع للتسكين --</option>
                                        @foreach ($branches ?? [] as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
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
                                                {{ old('hr_department_id') == $dept->id ? 'selected' : '' }}>
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
                                                {{ old('hr_shift_id') == $shift->id ? 'selected' : '' }}>
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
                                                {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->full_name_ar }} ({{ $manager->job_title }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">مدة العقد الوظيفي <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <input type="number" name="contract_months"
                                            class="form-control font-monospace text-center"
                                            value="{{ old('contract_months', 12) }}" required>
                                        <span class="input-group-text fw-bold border-start-0">شهر</span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label text-muted font-13 fw-bold">الراتب الأساسي للعقد <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text fw-bold text-success border-end-0">ر.ع</span>
                                        <input type="number" step="0.001" name="basic_salary"
                                            class="form-control text-success font-monospace text-center fs-5"
                                            value="{{ old('basic_salary', 0) }}" required>
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
                                        value="{{ old('email') }}" dir="ltr" placeholder="teacher@school.com">
                                    @error('email')
                                        <div class="error-msg"><i class="bx bx-x-circle"></i> {{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-dark font-13 fw-bold">كلمة المرور للبوابة <span
                                            class="text-danger">*</span></label>
                                    <input type="password"
                                        class="form-control font-monospace @error('password') input-error @enderror"
                                        name="password" placeholder="********" required>
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
                                                {{ is_array(old('subjects')) && in_array($subject->id, old('subjects')) ? 'selected' : '' }}>
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
                                    <i class="bx bx-info-circle fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                    <h6 class="fw-bold text-dark">يرجى اختيار الفرع من الأعلى أولاً</h6>
                                    <p class="text-muted mb-0 font-13">سيتم عرض شجرة الفصول والشعب الأكاديمية المتاحة للربط
                                        تلقائياً بمجرد تحديد الفرع.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ================= العمود الثاني (اليسار - العقد) ================= --}}
                <div class="col-xl-4">

                    {{-- الكارت الرابع: شروط العقد والتوقيع --}}
                    <div class="card card-premium shadow-sm border-0 mb-4 border-top border-info border-4"
                        style="position: sticky; top: 20px;">
                        <div class="card-body p-4 bg-white">
                            <h5 class="form-section-title text-info border-0 text-center justify-content-center"><i
                                    class="bx bx-pen fs-4 align-middle"></i> صياغة العقد والتوقيع</h5>

                            <div class="mb-4">
                                <label class="form-label fw-bold font-13 text-dark">الشروط والأحكام (النسخة
                                    العربية)</label>
                                <textarea id="summernote-ar" name="contract_terms_ar">{{ old('contract_terms_ar') }}</textarea>
                                @error('contract_terms_ar')
                                    <small class="text-danger font-12 fw-bold mt-1 d-block"><i class="bx bx-x-circle"></i>
                                        {{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold font-13 text-dark">Contract Terms (English
                                    Version)</label>
                                <textarea id="summernote-en" name="contract_terms_en">{{ old('contract_terms_en') }}</textarea>
                                @error('contract_terms_en')
                                    <small class="text-danger font-12 fw-bold mt-1 d-block"><i class="bx bx-x-circle"></i>
                                        {{ $message }}</small>
                                @enderror
                            </div>

                            <hr class="mb-4 border-secondary opacity-25">

                            <label class="form-label text-dark font-13 d-block text-center fw-bold mb-3">توقيع المعلم
                                بالموافقة على البنود والراتب <span class="text-danger">*</span></label>

                            <div class="signature-wrapper mb-3 shadow-sm" id="signature-container">
                                <canvas id="signature-pad" class="bg-white"></canvas>
                            </div>

                            <div class="text-center mb-4">
                                <button type="button" id="clear-signature"
                                    class="btn btn-sm btn-outline-danger rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bx bx-eraser me-1"></i> مسح وإعادة التوقيع
                                </button>
                                <input type="hidden" name="signature_data" id="signature_data">
                            </div>

                            <button type="submit"
                                class="btn w-100 btn-lg rounded-pill shadow font-bold btn-submit text-white"
                                style="background: linear-gradient(135deg, #10b981, #059669); border: none; transition: transform 0.2s;">
                                <i class="bx bx-check-shield me-2 fs-4 align-middle"></i> حفظ وتسجيل المعلم
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
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
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

            // 2. Summernote Init
            let summernoteOptions = {
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['view', ['fullscreen']]
                ]
            };
            $('#summernote-ar').summernote({
                ...summernoteOptions,
                placeholder: 'اكتب بنود عقد المعلم هنا...'
            });
            $('#summernote-en').summernote({
                ...summernoteOptions,
                placeholder: 'Write English contract terms here...'
            });

            // 3. AJAX Branch -> Sections Tree
            let branchSelect = $('#branch_select');
            branchSelect.on('change', function() {
                let branchId = $(this).val();
                if (!branchId) return;

                let url = `{{ url('ar/admin/get-branch-sections-tree') }}/${branchId}`;

                $('#academicYearsAccordion').html(
                    '<div class="text-center py-5"><i class="bx bx-loader-alt bx-spin fs-1 text-primary mb-3 d-block"></i><p class="text-dark fw-bold font-14">جاري تجهيز شجرة الفصول والشعب...</p></div>'
                    );

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function(htmlData) {
                        $('#academicYearsAccordion').html(htmlData);
                    },
                    error: function() {
                        $('#academicYearsAccordion').html(
                            '<div class="alert alert-danger text-center shadow-sm rounded-3 fw-bold"><i class="bx bx-error-circle me-1"></i> حدث خطأ أثناء الاتصال بالخادم.</div>'
                            );
                    }
                });
            });

            if (branchSelect.val()) {
                branchSelect.trigger('change');
            }

            // 4. Signature Pad Logic
            var canvas = document.getElementById('signature-pad');
            var signaturePad = null;

            function resizeCanvas() {
                var ratio = Math.max(window.devicePixelRatio || 1, 1);
                var data = signaturePad ? signaturePad.toData() : null;
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                if (signaturePad && data) {
                    signaturePad.fromData(data);
                }
            }

            setTimeout(function() {
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(15, 23, 42)'
                });
                resizeCanvas();
            }, 100);

            window.addEventListener("resize", resizeCanvas);

            $('#clear-signature').on('click', function() {
                if (signaturePad) {
                    signaturePad.clear();
                    $('#signature_data').val('');
                }
            });

            // 5. Form Submit Validation
            $('#teacherForm').on('submit', function(e) {
                if (signaturePad && signaturePad.isEmpty()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'التوقيع مفقود!',
                        text: '⚠️ يرجى إدراج توقيع المعلم بالموافقة على العقد قبل الحفظ.',
                        confirmButtonColor: '#3b82f6'
                    });
                    $('#signature-container').css({
                        'border-color': '#ef4444',
                        'box-shadow': '0 0 0 4px rgba(239, 68, 68, 0.2)'
                    });
                    setTimeout(() => {
                        $('#signature-container').css({
                            'border-color': '#94a3b8',
                            'box-shadow': 'none'
                        });
                    }, 2500);
                    return false;
                } else {
                    $('#signature_data').val(signaturePad.toDataURL("image/png"));
                    let btn = $(this).find('.btn-submit');
                    btn.prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin me-2 fs-5 align-middle"></i> جاري حفظ وربط البيانات...'
                        );
                }
            });
        });
    </script>
@endpush
