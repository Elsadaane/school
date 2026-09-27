@extends('backend.layouts.master')
@section('title', 'إجراء جرد وتسوية مخزنية')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .form-section-title {
            font-size: 15px;
            font-weight: 800;
            color: #dc3545;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .invoice-table {
            white-space: nowrap;
            /* تأمين الجدول من التكسير على الشاشات الصغيرة */
        }

        .invoice-table th {
            background-color: #f8f9fa;
            font-size: 13px;
            font-weight: 700;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            text-align: center;
        }

        .invoice-table td {
            vertical-align: middle;
            padding: 10px 5px;
        }

        .diff-badge {
            font-family: 'Courier New', Courier, monospace;
            font-size: 15px;
            font-weight: 900;
            padding: 5px 12px;
            border-radius: 6px;
            display: inline-block;
            min-width: 55px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .diff-negative {
            background-color: #fef2f2;
            color: #ef4444;
            border: 1px solid #fca5a5;
        }

        .diff-positive {
            background-color: #ecfdf5;
            color: #10b981;
            border: 1px solid #6ee7b7;
        }

        .diff-zero {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        /* تعديلات Select2 */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            min-height: 38px;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .form-section-title {
            color: #ff5b5b;
            border-bottom-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
            border: 1px solid #424e5a !important;
            color: #fff;
        }

        body[data-layout-color="dark"] .invoice-table th {
            background-color: #323a46;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .diff-negative {
            background-color: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
        }

        body[data-layout-color="dark"] .diff-positive {
            background-color: rgba(16, 185, 129, 0.1);
            border-color: #10b981;
        }

        body[data-layout-color="dark"] .diff-zero {
            background-color: #424e5a;
            color: #adb5bd;
            border-color: #64748b;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold mb-0 text-danger">
                    <i class="mdi mdi-clipboard-list-outline me-1"></i> إنشاء سند تسوية جردية
                </h4>
                <p class="text-muted font-13 mt-1 mb-0">تعديل كميات المخزون بناءً على الجرد الفعلي للمستودعات.</p>
            </div>
            <a href="{{ route('admin.inventory-adjustments.index') }}"
                class="btn btn-light rounded-pill shadow-sm border fw-bold px-4">
                إلغاء ورجوع
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-3 fw-bold">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li><i class="mdi mdi-alert-circle-outline me-1"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.inventory-adjustments.store') }}" method="POST" id="adjustmentForm">
                @csrf

                <h5 class="form-section-title"><i class="mdi mdi-information-outline me-1"></i> بيانات السند الأساسية</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-bold">فرع المدرسة (المستودع) <span
                                class="text-danger">*</span></label>
                        <select class="form-select bg-light fw-bold" name="branch_id" id="branch_id" required>
                            <option value="" disabled selected>-- اختر الفرع أولاً لبدء الجرد --</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted fw-bold">تاريخ الجرد <span class="text-danger">*</span></label>
                        <input type="date" class="form-control bg-light text-center fw-bold" name="adjustment_date"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 mt-5 flex-wrap gap-2">
                    <h5 class="form-section-title mb-0 border-0"><i class="mdi mdi-package-variant me-1"></i> بنود الجرد
                        الفعلي</h5>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-bold px-4 shadow-sm"
                        id="addRowBtn">
                        <i class="mdi mdi-plus me-1"></i> جرد منتج آخر
                    </button>
                </div>

                <div class="table-responsive mb-4" style="min-height: 250px; overflow-x: visible;">
                    <table class="table invoice-table table-nowrap" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 30%;">المنتج المجرود</th>
                                <th style="width: 15%;">رصيد النظام الحالي</th>
                                <th style="width: 15%;">الجرد الفعلي (على الرف)</th>
                                <th style="width: 10%;">الفرق</th>
                                <th style="width: 25%;">سبب العجز/الزيادة</th>
                                <th style="width: 5%;">حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- يتم حقن السطور برمجياً --}}
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4 align-items-center bg-light p-3 rounded-4 border">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <label class="form-label text-muted fw-bold">ملاحظات عامة على الجرد</label>
                        <input type="text" class="form-control bg-white fw-bold shadow-sm border-0" name="notes"
                            placeholder="ملاحظات تظهر لمدير المخازن عند الاعتماد...">
                    </div>
                    <div class="col-md-4 text-md-end text-center">
                        <button type="submit" class="btn btn-danger btn-lg rounded-pill px-5 fw-bold shadow-sm w-100"
                            id="submitBtn">
                            <i class="mdi mdi-content-save-edit me-1"></i> حفظ كمسودة للاعتماد
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            let previousBranch = $('#branch_id').val();

            // 🔥 1. حماية تغيير الفرع للحفاظ على عمل المستخدم 🔥
            $('#branch_id').on('change', function() {
                let currentBranch = $(this).val();
                let rowsCount = $('#itemsTable tbody tr').length;

                if (rowsCount > 0) {
                    Swal.fire({
                        title: 'تغيير الفرع؟',
                        text: 'تغيير الفرع سيؤدي إلى مسح جميع البنود الحالية المجرودة. هل أنت متأكد؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'نعم، امسح وابدأ من جديد',
                        cancelButtonText: 'تراجع'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#itemsTable tbody').empty();
                            previousBranch = currentBranch;
                            addRow();
                        } else {
                            // التراجع وإعادة الاختيار السابق
                            $('#branch_id').val(previousBranch);
                        }
                    });
                } else {
                    previousBranch = currentBranch;
                    $('#itemsTable tbody').empty();
                    addRow();
                }
            });

            // 🔥 2. دالة إضافة السطر وتهيئة الـ Select2 بمعرف فريد (Timestamp) 🔥
            function addRow() {
                let branchId = $('#branch_id').val();

                if (!branchId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى اختيار فرع المستودع أولاً قبل إضافة بنود للجرد.',
                        confirmButtonColor: '#1e2b3c'
                    });
                    return;
                }

                let rowId = Date.now(); // Unique ID لمنع تداخل الـ Indexes عند الحذف

                let tr = `
                    <tr id="row_${rowId}">
                        <td class="text-start">
                            <select class="form-select item-select select2-ajax" name="items[${rowId}][product_id]" style="width:100%;" required>
                                <option value="" disabled selected>ابحث عن المنتج بالاسم أو الكود...</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control item-sysqty bg-light text-center fw-bold border-0 font-monospace" value="0" readonly>
                        </td>
                        <td>
                            <input type="number" class="form-control item-actualqty border-danger text-center fw-bold font-monospace shadow-sm" name="items[${rowId}][actual_quantity]" value="0" min="0" required>
                        </td>
                        <td class="text-center">
                            <span class="diff-badge diff-zero item-diff shadow-sm">0</span>
                        </td>
                        <td>
                            <input type="text" class="form-control item-reason bg-light border-0 fw-bold" name="items[${rowId}][reason]" placeholder="الرصيد متطابق">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm remove-row" title="حذف السطر">
                                <i class="mdi mdi-trash-can-outline font-16"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#itemsTable tbody').append(tr);

                initSelect2Ajax($('#row_' + rowId + ' .select2-ajax'));
            }

            $('#addRowBtn').click(addRow);

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').fadeOut(300, function() {
                    $(this).remove();
                });
            });

            // 🔥 3. إعدادات الـ AJAX لجلب بيانات المنتجات بناءً على الفرع 🔥
            function initSelect2Ajax(element) {
                element.select2({
                    theme: "bootstrap-5",
                    dir: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}",
                    placeholder: "ابحث عن المنتج بالاسم أو الكود...",
                    ajax: {
                        url: "{{ route('admin.get-inventory-products') }}",
                        dataType: 'json',
                        delay: 300,
                        data: function(params) {
                            return {
                                q: params.term,
                                branch_id: $('#branch_id').val()
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });
            }

            // 🔥 4. السحر: التحقق من التكرار وقراءة الرصيد عند الاختيار 🔥
            $(document).on('select2:select', '.item-select', function(e) {
                let data = e.params.data;
                let currentSelect = $(this);
                let selectedProductId = data.id;
                let isDuplicate = false;

                // حارس التكرار: تجاهل الحقول الفارغة والتركيز على المنتجات المُختارة فقط
                $('.item-select').not(currentSelect).each(function() {
                    let val = $(this).val();
                    if (val && val == selectedProductId) {
                        isDuplicate = true;
                    }
                });

                let tr = currentSelect.closest('tr');

                if (isDuplicate) {
                    Swal.fire({
                        title: 'المنتج مكرر!',
                        text: 'هذا الصنف تمت إضافته بالفعل في قائمة الجرد، قم بتعديل الكمية في السطر الخاص به.',
                        icon: 'warning',
                        confirmButtonColor: '#e67e22',
                        confirmButtonText: 'حسناً'
                    });

                    currentSelect.val(null).trigger('change');
                    tr.find('.item-sysqty').val(0);
                    tr.find('.item-actualqty').val(0).trigger('input');
                    return false;
                }

                if (data && data.system_qty !== undefined) {
                    tr.find('.item-sysqty').val(data.system_qty);
                    tr.find('.item-actualqty').val(data.system_qty).trigger('input');
                }
            });

            // 🔥 5. حساب الفرق الديناميكي وتغيير الألوان والـ Required 🔥
            $(document).on('input', '.item-actualqty', function() {
                calculateDifference($(this).closest('tr'));
            });

            function calculateDifference(tr) {
                let sysQty = parseFloat(tr.find('.item-sysqty').val()) || 0;
                let actualQty = parseFloat(tr.find('.item-actualqty').val()) || 0;
                let difference = actualQty - sysQty;

                let diffBadge = tr.find('.item-diff');
                let reasonInput = tr.find('.item-reason');

                diffBadge.removeClass('diff-negative diff-positive diff-zero');
                reasonInput.removeClass('bg-light border-0').addClass('border-danger shadow-sm');

                if (difference < 0) {
                    diffBadge.text(difference).addClass('diff-negative');
                    reasonInput.prop('required', true).attr('placeholder', 'اكتب سبب العجز هنا (مطلوب)*');
                } else if (difference > 0) {
                    diffBadge.text('+' + difference).addClass('diff-positive');
                    reasonInput.prop('required', true).attr('placeholder', 'اكتب سبب الزيادة هنا (مطلوب)*');
                } else {
                    diffBadge.text('0').addClass('diff-zero');
                    reasonInput.prop('required', false)
                        .removeClass('border-danger shadow-sm').addClass('bg-light border-0')
                        .attr('placeholder', 'الرصيد متطابق');
                    reasonInput.val('');
                }
            }

            // 🔥 6. تأمين إرسال الفورم (Double Submit & Validation) 🔥
            $('#adjustmentForm').on('submit', function(e) {
                if ($('#itemsTable tbody tr').length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'جدول فارغ!',
                        text: 'لا يمكن حفظ السند. يرجى إضافة بند مجرود واحد على الأقل.',
                        confirmButtonColor: '#1e2b3c'
                    });
                    return false;
                }

                $('#submitBtn').prop('disabled', true).html(
                    '<i class="mdi mdi-spin mdi-loading me-1"></i> جاري حفظ السند والاعتماد...');
            });
        });
    </script>
@endpush
