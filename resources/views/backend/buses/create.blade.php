@extends('backend.layouts.master')
@section('title', 'إضافة حافلة جديدة')
@section('title_page', 'إدارة الحركة والنقل')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
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

        /* 🌟 Premium Stepper UI 🌟 */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .stepper-wrapper::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 33.33%;
            color: #64748b;
        }

        .step-bubble {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .step-title {
            font-size: 14px;
            font-weight: 700;
        }

        .nav-link.active .step-bubble {
            background: #3b82f6;
            border-color: #3b82f6;
            color: #fff;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
        }

        .nav-link.active .step-title {
            color: #3b82f6;
        }

        .nav-link.passed .step-bubble {
            background: #10b981;
            border-color: #10b981;
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

        /* 🌟 Form Tweaks 🌟 */
        .form-section-title {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #e2e8f0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 13px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 10px 15px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            background-color: #fff;
        }

        /* Select2 */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #f8fafc;
            font-weight: 600;
        }

        /* Signature Box */
        .signature-box {
            border: 2px dashed #3b82f6;
            border-radius: 12px;
            background-color: #f8fafc;
            padding: 15px;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.02);
            touch-action: none;
        }

        .signature-pad {
            border-radius: 8px;
            background-color: #fff;
            border: 1px solid #dee2e6;
            width: 100%;
            height: 200px;
            cursor: crosshair;
        }

        .ck-editor__editable_inline {
            min-height: 150px;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="page-title font-bold mb-1 text-dark"><i
                        class="bx bx-bus text-primary fs-3 align-middle me-1"></i> تسجيل حافلة جديدة</h4>
                <p class="text-muted font-14">أدخل بيانات الحافلة، حدد خط السير، اربطها بالسائق والمشرف، وقم بتوثيق العقد
                    إلكترونياً.</p>
            </div>
            <a href="{{ route('admin.buses.index') }}" class="btn btn-light rounded-pill shadow-sm border font-bold px-4">
                <i class="bx bx-arrow-back align-middle me-1"></i> رجوع للقائمة
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3 mb-4 border-0 border-start border-danger border-4">
            <div class="d-flex align-items-center mb-2">
                <i class="bx bx-error-circle fs-4 me-2"></i>
                <strong class="font-14">توجد أخطاء في البيانات، يرجى مراجعتها:</strong>
            </div>
            <ul class="mb-0 ps-4 font-13 fw-bold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card card-premium shadow-sm border-0 mb-5">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('admin.buses.store') }}" method="POST" id="busForm" enctype="multipart/form-data">
                @csrf

                {{-- 🌟 Stepper UI 🌟 --}}
                <ul class="nav nav-pills stepper-wrapper" id="busTab" role="tablist">
                    <li class="nav-item step-item" role="presentation">
                        <button class="nav-link active" id="bus-tab" data-bs-target="#bus-step" type="button"
                            role="tab">
                            <div class="step-bubble"><i class='bx bx-bus'></i></div>
                            <div class="step-title">بيانات الحافلة والخط</div>
                        </button>
                    </li>
                    <li class="nav-item step-item" role="presentation">
                        <button class="nav-link" id="driver-tab" data-bs-target="#driver-step" type="button"
                            role="tab">
                            <div class="step-bubble"><i class='bx bx-user-pin'></i></div>
                            <div class="step-title">السائق والمشرف</div>
                        </button>
                    </li>
                    <li class="nav-item step-item" role="presentation">
                        <button class="nav-link" id="contract-tab" data-bs-target="#contract-step" type="button"
                            role="tab">
                            <div class="step-bubble"><i class='bx bx-pen'></i></div>
                            <div class="step-title">العقد والتوقيع</div>
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-5" id="busTabContent">

                    {{-- ================= الخطوة 1: الحافلة ================= --}}
                    <div class="tab-pane fade show active" id="bus-step" role="tabpanel">
                        <h5 class="form-section-title text-primary"><i class="bx bx-info-circle fs-4 text-primary"></i>
                            المعلومات الأساسية للمركبة وخط السير</h5>
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">الفرع <span class="text-danger">*</span></label>
                                <select class="form-select select2-init" name="branch_id" required>
                                    <option value="" selected disabled>اختر الفرع</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">السنة الدراسية <span class="text-danger">*</span></label>
                                <select class="form-select select2-init" name="academic_year_id" required>
                                    <option value="" selected disabled>اختر السنة</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}"
                                            {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">رقم الحافلة (اللوحة) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control font-monospace" name="bus_number"
                                    value="{{ old('bus_number') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">نوع الحافلة</label>
                                <input type="text" class="form-control" name="bus_type" value="{{ old('bus_type') }}"
                                    placeholder="مثال: نيسان كوستر">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">موديل سنة</label>
                                <input type="text" class="form-control font-monospace" name="bus_model"
                                    value="{{ old('bus_model') }}" placeholder="مثال: 2024">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">عدد الركاب (السعة المقررة)</label>
                                <input type="number" class="form-control font-monospace" name="capacity"
                                    id="bus_capacity" value="{{ old('capacity') }}" placeholder="مثال: 30">
                            </div>

                            {{-- 🔄 خط السير (من جدول transport_routes) --}}
                            <div class="col-md-6">
                                <label class="form-label">خط سير الحافلة <span class="text-danger">*</span></label>
                                <select class="form-select select2-init" name="transport_route_id" required>
                                    <option value="" selected disabled>اختر خط السير</option>
                                    @foreach ($transportRoutes as $route)
                                        <option value="{{ $route->id }}"
                                            {{ old('transport_route_id') == $route->id ? 'selected' : '' }}>
                                            {{ $route->name }}
                                            @if ($route->monthly_fare_both > 0)
                                                (ذهاب وإياب: {{ number_format($route->monthly_fare_both, 3) }} OMR)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted font-12">* تسعيرة الطلاب تُحدد وتُجلب تلقائياً من خلال خط السير
                                    المختار.</small>
                            </div>

                            {{-- 🔄 نوع الملكية --}}
                            <div class="col-md-6">
                                <label class="form-label">نوع ملكية الحافلة <span class="text-danger">*</span></label>
                                <select class="form-select select2-init" name="ownership_type" id="ownership_type"
                                    required>
                                    <option value="owned" {{ old('ownership_type') == 'owned' ? 'selected' : '' }}>مملوكة
                                        للمدرسة</option>
                                    <option value="rented" {{ old('ownership_type') == 'rented' ? 'selected' : '' }}>
                                        مستأجرة (يُدفع إيجار)</option>
                                </select>
                            </div>

                            {{-- 🔄 الإيجار الشهري --}}
                            <div class="col-md-6" id="rent_amount_container" style="display: none;">
                                <label class="form-label text-danger">الإيجار الشهري للحافلة (يُدفع للمالك/السائق)</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-danger text-white border-0 fw-bold">OMR</span>
                                    <input type="number" step="0.001"
                                        class="form-control border-0 bg-light font-monospace text-center text-danger"
                                        id="monthly_rent" name="monthly_rent" value="{{ old('monthly_rent') }}"
                                        placeholder="0.000">
                                </div>
                            </div>

                            {{-- 🆕 تواريخ التأمين والفحص --}}
                            <div class="col-md-3">
                                <label class="form-label">تاريخ انتهاء التأمين</label>
                                <input type="date" class="form-control font-monospace" name="insurance_expiry_date"
                                    value="{{ old('insurance_expiry_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">تاريخ انتهاء الاستمارة/الفحص</label>
                                <input type="date" class="form-control font-monospace" name="registration_expiry_date"
                                    value="{{ old('registration_expiry_date') }}">
                            </div>
                        </div>
                        <div class="text-end border-top pt-4 mt-2">
                            <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold btn-next shadow-sm"
                                onclick="goToNextStep()">
                                التالي: السائق والمشرف <i class="bx bx-left-arrow-alt ms-1 align-middle fs-5"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ================= الخطوة 2: السائق والمشرف ================= --}}
                    <div class="tab-pane fade" id="driver-step" role="tabpanel">
                        <h5 class="form-section-title text-success"><i class="bx bx-steering fs-4 text-success"></i>
                            بيانات السائق والمشرف والارتباط بشؤون الموظفين</h5>

                        <div
                            class="alert alert-success bg-success bg-opacity-10 border-0 border-start border-success border-4 shadow-sm mb-4">
                            <i class="bx bx-check-shield fs-4 align-middle text-success me-2"></i>
                            <strong class="text-success font-14">ميزة الـ ERP:</strong> <span
                                class="text-dark font-13">عند اختيار سائق من القائمة، سيتم تسجيل هذه الحافلة كـ (عهدة) في
                                ملفه الوظيفي تلقائياً.</span>
                        </div>

                        <div class="row g-4 mb-4">
                            {{-- 🔥 السائق من الـ HR 🔥 --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label text-primary fs-6">اختر السائق من شؤون الموظفين (موصى به)</label>
                                <select class="form-select select2-init" name="driver_id" id="hr_driver_select">
                                    <option value="">-- سائق خارجي (إدخال بيانات يدوياً بالأسفل) --</option>
                                    @foreach ($drivers ?? [] as $driver)
                                        <option value="{{ $driver->id }}" data-name="{{ $driver->full_name_ar }}"
                                            data-phone="{{ $driver->phone }}" data-nid="{{ $driver->national_id }}"
                                            {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                            {{ $driver->full_name_ar }} (كود: {{ $driver->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 🆕 المشرف من الـ HR --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label text-info fs-6">اختر مشرف الحافلة من شؤون الموظفين</label>
                                <select class="form-select select2-init" name="supervisor_id">
                                    <option value="">-- لا يوجد مشرف --</option>
                                    @foreach ($supervisors ?? [] as $supervisor)
                                        <option value="{{ $supervisor->id }}"
                                            {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->full_name_ar }} (كود: {{ $supervisor->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 🔥 الإدخال اليدوي (يختفي لو اخترنا من الـ HR) 🔥 --}}
                            <div id="manual_driver_fields"
                                class="row g-4 m-0 p-4 bg-light rounded-4 border border-dashed border-secondary border-opacity-25 w-100">
                                <div class="col-12 mb-0 mt-0">
                                    <h6 class="fw-bold text-muted"><i class="bx bx-edit"></i> الإدخال اليدوي (للسائقين
                                        الخارجيين)</h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">الاسم الرباعي <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-white" name="driver_name"
                                        id="manual_driver_name" value="{{ old('driver_name') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم الهوية / الإقامة</label>
                                    <input type="text" class="form-control bg-white font-monospace"
                                        name="driver_national_id" id="manual_driver_nid"
                                        value="{{ old('driver_national_id') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم الهاتف للتواصل <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-white font-monospace"
                                        name="driver_phone" id="manual_driver_phone" value="{{ old('driver_phone') }}"
                                        dir="ltr">
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <label class="form-label">ملاحظات إضافية (اختياري)</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="أي ملاحظات تخص السائق أو المركبة...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-4 mt-2">
                            <button type="button"
                                class="btn btn-light rounded-pill px-4 fw-bold btn-prev text-muted border shadow-sm">
                                <i class="bx bx-right-arrow-alt me-1 align-middle fs-5"></i> السابق
                            </button>
                            <button type="button" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm"
                                id="btn-validate-driver">
                                التالي: العقد والتوقيع <i class="bx bx-left-arrow-alt ms-1 align-middle fs-5"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ================= الخطوة 3: العقد والتوقيع ================= --}}
                    <div class="tab-pane fade" id="contract-step" role="tabpanel">
                        <h5 class="form-section-title text-warning"><i class="bx bx-file-blank fs-4 text-warning"></i>
                            بنود العقد والتوثيق</h5>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">شروط العقد (بالعربية)</label>
                                <textarea id="ckeditor-ar" name="contract_terms_ar">{!! old('contract_terms_ar', $globalSettings->driver_contract_terms_ar ?? '') !!}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contract Terms (English)</label>
                                <textarea id="ckeditor-en" name="contract_terms_en">{!! old('contract_terms_en', $globalSettings->driver_contract_terms_en ?? '') !!}</textarea>
                            </div>
                        </div>

                        <div class="bg-light p-4 rounded-4 border mt-4">
                            <div class="text-center">
                                <h5 class="text-danger font-bold mb-1"><i class="bx bx-pen align-middle"></i> توقيع
                                    المستلم / السائق إلكترونياً <span class="text-danger">*</span></h5>
                                <p class="text-muted font-13 mb-3">يرجى التوقيع داخل المربع أدناه باستخدام الماوس أو اللمس
                                    لاعتماد استلام العهدة / العقد.</p>

                                <div class="signature-box" id="signature-container">
                                    <canvas id="driver-signature-pad" class="signature-pad"></canvas>
                                </div>

                                <div class="mt-3">
                                    <button type="button" id="clear-signature"
                                        class="btn btn-outline-danger rounded-pill px-4 shadow-sm fw-bold">
                                        <i class="bx bx-eraser align-middle"></i> مسح وإعادة التوقيع
                                    </button>
                                </div>
                                <input type="hidden" name="signature_data" id="signature_data" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between border-top pt-4 mt-4">
                            <button type="button"
                                class="btn btn-light rounded-pill px-4 fw-bold btn-prev text-muted border shadow-sm">
                                <i class="bx bx-right-arrow-alt me-1 align-middle fs-5"></i> السابق
                            </button>

                            @can('create_buses')
                                <button type="submit"
                                    class="btn text-white btn-lg px-5 rounded-pill font-bold shadow-lg prevent-double-submit"
                                    id="btn-final-submit"
                                    style="background: linear-gradient(135deg, #10b981, #059669); border: none;">
                                    <i class="bx bx-check-shield me-1 align-middle fs-4"></i> اعتماد وحفظ البيانات
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-lg px-5 rounded-pill font-bold shadow-lg"
                                    disabled>
                                    <i class="bx bx-lock me-1 align-middle"></i> غير مصرح بالحفظ
                                </button>
                            @endcan
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var signaturePad;

        function resizeCanvas() {
            var canvas = document.getElementById('driver-signature-pad');
            var container = document.getElementById('signature-container');
            if (container.offsetWidth === 0) return;
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            var data = null;
            if (signaturePad && !signaturePad.isEmpty()) {
                data = signaturePad.toData();
            }
            canvas.width = container.offsetWidth * ratio;
            canvas.height = 200 * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            if (signaturePad) {
                signaturePad.clear();
                if (data) {
                    signaturePad.fromData(data);
                }
            }
        }

        function goToNextStep() {
            let currentTabBtn = $('.stepper-wrapper button.active');
            let nextTabBtn = currentTabBtn.closest('li').next('li').find('button');
            if (nextTabBtn.length > 0) {
                currentTabBtn.addClass('passed');
                let targetPane = nextTabBtn.attr('data-bs-target');
                $('.tab-pane').removeClass('show active');
                $(targetPane).addClass('show active');
                currentTabBtn.removeClass('active');
                nextTabBtn.addClass('active');
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                if (targetPane === '#contract-step') {
                    setTimeout(function() {
                        resizeCanvas();
                    }, 250);
                }
            }
        }

        $(document).ready(function() {
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: "rtl"
            });

            // 🔥 إظهار وإخفاء حقل الإيجار بناءً على الملكية 🔥
            $('#ownership_type').on('change', function() {
                if ($(this).val() === 'rented') {
                    $('#rent_amount_container').slideDown();
                } else {
                    $('#rent_amount_container').slideUp();
                    $('#monthly_rent').val('');
                }
            });
            $('#ownership_type').trigger('change');

            // 🔥 اللوجيك الذكي للتبديل بين سائق الـ HR والإدخال اليدوي 🔥
            $('#hr_driver_select').on('change', function() {
                if ($(this).val() !== "") {
                    $('#manual_driver_fields').slideUp();
                    $('#manual_driver_name, #manual_driver_phone').removeAttr('required');
                } else {
                    $('#manual_driver_fields').slideDown();
                    $('#manual_driver_name, #manual_driver_phone').attr('required', true);
                }
            });
            $('#hr_driver_select').trigger('change');

            // التحقق في الخطوة 2
            $('#btn-validate-driver').on('click', function(e) {
                e.preventDefault();
                let isHrDriver = $('#hr_driver_select').val() !== "";
                if (!isHrDriver) {
                    if ($('#manual_driver_name').val() === '' || $('#manual_driver_phone').val() === '') {
                        Swal.fire('بيانات ناقصة', 'يرجى إدخال اسم السائق ورقم هاتفه كحد أدنى.', 'error');
                        return;
                    }
                }
                goToNextStep();
            });

            // CKEditor
            ClassicEditor.create(document.querySelector('#ckeditor-ar'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote',
                    '|', 'undo', 'redo'
                ],
                language: 'ar'
            }).catch(error => console.error(error));

            ClassicEditor.create(document.querySelector('#ckeditor-en'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote',
                    '|', 'undo', 'redo'
                ],
                language: 'en'
            }).catch(error => console.error(error));

            // Signature Pad
            var canvasElement = document.getElementById('driver-signature-pad');
            signaturePad = new SignaturePad(canvasElement, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });

            window.addEventListener("resize", function() {
                if ($('#contract-step').hasClass('active')) {
                    resizeCanvas();
                }
            });

            $('#clear-signature').on('click', function() {
                signaturePad.clear();
            });

            // Stepper Navigation
            $('.btn-prev').on('click', function(e) {
                e.preventDefault();
                let currentTabBtn = $('.stepper-wrapper button.active');
                let prevTabBtn = currentTabBtn.closest('li').prev('li').find('button');
                if (prevTabBtn.length > 0) {
                    currentTabBtn.removeClass('passed');
                    let targetPane = prevTabBtn.attr('data-bs-target');
                    $('.tab-pane').removeClass('show active');
                    $(targetPane).addClass('show active');
                    currentTabBtn.removeClass('active');
                    prevTabBtn.addClass('active').removeClass('passed');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });

            // Form Submit
            $('#busForm').on('submit', function(e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'توقيع السائق مفقود!',
                        text: 'لا يمكن حفظ البيانات وإبرام العقد بدون توقيع السائق إلكترونياً.',
                        confirmButtonText: 'حسناً، سأقوم بالتوقيع',
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    $('#signature_data').val(signaturePad.toDataURL("image/png"));
                    $('#btn-final-submit').prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin me-2 fs-4 align-middle"></i> جاري الحفظ وتوثيق العهدة...'
                    );
                }
            });
        });
    </script>
@endpush
