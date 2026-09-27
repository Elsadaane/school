@extends('backend.layouts.master')
@section('title', 'بنود الرسوم الدراسية | Fee Items')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    {{-- 🔥 استدعاء Select2 عشان تعدد الفروع 🔥 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

    <style>
        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            background: #fff;
        }

        .table th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700;
            font-size: 14px;
            border-bottom: 2px solid #e2e8f0;
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-color: #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* تظبيط شكل الـ Select2 عشان يناسب البوتستراب */
        .select2-container--bootstrap-5 .select2-selection--multiple {
            min-height: 42px;
            border-radius: 8px;
            border-color: #e2e8f0;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold text-primary mb-1">
                    <i class="mdi mdi-cash-multiple me-1"></i> بنود الرسوم الدراسية | Fee Items
                </h4>
                <p class="text-muted font-13 mb-0">إدارة وتسعير بنود الرسوم والخدمات لاستخدامها في عقود الطلاب.</p>
            </div>

            @can('create_fee_items')
                <button type="button" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#addFeeItemModal">
                    <i class="mdi mdi-plus-circle me-1"></i> إضافة بند جديد
                </button>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm fw-bold rounded-3 border-0">
            <i class="mdi mdi-check-circle fs-5 align-middle me-1"></i> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3 fw-bold border-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="bx bx-error-circle align-middle me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card card-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0 text-center align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-start ps-3">اسم البند | Item Name</th>
                            <th>الفرع | Branch</th>
                            <th>السعر | Price</th>
                            <th>الكمية | Qty</th>
                            <th>الضريبة | Tax (%)</th>
                            <th>الإجمالي | Total</th>
                            @canany(['edit_fee_items', 'delete_fee_items'])
                                <th>الإجراءات | Actions</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feeItems as $index => $item)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="text-start ps-3">
                                    <h6 class="mb-0 fw-bold text-dark">{{ $item->name_ar }}</h6>
                                    <small class="text-muted" dir="ltr">{{ $item->name_en ?? '---' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">
                                        {{ $item->branch->name_ar ?? 'عام' }}
                                    </span>
                                </td>
                                <td class="text-primary font-monospace fw-bold">{{ number_format($item->price, 3) }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->quantity }}</span></td>
                                <td><span
                                        class="badge bg-danger bg-opacity-10 text-danger">{{ floatval($item->tax_percent) }}%</span>
                                </td>
                                <td class="text-success fw-bolder font-15 font-monospace">
                                    {{ number_format($item->total, 3) }} OMR</td>

                                @canany(['edit_fee_items', 'delete_fee_items'])
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            @can('edit_fee_items')
                                                <button type="button" class="btn btn-sm btn-soft-warning rounded-circle shadow-sm"
                                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}"
                                                    title="تعديل">
                                                    <i class="mdi mdi-square-edit-outline font-18"></i>
                                                </button>
                                            @endcan

                                            @can('delete_fee_items')
                                                <button type="button"
                                                    class="btn btn-sm btn-soft-danger rounded-circle shadow-sm delete-btn"
                                                    data-url="{{ route('admin.fee-items.destroy', $item->id) }}" title="حذف البند">
                                                    <i class="mdi mdi-trash-can-outline font-18"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bx bx-receipt text-muted opacity-25" style="font-size: 60px;"></i>
                                    </div>
                                    <h5 class="text-muted fw-bold">لا توجد بنود رسوم دراسية مسجلة حالياً</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- 🔥 مودال الإضافة 🔥 --}}
    @can('create_fee_items')
        <div class="modal fade" id="addFeeItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title fw-bold text-dark"><i class="bx bx-plus-circle text-primary me-1"></i> إضافة بند
                            رسوم جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.fee-items.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="row g-3">

                                {{-- 🌟 ميزة الفروع المتعددة 🌟 --}}
                                <div class="col-12 border p-3 rounded bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label text-primary mb-0">تحديد الفروع <span
                                                class="text-danger">*</span></label>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input select-all-branches" type="checkbox"
                                                id="selectAllBranches" style="cursor: pointer;">
                                            <label class="form-check-label fw-bold text-dark" for="selectAllBranches"
                                                style="cursor: pointer;">تحديد الكل</label>
                                        </div>
                                    </div>
                                    {{-- غيرنا الاسم لـ branch_id[] وضيفنا multiple --}}
                                    <select name="branch_id[]" class="form-select select2-multi fw-bold" multiple required
                                        style="width: 100%;">
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-1">سيتم إنشاء البند في كل الفروع المحددة.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">الاسم عربي <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control fw-bold" name="name_ar"
                                        placeholder="مثال: رسوم دراسية فصل أول" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">الاسم إنجليزي</label>
                                    <input type="text" class="form-control fw-bold" name="name_en"
                                        placeholder="e.g. Term 1 Tuition Fee" dir="ltr">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">السعر الأساسي <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.001" min="0"
                                        class="form-control font-monospace add-price fw-bold text-primary" name="price"
                                        value="0.000" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">الكمية الافتراضية <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="1" min="1"
                                        class="form-control font-monospace add-qty fw-bold" name="quantity" value="1"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">نسبة الضريبة (%) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" max="100"
                                        class="form-control font-monospace add-tax fw-bold text-danger" name="tax_percent"
                                        value="0.00" required>
                                </div>
                                <div class="col-12 mt-4">
                                    <div
                                        class="alert alert-success d-flex justify-content-between font-bold font-18 mb-0 border-0 shadow-sm">
                                        <span>الإجمالي الشامل المتوقع:</span>
                                        <span><span id="add-total-display">0.000</span> OMR</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0">
                            <button type="button" class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary fw-bold rounded-pill px-5 shadow-sm btn-submit"><i
                                    class="bx bx-save me-1"></i> حفظ البند للفروع</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    {{-- 🔥 مودالات التعديل 🔥 --}}
    @can('edit_fee_items')
        @foreach ($feeItems as $item)
            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 rounded-4 shadow-lg">
                        <div class="modal-header bg-light border-bottom-0">
                            <h5 class="modal-title fw-bold text-dark"><i class="bx bx-edit text-warning me-1"></i> تعديل بند
                                الرسوم</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.fee-items.update', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body p-4 text-start">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label text-muted">الفرع <span class="text-danger">*</span></label>
                                        <select name="branch_id" class="form-select fw-bold" required>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ $item->branch_id == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name_ar }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">الاسم عربي <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control fw-bold" name="name_ar"
                                            value="{{ $item->name_ar }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">الاسم إنجليزي</label>
                                        <input type="text" class="form-control fw-bold" name="name_en"
                                            value="{{ $item->name_en }}" dir="ltr">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">السعر الأساسي <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.001" min="0"
                                            class="form-control font-monospace edit-price fw-bold text-primary" name="price"
                                            value="{{ number_format($item->price, 3, '.', '') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">الكمية <span class="text-danger">*</span></label>
                                        <input type="number" step="1" min="1"
                                            class="form-control font-monospace edit-qty fw-bold" name="quantity"
                                            value="{{ $item->quantity }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted">نسبة الضريبة (%) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" max="100"
                                            class="form-control font-monospace edit-tax fw-bold text-danger"
                                            name="tax_percent" value="{{ floatval($item->tax_percent) }}" required>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <div
                                            class="alert alert-success d-flex justify-content-between font-bold font-18 mb-0 border-0 shadow-sm">
                                            <span>الإجمالي المتوقع:</span>
                                            <span><span class="edit-total-display">{{ number_format($item->total, 3) }}</span>
                                                OMR</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top-0">
                                <button type="button" class="btn btn-light rounded-pill px-4"
                                    data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-primary fw-bold rounded-pill px-5 shadow-sm">حفظ
                                    التعديلات</button>
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
    {{-- 🔥 مكتبة الجافاسكريبت بتاعة الـ Select2 🔥 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // 1. تفعيل الـ Select2 جوه المودال بتاع الإضافة
            $('#addFeeItemModal').on('shown.bs.modal', function() {
                $('.select2-multi').select2({
                    theme: "bootstrap-5",
                    dir: "rtl",
                    placeholder: "اختر الفروع...",
                    dropdownParent: $('#addFeeItemModal') // لمنع التهنيج داخل المودال
                });
            });

            // 2. زرار "تحديد الكل" السحري
            $('.select-all-branches').on('change', function() {
                let selectElement = $('.select2-multi');
                if ($(this).is(':checked')) {
                    // لو علم صح، اختار كل الفروع
                    let allValues = [];
                    selectElement.find('option').each(function() {
                        if ($(this).val()) allValues.push($(this).val());
                    });
                    selectElement.val(allValues).trigger('change');
                } else {
                    // لو شال الصح، امسح التحديد
                    selectElement.val(null).trigger('change');
                }
            });

            // 3. حساب الإجمالي اللحظي
            function calcAddTotal() {
                let price = parseFloat($('.add-price').val()) || 0;
                let qty = parseFloat($('.add-qty').val()) || 0;
                let tax = parseFloat($('.add-tax').val()) || 0;
                let sub = price * qty;
                let total = sub + (sub * (tax / 100));
                $('#add-total-display').text(total.toFixed(3));
            }
            $('.add-price, .add-qty, .add-tax').on('input', calcAddTotal);

            $('.edit-price, .edit-qty, .edit-tax').on('input', function() {
                let modal = $(this).closest('.modal-body');
                let price = parseFloat(modal.find('.edit-price').val()) || 0;
                let qty = parseFloat(modal.find('.edit-qty').val()) || 0;
                let tax = parseFloat(modal.find('.edit-tax').val()) || 0;
                let sub = price * qty;
                let total = sub + (sub * (tax / 100));
                modal.find('.edit-total-display').text(total.toFixed(3));
            });

            // 4. الحذف بـ SweetAlert
            $(document).on('click', '.delete-btn', function() {
                let deleteUrl = $(this).data('url');
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "سيتم حذف هذا البند نهائياً.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#95a5a6',
                    confirmButtonText: 'نعم، احذف!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-form').attr('action', deleteUrl).submit();
                    }
                });
            });

            // 5. منع الدبل كليك
            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1"></i> جاري الحفظ...');
            });
        });
    </script>
@endpush
