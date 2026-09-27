@extends('backend.layouts.master')

@section('title', 'السنوات الدراسية')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        /* 🌟 كروت السنوات الدراسية 🌟 */
        .year-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background: #fff;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 2px solid #e2e8f0;
        }

        .year-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .year-card.current {
            border-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
            box-shadow: 0 4px 25px rgba(59, 130, 246, 0.15);
        }

        .year-card.closed {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            opacity: 0.85;
        }

        .year-card .year-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            z-index: 2;
        }

        .year-badge.current {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .year-badge.closed {
            background: #e2e8f0;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .year-card .year-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .year-icon.active {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 2px solid #3b82f6;
        }

        .year-icon.closed {
            background: #e2e8f0;
            color: #64748b;
            border: 2px solid #cbd5e1;
        }

        .year-card .year-name {
            font-size: 22px;
            font-weight: 900;
            color: #1e293b;
            margin-bottom: 15px;
            font-family: 'Courier New', monospace;
        }

        .year-card .branches-count {
            background: #f1f5f9;
            color: #475569;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 15px;
        }

        .year-card .stats-list {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
            text-align: center;
        }

        .year-card .stats-list li {
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
        }

        .year-card .stats-list li:last-child {
            border-bottom: none;
        }

        .year-card .stats-list .stat-value {
            font-family: 'Courier New', monospace;
            font-weight: 800;
            font-size: 16px;
            display: inline-block;
            margin-right: 5px;
        }

        .stat-value.primary { color: #2563eb; }
        .stat-value.success { color: #059669; }
        .stat-value.danger { color: #dc2626; }

        .year-card .card-actions {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding-top: 15px;
            border-top: 2px solid #f1f5f9;
        }

        .action-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all 0.2s ease;
            border: 2px solid transparent;
            text-decoration: none;
            cursor: pointer;
            font-size: 18px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .action-btn.view {
            background: #dbeafe;
            color: #1e40af;
            border-color: #93c5fd;
        }

        .action-btn.view:hover {
            background: #2563eb;
            color: #fff;
        }

        .action-btn.edit {
            background: #fef3c7;
            color: #b45309;
            border-color: #fde68a;
        }

        .action-btn.edit:hover {
            background: #f59e0b;
            color: #fff;
        }

        .action-btn.delete {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fca5a5;
        }

        .action-btn.delete:hover {
            background: #ef4444;
            color: #fff;
        }

        .action-btn.close {
            background: #fef3c7;
            color: #92400e;
            border-color: #fde68a;
        }

        .action-btn.close:hover {
            background: #d97706;
            color: #fff;
        }

        /* 🌟 أزرار العمليات 🌟 */
        .btn-add {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        /* 🌟 المودال 🌟 */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 25px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }

        .modal-title {
            font-weight: 800;
            color: #1e40af;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            padding: 15px 25px;
            background: #f8fafc;
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: #fff;
        }

        .btn-cancel {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
            color: #fff;
        }

        .btn-bulk {
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 11px;
            border: 1px solid;
            transition: all 0.2s ease;
        }

        .btn-bulk.select-all {
            background: #dbeafe;
            color: #1e40af;
            border-color: #93c5fd;
        }

        .btn-bulk.select-all:hover {
            background: #3b82f6;
            color: #fff;
        }

        .btn-bulk.deselect-all {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fca5a5;
        }

        .btn-bulk.deselect-all:hover {
            background: #ef4444;
            color: #fff;
        }

        /* 🌟 Select2 تحسينات 🌟 */
        .select2-container .select2-selection--multiple {
            min-height: 45px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding-top: 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
            border-radius: 15px;
            padding: 2px 10px;
            font-weight: 700;
        }

        .select2-container--open {
            z-index: 9999999 !important;
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
                    <i class='bx bx-calendar text-primary fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">السنوات الدراسية</h4>
                    <p class="text-muted mb-0 font-13">إدارة السنوات الدراسية وتفعيلها في الفروع</p>
                </div>
            </div>

            @can('manage_academics')
                <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#addYearModal">
                    <i class='bx bx-plus-circle'></i>
                    <span>إضافة سنة دراسية</span>
                </button>
            @endcan
        </div>

        {{-- رسائل النجاح والخطأ --}}
        @if (session('success'))
            <div class="alert alert-success fw-bold shadow-sm rounded-3 border-0">
                <i class='bx bx-check-circle me-2 fs-5 align-middle'></i> {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger fw-bold shadow-sm rounded-3 border-0">
                <i class='bx bx-error-circle me-2 fs-5 align-middle'></i>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🌟 كروت السنوات الدراسية 🌟 --}}
        <div class="row g-4">
            @forelse($academicYears as $year)
                <div class="col-xl-3 col-md-6">
                    <div class="year-card {{ $year->is_current ? 'current' : '' }} {{ $year->is_closed ? 'closed' : '' }}">
                        
                        {{-- شارة الحالة --}}
                        @if ($year->is_current)
                            <span class="year-badge current">
                                <i class='bx bx-check-circle'></i> السنة الحالية
                            </span>
                        @elseif ($year->is_closed)
                            <span class="year-badge closed">
                                <i class='bx bx-lock'></i> مؤرشفة
                            </span>
                        @endif

                        <div class="card-body py-4 px-3 text-center">
                            {{-- أيقونة السنة --}}
                            <div class="year-icon {{ $year->is_closed ? 'closed' : 'active' }}">
                                <i class='bx {{ $year->is_closed ? 'bx-lock-alt' : 'bx-calendar' }}'></i>
                            </div>

                            {{-- اسم السنة --}}
                            <h3 class="year-name">{{ $year->name }}</h3>

                            {{-- عدد الفروع --}}
                            <div class="branches-count">
                                <i class='bx bx-buildings me-1'></i> مفعلة في: {{ $year->branches->count() }} فروع
                            </div>

                            {{-- الإحصائيات --}}
                            <ul class="stats-list">
                                <li>
                                    <i class='bx bx-book-open me-1'></i> الصفوف المفعلة:
                                    <span class="stat-value primary">{{ $year->active_grades_count ?? 0 }}</span>
                                </li>
                                <li>
                                    <i class='bx bx-door-open me-1'></i> الشعب والفصول:
                                    <span class="stat-value success">{{ $year->sections_count ?? 0 }}</span>
                                </li>
                                <li>
                                    <i class='bx bx-user me-1'></i> الطلاب المسجلين:
                                    <span class="stat-value danger">{{ $year->students_count ?? 0 }}</span>
                                </li>
                            </ul>

                            {{-- أزرار الإجراءات --}}
                            <div class="card-actions">
                                <a href="{{ route('admin.academic-years.show', $year->id) }}"
                                    class="action-btn view" title="عرض التفاصيل">
                                    <i class='bx bx-show'></i>
                                </a>

                                @if (!$year->is_closed)
                                    @can('manage_academics')
                                        <button type="button" class="action-btn edit"
                                            data-bs-toggle="modal" data-bs-target="#editYearModal{{ $year->id }}"
                                            title="تعديل">
                                            <i class='bx bx-edit'></i>
                                        </button>

                                        <button type="button" class="action-btn delete btn-delete-year"
                                            data-url="{{ route('admin.academic-years.destroy', $year->id) }}" title="حذف">
                                            <i class='bx bx-trash'></i>
                                        </button>

                                        <button type="button" class="action-btn close btn-close-year"
                                            data-id="{{ $year->id }}" title="إغلاق وأرشفة العام">
                                            <i class='bx bx-lock'></i>
                                        </button>

                                        <form id="form-close-{{ $year->id }}"
                                            action="{{ route('admin.academic-years.close', $year->id) }}" method="POST"
                                            style="display: none;"> @csrf </form>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- مودال التعديل --}}
                @can('manage_academics')
                    @if (!$year->is_closed)
                        <div class="modal fade" id="editYearModal{{ $year->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class='bx bx-edit me-2'></i> تعديل السنة الدراسية: {{ $year->name }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.academic-years.update', $year->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="form_type" value="edit_{{ $year->id }}">

                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        <i class='bx bx-buildings me-1'></i> الفروع
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="mb-2 d-flex gap-2">
                                                        <button type="button" class="btn-bulk select-all"
                                                            onclick="selectAll('#branches_edit_{{ $year->id }}')">
                                                            <i class='bx bx-check-double me-1'></i> تحديد الكل
                                                        </button>
                                                        <button type="button" class="btn-bulk deselect-all"
                                                            onclick="deselectAll('#branches_edit_{{ $year->id }}')">
                                                            <i class='bx bx-x me-1'></i> إلغاء
                                                        </button>
                                                    </div>
                                                    <select name="branches[]" id="branches_edit_{{ $year->id }}"
                                                        class="form-control select2-multiple" multiple required style="width: 100%;">
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}"
                                                                {{ in_array($branch->id, $year->branches->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                                {{ $branch->name_ar }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        <i class='bx bx-calendar me-1'></i> السنة الدراسية
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ $year->name }}" required dir="rtl"
                                                        placeholder="مثال: 2026/2027">
                                                </div>
                                            </div>
                                            <div class="row g-3 mt-2">
                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        <i class='bx bx-calendar-check me-1'></i> تاريخ البداية
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" name="start_date" class="form-control"
                                                        value="{{ $year->start_date }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">
                                                        <i class='bx bx-calendar-x me-1'></i> تاريخ النهاية
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" name="end_date" class="form-control"
                                                        value="{{ $year->end_date }}" required>
                                                </div>
                                            </div>
                                            <div class="form-check mt-3">
                                                <input class="form-check-input" type="checkbox" name="is_current"
                                                    id="is_current_edit{{ $year->id }}" value="1"
                                                    {{ $year->is_current ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="is_current_edit{{ $year->id }}">
                                                    <i class='bx bx-star me-1 text-primary'></i> تعيين كسنة دراسية حالية (نشطة)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">
                                                <i class='bx bx-x me-1'></i> إغلاق
                                            </button>
                                            <button type="submit" class="btn-submit">
                                                <i class='bx bx-check me-1'></i> حفظ التعديلات
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endcan
            @empty
                <div class="col-12 text-center py-5">
                    <div class="py-4">
                        <i class='bx bx-calendar-x text-muted opacity-25' style="font-size: 100px;"></i>
                        <h5 class="fw-bold text-muted mt-3">لا توجد سنوات دراسية مضافة حتى الآن</h5>
                        <p class="text-muted">اضغط على "إضافة سنة دراسية" لبدء الإضافة</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 🌟 مودال إضافة سنة دراسية 🌟 --}}
    @can('manage_academics')
        <div class="modal fade" id="addYearModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class='bx bx-plus-circle me-2'></i> إضافة سنة دراسية
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.academic-years.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="form_type" value="add">

                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class='bx bx-buildings me-1'></i> الفروع
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="mb-2 d-flex gap-2">
                                        <button type="button" class="btn-bulk select-all"
                                            onclick="selectAll('#branches_add')">
                                            <i class='bx bx-check-double me-1'></i> تحديد الكل
                                        </button>
                                        <button type="button" class="btn-bulk deselect-all"
                                            onclick="deselectAll('#branches_add')">
                                            <i class='bx bx-x me-1'></i> إلغاء
                                        </button>
                                    </div>
                                    <select name="branches[]" id="branches_add" class="form-control select2-multiple"
                                        multiple required style="width: 100%;">
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class='bx bx-calendar me-1'></i> السنة الدراسية
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="name" required
                                        placeholder="مثال: 2026/2027" dir="rtl">
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class='bx bx-calendar-check me-1'></i> تاريخ البداية
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class='bx bx-calendar-x me-1'></i> تاريخ النهاية
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="is_current" id="is_current_add" value="1">
                                <label class="form-check-label fw-bold" for="is_current_add">
                                    <i class='bx bx-star me-1 text-primary'></i> تعيين كسنة دراسية حالية (نشطة)
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">
                                <i class='bx bx-x me-1'></i> إغلاق
                            </button>
                            <button type="submit" class="btn-submit">
                                <i class='bx bx-check me-1'></i> إضافة
                            </button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // تفعيل Select2
            $('.select2-multiple').each(function() {
                $(this).select2({
                    placeholder: "ابحث واختر الفروع...",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(this).closest('.modal')
                });
            });

            // حذف السنة
            $('.btn-delete-year').click(function() {
                let deleteUrl = $(this).data('url');
                Swal.fire({
                    title: 'هل أنت متأكد من حذف هذه السنة؟',
                    text: "لن تتمكن على استرجاعها بعد الحذف!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bx bx-trash"></i> نعم، احذف!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('<form action="' + deleteUrl + '" method="POST">@csrf @method('DELETE')</form>').submit();
                    }
                });
            });

            // إغلاق وأرشفة السنة
            $('.btn-close-year').click(function() {
                let yearId = $(this).data('id');
                Swal.fire({
                    title: 'هل أنت متأكد من إغلاق العام الدراسي؟',
                    html: "تحذير: سيتم أرشفة جميع بيانات الطلاب كلقطة تاريخية، ولن تتمكن من تعديلها لاحقاً!<br><br><small class='text-danger'>هذا الإجراء لا يمكن التراجع عنه.</small>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bx bx-lock"></i> نعم، إغلاق وأرشفة!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'جاري الأرشفة...',
                            text: 'برجاء الانتظار...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        $('#form-close-' + yearId).submit();
                    }
                });
            });

            // فتح المودال تلقائياً عند وجود أخطاء
            @if ($errors->any())
                let formType = "{{ old('form_type') }}";
                if (formType && formType.startsWith('edit_')) {
                    let yearId = formType.replace('edit_', '');
                    let modalEl = document.getElementById('editYearModal' + yearId);
                    if (modalEl) new bootstrap.Modal(modalEl).show();
                } else {
                    let modalEl = document.getElementById('addYearModal');
                    if (modalEl) new bootstrap.Modal(modalEl).show();
                }
            @endif
        });

        function selectAll(selectId) {
            $(selectId + ' > option').prop("selected", true);
            $(selectId).trigger("change");
        }

        function deselectAll(selectId) {
            $(selectId).val(null).trigger('change');
        }
    </script>
@endpush