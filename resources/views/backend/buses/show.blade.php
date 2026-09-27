@extends('backend.layouts.master')
@section('title', 'تفاصيل الحافلة | ' . $bus->bus_number)
@section('title_page', 'إدارة الحركة والنقل')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* 🌟 Profile Header (Bus Theme) 🌟 */
        .profile-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 16px;
            padding: 35px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.2);
            border-bottom: 5px solid #b45309;
        }

        .profile-header .bg-icon {
            position: absolute;
            top: -20px;
            left: -10px;
            font-size: 180px;
            opacity: 0.15;
            transform: rotate(-15deg);
            pointer-events: none;
        }

        /* 🌟 Premium Form UI 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px dashed #e2e8f0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .read-only-box {
            min-height: 42px;
            display: flex;
            align-items: center;
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .signature-box {
            border: 2px dashed #f59e0b;
            border-radius: 12px;
            background-color: #fffbeb;
            padding: 20px;
            text-align: center;
            max-width: 100%;
            margin: 0 auto;
        }

        .contract-text-box {
            max-height: 200px;
            overflow-y: auto;
            font-size: 14px;
            line-height: 1.8;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .contract-text-box::-webkit-scrollbar {
            width: 6px;
        }

        .contract-text-box::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }

        .contract-text-box::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }

        .contract-text-box::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* 🌟 Tabs Styling for Passengers 🌟 */
        .custom-pills .nav-link {
            color: #64748b;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }

        .custom-pills .nav-link.active {
            background-color: #3b82f6;
            color: #fff;
            border-color: #3b82f6;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }

        .custom-pills .nav-link.active .badge {
            color: #3b82f6 !important;
        }

        /* 🌟 تعديلات الثيم الدارك 🌟 */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-section-title {
            color: #f8fafc;
            border-bottom-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light,
        body[data-layout-color="dark"] .contract-text-box,
        body[data-layout-color="dark"] .custom-pills .nav-link {
            background-color: #3b4652 !important;
            color: #ced4da !important;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .read-only-box {
            color: #fff;
        }

        body[data-layout-color="dark"] .signature-box {
            background-color: #3b4652;
            border-color: #f59e0b;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="page-title font-bold mb-0 text-dark"><i
                        class="bx bx-bus text-warning me-1 fs-3 align-middle"></i> ملف الحافلة والسائق</h4>
                <p class="text-muted font-13 mt-1 mb-0">عرض تفاصيل المركبة، خط السير، المشرف، والعقد الموثق بشكل شامل.</p>
            </div>
            <div class="d-flex gap-2">
                @can('manage_buses')
                    <a href="{{ route('admin.buses.print_contract', $bus->id) }}" target="_blank"
                        class="btn btn-outline-dark bg-white border-2 rounded-pill shadow-sm font-bold">
                        <i class="bx bx-printer me-1 align-middle fs-5"></i> طباعة العقد
                    </a>
                    <a href="{{ route('admin.buses.edit', $bus->id) }}"
                        class="btn btn-warning text-dark rounded-pill shadow-sm font-bold px-4">
                        <i class="bx bx-edit me-1 align-middle fs-5"></i> تعديل البيانات
                    </a>
                @endcan
                <a href="{{ route('admin.buses.index') }}"
                    class="btn btn-light rounded-pill shadow-sm font-bold border-2 px-4">
                    <i class="bx bx-arrow-back align-middle me-1"></i> رجوع
                </a>
            </div>
        </div>
    </div>

    {{-- 🌟 الهيدر الفخم 🌟 --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="profile-header">
                <i class='bx bx-bus bg-icon'></i>
                <div class="row align-items-center position-relative z-index-1">
                    <div class="col-md-8">
                        <h2 class="text-white font-bold mb-2 font-monospace">رقم الحافلة: {{ $bus->bus_number }}</h2>
                        <h5 class="text-white-50 mb-3 fw-bold"><i class="bx bx-map-pin me-1 align-middle"></i> خط السير:
                            {{ $bus->transportRoute->name ?? 'غير محدد' }}</h5>

                        {{-- 🔥 تم التحديث: إضافة تفاصيل مقاعد الصباح والمساء 🔥 --}}
                        <div class="d-flex gap-2 flex-wrap mt-3">
                            <span class="badge bg-white text-dark rounded-pill px-3 py-2 font-13 shadow-sm"
                                title="السعة الإجمالية">
                                <i class="bx bx-chair text-warning me-1 align-middle fs-6"></i> السعة:
                                {{ $bus->capacity ?? '0' }} راكب
                            </span>

                            <span
                                class="badge {{ $bus->available_morning_seats > 0 ? 'bg-success' : 'bg-danger' }} text-white rounded-pill px-3 py-2 font-13 shadow-sm">
                                <i class="bx bx-sun me-1 align-middle fs-6"></i> متاح صباحاً:
                                {{ $bus->available_morning_seats }}
                            </span>

                            <span
                                class="badge {{ $bus->available_afternoon_seats > 0 ? 'bg-info' : 'bg-danger' }} text-white rounded-pill px-3 py-2 font-13 shadow-sm">
                                <i class="bx bx-moon me-1 align-middle fs-6"></i> متاح مساءً:
                                {{ $bus->available_afternoon_seats }}
                            </span>

                            <span class="badge bg-white text-dark rounded-pill px-3 py-2 font-13 shadow-sm">
                                <i class="bx bx-key text-warning me-1 align-middle fs-6"></i>
                                {{ $bus->ownership_type === 'owned' ? 'مملوكة للمدرسة' : 'مستأجرة' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-4 mt-md-0">
                        <div
                            class="bg-white bg-opacity-25 rounded-4 p-3 d-inline-block text-start border border-white border-opacity-25 shadow-sm">
                            <p class="text-white-50 mb-1 font-13 fw-bold">
                                @if ($bus->ownership_type === 'owned')
                                    حالة المركبة
                                @else
                                    إيجار الحافلة الشهري
                                @endif
                            </p>
                            <h3 class="text-white font-bold mb-0 font-monospace" dir="ltr">
                                @if ($bus->ownership_type === 'owned')
                                    <span class="badge bg-white text-dark fs-6">مملوكة بالكامل</span>
                                @else
                                    {{ number_format($bus->monthly_rent ?? 0, 3) }} <small class="font-14">OMR</small>
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 🌟 البيانات التفصيلية 🌟 --}}
    <div class="row g-4">
        {{-- العمود الأيمن: بيانات الحافلة والسائق والتسعيرة --}}
        <div class="col-xl-8">

            {{-- كارت بيانات الحافلة --}}
            <div class="card card-premium shadow-sm border-top border-primary border-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="form-section-title text-primary"><i class="bx bx-info-circle fs-4 text-primary"></i> بيانات
                        المركبة الأساسية ووثائقها</h5>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted font-13 fw-bold">الفرع</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box">
                                {{ $bus->branch->name_ar ?? 'غير محدد' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted font-13 fw-bold">السنة الدراسية</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box text-primary">
                                {{ $bus->academicYear->name ?? '---' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted font-13 fw-bold">رقم الحافلة (اللوحة)</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box font-monospace">
                                {{ $bus->bus_number }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted font-13 fw-bold">نوع الحافلة</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box">
                                {{ $bus->bus_type ?? '---' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted font-13 fw-bold">موديل الباص</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box font-monospace"
                                dir="ltr">{{ $bus->bus_model ?? '---' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted font-13 fw-bold">عدد الركاب المسموح (السعة)</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box">{{ $bus->capacity ?? '0' }}
                                راكب</div>
                        </div>

                        {{-- 🔄 تم التحديث: خط السير من العلاقة --}}
                        <div class="col-md-6">
                            <label class="form-label text-muted font-13 fw-bold">خط سير الحافلة</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box">
                                <i class="bx bx-map-pin text-info me-1"></i> {{ $bus->transportRoute->name ?? '---' }}
                            </div>
                        </div>

                        {{-- 🆕 تواريخ التأمين والفحص --}}
                        <div class="col-md-3">
                            <label class="form-label text-muted font-13 fw-bold">تاريخ انتهاء التأمين</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box font-monospace text-danger">
                                {{ $bus->insurance_expiry_date ? \Carbon\Carbon::parse($bus->insurance_expiry_date)->format('Y-m-d') : '---' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted font-13 fw-bold">تاريخ انتهاء الاستمارة/الفحص</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box font-monospace text-danger">
                                {{ $bus->registration_expiry_date ? \Carbon\Carbon::parse($bus->registration_expiry_date)->format('Y-m-d') : '---' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- كارت بيانات السائق والمشرف --}}
            <div class="card card-premium shadow-sm border-top border-success border-4 mb-4">
                <div class="card-body p-4">
                    <div
                        class="d-flex justify-content-between align-items-center mb-4 border-bottom border-success border-opacity-25 pb-3">
                        <h5 class="text-success fw-bold m-0"><i class="bx bx-steering me-2 fs-4 align-middle"></i> بيانات
                            قائد الحافلة</h5>
                        @if ($bus->driver_id)
                            <span
                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 font-12 rounded-pill shadow-sm">
                                <i class="bx bx-check-shield me-1"></i> مسجل بشؤون الموظفين
                            </span>
                        @else
                            <span
                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-1 font-12 rounded-pill shadow-sm">
                                <i class="bx bx-user-x me-1"></i> سائق خارجي
                            </span>
                        @endif
                    </div>

                    @php
                        $driverName = $bus->driver_id
                            ? $bus->driver->full_name_ar ?? '---'
                            : $bus->driver_name ?? '---';
                        $driverNID = $bus->driver_id
                            ? $bus->driver->national_id ?? '---'
                            : $bus->driver_national_id ?? '---';
                        $driverPhone = $bus->driver_id ? $bus->driver->phone ?? '---' : $bus->driver_phone ?? '---';
                        $driverCode = $bus->driver_id ? $bus->driver->employee_code ?? '---' : 'غير مرتبط';
                    @endphp

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted font-13 fw-bold">الاسم الرباعي</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box text-dark">
                                {{ $driverName }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted font-13 fw-bold">الرقم الوظيفي</label>
                            <div
                                class="form-control shadow-sm border-0 bg-light read-only-box font-monospace text-primary">
                                {{ $driverCode }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted font-13 fw-bold">رقم الهوية / الإقامة</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box font-monospace"
                                dir="ltr">{{ $driverNID }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted font-13 fw-bold">رقم الهاتف للتواصل</label>
                            <div class="form-control shadow-sm border-0 bg-light read-only-box font-monospace"
                                dir="ltr">
                                <a href="tel:{{ $driverPhone }}" class="text-decoration-none text-success fw-bold"><i
                                        class="bx bx-phone me-1"></i>{{ $driverPhone }}</a>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-muted font-13 fw-bold">ملاحظات إضافية</label>
                            <div class="form-control shadow-sm border-0 bg-light p-2" style="min-height: 42px;">
                                {{ $bus->notes ?? 'لا توجد ملاحظات إضافية.' }}</div>
                        </div>
                    </div>

                    {{-- 🆕 قسم المشرف --}}
                    @if ($bus->supervisor_id && $bus->supervisor)
                        <hr class="border-secondary opacity-25 my-4">
                        <h6 class="text-info fw-bold mb-3"><i class="bx bx-user-check me-2 fs-5 align-middle"></i> بيانات
                            مشرف الحافلة</h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted font-13 fw-bold">اسم المشرف</label>
                                <div class="form-control shadow-sm border-0 bg-light read-only-box text-dark">
                                    {{ $bus->supervisor->full_name_ar ?? '---' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted font-13 fw-bold">رقم الهاتف</label>
                                <div class="form-control shadow-sm border-0 bg-light read-only-box font-monospace"
                                    dir="ltr">
                                    <a href="tel:{{ $bus->supervisor->phone }}"
                                        class="text-decoration-none text-info fw-bold"><i
                                            class="bx bx-phone me-1"></i>{{ $bus->supervisor->phone ?? '---' }}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- كارت تسعيرة الطلاب --}}
            <div class="card card-premium shadow-sm border-top border-info border-4">
                <div class="card-body p-4">
                    <h5 class="form-section-title text-info"><i class="bx bx-money fs-4 text-info"></i> التسعيرة المعتمدة
                        للطلاب على هذا الخط</h5>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-white border border-success border-opacity-50 rounded-3 shadow-sm h-100">
                                <label class="form-label text-success font-13 fw-bold d-block"><i
                                        class="bx bx-sort-alt-2 align-middle me-1"></i> تسعيرة الاشتراك (ذهاب وعودة)
                                    شهرياً</label>
                                <div
                                    class="form-control border-0 bg-success bg-opacity-10 read-only-box text-success font-monospace fs-5 text-center mt-2">
                                    {{ number_format($bus->transportRoute->monthly_fare_both ?? 0, 3) }} OMR
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white border border-info border-opacity-50 rounded-3 shadow-sm h-100">
                                <label class="form-label text-info font-13 fw-bold d-block"><i
                                        class="bx bx-right-arrow-alt align-middle me-1"></i> تسعيرة الاشتراك (اتجاه واحد)
                                    شهرياً</label>
                                <div
                                    class="form-control border-0 bg-info bg-opacity-10 read-only-box text-info font-monospace fs-5 text-center mt-2">
                                    {{ number_format($bus->transportRoute->monthly_fare_single ?? 0, 3) }} OMR
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- العمود الأيسر: الشروط والتوقيع --}}
        <div class="col-xl-4">
            <div class="card card-premium shadow-sm h-100 border-top border-warning border-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="form-section-title text-warning"><i class="bx bx-file-blank fs-4 text-warning"></i> بنود
                        العقد والتوثيق</h5>

                    <div class="mb-4">
                        <label class="form-label text-muted font-13 fw-bold">شروط عقد تشغيل الحافلة (عربي)</label>
                        <div class="contract-text-box shadow-sm">
                            @if ($bus->contract_terms_ar)
                                {!! $bus->contract_terms_ar !!}
                            @else
                                <span class="text-muted"><i class="bx bx-info-circle"></i> لم يتم إدراج شروط إضافية. يعتمد
                                    الشروط العامة للمؤسسة.</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted font-13 fw-bold">Contract Terms (English)</label>
                        <div class="contract-text-box shadow-sm" dir="ltr" style="text-align: left;">
                            @if ($bus->contract_terms_en)
                                {!! $bus->contract_terms_en !!}
                            @else
                                <span class="text-muted"><i class="bx bx-info-circle"></i> No additional terms
                                    provided.</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-auto">
                        <hr class="border-secondary opacity-25 mb-4">
                        <label class="form-label text-danger font-bold d-block text-center mb-3"><i
                                class="bx bx-pen align-middle"></i> اعتماد وتوقيع السائق</label>
                        <div class="signature-box shadow-sm">
                            @if ($bus->signature_data)
                                <img src="{{ $bus->signature_data }}" alt="توقيع السائق" class="img-fluid"
                                    style="max-height: 120px;">
                                <div class="mt-3 p-2 bg-white rounded border border-success border-opacity-25">
                                    <p class="text-success mb-0 font-bold font-13"><i
                                            class="bx bx-check-shield align-middle fs-5"></i> تم توثيق الإمضاء بنجاح</p>
                                </div>
                            @else
                                <div class="py-4">
                                    <i class="bx bx-pen text-muted font-40 mb-2 d-block opacity-25"
                                        style="font-size: 50px;"></i>
                                    <h6 class="text-danger font-13 fw-bold">لا يوجد توقيع مسجل</h6>
                                    <p class="font-12 text-muted mb-0">يرجى تعديل البيانات لإضافة التوقيع.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- 🔥 الركاب المشتركين في الحافلة (ذهاب / إياب) 🔥 --}}
    <div class="row mt-2 mb-5">
        <div class="col-12">
            <div class="card card-premium shadow-sm border-top border-dark border-4">
                <div class="card-body p-4">
                    <h5 class="form-section-title text-dark"><i class="bx bx-group fs-4 text-dark"></i> قائمة الركاب
                        (الطلاب المشتركين)</h5>

                    {{-- Tabs Navigation --}}
                    <ul class="nav nav-pills custom-pills mb-4" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active font-bold px-4 rounded-pill" id="morning-tab"
                                data-bs-toggle="pill" data-bs-target="#morning-students" type="button" role="tab">
                                <i class="bx bx-sun me-1 align-middle"></i> رحلة الذهاب (الصباح)
                                <span
                                    class="badge bg-white text-dark ms-2 shadow-sm">{{ $bus->morningContracts->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item ms-2" role="presentation">
                            <button class="nav-link font-bold px-4 rounded-pill" id="afternoon-tab" data-bs-toggle="pill"
                                data-bs-target="#afternoon-students" type="button" role="tab">
                                <i class="bx bx-moon me-1 align-middle"></i> رحلة العودة (المساء)
                                <span
                                    class="badge bg-white text-dark ms-2 shadow-sm">{{ $bus->afternoonContracts->count() }}</span>
                            </button>
                        </li>
                    </ul>

                    {{-- Tabs Content --}}
                    <div class="tab-content" id="pills-tabContent">

                        {{-- 1. طلاب الصباح --}}
                        <div class="tab-pane fade show active" id="morning-students" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-center mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th class="text-start">اسم الطالب</th>
                                            <th>الصف والشعبة</th>
                                            <th>ولي الأمر ورقم التواصل</th>
                                            <th>حالة العقد</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bus->morningContracts as $index => $contract)
                                            <tr>
                                                <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                                <td class="text-start fw-bold text-primary">
                                                    <i class="bx bx-user-circle me-1 text-muted"></i>
                                                    {{ $contract->student->first_name ?? '---' }}
                                                    {{ $contract->student->second_name ?? '' }}
                                                    {{ $contract->student->tribe ?? '' }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-dark border border-secondary border-opacity-25 px-2 py-1">
                                                        {{ $contract->student->grade->name_ar ?? '---' }} -
                                                        {{ $contract->student->section->name_ar ?? '---' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="font-13 fw-bold">
                                                        {{ $contract->student->guardian->name_ar ?? '---' }}</div>
                                                    <a href="tel:{{ $contract->student->guardian->phone ?? '' }}"
                                                        class="text-success font-monospace font-12 text-decoration-none">
                                                        <i
                                                            class="bx bx-phone me-1"></i>{{ $contract->student->guardian->phone ?? '---' }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($contract->contract_status == 'active')
                                                        <span class="badge bg-success rounded-pill">نشط</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark rounded-pill">مسودة /
                                                            معلق</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">لا يوجد طلاب مسجلين
                                                    في رحلة الذهاب حتى الآن.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 2. طلاب المساء --}}
                        <div class="tab-pane fade" id="afternoon-students" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-center mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th class="text-start">اسم الطالب</th>
                                            <th>الصف والشعبة</th>
                                            <th>ولي الأمر ورقم التواصل</th>
                                            <th>حالة العقد</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bus->afternoonContracts as $index => $contract)
                                            <tr>
                                                <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                                <td class="text-start fw-bold text-primary">
                                                    <i class="bx bx-user-circle me-1 text-muted"></i>
                                                    {{ $contract->student->first_name ?? '---' }}
                                                    {{ $contract->student->second_name ?? '' }}
                                                    {{ $contract->student->tribe ?? '' }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-dark border border-secondary border-opacity-25 px-2 py-1">
                                                        {{ $contract->student->grade->name_ar ?? '---' }} -
                                                        {{ $contract->student->section->name_ar ?? '---' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="font-13 fw-bold">
                                                        {{ $contract->student->guardian->name_ar ?? '---' }}</div>
                                                    <a href="tel:{{ $contract->student->guardian->phone ?? '' }}"
                                                        class="text-info font-monospace font-12 text-decoration-none">
                                                        <i
                                                            class="bx bx-phone me-1"></i>{{ $contract->student->guardian->phone ?? '---' }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($contract->contract_status == 'active')
                                                        <span class="badge bg-success rounded-pill">نشط</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark rounded-pill">مسودة /
                                                            معلق</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">لا يوجد طلاب مسجلين
                                                    في رحلة العودة حتى الآن.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
