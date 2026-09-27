@extends('backend.layouts.master')
@section('title_page', 'إدارة الفصول الافتراضية')

@push('css')
    <style>
        /* 🌟 لمسات تصميم Premium 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        .table-custom tbody tr {
            transition: all 0.2s ease-in-out;
            border-bottom: 1px dashed #e2e8f0;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .table-custom th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #cbd5e1;
            padding: 15px;
        }

        .table-custom td {
            padding: 15px;
            vertical-align: middle;
        }

        /* تأثير النبض للغرفة النشطة */
        @keyframes pulse-live {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .badge-live {
            animation: pulse-live 2s infinite;
        }

        /* أزرار الإجراءات */
        .btn-action {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: 0.2s;
        }

        .btn-action:hover {
            transform: scale(1.1);
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-premium shadow-sm border-top border-danger border-4">
                <div
                    class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="fw-bold mb-0 text-danger d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-2">
                            <i class="mdi mdi-video-wireless-outline fs-4"></i>
                        </div>
                        جدول الفصول الافتراضية (Live)
                    </h5>

                    @can('manage_virtual_classes')
                        <a href="{{ route('admin.virtual-classes.create') }}"
                            class="btn btn-danger rounded-pill fw-bold shadow px-4">
                            <i class="mdi mdi-video-plus-outline me-1 fs-5 align-middle"></i> جدولة فصل جديد
                        </a>
                    @endcan
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 25%">عنوان الحصة / المادة</th>
                                    <th style="width: 20%">المدرسة / الشعبة</th>
                                    <th style="width: 15%">المعلم</th>
                                    <th style="width: 15%">وقت البدء / المدة</th>
                                    <th style="width: 10%">حالة الغرفة</th>
                                    @canany(['manage_virtual_classes', 'view_virtual_classes'])
                                        <th class="text-center" style="width: 10%">الإجراءات</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($virtualClasses as $index => $class)
                                    <tr>
                                        <td class="fw-bold text-muted">
                                            {{ $loop->iteration + $virtualClasses->firstItem() - 1 }}</td>

                                        <td>
                                            <h6 class="m-0 fw-bold text-dark font-15 mb-1">{{ $class->topic }}</h6>
                                            <span
                                                class="text-primary font-12 fw-bold bg-primary bg-opacity-10 px-2 py-1 rounded">
                                                <i class="mdi mdi-book-open-page-variant"></i>
                                                {{ $class->subject->name_ar ?? '---' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="d-block text-dark fw-bold mb-1"><i
                                                    class="mdi mdi-domain text-success"></i>
                                                {{ $class->section->branch->name_ar ?? '---' }}</span>
                                            <span class="text-muted font-12 fw-bold"><i class="mdi mdi-door"></i>
                                                {{ $class->section->grade->name_ar ?? '---' }} -
                                                {{ $class->section->name_ar ?? '---' }}</span>
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-2">
                                                    <span
                                                        class="avatar-title bg-info bg-opacity-10 text-info rounded-circle fw-bold">
                                                        {{ mb_substr($class->teacher->name_ar ?? 'م', 0, 1) }}
                                                    </span>
                                                </div>
                                                <span
                                                    class="text-dark fw-bold font-13">{{ $class->teacher->name_ar ?? '---' }}</span>
                                            </div>
                                        </td>

                                        <td dir="ltr" class="text-end">
                                            <span
                                                class="badge bg-light text-dark border mb-1 px-2 py-1 font-13 fw-bold shadow-sm">
                                                <i class="mdi mdi-calendar-clock text-warning"></i>
                                                {{ \Carbon\Carbon::parse($class->start_at)->format('Y-m-d / h:i A') }}
                                            </span><br>
                                            <span class="text-muted font-12 fw-bold"><i
                                                    class="mdi mdi-timer-sand text-secondary"></i> {{ $class->duration }}
                                                دقيقة</span>
                                        </td>

                                        <td>
                                            {{-- 🌟 حالة الغرفة مبنية على الداتابيز 🌟 --}}
                                            @switch($class->status)
                                                @case('active')
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill fw-bold badge-live">
                                                        <i class="mdi mdi-record-circle-outline"></i> نشطة الآن
                                                    </span>
                                                @break

                                                @case('scheduled')
                                                    <span
                                                        class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-1 rounded-pill fw-bold">
                                                        <i class="mdi mdi-clock-outline"></i> مجدولة
                                                    </span>
                                                @break

                                                @case('completed')
                                                    <span
                                                        class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-1 rounded-pill fw-bold">
                                                        <i class="mdi mdi-check-all"></i> مكتملة
                                                    </span>
                                                @break

                                                @case('cancelled')
                                                    <span
                                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill fw-bold">
                                                        <i class="mdi mdi-cancel"></i> ملغاة
                                                    </span>
                                                @break
                                            @endswitch

                                            {{-- تنبيه لو الغرفة مخفية عن الطلاب --}}
                                            @if (!$class->is_active)
                                                <span class="d-block mt-1 text-danger font-11 fw-bold"
                                                    data-bs-toggle="tooltip" title="غير مرئية للطلاب في المنصة">
                                                    <i class="mdi mdi-eye-off"></i> مخفية
                                                </span>
                                            @endif
                                        </td>

                                        @canany(['manage_virtual_classes', 'view_virtual_classes'])
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    @can('view_virtual_classes')
                                                        @if ($class->status !== 'cancelled' && $class->status !== 'completed')
                                                            <a href="{{ route('admin.virtual-classes.join', $class->id) }}"
                                                                target="_blank"
                                                                class="btn-action bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25"
                                                                data-bs-toggle="tooltip" title="دخول الغرفة">
                                                                <i class="mdi mdi-video font-18"></i>
                                                            </a>
                                                        @endif
                                                    @endcan

                                                    @can('manage_virtual_classes')
                                                        <a href="{{ route('admin.virtual-classes.edit', $class->id) }}"
                                                            class="btn-action bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25"
                                                            data-bs-toggle="tooltip" title="تعديل الحصة">
                                                            <i class="mdi mdi-pencil font-18"></i>
                                                        </a>

                                                        <form action="{{ route('admin.virtual-classes.destroy', $class->id) }}"
                                                            method="POST" class="d-inline confirm-delete-form">
                                                            @csrf @method('DELETE')
                                                            <button type="button"
                                                                class="btn-action bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25 btn-confirm-delete"
                                                                data-bs-toggle="tooltip" title="حذف نهائي">
                                                                <i class="mdi mdi-trash-can-outline font-18"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <div class="mb-3">
                                                    <i class="mdi mdi-video-off-outline text-muted opacity-25"
                                                        style="font-size: 80px;"></i>
                                                </div>
                                                <h4 class="fw-bold text-dark mb-1">لا توجد فصول افتراضية حالياً</h4>
                                                <p class="text-muted font-14">قم بجدولة حصص البث المباشر للطلاب لتبدأ بالظهور
                                                    هنا.</p>
                                                @can('manage_virtual_classes')
                                                    <a href="{{ route('admin.virtual-classes.create') }}"
                                                        class="btn btn-outline-danger rounded-pill fw-bold mt-2">
                                                        <i class="mdi mdi-plus me-1"></i> إنشاء فصل جديد
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center p-3 border-top">
                            {{ $virtualClasses->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
        <script>
            $(document).ready(function() {
                // تفعيل الـ Tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });

                // SweetAlert للحذف
                $('.btn-confirm-delete').click(function(e) {
                    e.preventDefault();
                    let form = $(this).closest('form');

                    Swal.fire({
                        title: 'تأكيد الحذف؟',
                        text: "هل أنت متأكد من رغبتك في حذف هذا الفصل الافتراضي نهائياً؟",
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
