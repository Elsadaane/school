@extends('backend.layouts.master')
@section('title', 'إدارة المتدربين')
@section('title_page', 'الموارد البشرية - سجل المتدربين وفترات التجربة')

@push('css')
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

        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }

        .status-active {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            border: 1px solid #0d6efd;
        }

        .status-completed {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
            border: 1px solid #198754;
        }

        .status-cancelled {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🔥 كروت الإحصائيات 🔥 --}}
        <div class="row g-4 mb-4 text-center">
            <div class="col-md-4">
                <div class="card card-custom border-start border-primary border-5 h-100 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div class="text-start">
                            <h6 class="text-muted fw-bold mb-1">إجمالي المتدربين</h6>
                            <h2 class="fw-bolder text-primary mb-0 font-monospace">{{ $total }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle shadow-sm">
                            <i class='mdi mdi-school-outline fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom border-start border-info border-5 h-100 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div class="text-start">
                            <h6 class="text-muted fw-bold mb-1">تدريب جاري (نشط)</h6>
                            <h2 class="fw-bolder text-info mb-0 font-monospace">{{ $active }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle shadow-sm">
                            <i class='mdi mdi-progress-clock fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom border-start border-success border-5 h-100 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div class="text-start">
                            <h6 class="text-muted fw-bold mb-1">أنهوا فترة التدريب</h6>
                            <h2 class="fw-bolder text-success mb-0 font-monospace">{{ $completed }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle shadow-sm">
                            <i class='mdi mdi-certificate-outline fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- رأس الجدول وزر الإضافة --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 class="fw-bolder text-dark mb-0">
                <i class='mdi mdi-account-school-outline text-primary me-2 fs-3 align-middle'></i> سجل المتدربين الحاليين
                والسابقين
            </h4>

            {{-- 🔥 زر تسجيل متدرب محمي 🔥 --}}
            @can('manage_trainees')
                <button class="btn btn-primary fw-bold shadow-sm px-4 py-2 rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#addTraineeModal">
                    <i class='mdi mdi-plus me-1'></i> تسجيل متدرب جديد
                </button>
            @endcan
        </div>

        {{-- جدول المتدربين --}}
        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-nowrap table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="text-start ps-4">اسم المتدرب</th>
                                <th>الجهة التعليمية</th>
                                <th>قسم التدريب</th>
                                <th>فترة التدريب</th>
                                <th>الحالة</th>
                                {{-- 🔥 عمود الإجراءات محمي 🔥 --}}
                                @can('manage_trainees')
                                    <th>الإجراءات</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trainees as $trainee)
                                <tr>
                                    <td class="text-start ps-4">
                                        <div class="fw-bolder text-primary fs-6">{{ $trainee->full_name }}</div>
                                        <small class="text-muted font-monospace"><i class='mdi mdi-phone-outline'></i>
                                            {{ $trainee->phone }}</small>
                                    </td>
                                    <td class="text-secondary fw-bold text-wrap" style="max-width: 200px;">
                                        {{ $trainee->university_or_school ?? 'غير محدد' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-light text-dark border px-3 py-2 shadow-sm">{{ $trainee->department->name_ar ?? '-' }}</span>
                                    </td>
                                    <td class="text-muted font-monospace">
                                        <div class="fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($trainee->start_date)->format('Y-m-d') }}</div>
                                        <i class='mdi mdi-arrow-down text-primary'></i>
                                        <div class="fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($trainee->end_date)->format('Y-m-d') }}</div>
                                    </td>
                                    <td>
                                        @if ($trainee->status == 'active')
                                            <span class="status-badge status-active shadow-sm"><i
                                                    class='mdi mdi-loading mdi-spin me-1'></i> جاري التدريب</span>
                                        @elseif($trainee->status == 'completed')
                                            <span class="status-badge status-completed shadow-sm"><i
                                                    class='mdi mdi-shield-check-outline me-1'></i> مكتمل</span>
                                        @else
                                            <span class="status-badge status-cancelled shadow-sm"><i
                                                    class='mdi mdi-close-circle-outline me-1'></i> ملغي</span>
                                        @endif
                                    </td>

                                    {{-- 🔥 أزرار الإجراءات محمية 🔥 --}}
                                    @can('manage_trainees')
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                @if ($trainee->status == 'active')
                                                    {{-- إنهاء التدريب بنجاح --}}
                                                    <form action="{{ route('admin.hr.trainees.status', $trainee->id) }}"
                                                        method="POST" class="d-inline status-form">
                                                        @csrf
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-success rounded-circle shadow-sm btn-change-status"
                                                            data-action="completed" title="إنهاء التدريب بنجاح">
                                                            <i class='mdi mdi-check-all font-16'></i>
                                                        </button>
                                                    </form>

                                                    {{-- إلغاء التدريب --}}
                                                    <form action="{{ route('admin.hr.trainees.status', $trainee->id) }}"
                                                        method="POST" class="d-inline status-form">
                                                        @csrf
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-warning text-dark rounded-circle shadow-sm btn-change-status"
                                                            data-action="cancelled" title="إلغاء التدريب">
                                                            <i class='mdi mdi-cancel font-16'></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- تعديل بيانات المتدرب --}}
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary rounded-circle shadow-sm btn-edit-trainee"
                                                    data-id="{{ $trainee->id }}" data-name="{{ $trainee->full_name }}"
                                                    data-phone="{{ $trainee->phone }}"
                                                    data-dept="{{ $trainee->hr_department_id }}"
                                                    data-uni="{{ $trainee->university_or_school }}"
                                                    data-start="{{ \Carbon\Carbon::parse($trainee->start_date)->format('Y-m-d') }}"
                                                    data-end="{{ \Carbon\Carbon::parse($trainee->end_date)->format('Y-m-d') }}"
                                                    data-bs-toggle="modal" data-bs-target="#editTraineeModal"
                                                    title="تعديل البيانات">
                                                    <i class='mdi mdi-pencil-outline font-16'></i>
                                                </button>

                                                {{-- حذف المتدرب --}}
                                                <form action="{{ route('admin.hr.trainees.destroy', $trainee->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger rounded-circle shadow-sm btn-delete"
                                                        title="حذف السجل">
                                                        <i class='mdi mdi-trash-can-outline font-16'></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('manage_trainees') ? 6 : 5 }}"
                                        class="p-5 text-muted text-center fw-bold">
                                        <i class='mdi mdi-school-outline fs-1 d-block mb-2 opacity-25'></i>
                                        لا يوجد متدربين مسجلين حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 إضافة الترقيم (Pagination) 🔥 --}}
            @if (isset($trainees) && $trainees->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $trainees->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    @can('manage_trainees')
        {{-- 🔥 مودال تسجيل متدرب محمي من الـ Double Submit 🔥 --}}
        <div class="modal fade" id="addTraineeModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-account-plus-outline me-1'></i> تسجيل
                            بيانات متدرب</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.trainees.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 bg-light text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم المتدرب بالكامل <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control fw-bold shadow-sm border-0"
                                    required placeholder="مثال: أحمد محمد عبدلله">
                            </div>
                            <div class="row g-3">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="text" name="phone"
                                        class="form-control font-monospace shadow-sm border-0" dir="ltr" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">الجهة التعليمية</label>
                                    <input type="text" name="university_or_school" class="form-control shadow-sm border-0"
                                        placeholder="مثال: جامعة السلطان قابوس">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">القسم الموجه إليه للتدريب <span
                                        class="text-danger">*</span></label>
                                <select name="hr_department_id" class="form-select border-primary shadow-sm" required>
                                    <option value="" disabled selected>-- اختر القسم --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-3">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-success">بداية التدريب <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="start_date"
                                        class="form-control text-center fw-bold shadow-sm border-0"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-danger">نهاية التدريب <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="end_date"
                                        class="form-control text-center fw-bold shadow-sm border-0" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-white rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm rounded-pill btn-submit"><i
                                    class="mdi mdi-content-save me-1"></i> حفظ وتأكيد التدريب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔥 مودال تعديل بيانات متدرب محمي من الـ Double Submit 🔥 --}}
        <div class="modal fade" id="editTraineeModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-pencil-outline me-1'></i> تعديل بيانات
                            المتدرب</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editTraineeForm" class="prevent-double-submit">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4 bg-light text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم المتدرب بالكامل <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="edit_full_name"
                                    class="form-control fw-bold shadow-sm border-0" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="edit_phone"
                                        class="form-control font-monospace shadow-sm border-0" dir="ltr" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">الجهة التعليمية</label>
                                    <input type="text" name="university_or_school" id="edit_uni"
                                        class="form-control shadow-sm border-0">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">القسم الموجه إليه للتدريب <span
                                        class="text-danger">*</span></label>
                                <select name="hr_department_id" id="edit_dept" class="form-select border-primary shadow-sm"
                                    required>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row g-3">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-success">بداية التدريب <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="edit_start"
                                        class="form-control text-center fw-bold shadow-sm border-0" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold text-danger">نهاية التدريب <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="edit_end"
                                        class="form-control text-center fw-bold shadow-sm border-0" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-white rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm rounded-pill btn-submit"><i
                                    class="mdi mdi-check-all me-1"></i> حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {

            // حماية الفورمات من الـ Double Submit
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري الحفظ...');
            });

            // تمرير البيانات لمودال التعديل
            $('.btn-edit-trainee').click(function() {
                let id = $(this).data('id');
                $('#edit_full_name').val($(this).data('name'));
                $('#edit_phone').val($(this).data('phone'));
                $('#edit_dept').val($(this).data('dept'));
                $('#edit_uni').val($(this).data('uni'));
                $('#edit_start').val($(this).data('start'));
                $('#edit_end').val($(this).data('end'));

                let url = "{{ route('admin.hr.trainees.update', ':id') }}".replace(':id', id);
                $('#editTraineeForm').attr('action', url);
            });

            // تأكيد تغيير حالة التدريب
            $('.btn-change-status').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                let action = $(this).data('action');
                let title = action === 'completed' ? 'تأكيد إكمال التدريب؟' : 'تأكيد إلغاء التدريب؟';
                let text = action === 'completed' ?
                    'سيتم تحويل حالة المتدرب إلى "مكتمل" كإثبات على إنهاء فترة التدريب بنجاح.' :
                    'سيتم تحويل حالة المتدرب إلى "ملغي".';
                let confirmColor = action === 'completed' ? '#198754' : '#ffc107';

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، تأكيد',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // تأكيد الحذف
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'هل أنت متأكد من الحذف؟',
                    text: 'سيتم مسح سجل المتدرب بشكل نهائي من النظام.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احذف السجل',
                    cancelButtonText: 'تراجع'
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
                    title: 'عملية ناجحة',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
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
                    title: 'خطأ في إدخال البيانات',
                    html: errorHtml,
                    confirmButtonColor: '#1e2b3c'
                });
            @endif
        });
    </script>
@endpush
