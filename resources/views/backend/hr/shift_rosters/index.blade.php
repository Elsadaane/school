@extends('backend.layouts.master')
@section('title', 'توزيع الورديات')
@section('title_page', 'الموارد البشرية - إدارة الورديات المتغيرة (Shift Rosters)')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        .page-wrapper {
            background-color: #f8f9fa;
            padding: 25px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .table-custom {
            white-space: nowrap;
            /* لمنع تكسير النصوص في الشاشات الصغيرة */
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
            padding: 15px;
            border-bottom: 1px dashed #eee;
            font-weight: 600;
        }

        .shift-badge {
            background-color: #e3f2fd;
            color: #0d6efd;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #0d6efd;
            font-weight: bold;
        }

        .date-badge {
            background-color: #fff3cd;
            color: #856404;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-family: monospace;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 class="fw-bolder text-dark mb-0">
                <i class='mdi mdi-calendar-clock-outline text-primary me-2 fs-3 align-middle'></i> جدول الورديات وتوزيع
                الموظفين
            </h4>

            {{-- 🔥 زرار تعيين وردية محمي 🔥 --}}
            @can('manage_shifts_attendance')
                <button class="btn btn-primary fw-bold shadow-sm px-4 py-2 rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#addRosterModal">
                    <i class='mdi mdi-plus me-1'></i> تعيين وردية جديدة
                </button>
            @endcan
        </div>

        {{-- 🔥 شريط فلترة التواريخ (مربوط بالكنترولر) 🔥 --}}
        <div class="card card-custom mb-4 bg-white shadow-sm border-0">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.hr.shift_rosters.index') }}" class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">استعراض الورديات من تاريخ:</label>
                        <input type="date" name="start_date" class="form-control bg-light fw-bold text-center"
                            value="{{ request('start_date', $startOfWeek) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">إلى تاريخ:</label>
                        <input type="date" name="end_date" class="form-control bg-light fw-bold text-center"
                            value="{{ request('end_date', $endOfWeek) }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-dark w-100 fw-bold py-2 rounded-3 shadow-sm">
                            <i class='mdi mdi-filter-outline me-1'></i> تصفية السجل
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- جدول الورديات --}}
        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover table-nowrap w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="text-start ps-4">الموظف</th>
                                <th>الوردية المُعينة</th>
                                <th>توقيت الوردية</th>
                                <th>تاريخ البدء</th>
                                <th>تاريخ الانتهاء</th>
                                {{-- 🔥 عمود الإجراءات محمي 🔥 --}}
                                @can('manage_shifts_attendance')
                                    <th>الإجراءات</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rosters as $roster)
                                <tr>
                                    <td class="text-start ps-4 text-primary fw-bolder">
                                        {{ $roster->employee->full_name_ar ?? 'موظف محذوف' }}
                                        <small
                                            class="d-block text-muted mt-1">{{ $roster->employee->department->name_ar ?? 'بدون قسم' }}</small>
                                    </td>
                                    <td>
                                        <span class="shift-badge">{{ $roster->shift->name_ar ?? 'بدون وردية' }}</span>
                                    </td>
                                    <td class="text-muted fw-bold font-monospace">
                                        {{ \Carbon\Carbon::parse($roster->shift->start_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($roster->shift->end_time)->format('h:i A') }}
                                    </td>
                                    <td>
                                        <span
                                            class="date-badge fw-bold">{{ \Carbon\Carbon::parse($roster->start_date)->format('Y-m-d') }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="date-badge fw-bold">{{ \Carbon\Carbon::parse($roster->end_date)->format('Y-m-d') }}</span>
                                    </td>

                                    {{-- 🔥 أزرار الإجراءات محمية 🔥 --}}
                                    @can('manage_shifts_attendance')
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary rounded-circle btn-edit-roster shadow-sm"
                                                    data-id="{{ $roster->id }}" data-employee="{{ $roster->employee_id }}"
                                                    data-shift="{{ $roster->shift_id }}"
                                                    data-start="{{ \Carbon\Carbon::parse($roster->start_date)->format('Y-m-d') }}"
                                                    data-end="{{ \Carbon\Carbon::parse($roster->end_date)->format('Y-m-d') }}"
                                                    data-bs-toggle="modal" data-bs-target="#editRosterModal"
                                                    title="تعديل الوردية">
                                                    <i class='mdi mdi-pencil-outline font-16'></i>
                                                </button>

                                                <form action="{{ route('admin.hr.shift_rosters.destroy', $roster->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger rounded-circle btn-delete shadow-sm"
                                                        title="إلغاء التعيين">
                                                        <i class='mdi mdi-trash-can-outline font-16'></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('manage_shifts_attendance') ? 6 : 5 }}"
                                        class="p-5 text-muted text-center">
                                        <i class='mdi mdi-calendar-remove-outline fs-1 d-block mb-2 opacity-25'></i>
                                        <h6 class="fw-bold">لم يتم تعيين أي ورديات متغيرة لهذه الفترة.</h6>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 إضافة الترقيم (Pagination) 🔥 --}}
            @if (isset($rosters) && $rosters->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-3">
                    {{ $rosters->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- 🔥 مودال تعيين وردية محمي 🔥 --}}
    @can('manage_shifts_attendance')
        <div class="modal fade" id="addRosterModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-calendar-plus me-1'></i> تعيين وردية
                            جديدة للموظف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.shift_rosters.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 bg-light text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اختر الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select border-primary fw-bold select2-init" required
                                    style="width: 100%;">
                                    <option value="" disabled selected>-- قائمة الموظفين النشطين --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">اختر الوردية <span class="text-danger">*</span></label>
                                <select name="shift_id" class="form-select select2-init fw-bold" required style="width: 100%;">
                                    <option value="" disabled selected>-- قائمة الورديات المتاحة --</option>
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}">
                                            {{ $shift->name_ar }}
                                            ({{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-3">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-success">تاريخ البدء <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control text-center fw-bold"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-danger">تاريخ الانتهاء <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control text-center fw-bold" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-white rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm rounded-pill btn-submit"><i
                                    class="mdi mdi-check-all me-1"></i> حفظ وتعيين الوردية</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔥 مودال تعديل الوردية محمي 🔥 --}}
        <div class="modal fade" id="editRosterModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-calendar-edit me-1'></i> تعديل وردية
                            الموظف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editRosterForm" class="prevent-double-submit">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4 bg-light text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" id="edit_employee_id"
                                    class="form-select border-primary fw-bold select2-edit" required style="width: 100%;">
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">الوردية <span class="text-danger">*</span></label>
                                <select name="shift_id" id="edit_shift_id" class="form-select select2-edit fw-bold" required
                                    style="width: 100%;">
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}">
                                            {{ $shift->name_ar }}
                                            ({{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-3">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-success">تاريخ البدء <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="edit_start_date"
                                        class="form-control text-center fw-bold" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-danger">تاريخ الانتهاء <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="edit_end_date"
                                        class="form-control text-center fw-bold" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-white rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm rounded-pill btn-submit"><i
                                    class="mdi mdi-check me-1"></i> حفظ التعديلات</button>
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
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل مكتبة Select2 عند فتح المودال لمنع تشوهات الـ UI
            $('#addRosterModal').on('shown.bs.modal', function() {
                $('.select2-init').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#addRosterModal')
                });
            });

            $('#editRosterModal').on('shown.bs.modal', function() {
                $('.select2-edit').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#editRosterModal')
                });
            });

            // حماية الفورم من الإرسال المزدوج (Double Submit Protection)
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري الحفظ...');
            });

            // تمرير البيانات لمودال التعديل
            $('.btn-edit-roster').click(function() {
                let id = $(this).data('id');

                // تحديث قيم الـ Select2
                $('#edit_employee_id').val($(this).data('employee')).trigger('change');
                $('#edit_shift_id').val($(this).data('shift')).trigger('change');

                $('#edit_start_date').val($(this).data('start'));
                $('#edit_end_date').val($(this).data('end'));

                let url = "{{ route('admin.hr.shift_rosters.update', ':id') }}".replace(':id', id);
                $('#editRosterForm').attr('action', url);
            });

            // سكريبت تأكيد الحذف
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: "تأكيد الإلغاء؟",
                    text: "هل أنت متأكد من إلغاء تعيين هذه الوردية للموظف؟ قد يؤثر ذلك على تقارير الحضور والغياب في هذه الفترة.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: "نعم، ألغِ التعيين",
                    cancelButtonText: "تراجع"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // إشعارات SweetAlert2 
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: "تم بنجاح",
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: "مرفوض أمنياً!",
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#1e2b3c'
                });
            @endif

            @if ($errors->any())
                let errorHtml =
                    '<ul class="text-start text-danger fw-bold mb-0" style="list-style: none; padding:0;">';
                @foreach ($errors->all() as $error)
                    errorHtml += '<li><i class="mdi mdi-close-circle-outline me-1"></i> {{ $error }}</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الإدخال',
                    html: errorHtml,
                    confirmButtonColor: '#1e2b3c'
                });
            @endif
        });
    </script>
@endpush
