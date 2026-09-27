@extends('backend.layouts.master')
@section('title', 'قائمة الطلاب الشاملة')

@push('css')
    {{-- مكتبة التوقيع للتحويلات --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* 🌟 Premium Table & Badges 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
            border-top: 5px solid #3b82f6;
        }

        .student-avatar {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .avatar-title {
            width: 45px;
            height: 45px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .premium-badge {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

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
            padding: 16px;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* ✅ الأزرار */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            color: #fff !important;
            border: none;
            transition: all 0.2s;
            margin: 0 2px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 14px;
            gap: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            color: #fff !important;
        }

        .btn-view {
            background-color: #475569;
        }

        .btn-view:hover {
            background-color: #334155;
        }

        .btn-edit {
            background-color: #f59e0b;
        }

        .btn-edit:hover {
            background-color: #d97706;
        }

        .btn-transfer {
            background-color: #0ea5e9;
        }

        .btn-transfer:hover {
            background-color: #0284c7;
        }

        .btn-payment {
            background-color: #10b981;
        }

        .btn-payment:hover {
            background-color: #059669;
        }

        /* ✅ كارت حالة التحويل المالي */
        .transfer-status-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 8px;
            font-size: 11px;
            min-width: 140px;
        }

        .transfer-status-card.paid {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            border-color: #10b981;
        }

        .transfer-status-card.unpaid {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-color: #ef4444;
        }

        .transfer-amount {
            font-family: 'Courier New', monospace;
            font-weight: 800;
            font-size: 13px;
        }

        .filter-bar {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }

        /* 🌟 Signature Pad 🌟 */
        .signature-wrapper {
            border: 2px dashed #3b82f6 !important;
            border-radius: 10px;
            background: #f8fafc;
            overflow: hidden;
            position: relative;
        }

        .signature-wrapper canvas {
            cursor: crosshair;
            touch-action: none;
            width: 100%;
            height: 200px;
        }

        .signature-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #94a3b8;
            pointer-events: none;
            user-select: none;
            font-size: 30px;
            opacity: 0.4;
        }

        /* 🌟 Custom Premium Pagination 🌟 */
        .pagination {
            margin-bottom: 0;
            gap: 6px;
        }

        .page-item .page-link {
            border-radius: 8px !important;
            border: none;
            color: #475569;
            font-weight: 700;
            padding: 8px 16px;
            transition: all 0.2s ease;
            background-color: #f8fafc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .page-item.active .page-link {
            background-color: #3b82f6;
            color: white !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }

        .page-item .page-link:hover:not(.active) {
            background-color: #e2e8f0;
            color: #1e293b;
            transform: translateY(-1px);
        }

        .page-item.disabled .page-link {
            background-color: transparent;
            color: #cbd5e1;
            box-shadow: none;
        }

        /* Print */
        @media print {
            body {
                background: #fff !important;
            }

            .no-print,
            .filter-bar,
            .navbar-custom,
            .left-side-menu,
            .footer,
            .action-col {
                display: none !important;
            }

            .card-premium {
                box-shadow: none !important;
                border: 1px solid #ddd;
                border-top: none;
            }

            .table-responsive {
                max-height: none !important;
                overflow: visible !important;
            }

            .table-erp th,
            .table-erp td {
                border: 1px solid #ccc !important;
                white-space: normal !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="row mb-3 mt-2 d-print-none">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold mb-1 text-dark">
                        <i class="mdi mdi-account-group text-primary me-1 fs-3 align-middle"></i> السجل الشامل للطلاب
                    </h4>
                    <p class="text-muted mb-0 font-14">إدارة الملفات الأكاديمية والشخصية وتحديث حالات القيد.</p>
                </div>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    {{-- زر التحديث --}}
                    <button class="btn btn-light border shadow-sm" type="button" onclick="location.reload();" title="تحديث"
                        style="color: #475569 !important;">
                        <i class="mdi mdi-refresh"></i> تحديث
                    </button>

                    {{-- زر الطباعة --}}
                    <button class="btn btn-info text-white border-0 shadow-sm" type="button" onclick="window.print();"
                        title="طباعة">
                        <i class="mdi mdi-printer"></i> طباعة
                    </button>

                    {{-- زر تسجيل طالب جديد --}}
                    @can('create_students')
                        <a href="{{ route('admin.students.create') }}"
                            class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold text-white">
                            <i class="mdi mdi-account-plus me-1"></i>
                            تسجيل طالب جديد
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="filter-bar d-print-none">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label font-13 fw-bold text-muted">البحث السريع</label>
                    <div class="input-group shadow-sm rounded-3">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="mdi mdi-magnify text-primary"></i></span>
                        <input type="text" id="quickSearch" class="form-control border-start-0 ps-0 fw-bold"
                            placeholder="ابحث بالاسم، ولي الأمر، الهاتف...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-13 fw-bold text-muted">الفرع</label>
                    <select class="form-select shadow-sm fw-bold" id="filterBranch">
                        <option value="">-- كل الفروع --</option>
                        @foreach ($students->pluck('branch')->unique('id')->filter() as $branch)
                            <option value="{{ $branch->name_ar }}">{{ $branch->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-13 fw-bold text-muted">الصف</label>
                    <select class="form-select shadow-sm fw-bold" id="filterGrade">
                        <option value="">-- كل الصفوف --</option>
                        @foreach ($students->pluck('grade')->unique('id')->filter() as $grade)
                            <option value="{{ $grade->name_ar }}">{{ $grade->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-end mt-4 pt-1">
                    <span
                        class="badge  bg-opacity-10 text-primary px-3 py-2 font-14 rounded-pill shadow-sm border border-primary border-opacity-25 w-100">
                        <i class="mdi mdi-counter"></i> الإجمالي: <span
                            class="font-monospace fs-5 ms-1">{{ $students->total() }}</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div
                class="alert alert-success alert-dismissible fade show auto-hide-alert shadow-sm rounded-3 fw-bold border-0">
                <i class="mdi mdi-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 border-0 fw-bold">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Data Table --}}
        <div class="card card-premium shadow-sm border-0 mb-5">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-hover table-erp text-center align-middle mb-0" id="studentsTable">
                        <thead class="sticky-top" style="z-index: 10;">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th class="text-start ps-4">بيانات الطالب</th>
                                <th class="text-start ps-3">ولي الأمر</th>
                                <th>التسكين</th>
                                <th>حالة التحويل</th>
                                <th>حالة القيد</th>
                                @canany(['view_students', 'edit_students', 'transfer_students'])
                                    <th class="action-col" style="width: 200px;">الإجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $index => $student)
                                @php
                                    $activeTransfer = $student->activeTransfers->first();
                                @endphp
                                <tr class="student-row">
                                    <td><span class="fw-bold text-muted">{{ $students->firstItem() + $index }}</span></td>

                                    <td class="text-start ps-4">
                                        <div class="d-flex align-items-center">
                                            @if ($student->student_photo)
                                                <img src="{{ asset('storage/' . $student->student_photo) }}" alt="student"
                                                    class="rounded-circle student-avatar me-3">
                                            @else
                                                <div
                                                    class="avatar-title  bg-opacity-10 text-primary rounded-circle font-18 fw-bold me-3 border border-primary border-opacity-25 shadow-sm">
                                                    {{ mb_substr($student->first_name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="m-0 font-15 fw-bold text-dark student-name">
                                                    {{ $student->first_name }} {{ $student->second_name ?? '' }}
                                                    {{ $student->tribe }}
                                                </h6>
                                                <small class="text-muted font-monospace student-id">
                                                    <i
                                                        class="mdi mdi-identifier me-1"></i>{{ $student->national_id ?? 'بدون رقم' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-start ps-3">
                                        <h6 class="m-0 font-14 text-dark fw-bold parent-name">
                                            {{ $student->parent->name_ar ?? 'غير محدد' }}</h6>
                                        <small class="text-muted font-monospace parent-phone" dir="ltr">
                                            <i class="mdi mdi-phone  me-1"></i>{{ $student->parent->phone ?? '---' }}
                                        </small>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column gap-2 align-items-center">
                                            <span class="premium-badge w-100 justify-content-center branch-cell shadow-sm">
                                                <i class="mdi mdi-domain text-info"></i>
                                                {{ $student->branch->name_ar ?? 'الرئيسي' }}
                                            </span>
                                            <span class="premium-badge w-100 justify-content-center grade-cell shadow-sm">
                                                <i class="mdi mdi-school-outline "></i>
                                                {{ $student->grade->name_ar ?? '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        @if ($activeTransfer)
                                            @php
                                                $transferClass = $activeTransfer->isFullyPaid()
                                                    ? 'paid'
                                                    : ($activeTransfer->hasPartialPayment()
                                                        ? ''
                                                        : 'unpaid');
                                            @endphp
                                            <div class="transfer-status-card {{ $transferClass }} shadow-sm">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="fw-bold text-muted">رسوم التحويل</small>
                                                    <span
                                                        class="transfer-amount">{{ number_format($activeTransfer->transfer_fee, 3) }}</span>
                                                </div>
                                                <hr class="my-1 opacity-25">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="fw-bold ">✓ المدفوع</small>
                                                    <span
                                                        class="transfer-amount ">{{ number_format($activeTransfer->paid_amount, 3) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="fw-bold text-danger">✗ المتبقي</small>
                                                    <span
                                                        class="transfer-amount text-danger">{{ number_format($activeTransfer->remaining_amount, 3) }}</span>
                                                </div>
                                                @if ($activeTransfer->remaining_amount > 0)
                                                    <hr class="my-1 opacity-25">
                                                    <div class="text-center mt-1">
                                                        <small class="badge bg-warning text-dark px-2 py-1">
                                                            <i class="mdi mdi-clock-outline"></i>
                                                            {{ $activeTransfer->getPaymentStatusLabel() }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <hr class="my-1 opacity-25">
                                                    <div class="text-center mt-1">
                                                        <small class="badge bg-success text-white px-2 py-1">
                                                            <i class="mdi mdi-check-circle"></i> مدفوع بالكامل
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted border px-3 py-2">
                                                <i class="mdi mdi-information-outline"></i> لا يوجد تحويل
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            $statusColors = [
                                                'active' => 'success',
                                                'graduated' => 'info',
                                                'suspended' => 'warning',
                                                'transferred' => 'secondary',
                                                'withdrawn' => 'danger',
                                            ];
                                            $statusLabels = [
                                                'active' => 'مقيد نشط',
                                                'graduated' => 'متخرج',
                                                'suspended' => 'موقوف',
                                                'transferred' => 'محول',
                                                'withdrawn' => 'منسحب',
                                            ];
                                            $statusIcons = [
                                                'active' => 'check-circle',
                                                'graduated' => 'school',
                                                'suspended' => 'pause-circle',
                                                'transferred' => 'swap-horizontal',
                                                'withdrawn' => 'exit-to-app',
                                            ];
                                            $color = $statusColors[$student->status] ?? 'success';
                                            $label = $statusLabels[$student->status] ?? 'مقيد نشط';
                                            $icon = $statusIcons[$student->status] ?? 'check-circle';
                                        @endphp
                                        <span
                                            class="badge bg-{{ $color }} text-white px-3 py-2 font-12 shadow-sm d-inline-flex align-items-center gap-1"
                                            style="font-weight: 700; min-width: 120px; justify-content: center;">
                                            <i class="mdi mdi-{{ $icon }}"></i>
                                            {{ $label }}
                                        </span>
                                    </td>
                                    @canany(['view_students', 'edit_students', 'transfer_students'])
                                        <td class="action-col">
                                            <div class="d-flex gap-1 justify-content-center">
                                                @can('view_students')
                                                    <a href="{{ route('admin.students.show', $student->id) }}"
                                                        class="action-btn btn-view shadow-sm" title="ملف الطالب">
                                                        <i class="mdi mdi-account-details"></i> عرض
                                                    </a>
                                                @endcan

                                                @can('edit_students')
                                                    <a href="{{ route('admin.students.edit', $student->id) }}"
                                                        class="action-btn btn-edit shadow-sm" title="تعديل">
                                                        <i class="mdi mdi-pencil"></i> تعديل
                                                    </a>
                                                @endcan

                                                @can('transfer_students')
                                                    @php
                                                        $totalDebt = $student->contracts->sum('total_remaining') ?? 0;
                                                    @endphp
                                                    <button type="button"
                                                        class="action-btn btn-transfer shadow-sm open-transfer-modal"
                                                        data-id="{{ $student->id }}"
                                                        data-name="{{ $student->first_name }} {{ $student->tribe }}"
                                                        data-branch="{{ $student->branch->name_ar ?? 'الرئيسي' }}"
                                                        data-debt="{{ $totalDebt }}" title="نقل طالب">
                                                        <i class="mdi mdi-swap-horizontal-bold"></i> نقل
                                                    </button>
                                                @endcan

                                                @if ($activeTransfer && $activeTransfer->remaining_amount > 0)
                                                    @can('collect_installments')
                                                        <button type="button"
                                                            class="action-btn btn-payment shadow-sm open-payment-modal"
                                                            data-transfer-id="{{ $activeTransfer->id }}"
                                                            data-remaining="{{ number_format($activeTransfer->remaining_amount, 3, '.', '') }}"
                                                            data-student-name="{{ $student->first_name }} {{ $student->tribe }}"
                                                            title="سداد دفعة من التحويل">
                                                            <i class="mdi mdi-cash-multiple"></i> سداد
                                                        </button>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i
                                            class="mdi mdi-account-search-outline fs-1 d-block mb-3 text-muted opacity-50"></i>
                                        <h5 class="fw-bold text-dark">لا يوجد طلاب مقيدين.</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 الترقيم (Pagination) بتصميم بريميوم 🔥 --}}
            @if ($students->hasPages())
                <div class="card-footer bg-white border-top-0 py-4 d-flex justify-content-center">
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

        {{-- 🔥 Transfer Modal 🔥 --}}
        @can('transfer_students')
            <div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg rounded-4 border-top border-primary border-4">
                        <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                            <h5 class="modal-title font-bold text-primary"><i
                                    class="mdi mdi-swap-horizontal-bold me-1 fs-4 align-middle"></i> نقل الطالب وتصفية الرسوم
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form id="transferForm" method="POST" action="" class="prevent-double-submit">
                            @csrf
                            <div class="modal-body p-4 text-start bg-white">

                                {{-- معلومات الطالب والديون السابقة --}}
                                <div
                                    class="alert border-info border-opacity-25 bg-info bg-opacity-10 border-start border-4 shadow-sm mb-4 d-flex justify-content-between align-items-center rounded-3 flex-wrap gap-2">
                                    <div>
                                        <span class="d-block text-muted font-12 fw-bold mb-1">اسم الطالب المُنقول</span>
                                        <span id="modal-student-name" class="font-bold text-dark font-15"></span>
                                    </div>
                                    <div class="text-center">
                                        <span class="d-block text-muted font-12 fw-bold mb-1">المديونية السابقة (الفرع
                                            الحالي)</span>
                                        <span id="modal-student-debt"
                                            class="badge bg-danger font-14 font-monospace shadow-sm px-3 py-1">0.000 OMR</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="d-block text-muted font-12 fw-bold mb-1">الفرع الحالي المقيد به</span>
                                        <span id="modal-current-branch"
                                            class="badge bg-secondary font-13 shadow-sm px-3 py-1"></span>
                                    </div>
                                </div>

                                {{-- تفاصيل النقل --}}
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">الفرع المراد النقل إليه <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select fw-bold shadow-sm border-primary" name="to_branch_id"
                                            id="modal_to_branch_id" required>
                                            <option value="" selected disabled>-- اختر الفرع الجديد --</option>
                                            @foreach (\App\Models\Branche::where('is_active', 1)->get() as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">العام الدراسي (الجديد) <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select fw-bold shadow-sm" name="to_academic_year_id"
                                            id="modal_to_academic_year_id" required disabled>
                                            <option value="" selected disabled>اختر الفرع أولاً...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted font-13 fw-bold">رسوم التحويل الإدارية <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-light text-primary border-primary fw-bold"><i
                                                    class="mdi mdi-cash-multiple"></i></span>
                                            <input type="number" step="0.001"
                                                class="form-control fw-bold font-monospace text-primary border-primary text-center"
                                                name="transfer_fee" id="transfer_fee_input" value="10.000" min="0"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                {{-- البلوك المالي المحدث --}}
                                <div
                                    class="row g-3 mb-4 p-4 bg-warning bg-opacity-10 rounded-3 border border-warning border-start border-4 shadow-sm">
                                    <div class="col-12 mb-1">
                                        <p class="text-dark fw-bold font-13 m-0"><i
                                                class="mdi mdi-information-outline text-warning me-1 fs-5 align-middle"></i>
                                            سيتم إنشاء <b>سند قبض (Payment)</b> آلياً بقيمة "المدفوع الآن"، وما يتبقى سيُسجل
                                            كمديونية على الطالب.
                                        </p>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label text-dark font-13 fw-bold">طريقة السداد <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select fw-bold border-warning shadow-sm" name="payment_type"
                                            id="payment_type" required>
                                            <option value="full">دفع بالكامل</option>
                                            <option value="installment">دفع جزئي / أقساط</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label text-dark font-13 fw-bold">المدفوع الآن (OMR) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.001" min="0"
                                            class="form-control fw-bold shadow-sm text-center border-warning text-danger"
                                            name="paid_amount" id="transfer_paid_amount" value="10.000" required>
                                    </div>

                                    <div class="col-md-3 d-none" id="due_date_div">
                                        <label class="form-label text-dark font-13 fw-bold">تاريخ استحقاق الباقي <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control fw-bold shadow-sm text-center"
                                            name="due_date" id="transfer_due_date"
                                            value="{{ date('Y-m-d', strtotime('+1 month')) }}">
                                    </div>

                                    <div class="col-md-3" id="account_div">
                                        <label class="form-label text-dark font-13 fw-bold">إيداع في الخزينة <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select fw-bold shadow-sm" name="financial_account_id"
                                            id="financial_account_id" required>
                                            @foreach (\App\Models\FinancialAccount::where('is_active', 1)->where('account_type', 'asset')->get() as $account)
                                                <option value="{{ $account->id }}">{{ $account->name_ar }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3" id="payment_method_div">
                                        <label class="form-label text-dark font-13 fw-bold">وسيلة الدفع</label>
                                        <select class="form-select fw-bold shadow-sm" name="payment_method"
                                            id="payment_method">
                                            <option value="cash">نقدي (Cash)</option>
                                            <option value="card">بطاقة بنكية (Card)</option>
                                            <option value="transfer">تحويل بنكي</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label text-muted font-13 fw-bold"><i
                                                class="mdi mdi-text-box-outline me-1"></i> أسباب التحويل / ملاحظات الإدارة
                                            <span class="text-danger">*</span></label>
                                        <textarea class="form-control bg-light shadow-sm fw-bold" name="transfer_reason" rows="2"
                                            placeholder="يرجى كتابة سبب طلب النقل لاعتماده في السجل المالي للإدارة..." required></textarea>
                                    </div>
                                </div>

                                {{-- صندوق التوقيع الإلكتروني --}}
                                <div class="row mb-2">
                                    <div class="col-md-10 mx-auto">
                                        <label class="form-label text-danger d-block text-center font-14 fw-bold mb-3"><i
                                                class="mdi mdi-draw"></i> توقيع ولي الأمر بالموافقة على النقل والرسوم المترتبة
                                            <span class="text-danger">*</span></label>
                                        <div class="signature-wrapper shadow-sm">
                                            <i class="mdi mdi-draw signature-placeholder"></i>
                                            <canvas id="transfer-signature-pad" class="w-100 bg-transparent"></canvas>
                                        </div>
                                        <div class="mt-3 text-center">
                                            <button type="button" id="clear-transfer-signature"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-4 fw-bold shadow-sm">
                                                <i class="mdi mdi-eraser me-1"></i> مسح وإعادة التوقيع
                                            </button>
                                        </div>
                                        <input type="hidden" name="signature_data" id="transfer_signature_data" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top-0 rounded-bottom-4 justify-content-between px-4">
                                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm"
                                    data-bs-dismiss="modal">إلغاء الأمر</button>
                                <button type="submit"
                                    class="btn btn-primary rounded-pill px-5 font-bold shadow-sm btn-submit"
                                    id="save-transfer-btn">
                                    <i class="mdi mdi-check-decagram me-1 fs-5 align-middle"></i> اعتماد التحويل ماليًا وفتح
                                    الطلب
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

        {{-- 🔥 Payment Modal 🔥 --}}
        @can('collect_installments')
            <div class="modal fade" id="paymentModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 border-top border-success border-4">
                        <div class="modal-header bg-success text-white rounded-top-4">
                            <h5 class="modal-title fw-bold">
                                <i class="mdi mdi-cash-multiple me-2"></i> سداد دفعة من رسوم التحويل
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="paymentForm" method="POST" action="">
                            @csrf
                            <div class="modal-body p-4 bg-white">
                                <div class="alert alert-info border-0 shadow-sm mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted d-block fw-bold">الطالب</small>
                                            <strong id="payment-student-name" class="text-dark"></strong>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block fw-bold">المتبقي</small>
                                            <strong id="payment-remaining" class="text-danger fs-5 font-monospace"></strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold text-dark">المبلغ المراد سداده (OMR) <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-success text-white border-success"><i
                                                    class="mdi mdi-cash"></i></span>
                                            <input type="number" step="0.001" min="0.001"
                                                class="form-control fw-bold font-monospace text-center border-success"
                                                name="amount" id="payment-amount" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">الخزنة <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select fw-bold shadow-sm" name="financial_account_id" required>
                                            @foreach (\App\Models\FinancialAccount::where('is_active', 1)->where('account_type', 'asset')->get() as $account)
                                                <option value="{{ $account->id }}">{{ $account->name_ar }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">وسيلة الدفع <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select fw-bold shadow-sm" name="payment_method" required>
                                            <option value="cash">💵 نقدي</option>
                                            <option value="card">💳 بطاقة</option>
                                            <option value="transfer">🏦 تحويل بنكي</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">تاريخ الدفع <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control fw-bold shadow-sm" name="payment_date"
                                            value="{{ date('Y-m-d') }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark">ملاحظات</label>
                                        <input type="text" class="form-control shadow-sm" name="notes"
                                            placeholder="اختياري">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold"
                                    data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
                                    <i class="mdi mdi-check-circle me-1"></i> تأكيد السداد
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            setTimeout(() => $('.auto-hide-alert').fadeOut('slow'), 4000);

            // البحث السريع
            $("#quickSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $(".student-row").filter(function() {
                    var text = $(this).find('.student-name').text().toLowerCase() + " " +
                        $(this).find('.student-id').text().toLowerCase() + " " +
                        $(this).find('.parent-name').text().toLowerCase() + " " +
                        $(this).find('.parent-phone').text().toLowerCase();
                    $(this).toggle(text.indexOf(value) > -1);
                });
            });

            $("#filterBranch").on("change", function() {
                var value = $(this).val().toLowerCase();
                $(".student-row").filter(function() {
                    $(this).toggle($(this).find('.branch-cell').text().toLowerCase().indexOf(
                        value) > -1 || value === "");
                });
            });

            $("#filterGrade").on("change", function() {
                var value = $(this).val().toLowerCase();
                $(".student-row").filter(function() {
                    $(this).toggle($(this).find('.grade-cell').text().toLowerCase().indexOf(value) >
                        -1 || value === "");
                });
            });
        });
    </script>

    @can('collect_installments')
        <script>
            $(document).ready(function() {
                // فتح مودال سداد دفعة
                $('.open-payment-modal').click(function() {
                    let transferId = $(this).data('transfer-id');
                    let remaining = parseFloat($(this).data('remaining'));
                    let studentName = $(this).data('student-name');

                    $('#payment-student-name').text(studentName);
                    $('#payment-remaining').text(remaining.toFixed(3) + ' OMR');
                    $('#payment-amount').attr('max', remaining).val(remaining.toFixed(3));

                    let localeUrl = "{{ url(app()->getLocale()) }}";
                    $('#paymentForm').attr('action', `${localeUrl}/admin/students/transfers/${transferId}/pay`);

                    $('#paymentModal').modal('show');
                });

                // تأمين مودال السداد
                $('#paymentForm').on('submit', function(e) {
                    let amount = parseFloat($('#payment-amount').val());
                    let max = parseFloat($('#payment-amount').attr('max'));

                    if (amount > max) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'مبلغ غير صحيح',
                            text: `المبلغ أكبر من المتبقي (${max.toFixed(3)} OMR)`,
                            confirmButtonColor: '#ef4444'
                        });
                        return false;
                    }

                    let btn = $(this).find('button[type="submit"]');
                    btn.prop('disabled', true).html(
                        '<i class="mdi mdi-loading mdi-spin me-2"></i> جاري المعالجة...');
                });
            });
        </script>
    @endcan

    @can('transfer_students')
        <script>
            $(document).ready(function() {
                // 3. Transfer Modal & Signature Pad Logic
                var canvas = document.getElementById('transfer-signature-pad');
                var signaturePad = null;

                $('#transferModal').on('shown.bs.modal', function() {
                    if (!signaturePad && canvas) {
                        var ratio = Math.max(window.devicePixelRatio || 1, 1);
                        canvas.width = canvas.offsetWidth * ratio;
                        canvas.height = canvas.offsetHeight * ratio;
                        canvas.getContext("2d").scale(ratio, ratio);

                        signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgba(255, 255, 255, 0)',
                            penColor: 'rgb(37, 99, 235)',
                            minWidth: 1.5,
                            maxWidth: 3
                        });

                        signaturePad.onBegin = function() {
                            $('.signature-placeholder').fadeOut();
                        };
                    } else if (signaturePad) {
                        signaturePad.clear();
                        $('.signature-placeholder').fadeIn();
                    }
                });

                $('#clear-transfer-signature').on('click', function() {
                    if (signaturePad) {
                        signaturePad.clear();
                        $('.signature-placeholder').fadeIn();
                    }
                });

                // 🔥 AJAX: جلب السنوات الدراسية للفرع الجديد جوا المودال 🔥
                $('#modal_to_branch_id').on('change', function() {
                    let branchId = $(this).val();
                    let yearSelect = $('#modal_to_academic_year_id');

                    yearSelect.html('<option value="" selected disabled>⏳ جاري التحميل...</option>').prop(
                        'disabled', true);

                    if (branchId) {
                        let localeUrl = "{{ url(app()->getLocale()) }}";
                        $.get(`${localeUrl}/admin/get-branch-academic-years/${branchId}`, function(data) {
                            let options =
                                '<option value="" selected disabled>-- اختر العام الدراسي --</option>';
                            if (data && data.length > 0) {
                                $.each(data, function(index, year) {
                                    options +=
                                        `<option value="${year.id}">${year.name}</option>`;
                                });
                                yearSelect.html(options).prop('disabled', false);
                            } else {
                                yearSelect.html(
                                    '<option value="" disabled>لا توجد سنوات دراسية لهذا الفرع</option>'
                                );
                            }
                        }).fail(function() {
                            yearSelect.html(
                                '<option value="" disabled>حدث خطأ، حاول مرة أخرى</option>');
                        });
                    }
                });

                // 🔥 لوجيك الماليات الذكي داخل مودال التحويل 🔥
                let feeInput = $('#transfer_fee_input');
                let paidInput = $('#transfer_paid_amount');
                let paymentType = $('#payment_type');

                function checkPaidAmount() {
                    let paid = parseFloat(paidInput.val()) || 0;
                    if (paid > 0) {
                        $('#account_div').removeClass('d-none');
                        $('#financial_account_id').prop('required', true);
                        $('#payment_method_div').removeClass('d-none');
                        $('#payment_method').prop('required', true);
                    } else {
                        $('#account_div').addClass('d-none');
                        $('#financial_account_id').prop('required', false);
                        $('#payment_method_div').addClass('d-none');
                        $('#payment_method').prop('required', false);
                    }
                }

                feeInput.on('input', function() {
                    if (paymentType.val() === 'full') {
                        paidInput.val($(this).val());
                        checkPaidAmount();
                    }
                });

                paymentType.on('change', function() {
                    if ($(this).val() === 'installment') {
                        paidInput.val(0);
                        $('#due_date_div').removeClass('d-none');
                        $('#transfer_due_date').prop('required', true);
                    } else {
                        paidInput.val(feeInput.val());
                        $('#due_date_div').addClass('d-none');
                        $('#transfer_due_date').prop('required', false);
                    }
                    checkPaidAmount();
                });

                paidInput.on('input', function() {
                    checkPaidAmount();
                });

                // فتح المودال وتجهيزه برمجياً (بالداتا المالية)
                $('.open-transfer-modal').click(function() {
                    let id = $(this).data('id');
                    let name = $(this).data('name');
                    let branch = $(this).data('branch');
                    let debt = parseFloat($(this).data('debt')).toFixed(3);

                    $('#modal-student-name').text(name);
                    $('#modal-current-branch').text(branch);
                    $('#modal-student-debt').text(debt + " OMR");

                    let localeUrl = "{{ url(app()->getLocale()) }}";
                    $('#transferForm').attr('action', `${localeUrl}/admin/students/${id}/transfer`);

                    $('#modal_to_branch_id').val('');
                    $('#modal_to_academic_year_id').html(
                        '<option value="" selected disabled>اختر الفرع أولاً...</option>').prop('disabled',
                        true);

                    feeInput.val('10.000');
                    paidInput.val('10.000');
                    paymentType.val('full').trigger('change');
                    $('textarea[name="transfer_reason"]').val('');

                    $('#transferModal').modal('show');
                });

                // تأمين فورم المودال
                $('#transferForm').on('submit', function(e) {
                    if (signaturePad && signaturePad.isEmpty()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'يرجى إدراج توقيع ولي الأمر لاعتماد التحويل والرسوم.',
                            confirmButtonColor: '#3b82f6'
                        });
                        $('.signature-wrapper').css('border-color', '#ef4444');
                        setTimeout(() => {
                            $('.signature-wrapper').css('border-color', '#3b82f6');
                        }, 1500);
                        return false;
                    } else {
                        $('#transfer_signature_data').val(signaturePad.toDataURL("image/png"));
                        let btn = $('#save-transfer-btn');
                        btn.prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2 fs-5 align-middle"></i> جاري حفظ ومعالجة الطلب المالي...'
                        );
                    }
                });
            });
        </script>
    @endcan
@endpush
