@extends('backend.layouts.master')
@section('title', 'إضافة تحويل بين الفروع')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #fff;
        }

        /* 🌟 Select2 Modern Tweaks 🌟 */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 45px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
            font-weight: bold;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 10px;
        }

        .table-erp {
            white-space: nowrap;
            /* تأمين الجداول من التكسير */
            margin-bottom: 0;
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px;
        }

        #transfer_tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .bg-light {
            background-color: #323a46 !important;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] #transfer_tbody tr:hover td {
            background-color: #3b4652;
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection,
        body[data-layout-color="dark"] .select2-dropdown {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 class="page-title font-bold mb-0 text-dark">
                <i class="bx bx-transfer-alt text-primary me-1 fs-4 align-middle"></i> إصدار أمر تحويل مخزني
            </h4>
            <a href="{{ route('admin.stock-transfers.index') }}"
                class="btn btn-light rounded-pill px-4 fw-bold shadow-sm border">
                <i class="mdi mdi-arrow-left me-1 align-middle"></i> العودة للسجل
            </a>
        </div>

        <form action="{{ route('admin.stock-transfers.store') }}" method="POST" id="transferForm">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger fw-bold shadow-sm border-0 rounded-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger fw-bold shadow-sm border-0 rounded-3">
                    <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
                </div>
            @endif

            {{-- 1. البيانات الأساسية للتحويل --}}
            <div class="card card-premium shadow-sm mb-4 border-top border-primary border-4">
                <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-map-pin text-primary me-1"></i> 1. مسار التحويل
                        والتواريخ</h5>
                </div>
                <div class="card-body p-4 bg-light bg-opacity-50 rounded-bottom-4">
                    <div class="row g-4 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label text-danger fw-bold"><i class="mdi mdi-upload me-1"></i> من الفرع
                                (المُرسِل) <span class="text-danger">*</span></label>
                            <select name="from_branch_id" id="from_branch_id"
                                class="form-select select2 border-danger shadow-sm" required>
                                <option value="" selected disabled>-- اختر فرع الإرسال --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('from_branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-success fw-bold"><i class="mdi mdi-download me-1"></i> إلى الفرع
                                (المُستقبِل) <span class="text-danger">*</span></label>
                            <select name="to_branch_id" id="to_branch_id"
                                class="form-select select2 border-success shadow-sm" required>
                                <option value="" selected disabled>-- اختر فرع الاستقبال --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('to_branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">تاريخ التحويل <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="transfer_date" class="form-control shadow-sm fw-bold text-center"
                                value="{{ old('transfer_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">تاريخ الاستلام المتوقع</label>
                            <input type="date" name="expected_receipt_date"
                                class="form-control shadow-sm fw-bold text-center"
                                value="{{ old('expected_receipt_date') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. المنتجات --}}
            <div class="card card-premium shadow-sm mb-4 border-top border-dark border-4">
                <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-box text-warning me-1"></i> 2. الأصناف المراد تحويلها
                    </h5>
                </div>
                <div class="card-body p-4 pt-0">

                    <div
                        class="mb-4 bg-primary bg-opacity-10 p-4 rounded-3 border border-primary border-opacity-25 shadow-sm">
                        <label class="form-label fw-bold text-primary fs-6"><i class="mdi mdi-barcode-scan me-1"></i> البحث
                            عن منتج داخل الفرع المُرسل</label>
                        <select id="product_search" class="form-select shadow-sm fw-bold" disabled>
                            <option value="">يرجى تحديد الفروع (المُرسل والمُستقبل) أولاً لتفعيل البحث...</option>
                        </select>
                    </div>

                    <div class="table-responsive border rounded-3 shadow-sm" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-erp text-center align-middle mb-0" id="transfer_table">
                            <thead class="sticky-top" style="z-index: 5;">
                                <tr>
                                    <th class="text-start ps-4">اسم المنتج / الصنف</th>
                                    <th style="width: 20%">الرصيد المتاح بالفرع</th>
                                    <th style="width: 20%">الكمية المُراد تحويلها</th>
                                    <th style="width: 10%">إجراء</th>
                                </tr>
                            </thead>
                            <tbody id="transfer_tbody">
                                {{-- يتم إدراج السطور هنا عبر JS --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 bg-light p-3 rounded-3 border">
                        <label class="form-label fw-bold text-muted"><i class="mdi mdi-text-box-outline me-1"></i> ملاحظات /
                            سبب التحويل (يظهر في المستند المطبوع)</label>
                        <textarea name="notes" class="form-control shadow-sm border-0" rows="2"
                            placeholder="اكتب أي ملاحظات لمدير المخزن المستلم هنا...">{{ old('notes') }}</textarea>
                    </div>

                    <div class="text-center mt-5 mb-2">
                        <button type="submit"
                            class="btn btn-primary rounded-pill px-5 py-3 fw-bold text-white fs-5 shadow-sm w-100 w-md-auto btn-submit">
                            <i class="bx bx-send me-2 align-middle fs-4"></i> اعتماد وإصدار أمر التحويل
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            $('.select2').select2({
                theme: "bootstrap-5",
                dir: isRtl ? "rtl" : "ltr"
            });

            // 1. تفعيل البحث عن المنتجات بعد اختيار الفرعين بشكل صحيح
            function checkBranchesAndEnableSearch() {
                let fromBranch = $('#from_branch_id').val();
                let toBranch = $('#to_branch_id').val();
                let productSearch = $('#product_search');

                if (fromBranch && toBranch && fromBranch === toBranch) {
                    Swal.fire('تنبيه هام!', 'لا يمكن التحويل لنفس الفرع! يرجى اختيار فرع مستقبل مختلف.', 'warning');
                    $('#to_branch_id').val(null).trigger('change.select2');
                    productSearch.prop('disabled', true).empty().append(
                        '<option value="">يرجى تحديد الفروع بشكل صحيح أولاً...</option>');
                    return;
                }

                if (fromBranch && toBranch) {
                    productSearch.prop('disabled', false).empty();

                    // تنظيف الجدول في حالة تغيير فرع الإرسال لضمان عدم بقاء أصناف غير متاحة
                    if (productSearch.data('current-branch') !== fromBranch) {
                        $('#transfer_tbody').empty();
                        productSearch.data('current-branch', fromBranch);
                    }

                    productSearch.select2({
                        theme: "bootstrap-5",
                        dir: isRtl ? "rtl" : "ltr",
                        placeholder: 'ابحث عن صنف لترحيله بالاسم أو الكود...',
                        ajax: {
                            url: "{{ route('admin.stock-transfers.get-products') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    q: params.term,
                                    branch_id: fromBranch
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data
                                };
                            }
                        }
                    });
                } else {
                    productSearch.prop('disabled', true).empty().append(
                        '<option value="">يرجى تحديد الفروع (المُرسل والمُستقبل) أولاً لتفعيل البحث...</option>'
                        );
                }
            }

            $('#from_branch_id, #to_branch_id').on('change', checkBranchesAndEnableSearch);

            // 2. إضافة الصنف للجدول عند اختياره
            $('#product_search').on('select2:select', function(e) {
                var data = e.params.data;

                // منع التكرار
                if ($('#row_' + data.id).length > 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'المنتج مكرر',
                        text: 'هذا الصنف تمت إضافته مسبقاً للقائمة! يرجى تعديل الكمية من الجدول.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    $(this).val(null).trigger('change');
                    return;
                }

                var row = `
                    <tr id="row_${data.id}">
                        <td class="text-start ps-4 fw-bold text-dark text-wrap" style="max-width: 250px;">
                            ${data.text.split('|')[0]}
                            <br><small class="text-muted font-monospace"><i class="mdi mdi-barcode-scan"></i> ${data.text.split('|')[1] || 'بدون كود'}</small>
                            <input type="hidden" name="items[${data.id}][product_id]" value="${data.id}">
                        </td>
                        <td class="fw-bold text-success available-qty fs-5 font-monospace">${parseFloat(data.available_qty)}</td>
                        <td>
                            <input type="number" step="1" name="items[${data.id}][quantity]" class="form-control text-center transfer-qty border-primary fw-bold font-monospace shadow-sm" value="1" min="1" max="${parseFloat(data.available_qty)}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-soft-danger btn-sm remove-row shadow-sm rounded-circle px-2 py-1" title="حذف من القائمة"><i class="mdi mdi-trash-can-outline fs-5"></i></button>
                        </td>
                    </tr>
                `;
                $('#transfer_tbody').append(row);
                $(this).val(null).trigger('change');
            });

            // 3. حذف السطر
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').fadeOut(200, function() {
                    $(this).remove();
                });
            });

            // 4. منع اليوزر من إدخال كمية تحويل أكبر من المتاحة
            $(document).on('input', '.transfer-qty', function() {
                let row = $(this).closest('tr');
                let maxQty = parseFloat(row.find('.available-qty').text());
                let enteredQty = parseFloat($(this).val()) || 0;

                if (enteredQty > maxQty) {
                    Swal.fire({
                        icon: 'error',
                        title: 'رصيد غير كافٍ',
                        text: 'الكمية المراد تحويلها أكبر من الرصيد المتاح في فرع الإرسال!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    $(this).val(maxQty);
                } else if (enteredQty < 1) {
                    $(this).val(1);
                }
            });

            // 5. حفظ الفورم وتوجيه المستخدم (Double Submit Protection)
            $('#transferForm').on('submit', function(e) {
                e.preventDefault();

                if ($('#transfer_tbody tr').length === 0) {
                    Swal.fire('جدول فارغ!', 'يجب إضافة منتج واحد على الأقل قبل حفظ واعتماد أمر التحويل.',
                        'error');
                    return false;
                }

                let submitBtn = $(this).find('.btn-submit');
                submitBtn.prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-2 align-middle fs-4"></i> جاري إرسال التحويل واعتماده...'
                    );

                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تمت العملية بنجاح',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href =
                                "{{ route('admin.stock-transfers.index') }}";
                        });
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(
                            '<i class="bx bx-send me-2 align-middle fs-4"></i> اعتماد وإصدار أمر التحويل'
                            );
                        let msg = xhr.responseJSON ? xhr.responseJSON.message :
                            'حدث خطأ غير متوقع أثناء المعالجة.';
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ في العملية',
                            text: msg
                        });
                    }
                });
            });
        });
    </script>
@endpush
