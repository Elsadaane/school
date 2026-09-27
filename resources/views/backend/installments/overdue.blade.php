@extends('backend.layouts.master')
@section('title', 'إدارة الأقساط المتأخرة (Overdue)')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .page-wrapper {
            background-color: #f4f6f9;
            padding: 25px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
        }

        /* 🌟 تصميم التابات الفخمة 🌟 */
        .custom-pills {
            background: #f8fafc;
            padding: 8px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            gap: 5px;
        }

        .custom-pills .nav-link {
            font-weight: 800;
            color: #64748b;
            border-radius: 8px;
            padding: 12px 25px;
            font-size: 15px;
            transition: 0.3s;
        }

        .custom-pills .nav-link:hover:not(.active) {
            background-color: #e2e8f0;
        }

        .custom-pills .nav-link.tab-students.active {
            background-color: #dc3545;
            color: white;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.2);
        }

        .custom-pills .nav-link.tab-purchases.active {
            background-color: #f59e0b;
            color: white;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2);
        }

        .custom-pills .nav-link.tab-assets.active {
            background-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
        }

        .table-custom {
            white-space: nowrap;
        }

        .table-custom thead th {
            background-color: #1e2b3c;
            color: white;
            padding: 15px;
            text-align: center;
            border: none;
        }

        .table-custom tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 14px;
            border-bottom: 1px dashed #eee;
            font-weight: 600;
        }

        .table-custom tbody tr:hover td {
            background-color: #f8fafc;
        }

        .font-monospace {
            font-family: 'Courier New', Courier, monospace !important;
            letter-spacing: -0.5px;
        }

        /* Dark Mode */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom thead th {
            background-color: #3b4652;
        }

        body[data-layout-color="dark"] .table-custom tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .custom-pills {
            background: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .custom-pills .nav-link {
            color: #ced4da;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold text-danger mb-1"><i
                            class="mdi mdi-alert-decagram text-danger me-1"></i> الأقساط المتأخرة المستحقة</h4>
                    <p class="text-muted mb-0 font-13">متابعة الأقساط التي تخطت تاريخ الاستحقاق ولم يتم سدادها (طلاب،
                        موردين، أصول).</p>
                </div>
                <a href="{{ route('admin.installments.index') }}"
                    class="btn btn-light rounded-pill border fw-bold shadow-sm px-4">
                    <i class="mdi mdi-arrow-left me-1"></i> العودة للسجل العام
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 rounded-3 fw-bold">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- فلاتر البحث --}}
        <div class="card card-premium mb-4 bg-white border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.installments.overdue') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-9">
                            <label class="form-label fw-bold font-13 text-muted">بحث شامل</label>
                            <input type="text" class="form-control bg-light border-0 shadow-sm fw-bold" name="search"
                                placeholder="ابحث باسم الطالب، رقم العقد، اسم المورد، أو كود الأصل..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 shadow-sm fw-bold"><i
                                    class="mdi mdi-magnify me-1"></i> بحث وتصفية</button>
                            <a href="{{ route('admin.installments.overdue') }}"
                                class="btn btn-light border shadow-sm px-3"><i class="mdi mdi-refresh"></i></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 التابات الرئيسية 🌟 --}}
        <div class="text-center mb-4">
            <ul class="nav nav-pills custom-pills" id="installments-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-students active" id="students-tab" data-bs-toggle="pill"
                        data-bs-target="#students" type="button" role="tab">
                        <i class="mdi mdi-account-alert-outline fs-5 align-middle me-1"></i> طلاب متأخرون
                        ({{ $studentOverdue->total() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-purchases" id="purchases-tab" data-bs-toggle="pill"
                        data-bs-target="#purchases" type="button" role="tab">
                        <i class="mdi mdi-cart-remove fs-5 align-middle me-1"></i> موردين متأخرون
                        ({{ $purchaseOverdue->total() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-assets" id="assets-tab" data-bs-toggle="pill" data-bs-target="#assets"
                        type="button" role="tab">
                        <i class="mdi mdi-domain-off fs-5 align-middle me-1"></i> أصول متأخرة ({{ $assetOverdue->total() }})
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="installments-tabs-content">

            {{-- ================= 1. تابة أقساط الطلاب المتأخرة ================= --}}
            <div class="tab-pane fade show active" id="students" role="tabpanel">
                <div class="card card-premium overflow-hidden border-top border-danger border-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover table-nowrap mb-0 align-middle text-center">
                                <thead style="background-color: #dc3545;">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th class="text-start ps-4">اسم الطالب</th>
                                        <th>ولي الأمر / تواصل</th>
                                        <th>مبلغ القسط</th>
                                        <th>المدفوع</th>
                                        <th>الباقي (المتأخر)</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الحالة</th>
                                        <th width="15%">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($studentOverdue as $index => $inst)
                                        @php
                                            $remaining = $inst->amount - $inst->paid_amount;
                                            $studentName = $inst->student->first_name ?? 'بدون اسم';
                                            $parentPhone = $inst->student->parent->phone ?? '';
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $parentPhone);
                                            $whatsappMessage =
                                                "مرحباً ولي أمر الطالب/ـة ({$studentName})،\nنود تذكيركم بوجود قسط مدرسي متأخر بقيمة *" .
                                                number_format($remaining, 3) .
                                                " OMR*، وتاريخ استحقاقه كان في {$inst->due_date}.\nيرجى المبادرة بالسداد لتجنب غرامات التأخير. شكراً لكم.";
                                            $whatsappUrl =
                                                "https://wa.me/{$cleanPhone}?text=" . urlencode($whatsappMessage);
                                        @endphp
                                        <tr>
                                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                            <td class="text-start ps-4 fw-bold text-dark">
                                                {{ $studentName }} {{ $inst->student->last_name ?? '' }}
                                                <div class="text-muted font-11 mt-1 font-monospace">رقم العقد:
                                                    #{{ $inst->contract->contract_number ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <div class="text-dark fw-bold">{{ $inst->student->parent->name_ar ?? '-' }}
                                                </div>
                                                <small class="text-muted font-monospace d-block mt-1"
                                                    dir="ltr">{{ $parentPhone }}</small>
                                            </td>
                                            <td class="font-monospace fw-bold">{{ number_format($inst->amount, 3) }}</td>
                                            <td class="text-success font-monospace fw-bold">
                                                {{ number_format($inst->paid_amount, 3) }}</td>
                                            <td class="text-danger font-monospace fw-black fs-5">
                                                {{ number_format($remaining, 3) }}</td>
                                            <td class="font-monospace text-danger fw-bold">
                                                {{ \Carbon\Carbon::parse($inst->due_date)->format('Y-m-d') }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill">متأخر</span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if ($cleanPhone)
                                                        <a href="{{ $whatsappUrl }}" target="_blank"
                                                            class="btn btn-sm btn-success rounded-circle shadow-sm"
                                                            data-bs-toggle="tooltip" title="مراسلة عبر الواتساب">
                                                            <i class="mdi mdi-whatsapp font-16"></i>
                                                        </a>
                                                    @endif
                                                    @can('pay_student_installments')
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger px-3 fw-bold text-white shadow-sm rounded-pill open-student-modal"
                                                            data-id="{{ $inst->id }}"
                                                            data-remaining="{{ $remaining }}"
                                                            data-name="{{ $studentName }}">
                                                            <i class="mdi mdi-cash-register me-1"></i> تحصيل إيراد
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="p-5 text-muted text-center fw-bold"><i
                                                    class="mdi mdi-check-decagram display-4 opacity-25 d-block mb-2 text-success"></i>
                                                لا توجد أقساط طلاب متأخرة حالياً.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($studentOverdue->hasPages())
                        <div class="card-footer bg-white border-0 pt-3 pb-3">
                            {{ $studentOverdue->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>

            {{-- ================= 2. تابة أقساط الموردين المتأخرة ================= --}}
            <div class="tab-pane fade" id="purchases" role="tabpanel">
                <div class="card card-premium overflow-hidden border-top border-warning border-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover table-nowrap mb-0 align-middle text-center">
                                <thead style="background-color: #f59e0b;">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th class="text-start ps-4">اسم المورد / التاجر</th>
                                        <th>الفاتورة المرتبطة</th>
                                        <th>مبلغ القسط</th>
                                        <th>المتبقي للسداد</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الحالة</th>
                                        <th width="15%">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseOverdue as $index => $inst)
                                        @php $remaining = $inst->amount - $inst->paid_amount; @endphp
                                        <tr>
                                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                            <td class="text-start ps-4 fw-bold text-dark">
                                                {{ $inst->supplier->company_name ?? '---' }}</td>
                                            <td class="font-monospace text-primary fw-bold">
                                                {{ $inst->invoice->invoice_number ?? '-' }}</td>
                                            <td class="font-monospace fw-bold">{{ number_format($inst->amount, 3) }}</td>
                                            <td class="text-danger font-monospace fw-black fs-5">
                                                {{ number_format($remaining, 3) }}</td>
                                            <td class="font-monospace text-danger fw-bold">
                                                {{ \Carbon\Carbon::parse($inst->due_date)->format('Y-m-d') }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 rounded-pill">متأخر</span>
                                            </td>
                                            <td>
                                                @can('manage_installments')
                                                    <button type="button"
                                                        class="btn btn-sm btn-warning px-3 fw-bold text-dark shadow-sm rounded-pill open-purchase-modal"
                                                        data-id="{{ $inst->id }}" data-remaining="{{ $remaining }}"
                                                        data-name="{{ $inst->supplier->company_name ?? '' }}">
                                                        <i class="mdi mdi-cash-minus me-1"></i> صرف للمورد
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="p-5 text-muted text-center fw-bold"><i
                                                    class="mdi mdi-check-decagram display-4 opacity-25 d-block mb-2 text-success"></i>
                                                لا توجد أقساط موردين متأخرة حالياً.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($purchaseOverdue->hasPages())
                        <div class="card-footer bg-white border-0 pt-3 pb-3">
                            {{ $purchaseOverdue->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>

            {{-- ================= 3. تابة أقساط الأصول المتأخرة ================= --}}
            <div class="tab-pane fade" id="assets" role="tabpanel">
                <div class="card card-premium overflow-hidden border-top border-primary border-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom table-hover table-nowrap mb-0 align-middle text-center">
                                <thead style="background-color: #3b82f6;">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th class="text-start ps-4">كود الأصل وبياناته</th>
                                        <th>مبلغ القسط المطلوب</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الحالة</th>
                                        <th width="15%">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assetOverdue as $index => $inst)
                                        <tr>
                                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                            <td class="text-start ps-4 fw-bold text-dark">
                                                <span
                                                    class="font-monospace text-primary">{{ $inst->asset->asset_code ?? '---' }}</span>
                                                <div class="text-muted font-12 mt-1">
                                                    {{ \Illuminate\Support\Str::limit($inst->asset->transaction_details ?? '', 30) }}
                                                </div>
                                            </td>
                                            <td class="text-danger font-monospace fw-black fs-5">
                                                {{ number_format($inst->amount, 3) }}</td>
                                            <td class="font-monospace text-danger fw-bold">
                                                {{ \Carbon\Carbon::parse($inst->due_date)->format('Y-m-d') }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill">متأخر
                                                    السداد</span>
                                            </td>
                                            <td>
                                                @can('manage_installments')
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary px-3 fw-bold text-white shadow-sm rounded-pill open-asset-modal"
                                                        data-id="{{ $inst->id }}" data-amount="{{ $inst->amount }}"
                                                        data-name="{{ $inst->asset->asset_code ?? '' }}">
                                                        <i class="mdi mdi-cash-minus me-1"></i> سداد الأصل
                                                    </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="p-5 text-muted text-center fw-bold"><i
                                                    class="mdi mdi-check-decagram display-4 opacity-25 d-block mb-2 text-success"></i>
                                                لا توجد أقساط أصول متأخرة حالياً.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($assetOverdue->hasPages())
                        <div class="card-footer bg-white border-0 pt-3 pb-3">
                            {{ $assetOverdue->appends(request()->query())->links() }}</div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- 🔥 مودال موحد وديناميكي لجميع أنواع الدفعات 🔥 --}}
    <div class="modal fade" id="universalPayModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 bg-light rounded-top-4">
                    <h5 class="modal-title font-bold text-dark" id="modal-title-text"><i
                            class="mdi mdi-cash-register me-1"></i> معالجة دفعة متأخرة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="universalPayForm" method="POST" action="" class="prevent-double-submit">
                    @csrf
                    <div class="modal-body p-4 text-start">
                        <div class="alert alert-info border-0 shadow-sm mb-4 bg-info bg-opacity-10 text-dark fw-bold">
                            <i class="mdi mdi-card-account-details-outline text-primary fs-5 align-middle me-1"></i> الجهة
                            / البيان: <span id="modal-entity-name" class="text-primary fs-6 fw-black"></span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">المبلغ المراد معالجته (OMR) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-dark text-white border-dark"><i
                                        class="mdi mdi-cash"></i></span>
                                <input type="number" step="0.001"
                                    class="form-control font-bold font-24 text-center border-dark font-monospace"
                                    name="amount_paid" id="modal-amount-paid" required>
                            </div>
                            <small class="text-danger mt-2 fw-bold d-block" id="limitWarning"
                                style="display: none !important;">
                                <i class="mdi mdi-alert-octagon"></i> تنبيه: لا يمكن تجاوز المبلغ المستحق.
                            </small>
                            <small class="text-muted mt-2 fw-bold d-block" id="assetNotice"
                                style="display: none !important;">
                                <i class="mdi mdi-information-outline text-primary"></i> تنبيه محاسبي: أقساط الأصول يتم
                                سدادها بالكامل كدفعة واحدة.
                            </small>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted" id="account-label">سحب/إيداع الحساب <span
                                        class="text-danger">*</span></label>
                                <select name="financial_account_id" class="form-select select2-modal shadow-sm" required
                                    style="width: 100%;">
                                    <option value="" disabled selected>-- اختر الخزينة/البنك --</option>
                                    @foreach ($financialAccounts ?? [] as $account)
                                        <option value="{{ $account->id }}">{{ $account->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted">رقم المرجع / الشيك</label>
                                <input type="text" name="check_number"
                                    class="form-control text-center font-monospace shadow-sm" placeholder="اختياري...">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label fw-bold text-muted">ملاحظات السند</label>
                                <textarea name="notes" class="form-control shadow-sm" rows="2" placeholder="أية ملاحظات إضافية..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light justify-content-between rounded-bottom-4">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold"
                            data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-dark px-5 font-bold shadow-sm rounded-pill btn-submit"><i
                                class="mdi mdi-check-decagram me-1"></i> ترحيل السند</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل Tooltips للواتساب
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // تفعيل الـ Select2 بداخل المودال
            $('#universalPayModal').on('shown.bs.modal', function() {
                $('.select2-modal').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#universalPayModal')
                });
            });

            // 1️⃣ تحصيل قسط الطالب
            $(document).on('click', '.open-student-modal', function() {
                let id = $(this).data('id');
                let remaining = parseFloat($(this).data('remaining')) || 0;

                $('#modal-title-text').html(
                    '<i class="mdi mdi-cash-register me-1 text-danger"></i> تحصيل إيراد (متأخر)');
                $('#modal-entity-name').text('الطالب: ' + $(this).data('name'));
                $('#account-label').html('إيداع النقدية إلى <span class="text-danger">*</span>');

                $('#modal-amount-paid').val(remaining.toFixed(3)).attr('max', remaining.toFixed(3)).prop(
                    'readonly', false);
                $('#assetNotice').hide();

                let payUrl = "{{ url('ar/admin/installments/student') }}/" + id + "/pay";
                $('#universalPayForm').attr('action', payUrl);
                $('#universalPayModal').modal('show');
            });

            // 2️⃣ سداد قسط مورد (مشتريات)
            $(document).on('click', '.open-purchase-modal', function() {
                let id = $(this).data('id');
                let remaining = parseFloat($(this).data('remaining')) || 0;

                $('#modal-title-text').html(
                    '<i class="mdi mdi-cash-minus me-1 text-warning"></i> صرف دفعة (مورد متأخر)');
                $('#modal-entity-name').text('المورد: ' + $(this).data('name'));
                $('#account-label').html('سحب النقدية من <span class="text-danger">*</span>');

                $('#modal-amount-paid').val(remaining.toFixed(3)).attr('max', remaining.toFixed(3)).prop(
                    'readonly', false);
                $('#assetNotice').hide();

                let payUrl = "{{ url('ar/admin/installments/purchase') }}/" + id + "/pay";
                $('#universalPayForm').attr('action', payUrl);
                $('#universalPayModal').modal('show');
            });

            // 3️⃣ سداد قسط أصل ثابت
            $(document).on('click', '.open-asset-modal', function() {
                let id = $(this).data('id');
                let amount = parseFloat($(this).data('amount')) || 0;

                $('#modal-title-text').html(
                    '<i class="mdi mdi-domain me-1 text-primary"></i> صرف دفعة (أصل متأخر)');
                $('#modal-entity-name').text('الأصل: ' + $(this).data('name'));
                $('#account-label').html('سحب النقدية من <span class="text-danger">*</span>');

                $('#modal-amount-paid').val(amount.toFixed(3)).attr('max', amount.toFixed(3)).prop(
                    'readonly', true);
                $('#assetNotice').show();

                let payUrl = "{{ url('ar/admin/installments/asset') }}/" + id + "/pay";
                $('#universalPayForm').attr('action', payUrl);
                $('#universalPayModal').modal('show');
            });

            // حماية النماذج ضد الإرسال المتكرر وتخطي الحدود
            $('.prevent-double-submit').on('submit', function(e) {
                let amount = parseFloat($('#modal-amount-paid').val()) || 0;
                let max = parseFloat($('#modal-amount-paid').attr('max')) || 0;

                if (!$('#modal-amount-paid').prop('readonly') && amount > max) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'تنبيه محاسبي!',
                        text: 'المبلغ المدخل يتجاوز المستحق!'
                    });
                    return false;
                }
                $(this).find('.btn-submit').prop('disabled', true).html(
                    "<i class='mdi mdi-loading mdi-spin me-1'></i> جاري التنفيذ...");
            });

            $('#modal-amount-paid').on('input', function() {
                let max = parseFloat($(this).attr('max')) || 0;
                let current = parseFloat($(this).val()) || 0;

                if (current > max) {
                    $(this).addClass('is-invalid border-danger text-danger');
                    $('#limitWarning').show();
                } else {
                    $(this).removeClass('is-invalid border-danger text-danger');
                    $('#limitWarning').hide();
                }
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'عملية ناجحة',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
