@extends('backend.layouts.master')
@section('title', 'أنواع الأصول الفرعية')
@section('title_page', 'أنواع الأصول الفرعية')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    {{-- تضمين ستايل SweetAlert --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="row">
        <div class="col-12 mb-3 text-end">
            <h6 class="text-muted fw-bold d-inline-block">أنواع الأصول الفرعية</h6>
        </div>

        {{-- 🔥 عرض رسائل النجاح والخطأ الشاملة 🔥 --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold">قائمة الأصول الفرعية</h5>
                @can('create_assets')
                    <button type="button" class="btn btn-purple text-white px-4 shadow-sm" style="background-color: #9c27b0;"
                        data-bs-toggle="modal" data-bs-target="#addSubCategoryModal">
                        <i class="bx bx-plus"></i> إضافة نوع فرعي
                    </button>
                @endcan
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>القسم الفرعي</th>
                                <th>القسم الرئيسي التابع له</th>
                                <th>تاريخ الانشاء</th>
                                @canany(['edit_assets', 'delete_assets'])
                                    <th>الإجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subCategories as $index => $subCategory)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $subCategory->name_ar }}</td>
                                    <td class="text-primary fw-bold">
                                        <span class="badge bg-light text-primary border border-primary px-3 py-2">
                                            {{ $subCategory->category->name_ar ?? 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td>{{ $subCategory->created_at->format('Y-m-d') }}</td>

                                    @canany(['edit_assets', 'delete_assets'])
                                        <td>
                                            @can('edit_assets')
                                                <button class="btn btn-sm btn-info text-white shadow-sm mx-1" data-bs-toggle="modal"
                                                    data-bs-target="#editSubCategoryModal{{ $subCategory->id }}" title="تعديل">
                                                    <i class="bx bx-edit fs-5"></i>
                                                </button>
                                            @endcan

                                            @can('delete_assets')
                                                <form action="{{ route('asset_subcategories.destroy', $subCategory->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger shadow-sm mx-1 btn-delete"
                                                        title="حذف">
                                                        <i class="bx bx-trash fs-5"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bx bx-folder-open fs-1 d-block mb-2 text-secondary"></i>
                                        لا توجد أنواع أصول فرعية مسجلة حتى الآن.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- 🔥 قسم المودالات (خارج الجدول لتجنب تدمير الـ HTML) 🔥 --}}
    {{-- ========================================== --}}

    {{-- 1. مودال الإضافة --}}
    @can('create_assets')
        <div class="modal fade" id="addSubCategoryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">إضافة نوع أصول فرعي</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('asset_subcategories.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold text-muted">القسم الرئيسي <span
                                            class="text-danger">*</span></label>
                                    <select name="asset_category_id" class="form-select select2-add" required
                                        style="width: 100%;">
                                        <option value="" selected disabled>ابحث واختر القسم...</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-danger fw-bold">اسم القسم عربي *</label>
                                    <input type="text" name="name_ar" class="form-control"
                                        placeholder="مثال: أجهزة كمبيوتر محمول" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-danger fw-bold">اسم القسم انجليزي *</label>
                                    <input type="text" name="name_en" class="form-control" dir="ltr"
                                        placeholder="Example: Laptops" required>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-0 d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary px-4"
                                style="background-color: #9c27b0; border: none;">حفظ وإضافة</button>
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إغلاق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- 2. مودالات التعديل (يتم توليدها خارج الجدول) --}}
    @can('edit_assets')
        @foreach ($subCategories as $subCategory)
            <div class="modal fade" id="editSubCategoryModal{{ $subCategory->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">تعديل نوع الأصل الفرعي</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('asset_subcategories.update', $subCategory->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-4 text-start">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label d-block fw-bold text-muted">القسم الرئيسي <span
                                                class="text-danger">*</span></label>
                                        <select name="asset_category_id" class="form-select select2-edit" required
                                            style="width: 100%;">
                                            <option value="" disabled>اختر القسم</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $subCategory->asset_category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name_ar }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-danger fw-bold">اسم القسم عربي *</label>
                                        <input type="text" name="name_ar" class="form-control"
                                            value="{{ $subCategory->name_ar }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-danger fw-bold">اسم القسم انجليزي *</label>
                                        <input type="text" name="name_en" class="form-control" dir="ltr"
                                            value="{{ $subCategory->name_en }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-0 d-flex justify-content-start">
                                <button type="submit" class="btn btn-success px-4">حفظ التعديلات</button>
                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إغلاق</button>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // تفعيل Select2 للإضافة
            if ($('#addSubCategoryModal').length > 0) {
                $('.select2-add').select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#addSubCategoryModal')
                });
            }

            // تفعيل Select2 للتعديل (لازم نلوب عليهم عشان الـ IDs الديناميكية)
            $('.select2-edit').each(function() {
                let modalId = $(this).closest('.modal').attr('id');
                $(this).select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#' + modalId)
                });
            });

            // سكريبت تأكيد الحذف بـ SweetAlert
            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من استرجاع هذا القسم الفرعي بعد الحذف!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احذف!',
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
