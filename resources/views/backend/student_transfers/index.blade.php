@extends('backend.layouts.master')
@section('title', 'نقل الطلاب بين الفروع')
@section('title_page', 'إدارة طلبات تحويل الطلاب')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
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

        /* 🌟 Select2 Modern Tweaks 🌟 */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            font-weight: 600;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }

        /* الأزرار الموحدة */
        .action-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: white !important;
            border: none;
            transition: 0.2s ease-in-out;
            margin: 0 3px;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-view {
            background-color: #64748b;
        }

        .btn-print {
            background-color: #8b5cf6;
        }

        .btn-ministry {
            background-color: #0dcaf0;
        }

        .btn-pay {
            background-color: #10b981;
        }

        .btn-cancel {
            background-color: #ef4444;
        }

        .branch-path {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .branch-badge {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: bold;
        }

        .from-branch {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .to-branch {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
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
            padding: 14px;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px;
            text-align: center;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
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
            height: 150px;
        }

        .signature-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #94a3b8;
            pointer-events: none;
            user-select: none;
            font-size: 20px;
            opacity: 0.4;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .bg-light {
            background-color: #323a46 !important;
            border-color: #424e5a;
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

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .from-branch {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        body[data-layout-color="dark"] .to-branch {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.2);
            color: #86efac;
        }

        body[data-layout-color="dark"] .signature-wrapper {
            background-color: #3b4652;
            border-color: #60a5fa !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- الإشعارات والأخطاء --}}
        @if (session('success'))
            <div class="alert alert-success fw-bold shadow-sm rounded-3 border-0"><i
                    class="bx bx-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger fw-bold shadow-sm rounded-3 border-0">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row mb-3 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold text-dark mb-1"><i
                            class="bx bx-transfer-alt text-primary me-1 fs-3 align-middle"></i> سجل طلبات نقل الطلاب</h4>
                    <p class="text-muted font-14 mb-0">متابعة الموافقات الوزارية وتسوية الرسوم للانتقال بين الفروع.</p>
                </div>

                @can('create_student_transfers')
                    <div>
                        <button class="btn btn-primary fw-bold rounded-pill shadow-sm px-4" data-bs-toggle="modal"
                            data-bs-target="#createTransferModal">
                            <i class="bx bx-plus-circle me-1 align-middle fs-5"></i> طلب نقل جديد
                        </button>
                    </div>
                @endcan
            </div>
        </div>

        <div class="card card-premium shadow-sm border-0 border-top border-primary border-4 mb-4">
            {{-- قسم الفلاتر --}}
            <div class="card-header bg-white py-4 border-bottom-0 rounded-top-4">
                <form action="{{ route('admin.student-transfers.index') }}" method="GET"
                    class="prevent-double-submit bg-light p-3 rounded-3 border border-light shadow-sm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label font-13 fw-bold text-muted">البحث باسم الطالب</label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="mdi mdi-magnify text-primary"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0 fw-bold"
                                    placeholder="ابحث عن الطالب..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-13 fw-bold text-muted">مرحلة الطلب (الحالة)</label>
                            <select name="status" class="form-select select2-filter shadow-sm fw-bold">
                                <option value="">-- كل الحالات --</option>
                                <option value="pending_parent"
                                    {{ request('status') == 'pending_parent' ? 'selected' : '' }}>انتظار توقيع ولي الأمر
                                </option>
                                <option value="pending_ministry"
                                    {{ request('status') == 'pending_ministry' ? 'selected' : '' }}>انتظار موافقة الوزارة
                                </option>
                                <option value="pending_payment"
                                    {{ request('status') == 'pending_payment' ? 'selected' : '' }}>انتظار دفع الرسوم
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل (تم
                                    النقل)</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي /
                                    مرفوض</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-dark flex-grow-1 shadow-sm fw-bold btn-submit">
                                <i class="bx bx-filter-alt me-1"></i> تصفية النتائج
                            </button>
                            <a href="{{ route('admin.student-transfers.index') }}"
                                class="btn btn-light border px-3 shadow-sm text-dark fw-bold" title="إعادة ضبط الفلاتر">
                                <i class="bx bx-reset fs-5 align-middle"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- الجدول --}}
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                    <table class="table table-erp table-hover text-center align-middle mb-0">
                        <thead class="sticky-top" style="z-index: 10;">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th class="text-start ps-4">الطالب المُنقول</th>
                                <th>مسار النقل والفروع</th>
                                <th>تاريخ الطلب</th>
                                <th class="text-start">البيان المالي (OMR)</th>
                                <th>المرحلة الحالية</th>
                                @canany(['view_student_transfers', 'edit_student_transfers'])
                                    <th style="width: 160px;">الإجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transfers as $index => $transfer)
                                <tr>
                                    <td class="text-muted fw-bold">
                                        {{ method_exists($transfers, 'firstItem') ? $transfers->firstItem() + $index : $index + 1 }}
                                    </td>

                                    <td class="text-start ps-4">
                                        <div class="fw-bold text-dark fs-6">{{ $transfer->student->first_name }}
                                            {{ $transfer->student->last_name }}</div>
                                        <small class="text-muted font-monospace"><i class="mdi mdi-identifier"></i> كود:
                                            {{ $transfer->student->id }}</small>
                                    </td>

                                    <td>
                                        <div class="branch-path">
                                            <span
                                                class="branch-badge from-branch shadow-sm">{{ $transfer->fromBranch->name_ar ?? 'محذوف' }}</span>
                                            <i class="bx bx-right-arrow-alt text-muted fs-4"></i>
                                            <span
                                                class="branch-badge to-branch shadow-sm">{{ $transfer->toBranch->name_ar ?? 'محذوف' }}</span>
                                        </div>
                                    </td>

                                    <td class="text-muted fw-bold font-monospace">
                                        {{ \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') }}
                                    </td>

                                    <td class="text-start" style="font-size: 13px; min-width: 180px;">
                                        <div class="d-flex justify-content-between border-bottom border-light pb-1 mb-1">
                                            <span class="text-muted fw-bold">الإجمالي:</span>
                                            <span
                                                class="text-dark fw-bolder font-monospace">{{ number_format($transfer->transfer_fee, 3) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom border-light pb-1 mb-1">
                                            <span class="text-muted fw-bold">المدفوع:</span>
                                            <span
                                                class="text-success fw-bolder font-monospace">{{ number_format($transfer->paid_amount ?? 0, 3) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted fw-bold">المتبقي:</span>
                                            <span
                                                class="text-danger fw-bolder font-monospace">{{ number_format($transfer->remaining_amount ?? $transfer->transfer_fee, 3) }}</span>
                                        </div>
                                    </td>

                                    <td>
                                        @if ($transfer->status == 'pending_parent')
                                            <span class="badge bg-warning text-dark px-3 py-1 shadow-sm"><i
                                                    class="bx bx-edit-alt me-1"></i> توقيع ولي الأمر</span>
                                        @elseif ($transfer->status == 'pending_ministry')
                                            <span class="badge bg-info text-dark px-3 py-1 shadow-sm"><i
                                                    class="bx bx-building-house me-1"></i> موافقة الوزارة</span>
                                        @elseif ($transfer->status == 'pending_payment')
                                            <span class="badge bg-primary text-white px-3 py-1 shadow-sm"><i
                                                    class="bx bx-wallet me-1"></i> انتظار الدفع</span>
                                        @elseif ($transfer->status == 'completed')
                                            <span class="badge bg-success text-white px-3 py-1 shadow-sm"><i
                                                    class="bx bx-check-double me-1"></i> تم النقل بنجاح</span>
                                        @else
                                            <span class="badge bg-danger text-white px-3 py-1 shadow-sm"><i
                                                    class="bx bx-x-circle me-1"></i> ملغي / مرفوض</span>
                                        @endif
                                    </td>

                                    @canany(['view_student_transfers', 'edit_student_transfers'])
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                @can('edit_student_transfers')
                                                    @if ($transfer->status == 'pending_ministry')
                                                        <button type="button"
                                                            class="action-btn btn-ministry btn-open-ministry shadow-sm"
                                                            title="إدخال موافقة الوزارة" data-bs-toggle="modal"
                                                            data-bs-target="#ministryApprovalModal"
                                                            data-id="{{ $transfer->id }}">
                                                            <i class="bx bx-certification fs-5"></i>
                                                        </button>
                                                    @endif

                                                    @if ($transfer->status == 'pending_payment')
                                                        <button type="button" class="action-btn btn-pay btn-open-pay shadow-sm"
                                                            title="دفع الرسوم واعتماد النقل" data-bs-toggle="modal"
                                                            data-bs-target="#paymentModal" data-id="{{ $transfer->id }}"
                                                            data-fee="{{ $transfer->transfer_fee }}">
                                                            <i class="bx bx-money fs-5"></i>
                                                        </button>
                                                    @endif
                                                @endcan

                                                @can('view_student_transfers')
                                                    <a href="{{ route('admin.student-transfers.show', $transfer->id) }}"
                                                        class="action-btn btn-view shadow-sm" title="التفاصيل">
                                                        <i class="bx bx-show fs-5"></i>
                                                    </a>
                                                    <a href="{{ route('admin.student-transfers.print', $transfer->id) }}"
                                                        target="_blank" class="action-btn btn-print shadow-sm"
                                                        title="طباعة إخلاء الطرف">
                                                        <i class="bx bx-printer fs-5"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5 bg-white">
                                        <i class="bx bx-transfer text-primary opacity-25 d-block mb-3"
                                            style="font-size: 60px;"></i>
                                        <h5 class="fw-bold text-dark">لا توجد طلبات نقل حالياً</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (method_exists($transfers, 'hasPages') && $transfers->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $transfers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ================= مودال 1: طلب نقل جديد محمى ================= --}}
    @can('create_student_transfers')
        <div class="modal fade" id="createTransferModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow-lg border-top border-primary border-4">
                    <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-primary"><i class="bx bx-plus-circle me-1 align-middle fs-4"></i>
                            إنشاء طلب نقل طالب لفرع آخر</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.student-transfers.store') }}" method="POST" id="createTransferForm"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start bg-white">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-muted font-13"><i
                                            class="mdi mdi-account-search-outline me-1"></i> اختر الطالب المُراد نقله <span
                                            class="text-danger">*</span></label>
                                    <select name="student_id" class="form-select select2-ajax shadow-sm" required
                                        style="width: 100%;">
                                        <option value=""></option>
                                    </select>

                                    {{-- 🔥 إظهار فرع الطالب الحالي 🔥 --}}
                                    <div id="current_branch_alert" class="mt-2 text-info fw-bold font-13"
                                        style="display: none;">
                                        <i class="bx bx-store-alt align-middle fs-5"></i> الطالب مقيد حالياً في: <span
                                            id="current_branch_name"
                                            class="text-dark font-monospace text-decoration-underline"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-success font-13"><i class="mdi mdi-domain me-1"></i>
                                        الفرع المُراد النقل إليه <span class="text-danger">*</span></label>
                                    <select name="to_branch_id" class="form-select border-success fw-bold shadow-sm" required>
                                        <option value="" disabled selected>-- اختر الفرع المُستهدف --</option>
                                        @foreach (\App\Models\Branche::where('is_active', 1)->get() as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-danger font-13"><i class="mdi mdi-cash me-1"></i>
                                        رسوم التحويل الإدارية (OMR) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.001" name="transfer_fee" id="create_transfer_fee"
                                        class="form-control fw-bold border-danger text-center font-monospace shadow-sm"
                                        value="10.000" min="0" required>
                                </div>

                                {{-- 🔥 البلوك المالي المحدث (السداد والخزينة) 🔥 --}}
                                <div class="col-12 mt-4">
                                    <div
                                        class="row g-3 p-4 bg-warning bg-opacity-10 rounded-3 border border-warning border-start border-4 shadow-sm mx-0">
                                        <div class="col-12 mb-1">
                                            <p class="text-dark fw-bold font-13 m-0"><i
                                                    class="bx bx-info-circle text-warning me-1 fs-5 align-middle"></i>
                                                سيتم إنشاء <b>سند قبض (Payment)</b> آلياً بقيمة "المدفوع الآن"، وما يتبقى سيُسجل
                                                كمديونية على الطالب.
                                            </p>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label text-dark font-13 fw-bold">طريقة السداد <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select fw-bold border-warning shadow-sm" name="payment_type"
                                                id="create_payment_type" required>
                                                <option value="full">دفع بالكامل</option>
                                                <option value="installment">دفع جزئي / آجل</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label text-dark font-13 fw-bold">المدفوع الآن (OMR) <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.001" min="0"
                                                class="form-control fw-bold shadow-sm text-center border-warning text-danger font-monospace"
                                                name="paid_amount" id="create_paid_amount" value="10.000" required>
                                        </div>

                                        <div class="col-md-3 d-none" id="create_due_date_div">
                                            <label class="form-label text-dark font-13 fw-bold">تاريخ استحقاق الباقي <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control fw-bold shadow-sm text-center"
                                                name="due_date" id="create_due_date"
                                                value="{{ date('Y-m-d', strtotime('+1 month')) }}">
                                        </div>

                                        <div class="col-md-3" id="create_account_div">
                                            <label class="form-label text-dark font-13 fw-bold">إيداع في الخزينة <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select fw-bold shadow-sm" name="financial_account_id"
                                                id="create_financial_account_id" required>
                                                @foreach (\App\Models\FinancialAccount::where('is_active', 1)->where('account_type', 'asset')->get() as $account)
                                                    <option value="{{ $account->id }}">{{ $account->name_ar }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 mt-3" id="create_payment_method_div">
                                            <label class="form-label text-dark font-13 fw-bold">وسيلة الدفع</label>
                                            <select class="form-select fw-bold shadow-sm" name="payment_method"
                                                id="create_payment_method" required>
                                                <option value="cash">نقدي (Cash)</option>
                                                <option value="card">بطاقة بنكية (Card)</option>
                                                <option value="transfer">تحويل بنكي</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <label class="form-label fw-bold text-muted font-13"><i
                                            class="mdi mdi-text-box-outline me-1"></i> سبب التحويل <span
                                            class="text-danger">*</span></label>
                                    <textarea name="transfer_reason" class="form-control shadow-sm border-0 bg-light" rows="2"
                                        placeholder="مثال: تغيير محل السكن أو رغبة ولي الأمر..." required></textarea>
                                </div>

                                <div class="col-12 mt-3">
                                    <label class="form-label text-primary d-block text-center font-14 fw-bold mb-3"><i
                                            class="mdi mdi-draw"></i> توقيع ولي الأمر (إلزامي للطلب) <span
                                            class="text-danger">*</span></label>
                                    <div class="signature-wrapper shadow-sm">
                                        <i class="mdi mdi-draw signature-placeholder"></i>
                                        <canvas id="transfer-signature-pad" class="w-100 bg-transparent"></canvas>
                                    </div>
                                    <div class="text-center mt-2">
                                        <button type="button" id="clear-signature"
                                            class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm"><i
                                                class="bx bx-eraser me-1"></i>مسح التوقيع وإعادته</button>
                                    </div>
                                    <input type="hidden" name="signature_data" id="signature_data" required>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0 rounded-bottom-4 justify-content-between px-4 pb-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill shadow-sm btn-submit"
                                id="save-transfer-btn"><i class="bx bx-save me-1 fs-5 align-middle"></i> حفظ وفتح
                                الطلب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- ================= مودال 2: موافقة الوزارة محمى ================= --}}
    @can('edit_student_transfers')
        <div class="modal fade" id="ministryApprovalModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg border-top border-info border-4">
                    <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-info"><i class="bx bx-certification me-1 fs-4 align-middle"></i>
                            إدخال بيانات موافقة الوزارة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="form_ministry" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start bg-white">
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted font-13">رقم موافقة الوزارة/المديرية <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="ministry_approval_number"
                                    class="form-control fw-bold font-monospace shadow-sm border-info text-center"
                                    placeholder="12345/2026" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold text-muted font-13">تاريخ الموافقة <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="ministry_approval_date"
                                    class="form-control fw-bold shadow-sm text-center" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4 justify-content-between px-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit"
                                class="btn btn-info text-white px-5 fw-bold rounded-pill shadow-sm btn-submit"><i
                                    class="bx bx-check-shield me-1 fs-5 align-middle"></i> اعتماد الموافقة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- ================= مودال 3: دفع الرسوم وإنهاء النقل ================= --}}
    @can('edit_student_transfers')
        <div class="modal fade" id="paymentModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg border-top border-success border-4">
                    <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-success"><i class="bx bx-money me-1 fs-4 align-middle"></i> تحصيل
                            الرسوم واعتماد النقل</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="form_payment" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-center bg-white">
                            <p class="text-muted fw-bold font-13 mb-2">إجمالي رسوم النقل الإدارية</p>
                            <h2 class="text-primary fw-bolder mb-4 font-monospace bg-primary bg-opacity-10 p-3 rounded-3 d-inline-block border border-primary border-opacity-25"
                                id="modal_fee_amount">0.000 OMR</h2>

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
                                            <input type="number" step="0.001" name="paid_amount" id="paid_amount_input"
                                                class="form-control text-center fw-bold border-success shadow-sm">
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
                                            <label class="form-label fw-bold text-muted font-13">الخزينة / الحساب للإيداع <span
                                                    class="text-danger">*</span></label>
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
                                    <i class="bx bx-info-circle fs-5 align-middle me-1"></i> عند التأكيد، سيتم نقل الطالب
                                    فعلياً وإنشاء السجلات المالية أو المديونية تلقائياً.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4 justify-content-between px-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success px-5 fw-bold rounded-pill shadow-sm btn-submit"><i
                                    class="bx bx-check-double me-1 fs-5 align-middle"></i> تنفيذ ونقل الطالب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? "rtl" : "ltr"
            });

            // 🔥 إعداد بحث الطلاب عبر AJAX مع عرض الفرع الحالي للمستخدم 🔥
            $('.select2-ajax').select2({
                theme: "bootstrap-5",
                dir: isRtl ? "rtl" : "ltr",
                placeholder: "-- ابحث باسم الطالب، كود الطالب، أو الرقم المدني --",
                dropdownParent: $('#createTransferModal'),
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('admin.api.students.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.first_name + ' ' + (item.last_name || item
                                        .tribe || '') + ' (كود: ' + item.id + ')',
                                    id: item.id,
                                    branch_name: item.branch_name,
                                    branch_id: item.branch_id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            // 🔥 الذكاء في منع اختيار نفس الفرع وعرض الفرع الحالي 🔥
            $('.select2-ajax').on('select2:select', function(e) {
                var data = e.params.data;

                if (data.branch_name) {
                    $('#current_branch_name').text(data.branch_name);
                    $('#current_branch_alert').fadeIn();

                    $('select[name="to_branch_id"] option').prop('disabled', false);
                    $('select[name="to_branch_id"] option[value="' + data.branch_id + '"]').prop('disabled',
                        true);

                    if ($('select[name="to_branch_id"]').val() == data.branch_id) {
                        $('select[name="to_branch_id"]').val('');
                    }
                }
            });

            $('.btn-open-ministry').click(function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.student-transfers.approve-ministry', ':id') }}".replace(':id',
                    id);
                $('#form_ministry').attr('action', url);
            });

            // התفاعل مع نوع التسوية في مودال الدفع لإخفاء وإظهار الحسابات
            $('#payment_status').change(function() {
                let status = $(this).val();
                let fee = parseFloat($('#modal_fee_amount').data('fee')) || 0;

                if (status === 'deferred') {
                    $('#payment_details_wrapper').slideUp();
                    $('#financial_account_select, #payment_method_select').removeAttr('required');
                    $('#paid_amount_input').removeAttr('required').val(0);
                } else {
                    $('#payment_details_wrapper').slideDown();
                    $('#financial_account_select, #payment_method_select').attr('required', 'required');

                    if (status === 'partial') {
                        $('#paid_amount_div').slideDown();
                        $('#paid_amount_input').attr('required', 'required').val('');
                    } else {
                        $('#paid_amount_div').slideUp();
                        $('#paid_amount_input').removeAttr('required').val(fee);
                    }
                }
            });

            // تمرير البيانات للمودال عند فتحه
            $('.btn-open-pay').click(function() {
                let id = $(this).data('id');
                let fee = $(this).data('fee');
                $('#modal_fee_amount').text(parseFloat(fee).toFixed(3) + ' OMR').data('fee', fee);

                let url = "{{ route('admin.student-transfers.finalize', ':id') }}".replace(':id', id);
                $('#form_payment').attr('action', url);

                $('#payment_status').val('paid').trigger('change');
            });

            // 🔥 الذكاء المالي داخل مودال الإضافة (سداد كلي، جزئي، آجل) 🔥
            let feeInput = $('#create_transfer_fee');
            let paidInput = $('#create_paid_amount');
            let paymentType = $('#create_payment_type');

            function checkPaidAmount() {
                let paid = parseFloat(paidInput.val()) || 0;
                if (paid > 0) {
                    $('#create_account_div').removeClass('d-none');
                    $('#create_financial_account_id').prop('required', true);
                    $('#create_payment_method_div').removeClass('d-none');
                    $('#create_payment_method').prop('required', true);
                } else {
                    $('#create_account_div').addClass('d-none');
                    $('#create_financial_account_id').prop('required', false);
                    $('#create_payment_method_div').addClass('d-none');
                    $('#create_payment_method').prop('required', false);
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
                    $('#create_due_date_div').removeClass('d-none');
                    $('#create_due_date').prop('required', true);
                } else {
                    paidInput.val(feeInput.val());
                    $('#create_due_date_div').addClass('d-none');
                    $('#create_due_date').prop('required', false);
                }
                checkPaidAmount();
            });

            paidInput.on('input', function() {
                checkPaidAmount();
            });


            var canvas = document.getElementById('transfer-signature-pad');
            var signaturePad = null;

            $('#createTransferModal').on('shown.bs.modal', function() {
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

            $('#clear-signature').on('click', function() {
                if (signaturePad) {
                    signaturePad.clear();
                    $('.signature-placeholder').fadeIn();
                }
            });

            $('#createTransferForm').on('submit', function(e) {
                if (signaturePad && signaturePad.isEmpty()) {
                    e.preventDefault();
                    $('.signature-wrapper').css('border-color', '#ef4444');
                    alert('يرجى توقيع ولي الأمر قبل إرسال الطلب!');
                    return false;
                } else if (signaturePad) {
                    $('#signature_data').val(signaturePad.toDataURL("image/png"));
                }

                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-2 fs-5 align-middle"></i> جاري التنفيذ...');
            });

            $('#form_ministry, #form_payment').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-2 fs-5 align-middle"></i> جاري التنفيذ...');
            });

            // SweetAlert لتأكيد الحذف والإلغاء
            $('.btn-confirm-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'تأكيد الحذف النهائي؟',
                    text: "سيتم مسح هذا الطلب بالكامل. لا يمكن التراجع!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

        });
    </script>
@endpush
