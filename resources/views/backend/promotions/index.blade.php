@extends('backend.layouts.master')
@section('title', 'ترحيل الطلاب للعام الجديد')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .page-wrapper {
            padding: 25px;
        }

        /* 🌟 كروت الإحصائيات 🌟 */
        .stat-card {
            border-radius: 16px;
            padding: 25px;
            color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card .stat-icon {
            position: absolute;
            right: -10px;
            bottom: -20px;
            font-size: 120px;
            opacity: 0.15;
            transform: rotate(15deg);
        }

        .stat-card .stat-label {
            font-size: 13px;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 900;
            font-family: 'Courier New', monospace;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        /* 🌟 كارت الفلاتر 🌟 */
        .filter-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            border-top: 5px solid #3b82f6;
        }

        .filter-label {
            font-weight: 800;
            color: #64748b;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-input {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            font-weight: 600;
            color: #334155;
            transition: all 0.3s ease;
        }

        .filter-input:focus {
            background-color: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* 🌟 كارت الجدول 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background: #fff;
            overflow: hidden;
        }

        .card-premium.table-card {
            border-top: 5px solid #10b981;
        }

        /* 🌟 الصفوف 🌟 */
        .student-row {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f1f5f9;
            border-right: 4px solid transparent;
        }

        .row-promoted {
            background: linear-gradient(90deg, #f0fdf4 0%, #ffffff 100%) !important;
            border-right-color: #10b981 !important;
        }

        .row-retained {
            background: linear-gradient(90deg, #fef2f2 0%, #ffffff 100%) !important;
            border-right-color: #ef4444 !important;
        }

        .student-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        /* 🌟 أزرار القرار 🌟 */
        .decision-group {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 5px;
            display: inline-flex;
            gap: 5px;
            border: 2px solid #e2e8f0;
            transition: 0.3s;
        }

        .decision-group:hover {
            border-color: #cbd5e1;
        }

        .decision-group .btn-check:checked+.btn-outline-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-color: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transform: scale(1.02);
        }

        .decision-group .btn-check:checked+.btn-outline-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border-color: #ef4444;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            transform: scale(1.02);
        }

        .decision-group .btn {
            border: none;
            border-radius: 8px !important;
            font-weight: 800;
            padding: 10px 24px;
            color: #64748b;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 13px;
        }

        .decision-group .btn:hover:not(.active) {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        /* 🌟 الجدول 🌟 */
        .table-custom {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 14px 16px;
        }

        /* 🌟 معلومات الطالب 🌟 */
        .student-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .student-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 14px;
        }

        .student-code {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .student-section {
            font-size: 11px;
            background: #eff6ff;
            color: #1e40af;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 700;
        }

        /* 🌟 شريط الحفظ العائم 🌟 */
        .sticky-footer {
            position: sticky;
            bottom: 0;
            z-index: 10;
            border-top: 2px solid #e2e8f0 !important;
            backdrop-filter: blur(10px);
            background-color: rgba(248, 250, 252, 0.95) !important;
            padding: 20px;
        }

        /* 🌟 أزرار العمليات 🌟 */
        .btn-filter {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 800;
            font-size: 15px;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        .btn-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 14px 35px;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: #fff;
        }

        .btn-bulk {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 13px;
            border: 2px solid;
            transition: all 0.2s ease;
        }

        .btn-bulk-pass {
            background: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }

        .btn-bulk-pass:hover {
            background: #10b981;
            color: #fff;
        }

        .btn-bulk-fail {
            background: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }

        .btn-bulk-fail:hover {
            background: #ef4444;
            color: #fff;
        }

        .btn-history {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(100, 116, 139, 0.3);
            transition: all 0.3s ease;
        }

        .btn-history:hover {
            transform: translateY(-2px);
            color: #fff;
        }

        /* 🌟 صندوق التعليمات 🌟 */
        .info-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #3b82f6;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-box .info-icon {
            width: 50px;
            height: 50px;
            background: #fff;
            border: 2px solid #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #2563eb;
        }

        .info-box h5 {
            color: #1e3a8a !important;
            font-weight: 800;
        }

        .info-box .info-item {
            color: #1e40af !important;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .info-box .info-item.warning {
            color: #b91c1c !important;
            font-weight: 700;
        }

        /* 🌟 Checkbox محسّن 🌟 */
        .form-check-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .form-check-input {
            cursor: pointer;
            width: 20px;
            height: 20px;
        }

        /* 🌟 Print 🌟 */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 العنوان 🌟 --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                    <i class='bx bx-transfer text-primary fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">ترحيل الطلاب (النجاح والرسوب)</h4>
                    <p class="text-muted mb-0 font-13">نقل الطلاب الناجحين للصفوف التالية وإعادة تسجيل الطلاب الراسبين</p>
                </div>
            </div>

            <a href="{{ route('admin.promotions.history') }}" class="btn-history" data-bs-toggle="tooltip"
                title="تصفح سجل الترقيات والتراجع عنها">
                <i class='bx bx-history me-1'></i> سجل الترقيات
            </a>
        </div>

        {{-- 🌟 كروت الإحصائيات 🌟 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-gradient-primary">
                    <div class="position-relative z-1">
                        <div class="stat-label">إجمالي الطلاب</div>
                        <div class="stat-value">{{ $students->count() ?? 0 }}</div>
                    </div>
                    <i class='bx bx-user stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-success">
                    <div class="position-relative z-1">
                        <div class="stat-label">الناجحون</div>
                        <div class="stat-value" id="passedCount">0</div>
                    </div>
                    <i class='bx bx-check-circle stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-danger">
                    <div class="position-relative z-1">
                        <div class="stat-label">الراسبون</div>
                        <div class="stat-value" id="failedCount">0</div>
                    </div>
                    <i class='bx bx-x-circle stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-warning">
                    <div class="position-relative z-1">
                        <div class="stat-label">بانتظار القرار</div>
                        <div class="stat-value" id="pendingCount">{{ $students->count() ?? 0 }}</div>
                    </div>
                    <i class='bx bx-time-five stat-icon'></i>
                </div>
            </div>
        </div>

        {{-- رسائل النجاح والخطأ --}}
        @if (session('success'))
            <div class="alert alert-success fw-bold shadow-sm rounded-3 border-0">
                <i class='bx bx-check-circle me-2 fs-5 align-middle'></i> {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger fw-bold shadow-sm rounded-3 border-0">
                <i class='bx bx-error-circle me-2 fs-5 align-middle'></i> {{ $errors->first() }}
            </div>
        @endif

        {{-- 🌟 كارت الفلترة 🌟 --}}
        <div class="filter-card mb-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.promotions.index') }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="filter-label"><i class='bx bx-buildings me-1'></i> الفرع / المدرسة</label>
                            <select name="branch_id" id="branch_id" class="form-select filter-input select2-init" required>
                                <option value="" disabled selected>اختر الفرع أولاً...</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="filter-label"><i class='bx bx-calendar me-1'></i> العام الدراسي الحالي</label>
                            <select name="from_academic_year_id" id="from_academic_year_id"
                                class="form-select filter-input select2-init" required
                                {{ !request('branch_id') ? 'disabled' : '' }}>
                                <option value="">-- بانتظار الفرع --</option>
                                @if (request('branch_id'))
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}"
                                            {{ request('from_academic_year_id') == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="filter-label"><i class='bx bx-book-open me-1'></i> الصف الدراسي الحالي</label>
                            <select name="from_grade_id" id="from_grade_id" class="form-select filter-input select2-init"
                                required>
                                <option value="" disabled selected>اختر الصف...</option>
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade->id }}"
                                        {{ request('from_grade_id') == $grade->id ? 'selected' : '' }}>
                                        {{ $grade->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="filter-label"><i class='bx bx-door-open me-1'></i> الشعبة (اختياري)</label>
                            <select name="from_section_id" id="from_section_id"
                                class="form-select filter-input select2-init"
                                {{ !request('from_grade_id') ? 'disabled' : '' }}>
                                <option value="">-- بانتظار الصف --</option>
                                @if (isset($sections) && $sections->count() > 0)
                                    <option value="">-- كل الشعب --</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}"
                                            {{ request('from_section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->name_ar }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn-filter w-100" id="btnFilter">
                                <i class='bx bx-search me-1'></i> استعراض الطلاب للترحيل
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 جدول الطلاب المرشحين 🌟 --}}
        @if (isset($students) && $students->count() > 0)
            {{-- صندوق التعليمات --}}
            <div class="info-box">
                <div class="d-flex align-items-start gap-3">
                    <div class="info-icon flex-shrink-0">
                        <i class='bx bx-info-circle'></i>
                    </div>
                    <div>
                        <h5 class="mb-3">تعليمات الترحيل المالي والأكاديمي الذكي:</h5>
                        <div class="info-item">
                            <i class='bx bx-check-circle me-1'></i> النظام سيقوم بنقل الطلاب تلقائياً إلى <b>العام الدراسي
                                التالي</b>.
                        </div>
                        <div class="info-item">
                            <i class='bx bx-check-circle me-1'></i> سيتم توليد <b>مسودة عقد مالي جديد</b> للعام القادم
                            آلياً لجميع الطلاب المُرحلين.
                        </div>
                        <div class="info-item warning">
                            <i class='bx bx-error me-1'></i> <b>هام للمحاسب:</b> إذا كان على الطالب مديونية سابقة، سيتم
                            ترحيلها كـ (رصيد افتتاحي متبقي) في عقده الجديد.
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.promotions.store') }}" method="POST" id="promotionForm">
                @csrf
                <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                <input type="hidden" name="from_academic_year_id" value="{{ request('from_academic_year_id') }}">
                <input type="hidden" name="from_grade_id" value="{{ request('from_grade_id') }}">

                <div class="card card-premium table-card">
                    <div
                        class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                                <i class='bx bx-list-check text-success fs-4'></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">قائمة الطلاب المُرشحين للترحيل</h5>
                                <small class="text-muted">إجمالي: {{ $students->count() }} طالب</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn-bulk btn-bulk-pass" id="btnSetAllPass">
                                <i class='bx bx-check-double me-1'></i> تحديد الكل ناجح
                            </button>
                            <button type="button" class="btn-bulk btn-bulk-fail" id="btnSetAllFail">
                                <i class='bx bx-x me-1'></i> تحديد الكل راسب
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom text-center align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="45%" class="text-start ps-4">بيانات الطالب</th>
                                    <th width="50%">القرار الأكاديمي (نجاح / رسوب)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $index => $student)
                                    <tr class="student-row row-promoted" id="row_{{ $student->id }}">
                                        <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="student-avatar flex-shrink-0">
                                                    {{ mb_substr($student->first_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="student-name">
                                                        {{ $student->first_name }} {{ $student->second_name }}
                                                        {{ $student->tribe }}
                                                    </div>
                                                    <div class="mt-1 d-flex gap-1 flex-wrap">
                                                        <span class="student-code">
                                                            <i
                                                                class='bx bx-barcode me-1'></i>{{ $student->student_code ?? 'بدون كود' }}
                                                        </span>
                                                        @if ($student->section)
                                                            <span class="student-section">
                                                                <i
                                                                    class='bx bx-door-open me-1'></i>{{ $student->section->name_ar }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="decision-group shadow-sm">
                                                <input type="radio" class="btn-check radio-pass status-toggle"
                                                    name="students[{{ $student->id }}]" id="pass_{{ $student->id }}"
                                                    value="promoted" checked autocomplete="off">
                                                <label class="btn btn-outline-success" for="pass_{{ $student->id }}">
                                                    <i class='bx bx-check-circle me-1'></i> ناجح ومُنتقل
                                                </label>

                                                <input type="radio" class="btn-check radio-fail status-toggle"
                                                    name="students[{{ $student->id }}]" id="fail_{{ $student->id }}"
                                                    value="retained" autocomplete="off">
                                                <label class="btn btn-outline-danger" for="fail_{{ $student->id }}">
                                                    <i class='bx bx-x-circle me-1'></i> راسب (إعادة السنة)
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- شريط الحفظ العائم --}}
                    <div class="sticky-footer text-end">
                        @can('manage_promotions')
                            <button type="submit" class="btn-submit" id="btnSubmitPromotion">
                                <i class='bx bx-check-double me-1'></i> اعتماد قرارات الترحيل آلياً
                            </button>
                        @else
                            <div class="alert alert-warning mb-0 border-0 fw-bold shadow-sm d-inline-block text-start w-100">
                                <i class='bx bx-lock-alt me-1'></i> عذراً، لا تملك الصلاحية لاعتماد الترحيل.
                            </div>
                        @endcan
                    </div>
                </div>
            </form>
        @elseif(request()->filled('from_academic_year_id'))
            <div class="text-center py-5">
                <i class='bx bx-search-alt text-warning opacity-25' style="font-size: 100px;"></i>
                <h4 class="fw-bold text-dark mt-3">لا يوجد طلاب مطابقين للبحث</h4>
                <p class="text-muted font-14 fw-normal mt-2">
                    عذراً، لم يتم العثور على أي طلاب نشطين مسجلين في هذا النطاق المختار.<br>
                    قد يكون تم ترحيلهم مسبقاً أو لا توجد بيانات مسجلة.
                </p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: "rtl",
                width: '100%'
            });

            // 🌟 تحديث الإحصائيات 🌟
            function updateStats() {
                let total = $('.student-row').length;
                let passed = $('.radio-pass:checked').length;
                let failed = $('.radio-fail:checked').length;
                let pending = total - passed - failed;

                $('#passedCount').text(passed);
                $('#failedCount').text(failed);
                $('#pendingCount').text(pending);
            }

            // تحديث أولي
            updateStats();

            // 🌟 تغيير لون الصف آلياً عند اختيار ناجح أو راسب 🌟
            $('.status-toggle').on('change', function() {
                let tr = $(this).closest('tr');
                if ($(this).val() === 'promoted') {
                    tr.removeClass('row-retained').addClass('row-promoted');
                } else {
                    tr.removeClass('row-promoted').addClass('row-retained');
                }
                updateStats();
            });

            function loadSections() {
                let branchId = $('#branch_id').val();
                let yearId = $('#from_academic_year_id').val();
                let gradeId = $('#from_grade_id').val();
                let sectionSelect = $('#from_section_id');

                if (branchId && yearId && gradeId) {
                    sectionSelect.prop('disabled', true).html('<option value="">⏳ التحميل...</option>');
                    $.ajax({
                        url: "{{ route('admin.get-filtered-sections') }}",
                        type: "GET",
                        data: {
                            branch_id: branchId,
                            academic_year_id: yearId,
                            grade_id: gradeId
                        },
                        success: function(data) {
                            let options = '<option value="">-- كل الشعب --</option>';
                            $.each(data, function(index, sec) {
                                options += `<option value="${sec.id}">${sec.name_ar}</option>`;
                            });
                            sectionSelect.html(options).prop('disabled', false);
                        }
                    });
                }
            }

            $('#branch_id').on('change', function() {
                let branchId = $(this).val();
                let yearSelect = $('#from_academic_year_id');

                if (branchId) {
                    yearSelect.prop('disabled', true).html('<option value="">⏳ التحميل...</option>');
                    let yearsUrl = "{{ route('admin.get-branch-academic-years', ':id') }}".replace(':id',
                        branchId);

                    $.get(yearsUrl, function(data) {
                        let options =
                            '<option value="" disabled selected>-- اختر العام الدراسي --</option>';
                        $.each(data, function(index, year) {
                            options += `<option value="${year.id}">${year.name}</option>`;
                        });
                        yearSelect.html(options).prop('disabled', false);
                        loadSections();
                    });
                }
            });

            $('#from_academic_year_id, #from_grade_id').on('change', function() {
                loadSections();
            });

            // 🌟 وظائف تحديد الكل الذكية 🌟
            $('#btnSetAllPass').click(function() {
                $('.radio-pass').prop('checked', true).trigger('change');
            });

            $('#btnSetAllFail').click(function() {
                $('.radio-fail').prop('checked', true).trigger('change');
            });

            // حماية الاعتماد بـ SweetAlert
            $('#promotionForm').on('submit', function(e) {
                e.preventDefault();
                let form = this;

                Swal.fire({
                    title: 'تأكيد الترحيل النهائي',
                    html: "سيتم ترحيل الطلاب وتوليد <b>عقود مالية جديدة</b> للعام القادم (مع نقل المديونيات السابقة إن وجدت).<br><br>هل أنت متأكد من صحة القرارات الأكاديمية؟",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bx bx-check-double"></i> نعم، اعتمد الترحيل',
                    cancelButtonText: 'إلغاء المراجعة'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let submitBtn = $('#btnSubmitPromotion');
                        submitBtn.prop('disabled', true).html(
                            '<i class="bx bx-loader bx-spin me-1"></i> جاري الأتمتة والترحيل...'
                        );

                        Swal.fire({
                            title: 'جاري المعالجة...',
                            text: 'يرجى الانتظار، النظام يقوم ببناء السجلات والعقود.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
