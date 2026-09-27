@extends('backend.layouts.master')
@section('title', 'التسكين الأكاديمي للطلاب')
@section('title_page', 'شؤون الطلاب - تسكين الفصول (Placement)')

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

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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

        /* 🌟 كروت الصفوف 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
        }

        .card-premium.students-card {
            border-top: 5px solid #ef4444;
        }

        .card-premium.sections-card {
            border-top: 5px solid #10b981;
        }

        .section-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        }

        .section-card:hover {
            border-color: #3b82f6;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.15);
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }

        .section-card.selected {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #d1fae5 100%);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .section-card.full {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-color: #fca5a5;
            opacity: 0.7;
            cursor: not-allowed;
        }

        .section-card .section-name {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .section-card .section-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .section-card .section-count {
            font-family: 'Courier New', monospace;
            font-weight: 800;
            font-size: 14px;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .section-card .section-count.available {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .section-card .section-count.full {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .section-card .remaining-space {
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .section-card .remaining-space.ok {
            color: #059669;
        }

        .section-card .remaining-space.danger {
            color: #dc2626;
        }

        /* 🌟 شريط التقدم 🌟 */
        .progress-bar-custom {
            height: 12px;
            border-radius: 6px;
            background-color: #e2e8f0;
            margin-top: 12px;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
            transition: width 0.5s ease-in-out;
            border-radius: 6px;
        }

        .progress-fill.warning {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }

        .progress-fill.full {
            background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
        }

        /* 🌟 جدول الطلاب 🌟 */
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
            padding: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px 14px;
            transition: background-color 0.2s ease;
        }

        .table-custom tbody tr:hover td {
            background-color: #f8fafc;
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
        }

        /* 🌟 شارات الجنس 🌟 */
        .gender-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .gender-male {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .gender-female {
            background: #fce7f3;
            color: #9d174d;
            border: 1px solid #f9a8d4;
        }

        /* 🌟 أزرار العمليات 🌟 */
        .btn-fetch {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .btn-fetch:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        .btn-assign {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 14px 30px;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-assign:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: #fff;
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
                    <i class='bx bx-group text-light fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">التسكين وتوزيع الفصول</h4>
                    <p class="text-muted mb-0 font-13">توزيع الطلاب الجدد أو نقلهم بين الشعب حسب السعة الاستيعابية</p>
                </div>
            </div>
        </div>

        {{-- 🌟 كروت الإحصائيات 🌟 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-gradient-primary">
                    <div class="position-relative z-1">
                        <div class="stat-label">إجمالي الطلاب</div>
                        <div class="stat-value" id="totalStudentsStat">0</div>
                    </div>
                    <i class='bx bx-user stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-success">
                    <div class="position-relative z-1">
                        <div class="stat-label">طلاب مُسكّنين</div>
                        <div class="stat-value" id="assignedStudentsStat">0</div>
                    </div>
                    <i class='bx bx-check-circle stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-warning">
                    <div class="position-relative z-1">
                        <div class="stat-label">بانتظار التسكين</div>
                        <div class="stat-value" id="pendingStudentsStat">0</div>
                    </div>
                    <i class='bx bx-time-five stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-danger">
                    <div class="position-relative z-1">
                        <div class="stat-label">فصول ممتلئة</div>
                        <div class="stat-value" id="fullSectionsStat">0</div>
                    </div>
                    <i class='bx bx-error stat-icon'></i>
                </div>
            </div>
        </div>

        {{-- 🌟 فلاتر البحث 🌟 --}}
        <div class="filter-card mb-4">
            <div class="card-body p-4">
                <form id="fetchForm">
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="filter-label"><i class='bx bx-buildings me-1'></i> الفرع / المدرسة</label>
                            <select name="branch_id" id="branch_id" class="form-select filter-input select2-init" required>
                                <option value="" disabled selected>-- اختر الفرع أولاً --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="filter-label"><i class='bx bx-calendar me-1'></i> العام الدراسي</label>
                            <select name="academic_year_id" id="academic_year_id"
                                class="form-select filter-input select2-init" required disabled>
                                <option value="">-- بانتظار اختيار الفرع --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="filter-label"><i class='bx bx-book-open me-1'></i> الصف الدراسي</label>
                            <select name="grade_id" id="grade_id" class="form-select filter-input select2-init" required>
                                <option value="" disabled selected>-- اختر الصف --</option>
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade->id }}">{{ $grade->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="filter-label"><i class='bx bx-transfer me-1'></i> نوع العملية</label>
                            <select name="placement_type" id="placement_type" class="form-select filter-input select2-init"
                                required>
                                <option value="new" selected>تسكين طلاب جدد (غير مسكنين)</option>
                                <option value="transfer">نقل داخلي (من فصل لفصل)</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-none" id="from_section_wrapper">
                            <label class="filter-label"><i class='bx bx-log-out me-1'></i> الفصل الحالي (النقل منه)</label>
                            <select name="from_section_id" id="from_section_id"
                                class="form-select filter-input select2-init">
                                <option value="" disabled selected>-- بانتظار اختيار الفرع --</option>
                            </select>
                        </div>
                        <div class="col-md-4 ms-auto">
                            <button type="submit" class="btn-fetch w-100">
                                <i class='bx bx-search me-1'></i> جلب الطلاب والشعب
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🌟 منطقة التسكين (مخفية حتى يتم البحث) 🌟 --}}
        <div class="row d-none" id="placementArea">

            {{-- قائمة الطلاب --}}
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="card card-premium students-card h-100">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-danger bg-opacity-10 p-2 rounded-circle">
                                <i class='bx bx-user text-danger fs-4'></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark" id="studentsListTitle">
                                    قائمة الطلاب (<span id="studentsCount" class="font-monospace">0</span>)
                                </h5>
                                <small class="text-muted">حدد الطلاب المطلوب تسكينهم</small>
                            </div>
                        </div>
                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" id="selectAll"
                                style="transform: scale(1.3); cursor: pointer;">
                            <label class="form-check-label fw-bold" for="selectAll" style="cursor: pointer;">تحديد
                                الكل</label>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                            <table class="table table-hover table-custom w-100" id="studentsTable">
                                <thead class="sticky-top" style="z-index: 1;">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">تحديد</th>
                                        <th class="text-start ps-3">اسم الطالب (الكود)</th>
                                        <th class="text-center">الجنس</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- يتم التعبئة بواسطة AJAX --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- قائمة الشعب المتاحة --}}
            <div class="col-lg-5">
                <div class="card card-premium sections-card h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                                <i class='bx bx-door-open text-success fs-4'></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">الفصول المتاحة للتسكين</h5>
                                <small class="text-muted">اختر الفصل المطلوب النقل إليه</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3" id="sectionsContainer"
                        style="max-height: 470px; overflow-y: auto; background: #f8fafc;">
                        {{-- يتم التعبئة بواسطة AJAX --}}
                    </div>
                    <div class="card-footer bg-white border-top text-center py-3">
                        @can('manage_student_placements')
                            <button type="button" class="btn-assign" id="btnAssign">
                                <i class='bx bx-check-double me-1'></i> تنفيذ التسكين / النقل
                            </button>
                        @else
                            <div class="alert alert-warning mb-0 fw-bold border-warning shadow-sm">
                                <i class='bx bx-lock-alt me-1'></i> عفواً، لا تملك صلاحية اعتماد تسكين الطلاب.
                            </div>
                        @endcan
                    </div>
                </div>
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

            // تفعيل Select2 بثيم Bootstrap 5
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // 🔥 الذكاء في جلب السنوات بناءً على الفرع 🔥
            $('#branch_id').change(function() {
                let branchId = $(this).val();
                let yearSelect = $('#academic_year_id');

                if (branchId) {
                    yearSelect.prop('disabled', true).html('<option value="">جاري التحميل...</option>');

                    let yearsUrl = "{{ route('admin.get-branch-academic-years', ':id') }}".replace(':id',
                        branchId);
                    $.get(yearsUrl, function(data) {
                        let options = '<option value="">-- اختر العام الدراسي --</option>';
                        $.each(data, function(index, year) {
                            options += `<option value="${year.id}">${year.name}</option>`;
                        });
                        yearSelect.html(options).prop('disabled', false);
                    });

                    loadSourceSections();
                } else {
                    yearSelect.html('<option value="">-- بانتظار اختيار الفرع --</option>').prop('disabled',
                        true);
                }
            });

            // 🔥 التحكم في ظهور الفصول للنقل الداخلي 🔥
            $('#placement_type').change(function() {
                if ($(this).val() === 'transfer') {
                    $('#from_section_wrapper').removeClass('d-none');
                    $('#from_section_id').prop('required', true);
                    loadSourceSections();
                } else {
                    $('#from_section_wrapper').addClass('d-none');
                    $('#from_section_id').prop('required', false);
                }
            });

            function loadSourceSections() {
                let branchId = $('#branch_id').val();
                let type = $('#placement_type').val();

                if (type === 'transfer' && branchId) {
                    $('#from_section_id').prop('disabled', true).html('<option value="">جاري التحميل...</option>');
                    let sectionsUrl = `/admin/get-branch-sections/${branchId}`;
                    $.get(sectionsUrl, function(data) {
                        let options = '<option value="" disabled selected>-- اختر الفصل الحالي --</option>';
                        $.each(data, function(index, sec) {
                            options += `<option value="${sec.id}">${sec.name_ar}</option>`;
                        });
                        $('#from_section_id').html(options).prop('disabled', false);
                    });
                }
            }

            let selectedSectionId = null;

            // جلب الداتا بالأجاكس
            $('#fetchForm').submit(function(e) {
                e.preventDefault();
                let btn = $(this).find('button[type="submit"]');
                let originalText = btn.html();
                btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i> جاري البحث...');

                $.ajax({
                    url: "{{ route('admin.placements.fetch') }}",
                    type: "POST",
                    data: $(this).serialize() + "&_token={{ csrf_token() }}",
                    success: function(response) {
                        $('#placementArea').removeClass('d-none');

                        // تحديث الإحصائيات
                        updateStats(response);

                        // تغيير العنوان بناء على العملية
                        let type = $('#placement_type').val();
                        if (type === 'transfer') {
                            $('#studentsListTitle').html(
                                `طلاب الفصل الحالي (<span id="studentsCount" class="font-monospace">${response.students.length}</span>)`
                            );
                        } else {
                            $('#studentsListTitle').html(
                                `طلاب قيد الانتظار (<span id="studentsCount" class="font-monospace">${response.students.length}</span>)`
                            );
                        }

                        renderStudents(response.students, type);
                        renderSections(response.sections, $('#from_section_id').val(), type);

                        btn.prop('disabled', false).html(
                            '<i class="bx bx-check me-1"></i> تم التحديث');
                        setTimeout(() => btn.html(originalText), 2000);
                        selectedSectionId = null;
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(originalText);
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ في الاتصال بالخادم، يرجى المحاولة لاحقاً.',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            });

            // 🔥 تحديث الإحصائيات 🔥
            function updateStats(response) {
                $('#totalStudentsStat').text(response.students.length);
                $('#assignedStudentsStat').text(response.assigned_count || 0);
                $('#pendingStudentsStat').text(response.students.length);
                $('#fullSectionsStat').text(response.full_sections_count || 0);
            }

            function renderStudents(students, type) {
                let tbody = $('#studentsTable tbody');
                tbody.empty();
                $('#studentsCount').text(students.length);
                $('#selectAll').prop('checked', false);

                if (students.length === 0) {
                    let msg = type === 'transfer' ? 'لا يوجد طلاب مسجلين في هذا الفصل حالياً.' :
                        'جميع الطلاب في هذا الصف مُسكنين بالفعل.';
                    tbody.append(`
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <div class="py-4">
                                    <i class='bx bx-check-circle text-success opacity-25' style="font-size: 80px;"></i>
                                    <h5 class="fw-bold text-muted mt-3">${msg}</h5>
                                </div>
                            </td>
                        </tr>
                    `);
                    return;
                }

                students.forEach(student => {
                    let genderBadge = student.gender === 'male' ?
                        `<span class="gender-badge gender-male"><i class='bx bx-male'></i> ذكر</span>` :
                        `<span class="gender-badge gender-female"><i class='bx bx-female'></i> أنثى</span>`;

                    let tr = `
                        <tr>
                            <td class="text-center">
                                <input class="form-check-input student-checkbox" type="checkbox" value="${student.id}" style="cursor: pointer;">
                            </td>
                            <td class="text-start ps-3">
                                <div class="student-name">
                                    <i class='bx bx-user-circle text-primary me-1'></i>
                                    ${student.first_name} ${student.second_name || ''} ${student.tribe || ''}
                                </div>
                                <div class="student-code mt-1">
                                    <i class='bx bx-hash me-1'></i>${student.student_code || '---'}
                                </div>
                            </td>
                            <td class="text-center">${genderBadge}</td>
                        </tr>
                    `;
                    tbody.append(tr);
                });
            }

            function renderSections(sections, fromSectionId, type) {
                let container = $('#sectionsContainer');
                container.empty();

                let validSections = sections;

                if (type === 'transfer' && fromSectionId) {
                    validSections = sections.filter(sec => sec.id != fromSectionId);
                }

                if (validSections.length === 0) {
                    container.append(`
                        <div class="text-center py-5">
                            <i class='bx bx-error text-warning opacity-25' style="font-size: 80px;"></i>
                            <h5 class="fw-bold text-muted mt-3">لا توجد فصول متاحة للنقل إليها</h5>
                        </div>
                    `);
                    return;
                }

                validSections.forEach(sec => {
                    let remainingSpace = sec.max_capacity - sec.students_count;
                    let percent = (sec.students_count / sec.max_capacity) * 100;
                    let isFull = sec.students_count >= sec.max_capacity;
                    let barColor = isFull ? 'full' : (percent > 75 ? 'warning' : '');
                    let cardClass = isFull ? 'full' : '';
                    let countClass = isFull ? 'full' : 'available';
                    let spaceClass = isFull ? 'danger' : 'ok';

                    let html = `
                        <div class="section-card ${cardClass}" data-id="${sec.id}" data-full="${isFull}" data-remaining="${remainingSpace}">
                            <div class="section-name">
                                <i class='bx bx-door-open text-primary me-1'></i> ${sec.name_ar}
                            </div>
                            <div class="section-stats">
                                <span class="remaining-space ${spaceClass}">
                                    <i class='bx bx-${isFull ? 'x-circle' : 'check-circle'}'></i>
                                    ${isFull ? 'ممتلئ' : `${remainingSpace} مقعد شاغر`}
                                </span>
                                <span class="section-count ${countClass}">
                                    ${sec.students_count} / ${sec.max_capacity}
                                </span>
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill ${barColor}" style="width: ${percent}%"></div>
                            </div>
                        </div>
                    `;
                    container.append(html);
                });
            }

            // تحديد الكل مع المزامنة الديناميكية
            $('#selectAll').change(function() {
                $('.student-checkbox').prop('checked', $(this).prop('checked'));
            });

            $(document).on('change', '.student-checkbox', function() {
                if (!$(this).prop('checked')) {
                    $('#selectAll').prop('checked', false);
                } else if ($('.student-checkbox:checked').length === $('.student-checkbox').length) {
                    $('#selectAll').prop('checked', true);
                }
            });

            // اختيار شعبة (تحديد بطاقة الفصل)
            $(document).on('click', '.section-card', function() {
                if ($(this).data('full') === true) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'الفصل ممتلئ!',
                        text: 'لا يوجد مقاعد شاغرة في هذه الشعبة، يرجى اختيار شعبة أخرى.',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }
                $('.section-card').removeClass('selected');
                $(this).addClass('selected');
                selectedSectionId = $(this).data('id');
            });

            // تسكين الطلاب (حفظ واعتماد)
            $('#btnAssign').click(function() {
                let selectedStudents = [];
                $('.student-checkbox:checked').each(function() {
                    selectedStudents.push($(this).val());
                });

                if (selectedStudents.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى تحديد طالب واحد على الأقل من القائمة.',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }
                if (!selectedSectionId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى اختيار الشعبة المُراد النقل/التسكين إليها.',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }

                // 🔥 التحقق من السعة الاستيعابية 🔥
                let selectedSectionCard = $('.section-card.selected');
                let remainingCapacity = parseInt(selectedSectionCard.data('remaining'));

                if (selectedStudents.length > remainingCapacity) {
                    Swal.fire({
                        icon: 'error',
                        title: 'تجاوز السعة الاستيعابية!',
                        html: `لقد قمت بتحديد <b>(${selectedStudents.length})</b> طلاب، بينما عدد المقاعد الشاغرة هو <b>(${remainingCapacity})</b> فقط.`,
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                Swal.fire({
                    title: 'تأكيد الإجراء',
                    html: `هل أنت متأكد من تنفيذ العملية لعدد <b>(${selectedStudents.length})</b> طالب؟`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، اعتمد',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let btnAssign = $('#btnAssign');
                        let originalBtnText = btnAssign.html();
                        btnAssign.prop('disabled', true).html(
                            '<i class="bx bx-loader bx-spin me-1"></i> جاري الاعتماد...');

                        $.ajax({
                            url: "{{ route('admin.placements.store') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                section_id: selectedSectionId,
                                student_ids: selectedStudents
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'تمت العملية بنجاح',
                                    text: response.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                                btnAssign.prop('disabled', false).html(originalBtnText);
                                $('#fetchForm').submit();
                            },
                            error: function(xhr) {
                                btnAssign.prop('disabled', false).html(originalBtnText);
                                let errorMsg = xhr.responseJSON ? xhr.responseJSON
                                    .message :
                                    'حدث خطأ غير متوقع أثناء الاتصال بالخادم.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'خطأ',
                                    text: errorMsg,
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
