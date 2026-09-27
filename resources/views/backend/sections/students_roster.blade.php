@extends('backend.layouts.master')
@section('title', 'سجل الطلاب | ' . $section->name_ar)
@section('title_page', 'إدارة الشعب - قائمة الطلاب')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* 🌟 Premium UI Tweaks 🌟 */
        .roster-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 12px;
            border-right: 5px solid #0d6efd;
        }

        .student-avatar {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .capacity-bar {
            height: 10px;
            border-radius: 10px;
            background-color: #e2e8f0;
        }

        .table-custom-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            margin-top: 0;
        }

        .badge-soft {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 12px;
        }

        .table-erp {
            white-space: nowrap;
            /* تأمين الجداول من التكسير */
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px;
        }

        .whatsapp-icon {
            color: #10b981;
            background: #ecfdf5;
            padding: 5px 8px;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .whatsapp-icon:hover {
            background: #10b981;
            color: #fff;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .roster-header {
            background: #323a46;
            border-color: #424e5a;
            border-right-color: #3b82f6;
        }

        body[data-layout-color="dark"] .bg-white {
            background-color: #323a46 !important;
        }

        body[data-layout-color="dark"] .table-erp thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .capacity-bar {
            background-color: #424e5a;
        }

        body[data-layout-color="dark"] .text-dark {
            color: #f8fafc !important;
        }

        /* 🖨️ إعدادات الطباعة النظيفة 🖨️ */
        @media print {
            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print,
            .left-side-menu,
            .navbar-custom,
            .page-title-box,
            .footer,
            .checkbox-col,
            .action-col {
                display: none !important;
            }

            .roster-header {
                box-shadow: none !important;
                border: 1px solid #ddd;
                break-inside: avoid;
            }

            .table-responsive {
                max-height: none !important;
                overflow: visible !important;
            }

            .table-erp th,
            .table-erp td {
                border: 1px solid #ddd !important;
            }
        }
    </style>
@endpush

@section('content')
    {{-- 🌟 إحصائيات الكثافة 🌟 --}}
    @php
        $currentCount = $students->count();
        $maxCapacity = $section->max_capacity ?? 1; // حماية من القسمة على صفر
        $percentage = ($currentCount / $maxCapacity) * 100;

        if ($percentage >= 95) {
            $progressColor = 'bg-danger';
            $statusText = 'ممتلئ أو شبه ممتلئ';
        } elseif ($percentage >= 80) {
            $progressColor = 'bg-warning';
            $statusText = 'يقترب من الحد الأقصى';
        } else {
            $progressColor = 'bg-success';
            $statusText = 'كثافة طبيعية';
        }
    @endphp

    <div class="container-fluid py-4">
        {{-- 🌟 رأس الصفحة (Header) 🌟 --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="roster-header p-4 shadow-sm border text-start">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center shadow-sm"
                                style="width: 70px; height: 70px;">
                                <i class="mdi mdi-google-classroom text-primary" style="font-size: 35px;"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold text-dark mb-1">سجل الطلاب: {{ $section->name_ar }}</h3>
                                <div class="d-flex gap-2 flex-wrap mt-2">
                                    <span class="badge bg-light text-dark border shadow-sm px-2 py-1"><i
                                            class="mdi mdi-domain text-info me-1"></i>{{ $section->branch->name_ar ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border shadow-sm px-2 py-1"><i
                                            class="mdi mdi-school-outline text-success me-1"></i>{{ $section->grade->name_ar ?? '-' }}</span>
                                    <span class="badge bg-light text-dark border shadow-sm px-2 py-1"><i
                                            class="mdi mdi-calendar-clock text-warning me-1"></i>{{ $section->academicYear->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-end bg-white p-3 rounded-3 shadow-sm border" style="min-width: 250px;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted font-13 fw-bold">الكثافة ({{ $statusText }})</span>
                                <span class="text-dark fw-bold font-monospace">{{ $currentCount }} /
                                    {{ $maxCapacity }}</span>
                            </div>
                            <div class="progress capacity-bar border">
                                <div class="progress-bar {{ $progressColor }} progress-bar-striped progress-bar-animated"
                                    role="progressbar" style="width: {{ $percentage }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded-3 fw-bold border-0"><i class="mdi mdi-check-circle me-1"></i>
                {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger shadow-sm rounded-3 fw-bold border-0"><i class="mdi mdi-alert-circle me-1"></i>
                {{ session('error') }}</div>
        @endif

        {{-- 🌟 شريط الأدوات (Toolbar) 🌟 --}}
        <div class="row mb-3 d-print-none">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="fw-bold mb-0 text-dark"><i
                        class="mdi mdi-account-group-outline text-primary me-1 fs-4 align-middle"></i> قائمة الأسماء المقيدة
                    (<span class="text-primary">{{ $currentCount }}</span> طالب)</h5>

                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary shadow-sm rounded-pill fw-bold bg-white px-4"
                        onclick="window.print()">
                        <i class="mdi mdi-printer me-1"></i> طباعة كشف مناداة
                    </button>

                    @can('edit_sections')
                        <button type="button" class="btn btn-primary shadow-sm rounded-pill fw-bold px-4"
                            id="btn-bulk-transfer" data-bs-toggle="modal" data-bs-target="#bulkTransferModal" disabled>
                            <i class="mdi mdi-swap-horizontal me-1"></i> نقل المحددين
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        {{-- 🌟 جدول الطلاب 🌟 --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <form id="bulkTransferForm" action="{{ route('admin.sections.bulk_transfer') }}" method="POST">
                            @csrf
                            <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                                <table class="table table-erp table-hover table-centered mb-0 align-middle text-center">
                                    <thead class="sticky-top" style="z-index: 5;">
                                        <tr>
                                            <th style="width: 50px;" class="checkbox-col">
                                                <input type="checkbox"
                                                    class="form-check-input table-custom-checkbox shadow-sm" id="checkAll">
                                            </th>
                                            <th style="width: 60px;">#</th>
                                            <th class="text-start ps-3">اسم الطالب (الكود)</th>
                                            <th>الرقم المدني</th>
                                            <th>تاريخ الميلاد</th>
                                            <th class="text-start">ولي الأمر / الهاتف</th>
                                            <th>حالة القيد</th>
                                            <th class="action-col">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $index => $student)
                                            <tr>
                                                <td class="checkbox-col">
                                                    <input type="checkbox" name="student_ids[]"
                                                        class="form-check-input table-custom-checkbox student-checkbox shadow-sm"
                                                        value="{{ $student->id }}">
                                                </td>
                                                <td><span class="fw-bold text-muted">{{ $index + 1 }}</span></td>
                                                <td class="text-start ps-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm d-inline-block align-middle me-3">
                                                            <span
                                                                class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle font-16 fw-bold shadow-sm">
                                                                {{ mb_substr($student->first_name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="m-0 font-14 fw-bold text-dark">
                                                                {{ $student->first_name }} {{ $student->second_name }}
                                                                {{ $student->tribe }}</h6>
                                                            <small class="text-muted d-block mt-1">
                                                                <i
                                                                    class="mdi mdi-gender-{{ $student->gender == 'male' ? 'male text-primary' : 'female text-danger' }} me-1"></i>
                                                                {{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }} | كود:
                                                                <span
                                                                    class="font-monospace">{{ $student->student_code ?? '---' }}</span>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span
                                                        class="text-dark font-13 font-monospace fw-bold">{{ $student->national_id ?? '---' }}</span>
                                                </td>
                                                <td><span
                                                        class="text-muted font-13 font-monospace">{{ $student->dob ?? '---' }}</span>
                                                </td>
                                                <td class="text-start">
                                                    <h6 class="m-0 font-13 text-dark fw-bold">
                                                        {{ $student->parent->name_ar ?? '---' }}</h6>
                                                    @if (!empty($student->parent->phone))
                                                        <a href="https://wa.me/{{ preg_replace('/[^0-9\+]/', '', $student->parent->phone) }}"
                                                            target="_blank" rel="noopener noreferrer"
                                                            class="whatsapp-icon d-inline-block mt-1 font-monospace text-decoration-none shadow-sm">
                                                            <i class="mdi mdi-whatsapp"></i> {{ $student->parent->phone }}
                                                        </a>
                                                    @else
                                                        <small class="text-muted"><i class="mdi mdi-phone"></i> ---</small>
                                                    @endif
                                                </td>
                                                <td><span
                                                        class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 badge-soft px-3 shadow-sm"><i
                                                            class="mdi mdi-check-circle me-1"></i> مقيد نشط</span></td>
                                                <td class="action-col">
                                                    <a href="{{ route('admin.students.show', $student->id) }}"
                                                        class="btn btn-sm btn-light border rounded-circle shadow-sm mx-1"
                                                        title="عرض الملف">
                                                        <i class="mdi mdi-account-details text-primary font-18"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5 bg-white">
                                                    <i
                                                        class="mdi mdi-account-off-outline fs-1 d-block mb-2 text-danger opacity-50"></i>
                                                    <h5 class="text-muted fw-bold">لا يوجد طلاب مقيدين في هذه الشعبة.</h5>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- 🔥 مودال النقل الجماعي 🔥 --}}
                            @can('edit_sections')
                                <div class="modal fade" id="bulkTransferModal" tabindex="-1" aria-hidden="true"
                                    data-bs-backdrop="static">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div
                                            class="modal-content border-0 shadow-lg rounded-4 border-top border-primary border-4">
                                            <div class="modal-header border-bottom-0 bg-light rounded-top-4 pt-4 px-4">
                                                <h5 class="modal-title font-bold text-primary">
                                                    <i class="mdi mdi-swap-horizontal fs-4 align-middle me-1"></i> نقل الطلاب
                                                    المحددين (<span id="modal-student-count"
                                                        class="text-danger font-monospace">0</span>)
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start bg-white">
                                                <div
                                                    class="alert alert-warning border-warning border-opacity-25 bg-warning bg-opacity-10 shadow-sm font-13 fw-bold mb-4">
                                                    <i class="mdi mdi-alert-outline me-1 fs-5 align-middle"></i> سيتم نقل
                                                    الطلاب المحددين من الشعبة الحالية بشكل نهائي إلى الشعبة الوجهة.
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label text-muted fw-bold">الفرع الوجهة <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select fw-bold shadow-sm" name="new_branch_id"
                                                        id="new_branch_id" required>
                                                        <option value="" disabled selected>-- اختر الفرع --</option>
                                                        @foreach (\App\Models\Branche::where('is_active', 1)->get() as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->name_ar }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label text-muted fw-bold">الشعبة (الفصل) الوجهة <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select fw-bold shadow-sm border-primary"
                                                        name="new_section_id" id="new_section_id" required disabled>
                                                        <option value="" disabled selected>-- اختر الفرع أولاً... --
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label text-muted fw-bold">سبب النقل (ملاحظة
                                                        اختيارية)</label>
                                                    <input type="text" name="transfer_reason"
                                                        class="form-control shadow-sm"
                                                        placeholder="مثال: بناء على طلب ولي الأمر أو إعادة التوزيع">
                                                </div>
                                            </div>
                                            <div
                                                class="modal-footer border-top-0 bg-light rounded-bottom-4 justify-content-between">
                                                <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold"
                                                    data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" id="btn-modal-submit"
                                                    class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
                                                    <i class="mdi mdi-check-decagram-outline me-1"></i> تأكيد ونقل الطلاب
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan

                        </form>
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
            // 🌟 منطق تحديد الطلاب 🌟
            const checkAll = $('#checkAll');
            const studentCheckboxes = $('.student-checkbox');
            const bulkTransferBtn = $('#btn-bulk-transfer');
            const modalStudentCount = $('#modal-student-count');

            function updateBulkButtonState() {
                const checkedCount = $('.student-checkbox:checked').length;
                if (checkedCount > 0) {
                    bulkTransferBtn.prop('disabled', false).html(
                        `<i class="mdi mdi-swap-horizontal me-1"></i> نقل المحددين (${checkedCount})`);
                    modalStudentCount.text(checkedCount);
                } else {
                    bulkTransferBtn.prop('disabled', true).html(
                        `<i class="mdi mdi-swap-horizontal me-1"></i> نقل المحددين`);
                }
            }

            checkAll.on('change', function() {
                studentCheckboxes.prop('checked', $(this).is(':checked'));
                updateBulkButtonState();
            });

            studentCheckboxes.on('change', function() {
                if (!$(this).is(':checked')) checkAll.prop('checked', false);
                else if ($('.student-checkbox:checked').length === studentCheckboxes.length) checkAll.prop(
                    'checked', true);
                updateBulkButtonState();
            });

            // 🌟 AJAX لجلب الشعب المتاحة في الفرع المحدد 🌟
            $('#new_branch_id').change(function() {
                let branchId = $(this).val();
                let sectionSelect = $('#new_section_id');

                sectionSelect.empty().append(
                    '<option value="" disabled selected>⏳ جاري التحميل...</option>').prop('disabled',
                    true);

                let localeUrl = "{{ url(app()->getLocale()) }}";

                if (branchId) {
                    $.ajax({
                        url: `${localeUrl}/admin/get-branch-sections/${branchId}`,
                        type: "GET",
                        success: function(data) {
                            sectionSelect.empty().append(
                                '<option value="" disabled selected>-- اختر الشعبة الوجهة --</option>'
                                );
                            if (data.length > 0) {
                                $.each(data, function(key, section) {
                                    // التأكد من عدم إظهار الشعبة الحالية في الخيارات
                                    if (section.id != {{ $section->id }}) {
                                        sectionSelect.append(
                                            `<option value="${section.id}">${section.name_ar} - (${section.grade.name_ar} - ${section.academic_year.name})</option>`
                                            );
                                    }
                                });
                                sectionSelect.prop('disabled', false);
                            } else {
                                sectionSelect.append(
                                    '<option value="" disabled>لا توجد شعب أخرى مسجلة في هذا الفرع</option>'
                                    );
                            }
                        },
                        error: function() {
                            sectionSelect.empty().append(
                                '<option value="" disabled>حدث خطأ في التحميل</option>');
                        }
                    });
                }
            });

            // 🔥 تأمين فورم النقل الجماعي (Double Submit & Confirmation) 🔥
            $('#bulkTransferForm').on('submit', function(e) {
                e.preventDefault();
                let form = this;

                Swal.fire({
                    title: 'تأكيد النقل الجماعي',
                    text: `هل أنت متأكد من نقل عدد (${modalStudentCount.text()}) طلاب إلى الشعبة الجديدة؟ سيتم نقل ملفاتهم المالية والأكاديمية كلياً.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، انقل الطلاب الآن',
                    cancelButtonText: 'تراجع وإلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#btn-modal-submit').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-1 fs-5 align-middle"></i> جاري نقل السجلات...'
                            );
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
