@extends('backend.layouts.master')
@section('title', 'إدارة موافقات الإجازات')
@section('title_page', 'الموارد البشرية - طلبات الإجازات الواردة')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .page-wrapper {
            background-color: #f4f6f9;
            padding: 25px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 25px;
        }

        .card-header-custom {
            background-color: #fff;
            border-bottom: 2px solid #f1f4f8;
            padding: 20px 25px;
        }

        .table-custom {
            white-space: nowrap;
            /* لمنع تكسير النصوص في الجداول */
        }

        .table-custom thead th {
            background-color: #1e2b3c;
            color: #fff;
            font-weight: 700;
            padding: 15px;
            border: none;
            text-align: center;
        }

        .table-custom tbody td {
            vertical-align: middle;
            padding: 15px;
            border-bottom: 1px dashed #eee;
            font-weight: 600;
            color: #444;
            text-align: center;
        }

        .table-custom tbody tr:hover td {
            background-color: #fdfbf9;
        }

        .avatar-sm {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eee;
            margin-inline-end: 10px;
            /* لدعم اللغتين */
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }

        /* تنسيق الأيقونات الإحصائية */
        .icon-circle {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🔥 كروت الإحصائيات الذكية 🔥 --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-4 col-md-6">
                <div class="card card-custom border-start border-warning border-5 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h6 class="text-muted fw-bold mb-1">طلبات قيد الانتظار</h6>
                            <h2 class="mb-0 fw-bolder text-warning" style="color: #d97706 !important;">
                                {{ $requests->where('status', 'pending')->count() }}
                            </h2>
                        </div>
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning" style="color: #d97706 !important;">
                            <i class='mdi mdi-clock-outline fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="card card-custom border-start border-success border-5 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h6 class="text-muted fw-bold mb-1">تمت الموافقة (كلياً)</h6>
                            <h2 class="mb-0 fw-bolder text-success">
                                {{ $requests->where('status', 'approved')->count() }}
                            </h2>
                        </div>
                        <div class="icon-circle bg-success bg-opacity-10 text-success">
                            <i class='mdi mdi-check-decagram-outline fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-12">
                <div class="card card-custom border-start border-primary border-5 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h6 class="text-muted fw-bold mb-1">إجمالي الطلبات المُقدمة</h6>
                            <h2 class="mb-0 fw-bolder text-primary">{{ $requests->total() ?? $requests->count() }}</h2>
                        </div>
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                            <i class='mdi mdi-format-list-bulleted fs-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔥 جدول طلبات الإجازة 🔥 --}}
        <div class="card card-custom">
            <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold text-dark"><i
                        class='mdi mdi-email-receive-outline text-primary me-2 fs-4 align-middle'></i> سجل طلبات الإجازات
                    الواردة</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="text-start ps-4">الموظف</th>
                                <th>نوع الإجازة</th>
                                <th>المدة</th>
                                <th>تاريخ البدء والانتهاء</th>
                                <th>رصيد الموظف</th>
                                <th>المرفق</th>
                                <th>الحالة</th>
                                {{-- عمود القرار يظهر للي عنده صلاحية فقط --}}
                                @can('update_leave_status')
                                    <th>الإجراء والقرار</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                                <tr>
                                    <td class="text-start fw-bold text-dark ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $req->employee->profile_image ? asset($req->employee->profile_image) : asset('ltr/assets/images/avatars/avatar-1.png') }}"
                                                class="avatar-sm" alt="Avatar">
                                            <div>
                                                {{ app()->getLocale() == 'ar' ? $req->employee->full_name_ar : $req->employee->full_name_en ?? $req->employee->full_name_ar }}
                                                <div class="small text-muted mt-1 font-monospace">
                                                    {{ $req->employee->employee_code ?? '---' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border px-3 py-2 shadow-sm">
                                            {{ app()->getLocale() == 'ar' ? $req->leaveType->name_ar ?? 'إجازة' : $req->leaveType->name_en ?? ($req->leaveType->name_ar ?? 'Leave') }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="text-primary fw-bolder fs-5">{{ $req->total_days }}</span> <small
                                            class="text-muted">يوم</small>
                                    </td>

                                    <td class="text-muted font-monospace">
                                        <div class="fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($req->start_date)->format('Y-m-d') }}</div>
                                        <i class='mdi mdi-arrow-down text-primary'></i>
                                        <div class="fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($req->end_date)->format('Y-m-d') }}</div>
                                    </td>

                                    <td>
                                        @php
                                            $balance = $req->employee->leaveBalances
                                                ->where('leave_type_id', $req->leave_type_id)
                                                ->where('year', \Carbon\Carbon::parse($req->start_date)->year)
                                                ->first();
                                            $remaining = $balance->remaining_days ?? 0;
                                            $isInsufficient = $remaining < $req->total_days;
                                        @endphp
                                        <span
                                            class="badge {{ $isInsufficient ? 'bg-danger' : 'bg-info' }} bg-opacity-10 text-dark border px-3 py-2 shadow-sm"
                                            title="الرصيد المتبقي للموظف من هذا النوع من الإجازات">
                                            المتبقي: <span
                                                class="fw-bolder fs-6 {{ $isInsufficient ? 'text-danger' : '' }} font-monospace">{{ $remaining }}</span>
                                            يوم
                                        </span>
                                        @if ($isInsufficient && $req->status == 'pending')
                                            <div class="small text-danger fw-bold mt-1"><i
                                                    class="mdi mdi-alert-circle-outline"></i> الرصيد لا يكفي</div>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($req->attachment)
                                            {{-- 🔥 حماية المرفقات بـ rel="noopener noreferrer" 🔥 --}}
                                            <a href="{{ asset($req->attachment) }}" target="_blank"
                                                rel="noopener noreferrer"
                                                class="btn btn-sm btn-outline-dark rounded-3 shadow-sm px-3"
                                                title="عرض المرفق (عذر طبي، الخ)">
                                                <i class="mdi mdi-paperclip me-1"></i> عرض
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($req->status == 'pending')
                                            <span class="status-badge bg-warning text-dark"><i
                                                    class='mdi mdi-clock-outline me-1'></i> قيد الانتظار</span>
                                        @elseif($req->status == 'approved')
                                            <span class="status-badge bg-success text-white"><i
                                                    class='mdi mdi-check-all me-1'></i> معتمدة</span>
                                        @else
                                            <span class="status-badge bg-danger text-white"><i
                                                    class='mdi mdi-close-circle-outline me-1'></i> مرفوضة</span>
                                        @endif
                                    </td>

                                    {{-- 🔥 إجراءات اتخاذ القرار محمية 🔥 --}}
                                    @can('update_leave_status')
                                        <td>
                                            @if ($req->status == 'pending')
                                                <div class="d-flex justify-content-center gap-2">
                                                    {{-- زر الموافقة --}}
                                                    <form action="{{ route('admin.hr.leaves.status', $req->id) }}"
                                                        method="POST" class="status-form">
                                                        @csrf
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="button"
                                                            class="btn btn-sm btn-success fw-bold rounded-3 shadow-sm btn-approve"
                                                            data-days="{{ $req->total_days }}">
                                                            <i class='mdi mdi-check'></i> موافقة
                                                        </button>
                                                    </form>

                                                    {{-- زر الرفض --}}
                                                    <form action="{{ route('admin.hr.leaves.status', $req->id) }}"
                                                        method="POST" class="status-form">
                                                        @csrf
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger fw-bold rounded-3 shadow-sm btn-reject">
                                                            <i class='mdi mdi-close'></i> رفض
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                {{-- زر التراجع (Revert) لو الطلب اتاخد فيه قرار بالغلط --}}
                                                <form action="{{ route('admin.hr.leaves.status', $req->id) }}" method="POST"
                                                    class="status-form">
                                                    @csrf
                                                    <input type="hidden" name="status" value="pending">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary fw-bold rounded-3 btn-revert">
                                                        <i class='mdi mdi-undo-variant me-1'></i> تراجع عن القرار
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('update_leave_status') ? 8 : 7 }}"
                                        class="p-5 text-muted text-center fw-bold">
                                        <i class='mdi mdi-email-open-outline fs-1 opacity-50 d-block mb-2 text-primary'></i>
                                        لا توجد طلبات إجازة مُقدمة أو مسجلة حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 ترقيم الصفحات (Pagination) 🔥 --}}
            @if (isset($requests) && $requests->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-3">
                    {{ $requests->appends(request()->query())->links() }}
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

            // تأكيد الموافقة مع حماية التكرار
            $('.btn-approve').click(function() {
                let btn = $(this);
                let form = btn.closest('form');
                let days = btn.data('days');

                Swal.fire({
                    title: 'تأكيد الموافقة',
                    text: `هل أنت متأكد من الموافقة؟ سيتم خصم (${days}) أيام من رصيد الموظف أوتوماتيكياً.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، اعتمد الإجازة',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            "<i class='mdi mdi-loading mdi-spin'></i> جاري الاعتماد...");
                        form.submit();
                    }
                });
            });

            // تأكيد الرفض مع حماية التكرار
            $('.btn-reject').click(function() {
                let btn = $(this);
                let form = btn.closest('form');

                Swal.fire({
                    title: 'تأكيد الرفض',
                    text: "هل أنت متأكد من رفض طلب الإجازة المقدم؟",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، أرفض الطلب',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            "<i class='mdi mdi-loading mdi-spin'></i> جاري الرفض...");
                        form.submit();
                    }
                });
            });

            // تأكيد التراجع مع حماية التكرار
            $('.btn-revert').click(function() {
                let btn = $(this);
                let form = btn.closest('form');

                Swal.fire({
                    title: 'تراجع عن القرار',
                    text: "سيتم إعادة الطلب لحالة (قيد الانتظار) وسيتم إرجاع الرصيد للموظف إذا كان قد خُصم.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#0dcaf0',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'تأكيد التراجع',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            "<i class='mdi mdi-loading mdi-spin'></i> جاري التراجع...");
                        form.submit();
                    }
                });
            });

            // إشعارات SweetAlert2 للنجاح والخطأ
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
                    title: 'خطأ',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#1e2b3c'
                });
            @endif
        });
    </script>
@endpush
