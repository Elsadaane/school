@extends('backend.layouts.master')
@section('title', 'إضافة مورد جديد')
@section('title_page', 'إدارة المشتريات والموردين')

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
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            border-top: 5px solid #5b69bc;
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 800;
            color: #5b69bc;
            border-bottom: 2px dashed #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 25px;
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
            border-color: #5b69bc;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(91, 105, 188, 0.1);
        }

        /* 🌟 Select2 Modern Tweaks 🌟 */
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

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-section-title {
            color: #828fd6;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection,
        body[data-layout-color="dark"] .select2-dropdown {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="row mb-4 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold mb-1 text-dark"><i
                            class="mdi mdi-domain-plus text-primary me-1 fs-3 align-middle"></i> تسجيل مورد / تاجر جديد</h4>
                    <p class="text-muted mb-0 font-14">إضافة بيانات الشركات والموردين لربطها بفواتير المشتريات والمخازن.</p>
                </div>
                <a href="{{ route('admin.suppliers.index') }}"
                    class="btn btn-light rounded-pill shadow-sm border fw-bold px-4">
                    <i class="mdi mdi-arrow-left me-1 align-middle"></i> العودة لقائمة الموردين
                </a>
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

        <div class="card card-premium shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.suppliers.store') }}" method="POST" id="supplierForm"
                    class="prevent-double-submit" novalidate>
                    @csrf

                    <h5 class="form-section-title"><i class="mdi mdi-card-account-details-outline fs-4 align-middle"></i>
                        البيانات الأساسية ومعلومات التواصل</h5>
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-bold font-13">الفرع التابع له المورد <span
                                    class="text-danger">*</span></label>
                            <select name="branch_id"
                                class="form-select select2-init shadow-sm @error('branch_id') input-error @enderror"
                                required>
                                <option value="" disabled selected>-- اختر الفرع --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted fw-bold font-13">اسم الشركة / المتجر <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="company_name"
                                class="form-control shadow-sm fw-bold @error('company_name') input-error @enderror"
                                value="{{ old('company_name') }}" placeholder="مثال: شركة مسقط لتوريد الكتب" required>
                            @error('company_name')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted fw-bold font-13">اسم المسؤول (المندوب للتواصل)</label>
                            <input type="text" name="contact_person"
                                class="form-control shadow-sm @error('contact_person') input-error @enderror"
                                value="{{ old('contact_person') }}" placeholder="اسم الشخص المسؤول">
                            @error('contact_person')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted fw-bold font-13">رقم الهاتف / الواتساب</label>
                            <input type="text" name="phone"
                                class="form-control shadow-sm font-monospace @error('phone') input-error @enderror"
                                value="{{ old('phone') }}" dir="ltr" placeholder="+968 ...">
                            @error('phone')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted fw-bold font-13">البريد الإلكتروني الرسمي</label>
                            <input type="email" name="email"
                                class="form-control shadow-sm font-monospace @error('email') input-error @enderror"
                                value="{{ old('email') }}" dir="ltr" placeholder="supplier@example.com">
                            @error('email')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted fw-bold font-13">الرقم الضريبي (VAT Number)</label>
                            <input type="text" name="tax_number"
                                class="form-control shadow-sm font-monospace fw-bold @error('tax_number') input-error @enderror"
                                value="{{ old('tax_number') }}" dir="ltr">
                            @error('tax_number')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h5 class="form-section-title"><i class="mdi mdi-cash-register fs-4 align-middle"></i> البيانات المالية
                        والعنوان السكني</h5>
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-bold font-13">الرصيد الافتتاحي (مستحق للمورد إن
                                وجد)</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-light text-primary border-end-0 fw-bold">OMR</span>
                                <input type="number" step="0.001" name="opening_balance"
                                    class="form-control border-start-0 text-dark fw-bolder font-monospace @error('opening_balance') input-error @enderror"
                                    value="{{ old('opening_balance', 0) }}">
                            </div>
                            @error('opening_balance')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label text-muted fw-bold font-13">العنوان التفصيلي والموقع</label>
                            <input type="text" name="address"
                                class="form-control shadow-sm @error('address') input-error @enderror"
                                value="{{ old('address') }}" placeholder="المدينة، الحي، الشارع، ورقم المبنى...">
                            @error('address')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mt-4">
                            <div class="form-check form-switch d-flex align-items-center gap-2 bg-light p-3 rounded-3 border shadow-sm"
                                style="max-width: max-content;">
                                <input class="form-check-input m-0 shadow-sm" type="checkbox" name="is_active"
                                    id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                    style="width: 2.5em; height: 1.25em;">
                                <label class="form-check-label fw-bold text-dark cursor-pointer mt-1" for="isActive">حساب
                                    المورد نشط ومفعل (يظهر في فواتير المشتريات)</label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end border-top pt-4 mt-2">
                        <button type="submit"
                            class="btn btn-primary btn-lg rounded-pill px-5 font-bold shadow-sm btn-submit">
                            <i class="mdi mdi-content-save-all me-1 fs-5 align-middle"></i> حفظ بيانات المورد
                        </button>
                    </div>
                </form>
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

            // تفعيل Select2
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? "rtl" : "ltr"
            });

            // تأمين الفورم ضد الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-2 fs-5 align-middle"></i> جاري حفظ السجل...');
            });
        });
    </script>
@endpush
