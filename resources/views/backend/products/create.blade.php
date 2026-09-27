@extends('backend.layouts.master')
@section('title', 'إضافة منتج أو رسوم')

@push('css')
    {{-- 🌟 مكتبة Select2 و SweetAlert 🌟 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        .form-label {
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .section-title {
            font-size: 15px;
            color: #004b87;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 800;
        }

        /* 🌟 Animation لظهور المخزون 🌟 */
        #inventory_fields {
            transition: all 0.4s ease-in-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            background: #f8fafc;
            border-radius: 8px;
            padding: 0 15px;
        }

        #inventory_fields.show {
            max-height: 250px;
            opacity: 1;
            padding: 15px;
            margin-top: 15px;
            border: 1px dashed #cbd5e1;
        }

        /* تعديلات Select2 المتعدد لتناسب Theme Bootstrap 5 */
        .select2-container--bootstrap-5 .select2-selection--multiple {
            min-height: 45px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #e0e7ff;
            color: #004b87;
            border: 1px solid #c7d2fe;
            border-radius: 4px;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 12px;
        }

        /* تنسيق مفتاح التفعيل (Switch) */
        .custom-switch-lg .form-check-input {
            width: 50px;
            height: 25px;
            cursor: pointer;
        }

        .custom-switch-lg .form-check-label {
            cursor: pointer;
            padding-top: 2px;
        }

        /* Dark Mode Adjustments */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
        }

        body[data-layout-color="dark"] .section-title {
            color: #66b0ff;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] #inventory_fields {
            background: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection--multiple {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold mb-1"><i class="mdi mdi-plus-box-multiple text-primary me-1"></i> إضافة بند
                    مالي جديد</h4>
                <p class="text-muted font-13 mb-0">يمكنك إضافة رسوم دراسية أو منتجات لأكثر من فرع في نفس الوقت والتحكم في
                    توفرها.</p>
            </div>
            <a href="{{ route('admin.products.index') }}"
                class="btn btn-light rounded-pill shadow-sm border font-bold px-4">
                <i class="mdi mdi-arrow-left me-1"></i> رجوع للقائمة
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-4 fw-bold">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card card-premium">
                <div class="card-body p-4">
                    {{-- 🔥 تأمين الفورم ضد الـ Double Submit عبر كلاس prevent-double-submit 🔥 --}}
                    <form action="{{ route('admin.products.store') }}" method="POST" id="productForm"
                        class="prevent-double-submit">
                        @csrf

                        <div
                            class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4 flex-wrap gap-2">
                            <h5 class="section-title border-0 mb-0 pb-0"><i class="mdi mdi-information-outline me-1"></i>
                                المعلومات الأساسية والتصنيف</h5>

                            {{-- 🔥 مفتاح التحكم في التوفر 🔥 --}}
                            <div
                                class="form-check form-switch custom-switch-lg d-flex align-items-center bg-light p-2 rounded-3 border shadow-sm">
                                <input class="form-check-input ms-0 me-2 shadow-sm" type="checkbox" name="is_active"
                                    id="is_active_switch" value="1" checked>
                                <label class="form-check-label fw-bold text-success ms-2 font-14" for="is_active_switch"
                                    id="is_active_label">✅ البند متوفر ومتاح</label>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            {{-- 🌟 اختيار الفرع (تحديد متعدد + تحديد الكل) 🌟 --}}
                            <div class="col-md-7">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label text-muted fw-bold mb-0">
                                        <i class="mdi mdi-store-marker-outline text-primary font-18 align-middle me-1"></i>
                                        الفرع التابع له البند <span class="text-danger">*</span>
                                    </label>
                                    <button type="button"
                                        class="btn btn-sm btn-soft-primary rounded-pill py-0 px-3 fw-bold shadow-sm"
                                        id="btn-select-all-branches">تحديد كل الفروع</button>
                                </div>
                                <select name="branch_id[]" id="branch_select" class="form-select select2-multiple"
                                    multiple="multiple" required style="width: 100%;">
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- اختيار النوع --}}
                            <div class="col-md-5">
                                <label class="form-label text-muted fw-bold">نوع البند <span
                                        class="text-danger">*</span></label>
                                <select name="type" id="item_type"
                                    class="form-select border-primary fw-bold text-primary shadow-sm" required
                                    style="height: 45px;">
                                    <option value="service" selected>خدمة / رسوم دراسية (بدون مخزون)</option>
                                    <option value="product">منتج ملموس (له كمية ومخزون)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">كود الصنف (SKU/Barcode)</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="mdi mdi-barcode-scan"></i></span>
                                    <input type="text" name="sku" id="sku_input"
                                        class="form-control bg-light border-0 font-monospace fw-bold"
                                        placeholder="مثال: BOOK-MATH-01">
                                </div>
                                <small class="text-muted font-11"><i class="mdi mdi-information-outline"></i> اتركه فارغاً
                                    للتوليد التلقائي.</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">الاسم بالعربي <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name_ar"
                                    class="form-control form-control-lg bg-light border-0 shadow-sm fw-bold" required
                                    placeholder="مثال: رسوم دراسية صف أول">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">الاسم بالإنجليزي</label>
                                <input type="text" name="name_en"
                                    class="form-control form-control-lg bg-light border-0 shadow-sm fw-bold"
                                    placeholder="Optional" dir="ltr">
                            </div>
                        </div>

                        <h5 class="section-title mt-2"><i class="mdi mdi-cash-multiple me-1"></i> التسعير والضرائب</h5>
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">سعر التكلفة (شراء)</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <input type="number" step="0.001" name="cost_price" id="cost_price"
                                        class="form-control bg-light border-0 text-center font-monospace fw-bold"
                                        value="0">
                                    <span
                                        class="input-group-text bg-light border-0 border-start-0 fw-bold text-muted">OMR</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">سعر البيع (للطلاب) <span
                                        class="text-danger">*</span></label>
                                <div
                                    class="input-group border border-success border-2 shadow-sm rounded-3 overflow-hidden">
                                    <input type="number" step="0.001" name="selling_price" id="selling_price"
                                        class="form-control border-0 text-center font-monospace fw-bolder fs-5 text-success"
                                        required>
                                    <span class="input-group-text bg-success text-white border-0 fw-bold">OMR</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">الضريبة المضافة (%)</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-light border-0 border-end-0"><i
                                            class="mdi mdi-percent"></i></span>
                                    <input type="number" step="0.1" name="tax_percent"
                                        class="form-control border-0 ps-0 bg-light text-center font-monospace fw-bold"
                                        value="0">
                                </div>
                            </div>
                        </div>

                        {{-- 🔥 قسم الوصف 🔥 --}}
                        <h5 class="section-title mt-4"><i class="mdi mdi-card-text-outline me-1"></i> وصف وتفاصيل البند
                        </h5>
                        <div class="row g-4 mb-2">
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold">الوصف (عربي)</label>
                                <textarea name="description_ar" class="form-control bg-light border-0 shadow-sm fw-bold" rows="3"
                                    placeholder="اكتب تفاصيل وشرح البند هنا..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold">الوصف (إنجليزي)</label>
                                <textarea name="description_en" class="form-control bg-light border-0 shadow-sm fw-bold" rows="3"
                                    placeholder="Description in English..." dir="ltr"></textarea>
                            </div>
                        </div>

                        {{-- 🔥 قسم المخزون المطور 🔥 --}}
                        <div id="inventory_fields" class="shadow-sm">
                            <h6 class="fw-bolder text-dark mb-3"><i class="mdi mdi-warehouse me-1 text-info"></i> إعدادات
                                مستودع الفروع</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold">الكمية الافتتاحية في المخزن (لكل فرع تم
                                        تحديده)</label>
                                    <input type="number" name="quantity"
                                        class="form-control bg-white border-0 shadow-sm text-center font-monospace fw-bold fs-5"
                                        value="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold">حد التنبيه (نقص المخزون)</label>
                                    <input type="number" name="alert_quantity"
                                        class="form-control border-warning bg-warning bg-opacity-10 text-center font-monospace fw-bold fs-5 shadow-sm"
                                        value="5">
                                    <small class="text-danger font-11 d-block mt-1"><i
                                            class="mdi mdi-alert-circle-outline"></i> سيظهر تنبيه للإدارة عندما تصل الكمية
                                        لهذا الرقم.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5 pt-3 border-top">
                            <div class="col-12 d-flex justify-content-end gap-2 flex-wrap">
                                <button type="reset"
                                    class="btn btn-light px-4 rounded-pill fw-bold border shadow-sm">إفراغ الحقول</button>
                                <button type="submit"
                                    class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm btn-lg btn-submit">
                                    <i class="mdi mdi-check-decagram me-1"></i> حفظ واعتماد البند
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- 🔥 استدعاء مكتبة Select2 و SweetAlert 🔥 --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // 🔥 تفعيل Select2 للفروع بشكل صحيح 🔥
            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr',
                placeholder: "اضغط لاختيار فرع أو أكثر...",
                allowClear: true,
                width: '100%'
            });

            // زرار تحديد الكل
            $('#btn-select-all-branches').click(function() {
                let allValues = [];
                $('#branch_select option').each(function() {
                    allValues.push($(this).val());
                });
                $('#branch_select').val(allValues).trigger('change');
            });

            // تغيير حالة المفتاح (متوفر / غير متوفر)
            $('#is_active_switch').change(function() {
                let label = $('#is_active_label');
                if ($(this).is(':checked')) {
                    label.text('✅ البند متوفر ومتاح').removeClass('text-danger').addClass('text-success');
                } else {
                    label.text('🚫 البند غير متوفر (موقوف)').removeClass('text-success').addClass(
                        'text-danger');
                }
            });

            const typeSelect = document.getElementById('item_type');
            const inventoryFields = document.getElementById('inventory_fields');
            const skuInput = document.getElementById('sku_input');
            const form = document.getElementById('productForm');

            // أنيميشن لظهور المخزون
            function toggleInventory() {
                if (typeSelect.value === 'product') {
                    inventoryFields.classList.add('show');
                } else {
                    inventoryFields.classList.remove('show');
                }
            }

            typeSelect.addEventListener('change', toggleInventory);
            toggleInventory(); // Initial check

            // توليد SKU والتحقق من التكلفة وإصلاح الـ Double Submit 🔥
            $('#productForm').on('submit', function(e) {

                let selectedBranches = $('#branch_select').val();
                let submitBtn = $(this).find('.btn-submit');

                if (!selectedBranches || selectedBranches.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'تنبيه',
                        text: 'يرجى تحديد فرع واحد على الأقل ليتم تسجيل الصنف فيه!',
                        confirmButtonColor: '#1e2b3c'
                    });
                    return false;
                }

                if (skuInput.value.trim() === '') {
                    skuInput.value = 'ITEM-' + Math.floor(Math.random() * 1000000);
                }

                const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
                const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;

                // منع زر الإرسال افتراضياً لتفادي الـ Double Click
                submitBtn.prop('disabled', true);

                if (sellingPrice < costPrice) {
                    e.preventDefault(); // وقف الإرسال مؤقتاً لطلب التأكيد

                    Swal.fire({
                        title: 'تنبيه: بيع بالخسارة!',
                        text: `سعر البيع (${sellingPrice} OMR) أقل من سعر التكلفة (${costPrice} OMR). هل أنت متأكد من حفظ هذا البند؟`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'نعم، احفظ على أي حال',
                        cancelButtonText: 'تراجع لتعديل السعر'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitBtn.html(
                                '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري الحفظ...');
                            form.submit();
                        } else {
                            // لو تراجع، نرجع نشغل الزرار تاني
                            submitBtn.prop('disabled', false);
                        }
                    });

                    return false; // نوقف السلوك الافتراضي لغايه ماليوزر يقرر
                }

                // لو مفيش خسارة في البيع، نكمل الإرسال مع تغيير حالة الزر
                submitBtn.html('<i class="mdi mdi-loading mdi-spin me-1"></i> جاري الحفظ...');
            });
        });
    </script>
@endpush
