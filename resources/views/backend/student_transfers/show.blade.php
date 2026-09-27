@extends('backend.layouts.master')
@section('title', 'تفاصيل نقل الطالب')
@section('title_page', 'تفاصيل طلب النقل الداخلي')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        .info-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .info-table th {
            background-color: #f8fafc;
            width: 30%;
            color: #475569;
            font-weight: 800;
            font-size: 13px;
            padding: 14px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-table td {
            padding: 14px;
            font-weight: 600;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        /* شريط المراحل المتطور (Stepper) */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            position: relative;
        }

        .stepper-item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            z-index: 2;
        }

        .stepper-item::before {
            content: "";
            position: absolute;
            top: 25px;
            right: -50%;
            width: 100%;
            height: 4px;
            background-color: #e2e8f0;
            z-index: -1;
            transition: 0.4s ease-in-out;
        }

        .stepper-item:first-child::before {
            display: none;
        }

        .stepper-item.completed::before {
            background-color: #10b981;
        }

        .stepper-item.active::before {
            background-color: #3b82f6;
        }

        .stepper-item .step-counter {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #e2e8f0;
            font-size: 22px;
            color: #94a3b8;
            transition: 0.4s;
            box-shadow: 0 0 0 5px #fff;
        }

        .stepper-item.completed .step-counter {
            background-color: #10b981;
            border-color: #10b981;
            color: #fff;
        }

        .stepper-item.completed .step-name {
            color: #10b981;
            font-weight: 800;
        }

        .stepper-item.active .step-counter {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: #fff;
            box-shadow: 0 0 0 5px #fff, 0 0 15px rgba(59, 130, 246, 0.4);
            transform: scale(1.1);
        }

        .stepper-item.active .step-name {
            color: #3b82f6;
            font-weight: 900;
        }

        .stepper-item .step-name {
            margin-top: 15px;
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            transition: 0.3s;
            text-align: center;
            line-height: 1.4;
        }

        /* الأزرار العلوية */
        .btn-header {
            padding: 10px 20px;
            font-weight: 800;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            color: white !important;
            transition: 0.3s;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-header:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            color: white !important;
        }

        .btn-back {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        }

        .btn-print {
            background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
        }

        .btn-ministry {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        }

        .btn-pay {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .btn-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        /* Print Settings */
        @media print {
            body {
                background: #fff !important;
            }

            .no-print,
            .navbar-custom,
            .left-side-menu,
            .footer,
            .btn-header {
                display: none !important;
            }

            .card-premium {
                box-shadow: none !important;
                border: 1px solid #ddd;
                break-inside: avoid;
            }

            .stepper-item .step-counter {
                border: 2px solid #000;
                color: #000;
                box-shadow: none;
            }

            .info-table th,
            .info-table td {
                border: 1px solid #000;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        @if (session('success'))
            <div class="alert alert-success fw-bold shadow-sm rounded-3 border-0">
                <i class='bx bx-check-circle me-1 align-middle fs-5'></i> {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger fw-bold shadow-sm rounded-3 border-0">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class='bx bx-error-circle me-1'></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $status = $transfer->status;
            $isCompleted = $status == 'completed';
            $isCancelled = in_array($status, ['cancelled', 'rejected']);
        @endphp

        {{-- 1. شريط مراحل التحويل (Stepper) --}}
        <div class="card card-premium shadow-sm border-0 mb-4 border-top border-primary border-4">
            <div class="card-body py-4 px-2 px-md-5">
                @if ($isCancelled)
                    <div
                        class="alert alert-danger text-center m-0 fw-bold fs-5 rounded-3 border-0 shadow-sm bg-danger bg-opacity-10 text-danger">
                        <i class='bx bx-x-circle fs-3 align-middle me-1'></i> تم إلغاء أو إيقاف طلب النقل الداخلي هذا.
                    </div>
                @else
                    <div class="stepper-wrapper">
                        <div class="stepper-item {{ $status == 'pending_parent' ? 'active' : 'completed' }}">
                            <div class="step-counter">
                                <i class='bx {{ $status == 'pending_parent' ? 'bx-edit-alt' : 'bx-check' }}'></i>
                            </div>
                            <div class="step-name">توقيع الإقرار<br><small class="text-muted fw-bold">تقديم الطلب</small>
                            </div>
                        </div>

                        <div
                            class="stepper-item {{ $status == 'pending_ministry' ? 'active' : (in_array($status, ['pending_payment', 'completed']) ? 'completed' : '') }}">
                            <div class="step-counter">
                                <i
                                    class='bx {{ $status == 'pending_ministry' ? 'bx-certification' : (in_array($status, ['pending_payment', 'completed']) ? 'bx-check' : 'bx-time') }}'></i>
                            </div>
                            <div class="step-name">موافقة الوزارة<br><small class="text-muted fw-bold">إجراء حكومي</small>
                            </div>
                        </div>

                        <div
                            class="stepper-item {{ $status == 'pending_payment' ? 'active' : ($isCompleted ? 'completed' : '') }}">
                            <div class="step-counter">
                                <i
                                    class='bx {{ $status == 'pending_payment' ? 'bx-wallet' : ($isCompleted ? 'bx-check' : 'bx-time') }}'></i>
                            </div>
                            <div class="step-name">دفع الرسوم<br><small class="text-muted fw-bold">اعتماد مالي</small></div>
                        </div>

                        <div class="stepper-item {{ $isCompleted ? 'completed' : '' }}">
                            <div class="step-counter">
                                <i class='bx {{ $isCompleted ? 'bx-check-double' : 'bx-user-check' }}'></i>
                            </div>
                            <div class="step-name">تم النقل<br><small class="text-muted fw-bold">بالفرع الجديد</small></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. رأس الصفحة والأزرار --}}
        <div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-3 d-print-none">
            <h5 class="fw-bold mb-0 text-dark">
                تفاصيل نقل الطالب: <span class="text-primary">{{ $transfer->student->first_name }}
                    {{ $transfer->student->last_name }}</span>
            </h5>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.student-transfers.index') }}" class="btn-header btn-back">
                    <i class='bx bx-arrow-back fs-5'></i>
                    <span>رجوع للقائمة</span>
                </a>

                <a href="{{ route('admin.student-transfers.print', $transfer->id) }}" target="_blank"
                    class="btn-header btn-print">
                    <i class='bx bx-printer fs-5'></i>
                    <span>طباعة الإستمارة</span>
                </a>

                @can('edit_student_transfers')
                    @if ($status == 'pending_ministry')
                        <button type="button" class="btn-header btn-ministry" data-bs-toggle="modal"
                            data-bs-target="#ministryApprovalModal">
                            <i class='bx bx-certification fs-5'></i>
                            <span>موافقة الوزارة</span>
                        </button>
                    @endif

                    @if ($status == 'pending_payment')
                        <button type="button" class="btn-header btn-pay" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class='bx bx-money fs-5'></i>
                            <span>سداد وإتمام النقل</span>
                        </button>
                    @endif

                    @if (!$isCompleted && !$isCancelled)
                        <form action="{{ route('admin.student-transfers.cancel', $transfer->id) }}" method="POST"
                            class="d-inline confirm-cancel-form">
                            @csrf
                            <button type="button" class="btn-header btn-cancel btn-confirm-cancel">
                                <i class='bx bx-x-circle fs-5'></i>
                                <span>إلغاء الطلب</span>
                            </button>
                        </form>
                    @endif
                @endcan

                @can('delete_student_transfers')
                    @if ($status != 'completed')
                        <form action="{{ route('admin.student-transfers.destroy', $transfer->id) }}" method="POST"
                            class="d-inline confirm-delete-form">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-header btn-delete btn-confirm-delete">
                                <i class='bx bx-trash fs-5'></i>
                                <span>حذف نهائي</span>
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>

        <div class="row g-4">
            {{-- 3. بيانات النقل الأساسية (اليمين) --}}
            <div class="col-xl-7 col-lg-6">
                <div class="card card-premium shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-4 border-bottom-0 rounded-top-4">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class='bx bx-info-circle text-primary me-1 fs-5 align-middle'></i> السجل الإداري لطلب النقل
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0 info-table border-start-0 border-end-0">
                            <tbody>
                                <tr>
                                    <th>الفرع الحالي (المُرسِل)</th>
                                    <td class="fw-bold text-danger">
                                        <i class='bx bx-store-alt me-1'></i>
                                        {{ $transfer->fromBranch->name_ar ?? 'محذوف' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>الفرع الجديد (المُستقبِل)</th>
                                    <td class="fw-bold text-success">
                                        <i class='bx bx-store me-1'></i> {{ $transfer->toBranch->name_ar ?? 'محذوف' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>تاريخ تقديم الطلب</th>
                                    <td class="font-monospace fw-bold text-dark">
                                        {{ \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>مُلاحظات أو سبب التحويل</th>
                                    <td class="text-muted fw-bold">
                                        {{ $transfer->transfer_reason ?? 'لا توجد ملاحظات مسجلة.' }}
                                    </td>
                                </tr>

                                @if ($transfer->ministry_approval_number)
                                    <tr class="bg-light">
                                        <th>رقم القرار الوزاري / الإداري</th>
                                        <td class="text-info fw-bolder font-monospace fs-6">
                                            {{ $transfer->ministry_approval_number }}
                                        </td>
                                    </tr>
                                    <tr class="bg-light">
                                        <th>تاريخ الموافقة والتصديق</th>
                                        <td class="font-monospace fw-bold">
                                            {{ \Carbon\Carbon::parse($transfer->ministry_approval_date)->format('Y-m-d') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 4. بيانات الطالب والمالية (اليسار) --}}
            <div class="col-xl-5 col-lg-6">
                <div class="card card-premium shadow-sm border-0 h-100 bg-light">
                    <div class="card-header bg-transparent py-4 text-center border-bottom-0">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class='bx bx-user text-primary me-1 fs-5 align-middle'></i> ملخص الطالب والحالة المالية
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="text-center mb-4 bg-white p-4 rounded-4 shadow-sm border">
                            <div class="avatar-lg mx-auto mb-3">
                                <span
                                    class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle fs-1 shadow-sm"
                                    style="width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class='bx bx-user' style="font-size: 40px; color:white"></i>
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">{{ $transfer->student->first_name }}
                                {{ $transfer->student->last_name }}</h5>
                            <p class="text-muted mb-0 font-13">كود ومرجع الطالب: <span
                                    class="font-monospace fw-bold text-dark">{{ $transfer->student->id }}</span></p>
                            <span class="badge bg-secondary mt-3 shadow-sm px-3 py-2 fs-6">
                                {{ $transfer->student->grade->name_ar ?? 'الصف غير محدد' }}
                            </span>
                        </div>

                        <hr class="border-secondary opacity-25">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-muted d-flex align-items-center gap-2">
                                <i class='bx bx-wallet' style="font-size: 18px; color: #3b82f6;"></i>
                                إجمالي رسوم النقل:
                            </span>
                            <h5 class="fw-bolder text-primary mb-0 font-monospace">
                                {{ number_format($transfer->transfer_fee, 3) }} <small
                                    class="fs-6 text-muted font-sans">OMR</small>
                            </h5>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-muted d-flex align-items-center gap-2">
                                <i class='bx bx-check-circle' style="font-size: 18px; color: #10b981;"></i>
                                المدفوع:
                            </span>
                            <h5 class="fw-bolder text-success mb-0 font-monospace">
                                {{ number_format($transfer->paid_amount ?? 0, 3) }} <small
                                    class="fs-6 text-muted font-sans">OMR</small>
                            </h5>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold text-muted d-flex align-items-center gap-2">
                                <i class='bx bx-x-circle' style="font-size: 18px; color: #ef4444;"></i>
                                المتبقي:
                            </span>
                            <h5 class="fw-bolder text-danger mb-0 font-monospace">
                                {{ number_format($transfer->remaining_amount ?? $transfer->transfer_fee, 3) }} <small
                                    class="fs-6 text-muted font-sans">OMR</small>
                            </h5>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-muted d-flex align-items-center gap-2">
                                <i class='bx bx-shield' style="font-size: 18px; color: #64748b;"></i>
                                حالة الخزينة والدفع:
                            </span>
                            @if ($transfer->is_fee_paid)
                                <span
                                    class="badge bg-success text-white px-3 py-2 fs-6 shadow-sm d-inline-flex align-items-center gap-1">
                                    <i class='bx bx-check-double' style="font-size: 16px;"></i>
                                    تم السداد بالكامل
                                </span>
                            @else
                                <span
                                    class="badge bg-danger text-white px-3 py-2 fs-6 shadow-sm d-inline-flex align-items-center gap-1">
                                    <i class='bx bx-time' style="font-size: 16px;"></i>
                                    مديونية غير مسددة
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= مودال موافقة الوزارة ================= --}}
    @if ($status == 'pending_ministry')
        @can('edit_student_transfers')
            <div class="modal fade" id="ministryApprovalModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow-lg border-top border-info border-4">
                        <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                            <h5 class="modal-title fw-bold text-info">
                                <i class='bx bx-certification me-1 fs-4 align-middle'></i> توثيق موافقة الوزارة/المديرية
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.student-transfers.approve-ministry', $transfer->id) }}" method="POST"
                            class="prevent-double-submit">
                            @csrf
                            <div class="modal-body p-4 bg-white text-start">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted font-13">رقم القرار / الموافقة الرسمية <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="ministry_approval_number"
                                        class="form-control fw-bold font-monospace shadow-sm border-info text-center"
                                        placeholder="أدخل رقم الخطاب الرسمي..." required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-bold text-muted font-13">تاريخ اعتماد القرار <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="ministry_approval_date"
                                        class="form-control fw-bold shadow-sm text-center" value="{{ date('Y-m-d') }}"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 bg-light rounded-bottom-4 justify-content-between px-4">
                                <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                    data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit"
                                    class="btn btn-info text-white px-5 fw-bold rounded-pill shadow-sm btn-submit">
                                    <i class='bx bx-check-shield me-1 fs-5 align-middle'></i> حفظ واعتماد الموافقة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    @endif

    {{-- ================= مودال الدفع وإنهاء النقل المحدث ================= --}}
    @if ($status == 'pending_payment')
        @can('edit_student_transfers')
            <div class="modal fade" id="paymentModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow-lg border-top border-success border-4">
                        <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                            <h5 class="modal-title fw-bold text-success">
                                <i class='bx bx-money me-1 fs-4 align-middle'></i> تحصيل الرسوم واعتماد النقل
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.student-transfers.finalize', $transfer->id) }}" method="POST"
                            id="form_payment" class="prevent-double-submit">
                            @csrf
                            <div class="modal-body p-4 text-center bg-white">
                                <p class="text-muted fw-bold font-13 mb-2">إجمالي رسوم النقل الإدارية</p>
                                <h2 class="text-primary fw-bolder mb-4 font-monospace bg-primary bg-opacity-10 p-3 rounded-3 d-inline-block border border-primary border-opacity-25"
                                    id="modal_fee_amount" data-fee="{{ $transfer->transfer_fee }}">
                                    {{ number_format($transfer->transfer_fee, 3) }} OMR
                                </h2>

                                <div class="text-start mt-2">
                                    {{-- 🌟 نوع التسوية المالية 🌟 --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted font-13">طريقة تسوية الرسوم <span
                                                class="text-danger">*</span></label>
                                        <select name="payment_status" id="payment_status"
                                            class="form-select fw-bold shadow-sm border-dark" required>
                                            <option value="paid">سداد فوري (دفع كامل المبلغ الآن)</option>
                                            <option value="partial">سداد جزء وتقسيط/تأجيل الباقي</option>
                                            <option value="deferred">آجل (تأجيل الرسوم وتسجيلها كمديونية)</option>
                                        </select>
                                    </div>

                                    <div id="payment_details_wrapper">
                                        <div class="row">
                                            <div class="col-md-6 mb-3" id="paid_amount_div" style="display: none;">
                                                <label class="form-label fw-bold text-muted font-13">المبلغ المدفوع <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.001" name="paid_amount"
                                                    id="paid_amount_input"
                                                    class="form-control text-center fw-bold border-success shadow-sm"
                                                    value="{{ $transfer->transfer_fee }}">
                                            </div>
                                            <div class="col-md-6 mb-3" id="payment_method_div">
                                                <label class="form-label fw-bold text-muted font-13">طريقة الدفع <span
                                                        class="text-danger">*</span></label>
                                                <select name="payment_method" id="payment_method_select"
                                                    class="form-select fw-bold shadow-sm" required>
                                                    <option value="cash">نقدي (Cash)</option>
                                                    <option value="card">بطاقة بنكية (Card)</option>
                                                    <option value="transfer">تحويل بنكي</option>
                                                </select>
                                            </div>
                                            <div class="col-12 mb-3" id="financial_account_div">
                                                <label class="form-label fw-bold text-muted font-13">الخزينة / الحساب للإيداع
                                                    <span class="text-danger">*</span></label>
                                                <select name="financial_account_id" id="financial_account_select"
                                                    class="form-select fw-bold border-success shadow-sm" required>
                                                    <option value="" disabled selected>-- اختر الحساب المالي --</option>
                                                    @foreach (\App\Models\FinancialAccount::where('account_type', 'asset')->where('is_active', 1)->get() as $acc)
                                                        <option value="{{ $acc->id }}">{{ $acc->name_ar }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="alert alert-warning border-warning border-opacity-25 bg-warning bg-opacity-10 rounded-3 mt-3 mb-0 text-dark fw-bold font-12 text-center shadow-sm">
                                        <i class='bx bx-info-circle fs-5 align-middle me-1'></i> عند التأكيد، سيتم نقل الطالب
                                        فعلياً وإنشاء السجلات المالية أو المديونية تلقائياً.
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 bg-light rounded-bottom-4 justify-content-between px-4">
                                <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                    data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-success px-5 fw-bold rounded-pill shadow-sm btn-submit">
                                    <i class='bx bx-check-double me-1 fs-5 align-middle'></i> تنفيذ ونقل الطالب
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    @endif

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // منع الإرسال المزدوج وتحويل الأزرار لوضع التحميل
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-2 fs-5 align-middle"></i> جاري تنفيذ الطلب...');
            });

            // SweetAlert لتأكيد إلغاء الطلب
            $('.btn-confirm-cancel').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'هل تريد إلغاء طلب النقل؟',
                    text: "سيتم إيقاف عملية النقل وستبقى ملفات الطالب في الفرع الحالي.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، إلغاء الطلب',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // SweetAlert لتأكيد الحذف النهائي
            $('.btn-confirm-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'هل أنت متأكد من الحذف؟',
                    text: "سيتم مسح هذا السجل بالكامل من النظام ولن يمكن التراجع!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف السجل',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // 🔥 الذكاء المالي الخاص بمودال الدفع 🔥
            $('#payment_status').change(function() {
                let status = $(this).val();
                let fee = parseFloat($('#modal_fee_amount').data('fee')) || 0;

                if (status === 'deferred') {
                    // آجل: إخفاء تفاصيل الدفع وإلغاء الإجبار
                    $('#payment_details_wrapper').slideUp();
                    $('#financial_account_select, #payment_method_select').removeAttr('required');
                    $('#paid_amount_input').removeAttr('required').val(0);
                } else {
                    // نقدي أو جزء: إظهار التفاصيل وإعادة الإجبار
                    $('#payment_details_wrapper').slideDown();
                    $('#financial_account_select, #payment_method_select').attr('required', 'required');

                    if (status === 'partial') {
                        $('#paid_amount_div').slideDown();
                        $('#paid_amount_input').attr('required', 'required').val('');
                    } else { // سداد كامل
                        $('#paid_amount_div').slideUp();
                        $('#paid_amount_input').removeAttr('required').val(fee);
                    }
                }
            });

            // ضبط القيم الافتراضية عند فتح المودال
            $('#paymentModal').on('shown.bs.modal', function() {
                $('#payment_status').val('paid').trigger('change');
            });
        });
    </script>
@endpush
