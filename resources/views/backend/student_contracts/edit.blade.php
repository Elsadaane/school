@extends('backend.layouts.master')
@section('title', 'تعديل العقد المالي | ' . $contract->contract_number)

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .contract-wizard-nav {
            display: flex;
            justify-content: space-around;
            background: #fff;
            padding: 20px 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #e9ecef;
        }

        .contract-step-item {
            color: #98a6ad;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            position: relative;
        }

        .contract-step-item.active {
            color: #f59e0b;
        }

        .contract-step-item.passed {
            color: #10b981;
        }

        .contract-step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: 0.3s;
        }

        .contract-step-item.active .contract-step-number {
            background-color: #f59e0b;
            color: #fff;
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);
        }

        .contract-step-item.passed .contract-step-number {
            background-color: #10b981;
            color: #fff;
            border-color: #10b981;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
            border-top: 5px solid #f59e0b;
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 2px dashed #e2e8f0;
        }

        .invoice-table {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .invoice-table th {
            background-color: #f8fafc;
            font-size: 13px;
            font-weight: 800;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
            text-align: center;
        }

        .invoice-table td {
            vertical-align: middle;
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }

        .invoice-table .form-control {
            border: 1px solid #e2e8f0;
            background-color: #fff;
            border-radius: 8px;
            font-size: 14px;
            text-align: center;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
        }

        .invoice-table .form-control:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .totals-box {
            background-color: #fffbeb;
            border-radius: 12px;
            padding: 25px;
            border: 1px dashed #fcd34d;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
            font-weight: 700;
            color: #475569;
        }

        .grand-total-row {
            font-size: 20px;
            font-weight: 900;
            color: #d97706;
            border-top: 2px solid #fde68a;
            padding-top: 15px;
            margin-top: 15px;
        }

        .quick-insert-btn {
            font-size: 13px;
            padding: 8px 16px;
            border-radius: 8px;
            margin-left: 5px;
            transition: 0.2s;
            font-weight: bold;
            border: none;
        }

        .quick-insert-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .progress-container {
            height: 10px;
            border-radius: 10px;
            background-color: #e2e8f0;
            margin-top: 15px;
            overflow: hidden;
        }

        .progress-bar-custom {
            height: 100%;
            transition: width 0.4s ease, background-color 0.4s ease;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-weight: 700;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.4s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .contract-wizard-nav {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .invoice-table th,
        body[data-layout-color="dark"] .totals-box {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #ced4da;
        }

        body[data-layout-color="dark"] .invoice-table td {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-control {
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
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-3 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold mb-1 text-warning"><i
                            class="mdi mdi-file-document-edit text-warning me-1 fs-3 align-middle"></i> تعديل عقد الطالب
                        المالي</h4>
                    <p class="text-muted font-14 mb-0">العقد الحالي: <span
                            class="font-monospace fw-bold text-dark">{{ $contract->contract_number }}</span> | الرجاء
                        الانتباه: تعديل الرسوم سيعيد جدولة الأقساط.</p>
                </div>
                <a href="{{ route('admin.student-contracts.index') }}"
                    class="btn btn-light rounded-pill shadow-sm border font-bold px-4">
                    <i class="mdi mdi-arrow-left me-1 align-middle"></i> إلغاء ورجوع
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 rounded-3 fw-bold mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-premium shadow-sm">
            <div class="card-body p-4 p-md-5">

                <div class="contract-wizard-nav shadow-sm">
                    <div class="contract-step-item active" id="step1-nav"><span class="contract-step-number">1</span> بيانات
                        العقد الأساسية</div>
                    <div class="contract-step-item" id="step2-nav"><span class="contract-step-number">2</span> بنود الرسوم
                        والخصومات</div>
                    <div class="contract-step-item" id="step3-nav"><span class="contract-step-number">3</span> إعادة جدولة
                        الأقساط</div>
                </div>

                <form action="{{ route('admin.student-contracts.update', $contract->id) }}" method="POST" id="contractForm"
                    class="prevent-double-submit">
                    @csrf
                    @method('PUT')

                    {{-- ================= الخطوة 1: البيانات الأساسية ================= --}}
                    <div class="tab-pane active" id="step1">
                        <h5 class="form-section-title"><i
                                class="mdi mdi-account-details-outline text-warning me-1 fs-4 align-middle"></i> تفاصيل
                            الطالب والعقد</h5>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label text-muted font-13 fw-bold">الطالب المستهدف <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2-init shadow-sm" name="student_id" id="contract_student"
                                    required style="width: 100%;">
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" data-branch="{{ $student->branch_id }}"
                                            data-parent="{{ $student->parent->name_ar ?? '---' }}"
                                            data-grade-id="{{ $student->grade_id }}"
                                            data-grade-name="{{ $student->grade->name_ar ?? '---' }}"
                                            data-section-id="{{ $student->section_id }}"
                                            data-section-name="{{ $student->section->name_ar ?? 'بدون شعبة' }}"
                                            data-transport="{{ $student->transport_type }}"
                                            {{ $contract->student_id == $student->id ? 'selected' : '' }}>
                                            {{ trim($student->first_name . ' ' . ($student->second_name ?? '') . ' ' . $student->tribe) }}
                                            {{ $student->student_code ? ' - (كود: ' . $student->student_code . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted font-13 fw-bold">تاريخ إنشاء العقد <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control shadow-sm fw-bold text-center border-warning"
                                    style="height: 42px;" name="contract_date"
                                    value="{{ \Carbon\Carbon::parse($contract->contract_date)->format('Y-m-d') }}"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted font-13 fw-bold">اسم ولي الأمر</label>
                                <input type="text" id="guardian_name"
                                    class="form-control bg-light fw-bold text-dark shadow-sm" style="height: 42px;"
                                    readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted font-13 fw-bold">الصف الدراسي</label>
                                <input type="text" id="student_grade_text"
                                    class="form-control bg-light fw-bold text-dark shadow-sm" style="height: 42px;"
                                    readonly>
                                <input type="hidden" name="grade_id" id="input_grade_id">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted font-13 fw-bold">الشعبة الحاليّة</label>
                                <input type="text" id="student_section_text"
                                    class="form-control bg-light fw-bold text-dark shadow-sm" style="height: 42px;"
                                    readonly>
                                <input type="hidden" name="section_id" id="input_section_id">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted font-13 fw-bold">الفرع / المدرسة <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2-init shadow-sm" name="branch_id" id="contract_branch"
                                    required>
                                    <option value="" selected disabled>-- اختر الفرع --</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ $contract->branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted font-13 fw-bold">السنة الدراسية المستهدفة للعقد <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2-init shadow-sm" name="academic_year_id"
                                    id="academic_year_id" required>
                                    <option value="" selected disabled>-- اختر السنة الدراسية --</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}"
                                            {{ $contract->academic_year_id == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mt-2">
                                <label class="form-label text-muted font-13 fw-bold"><i
                                        class="mdi mdi-comment-text-outline text-secondary me-1"></i> ملاحظات أو شروط خاصة
                                    بالعقد (اختياري)</label>
                                <textarea name="notes" class="form-control shadow-sm fw-bold border-secondary border-opacity-25" rows="3"
                                    placeholder="اكتب هنا أي ملاحظات تخص هذا العقد...">{{ old('notes', $contract->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-5 border-top pt-4">
                            <button type="button"
                                class="btn btn-warning px-5 py-2 rounded-pill btn-next fw-bold shadow-sm text-dark"
                                id="btn-to-items">
                                حفظ ومراجعة البنود <i class="mdi mdi-arrow-left ms-1 align-middle"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ================= الخطوة 2: بنود الرسوم ================= --}}
                    <div class="tab-pane" id="step2">
                        <div
                            class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3 flex-wrap gap-3">
                            <h5 class="form-section-title border-0 mb-0 pb-0 text-warning"><i
                                    class="mdi mdi-cash-register me-1 fs-4 align-middle"></i> بنود الرسوم المدرسية</h5>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="text-muted font-12 fw-bold"><i class="mdi mdi-flash text-warning"></i> إدراج
                                    سريع:</span>
                                <button type="button" class="quick-insert-btn bg-info bg-opacity-10 text-info"
                                    data-keyword="تسجيل">رسوم تسجيل</button>
                                <button type="button" class="quick-insert-btn bg-success bg-opacity-10 text-success"
                                    data-keyword="دراسية">رسوم دراسية</button>
                                <button type="button" class="quick-insert-btn bg-primary bg-opacity-10 text-primary"
                                    data-keyword="كتب">حقيبة كتب</button>
                                <button type="button" class="quick-insert-btn bg-secondary bg-opacity-10 text-secondary"
                                    data-keyword="زي">زي مدرسي</button>
                            </div>
                        </div>

                        {{-- 🚌 قسم المواصلات الذكي (المحدث) 🚌 --}}
                        <div class="row g-3 bg-light p-3 rounded-4 mb-4 border shadow-sm">
                            <div class="col-md-4">
                                <label class="form-label text-muted font-13 fw-bold"><i
                                        class="mdi mdi-map-marker-path text-info fs-5 align-middle me-1"></i> خط السير
                                    (Route)</label>
                                <select class="form-select bg-white border-0 shadow-sm fw-bold" name="transport_route_id"
                                    id="transport_route_id" disabled>
                                    <option value="" selected>اختر الفرع أولاً...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted font-13 fw-bold"><i
                                        class="mdi mdi-bus-clock text-success fs-5 align-middle me-1"></i> باص الذهاب
                                    (الصباحي)</label>
                                <select class="form-select bg-white border-0 shadow-sm fw-bold" name="morning_bus_id"
                                    id="morning_bus_id" disabled>
                                    <option value="" selected>اختر خط السير أولاً...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted font-13 fw-bold"><i
                                        class="mdi mdi-bus-clock text-warning fs-5 align-middle me-1"></i> باص العودة
                                    (المسائي)</label>
                                <select class="form-select bg-white border-0 shadow-sm fw-bold" name="afternoon_bus_id"
                                    id="afternoon_bus_id" disabled>
                                    <option value="" selected>اختر خط السير أولاً...</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <small class="text-danger font-12 d-none fw-bold mt-1 d-block" id="bus_full_alert"><i
                                        class="mdi mdi-alert-circle"></i> 🚫 عفواً، إحدى الحافلات المختارة مكتملة
                                    العدد!</small>
                            </div>
                        </div>

                        <div class="table-responsive mb-4 border rounded-3 shadow-sm"
                            style="max-height: 350px; overflow-y: auto;">
                            <table class="table invoice-table mb-0" id="itemsTable">
                                <thead class="sticky-top">
                                    <tr>
                                        <th class="text-start ps-3" style="width: 30%;">البند (الرسوم/المنتج)</th>
                                        <th style="width: 15%;">سعر الوحدة</th>
                                        <th style="width: 10%;">الكمية</th>
                                        <th style="width: 15%;">خصم (OMR)</th>
                                        <th style="width: 10%;">الضريبة %</th>
                                        <th style="width: 15%;">الإجمالي</th>
                                        <th style="width: 5%;">حذف</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4">
                            <button type="button"
                                class="btn btn-sm btn-outline-warning rounded-pill fw-bold px-3 shadow-sm text-dark"
                                id="addRowBtn">
                                <i class="mdi mdi-plus-circle me-1"></i> إضافة سطر مخصص للفاتورة
                            </button>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <div class="totals-box shadow-sm">
                                    <div class="total-row">
                                        <span>الإجمالي الفرعي للبنود:</span>
                                        <span class="font-monospace text-dark"><span
                                                id="display_sub_total">{{ $contract->sub_total }}</span> OMR</span>
                                        <input type="hidden" name="sub_total" id="input_sub_total"
                                            value="{{ $contract->sub_total }}">
                                    </div>
                                    <div class="total-row align-items-center text-danger mt-3">
                                        <span>منحة / خصم إضافي:</span>
                                        <div class="input-group input-group-sm shadow-sm" style="width: 160px;">
                                            <input type="number" step="0.001"
                                                class="form-control text-center text-danger font-monospace fw-bold border-warning bg-white shadow-none"
                                                name="discount" id="input_discount" value="{{ $contract->discount }}"
                                                min="0">
                                            <span
                                                class="input-group-text bg-warning text-dark border-warning fw-bold">OMR</span>
                                        </div>
                                    </div>
                                    <div class="total-row text-warning mt-3">
                                        <span>إجمالي الضريبة:</span>
                                        <span class="font-monospace text-dark"><span
                                                id="display_tax_total">{{ $contract->tax_amount }}</span> OMR</span>
                                        <input type="hidden" name="tax_amount" id="input_tax_amount"
                                            value="{{ $contract->tax_amount }}">
                                    </div>
                                    <div class="grand-total-row d-flex justify-content-between align-items-center">
                                        <span>الإجمالي الشامل للعقد:</span>
                                        <span class="font-monospace"><span
                                                id="display_grand_total">{{ $contract->grand_total }}</span> <span
                                                class="font-14 text-muted">OMR</span></span>
                                        <input type="hidden" name="grand_total" id="input_grand_total"
                                            value="{{ $contract->grand_total }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5 border-top pt-4">
                            <button type="button"
                                class="btn btn-secondary px-5 py-2 rounded-pill btn-prev fw-bold shadow-sm"><i
                                    class="mdi mdi-arrow-right me-1"></i> السابق</button>
                            <button type="button"
                                class="btn btn-warning px-5 py-2 rounded-pill btn-next fw-bold shadow-sm text-dark"
                                id="btn-to-installments">
                                المتابعة لجدولة الأقساط <i class="mdi mdi-arrow-left ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ================= الخطوة 3: الأقساط ================= --}}
                    <div class="tab-pane" id="step3">
                        <h5 class="form-section-title text-success"><i
                                class="mdi mdi-calendar-clock text-success me-1 fs-4 align-middle"></i> إعادة هيكلة وجدولة
                            الأقساط</h5>

                        <div class="row align-items-end bg-light p-4 rounded-4 mb-4 border shadow-sm g-3"
                            style="border-color: #f59e0b !important;">
                            <div class="col-md-2 border-end">
                                <h6 class="mb-2 text-muted font-bold text-uppercase font-12">الإجمالي المطلوب</h6>
                                <h3 class="text-warning font-bold mb-0 font-monospace text-darken"><span
                                        id="installment_target_amount">{{ $contract->grand_total }}</span></h3>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label font-12 fw-bold text-success mb-2"><i
                                        class="mdi mdi-cash-fast fs-5 align-middle me-1"></i> دفعة مقدمة</label>
                                <input type="number" step="0.001" id="down_payment" name="down_payment"
                                    class="form-control form-control-lg border-success text-success font-monospace fw-bold text-center shadow-sm"
                                    value="{{ $contract->paid_amount }}" min="0"
                                    {{ $contract->paid_amount > 0 ? 'readonly' : '' }}>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label font-12 fw-bold text-primary mb-2"><i
                                        class="mdi mdi-bank fs-5 align-middle me-1"></i> إيداع في (الخزنة/البنك)</label>
                                <select class="form-select form-select-lg shadow-sm fw-bold border-primary"
                                    name="financial_account_id" id="financial_account_id"
                                    {{ $contract->paid_amount > 0 ? 'disabled' : '' }}>
                                    <option value="" selected disabled>-- اختر الخزنة --</option>
                                    @foreach ($financialAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name_ar }}</option>
                                    @endforeach
                                </select>
                                @if ($contract->paid_amount > 0)
                                    <small class="text-muted font-11 d-block mt-1">تم قبض المقدم مسبقاً ولا يمكن
                                        تعديله.</small>
                                @endif
                            </div>

                            <div class="col-md-2">
                                <label class="form-label font-12 fw-bold text-muted mb-2">تُقسط على (أشهر)</label>
                                <input type="number" id="installment_count" name="installments_count"
                                    class="form-control form-control-lg border-warning text-center fw-bold shadow-sm font-monospace"
                                    value="{{ max(1, $contract->installments->count() - ($contract->paid_amount > 0 ? 1 : 0)) }}"
                                    min="1" max="12">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label font-12 fw-bold text-muted mb-2">تاريخ أول قسط</label>
                                @php $firstInst = $contract->installments->where('status', 'unpaid')->first(); @endphp
                                <input type="date" id="first_installment_date"
                                    class="form-control form-control-lg shadow-sm text-center fw-bold border-warning"
                                    value="{{ $firstInst ? \Carbon\Carbon::parse($firstInst->due_date)->format('Y-m-d') : date('Y-m-d', strtotime('+1 month')) }}">
                            </div>

                            <div class="col-md-1 text-end">
                                <button type="button" id="generate_installments_btn"
                                    class="btn btn-dark btn-lg fw-bold w-100 shadow-sm px-1" title="إعادة التوزيع">
                                    <i class="mdi mdi-refresh fs-4"></i>
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive border rounded-3 shadow-sm mb-4"
                            style="max-height: 350px; overflow-y: auto;">
                            <table class="table invoice-table mb-0" id="installmentsTable">
                                <thead class="sticky-top">
                                    <tr>
                                        <th style="width: 10%;"># القسط</th>
                                        <th style="width: 45%;">تاريخ الاستحقاق</th>
                                        <th style="width: 45%;">قيمة القسط (OMR)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="bg-light p-4 rounded-4 border shadow-sm">
                            <div class="d-flex justify-content-between font-14 fw-bold text-dark mb-2">
                                <span>مجموع الأقساط الموزعة + الدفعة المقدمة: <span id="current_distributed"
                                        class="text-primary font-monospace fs-5">{{ $contract->grand_total }}</span></span>
                                <span>القيمة المستهدفة (إجمالي العقد): <span id="target_distributed"
                                        class="text-dark font-monospace fs-5">{{ $contract->grand_total }}</span></span>
                            </div>
                            <div class="progress-container shadow-sm border bg-white">
                                <div class="progress-bar-custom" id="distribution_progress"
                                    style="width: 100%; background-color: #10b981;"></div>
                            </div>
                            <div id="installment_warning"
                                class="text-danger font-13 fw-bold mt-3 text-center bg-danger bg-opacity-10 p-2 rounded-3"
                                style="display: none;">
                                <i class="mdi mdi-alert-circle fs-5 align-middle me-1"></i> تنبيه: مجموع الدفعات الموزعة لا
                                يتطابق مع إجمالي العقد. يرجى تعديل قيم الأقساط يدوياً أو الضغط على (إعادة التوزيع).
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5 border-top pt-4">
                            <button type="button"
                                class="btn btn-secondary px-5 py-3 rounded-pill btn-prev fw-bold shadow-sm"><i
                                    class="mdi mdi-arrow-right me-1"></i> السابق للبنود</button>
                            <button type="submit"
                                class="btn btn-warning btn-lg px-5 py-3 rounded-pill fw-bold shadow-lg btn-submit text-dark"
                                id="final_submit_btn">
                                <i class="mdi mdi-update me-2 fs-4 align-middle"></i> حفظ واعتماد التعديلات
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('backend.contracts.partials.modals')

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        let allProducts = @json($feeItems ?? []);
        let existingItems = @json($contract->items ?? []);
        let existingInstallments = @json($contract->installments ?? []);
        let rowIdx = 0;

        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? "rtl" : "ltr"
            });

            $('#contract_student').on('change', function() {
                let option = $(this).find(':selected');

                if (option.val()) {
                    let branchId = option.data('branch');
                    let parentName = option.data('parent');
                    let gradeId = option.data('grade-id');
                    let gradeName = option.data('grade-name');
                    let sectionId = option.data('section-id');
                    let sectionName = option.data('section-name');

                    $('#guardian_name').val(parentName);
                    $('#student_grade_text').val(gradeName);
                    $('#student_section_text').val(sectionName);
                    $('#input_grade_id').val(gradeId);
                    $('#input_section_id').val(sectionId);

                    if (branchId && $('#contract_branch').val() != branchId) {
                        $('#contract_branch').val(branchId).trigger('change');
                    }
                }
            });

            if ($('#contract_student').val()) {
                $('#contract_student').trigger('change');
            }

            $('.btn-next').off('click').on('click', function(e) {
                e.preventDefault();
                let currentTabBtn = $('.contract-wizard-nav div.active');
                let nextTabBtn = currentTabBtn.next('.contract-step-item');

                if ($(this).attr('id') === 'btn-to-items') {
                    let branchId = $('#contract_branch').val();
                    let studentId = $('#contract_student').val();

                    if (!branchId || !studentId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'يرجى اختيار الفرع والطالب للاستمرار.',
                            confirmButtonColor: '#3b82f6'
                        });
                        return;
                    }
                    loadRoutes(branchId);
                }

                if ($(this).attr('id') === 'btn-to-installments') {
                    let gTotal = parseFloat($('#input_grand_total').val()) || 0;
                    if (gTotal <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ مالي',
                            text: 'يجب إضافة رسوم دراسية بقيمة صحيحة للاستمرار.',
                            confirmButtonColor: '#ef4444'
                        });
                        return;
                    }
                    $('#installment_target_amount').text(gTotal.toFixed(3));

                    if ($('#installmentsTable tbody tr').length === 0) {
                        generateInstallments();
                    } else {
                        validateInstallmentsTotal();
                    }
                }

                if (nextTabBtn.length > 0) {
                    currentTabBtn.addClass('passed').removeClass('active');
                    let targetPane = nextTabBtn.attr('id').replace('-nav', '');
                    $('.tab-pane').removeClass('show active');
                    $('#' + targetPane).addClass('show active');
                    nextTabBtn.addClass('active');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });

            $('.btn-prev').off('click').on('click', function(e) {
                e.preventDefault();
                let currentTabBtn = $('.contract-wizard-nav div.active');
                let prevTabBtn = currentTabBtn.prev('.contract-step-item');

                if (prevTabBtn.length > 0) {
                    currentTabBtn.removeClass('passed active');
                    let targetPane = prevTabBtn.attr('id').replace('-nav', '');
                    $('.tab-pane').removeClass('show active');
                    $('#' + targetPane).addClass('show active');
                    prevTabBtn.addClass('active').removeClass('passed');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });

            $('#contract_branch').on('change', function() {
                let branchId = $(this).val();
                if (branchId) {
                    loadRoutes(branchId);
                } else {
                    $('#transport_route_id, #morning_bus_id, #afternoon_bus_id').html(
                        '<option value="" selected>اختر الفرع أولاً...</option>').prop('disabled', true);
                }
            });

            $('.quick-insert-btn').click(function() {
                let keyword = $(this).data('keyword');
                let branchId = $('#contract_branch').val();

                if (!branchId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى الرجوع واختيار الفرع أولاً.',
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    return;
                }

                let foundProduct = allProducts.find(p => p.branch_id == branchId && p.name_ar.includes(
                    keyword));

                if (foundProduct) {
                    let exists = false;
                    $('.item-select').each(function() {
                        if ($(this).val() == foundProduct.id) exists = true;
                    });

                    if (exists) {
                        Swal.fire({
                            icon: 'info',
                            title: 'البند موجود',
                            text: 'تم إدراج هذا البند بالفعل في الفاتورة بالأسفل.',
                            toast: true,
                            position: 'top-end',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    } else {
                        addRow({
                            product_id: foundProduct.id,
                            unit_price: foundProduct.selling_price || 0,
                            quantity: 1,
                            item_discount: 0,
                            tax_percent: foundProduct.tax_percent || 0,
                            row_total: foundProduct.selling_price || 0
                        });
                        calculateGrandTotal();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'غير متوفر',
                        text: `لم يتم العثور على بند (${keyword}) في هذا الفرع.`,
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });

            // ==========================================
            // 🚌 نظام المواصلات المحدث (مع Accessors الجديدة) 🚌
            // ==========================================

            function loadRoutes(branchId) {
                let routeSelect = $('#transport_route_id');
                routeSelect.html('<option value="">جاري التحميل...</option>').prop('disabled', true);
                $('#morning_bus_id, #afternoon_bus_id').html(
                    '<option value="" selected>اختر خط السير أولاً...</option>').prop('disabled', true);
                $('#bus-fee-row').remove();
                calculateGrandTotal();

                $.get("{{ route('admin.get-branch-routes') }}", {
                    branch_id: branchId
                }, function(data) {
                    routeSelect.empty();
                    if (data.length === 0) {
                        routeSelect.append(
                            '<option value="" disabled selected>لا توجد خطوط سير في هذا الفرع</option>');
                    } else {
                        routeSelect.append(
                            '<option value="" selected disabled>-- اختر خط السير --</option>');
                        data.forEach(route => {
                            let selected = (route.id == "{{ $contract->transport_route_id }}") ?
                                'selected' : '';
                            let fareText = route.monthly_fare_both > 0 ?
                                ` (ذ/إ: ${route.monthly_fare_both} OMR)` : '';
                            routeSelect.append(
                                `<option value="${route.id}" data-fare-both="${route.monthly_fare_both}" data-fare-single="${route.monthly_fare_single}" ${selected}>${route.name}${fareText}</option>`
                            );
                        });
                        routeSelect.prop('disabled', false);

                        // تحميل الحافلات إذا كان هناك خط سير محدد مسبقاً
                        if ("{{ $contract->transport_route_id }}") {
                            loadBusesForRoute("{{ $contract->transport_route_id }}");
                        }
                    }
                }).fail(function() {
                    routeSelect.html('<option value="" disabled selected>خطأ في تحميل الخطوط</option>');
                });
            }

            function loadBusesForRoute(routeId) {
                let morningSelect = $('#morning_bus_id');
                let afternoonSelect = $('#afternoon_bus_id');

                morningSelect.html('<option value="">جاري التحميل...</option>').prop('disabled', true);
                afternoonSelect.html('<option value="">جاري التحميل...</option>').prop('disabled', true);
                $('#bus-fee-row').remove();
                calculateGrandTotal();

                $.get("{{ route('admin.get-route-buses') }}", {
                    route_id: routeId
                }, function(data) {
                    morningSelect.empty();
                    afternoonSelect.empty();

                    if (data.length === 0) {
                        let opt = '<option value="" disabled selected>لا توجد حافلات في هذا الخط</option>';
                        morningSelect.append(opt).prop('disabled', true);
                        afternoonSelect.append(opt).prop('disabled', true);
                    } else {
                        // إضافة خيار "بدون باص"
                        morningSelect.append('<option value="">🚫 لا يوجد باص صباحي (توصيل خاص)</option>');
                        afternoonSelect.append(
                            '<option value="">🚫 لا يوجد باص مسائي (توصيل خاص)</option>');

                        data.forEach(bus => {
                            // 🎯 عرض المقاعد المتاحة بشكل منفصل
                            let morningText = bus.is_full_morning ?
                                `🚫 ممتلئة (${bus.morning_booked}/${bus.capacity})` :
                                `متاح: ${bus.available_morning} كرسي`;

                            let afternoonText = bus.is_full_afternoon ?
                                `🚫 ممتلئة (${bus.afternoon_booked}/${bus.capacity})` :
                                `متاح: ${bus.available_afternoon} كرسي`;

                            // باص الصباح
                            let morningDisabled = bus.is_full_morning ? 'disabled' : '';
                            let morningSelected = (bus.id == "{{ $contract->morning_bus_id }}") ?
                                'selected' : '';
                            let morningOpt = `<option value="${bus.id}" 
                                data-fare-both="${bus.fare_both}" 
                                data-fare-single="${bus.fare_single}" 
                                data-full-morning="${bus.is_full_morning}" 
                                data-full-afternoon="${bus.is_full_afternoon}"
                                ${morningDisabled} ${morningSelected}>
                                ${bus.bus_number} - صباحاً: ${morningText}
                            </option>`;
                            morningSelect.append(morningOpt);

                            // باص المساء
                            let afternoonDisabled = bus.is_full_afternoon ? 'disabled' : '';
                            let afternoonSelected = (bus.id ==
                                "{{ $contract->afternoon_bus_id }}") ? 'selected' : '';
                            let afternoonOpt = `<option value="${bus.id}" 
                                data-fare-both="${bus.fare_both}" 
                                data-fare-single="${bus.fare_single}" 
                                data-full-morning="${bus.is_full_morning}" 
                                data-full-afternoon="${bus.is_full_afternoon}"
                                ${afternoonDisabled} ${afternoonSelected}>
                                ${bus.bus_number} - مساءً: ${afternoonText}
                            </option>`;
                            afternoonSelect.append(afternoonOpt);
                        });

                        morningSelect.prop('disabled', false);
                        afternoonSelect.prop('disabled', false);

                        // حساب أجرة المواصلات إذا كانت هناك حافلات محددة مسبقاً
                        calculateBusFare();
                    }
                }).fail(function() {
                    let opt = '<option value="" disabled selected>خطأ في تحميل الحافلات</option>';
                    morningSelect.html(opt).prop('disabled', true);
                    afternoonSelect.html(opt).prop('disabled', true);
                });
            }

            $('#transport_route_id').on('change', function() {
                let routeId = $(this).val();
                if (routeId) {
                    loadBusesForRoute(routeId);
                } else {
                    $('#morning_bus_id, #afternoon_bus_id').html(
                        '<option value="" selected>اختر خط السير أولاً...</option>').prop('disabled',
                        true);
                    $('#bus-fee-row').remove();
                    calculateGrandTotal();
                }
            });

            // 🚌 احتساب أجرة المواصلات من خط السير (وليس من الباص)
            $('#morning_bus_id, #afternoon_bus_id').on('change', function() {
                calculateBusFare();
            });

            function calculateBusFare() {
                let morningBus = $('#morning_bus_id').find(':selected');
                let afternoonBus = $('#afternoon_bus_id').find(':selected');

                let hasMorning = morningBus.val() !== '' && morningBus.val() !== null;
                let hasAfternoon = afternoonBus.val() !== '' && afternoonBus.val() !== null;

                // 🎯 تحديد السعر من خط السير
                let fareBoth = parseFloat(morningBus.data('fare-both') || afternoonBus.data('fare-both')) || 0;
                let fareSingle = parseFloat(morningBus.data('fare-single') || afternoonBus.data('fare-single')) ||
                    0;

                let totalPrice = 0;
                let descParts = [];

                if (hasMorning && hasAfternoon) {
                    // ذهاب وإياب
                    totalPrice = fareBoth;
                    descParts.push('ذهاب وإياب');
                } else if (hasMorning) {
                    // ذهاب فقط
                    totalPrice = fareSingle;
                    descParts.push('ذهاب فقط');
                } else if (hasAfternoon) {
                    // إياب فقط
                    totalPrice = fareSingle;
                    descParts.push('إياب فقط');
                }

                let desc = descParts.length > 0 ? `رسوم مواصلات (${descParts.join(' و ')})` : '';
                let existingRow = $('#bus-fee-row');

                if (totalPrice > 0 && desc) {
                    if (existingRow.length > 0) {
                        existingRow.find('.item-desc').val(desc);
                        existingRow.find('.item-price').val(totalPrice);
                        calculateRow(existingRow);
                    } else {
                        let tr = `
                            <tr id="bus-fee-row" class="table-warning">
                                <td class="text-start ps-3">
                                    <input type="text" class="form-control bg-white fw-bold text-success item-desc text-start ps-2" value="${desc}" readonly>
                                    <input type="hidden" name="items[bus_row][product_id]" value=""> 
                                </td>
                                <td><input type="number" class="form-control item-price font-monospace text-dark" name="items[bus_row][unit_price]" value="${totalPrice}" readonly></td>
                                <td><input type="number" class="form-control item-qty font-monospace text-dark" name="items[bus_row][quantity]" value="1" min="1" readonly></td>
                                <td><input type="number" class="form-control item-discount font-monospace text-muted d-none" name="items[bus_row][item_discount]" value="0"> - </td>
                                <td><input type="number" class="form-control item-tax font-monospace text-dark" name="items[bus_row][tax_percent]" value="0" readonly></td>
                                <td><input type="text" class="form-control item-total bg-light fw-bold text-success font-monospace" name="items[bus_row][row_total]" value="${totalPrice}" readonly></td>
                                <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">بند ثابت</span></td>
                            </tr>
                        `;
                        $('#itemsTable tbody').prepend(tr);
                        calculateGrandTotal();
                    }
                } else {
                    existingRow.remove();
                    calculateGrandTotal();
                }

                // تنبيه إذا كانت الحافلة ممتلئة (حماية للـ UI)
                let isFullMorning = hasMorning && morningBus.data('full-morning');
                let isFullAfternoon = hasAfternoon && afternoonBus.data('full-afternoon');
                if (isFullMorning || isFullAfternoon) {
                    $('#bus_full_alert').removeClass('d-none');
                } else {
                    $('#bus_full_alert').addClass('d-none');
                }
            }

            // ==========================================
            // 4. الفاتورة والصفوف
            // ==========================================
            function addRow(itemData = null) {
                let selectedBranchId = $('#contract_branch').val();
                let options = '<option value="" disabled selected>ابحث عن رسوم/منتج...</option>';
                let selectedProductId = itemData ? itemData.product_id : null;

                let branchProducts = allProducts.filter(p => p.branch_id == selectedBranchId);

                branchProducts.forEach(p => {
                    let isSelected = (selectedProductId == p.id) ? 'selected' : '';
                    let availableQty = p.quantity || 0;

                    if (itemData && itemData.product_id == p.id) {
                        availableQty += itemData.quantity;
                    }

                    let maxQty = p.type === 'service' ? 9999 : availableQty;
                    let qtyLabel = p.type === 'service' ? '' : ` (المتاح: ${maxQty})`;
                    let productName = p.name_ar || p.name || 'بند مجهول';

                    options +=
                        `<option value="${p.id}" data-price="${p.price ?? p.selling_price ?? 0}" data-tax="${p.tax_percent || 0}" data-type="${p.type}" data-max="${maxQty}" ${isSelected}>${productName}${qtyLabel}</option>`;
                });

                let uPrice = itemData ? itemData.unit_price : 0;
                let qty = itemData ? itemData.quantity : 1;
                let discount = itemData ? (itemData.item_discount || 0) : 0;
                let tax = itemData ? itemData.tax_percent : 0;
                let total = itemData ? itemData.row_total : 0;

                let tr = `
                    <tr id="row_${rowIdx}">
                        <td class="text-start ps-3 position-relative">
                            <select class="form-select item-select select2-dynamic shadow-sm" name="items[${rowIdx}][product_id]" style="width:100%; text-align:right;" required>
                                ${options}
                            </select>
                            <small class="stock-warning text-danger font-12 fw-bold position-absolute w-100 text-center mt-1" style="display: none; bottom: -18px; right: 0;">الكمية المتوفرة لا تكفي!</small>
                        </td>
                        <td><input type="number" step="0.001" class="form-control item-price text-primary font-monospace shadow-sm" name="items[${rowIdx}][unit_price]" value="${uPrice}" required></td>
                        <td><input type="number" class="form-control item-qty font-monospace shadow-sm" name="items[${rowIdx}][quantity]" value="${qty}" min="1" required></td>
                        <td><input type="number" step="0.001" class="form-control item-discount text-danger font-monospace shadow-sm" name="items[${rowIdx}][item_discount]" value="${discount}" min="0"></td>
                        <td><input type="number" step="0.01" class="form-control item-tax font-monospace shadow-sm" name="items[${rowIdx}][tax_percent]" value="${tax}"></td>
                        <td><input type="text" class="form-control item-total bg-light text-dark font-monospace fw-bolder border-0" name="items[${rowIdx}][row_total]" value="${total}" readonly></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-soft-danger rounded-circle p-2 shadow-sm border-0 remove-row" title="حذف السطر"><i class="mdi mdi-trash-can-outline font-18"></i></button></td>
                    </tr>
                `;
                $('#itemsTable tbody').append(tr);

                let newSelect = $('#row_' + rowIdx + ' .select2-dynamic');
                newSelect.select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? "rtl" : "ltr"
                });

                if (!itemData) {
                    newSelect.trigger('change');
                }

                rowIdx++;
            }

            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    if (item.product_id) {
                        addRow(item);
                    }
                });
            }

            $('#addRowBtn').click(() => addRow());

            $(document).on('change', '.item-select', function(e) {
                let currentSelect = $(this);
                let selectedId = currentSelect.val();
                let selectedOption = currentSelect.find(':selected');
                let currentRow = currentSelect.closest('tr');
                let isDuplicate = false;

                $('.item-select').not(this).each(function() {
                    if ($(this).val() == selectedId && selectedId != '') isDuplicate = true;
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'مكرر',
                        text: 'تم إدراج هذا البند مسبقاً!',
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    currentSelect.val('').trigger('change');
                } else {
                    currentRow.find('.item-price').val(selectedOption.data('price') || 0);
                    currentRow.find('.item-tax').val(selectedOption.data('tax') || 0);
                    let qtyInput = currentRow.find('.item-qty');
                    if (selectedOption.data('type') === 'product') {
                        qtyInput.attr('max', selectedOption.data('max'));
                    } else {
                        qtyInput.removeAttr('max');
                    }
                    calculateRow(currentRow);
                }
            });

            $(document).on('input', '.item-price, .item-qty, .item-tax, .item-discount', function() {
                calculateRow($(this).closest('tr'));
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').fadeOut(200, function() {
                    $(this).remove();
                    calculateGrandTotal();
                });
            });

            $('#input_discount').on('input', calculateGrandTotal);

            function calculateRow(tr) {
                let price = parseFloat(tr.find('.item-price').val()) || 0;
                let qty = parseFloat(tr.find('.item-qty').val()) || 0;
                let itemDiscount = parseFloat(tr.find('.item-discount').val()) || 0;
                let taxPercent = parseFloat(tr.find('.item-tax').val()) || 0;

                let rowSubTotal = (price * qty) - itemDiscount;
                if (rowSubTotal < 0) rowSubTotal = 0;
                let rowTax = rowSubTotal * (taxPercent / 100);

                tr.find('.item-total').val((rowSubTotal + rowTax).toFixed(3));
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let subTotal = 0;
                let totalTax = 0;
                $('#itemsTable tbody tr').each(function() {
                    let price = parseFloat($(this).find('.item-price').val()) || 0;
                    let qty = parseFloat($(this).find('.item-qty').val()) || 0;
                    let itemDiscount = parseFloat($(this).find('.item-discount').val()) || 0;
                    let taxPercent = parseFloat($(this).find('.item-tax').val()) || 0;
                    let rowSubTotal = (price * qty) - itemDiscount;
                    if (rowSubTotal > 0) {
                        subTotal += rowSubTotal;
                        totalTax += rowSubTotal * (taxPercent / 100);
                    }
                });

                let globalDiscount = parseFloat($('#input_discount').val()) || 0;
                let grandTotal = (subTotal + totalTax) - globalDiscount;
                if (grandTotal < 0) grandTotal = 0;

                $('#display_sub_total').text(subTotal.toFixed(3));
                $('#display_tax_total').text(totalTax.toFixed(3));
                $('#display_grand_total').text(grandTotal.toFixed(3));

                $('#input_sub_total').val(subTotal.toFixed(3));
                $('#input_tax_amount').val(totalTax.toFixed(3));
                $('#input_grand_total').val(grandTotal.toFixed(3));

                $('#installment_target_amount').text(grandTotal.toFixed(3));
                validateInstallmentsTotal();
            }

            function renderExistingInstallments() {
                let tbody = $('#installmentsTable tbody');
                tbody.empty();

                let unpaidInsts = existingInstallments.filter(i => i.status !== 'paid');

                unpaidInsts.forEach((inst, index) => {
                    let dateStr = inst.due_date.split(' ')[0];
                    let tr = `
                        <tr>
                            <td class="text-muted fw-bold">قسط #${index + 1}</td>
                            <td><input type="date" class="form-control bg-white text-center shadow-sm fw-bold border-primary" name="installments[${index}][due_date]" value="${dateStr}" required></td>
                            <td><input type="number" step="0.001" class="form-control bg-white text-center font-monospace fw-bold text-primary inst-amount-input shadow-sm border-primary" name="installments[${index}][amount]" value="${parseFloat(inst.amount).toFixed(3)}" required></td>
                        </tr>
                    `;
                    tbody.append(tr);
                });
                validateInstallmentsTotal();
            }

            if (existingInstallments.length > 0) {
                renderExistingInstallments();
            }

            $('#generate_installments_btn').click(generateInstallments);
            $('#down_payment').on('input', validateInstallmentsTotal);

            function generateInstallments() {
                let totalAmount = parseFloat($('#input_grand_total').val()) || 0;
                let downPayment = parseFloat($('#down_payment').val()) || 0;
                let count = parseInt($('#installment_count').val()) || 1;
                let startDateStr = $('#first_installment_date').val();

                if (downPayment > totalAmount) {
                    downPayment = totalAmount;
                    $('#down_payment').val(downPayment);
                }

                let amountToInstall = totalAmount - downPayment;
                let tbody = $('#installmentsTable tbody');
                tbody.empty();

                if (amountToInstall <= 0) {
                    validateInstallmentsTotal();
                    return;
                }

                let baseAmount = Math.floor((amountToInstall / count) * 1000) / 1000;
                let remainder = amountToInstall - (baseAmount * count);
                let currentDate = new Date(startDateStr);

                for (let i = 0; i < count; i++) {
                    let currentInstAmount = (i === 0) ? (baseAmount + remainder) : baseAmount;
                    let dateStr = currentDate.toISOString().split('T')[0];

                    let tr = `
                        <tr>
                            <td class="text-muted fw-bold">قسط #${i + 1}</td>
                            <td><input type="date" class="form-control bg-white text-center shadow-sm fw-bold border-warning" name="installments[${i}][due_date]" value="${dateStr}" required></td>
                            <td><input type="number" step="0.001" class="form-control bg-white text-center font-monospace fw-bold text-primary inst-amount-input shadow-sm border-primary" name="installments[${i}][amount]" value="${currentInstAmount.toFixed(3)}" required></td>
                        </tr>
                    `;
                    tbody.append(tr);
                    currentDate.setMonth(currentDate.getMonth() + 1);
                }
                validateInstallmentsTotal();
            }

            $(document).on('input', '.inst-amount-input', validateInstallmentsTotal);

            function validateInstallmentsTotal() {
                let expectedTotal = parseFloat($('#input_grand_total').val()) || 0;
                let downPayment = parseFloat($('#down_payment').val()) || 0;
                let sum = downPayment;

                $('.inst-amount-input').each(function() {
                    sum += parseFloat($(this).val()) || 0;
                });

                $('#current_distributed').text(sum.toFixed(3));
                $('#target_distributed').text(expectedTotal.toFixed(3));

                let percentage = expectedTotal > 0 ? (sum / expectedTotal) * 100 : 0;
                let bar = $('#distribution_progress');
                bar.css('width', Math.min(percentage, 100) + '%');

                if (Math.abs(sum - expectedTotal) > 0.005) {
                    bar.css('background-color', percentage > 100 ? '#ef4444' : '#f59e0b');
                    $('#installment_warning').fadeIn();
                    $('#final_submit_btn').prop('disabled', true);
                } else {
                    bar.css('background-color', '#10b981');
                    $('#installment_warning').fadeOut();
                    $('#final_submit_btn').prop('disabled', false);
                }
            }

            $('.prevent-double-submit').on('submit', function() {
                let btn = $('#final_submit_btn');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-2 fs-4 align-middle"></i> جاري اعتماد وتحديث العقد...'
                );
            });

            // تحميل البيانات الأولية عند فتح الصفحة
            calculateGrandTotal();
        });
    </script>
@endpush
