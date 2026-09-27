@extends('backend.layouts.master')
@section('title', 'طلبات استئذان الطلاب')

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

        .table-custom tbody tr:hover td {
            background-color: #f8fafc;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold mb-1 text-dark">
                        <i class="mdi mdi-envelope-open-text text-primary me-1 fs-3 align-middle"></i> طلبات استئذان الطلاب
                    </h4>
                    <p class="text-muted mb-0 font-13">مراجعة وتوثيق الأعذار الطبية والغياب المبرر للطلاب.</p>
                </div>

                {{-- 🔥 تعديل روت الفلتر هنا 🔥 --}}
                <form action="{{ route('admin.leave_requests.index') }}" method="GET" class="d-flex gap-2">
                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                        <span class="input-group-text bg-white border-0 text-muted"><i
                                class="mdi mdi-filter-variant"></i></span>
                        <select name="status" class="form-select border-0 fw-bold bg-white" onchange="this.form.submit()"
                            style="min-width: 180px;">
                            <option value="">جميع الطلبات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة
                            </option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبول ومُسجل
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-3 fw-bold border-0">
                <i class="mdi mdi-alert-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        {{-- جدول الطلبات --}}
        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0 text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-start ps-4">الطالب</th>
                                <th>ولي الأمر (مقدم الطلب)</th>
                                <th>تاريخ الاستئذان</th>
                                <th>السبب والمرفقات</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leaves as $index => $leave)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $leaves->firstItem() + $index }}</td>
                                    <td class="text-start ps-4">
                                        <h6 class="mb-1 fw-bolder text-primary">
                                            {{ $leave->student->first_name }} {{ $leave->student->tribe }}
                                        </h6>
                                        <small class="text-muted fw-bold">
                                            {{ $leave->student->grade->name_ar ?? 'بدون صف' }} -
                                            {{ $leave->student->section->name_ar ?? 'بدون شعبة' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span
                                            class="d-block fw-bold text-dark">{{ $leave->guardian->name_ar ?? 'غير مسجل' }}</span>
                                    </td>
                                    <td class="font-monospace fw-bold text-dark" dir="ltr">
                                        {{ \Carbon\Carbon::parse($leave->from_date)->format('Y-m-d') }} <br>
                                        <i class="mdi mdi-arrow-down text-primary"></i> <br>
                                        {{ \Carbon\Carbon::parse($leave->to_date)->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        <button
                                            class="btn btn-sm btn-light border rounded-pill shadow-sm text-dark font-12 fw-bold px-3"
                                            data-bs-toggle="modal" data-bs-target="#reasonModal{{ $leave->id }}">
                                            <i class="mdi mdi-text-box-search-outline me-1"></i> عرض التفاصيل
                                        </button>
                                    </td>
                                    <td>
                                        @if ($leave->status == 'pending')
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill shadow-sm"><i
                                                    class="mdi mdi-clock-outline me-1"></i> قيد المراجعة</span>
                                        @elseif($leave->status == 'approved')
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill shadow-sm"><i
                                                    class="mdi mdi-check-all me-1"></i> مقبول</span>
                                        @else
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill shadow-sm"><i
                                                    class="mdi mdi-close-circle-outline me-1"></i> مرفوض</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($leave->status == 'pending')
                                            <div class="d-flex justify-content-center gap-2">

                                                {{-- 🔥 تعديل فورم القبول 🔥 --}}
                                                <form action="{{ route('admin.leave_requests.approve', $leave->id) }}"
                                                    method="POST" class="status-form">
                                                    @csrf
                                                    <button type="button"
                                                        class="btn btn-sm btn-success rounded-circle shadow-sm btn-approve"
                                                        title="قبول العذر واعتماد الغياب">
                                                        <i class="mdi mdi-check-bold font-16"></i>
                                                    </button>
                                                </form>

                                                {{-- 🔥 تعديل فورم الرفض 🔥 --}}
                                                <form action="{{ route('admin.leave_requests.reject', $leave->id) }}"
                                                    method="POST" class="status-form">
                                                    @csrf
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger rounded-circle shadow-sm btn-reject"
                                                        title="رفض العذر">
                                                        <i class="mdi mdi-close-thick font-16"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted small fw-bold"><i class="mdi mdi-lock-outline"></i> تمت
                                                المعالجة</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- مودال عرض التفاصيل لكل طلب --}}
                                <div class="modal fade" id="reasonModal{{ $leave->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div
                                            class="modal-content border-0 rounded-4 shadow-lg border-top border-primary border-4">
                                            <div class="modal-header bg-light rounded-top-4 border-bottom-0 pb-3">
                                                <h5 class="modal-title fw-bold text-primary">
                                                    <i class="mdi mdi-information-outline me-1"></i> تفاصيل طلب الاستئذان
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start">
                                                <h6 class="fw-bold text-muted mb-2"><i
                                                        class="mdi mdi-text-box-edit-outline me-1"></i> سبب الغياب المقدم:
                                                </h6>
                                                <div class="bg-light p-3 rounded-3 border mb-4 shadow-sm">
                                                    <p class="mb-0 text-dark lh-lg fw-bold">
                                                        {{ $leave->reason ?? 'لم يتم كتابة تفاصيل.' }}</p>
                                                </div>

                                                <h6 class="fw-bold text-muted mb-2"><i class="mdi mdi-paperclip me-1"></i>
                                                    המرفقات الطبية/الرسمية:</h6>
                                                @if ($leave->attachment)
                                                    <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="btn btn-outline-primary fw-bold w-100 rounded-pill shadow-sm py-2">
                                                        <i class="mdi mdi-download me-1"></i> استعراض / تحميل المرفق
                                                    </a>
                                                @else
                                                    <div class="alert alert-secondary text-center mb-0 border-0 fw-bold">
                                                        <i class="mdi mdi-file-hidden opacity-50 fs-5 d-block mb-1"></i>
                                                        لا توجد مرفقات مع هذا الطلب.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer bg-light rounded-bottom-4 border-top-0 pt-2">
                                                <button type="button"
                                                    class="btn btn-secondary px-4 fw-bold rounded-pill shadow-sm"
                                                    data-bs-dismiss="modal">إغلاق</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted fw-bold">
                                        <i class="mdi mdi-inbox-outline opacity-25 d-block mb-2"
                                            style="font-size: 50px;"></i>
                                        لا توجد طلبات استئذان مسجلة تطابق بحثك حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($leaves->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $leaves->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {

            // سكريبت الاعتماد (القبول) باستخدام SweetAlert2
            $('.btn-approve').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                let btn = $(this);

                Swal.fire({
                    title: 'تأكيد قبول العذر؟',
                    text: 'سيتم تحويل حالة الطلب إلى "مقبول"، وسيتم تسجيل الطالب كمستأذن (غائب بعذر) في سجلات الحضور والغياب لتلك الفترة.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، اقبل العذر',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>');
                        form.submit();
                    }
                });
            });

            // سكريبت الرفض باستخدام SweetAlert2
            $('.btn-reject').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                let btn = $(this);

                Swal.fire({
                    title: 'تأكيد رفض العذر؟',
                    text: 'سيتم رفض الطلب وإبلاغ ولي الأمر بذلك عبر المنصة.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، ارفض العذر',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>');
                        form.submit();
                    }
                });
            });

            // إشعارات النظام (SweetAlert2) للردود الناجحة
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تمت العملية بنجاح',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
