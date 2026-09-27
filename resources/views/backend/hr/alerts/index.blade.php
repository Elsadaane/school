@extends('backend.layouts.master')
@section('title', 'رادار التنبيهات | Alert Radar')
@section('title_page', 'مركز متابعة الوثائق والعقود (HR Alert Center)')

@push('css')
    <style>
        .page-wrapper {
            background-color: #f4f6f9;
            padding: 25px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .alert-card {
            border-radius: 15px;
            border: none;
            transition: all 0.3s ease;
            cursor: default;
        }

        .alert-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .table-custom thead th {
            background-color: #1e2b3c;
            color: white;
            border: none;
            padding: 15px;
            white-space: nowrap;
        }

        .expired-row {
            background-color: rgba(231, 76, 60, 0.04);
        }

        .near-expiry {
            color: #e67e22;
            font-weight: bold;
        }

        .days-badge {
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 800;
            white-space: nowrap;
        }

        .emp-link {
            text-decoration: none;
            transition: 0.2s;
        }

        .emp-link:hover {
            color: #e74c3c !important;
        }

        /* تحسين التمرير للقوائم */
        .custom-scroll {
            max-height: 500px;
            overflow-y: auto;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 الإحصائيات العلوية (4 كروت) 🌟 --}}
        <div class="row g-4 mb-4 text-center">
            <div class="col-xl-3 col-md-6">
                <div class="card alert-card shadow-sm border-start border-danger border-5 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <h1 class="fw-bolder text-danger mb-0">{{ $expiredDocuments->count() }}</h1>
                        <h6 class="fw-bold text-muted mt-2">وثائق منتهية (Expired)</h6>
                        <small class="text-danger fw-bold"><i class="mdi mdi-alert-octagon"></i> تحتاج تجديد فوري</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card alert-card shadow-sm border-start border-warning border-5 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <h1 class="fw-bolder mb-0" style="color: #d97706 !important;">{{ $expiringDocuments->count() }}</h1>
                        <h6 class="fw-bold text-muted mt-2">وثائق تنتهي قريباً</h6>
                        <small class="fw-bold" style="color: #d97706 !important;"><i
                                class="mdi mdi-clock-alert-outline"></i> خلال 30 يوم</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card alert-card shadow-sm border-start border-dark border-5 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <h1 class="fw-bolder text-dark mb-0">{{ $expiredContracts->count() }}</h1>
                        <h6 class="fw-bold text-muted mt-2">عقود عمل منتهية</h6>
                        <small class="text-dark fw-bold"><i class="mdi mdi-file-cancel-outline"></i> خطر قانوني</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card alert-card shadow-sm border-start border-primary border-5 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <h1 class="fw-bolder text-primary mb-0">{{ $expiringContracts->count() }}</h1>
                        <h6 class="fw-bold text-muted mt-2">عقود تنتهي قريباً</h6>
                        <small class="text-primary fw-bold"><i class="mdi mdi-file-clock-outline"></i> مراجعة
                            للتجديد</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- 1. جدول الوثائق (إقامات / جوازات / بطاقات صحية) --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                    <div
                        class="card-header bg-white pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="mdi mdi-passport text-danger me-2"></i> متابعة الوثائق الرسمية للموظفين
                        </h5>
                    </div>
                    <div class="card-body p-0 custom-scroll">
                        <div class="table-responsive">
                            {{-- 🔥 إضافة table-nowrap لمنع تداخل النصوص 🔥 --}}
                            <table class="table table-custom align-middle text-center mb-0 table-nowrap">
                                <thead>
                                    <tr>
                                        <th class="text-start ps-4">اسم الموظف</th>
                                        <th>نوع الوثيقة</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th>الحالة / المتبقي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- 🔥 وثائق منتهية 🔥 --}}
                                    @foreach ($expiredDocuments as $doc)
                                        <tr class="expired-row">
                                            <td class="text-start ps-4 fw-bold text-dark">
                                                {{ $doc->employee->full_name_ar ?? 'موظف محذوف' }}
                                                @can('view_employees')
                                                    @if ($doc->employee)
                                                        <a href="{{ route('admin.hr.employees.show', $doc->employee->id) }}"
                                                            class="emp-link text-primary ms-1" title="فتح ملف الموظف">
                                                            <i class="mdi mdi-open-in-new"></i>
                                                        </a>
                                                    @endif
                                                @endcan
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2">
                                                    {{ $doc->document_type }}
                                                </span>
                                            </td>
                                            <td class="text-danger fw-bold font-monospace">
                                                {{ $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->format('Y-m-d') : '---' }}
                                            </td>
                                            <td>
                                                @php $daysPassed = $doc->expiry_date ? now()->diffInDays(\Carbon\Carbon::parse($doc->expiry_date)) : 0; @endphp
                                                <span class="days-badge bg-danger text-white">منتهية منذ {{ $daysPassed }}
                                                    يوم</span>
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- ⚠️ وثائق تنتهي قريباً ⚠️ --}}
                                    @foreach ($expiringDocuments as $doc)
                                        @php $daysLeft = $doc->expiry_date ? now()->diffInDays(\Carbon\Carbon::parse($doc->expiry_date), false) : 0; @endphp
                                        <tr>
                                            <td class="text-start ps-4 fw-bold text-dark">
                                                {{ $doc->employee->full_name_ar ?? 'موظف محذوف' }}
                                                @can('view_employees')
                                                    @if ($doc->employee)
                                                        <a href="{{ route('admin.hr.employees.show', $doc->employee->id) }}"
                                                            class="emp-link text-primary ms-1" title="فتح ملف الموظف">
                                                            <i class="mdi mdi-open-in-new"></i>
                                                        </a>
                                                    @endif
                                                @endcan
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-3 py-2">
                                                    {{ $doc->document_type }}
                                                </span>
                                            </td>
                                            <td class="fw-bold font-monospace">
                                                {{ $doc->expiry_date ? \Carbon\Carbon::parse($doc->expiry_date)->format('Y-m-d') : '---' }}
                                            </td>
                                            <td>
                                                <span
                                                    class="days-badge {{ $daysLeft <= 7 ? 'bg-warning text-dark' : 'bg-info text-white' }}">
                                                    باقي {{ max(0, $daysLeft) }} يوم
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- رسالة النجاح في حالة عدم وجود تنبيهات --}}
                                    @if ($expiredDocuments->isEmpty() && $expiringDocuments->isEmpty())
                                        <tr>
                                            <td colspan="4" class="p-5 text-success fw-bold fs-5">
                                                <i class="mdi mdi-shield-check-outline fs-1 d-block mb-2"></i>
                                                جميع الوثائق الإدارية للموظفين صالحة وسارية ✅
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. قائمة العقود المنتهية وقريباً --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white pt-4 pb-3 border-bottom">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="mdi mdi-file-sign text-primary me-2"></i> موقف عقود العمل
                        </h5>
                    </div>
                    <div class="card-body p-0 custom-scroll">
                        <ul class="list-group list-group-flush">

                            {{-- رسالة النجاح الكلية لو القائمتين فاضيين --}}
                            @if ($expiredContracts->isEmpty() && $expiringContracts->isEmpty())
                                <li
                                    class="list-group-item text-center p-5 text-success fw-bold border-0 h-100 d-flex flex-column justify-content-center">
                                    <div>
                                        <i class="mdi mdi-file-check-outline fs-1 d-block mb-2"></i>
                                        لا توجد أي عقود عمل منتهية أو قاربت على الانتهاء ✅
                                    </div>
                                </li>
                            @else
                                {{-- 🔥 عقود منتهية بالفعل 🔥 --}}
                                @foreach ($expiredContracts as $contract)
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3"
                                        style="background-color: rgba(239, 68, 68, 0.05);">
                                        <div>
                                            <div class="fw-bold text-danger">
                                                {{ $contract->employee->full_name_ar ?? 'موظف محذوف' }}
                                            </div>
                                            <small class="text-danger fw-bold">
                                                <i class="mdi mdi-alert-circle-outline"></i> العقد منتهي!
                                            </small>
                                        </div>
                                        <span class="badge bg-danger text-white rounded-pill fw-bold px-3 py-2">
                                            تجديد فوراً
                                        </span>
                                    </li>
                                @endforeach

                                {{-- ⚠️ عقود تنتهي قريباً ⚠️ --}}
                                @foreach ($expiringContracts as $contract)
                                    @php $daysLeft = $contract->end_date ? now()->diffInDays(\Carbon\Carbon::parse($contract->end_date), false) : 0; @endphp
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div>
                                            <div class="fw-bold text-dark">
                                                {{ $contract->employee->full_name_ar ?? 'موظف محذوف' }}
                                            </div>
                                            <small class="text-muted">
                                                <i class="mdi mdi-calendar-blank-outline"></i> ينتهي:
                                                {{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('Y-m-d') : '---' }}
                                            </small>
                                        </div>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill fw-bold px-3 py-2 text-nowrap">
                                            باقي {{ max(0, $daysLeft) }} يوم
                                        </span>
                                    </li>
                                @endforeach
                            @endif

                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
