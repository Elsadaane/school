@extends('backend.layouts.master')
@section('title', 'سجل الحضور والغياب | ' . $section->name_ar)

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .roster-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 12px;
            border-right: 5px solid #0d6efd;
        }

        /* 🌟 Custom Styles for Radio Buttons 🌟 */
        .btn-check-custom:checked+.btn-outline-success {
            background-color: #10b981;
            color: white;
            border-color: #10b981;
        }

        .btn-check-custom:checked+.btn-outline-danger {
            background-color: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        .btn-check-custom:checked+.btn-outline-warning {
            background-color: #f59e0b;
            color: #fff;
            border-color: #f59e0b;
        }

        .btn-check-custom:checked+.btn-outline-info {
            background-color: #0ea5e9;
            color: white;
            border-color: #0ea5e9;
        }

        .attendance-btn-group .btn {
            padding: 8px 15px;
            font-weight: bold;
            font-size: 13px;
            box-shadow: none !important;
        }

        .table-custom {
            white-space: nowrap;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
        }

        .table-custom tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 10px 14px;
        }

        /* 🌟 Scrollable Table for large classes 🌟 */
        .table-scrollable-container {
            max-height: 60vh;
            overflow-y: auto;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .roster-header,
        body[data-layout-color="dark"] .card {
            background-color: #323a46 !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .roster-header {
            background: linear-gradient(135deg, #323a46 0%, #3b4652 100%);
        }

        body[data-layout-color="dark"] .table-custom thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom tbody td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .bottom-action-bar {
            background-color: #323a46 !important;
            border-top-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 رأس الصفحة 🌟 --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="roster-header p-4 shadow-sm border text-start">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center shadow-sm"
                            style="width: 60px; height: 60px;">
                            <i class="mdi mdi-calendar-check text-primary" style="font-size: 30px;"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold text-dark mb-1">سجل الحضور والغياب: {{ $section->name_ar }}</h3>
                            <div class="d-flex gap-2 flex-wrap mt-2">
                                <span class="badge bg-light text-dark border shadow-sm px-3 py-1">
                                    <i class="mdi mdi-domain text-info me-1"></i> {{ $section->branch->name_ar ?? '-' }}
                                </span>
                                <span class="badge bg-light text-dark border shadow-sm px-3 py-1">
                                    <i class="mdi mdi-school-outline text-success me-1"></i>
                                    {{ $section->grade->name_ar ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded-3 fw-bold border-0">
                <i class="mdi mdi-check-circle fs-5 align-middle me-1"></i> {{ session('success') }}
            </div>
        @endif

        {{-- 🌟 فورم الحضور والغياب 🌟 --}}
        <form action="{{ route('admin.sections.storeAttendance', $section->id) }}" method="POST" id="attendanceForm"
            class="prevent-double-submit">
            @csrf

            {{-- شريط التاريخ والعدد --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 border-top border-primary border-4">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <label class="fw-bold mb-0 text-dark"><i
                                class="mdi mdi-calendar-cursor text-primary me-1 fs-5 align-middle"></i> تاريخ
                            السجل:</label>
                        <input type="date"
                            class="form-control fw-bold border-primary shadow-sm font-monospace text-center fs-6"
                            name="attendance_date" id="attendanceDate" value="{{ $date }}" style="width: 220px;"
                            data-original-url="{{ route('admin.sections.attendance', $section->id) }}">
                    </div>
                    <div>
                        <span class="badge bg-primary px-4 py-2 font-14 rounded-pill shadow-sm">
                            <i class="mdi mdi-account-group me-1"></i> إجمالي الطلاب: {{ $students->count() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- جدول الطلاب --}}
            <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive table-scrollable-container">
                        <table class="table table-hover table-custom mb-0 align-middle">
                            <thead class="sticky-top shadow-sm" style="z-index: 10;">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th class="text-start ps-4">اسم الطالب / الكود</th>
                                    <th class="text-center">حالة الحضور (تسجيل سريع)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $student)
                                    @php
                                        // تحديد الحالة الافتراضية
                                        $currentStatus = isset($attendances[$student->id])
                                            ? $attendances[$student->id]->status
                                            : 'present';
                                    @endphp
                                    <tr>
                                        <td class="text-center"><span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                        </td>
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-sm d-inline-block align-middle me-3 shadow-sm rounded-circle">
                                                    <span
                                                        class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle font-18 fw-bold">
                                                        {{ mb_substr($student->first_name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="m-0 font-15 fw-bold text-dark">{{ $student->first_name }}
                                                        {{ $student->second_name }} {{ $student->tribe }}</h6>
                                                    <small class="text-muted font-monospace"><i
                                                            class="mdi mdi-barcode-scan me-1"></i>{{ $student->student_code ?? 'بدون كود' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group attendance-btn-group shadow-sm" role="group">
                                                <input type="radio" class="btn-check btn-check-custom status-radio"
                                                    name="attendances[{{ $student->id }}]"
                                                    id="present_{{ $student->id }}" value="present"
                                                    {{ $currentStatus == 'present' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success"
                                                    for="present_{{ $student->id }}">حاضر</label>

                                                <input type="radio" class="btn-check btn-check-custom status-radio"
                                                    name="attendances[{{ $student->id }}]" id="late_{{ $student->id }}"
                                                    value="late" {{ $currentStatus == 'late' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning"
                                                    for="late_{{ $student->id }}">متأخر</label>

                                                <input type="radio" class="btn-check btn-check-custom status-radio"
                                                    name="attendances[{{ $student->id }}]"
                                                    id="excused_{{ $student->id }}" value="excused"
                                                    {{ $currentStatus == 'excused' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-info"
                                                    for="excused_{{ $student->id }}">مستأذن</label>

                                                <input type="radio" class="btn-check btn-check-custom status-radio"
                                                    name="attendances[{{ $student->id }}]"
                                                    id="absent_{{ $student->id }}" value="absent"
                                                    {{ $currentStatus == 'absent' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger"
                                                    for="absent_{{ $student->id }}">غائب</label>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted fw-bold fs-5 bg-white">
                                            <i
                                                class="mdi mdi-account-off-outline fs-1 d-block mb-2 opacity-50 text-danger"></i>
                                            لا يوجد طلاب مسجلين في هذه الشعبة حتى الآن.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- زر الحفظ ثابت أسفل الشاشة --}}
            @if ($students->count() > 0)
                <div class="position-fixed bottom-0 start-0 end-0 p-3 bg-white border-top shadow-lg text-center text-md-start bottom-action-bar"
                    style="z-index: 1000;">
                    <div class="container-fluid">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow fw-bold btn-submit">
                            <i class="mdi mdi-content-save-all me-1 fs-4 align-middle"></i> حفظ واعتماد سجل الحضور
                        </button>
                    </div>
                </div>
            @endif

            {{-- مساحة فارغة أسفل الفورم لمنع تغطية الزر الثابت لآخر صف --}}
            <div style="height: 90px;"></div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            let isDirty = false;

            // رصد أي تغيير في الفورم
            $('.status-radio').on('change', function() {
                isDirty = true;
            });

            // تأمين تغيير التاريخ (UX Enhancement)
            $('#attendanceDate').on('change', function(e) {
                let newDate = $(this).val();
                let url = $(this).data('original-url') + '?attendance_date=' + newDate;

                if (isDirty) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'تغيير التاريخ؟',
                        text: "لقد قمت بتعديل بعض الحالات، إذا قمت بتغيير التاريخ الآن سيتم فقدان التعديلات غير المحفوظة.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3b82f6',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'نعم، غيّر التاريخ',
                        cancelButtonText: 'تراجع'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            showLoadingOverlay();
                            window.location.href = url;
                        } else {
                            // إعادة التاريخ للقديم
                            $(this).val("{{ $date }}");
                        }
                    });
                } else {
                    showLoadingOverlay();
                    window.location.href = url;
                }
            });

            // عرض شاشة تحميل بسيطة عند التنقل
            function showLoadingOverlay() {
                Swal.fire({
                    title: 'جاري جلب السجل...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            // تأمين الفورم ضد الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1 fs-4 align-middle"></i> جاري الحفظ والمعالجة...'
                    );
                isDirty = false; // تصفير الحالة عشان ميطلعش تحذير الخروج
            });
        });
    </script>
@endpush
