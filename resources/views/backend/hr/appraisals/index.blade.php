@extends('backend.layouts.master')
@section('title', 'تقييمات الأداء')
@section('title_page', 'الموارد البشرية - سجل التقييمات والأداء')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* 🌟 Premium UI 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 24px;
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
            font-size: 14px;
            color: #334155;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* دوائر الدرجات */
        .score-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 15px;
            margin: auto;
            color: white;
        }

        .score-excellent {
            background: linear-gradient(135deg, #10b981, #34d399);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .score-good {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
        }

        .score-bad {
            background: linear-gradient(135deg, #ef4444, #f87171);
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }

        /* الأزرار الموحدة */
        .action-btn {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            font-size: 18px;
            margin: 0 3px;
            background: #fff;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .action-btn.edit {
            background: #e0f2fe;
            color: #0284c7;
            border-color: #bae6fd;
        }

        .action-btn.edit:hover {
            background: #0284c7;
            color: #fff;
            border-color: #0284c7;
        }

        .action-btn.delete {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fecaca;
        }

        .action-btn.delete:hover {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }

        /* تحسينات الـ Select2 */
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #cbd5e1;
            min-height: 42px;
            border-radius: 8px;
            background-color: #f8fafc;
            font-weight: 600;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
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

        body[data-layout-color="dark"] .action-btn {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #ced4da;
        }

        body[data-layout-color="dark"] .bg-light,
        body[data-layout-color="dark"] .modal-header {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🔥 كروت الإحصائيات 🔥 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-premium border-bottom border-primary border-4 shadow-sm h-100">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold mb-2">إجمالي التقييمات</h6>
                            <h3 class="fw-bolder text-primary mb-0">{{ $total }}</h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class='bx bx-bar-chart-alt-2'></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-premium border-bottom border-success border-4 shadow-sm h-100">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold mb-2">أداء ممتاز</h6>
                            <h3 class="fw-bolder text-success mb-0">{{ $excellent }}</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success"><i class='bx bx-medal'></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-premium border-bottom border-warning border-4 shadow-sm h-100">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold mb-2">أداء جيد جداً</h6>
                            <h3 class="fw-bolder text-warning mb-0">{{ $good }}</h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class='bx bx-like'></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-premium border-bottom border-danger border-4 shadow-sm h-100">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold mb-2">يحتاج تحسين</h6>
                            <h3 class="fw-bolder text-danger mb-0">{{ $needsImprovement }}</h3>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class='bx bx-trending-down'></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- رأس الجدول وزر الإضافة --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 class="fw-bolder text-dark mb-0">
                <i class='bx bx-line-chart text-success me-2 fs-3 align-middle'></i>
                سجل تقييمات الأداء
            </h4>

            @can('create_appraisal')
                <button class="btn btn-success fw-bold shadow-sm px-4 py-2 rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#addAppraisalModal">
                    <i class='bx bx-plus-circle me-1 align-middle fs-5'></i> إضافة تقييم جديد
                </button>
            @endcan
        </div>

        {{-- جدول التقييمات --}}
        <div class="card card-premium mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-erp table-hover w-100 mb-0">
                        <thead class="sticky-top" style="z-index: 10;">
                            <tr>
                                <th class="text-start ps-4">الموظف والقسم</th>
                                <th>فترة التقييم والتاريخ</th>
                                <th>الدرجة</th>
                                <th>التقييم العام</th>
                                <th>اسم المُقيِّم</th>
                                <th>ملاحظات</th>
                                @canany(['edit_appraisal', 'delete_appraisal'])
                                    <th style="width: 150px;">الإجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appraisals as $appraisal)
                                @php
                                    $score = $appraisal->score_out_of_100;
                                    $scoreClass =
                                        $score >= 90 ? 'score-excellent' : ($score >= 75 ? 'score-good' : 'score-bad');
                                    $scoreText = $score >= 90 ? 'ممتاز' : ($score >= 75 ? 'جيد جداً' : 'يحتاج تحسين');
                                    $textColor =
                                        $score >= 90 ? 'text-success' : ($score >= 75 ? 'text-warning' : 'text-danger');
                                @endphp
                                <tr>
                                    <td class="text-start ps-4 text-primary fw-bolder">
                                        {{ $appraisal->employee->full_name_ar ?? 'محذوف من النظام' }}
                                        <div class="text-muted fw-bold font-12 mt-1">
                                            <i
                                                class="bx bx-buildings align-middle me-1"></i>{{ $appraisal->employee->department->name_ar ?? 'بدون قسم' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $appraisal->evaluation_period }}</div>
                                        <div class="text-muted font-12 font-monospace mt-1">
                                            <i
                                                class="bx bx-calendar-event align-middle me-1"></i>{{ \Carbon\Carbon::parse($appraisal->evaluation_date)->format('Y-m-d') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="score-circle {{ $scoreClass }} font-monospace shadow-sm">
                                            {{ $score }}</div>
                                    </td>
                                    <td class="fw-bold {{ $textColor }}">
                                        <i class="bx bx-star align-middle me-1"></i>{{ $scoreText }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-light text-dark border border-secondary border-opacity-25 px-3 py-2 rounded-pill font-13">
                                            <i class='bx bx-user-circle text-muted me-1 align-middle'></i>
                                            {{ $appraisal->evaluator->name ?? 'مدير النظام' }}
                                        </span>
                                    </td>
                                    <td class="text-secondary font-13"
                                        style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                        title="{{ $appraisal->feedback }}">
                                        {{ $appraisal->feedback ?? 'لا توجد ملاحظات مسجلة.' }}
                                    </td>

                                    @canany(['edit_appraisal', 'delete_appraisal'])
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center">
                                                @can('edit_appraisal')
                                                    <button type="button" class="action-btn edit btn-edit-appraisal shadow-sm"
                                                        data-id="{{ $appraisal->id }}"
                                                        data-employee="{{ $appraisal->employee_id }}"
                                                        data-period="{{ $appraisal->evaluation_period }}"
                                                        data-score="{{ $appraisal->score_out_of_100 }}"
                                                        data-date="{{ \Carbon\Carbon::parse($appraisal->evaluation_date)->format('Y-m-d') }}"
                                                        data-feedback="{{ $appraisal->feedback }}" data-bs-toggle="modal"
                                                        data-bs-target="#editAppraisalModal" title="تعديل التقييم">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                @endcan

                                                @can('delete_appraisal')
                                                    <form action="{{ route('admin.hr.appraisals.destroy', $appraisal->id) }}"
                                                        method="POST" class="d-inline-block delete-appraisal-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="action-btn delete btn-delete-appraisal shadow-sm" title="حذف">
                                                            <i class='bx bx-trash'></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="p-5 text-muted text-center fw-bold">
                                        <i class='bx bx-bar-chart-alt-2 fs-1 d-block mb-3 opacity-25'
                                            style="font-size: 50px;"></i>
                                        لا توجد تقييمات أداء مسجلة حالياً
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 إضافة الترقيم (Pagination) 🔥 --}}
            @if (method_exists($appraisals, 'hasPages') && $appraisals->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $appraisals->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- 🔥 مودال إضافة تقييم محمي 🔥 --}}
    @can('create_appraisal')
        <div class="modal fade" id="addAppraisalModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg border-top border-success border-4">
                    <div class="modal-header border-bottom-0 bg-light rounded-top-4 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-success"><i class='bx bx-target-lock me-1 fs-4 align-middle'></i>
                            إضافة تقييم جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.appraisals.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">اختر الموظف <span
                                        class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select select2-init" required style="width: 100%;">
                                    <option value="" disabled selected>-- قائمة الموظفين --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-muted">فترة التقييم <span
                                            class="text-danger">*</span></label>
                                    <select name="evaluation_period" class="form-select bg-light fw-bold border-0 shadow-sm"
                                        required>
                                        <option value="تقييم الربع الأول">الربع الأول (Q1)</option>
                                        <option value="تقييم الربع الثاني">الربع الثاني (Q2)</option>
                                        <option value="تقييم الربع الثالث">الربع الثالث (Q3)</option>
                                        <option value="تقييم الربع الرابع">الربع الرابع (Q4)</option>
                                        <option value="التقييم السنوي الشامل">التقييم السنوي الشامل</option>
                                        <option value="تقييم فترة التجربة">تقييم فترة التجربة</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-muted">الدرجة (من 100) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="score_out_of_100"
                                        class="form-control fw-bold bg-light text-center fs-5 text-success font-monospace border-0 shadow-sm"
                                        min="0" max="100" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">تاريخ التقييم <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="evaluation_date"
                                    class="form-control bg-light fw-bold text-center border-0 shadow-sm font-monospace"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold text-muted">ملاحظات أو توصيات التطوير</label>
                                <textarea name="feedback" class="form-control bg-light border-0 shadow-sm" rows="3"
                                    placeholder="اكتب ملاحظاتك حول أداء الموظف..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4 px-4 pb-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success px-5 rounded-pill fw-bold shadow-sm btn-submit">
                                <i class="bx bx-check-shield me-1 align-middle fs-5"></i> حفظ التقييم
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- 🔥 مودال تعديل تقييم محمي 🔥 --}}
    @can('edit_appraisal')
        <div class="modal fade" id="editAppraisalModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg border-top border-primary border-4">
                    <div class="modal-header border-bottom-0 bg-light rounded-top-4 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='bx bx-edit me-1 fs-4 align-middle'></i> تعديل
                            بيانات التقييم</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editAppraisalForm" class="prevent-double-submit">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">اختر الموظف <span
                                        class="text-danger">*</span></label>
                                <select name="employee_id" id="edit_employee_id" class="form-select select2-edit" required
                                    style="width: 100%;">
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-muted">فترة التقييم <span
                                            class="text-danger">*</span></label>
                                    <select name="evaluation_period" id="edit_evaluation_period"
                                        class="form-select bg-light fw-bold border-0 shadow-sm" required>
                                        <option value="تقييم الربع الأول">الربع الأول (Q1)</option>
                                        <option value="تقييم الربع الثاني">الربع الثاني (Q2)</option>
                                        <option value="تقييم الربع الثالث">الربع الثالث (Q3)</option>
                                        <option value="تقييم الربع الرابع">الربع الرابع (Q4)</option>
                                        <option value="التقييم السنوي الشامل">التقييم السنوي الشامل</option>
                                        <option value="تقييم فترة التجربة">تقييم فترة التجربة</option>
                                    </select>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-muted">الدرجة (من 100) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="score_out_of_100" id="edit_score_out_of_100"
                                        class="form-control fw-bold bg-light text-center fs-5 text-primary font-monospace border-0 shadow-sm"
                                        min="0" max="100" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">تاريخ التقييم <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="evaluation_date" id="edit_evaluation_date"
                                    class="form-control bg-light fw-bold text-center border-0 shadow-sm font-monospace"
                                    required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold text-muted">ملاحظات أو توصيات التطوير</label>
                                <textarea name="feedback" id="edit_feedback" class="form-control bg-light border-0 shadow-sm" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4 px-4 pb-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm btn-submit">
                                <i class="bx bx-check me-1 align-middle fs-5"></i> حفظ التعديلات
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            let isRtl = true; // النظام باللغة العربية دائمًا بناءً على التعديل

            // تفعيل الـ Select2 لمودال الإضافة
            $('#addAppraisalModal').on('shown.bs.modal', function() {
                $('.select2-init').select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl',
                    dropdownParent: $('#addAppraisalModal')
                });
            });

            // تفعيل الـ Select2 لمودال التعديل
            $('#editAppraisalModal').on('shown.bs.modal', function() {
                $('.select2-edit').select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl',
                    dropdownParent: $('#editAppraisalModal')
                });
            });

            // منع التكرار في الحفظ (Double Submit)
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader-alt bx-spin me-1 align-middle fs-5"></i> جاري الحفظ...');
            });

            // تمرير البيانات لمودال التعديل وتحديث مسار الفورم
            $('.btn-edit-appraisal').click(function() {
                let id = $(this).data('id');
                $('#edit_employee_id').val($(this).data('employee')).trigger('change');
                $('#edit_evaluation_period').val($(this).data('period'));
                $('#edit_score_out_of_100').val($(this).data('score'));
                $('#edit_evaluation_date').val($(this).data('date'));
                $('#edit_feedback').val($(this).data('feedback'));

                let actionUrl = "{{ route('admin.hr.appraisals.update', ':id') }}".replace(':id', id);
                $('#editAppraisalForm').attr('action', actionUrl);
            });

            // سكريبت تأكيد الحذف بـ SweetAlert
            $('.btn-delete-appraisal').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: "هل أنت متأكد من الحذف؟",
                    text: "سيتم مسح هذا التقييم نهائياً من سجل الموظف.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: "نعم، احذف نهائياً",
                    cancelButtonText: "إلغاء"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // إظهار رسائل النظام باستخدام SweetAlert
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: "عملية ناجحة",
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: "حدث خطأ!",
                    text: "{{ session('error') }}"
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: "خطأ في البيانات المدخلة",
                    html: `<ul class="text-start mb-0 fw-bold">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                           </ul>`
                });
            @endif
        });
    </script>
@endpush
