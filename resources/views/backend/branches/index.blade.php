@extends('backend.layouts.master')
@section('title', 'إدارة الفروع')

@push('css')
    {{-- 🔥 تضمين ستايل SweetAlert 🔥 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        /* تأثيرات حركية خفيفة للبطاقات */
        .hover-effect {
            transition: all 0.3s ease-in-out;
        }

        .hover-effect:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .bg-soft-primary {
            background-color: rgba(91, 105, 188, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="page-title font-bold">المقرات والفروع</h4>

            {{-- 🔥 حماية زرار الإضافة بصلاحية الإدارة 🔥 --}}
            @can('manage_branches')
                <button type="button" class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#addBranchModal">
                    <i class="mdi mdi-plus-circle me-1"></i> إضافة فرع جديد
                </button>
            @endcan
        </div>
    </div>

    {{-- 🔥 عرض رسائل النجاح أو الخطأ بشكل موحد 🔥 --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="mdi mdi-check-circle me-1 text-success"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="mdi mdi-alert-circle me-1 text-danger"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        @forelse($branches as $branch)
            <div class="col-xl-4 col-md-6">
                <div class="card text-center shadow-sm hover-effect border-0">
                    <div class="card-body py-4">

                        {{-- أيقونة الفرع وحالته --}}
                        <div class="mb-3">
                            <div
                                class="avatar-lg mx-auto bg-soft-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-domain text-primary" style="font-size: 32px;"></i>
                            </div>
                        </div>

                        <h4 class="font-bold text-dark mt-2 mb-1">{{ $branch->name_ar }}</h4>
                        <p class="text-muted font-13 mb-3" dir="ltr">{{ $branch->name_en ?? 'لا يوجد اسم إنجليزي' }}
                        </p>

                        <div class="text-start mt-3">
                            <p class="text-muted mb-2 font-13"><strong><i class="mdi mdi-phone text-info me-1"></i>
                                    الهاتف:</strong>
                                <span class="ms-2" dir="ltr">{{ $branch->phone ?? 'غير متوفر' }}</span>
                            </p>
                            <p class="text-muted mb-2 font-13"><strong><i class="mdi mdi-map-marker text-danger me-1"></i>
                                    العنوان:</strong>
                                <span class="ms-2">{{ $branch->address ?? 'غير متوفر' }}</span>
                            </p>
                            <p class="text-muted mb-2 font-13"><strong><i
                                        class="mdi mdi-circle-edit-outline text-success me-1"></i> الحالة:</strong>
                                @if ($branch->is_active)
                                    <span class="badge bg-success ms-2 px-2 py-1">نشط</span>
                                @else
                                    <span class="badge bg-danger ms-2 px-2 py-1">غير نشط</span>
                                @endif
                            </p>
                        </div>

                        {{-- 🔥 حماية زراير الإجراءات بصلاحية الإدارة 🔥 --}}
                        @can('manage_branches')
                            <div class="mt-4 pt-3 border-top">
                                {{-- زر التعديل --}}
                                <button class="btn btn-sm btn-soft-info rounded-pill px-3 me-1 shadow-sm" data-bs-toggle="modal"
                                    data-bs-target="#editBranchModal{{ $branch->id }}">
                                    <i class="mdi mdi-pencil"></i> تعديل
                                </button>

                                {{-- 🔥 زر الحذف بـ SweetAlert 🔥 --}}
                                <button type="button" class="btn btn-sm btn-soft-danger rounded-pill px-3 shadow-sm delete-btn"
                                    data-url="{{ route('admin.branches.destroy', $branch->id) }}">
                                    <i class="mdi mdi-trash-can"></i> حذف
                                </button>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="avatar-xl mx-auto mb-3">
                    <i class="mdi mdi-domain-off text-muted" style="font-size: 60px;"></i>
                </div>
                <h4 class="text-muted font-bold">لا توجد فروع مضافة حتى الآن</h4>
                <p class="text-muted">قم بإضافة الفرع الأول لتبدأ في إعداد النظام.</p>
            </div>
        @endforelse
    </div>

    {{-- فورم الحذف المخفي المشترك --}}
    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- ========================================== --}}
    {{-- 🔥 قسم المودالات (خارج الـ Grid تماماً) 🔥 --}}
    {{-- ========================================== --}}

    @can('manage_branches')

        {{-- مودال إضافة فرع جديد --}}
        <div class="modal fade" id="addBranchModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title font-bold">إضافة فرع جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.branches.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">اسم الفرع بالعربية <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name_ar" required
                                        placeholder="مثال: فرع مسقط">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">اسم الفرع بالإنجليزية</label>
                                    <input type="text" class="form-control" name="name_en" placeholder="e.g. Muscat Branch"
                                        dir="ltr">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control" name="phone" placeholder="أدخل رقم هاتف الفرع"
                                    dir="ltr">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">العنوان التفصيلي</label>
                                <textarea class="form-control" name="address" rows="3" placeholder="أدخل العنوان بالكامل..."></textarea>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label text-primary font-bold" for="is_active">فرع نشط (يعمل
                                    حالياً)</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-secondary rounded-pill px-4"
                                data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm"><i
                                    class="mdi mdi-content-save"></i> حفظ الفرع</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔥 موديلات تعديل الفروع (يتم توليدها برمجياً هنا) 🔥 --}}
        @foreach ($branches as $branch)
            <div class="modal fade" id="editBranchModal{{ $branch->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title font-bold text-info"><i class="mdi mdi-pencil me-1"></i> تعديل بيانات الفرع
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.branches.update', $branch->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4 text-start">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">اسم الفرع بالعربية <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name_ar"
                                            value="{{ $branch->name_ar }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">اسم الفرع بالإنجليزية</label>
                                        <input type="text" class="form-control" name="name_en"
                                            value="{{ $branch->name_en }}" dir="ltr">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $branch->phone }}"
                                        dir="ltr">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">العنوان التفصيلي</label>
                                    <textarea class="form-control" name="address" rows="3">{{ $branch->address }}</textarea>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="isActiveEdit{{ $branch->id }}"
                                        name="is_active" value="1" {{ $branch->is_active ? 'checked' : '' }}>
                                    <label
                                        class="form-check-label font-bold {{ $branch->is_active ? 'text-primary' : 'text-danger' }}"
                                        for="isActiveEdit{{ $branch->id }}">فرع نشط (يعمل حالياً)</label>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-0">
                                <button type="button" class="btn btn-secondary rounded-pill px-4"
                                    data-bs-dismiss="modal">إغلاق</button>
                                <button type="submit" class="btn btn-info text-white rounded-pill px-4 shadow-sm"><i
                                        class="mdi mdi-content-save"></i> حفظ التعديلات</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

    @endcan
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // سكريبت الحذف الموحد بـ SweetAlert
            $(document).on('click', '.delete-btn', function() {
                let deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "تحذير: سيتم حذف هذا الفرع نهائياً. قد يؤثر هذا على الموظفين والأصول المرتبطة به!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#95a5a6',
                    confirmButtonText: 'نعم، احذف الفرع!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-form').attr('action', deleteUrl).submit();
                    }
                });
            });
        });
    </script>
@endpush
