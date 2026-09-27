@extends('backend.layouts.master')
@section('title', 'الملف الشخصي | ' . $teacher->name_ar)
@section('title_page', 'إدارة المعلمين والهيئة التدريسية')

@push('css')
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
        }

        /* 🌟 Premium Avatar 🌟 */
        .avatar-xxl {
            width: 160px;
            height: 160px;
            border: 5px solid #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            object-fit: cover;
        }

        .avatar-title-lg {
            width: 160px;
            height: 160px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 5px solid #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            font-size: 60px;
        }

        /* 🌟 Info Cards 🌟 */
        .info-card {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            border: 1px dashed #cbd5e1;
            height: 100%;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(59, 130, 246, 0.1);
        }

        .info-label {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0;
        }

        /* 🌟 Subject Badges 🌟 */
        .subject-badge {
            background-color: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }

        .subject-badge:hover {
            background-color: #0284c7;
            color: #fff;
        }

        /* 🌟 Sections Table 🌟 */
        .table-erp {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            padding: 14px 10px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .info-card {
            background-color: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .info-card:hover {
            border-color: #60a5fa;
            background-color: rgba(96, 165, 250, 0.1);
        }

        body[data-layout-color="dark"] .info-label {
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .info-value,
        body[data-layout-color="dark"] .text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .table-erp thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody tr:hover td {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .subject-badge {
            background-color: rgba(14, 165, 233, 0.1);
            border-color: rgba(14, 165, 233, 0.2);
            color: #38bdf8;
        }

        body[data-layout-color="dark"] .bg-white {
            background-color: #323a46 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 رأس الصفحة والأزرار 🌟 --}}
        <div class="row mb-4 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold text-dark mb-1">
                        <i class="bx bx-user-pin text-primary me-1 fs-3 align-middle"></i> الملف الشخصي والسجل الوظيفي
                    </h4>
                    <p class="text-muted mb-0 font-14">مراجعة البيانات الأكاديمية والإدارية للمعلم: <span
                            class="fw-bold text-dark">{{ $teacher->name_ar }}</span></p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    @can('view_teachers')
                        <a href="{{ route('admin.teachers.print_contract', $teacher->id) }}" target="_blank"
                            class="btn btn-outline-dark rounded-pill px-4 fw-bold shadow-sm bg-white border-2">
                            <i class="bx bx-printer me-1 align-middle fs-5"></i> طباعة العقد
                        </a>
                    @endcan

                    @can('edit_teachers')
                        <a href="{{ route('admin.teachers.edit', $teacher->id) }}"
                            class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark">
                            <i class="bx bx-edit me-1 align-middle fs-5"></i> تعديل البيانات
                        </a>
                    @endcan

                    <a href="{{ route('admin.teachers.index') }}"
                        class="btn btn-light border-2 rounded-pill px-4 fw-bold shadow-sm text-dark">
                        <i class="bx bx-arrow-back me-1 align-middle fs-5"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- 🌟 الكارت الأيمن (البروفايل المصغر) 🌟 --}}
            <div class="col-xl-4 col-lg-5">
                <div class="card card-premium text-center shadow-sm h-100 border-top border-primary border-4">
                    <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-center">
                        <div class="mx-auto mb-4">
                            @php $avatar = $teacher->image ?? ($teacher->hrEmployee->profile_image ?? null); @endphp
                            @if ($avatar)
                                <img src="{{ asset('storage/' . $avatar) }}" class="rounded-circle avatar-xxl"
                                    alt="profile-image">
                            @else
                                <div class="avatar-title-lg bg-primary bg-opacity-10 text-primary rounded-circle fw-bold">
                                    {{ mb_substr($teacher->name_ar, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <h4 class="mb-2 fw-bolder text-dark">{{ $teacher->name_ar }}</h4>
                        <p class="text-muted font-monospace fw-bold mb-3"><i
                                class="bx bx-id-card text-primary align-middle me-1"></i> {{ $teacher->national_id }}</p>

                        <div class="mb-4">
                            <span
                                class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-4 py-2 font-13 rounded-pill shadow-sm">
                                <i class="bx bx-buildings me-1 align-middle"></i>
                                {{ $teacher->branch?->name_ar ?? 'الفرع الرئيسي' }}
                            </span>
                        </div>

                        <div class="border-top border-dashed pt-4 mt-2">
                            <h6 class="text-muted fw-bold mb-3 font-13 text-uppercase">التخصص الأكاديمي (المواد)</h6>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                @forelse ($teacher->subjects as $subject)
                                    <span class="subject-badge shadow-sm"><i class="bx bx-book-open"></i>
                                        {{ $subject->name_ar }}</span>
                                @empty
                                    <span class="badge bg-light text-muted border px-3 py-2">تخصص غير محدد أو غير مربوط
                                        بمادة</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🌟 الكارت الأيسر (البيانات التفصيلية والتسكين) 🌟 --}}
            <div class="col-xl-8 col-lg-7">

                {{-- 1. كارت البيانات الإدارية والـ HR --}}
                @if ($teacher->hrEmployee)
                    <div class="card card-premium shadow-sm border-top border-success border-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="text-success mb-4 fw-bold border-bottom border-success border-opacity-25 pb-3">
                                <i class="bx bx-briefcase me-2 fs-4 align-middle"></i> بيانات شؤون الموظفين (HR)
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="info-card">
                                        <div class="info-label"><i class="bx bx-barcode text-success"></i> الرقم الوظيفي
                                        </div>
                                        <div class="info-value font-monospace text-primary">
                                            {{ $teacher->hrEmployee->employee_code }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-card">
                                        <div class="info-label"><i class="bx bx-money text-success"></i> الراتب الأساسي
                                        </div>
                                        <div class="info-value font-monospace text-success" dir="ltr">
                                            {{ number_format($teacher->hrEmployee->basic_salary, 3) }} OMR</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-card">
                                        <div class="info-label"><i class="bx bx-calendar-check text-success"></i> تاريخ
                                            التعيين</div>
                                        <div class="info-value font-monospace" dir="ltr">
                                            {{ $teacher->hrEmployee->join_date }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-label"><i class="bx bx-sitemap text-success"></i> القسم الأكاديمي
                                        </div>
                                        <div class="info-value">
                                            {{ $teacher->hrEmployee->department->name_ar ?? 'غير محدد' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-label"><i class="bx bx-time-five text-success"></i> الوردية (مواعيد
                                            العمل)</div>
                                        <div class="info-value">{{ $teacher->hrEmployee->shift->name_ar ?? 'غير محدد' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 2. كارت معلومات التواصل الأساسية --}}
                <div class="card card-premium shadow-sm border-top border-info border-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="text-info mb-4 fw-bold border-bottom border-info border-opacity-25 pb-3">
                            <i class="bx bx-id-card me-2 fs-4 align-middle"></i> معلومات التواصل والاتصال
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label"><i class="bx bx-phone text-info"></i> رقم الهاتف المحمول</div>
                                    <div class="info-value font-monospace" dir="ltr">{{ $teacher->phone }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label"><i class="bx bx-envelope text-info"></i> البريد الإلكتروني
                                        الرسمي</div>
                                    <div class="info-value font-monospace" dir="ltr">
                                        {{ $teacher->email ?? 'لا يوجد بريد إلكتروني' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label"><i class="bx bx-globe text-info"></i> الجنسية</div>
                                    <div class="info-value">{{ $teacher->nationality ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label"><i class="bx bx-map text-info"></i> العنوان السكني</div>
                                    <div class="info-value">{{ $teacher->address ?? 'لا يوجد عنوان مسجل بالتفصيل' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. كارت التسكين الأكاديمي --}}
                <div class="card card-premium shadow-sm h-100 border-top border-warning border-4">
                    <div class="card-body p-4">
                        <div
                            class="d-flex justify-content-between align-items-center mb-4 border-bottom border-warning border-opacity-25 pb-3">
                            <h5 class="text-warning text-darken fw-bold m-0">
                                <i class="bx bx-chalkboard me-2 fs-4 align-middle"></i> التسكين الأكاديمي والشعب المسندة
                            </h5>
                            <span class="badge bg-warning text-dark px-3 py-1 font-13 rounded-pill shadow-sm">
                                <i class="bx bx-layer me-1 align-middle"></i> مسند لـ ({{ $teacher->sections->count() }})
                                شعبة
                            </span>
                        </div>

                        <div class="table-responsive border rounded-3 shadow-sm"
                            style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-erp table-hover align-middle mb-0">
                                <thead class="sticky-top" style="z-index: 5;">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th class="text-start ps-3">الصف الدراسي</th>
                                        <th class="text-start ps-3">اسم الشعبة</th>
                                        <th>السنة الدراسية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teacher->sections as $index => $section)
                                        <tr>
                                            <td class="text-muted fw-bold font-monospace">{{ $index + 1 }}</td>
                                            <td class="text-start ps-3 fw-bold text-primary">
                                                {{ $section->grade?->name_ar ?? 'غير محدد' }}</td>
                                            <td class="text-start ps-3 fw-bold text-dark">{{ $section->name_ar }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-light text-secondary border px-3 py-1">{{ $section->academicYear?->name ?? '---' }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 bg-white text-muted">
                                                <i class="bx bx-error-circle fs-1 d-block mb-2 opacity-50"></i>
                                                <h6 class="fw-bold m-0">لم يتم إسناد أي شعب أكاديمية لهذا المعلم حتى الآن.
                                                </h6>
                                            </td>
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
@endsection
