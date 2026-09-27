@extends('backend.layouts.master')
@section('title', 'تعديل بيانات المورد | ' . $supplier->company_name)
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
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.03);
            background-color: #fff;
            border-top: 5px solid #f59e0b;
            /* لون برتقالي يعبر عن التعديل */
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

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 12px 15px;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #fff;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        /* Select2 Styling */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 48px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background-color: #f8fafc;
            padding: 4px;
            font-weight: 600;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            background-color: #fff;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        /* Custom Switch */
        .form-check-input {
            width: 3rem;
            height: 1.5rem;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #10b981;
            border-color: #10b981;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
            border-top-color: #f59e0b;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .form-section-title {
            color: #f8fafc;
            border-bottom-color: #424e5a;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .text-muted {
            color: #adb5bd !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 رأس الصفحة 🌟 --}}
        <div class="row mb-4 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold text-dark mb-1">
                        <i class="mdi mdi-square-edit-outline text-warning me-1 fs-3 align-middle"></i> تعديل بيانات المورد
                    </h4>
                    <p class="text-muted mb-0 font-14">تحديث المعلومات الأساسية وطرق التواصل للشركة الموردة.</p>
                </div>
                <a href="{{ route('admin.suppliers.index') }}"
                    class="btn btn-light rounded-pill border fw-bold shadow-sm px-4 py-2">
                    <i class="mdi mdi-arrow-left me-1 align-middle"></i> العودة للقائمة
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm fw-bold border-0 rounded-3 mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="mdi mdi-alert-circle me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🌟 فورم التعديل 🌟 --}}
        <div class="card card-premium shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST"
                    class="prevent-double-submit">
                    @csrf
                    @method('PUT')

                    {{-- القسم الأول: البيانات الأساسية --}}
                    <h5 class="form-section-title text-warning">
                        <i class="mdi mdi-domain fs-4"></i> البيانات الأساسية للشركة
                    </h5>
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <label class="form-label">الفرع التابع له <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select select2-init" required>
                                <option value="" disabled>-- اختر الفرع --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ $supplier->branch_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">اسم الشركة / التاجر <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control"
                                placeholder="أدخل الاسم الرسمي للمورد"
                                value="{{ old('company_name', $supplier->company_name) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">الرقم الضريبي (إن وجد)</label>
                            <input type="text" name="tax_number" class="form-control font-monospace"
                                placeholder="مثال: 1002938475" value="{{ old('tax_number', $supplier->tax_number) }}">
                        </div>
                    </div>

                    {{-- القسم الثاني: معلومات التواصل --}}
                    <h5 class="form-section-title text-info">
                        <i class="mdi mdi-card-account-phone-outline fs-4"></i> معلومات مسؤول التواصل
                    </h5>
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <label class="form-label">اسم المندوب / المسؤول</label>
                            <input type="text" name="contact_person" class="form-control"
                                placeholder="اسم الشخص المسؤول عن التوريد"
                                value="{{ old('contact_person', $supplier->contact_person) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">رقم الهاتف / الواتساب</label>
                            <input type="text" name="phone" class="form-control font-monospace text-start"
                                dir="ltr" placeholder="+968 XXXXXXXX" value="{{ old('phone', $supplier->phone) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control font-monospace text-start"
                                dir="ltr" placeholder="supplier@example.com"
                                value="{{ old('email', $supplier->email) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">العنوان التفصيلي للشركة</label>
                            <textarea name="address" class="form-control" rows="3" placeholder="أدخل مقر الشركة والمخازن...">{{ old('address', $supplier->address) }}</textarea>
                        </div>
                    </div>

                    {{-- القسم الثالث: البيانات المالية والحالة --}}
                    <h5 class="form-section-title text-success">
                        <i class="mdi mdi-shield-check-outline fs-4"></i> الإعدادات المالية وحالة الحساب
                    </h5>
                    <div class="row g-4 mb-4 align-items-center bg-light p-3 rounded-4 border">
                        <div class="col-md-4 border-end border-2">
                            <label class="form-label text-muted">الرصيد الافتتاحي (لا يمكن تعديله)</label>
                            <div class="input-group shadow-sm">
                                <input type="text" class="form-control font-monospace fw-bold bg-white text-dark"
                                    value="{{ number_format($supplier->opening_balance, 3) }}" readonly disabled>
                                <span class="input-group-text bg-light text-muted fw-bold">OMR</span>
                            </div>
                            <small class="text-danger font-11 mt-1 d-block"><i class="mdi mdi-information-outline"></i>
                                الرصيد الافتتاحي يتم تسجيله مرة واحدة عند الإنشاء فقط.</small>
                        </div>

                        <div class="col-md-8 ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input shadow-sm" type="checkbox" role="switch"
                                        id="is_active" name="is_active" {{ $supplier->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold ms-2 mt-1" for="is_active">تفعيل حساب
                                        المورد</label>
                                </div>
                                <div class="text-muted font-12 ms-3 border-start ps-3">
                                    عند إيقاف الحساب، لن يظهر هذا المورد في شاشات إنشاء فواتير المشتريات الجديدة.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- أزرار الحفظ --}}
                    <div class="d-flex justify-content-end mt-5 border-top pt-4">
                        <button type="submit"
                            class="btn btn-warning btn-lg px-5 py-3 rounded-pill fw-bold shadow-lg btn-submit text-dark">
                            <i class="mdi mdi-content-save-edit-outline me-2 fs-4 align-middle"></i> حفظ التعديلات
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
            // تفعيل Select2
            let isRtl = "{{ app()->getLocale() }}" === 'ar';
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? "rtl" : "ltr",
                width: '100%'
            });

            // تأمين زر الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-2 fs-4 align-middle"></i> جاري الحفظ...');
            });
        });
    </script>
@endpush
