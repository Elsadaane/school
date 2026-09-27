@extends('backend.layouts.master')
@section('title', 'إعدادات النظام العامة')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .settings-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background: #fff;
        }

        .nav-pills .nav-link {
            color: #64748b;
            font-weight: bold;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 5px;
            text-align: right;
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link:hover {
            background-color: #f8fafc;
            color: #3b82f6;
        }

        .nav-pills .nav-link.active {
            background-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }

        .nav-pills .nav-link i {
            font-size: 18px;
            margin-left: 8px;
            vertical-align: middle;
        }

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .img-preview {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            background: #f8fafc;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: border-color 0.3s;
        }

        .img-preview:hover {
            border-color: #3b82f6;
        }

        .img-preview img {
            max-height: 120px;
            max-width: 100%;
            object-fit: contain;
            border-radius: 4px;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .settings-card {
            background-color: #323a46;
            border: 1px solid #424e5a;
        }

        body[data-layout-color="dark"] .form-label {
            color: #cbd5e1;
        }

        body[data-layout-color="dark"] .nav-pills .nav-link {
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .nav-pills .nav-link:hover {
            background-color: #3b4652;
            color: #60a5fa;
        }

        body[data-layout-color="dark"] .nav-pills .nav-link.active {
            background-color: #3b82f6;
            color: #fff;
        }

        body[data-layout-color="dark"] .img-preview {
            background-color: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .input-group-text {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">
                <i class="mdi mdi-cogs text-primary me-2 fs-4 align-middle"></i> الإعدادات العامة للنظام (General Settings)
            </h4>
        </div>

        {{-- 🌟 التنبيهات والأخطاء --}}
        @if (session('success'))
            <div class="alert alert-success fw-bold rounded-3 shadow-sm border-0">
                <i class="mdi mdi-check-circle fs-5 me-1 align-middle"></i> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger fw-bold rounded-3 shadow-sm border-0">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data"
            class="prevent-double-submit">
            @csrf
            <div class="row">
                {{-- قائمة التابات الجانبية --}}
                <div class="col-md-3 mb-4">
                    <div class="settings-card p-3 sticky-top shadow-sm border-top border-primary border-4"
                        style="top: 80px;">
                        <ul class="nav nav-pills flex-column" id="settingsTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link w-100 active" data-bs-toggle="pill" data-bs-target="#basic-info"
                                    type="button">
                                    <i class="mdi mdi-domain"></i> بيانات الشركة الأساسية
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link w-100" data-bs-toggle="pill" data-bs-target="#branding"
                                    type="button">
                                    <i class="mdi mdi-palette-swatch-outline"></i> الهوية والختم
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link w-100" data-bs-toggle="pill" data-bs-target="#contracts"
                                    type="button">
                                    <i class="mdi mdi-file-document-edit-outline"></i> شروط العقود والتسجيل
                                </button>
                            </li>
                        </ul>
                        <hr class="border-secondary opacity-25">
                        <button type="submit"
                            class="btn btn-primary btn-lg w-100 fw-bold rounded-pill shadow-sm btn-submit">
                            <i class="mdi mdi-content-save-all me-1"></i> حفظ الإعدادات
                        </button>
                    </div>
                </div>

                {{-- محتوى التابات --}}
                <div class="col-md-9">
                    <div class="settings-card p-4 shadow-sm">
                        <div class="tab-content">

                            {{-- 1. بيانات الشركة الأساسية --}}
                            <div class="tab-pane fade show active" id="basic-info">
                                <h5 class="fw-bold mb-4 text-primary border-bottom pb-3"><i
                                        class="mdi mdi-card-account-details-outline me-1"></i> البيانات الرسمية والموقع</h5>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">اسم الشركة (عربي) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="company_name_ar" class="form-control fw-bold shadow-sm"
                                            value="{{ $settings->company_name_ar ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">اسم الشركة (إنجليزي)</label>
                                        <input type="text" name="company_name_en" class="form-control fw-bold shadow-sm"
                                            value="{{ $settings->company_name_en ?? '' }}" dir="ltr">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">رقم السجل التجاري (CR)</label>
                                        <input type="text" name="cr_number" class="form-control font-monospace shadow-sm"
                                            value="{{ $settings->cr_number ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">الرقم الضريبي (VAT Number)</label>
                                        <input type="text" name="tax_number"
                                            class="form-control font-monospace shadow-sm"
                                            value="{{ $settings->tax_number ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">قيمة الضريبة المضافة (%)</label>
                                        <div class="input-group shadow-sm">
                                            <input type="number" name="tax_value"
                                                class="form-control text-center font-monospace fw-bold text-danger"
                                                value="{{ $settings->tax_value ?? 0 }}" step="0.01" min="0">
                                            <span class="input-group-text bg-light text-danger fw-bold">%</span>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <hr class="border-secondary opacity-25 my-1">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">المحافظة</label>
                                        <input type="text" name="governorate_ar" class="form-control shadow-sm"
                                            value="{{ $settings->governorate_ar ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الولاية / المدينة</label>
                                        <input type="text" name="wilayat_ar" class="form-control shadow-sm"
                                            value="{{ $settings->wilayat_ar ?? '' }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">العنوان التفصيلي (عربي)</label>
                                        <input type="text" name="address_ar" class="form-control shadow-sm"
                                            value="{{ $settings->address_ar ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Address (English)</label>
                                        <input type="text" name="address_en" class="form-control shadow-sm"
                                            value="{{ $settings->address_en ?? '' }}" dir="ltr">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">رقم المبنى</label>
                                        <input type="text" name="building_number"
                                            class="form-control font-monospace shadow-sm"
                                            value="{{ $settings->building_number ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">صندوق البريد (P.O Box)</label>
                                        <input type="text" name="po_box"
                                            class="form-control font-monospace shadow-sm"
                                            value="{{ $settings->po_box ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">الرمز البريدي</label>
                                        <input type="text" name="postal_code"
                                            class="form-control font-monospace shadow-sm"
                                            value="{{ $settings->postal_code ?? '' }}">
                                    </div>

                                    <div class="col-md-12">
                                        <hr class="border-secondary opacity-25 my-1">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">البريد الإلكتروني الرسمي</label>
                                        <input type="email" name="email"
                                            class="form-control font-monospace shadow-sm"
                                            value="{{ $settings->email ?? '' }}" dir="ltr">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم هاتف التواصل</label>
                                        <div class="input-group shadow-sm" dir="ltr">
                                            <input type="text" name="phone_code"
                                                class="form-control text-center text-muted"
                                                value="{{ $settings->phone_code ?? '+968' }}" style="max-width: 80px;"
                                                readonly>
                                            <input type="text" name="phone"
                                                class="form-control font-monospace fw-bold"
                                                value="{{ $settings->phone ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. الهوية البصرية والختم --}}
                            <div class="tab-pane fade" id="branding">
                                <h5 class="fw-bold mb-4 text-primary border-bottom pb-3"><i
                                        class="mdi mdi-image-multiple-outline me-1"></i> الصور والترويسات الرسمية للتقارير
                                </h5>
                                <div class="row g-4">
                                    @php
                                        $imageFields = [
                                            'logo' => ['title' => 'لوجو النظام الأساسي', 'icon' => 'mdi-school'],
                                            'seal' => [
                                                'title' => 'الختم الرسمي (يفضل شفاف PNG)',
                                                'icon' => 'mdi-stamp',
                                            ],
                                            'header_image' => [
                                                'title' => 'صورة الهيدر العامة للتقارير',
                                                'icon' => 'mdi-page-layout-header',
                                            ],
                                            'contract_header' => [
                                                'title' => 'ترويسة العقود (Contract Header)',
                                                'icon' => 'mdi-file-document-outline',
                                            ],
                                            'contract_footer' => [
                                                'title' => 'تذييل العقود (Contract Footer)',
                                                'icon' => 'mdi-page-layout-footer',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($imageFields as $field => $info)
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold"><i
                                                    class="mdi {{ $info['icon'] }} text-muted me-1"></i>
                                                {{ $info['title'] }}</label>
                                            <div class="img-preview mb-2 shadow-sm" id="preview_{{ $field }}">
                                                @if (isset($settings) && $settings->$field)
                                                    <img src="{{ Storage::url($settings->$field) }}"
                                                        alt="{{ $info['title'] }}">
                                                @else
                                                    <span class="text-muted opacity-50"><i
                                                            class="mdi mdi-cloud-upload-outline fs-1 d-block"></i>لم يتم
                                                        الرفع</span>
                                                @endif
                                            </div>
                                            <input type="file" name="{{ $field }}"
                                                class="form-control shadow-sm" accept="image/*"
                                                onchange="previewLocalImage(this, 'preview_{{ $field }}')">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- 3. الشروط والعقود (CKEditor) --}}
                            <div class="tab-pane fade" id="contracts">
                                <h5 class="fw-bold mb-4 text-primary border-bottom pb-3"><i
                                        class="mdi mdi-gavel me-1"></i> صياغة العقود والشروط القانونية</h5>
                                <div
                                    class="alert alert-info border-info border-opacity-25 bg-info bg-opacity-10 rounded-3 font-13 fw-bold shadow-sm">
                                    <i class="mdi mdi-information fs-5 align-middle me-1"></i>
                                    استخدم المحرر لتنسيق الخطوط (حجم الخط، الأنماط، المحاذاة) ليظهر العقد بشكل قانوني
                                    واحترافي عند الطباعة أو التوقيع الإلكتروني.
                                </div>

                                <div class="accordion shadow-sm rounded-3" id="contractsAccordion">
                                    {{-- شروط تسجيل الطلاب --}}
                                    <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden shadow-sm">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button fw-bold bg-light text-dark" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#colStudent">
                                                <i class="mdi mdi-account-school-outline me-2 fs-5 text-primary"></i> شروط
                                                وبنود التسجيل (عقد الطالب)
                                            </button>
                                        </h2>
                                        <div id="colStudent" class="accordion-collapse collapse show"
                                            data-bs-parent="#contractsAccordion">
                                            <div class="accordion-body bg-white border-top">
                                                <label class="form-label text-primary"><i
                                                        class="mdi mdi-abjad-arabic me-1"></i> النسخة العربية</label>
                                                <textarea name="student_contract_terms_ar" id="editor_student_ar" class="ckeditor">{{ $settings->student_contract_terms_ar ?? '' }}</textarea>

                                                <label class="form-label text-primary mt-4"><i
                                                        class="mdi mdi-alphabet-latin me-1"></i> النسخة الإنجليزية</label>
                                                <textarea name="student_contract_terms_en" id="editor_student_en" class="ckeditor">{{ $settings->student_contract_terms_en ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- الشروط والأحكام العامة --}}
                                    <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden shadow-sm">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed fw-bold bg-light text-dark"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#colGeneral">
                                                <i class="mdi mdi-shield-check-outline me-2 fs-5 text-success"></i> الشروط
                                                والأحكام العامة للمدرسة (General Terms)
                                            </button>
                                        </h2>
                                        <div id="colGeneral" class="accordion-collapse collapse"
                                            data-bs-parent="#contractsAccordion">
                                            <div class="accordion-body bg-white border-top">
                                                <label class="form-label text-primary"><i
                                                        class="mdi mdi-abjad-arabic me-1"></i> النسخة العربية</label>
                                                <textarea name="general_terms_ar" id="editor_general_ar" class="ckeditor">{{ $settings->general_terms_ar ?? '' }}</textarea>

                                                <label class="form-label text-primary mt-4"><i
                                                        class="mdi mdi-alphabet-latin me-1"></i> النسخة الإنجليزية</label>
                                                <textarea name="general_terms_en" id="editor_general_en" class="ckeditor">{{ $settings->general_terms_en ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- شروط عقد المعلم --}}
                                    <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden shadow-sm">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed fw-bold bg-light text-dark"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#colTeacher">
                                                <i class="mdi mdi-account-tie-outline me-2 fs-5 text-info"></i> شروط عقد
                                                التوظيف للمعلم (HR)
                                            </button>
                                        </h2>
                                        <div id="colTeacher" class="accordion-collapse collapse"
                                            data-bs-parent="#contractsAccordion">
                                            <div class="accordion-body bg-white border-top">
                                                <label class="form-label text-primary"><i
                                                        class="mdi mdi-abjad-arabic me-1"></i> النسخة العربية</label>
                                                <textarea name="teacher_contract_terms_ar" id="editor_teacher_ar" class="ckeditor">{{ $settings->teacher_contract_terms_ar ?? '' }}</textarea>

                                                <label class="form-label text-primary mt-4"><i
                                                        class="mdi mdi-alphabet-latin me-1"></i> النسخة الإنجليزية</label>
                                                <textarea name="teacher_contract_terms_en" id="editor_teacher_en" class="ckeditor">{{ $settings->teacher_contract_terms_en ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- شروط السائق --}}
                                    <div class="accordion-item border-0 rounded-3 overflow-hidden shadow-sm">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed fw-bold bg-light text-dark"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#colDriver">
                                                <i class="mdi mdi-steering me-2 fs-5 text-warning"></i> شروط التوظيف الخاصة
                                                بالسائقين (Logistics)
                                            </button>
                                        </h2>
                                        <div id="colDriver" class="accordion-collapse collapse"
                                            data-bs-parent="#contractsAccordion">
                                            <div class="accordion-body bg-white border-top">
                                                <label class="form-label text-primary"><i
                                                        class="mdi mdi-abjad-arabic me-1"></i> النسخة العربية</label>
                                                <textarea name="driver_contract_terms_ar" id="editor_driver_ar" class="ckeditor">{{ $settings->driver_contract_terms_ar ?? '' }}</textarea>

                                                <label class="form-label text-primary mt-4"><i
                                                        class="mdi mdi-alphabet-latin me-1"></i> النسخة الإنجليزية</label>
                                                <textarea name="driver_contract_terms_en" id="editor_driver_en" class="ckeditor">{{ $settings->driver_contract_terms_en ?? '' }}</textarea>
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
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
        // 🌟 دالة المعاينة الفورية للصور (UX Enhancement) 🌟
        function previewLocalImage(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    let container = $('#' + previewId);
                    // مسح المحتوى القديم ووضع الصورة الجديدة بانيميشن خفيف
                    container.hide().html('<img src="' + e.target.result + '" alt="Preview">').fadeIn(300);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            // تفعيل CKEditor لكل الحقول
            $('.ckeditor').each(function() {
                CKEDITOR.replace($(this).attr('id'), {
                    language: 'ar',
                    height: 250,
                    extraPlugins: 'bidi', // 🔥 السطر السحري لتفعيل أزرار تغيير الاتجاه
                    toolbar: [{
                            name: 'document',
                            items: ['Source', '-', 'Maximize', 'ShowBlocks']
                        },
                        {
                            name: 'clipboard',
                            items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-',
                                'Undo', 'Redo'
                            ]
                        },
                        {
                            name: 'editing',
                            items: ['Find', 'Replace', '-', 'SelectAll']
                        },
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent',
                                '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter',
                                'JustifyRight', 'JustifyBlock', '-', 'BidiRtl',
                                'BidiLtr' // هنا أزرار الاتجاهات
                            ]
                        },
                        '/',
                        {
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript',
                                'Superscript', '-', 'CopyFormatting', 'RemoveFormat'
                            ]
                        },
                        {
                            name: 'styles',
                            items: ['Styles', 'Format', 'Font', 'FontSize']
                        },
                        {
                            name: 'colors',
                            items: ['TextColor', 'BGColor']
                        }
                    ]
                });
            });

            // 🔥 تأمين الفورم ضد الإرسال المزدوج وفقدان بيانات CKEditor 🔥
            $('.prevent-double-submit').on('submit', function() {
                // إجبار CKEditor على مزامنة البيانات مع الـ Textareas المخفية قبل الإرسال
                for (var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement();
                }

                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-2 fs-5 align-middle"></i> جاري حفظ وتحديث الإعدادات...'
                );
            });
        });
    </script>
@endpush
