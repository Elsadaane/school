@extends('backend.layouts.master')
@section('title', 'إدارة المواد الدراسية')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
            border-top: 5px solid #3b82f6;
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
            padding: 14px 10px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* الأزرار الموحدة */
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: white !important;
            border: none;
            transition: 0.2s;
            margin: 0 2px;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-edit {
            background-color: #f59e0b;
        }

        .btn-delete {
            background-color: #ef4444;
        }

        .form-control {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #3b82f6;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
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

        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 Page Header 🌟 --}}
        <div class="row mb-4 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold mb-1 text-dark"><i
                            class="mdi mdi-book-open-page-variant text-primary me-1 fs-3 align-middle"></i> إدارة المواد
                        الدراسية</h4>
                    <p class="text-muted mb-0 font-14">إدارة القاموس الأكاديمي للمواد وربطها بنظام التقييمات والمعلمين.</p>
                </div>

                {{-- 🔥 حماية الزر بصلاحية الإنشاء 🔥 --}}
                @can('create_subjects')
                    <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4 py-2 fw-bold"
                        data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                        <i class="mdi mdi-plus-circle me-1 align-middle fs-5"></i> إضافة مادة جديدة
                    </button>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success shadow-sm fw-bold border-0 rounded-3"><i
                    class="mdi mdi-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 rounded-3 fw-bold">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🌟 Data Table 🌟 --}}
        <div class="card card-premium shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-erp table-hover mb-0 align-middle text-center">
                        <thead class="sticky-top" style="z-index: 5;">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th class="text-start ps-4">اسم المادة الأكاديمية</th>
                                <th>كود المادة الأكاديمي</th>
                                <th>الكادر (عدد المعلمين)</th>
                                <th>الحالة</th>

                                {{-- 🔥 إخفاء عمود الإجراءات لو مفيش صلاحية 🔥 --}}
                                @canany(['edit_subjects', 'delete_subjects'])
                                    <th style="width: 120px;">الإجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjects as $index => $subject)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                    <td class="text-start ps-4">
                                        <h6 class="m-0 font-15 fw-bold text-dark">{{ $subject->name_ar }}</h6>
                                        @if ($subject->name_en)
                                            <small class="text-muted font-monospace d-block mt-1"
                                                dir="ltr">{{ $subject->name_en }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 font-13 font-monospace shadow-sm rounded-pill">{{ $subject->code ?? '---' }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1 font-13 shadow-sm rounded-pill">
                                            <i class="mdi mdi-account-group me-1"></i> {{ $subject->teachers_count ?? 0 }}
                                            معلم
                                        </span>
                                    </td>
                                    <td>
                                        @if ($subject->is_active)
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 font-12 shadow-sm rounded-pill"><i
                                                    class="mdi mdi-check-circle me-1"></i> مفعلة</span>
                                        @else
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 font-12 shadow-sm rounded-pill"><i
                                                    class="mdi mdi-close-circle me-1"></i> معطلة</span>
                                        @endif
                                    </td>

                                    @canany(['edit_subjects', 'delete_subjects'])
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                @can('edit_subjects')
                                                    <button type="button" class="action-btn btn-edit shadow-sm"
                                                        data-bs-toggle="modal" data-bs-target="#editSubjectModal{{ $subject->id }}"
                                                        title="تعديل بيانات المادة">
                                                        <i class="mdi mdi-square-edit-outline font-18"></i>
                                                    </button>
                                                @endcan

                                                @can('delete_subjects')
                                                    <form action="{{ route('admin.subjects.destroy', $subject->id) }}"
                                                        method="POST" class="d-inline delete-form">
                                                        @csrf @method('DELETE')
                                                        <button type="button"
                                                            class="action-btn btn-delete shadow-sm btn-delete-subject"
                                                            title="حذف المادة من النظام">
                                                            <i class="mdi mdi-trash-can-outline font-18"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>

                                {{-- 🔥 مودال التعديل لكل مادة 🔥 --}}
                                @can('edit_subjects')
                                    <div class="modal fade" id="editSubjectModal{{ $subject->id }}" tabindex="-1"
                                        aria-hidden="true" data-bs-backdrop="static">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div
                                                class="modal-content border-0 shadow-lg rounded-4 border-top border-warning border-4">
                                                <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                                                    <h5 class="modal-title font-bold text-warning"><i
                                                            class="mdi mdi-square-edit-outline me-1 fs-4 align-middle"></i>
                                                        تعديل المادة: <span class="text-dark">{{ $subject->name_ar }}</span>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.subjects.update', $subject->id) }}"
                                                    method="POST" class="prevent-double-submit">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body p-4 text-start bg-white">
                                                        <div class="mb-3">
                                                            <label class="form-label text-muted font-13 fw-bold">اسم المادة
                                                                الأكاديمية (عربي) <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control fw-bold shadow-sm"
                                                                name="name_ar" value="{{ $subject->name_ar }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label text-muted font-13 fw-bold">اسم المادة
                                                                (إنجليزي)</label>
                                                            <input type="text" class="form-control shadow-sm" name="name_en"
                                                                value="{{ $subject->name_en }}" dir="ltr"
                                                                placeholder="e.g. Mathematics">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label text-muted font-13 fw-bold">كود المادة
                                                                الأكاديمي (اختياري)</label>
                                                            <input type="text" class="form-control shadow-sm font-monospace"
                                                                name="code" value="{{ $subject->code }}" dir="ltr"
                                                                placeholder="MATH-01">
                                                        </div>
                                                        <div
                                                            class="form-check form-switch mt-4 d-flex align-items-center gap-2">
                                                            <input class="form-check-input shadow-sm m-0" type="checkbox"
                                                                name="is_active" id="active{{ $subject->id }}"
                                                                {{ $subject->is_active ? 'checked' : '' }}
                                                                style="width: 2.5em; height: 1.25em;">
                                                            <label
                                                                class="form-check-label fw-bold text-dark mt-1 cursor-pointer"
                                                                for="active{{ $subject->id }}">تفعيل ظهور المادة
                                                                للمعلمين</label>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="modal-footer bg-light border-top-0 rounded-bottom-4 justify-content-between px-4">
                                                        <button type="button"
                                                            class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm"
                                                            data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit"
                                                            class="btn btn-warning rounded-pill px-5 font-bold shadow-sm text-dark btn-submit"><i
                                                                class="mdi mdi-check-decagram-outline me-1 fs-5 align-middle"></i>
                                                            حفظ التعديلات</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endcan

                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-muted bg-white text-center">
                                        <i
                                            class="mdi mdi-book-remove-multiple-outline fs-1 d-block mb-2 text-danger opacity-50"></i>
                                        <h5 class="fw-bold">لا توجد مواد دراسية مسجلة.</h5>
                                        <p class="font-14 mb-0">يرجى إضافة المواد الأكاديمية لبدء ربطها بخطط المعلمين
                                            وجداول الحصص.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔥 مودال الإضافة 🔥 --}}
    @can('create_subjects')
        <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 border-top border-primary border-4">
                    <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                        <h5 class="modal-title font-bold text-primary"><i
                                class="mdi mdi-plus-circle-outline me-1 fs-4 align-middle"></i> إدراج مادة أكاديمية جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.subjects.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start bg-white">
                            <div class="mb-3">
                                <label class="form-label text-muted font-13 fw-bold">اسم المادة الأكاديمية (عربي) <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control fw-bold shadow-sm" name="name_ar"
                                    value="{{ old('name_ar') }}" placeholder="مثال: الرياضيات" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted font-13 fw-bold">اسم المادة (إنجليزي)</label>
                                <input type="text" class="form-control shadow-sm" name="name_en"
                                    value="{{ old('name_en') }}" dir="ltr" placeholder="e.g. Mathematics">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted font-13 fw-bold">كود المادة الأكاديمي (اختياري)</label>
                                <input type="text" class="form-control shadow-sm font-monospace" name="code"
                                    value="{{ old('code') }}" dir="ltr" placeholder="MATH-101">
                            </div>
                            <div class="form-check form-switch mt-4 d-flex align-items-center gap-2">
                                <input class="form-check-input shadow-sm m-0" type="checkbox" name="is_active"
                                    id="activeNew" checked style="width: 2.5em; height: 1.25em;">
                                <label class="form-check-label fw-bold text-dark mt-1 cursor-pointer" for="activeNew">تفعيل
                                    ظهور المادة فوراً للمعلمين</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0 rounded-bottom-4 justify-content-between px-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 font-bold shadow-sm btn-submit"><i
                                    class="mdi mdi-check-decagram-outline me-1 fs-5 align-middle"></i> حفظ وإضافة
                                المادة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // تأمين الفورم ضد الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-2 fs-5 align-middle"></i> جاري التنفيذ...');
            });

            // تأكيد الحذف
            $('.btn-delete-subject').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد الحذف؟',
                    text: "هل أنت متأكد من حذف هذه المادة بشكل نهائي؟ سيتم فك ارتباطها من جميع المعلمين وجداول الحصص المدرسية.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف المادة',
                    cancelButtonText: 'تراجع وإلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
