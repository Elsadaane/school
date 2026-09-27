@extends('backend.layouts.master')
@section('title', 'ملف الطالب | ' . $student->first_name)
@section('title_page', 'السجل الشامل لبيانات الطالب')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* 🌟 Premium Profile UI 🌟 */
        .profile-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%);
            border-radius: 16px;
            padding: 30px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 40%);
            transform: rotate(30deg);
            pointer-events: none;
        }

        .avatar-lg {
            width: 120px;
            height: 120px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-pills .nav-link {
            color: #64748b;
            font-weight: 700;
            border-radius: 10px;
            margin-bottom: 8px;
            padding: 12px 20px;
            transition: all 0.3s ease;
            text-align: right;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #f8fafc;
            color: #3b82f6;
        }

        .nav-pills .nav-link.active {
            background-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            color: #fff;
        }

        /* 🔥 تحسين التباين في صناديق المعلومات 🔥 */
        .info-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #3b82f6;
        }

        .info-box .text-muted {
            color: #1e40af !important;
            font-weight: 700;
        }

        .info-box .text-dark {
            color: #0f172a !important;
        }

        .info-box .text-primary {
            color: #1d4ed8 !important;
        }

        /* 🔥 بطاقات المرفقات المحسّنة 🔥 */
        .document-card {
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: 0.3s;
            background: #fff;
        }

        .document-card:hover {
            border-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .document-card .doc-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 36px;
        }

        .doc-icon.primary {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .doc-icon.warning {
            background: #fef3c7;
            color: #b45309;
        }

        .doc-icon.secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .doc-icon.danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .doc-icon.success {
            background: #d1fae5;
            color: #047857;
        }

        /* 🔥 خلايا الجداول المحسّنة 🔥 */
        .bg-light-blue {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
            color: #1e3a8a !important;
            font-weight: 800 !important;
        }

        .bg-light-green {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%) !important;
            color: #064e3b !important;
            font-weight: 800 !important;
        }

        .bg-light-red {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%) !important;
            color: #7f1d1d !important;
            font-weight: 800 !important;
        }

        /* 🔥 بطاقات الحالة الصحية 🔥 */
        .health-item {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid;
        }

        .health-item.ok {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-color: #10b981;
        }

        .health-item.ok .health-label {
            color: #064e3b;
            font-weight: 700;
        }

        .health-item.ok .health-icon {
            color: #059669;
            font-size: 22px;
        }

        .health-item.warning {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-color: #ef4444;
        }

        .health-item.warning .health-label {
            color: #7f1d1d;
            font-weight: 700;
        }

        .health-item.warning .health-icon {
            color: #dc2626;
            font-size: 22px;
        }

        /* 🔥 بطاقات النقل والطوارئ 🔥 */
        .transport-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .transport-box .transport-icon {
            width: 60px;
            height: 60px;
            background: #fff;
            border: 2px solid #f59e0b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #b45309;
        }

        .emergency-box {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #ef4444;
            border-radius: 12px;
            padding: 20px;
        }

        .emergency-box p,
        .emergency-box h6 {
            color: #7f1d1d !important;
        }

        .emergency-box .emergency-icon {
            color: #b91c1c;
            font-size: 20px;
        }

        /* 🔥 بطاقات الإحصائيات (حضور/غياب) 🔥 */
        .stat-box {
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid;
        }

        .stat-box.present {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-color: #10b981;
        }

        .stat-box.present .stat-value {
            color: #064e3b;
        }

        .stat-box.present .stat-icon {
            color: #059669;
            background: #fff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .stat-box.absent {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-color: #ef4444;
        }

        .stat-box.absent .stat-value {
            color: #7f1d1d;
        }

        .stat-box.absent .stat-icon {
            color: #dc2626;
            background: #fff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .stat-box.late {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-color: #f59e0b;
        }

        .stat-box.late .stat-value {
            color: #78350f;
        }

        .stat-box.late .stat-icon {
            color: #d97706;
            background: #fff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 10px;
        }

        /* 🔥 شارات الحالة المحسّنة 🔥 */
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 2px solid;
        }

        .status-badge.success {
            background: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }

        .status-badge.danger {
            background: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }

        .status-badge.warning {
            background: #fef3c7;
            color: #78350f;
            border-color: #f59e0b;
        }

        .status-badge.info {
            background: #dbeafe;
            color: #1e3a8a;
            border-color: #3b82f6;
        }

        .status-badge.secondary {
            background: #e2e8f0;
            color: #334155;
            border-color: #64748b;
        }

        /* 🌟 Contracts Table Styling 🌟 */
        .table-custom {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            font-size: 13px;
            font-weight: 800;
            color: #475569;
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
        }

        .table-custom tbody td {
            font-size: 14px;
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px;
        }

        .action-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: 0.2s;
            border: 2px solid;
            color: #64748b;
            background: #fff;
            text-decoration: none;
            font-size: 18px;
        }

        .action-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .action-icon.view {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #3b82f6;
        }

        .action-icon.edit {
            background: #fef3c7;
            color: #b45309;
            border-color: #f59e0b;
        }

        .action-icon.delete {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #ef4444;
        }

        .action-icon.pay {
            background: #d1fae5;
            color: #047857;
            border-color: #10b981;
        }

        .action-icon.signature {
            background: #ede9fe;
            color: #6d28d9;
            border-color: #8b5cf6;
        }

        /* 🔥 ملاحظات صحية 🔥 */
        .health-notes {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
        }

        .health-notes h6 {
            color: #78350f !important;
            font-weight: 800;
        }

        .health-notes p {
            color: #713f12 !important;
            font-weight: 600;
        }

        /* 🔥 شارات الدرجات 🔥 */
        .grade-badge {
            padding: 8px 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            border: 2px solid;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .grade-badge.pass {
            background: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }

        .grade-badge.fail {
            background: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }

        @media (max-width: 991.98px) {
            .nav-pills {
                flex-direction: row !important;
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 10px;
                margin-bottom: 15px;
            }

            .nav-pills .nav-link {
                white-space: nowrap;
                margin-bottom: 0;
                margin-left: 10px;
            }
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .nav-pills .nav-link:hover:not(.active) {
            background-color: #3b4652;
            color: #60a5fa;
        }

        body[data-layout-color="dark"] .info-box {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border-color: #3b82f6;
        }

        body[data-layout-color="dark"] .info-box .text-muted {
            color: #93c5fd !important;
        }

        body[data-layout-color="dark"] .info-box .text-dark {
            color: #f8fafc !important;
        }

        body[data-layout-color="dark"] .document-card {
            background-color: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .document-card:hover {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border-color: #3b82f6;
        }

        body[data-layout-color="dark"] .table-custom thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light-blue {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
            color: #dbeafe !important;
        }

        body[data-layout-color="dark"] .bg-light-green {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%) !important;
            color: #d1fae5 !important;
        }

        body[data-layout-color="dark"] .bg-light-red {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%) !important;
            color: #fee2e2 !important;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }

        body[data-layout-color="dark"] .health-item.ok {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
            border-color: #10b981;
        }

        body[data-layout-color="dark"] .health-item.ok .health-label {
            color: #d1fae5;
        }

        body[data-layout-color="dark"] .health-item.warning {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
            border-color: #ef4444;
        }

        body[data-layout-color="dark"] .health-item.warning .health-label {
            color: #fee2e2;
        }

        body[data-layout-color="dark"] .transport-box {
            background: linear-gradient(135deg, #78350f 0%, #92400e 100%);
            border-color: #f59e0b;
        }

        body[data-layout-color="dark"] .transport-box .transport-icon {
            background: #323a46;
            border-color: #f59e0b;
            color: #fde68a;
        }

        body[data-layout-color="dark"] .emergency-box {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
            border-color: #ef4444;
        }

        body[data-layout-color="dark"] .emergency-box p,
        body[data-layout-color="dark"] .emergency-box h6 {
            color: #fee2e2 !important;
        }

        body[data-layout-color="dark"] .stat-box.present {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
            border-color: #10b981;
        }

        body[data-layout-color="dark"] .stat-box.present .stat-value {
            color: #d1fae5;
        }

        body[data-layout-color="dark"] .stat-box.absent {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
            border-color: #ef4444;
        }

        body[data-layout-color="dark"] .stat-box.absent .stat-value {
            color: #fee2e2;
        }

        body[data-layout-color="dark"] .stat-box.late {
            background: linear-gradient(135deg, #78350f 0%, #92400e 100%);
            border-color: #f59e0b;
        }

        body[data-layout-color="dark"] .stat-box.late .stat-value {
            color: #fef3c7;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="row mb-3 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold mb-1 text-dark">
                        <i class="mdi mdi-account-details-outline text-primary me-1 fs-3 align-middle"></i> السجل الشامل
                        للطالب
                    </h4>
                    <p class="text-muted mb-0 font-14">إدارة وتحديث كافة بيانات الطالب الشخصية والأكاديمية والمالية.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @can('create_students')
                        <a href="{{ route('admin.students.create', ['copy_from' => $student->id]) }}"
                            class="btn btn-info rounded-pill shadow-sm font-bold text-white px-4">
                            <i class="mdi mdi-content-copy me-1 align-middle"></i> نسخ البيانات (إضافة أخ)
                        </a>
                    @endcan

                    @can('edit_students')
                        <a href="{{ route('admin.students.edit', $student->id) }}"
                            class="btn btn-warning rounded-pill shadow-sm font-bold px-4 text-dark">
                            <i class="mdi mdi-pencil-outline me-1 align-middle"></i> تعديل السجل
                        </a>
                    @endcan

                    <a href="{{ route('admin.students.index') }}"
                        class="btn btn-light border rounded-pill shadow-sm fw-bold px-4 text-dark">
                        <i class="mdi mdi-arrow-left me-1 align-middle"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        {{-- ================= كارت الهوية (Header) ================= --}}
        <div class="row">
            <div class="col-12">
                <div class="profile-header mb-4 border border-primary border-opacity-25">
                    <div
                        class="d-flex align-items-center position-relative z-index-1 flex-wrap gap-4 text-center text-md-start">
                        <div class="flex-shrink-0 mx-auto mx-md-0">
                            @if ($student->student_photo)
                                <img src="{{ Storage::url($student->student_photo) }}" alt="student-image"
                                    class="rounded-circle avatar-lg shadow-lg">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->first_name . ' ' . $student->tribe) }}&background=random&color=fff"
                                    alt="student-avatar" class="rounded-circle avatar-lg shadow-lg">
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mt-0 mb-1 text-white font-bold">{{ $student->first_name }}
                                {{ $student->second_name ?? '' }} {{ $student->third_name ?? '' }} {{ $student->tribe }}
                            </h3>
                            <p class="text-white-50 font-monospace fs-6 mb-2">
                                <i class="mdi mdi-identifier me-1"></i> الرقم الأكاديمي:
                                #STU-{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}
                            </p>

                            <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start mt-3">
                                <span class="badge bg-white text-dark rounded-pill px-3 py-2 font-13 shadow-sm">
                                    <i class="mdi mdi-school-outline text-primary me-1"></i>
                                    {{ $student->grade->name_ar ?? 'الصف غير محدد' }}
                                </span>
                                <span class="badge bg-white text-dark rounded-pill px-3 py-2 font-13 shadow-sm">
                                    <i class="mdi mdi-calendar-blank text-primary me-1"></i>
                                    {{ $student->academicYear->name ?? 'السنة غير محددة' }}
                                </span>
                                <span class="badge bg-white text-dark rounded-pill px-3 py-2 font-13 shadow-sm">
                                    <i class="mdi mdi-map-marker-outline text-primary me-1"></i>
                                    {{ $student->branch->name_ar ?? 'الفرع غير محدد' }}
                                </span>
                                <span class="badge rounded-pill px-3 py-2 font-13 shadow-sm"
                                    style="background: #10b981; color: #fff; border: 2px solid #059669;">
                                    <i class="mdi mdi-check-decagram me-1"></i> مقيد وفعال
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= التابات والمحتوى ================= --}}
        <div class="row">
            {{-- القائمة الجانبية للتابات --}}
            <div class="col-xl-3 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body p-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-info-tab" data-bs-toggle="pill" href="#v-pills-info"
                                role="tab">
                                <i class="mdi mdi-card-account-details-outline me-2 fs-5 align-middle"></i> البيانات الشخصية
                            </a>
                            <a class="nav-link" id="v-pills-health-tab" data-bs-toggle="pill" href="#v-pills-health"
                                role="tab">
                                <i class="mdi mdi-hospital-box-outline me-2 fs-5 align-middle"></i> السجل الصحي والمواصلات
                            </a>
                            <a class="nav-link" id="v-pills-docs-tab" data-bs-toggle="pill" href="#v-pills-docs"
                                role="tab">
                                <i class="mdi mdi-folder-zip-outline me-2 fs-5 align-middle"></i> المرفقات والوثائق
                            </a>
                            <hr class="my-2 border-secondary opacity-25">
                            <a class="nav-link" id="v-pills-contracts-tab" data-bs-toggle="pill" href="#v-pills-contracts"
                                role="tab">
                                <i class="mdi mdi-file-document-edit-outline me-2 fs-5 align-middle"></i> العقود والماليات
                            </a>
                            <a class="nav-link" id="v-pills-academic-tab" data-bs-toggle="pill" href="#v-pills-academic"
                                role="tab">
                                <i class="mdi mdi-chart-line me-2 fs-5 align-middle"></i> السجل الأكاديمي
                            </a>
                            <a class="nav-link" id="v-pills-attendance-tab" data-bs-toggle="pill"
                                href="#v-pills-attendance" role="tab">
                                <i class="mdi mdi-calendar-check-outline me-2 fs-5 align-middle"></i> سجل الحضور
                            </a>
                            <a class="nav-link text-primary mt-2" id="v-pills-transfers-tab" data-bs-toggle="pill"
                                href="#v-pills-transfers" role="tab"
                                style="background: #eff6ff; border: 2px solid #3b82f6;">
                                <i class="mdi mdi-swap-horizontal-bold me-2 fs-5 align-middle"></i> سجل التحويلات
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- محتوى التابات --}}
            <div class="col-xl-9 col-lg-8">
                <div class="tab-content pt-0" id="v-pills-tabContent">

                    {{-- 1. البيانات الأساسية --}}
                    <div class="tab-pane fade show active" id="v-pills-info" role="tabpanel">
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4 p-md-5">
                                <h5 class="text-primary font-bold mb-4 border-bottom pb-3">
                                    <i class="mdi mdi-information-outline me-1 fs-4 align-middle"></i> البيانات الشخصية
                                    للطالب
                                </h5>
                                <div class="row mb-5 g-4">
                                    <div class="col-md-4">
                                        <p class="text-muted fw-bold mb-1 font-13" style="color: #475569 !important;">
                                            تاريخ الميلاد والعمر</p>
                                        <h6 class="font-monospace fw-bold fs-6 text-dark">
                                            {{ \Carbon\Carbon::parse($student->dob)->format('Y-m-d') }}
                                            <span class="text-muted font-sans font-12 ms-1">
                                                ({{ \Carbon\Carbon::parse($student->dob)->age }} سنة)
                                            </span>
                                        </h6>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted fw-bold mb-1 font-13" style="color: #475569 !important;">
                                            النوع (الجنس)</p>
                                        <h6 class="fw-bold text-dark fs-6">
                                            <i
                                                class="mdi mdi-gender-{{ $student->gender == 'male' ? 'male text-primary' : 'female text-danger' }} me-1"></i>
                                            {{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}
                                        </h6>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted fw-bold mb-1 font-13" style="color: #475569 !important;">
                                            الجنسية</p>
                                        <h6 class="fw-bold text-dark fs-6">{{ $student->nationality ?? '---' }}</h6>
                                    </div>
                                </div>

                                <h5 class="text-primary font-bold mb-4 border-bottom pb-3">
                                    <i class="mdi mdi-account-group-outline me-1 fs-4 align-middle"></i> بيانات العائلة
                                    وولي الأمر
                                </h5>
                                <div class="info-box shadow-sm mb-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <p class="fw-bold mb-1 font-13" style="color: #1e40af !important;">ولي الأمر
                                                (المسؤول المالي والأكاديمي)</p>
                                            <h6 class="font-15 fw-bold mb-2" style="color: #1d4ed8 !important;">
                                                @if ($student->parent)
                                                    <a href="{{ route('admin.guardians.show', $student->parent_id) }}"
                                                        class="text-decoration-none" style="color: #1d4ed8 !important;">
                                                        {{ $student->parent->name_ar }}
                                                        <i class="mdi mdi-open-in-new ms-1 font-12"></i>
                                                    </a>
                                                @else
                                                    <span style="color: #b91c1c !important;">
                                                        <i class="mdi mdi-alert-circle-outline me-1"></i> غير مسجل
                                                    </span>
                                                @endif
                                            </h6>
                                            <p class="mb-0 font-monospace fw-bold" style="color: #0f172a !important;">
                                                <i class="mdi mdi-phone me-1" style="color: #059669 !important;"></i>
                                                {{ $student->parent?->phone ?? '---' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 border-start">
                                            <p class="fw-bold mb-1 font-13 ps-md-3" style="color: #1e40af !important;">
                                                بيانات الأم</p>
                                            <div class="ps-md-3">
                                                @if ($student->mother_name)
                                                    <h6 class="font-14 font-bold mb-2" style="color: #0f172a !important;">
                                                        {{ $student->mother_name }}
                                                    </h6>
                                                    <p class="mb-0 font-monospace fw-bold"
                                                        style="color: #0f172a !important;">
                                                        <i class="mdi mdi-phone me-1"
                                                            style="color: #059669 !important;"></i>
                                                        {{ $student->mother_phone ?? '---' }}
                                                    </p>
                                                @else
                                                    <span class="status-badge danger mt-2">
                                                        <i class="mdi mdi-alert-circle-outline"></i>
                                                        بيانات الأم غير متوفرة في السجلات
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. الصحة والمواصلات --}}
                    <div class="tab-pane fade" id="v-pills-health" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="card shadow-sm border-0 rounded-4 h-100 border-top border-success border-4">
                                    <div class="card-body p-4">
                                        <h5 class="font-bold mb-4 border-bottom border-success border-opacity-25 pb-3"
                                            style="color: #047857 !important;">
                                            <i class="mdi mdi-hospital-box me-1 fs-4 align-middle"></i> التقييم والحالة
                                            الصحية
                                        </h5>

                                        {{-- 🔥 بطاقات الحالة الصحية المحسّنة 🔥 --}}
                                        <div class="health-item {{ $student->has_allergies ? 'warning' : 'ok' }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="mdi mdi-peanut-outline health-icon"></i>
                                                <span class="health-label font-14">يعاني من حساسية؟</span>
                                            </div>
                                            @if ($student->has_allergies)
                                                <span class="status-badge danger">
                                                    <i class="mdi mdi-alert"></i> نعم، يعاني
                                                </span>
                                            @else
                                                <span class="status-badge success">
                                                    <i class="mdi mdi-check"></i> سليم
                                                </span>
                                            @endif
                                        </div>

                                        <div class="health-item {{ $student->has_hearing_issues ? 'warning' : 'ok' }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="mdi mdi-ear-hearing health-icon"></i>
                                                <span class="health-label font-14">توجد مشاكل سمعية؟</span>
                                            </div>
                                            @if ($student->has_hearing_issues)
                                                <span class="status-badge danger">
                                                    <i class="mdi mdi-alert"></i> توجد ملاحظات
                                                </span>
                                            @else
                                                <span class="status-badge success">
                                                    <i class="mdi mdi-check"></i> سليم
                                                </span>
                                            @endif
                                        </div>

                                        <div class="health-item {{ $student->has_vision_issues ? 'warning' : 'ok' }}">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="mdi mdi-eye-outline health-icon"></i>
                                                <span class="health-label font-14">توجد مشاكل بصرية؟</span>
                                            </div>
                                            @if ($student->has_vision_issues)
                                                <span class="status-badge danger">
                                                    <i class="mdi mdi-alert"></i> توجد ملاحظات
                                                </span>
                                            @else
                                                <span class="status-badge success">
                                                    <i class="mdi mdi-check"></i> سليم
                                                </span>
                                            @endif
                                        </div>

                                        @if ($student->health_notes)
                                            <div class="health-notes">
                                                <h6 class="font-13 fw-bold mb-2">
                                                    <i class="mdi mdi-note-edit-outline me-1"></i> ملاحظات صحية إضافية:
                                                </h6>
                                                <p class="mb-0 font-14 lh-base">{{ $student->health_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card shadow-sm border-0 rounded-4 h-100 border-top border-warning border-4">
                                    <div class="card-body p-4">
                                        <h5 class="font-bold mb-4 border-bottom border-warning border-opacity-25 pb-3"
                                            style="color: #b45309 !important;">
                                            <i class="mdi mdi-bus-school me-1 fs-4 align-middle"></i> خدمة النقل والطوارئ
                                        </h5>

                                        @php
                                            $transportTypes = [
                                                'private' => [
                                                    'text' => 'نقل خاص (أهلي)',
                                                    'icon' => 'mdi-car',
                                                    'color' => 'secondary',
                                                ],
                                                'bus_both' => [
                                                    'text' => 'باص مدرسة (ذهاب وعودة)',
                                                    'icon' => 'mdi-bus-double-decker',
                                                    'color' => 'success',
                                                ],
                                                'bus_go' => [
                                                    'text' => 'باص مدرسة (ذهاب فقط)',
                                                    'icon' => 'mdi-bus',
                                                    'color' => 'info',
                                                ],
                                                'bus_return' => [
                                                    'text' => 'باص مدرسة (عودة فقط)',
                                                    'icon' => 'mdi-bus',
                                                    'color' => 'warning',
                                                ],
                                            ];
                                            $trans =
                                                $transportTypes[$student->transport_type] ?? $transportTypes['private'];
                                        @endphp

                                        {{-- 🔥 صندوق النقل المحسّن 🔥 --}}
                                        <div class="transport-box">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="transport-icon flex-shrink-0">
                                                    <i class="mdi {{ $trans['icon'] }}"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 font-15 fw-bold" style="color: #78350f !important;">
                                                        {{ $trans['text'] }}
                                                    </h6>
                                                    <p class="font-13 mb-0 fw-bold" style="color: #92400e !important;">
                                                        <i class="mdi mdi-map-marker-radius me-1"></i>
                                                        نقطة التجمع: {{ $student->pickup_point ?? 'غير محدد' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="font-bold mb-3 mt-4" style="color: #b91c1c !important;">
                                            <i class="mdi mdi-phone-alert me-1 fs-5 align-middle"></i> جهة اتصال الطوارئ
                                            وعنوان السكن
                                        </h6>

                                        {{-- 🔥 صندوق الطوارئ المحسّن 🔥 --}}
                                        <div class="emergency-box">
                                            <p class="mb-2 font-14 fw-bold">
                                                <i class="mdi mdi-account-alert emergency-icon me-2"></i>
                                                {{ $student->emergency_name ?? '---' }}
                                                <small
                                                    style="color: #991b1b !important;">({{ $student->emergency_relation ?? '---' }})</small>
                                            </p>
                                            <p class="mb-3 font-14 fw-bold font-monospace">
                                                <i class="mdi mdi-phone emergency-icon me-2"></i>
                                                <a href="tel:{{ $student->emergency_phone }}"
                                                    class="text-decoration-none" dir="ltr"
                                                    style="color: #7f1d1d !important;">
                                                    {{ $student->emergency_phone ?? '---' }}
                                                </a>
                                            </p>
                                            <hr style="border-color: rgba(239, 68, 68, 0.3); margin: 10px 0;">
                                            <p class="mb-0 font-13 fw-bold lh-base" style="color: #7f1d1d !important;">
                                                <i class="mdi mdi-map-marker emergency-icon me-1"></i>
                                                {{ $student->region ?? '---' }} - م: {{ $student->house_number ?? '-' }} ،
                                                س: {{ $student->street_number ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. المرفقات --}}
                    <div class="tab-pane fade" id="v-pills-docs" role="tabpanel">
                        <div class="card shadow-sm border-0 rounded-4 border-top border-info border-4">
                            <div class="card-body p-4 p-md-5">
                                <h5 class="font-bold mb-4 border-bottom border-info border-opacity-25 pb-3"
                                    style="color: #0369a1 !important;">
                                    <i class="mdi mdi-folder-zip-outline fs-4 align-middle me-1"></i> الوثائق الرسمية
                                    للطالب
                                </h5>
                                <div class="row g-4">
                                    @php
                                        $docs = [
                                            [
                                                'title' => 'الصورة الشخصية الرسمية',
                                                'field' => 'student_photo',
                                                'icon' => 'mdi-camera-front-variant',
                                                'color' => 'primary',
                                            ],
                                            [
                                                'title' => 'نسخة من شهادة الميلاد',
                                                'field' => 'birth_certificate_copy',
                                                'icon' => 'mdi-file-certificate-outline',
                                                'color' => 'warning',
                                            ],
                                            [
                                                'title' => 'نسخة من جواز السفر (إن وجد)',
                                                'field' => 'passport_copy',
                                                'icon' => 'mdi-passport',
                                                'color' => 'secondary',
                                            ],
                                            [
                                                'title' => 'التقارير الصحية المفصلة',
                                                'field' => 'health_reports',
                                                'icon' => 'mdi-file-document-multiple-outline',
                                                'color' => 'danger',
                                            ],
                                            [
                                                'title' => 'كرت التحصين والتطعيمات',
                                                'field' => 'vaccination_card',
                                                'icon' => 'mdi-needle',
                                                'color' => 'success',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($docs as $doc)
                                        <div class="col-lg-4 col-md-6">
                                            <div
                                                class="document-card shadow-sm h-100 d-flex flex-column justify-content-center">
                                                <div class="doc-icon {{ $doc['color'] }}">
                                                    <i class="mdi {{ $doc['icon'] }}"></i>
                                                </div>
                                                <h6 class="font-14 fw-bold text-dark mb-3">{{ $doc['title'] }}</h6>
                                                <div class="mt-auto">
                                                    @if ($student->{$doc['field']})
                                                        <a href="{{ Storage::url($student->{$doc['field']}) }}"
                                                            target="_blank"
                                                            class="btn btn-{{ $doc['color'] }} text-white rounded-pill px-4 shadow-sm fw-bold w-100">
                                                            <i class="mdi mdi-eye me-1"></i> عرض وفتح المرفق
                                                        </a>
                                                    @else
                                                        <span class="status-badge secondary w-100 justify-content-center">
                                                            <i class="mdi mdi-close-circle-outline"></i> غير متوفر
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. العقود والماليات --}}
                    <div class="tab-pane fade" id="v-pills-contracts" role="tabpanel">
                        <div class="card shadow-sm border-0 rounded-4 border-top border-success border-4">
                            <div
                                class="card-header bg-white border-bottom py-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <h5 class="fw-bold mb-0" style="color: #047857 !important;">
                                    <i class="mdi mdi-file-document-edit-outline me-1 fs-4 align-middle"></i> سجل العقود
                                    والفواتير المالية
                                </h5>
                                @can('create_student_contracts')
                                    <a href="{{ route('admin.student-contracts.create', ['student_id' => $student->id]) }}"
                                        class="btn btn-success rounded-pill fw-bold shadow-sm px-4">
                                        <i class="mdi mdi-plus-circle me-1 align-middle fs-5"></i> إصدار عقد مالي جديد
                                    </a>
                                @endcan
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table
                                        class="table table-bordered table-custom table-hover mb-0 text-center align-middle">
                                        <thead class="sticky-top bg-light" style="z-index: 5;">
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th>رقم العقد</th>
                                                <th>السنة الدراسية / الصف</th>
                                                <th>تاريخ العقد</th>
                                                <th class="bg-light-blue">الإجمالي الشامل</th>
                                                <th class="bg-light-green">المدفوع الفعلي</th>
                                                <th class="bg-light-red">المتبقي (المديونية)</th>
                                                @canany(['view_student_contracts', 'edit_student_contracts',
                                                    'delete_student_contracts', 'collect_installments'])
                                                    <th style="width: 220px;">إجراءات الإدارة</th>
                                                @endcanany
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($student->contracts ?? [] as $index => $contract)
                                                <tr>
                                                    <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                                    <td class="font-monospace fw-bold fs-6"
                                                        style="color: #1d4ed8 !important;">
                                                        {{ $contract->contract_number }}
                                                    </td>
                                                    <td>
                                                        <span class="status-badge secondary mb-1 d-block">
                                                            {{ $contract->academicYear->name ?? '---' }}
                                                        </span>
                                                        <span class="text-muted font-12 fw-bold">
                                                            {{ $contract->grade->name_ar ?? '---' }}
                                                        </span>
                                                    </td>
                                                    <td dir="ltr" class="font-monospace fw-bold text-muted">
                                                        {{ \Carbon\Carbon::parse($contract->contract_date)->format('Y-m-d') }}
                                                    </td>
                                                    <td class="bg-light-blue font-monospace fw-bolder fs-6">
                                                        {{ number_format($contract->grand_total, 3) }} OMR
                                                    </td>
                                                    <td class="bg-light-green font-monospace fw-bolder fs-6">
                                                        {{ number_format($contract->paid_amount, 3) }} OMR
                                                    </td>
                                                    <td class="bg-light-red font-monospace fw-bolder fs-6">
                                                        {{ number_format($contract->total_remaining ?? $contract->grand_total - $contract->paid_amount, 3) }}
                                                        OMR
                                                    </td>
                                                    @canany(['view_student_contracts', 'edit_student_contracts',
                                                        'delete_student_contracts', 'collect_installments'])
                                                        <td>
                                                            <div class="d-flex justify-content-center gap-1">
                                                                @can('view_student_contracts')
                                                                    <a href="{{ route('admin.student-contracts.signature', $contract->id) }}"
                                                                        class="action-icon signature" title="توقيع ولي الأمر">
                                                                        <i class="mdi mdi-signature-free"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.student-contracts.show', $contract->id) }}"
                                                                        target="_blank" class="action-icon view"
                                                                        title="طباعة الفاتورة">
                                                                        <i class="mdi mdi-printer"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('collect_installments')
                                                                    <a href="{{ route('admin.student-contracts.payments', $contract->id) }}"
                                                                        class="action-icon pay" title="المدفوعات والأقساط">
                                                                        <i class="mdi mdi-cash-register"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('edit_student_contracts')
                                                                    <a href="{{ route('admin.student-contracts.edit', $contract->id) }}"
                                                                        class="action-icon edit" title="تعديل العقد">
                                                                        <i class="mdi mdi-pencil"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('delete_student_contracts')
                                                                    <form
                                                                        action="{{ route('admin.student-contracts.destroy', $contract->id) }}"
                                                                        method="POST" class="d-inline confirm-delete-form">
                                                                        @csrf @method('DELETE')
                                                                        <button type="button"
                                                                            class="action-icon delete btn-confirm-delete"
                                                                            title="حذف وإلغاء العقد">
                                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    @endcanany
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-muted text-center py-5 bg-light">
                                                        <i class="mdi mdi-file-document-remove-outline fs-1 d-block mb-3 text-secondary opacity-50"
                                                            style="font-size: 50px;"></i>
                                                        <h6 class="fw-bold text-dark">لا توجد عقود أو فواتير مالية مسجلة
                                                            لهذا الطالب حتى الآن.</h6>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 🔥 5. الأكاديميات والدرجات 🔥 --}}
                    <div class="tab-pane fade" id="v-pills-academic" role="tabpanel">
                        <div class="card shadow-sm border-0 rounded-4 border-top border-dark border-4">
                            <div
                                class="card-header bg-white border-bottom py-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <h5 class="mb-0 fw-bold text-dark">
                                    <i class="mdi mdi-school text-primary me-2 fs-4 align-middle"></i> السجل الأكاديمي
                                    والدرجات
                                </h5>
                                @if (isset($marks) && $marks->count() > 0)
                                    <a href="{{ route('admin.certificates.print', ['student_id' => $student->id, 'term_id' => 1]) }}"
                                        target="_blank" class="btn btn-outline-dark rounded-pill fw-bold shadow-sm px-4">
                                        <i class="mdi mdi-printer me-1 align-middle"></i> طباعة الشهادة الأكاديمية
                                    </a>
                                @endif
                            </div>
                            <div class="card-body p-0">
                                @if (isset($marks) && $marks->count() > 0)
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-striped table-hover table-custom mb-0 align-middle">
                                            <thead class="sticky-top" style="z-index: 5;">
                                                <tr>
                                                    <th style="width: 25%;" class="text-start ps-4">اسم المادة الأكاديمية
                                                    </th>
                                                    <th style="width: 55%;" class="text-start ps-3">تفاصيل التقويم
                                                        (الأدوات والاختبارات)</th>
                                                    <th style="width: 20%;" class="text-center">المجموع الكلي للمادة</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($marks as $subject_id => $subjectMarks)
                                                    @php
                                                        $subjectName =
                                                            $subjectMarks->first()->subject->name_ar ??
                                                            'مادة غير معروفة';
                                                        $totalObtained = 0;
                                                        $totalMax = 0;
                                                    @endphp
                                                    <tr>
                                                        <td class="fw-bold text-dark text-start ps-4 fs-6 border-end">
                                                            {{ $subjectName }}
                                                        </td>
                                                        <td class="text-start ps-3 border-end">
                                                            <div class="d-flex flex-wrap gap-2">
                                                                @foreach ($subjectMarks as $mark)
                                                                    @php
                                                                        $catMax = floatval(
                                                                            $mark->category->max_mark ?? 0,
                                                                        );
                                                                        $catObtained = floatval(
                                                                            $mark->mark_obtained ?? 0,
                                                                        );
                                                                        $totalMax += $catMax;
                                                                        $totalObtained += $catObtained;
                                                                        $badgeClass =
                                                                            $catObtained < $catMax / 2
                                                                                ? 'fail'
                                                                                : 'pass';
                                                                    @endphp
                                                                    <span class="grade-badge {{ $badgeClass }}"
                                                                        title="{{ $mark->category->name ?? '' }}">
                                                                        {{ $mark->category->name ?? 'تقييم' }}:
                                                                        <strong
                                                                            class="font-monospace fs-6 ms-1">{{ $catObtained }}</strong>
                                                                        <span style="opacity: 0.7;">/
                                                                            {{ $catMax }}</span>
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td class="text-center bg-light">
                                                            @php
                                                                $percent =
                                                                    $totalMax > 0
                                                                        ? ($totalObtained / $totalMax) * 100
                                                                        : 0;
                                                                $finalColor = $percent < 50 ? '#b91c1c' : '#047857';
                                                            @endphp
                                                            <div class="fw-bolder font-monospace fs-5"
                                                                style="color: {{ $finalColor }} !important;">
                                                                {{ $totalObtained }}
                                                                <span class="text-muted font-13 fs-6">/
                                                                    {{ $totalMax }}</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted bg-light">
                                        <i
                                            class="mdi mdi-clipboard-text-off-outline fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                        <h6 class="fw-bold text-dark">السجل الأكاديمي فارغ.</h6>
                                        <p class="font-14 mb-0">لا توجد درجات أو تقييمات مرصودة لهذا الطالب حتى الآن.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 🔥 6. الحضور والغياب 🔥 --}}
                    <div class="tab-pane fade" id="v-pills-attendance" role="tabpanel">
                        <div class="card shadow-sm border-0 rounded-4 mb-4 border-top border-secondary border-4">
                            <div class="card-header bg-white border-bottom py-4">
                                <h5 class="mb-0 fw-bold text-dark">
                                    <i class="mdi mdi-calendar-check text-secondary me-2 fs-4 align-middle"></i> سجل الحضور
                                    والغياب
                                </h5>
                            </div>
                            <div class="card-body p-4 p-md-5">
                                @if (isset($attendanceStats))
                                    {{-- 🔥 بطاقات الإحصائيات المحسّنة 🔥 --}}
                                    <div class="row text-center mb-5 g-3">
                                        <div class="col-4">
                                            <div class="stat-box present">
                                                <div class="stat-icon">
                                                    <i class="mdi mdi-check"></i>
                                                </div>
                                                <h3 class="fw-bolder font-monospace mb-1 stat-value">
                                                    {{ $attendanceStats['present'] }}
                                                </h3>
                                                <small class="fw-bold font-13" style="color: #064e3b !important;">
                                                    أيام الحضور
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-box absent">
                                                <div class="stat-icon">
                                                    <i class="mdi mdi-close"></i>
                                                </div>
                                                <h3 class="fw-bolder font-monospace mb-1 stat-value">
                                                    {{ $attendanceStats['absent'] }}
                                                </h3>
                                                <small class="fw-bold font-13" style="color: #7f1d1d !important;">
                                                    أيام الغياب
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-box late">
                                                <div class="stat-icon">
                                                    <i class="mdi mdi-clock-alert"></i>
                                                </div>
                                                <h3 class="fw-bolder font-monospace mb-1 stat-value">
                                                    {{ $attendanceStats['late'] }}
                                                </h3>
                                                <small class="fw-bold font-13" style="color: #78350f !important;">
                                                    مرات التأخير
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive border rounded-3 shadow-sm"
                                        style="max-height: 350px; overflow-y: auto;">
                                        <table class="table table-custom table-hover mb-0 text-center">
                                            <thead class="bg-light sticky-top">
                                                <tr>
                                                    <th>التاريخ الفعلي</th>
                                                    <th>حالة الانضباط (الحضور)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($attendances ?? [] as $att)
                                                    <tr>
                                                        <td dir="ltr" class="text-dark font-monospace fw-bold fs-6">
                                                            {{ \Carbon\Carbon::parse($att->attendance_date)->format('Y-m-d') }}
                                                        </td>
                                                        <td>
                                                            @if ($att->status == 'present')
                                                                <span class="status-badge success">
                                                                    <i class="mdi mdi-check"></i> حاضر
                                                                </span>
                                                            @elseif($att->status == 'absent')
                                                                <span class="status-badge danger">
                                                                    <i class="mdi mdi-close"></i> غائب
                                                                </span>
                                                            @elseif($att->status == 'late')
                                                                <span class="status-badge warning">
                                                                    <i class="mdi mdi-clock-alert"></i> متأخر
                                                                </span>
                                                            @else
                                                                <span class="status-badge secondary">
                                                                    <i class="mdi mdi-account-question"></i> بعذر
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-muted py-5 bg-white">
                                                            <i
                                                                class="mdi mdi-calendar-remove fs-1 d-block mb-2 opacity-50"></i>
                                                            <h6 class="fw-bold">لم يتم رصد أي غياب أو حضور.</h6>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted bg-light rounded-4">
                                        <i class="mdi mdi-calendar-blank fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                        <h5 class="fw-bold text-dark">البيانات غير متاحة.</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 🔥 7. سجل التحويلات 🔥 --}}
                    <div class="tab-pane fade" id="v-pills-transfers" role="tabpanel">
                        <div class="card shadow-sm border-0 rounded-4 border-top border-primary border-4">
                            <div
                                class="card-header bg-white border-bottom py-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <h5 class="fw-bold mb-0" style="color: #1d4ed8 !important;">
                                    <i class="mdi mdi-swap-horizontal-bold me-1 fs-4 align-middle"></i> السجل التاريخي
                                    للتحويلات
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table
                                        class="table table-bordered table-custom table-hover mb-0 text-center align-middle">
                                        <thead class="sticky-top bg-light" style="z-index: 5;">
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th>التاريخ</th>
                                                <th>مسار النقل (من - إلى)</th>
                                                <th class="bg-light-blue">رسوم التحويل</th>
                                                <th class="bg-light-green">المدفوع</th>
                                                <th class="bg-light-red">المتبقي</th>
                                                <th>حالة الطلب</th>
                                                <th>المرفقات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transfers ?? [] as $index => $tr)
                                                <tr>
                                                    <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                                    <td dir="ltr" class="font-monospace fw-bold text-muted">
                                                        {{ \Carbon\Carbon::parse($tr->transfer_date)->format('Y-m-d') }}
                                                    </td>
                                                    <td>
                                                        <span class="status-badge secondary mb-1 d-block">
                                                            {{ $tr->fromBranch->name_ar ?? '---' }}
                                                        </span>
                                                        <i class="mdi mdi-arrow-down text-muted"></i>
                                                        <span class="status-badge info d-block mt-1">
                                                            {{ $tr->toBranch->name_ar ?? '---' }}
                                                        </span>
                                                    </td>
                                                    <td class="bg-light-blue font-monospace fw-bolder fs-6">
                                                        {{ number_format($tr->transfer_fee, 3) }} OMR
                                                    </td>
                                                    <td class="bg-light-green font-monospace fw-bolder fs-6">
                                                        {{ number_format($tr->paid_amount, 3) }} OMR
                                                    </td>
                                                    <td class="bg-light-red font-monospace fw-bolder fs-6">
                                                        {{ number_format($tr->remaining_amount, 3) }} OMR
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusClasses = [
                                                                'pending_parent' => 'warning',
                                                                'pending_ministry' => 'info',
                                                                'pending_payment' => 'danger',
                                                                'completed' => 'success',
                                                                'rejected' => 'secondary',
                                                            ];
                                                            $statusLabels = [
                                                                'pending_parent' => 'بانتظار الموافقة',
                                                                'pending_ministry' => 'موافقة الوزارة',
                                                                'pending_payment' => 'بانتظار السداد',
                                                                'completed' => 'مكتمل',
                                                                'rejected' => 'مرفوض',
                                                            ];
                                                            $statusIcons = [
                                                                'pending_parent' => 'mdi-clock-outline',
                                                                'pending_ministry' => 'mdi-shield-check',
                                                                'pending_payment' => 'mdi-cash',
                                                                'completed' => 'mdi-check-circle',
                                                                'rejected' => 'mdi-close-circle',
                                                            ];
                                                        @endphp
                                                        <span
                                                            class="status-badge {{ $statusClasses[$tr->status] ?? 'secondary' }}">
                                                            <i
                                                                class="mdi {{ $statusIcons[$tr->status] ?? 'mdi-help-circle' }}"></i>
                                                            {{ $statusLabels[$tr->status] ?? $tr->status }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.student-transfers.print', $tr->id) }}"
                                                            target="_blank" class="action-icon view"
                                                            title="طباعة الإخلاء والتعهد">
                                                            <i class="mdi mdi-printer"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-muted text-center py-5 bg-light">
                                                        <i class="mdi mdi-swap-horizontal fs-1 d-block mb-3 text-secondary opacity-50"
                                                            style="font-size: 50px;"></i>
                                                        <h6 class="fw-bold text-dark">لا يوجد أي سجل تحويلات مسجل لهذا
                                                            الطالب.</h6>
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
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.btn-confirm-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'تأكيد الحذف النهائي؟',
                    text: "سيتم مسح العقد المالي وإرجاع أي أرصدة، لا يمكن التراجع عن هذا الإجراء!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف العقد',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
