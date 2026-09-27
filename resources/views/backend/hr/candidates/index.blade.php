@extends('backend.layouts.master')
@section('title', 'نظام التوظيف والمقابلات')
@section('title_page', 'الموارد البشرية - تتبع المرشحين للوظائف (ATS)')

@push('css')
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

        .table-custom thead th {
            background-color: #1e2b3c;
            color: white;
            padding: 15px;
            text-align: center;
            white-space: nowrap;
        }

        .table-custom tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 15px;
            border-bottom: 1px dashed #eee;
            font-weight: 600;
        }

        .status-new {
            background-color: #e3f2fd;
            color: #0d6efd;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .status-interviewed {
            background-color: #fff3cd;
            color: #fd7e14;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .status-hired {
            background-color: #d1e7dd;
            color: #198754;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #dc3545;
            padding: 6px 12px;
            border-radius: 6px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 الإحصائيات العلوية 🌟 --}}
        <div class="row g-4 mb-4 text-center">
            <div class="col-md-3">
                <div class="card card-custom border-start border-primary border-4 h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h6 class="text-muted fw-bold">إجمالي المرشحين</h6>
                        <h2 class="fw-bolder text-primary mb-0">{{ $total }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom border-start border-info border-4 h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h6 class="text-muted fw-bold">طلبات جديدة</h6>
                        <h2 class="fw-bolder text-info mb-0">{{ $new }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom border-start border-warning border-4 h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h6 class="text-muted fw-bold">قيد المقابلة</h6>
                        <h2 class="fw-bolder text-warning mb-0">{{ $interviewed }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom border-start border-success border-4 h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h6 class="text-muted fw-bold">تم التعيين</h6>
                        <h2 class="fw-bolder text-success mb-0">{{ $hired }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 رأس الجدول وزر الإضافة 🌟 --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 class="fw-bolder text-dark mb-0">
                <i class='mdi mdi-card-account-details-outline text-primary me-2'></i>سجل المتقدمين للوظائف
            </h4>

            @can('create_candidate')
                <button class="btn btn-primary fw-bold shadow-sm px-4 py-2 rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#addCandidateModal">
                    <i class='mdi mdi-plus me-1'></i> تسجيل مرشح جديد
                </button>
            @endcan
        </div>

        {{-- 🌟 جدول المرشحين 🌟 --}}
        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-nowrap w-100 mb-0">
                        <thead>
                            <tr>
                                <th class="text-start ps-4">اسم المرشح</th>
                                <th>الوظيفة المتقدم لها</th>
                                <th>القسم</th>
                                <th>السيرة الذاتية (CV)</th>
                                <th>الحالة الحالية</th>
                                @can('update_candidate_status')
                                    <th>تحديث الحالة</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($candidates as $candidate)
                                <tr>
                                    <td class="text-start ps-4">
                                        <div class="fw-bolder text-dark">{{ $candidate->full_name }}</div>
                                        <small class="text-muted font-monospace"><i class='mdi mdi-phone text-success'></i>
                                            {{ $candidate->phone }}</small>
                                    </td>
                                    <td class="fw-bold text-primary">{{ $candidate->applied_for_position }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            {{ $candidate->department->name_ar ?? 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($candidate->cv_attachment)
                                            {{-- 🔥 حماية الرابط الخارجي 🔥 --}}
                                            <a href="{{ asset($candidate->cv_attachment) }}" target="_blank"
                                                rel="noopener noreferrer"
                                                class="btn btn-sm btn-outline-dark rounded-3 shadow-sm fw-bold">
                                                <i class='mdi mdi-file-pdf-box text-danger'></i> عرض الـ CV
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($candidate->status == 'new')
                                            <span class="status-new"><i class='mdi mdi-star-outline'></i> جديد</span>
                                        @elseif($candidate->status == 'interviewed')
                                            <span class="status-interviewed"><i
                                                    class='mdi mdi-account-tie-voice-outline'></i> تمت المقابلة</span>
                                        @elseif($candidate->status == 'shortlisted')
                                            <span class="status-interviewed"><i class='mdi mdi-clipboard-list-outline'></i>
                                                بالقائمة القصيرة</span>
                                        @elseif($candidate->status == 'hired')
                                            <span class="status-hired"><i class='mdi mdi-account-check-outline'></i> تم
                                                التعيين</span>
                                        @else
                                            <span class="status-rejected"><i class='mdi mdi-close-circle-outline'></i>
                                                مرفوض</span>
                                        @endif
                                    </td>

                                    @can('update_candidate_status')
                                        <td>
                                            <form action="{{ route('admin.hr.candidates.status', $candidate->id) }}"
                                                method="POST"
                                                class="d-flex gap-2 justify-content-center align-items-center m-0"
                                                id="status-form-{{ $candidate->id }}">
                                                @csrf
                                                <select name="status" id="status-select-{{ $candidate->id }}"
                                                    class="form-select form-select-sm w-auto bg-light border-0 fw-bold shadow-sm">
                                                    <option value="new" {{ $candidate->status == 'new' ? 'selected' : '' }}>
                                                        جديد</option>
                                                    <option value="interviewed"
                                                        {{ $candidate->status == 'interviewed' ? 'selected' : '' }}>تمت
                                                        المقابلة</option>
                                                    <option value="shortlisted"
                                                        {{ $candidate->status == 'shortlisted' ? 'selected' : '' }}>القائمة
                                                        القصيرة</option>
                                                    <option value="hired"
                                                        {{ $candidate->status == 'hired' ? 'selected' : '' }}>تعيين</option>
                                                    <option value="rejected"
                                                        {{ $candidate->status == 'rejected' ? 'selected' : '' }}>رفض</option>
                                                </select>
                                                <button type="button" class="btn btn-sm btn-success shadow-sm rounded-3"
                                                    onclick="processStatus({{ $candidate->id }}, '{{ route('admin.hr.candidates.status', $candidate->id) }}')">
                                                    <i class='mdi mdi-content-save-outline font-16'></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('update_candidate_status') ? 6 : 5 }}"
                                        class="p-5 text-muted text-center fw-bold">
                                        <i class='mdi mdi-account-cancel-outline fs-1 d-block mb-2 opacity-25'></i>
                                        لا يوجد مرشحين مسجلين حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 إضافة الترقيم للحفاظ على استقرار الشاشة 🔥 --}}
            @if ($candidates->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-3">
                    {{ $candidates->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- 1. 🔥 مودال إضافة مرشح جديد (محمي بالصلاحية) 🔥 --}}
    {{-- ================================================= --}}
    @can('create_candidate')
        <div class="modal fade" id="addCandidateModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom-0 bg-light pb-3">
                        <h5 class="modal-title fw-bold text-primary"><i class='mdi mdi-account-plus-outline me-1'></i> تسجيل
                            بيانات مرشح</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    {{-- إضافة prevent-double-submit لمنع التكرار --}}
                    <form action="{{ route('admin.hr.candidates.store') }}" method="POST" enctype="multipart/form-data"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            @if ($errors->any())
                                <div class="alert alert-danger mb-3 fw-bold rounded-3">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label fw-bold">اسم المرشح بالكامل <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control bg-light" required>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control bg-light font-monospace"
                                        dir="ltr" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">البريد الإلكتروني</label>
                                    <input type="email" name="email" class="form-control bg-light font-monospace"
                                        dir="ltr">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">الوظيفة المتقدم لها <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="applied_for_position" class="form-control bg-light fw-bold"
                                        placeholder="مثال: محاسب، كاشير..." required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label fw-bold">القسم الموجه إليه</label>
                                    <select name="department_id" class="form-select bg-light fw-bold">
                                        <option value="" selected>اختر القسم...</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">إرفاق السيرة الذاتية (CV)</label>
                                <input type="file" name="cv_attachment" class="form-control border-dashed p-2 bg-light"
                                    accept=".pdf,.doc,.docx">
                                <small class="text-muted mt-1 d-block"><i class="mdi mdi-information-outline"></i> صيغ مدعومة:
                                    PDF, DOC, DOCX</small>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm rounded-pill btn-submit"><i
                                    class="mdi mdi-content-save me-1"></i> حفظ بيانات المرشح</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- ================================================= --}}
    {{-- 2. 🔥 مودال اعتماد التعيين وتحديد الراتب (محمي) 🔥 --}}
    {{-- ================================================= --}}
    @can('update_candidate_status')
        <div class="modal fade" id="hireModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg border-top border-success border-4">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-success"><i class='mdi mdi-account-check-outline me-1'></i> اعتماد
                            تعيين الموظف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    {{-- إضافة prevent-double-submit لمنع تكرار الموظف --}}
                    <form id="hireForm" method="POST" class="prevent-double-submit">
                        @csrf
                        <input type="hidden" name="status" value="hired">
                        <div class="modal-body p-4 text-start">
                            <div class="alert alert-warning fw-bold font-14 mb-4 rounded-3 border-0 shadow-sm">
                                <i class='mdi mdi-information-outline fs-5 align-middle me-1'></i> سيتم نقل هذا المرشح تلقائياً
                                إلى سجل الموظفين وإنشاء حساب دخول له. يرجى استكمال البيانات التالية:
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">الراتب الأساسي <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                        <input type="number" step="0.001" name="basic_salary"
                                            class="form-control bg-light border-success text-center font-monospace fw-bold fs-5"
                                            placeholder="0.000" required>
                                        <span class="input-group-text bg-white fw-bold text-muted border-success">OMR</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">تاريخ المباشرة للعمل <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="join_date"
                                        class="form-control bg-light border-success text-center fw-bold shadow-sm"
                                        value="{{ now()->toDateString() }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold"
                                data-bs-dismiss="modal">تراجع</button>
                            <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm rounded-pill btn-submit">
                                <i class="mdi mdi-check-decagram me-1"></i> تأكيد التعيين
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // دالة تحويل الحالة
        function processStatus(candidateId, formActionUrl) {
            let select = document.getElementById('status-select-' + candidateId);
            let status = select.value;

            if (status === 'hired') {
                document.getElementById('hireForm').action = formActionUrl;
                let hireModal = new bootstrap.Modal(document.getElementById('hireModal'));
                hireModal.show();
            } else {
                document.getElementById('status-form-' + candidateId).submit();
            }
        }

        $(document).ready(function() {
            // حماية الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري الحفظ...');
            });

            // SweetAlert للمتغيرات القادمة من الـ Controller
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: "تمت العملية",
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: "خطأ",
                    text: "{{ session('error') }}"
                });
            @endif
        });
    </script>
@endpush
