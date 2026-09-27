@extends('backend.layouts.master')
@section('title', 'نقطة بيع المتجر المدرسي')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .pos-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            border: none;
        }

        .cart-header {
            background: #0f172a;
            color: white;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0;
        }

        .cart-table th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px;
        }

        .cart-table td {
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            padding: 10px;
        }

        .product-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
            background: #fff;
            text-align: center;
            padding: 15px 10px;
            height: 100%;
            position: relative;
            overflow: hidden;
            user-select: none;
        }

        .product-card:hover {
            transform: translateY(-4px);
            border-color: #3b82f6;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.15);
        }

        .product-card:active {
            transform: translateY(0);
        }

        .product-title {
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .product-price {
            font-size: 16px;
            font-weight: 900;
            color: #10b981;
            font-family: 'Courier New', Courier, monospace;
        }

        .totals-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            border: 1px dashed #cbd5e1;
        }

        .grand-total-val {
            font-size: 32px;
            font-weight: 900;
            color: #0ea5e9;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: -1px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border-radius: 6px;
        }

        /* 🌟 Select2 Modern Tweaks 🌟 */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 40px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);
            font-weight: bold;
        }

        /* 🌟 Dark Mode 🌟 */
        body[data-layout-color="dark"] .pos-card {
            background-color: #323a46;
            border: 1px solid #424e5a;
        }

        body[data-layout-color="dark"] .cart-header {
            background-color: #1e293b;
            border-bottom: 2px solid #3b82f6;
        }

        body[data-layout-color="dark"] .cart-table th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .cart-table td {
            border-color: #3b4652;
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .product-card {
            background: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .product-card:hover {
            border-color: #60a5fa;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
        }

        body[data-layout-color="dark"] .product-title {
            color: #f8fafc;
        }

        body[data-layout-color="dark"] .totals-box {
            background: #3b4652;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .grand-total-val {
            color: #38bdf8;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
            color: #fff;
        }

        body[data-layout-color="dark"] .bg-white {
            background-color: #323a46 !important;
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
    <div class="row mb-3 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold text-dark mb-0"><i
                        class="mdi mdi-store-outline text-primary me-1 fs-3 align-middle"></i> نقطة البيع السريعة (POS)</h4>
                <p class="text-muted font-13 mt-1 mb-0">المقصف والزي المدرسي - اضغط على المنتج لإضافته للسلة.</p>
            </div>
            <div>
                <a href="{{ route('admin.store-invoices.index') }}"
                    class="btn btn-light border rounded-pill px-4 fw-bold shadow-sm">
                    <i class="mdi mdi-receipt-text-outline me-1"></i> سجل المبيعات
                </a>
            </div>
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

    <form action="{{ route('admin.store-invoices.store') }}" method="POST" id="posForm" class="prevent-double-submit">
        @csrf
        <div class="row g-4">

            {{-- 📦 العمود الأيمن: المنتجات المتاحة (Quick Add) 📦 --}}
            <div class="col-lg-7">
                <div class="card pos-card h-100 border-top border-primary border-4 shadow-sm">
                    <div class="card-body p-4">
                        <div
                            class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3 flex-wrap gap-2">
                            <h5 class="text-dark fw-bold mb-0"><i
                                    class="mdi mdi-package-variant-closed text-primary me-1 fs-4 align-middle"></i> قائمة
                                المنتجات المتاحة</h5>

                            {{-- 🔥 شريط البحث السريع للـ POS 🔥 --}}
                            <div class="input-group input-group-sm w-auto shadow-sm rounded-3">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="mdi mdi-magnify text-primary"></i></span>
                                <input type="text" id="posSearch" class="form-control border-start-0 ps-0 fw-bold"
                                    placeholder="ابحث عن منتج..." style="min-width: 200px;">
                            </div>
                        </div>

                        <div class="row g-3" id="productsContainer">
                            @forelse ($products ?? [] as $product)
                                @php
                                    $price = $product->selling_price ?? ($product->price ?? 0);
                                @endphp
                                <div class="col-md-3 col-sm-4 col-6 product-item-wrapper">
                                    <div class="product-card shadow-sm"
                                        onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name_ar) }}', {{ $price }}, {{ $product->quantity }})">
                                        <div class="product-title text-wrap lh-base" style="min-height: 40px;">
                                            {{ Str::limit($product->name_ar, 30) }}</div>
                                        <div class="product-price mb-2">{{ number_format($price, 3) }}</div>

                                        @if ($product->quantity > 0)
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 font-11 font-monospace shadow-sm">متاح:
                                                {{ $product->quantity }}</span>
                                        @else
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 font-11 shadow-sm">نفذت
                                                الكمية</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <i class="mdi mdi-store-off-outline fs-1 d-block mb-2 text-muted opacity-50"></i>
                                    <h6 class="text-muted fw-bold">لا توجد منتجات متاحة للبيع في المخزن الخاص بك حالياً.
                                    </h6>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🛒 العمود الأيسر: السلة والدفع 🛒 --}}
            <div class="col-lg-5">
                <div class="card pos-card h-100 d-flex flex-column shadow-sm border-top border-dark border-4">
                    <div class="cart-header d-flex justify-content-between align-items-center shadow-sm">
                        <h5 class="mb-0 fw-bold text-white"><i class="mdi mdi-cart-outline me-1 fs-4 align-middle"></i> سلة
                            المشتريات</h5>
                        <button type="button" class="btn btn-sm btn-danger rounded-pill fw-bold shadow-sm px-3"
                            onclick="clearCart()">
                            <i class="mdi mdi-trash-can-outline me-1"></i> إفراغ
                        </button>
                    </div>

                    <div class="card-body p-0 flex-grow-1" style="overflow-y: auto; max-height: 450px;">
                        <div class="table-responsive">
                            <table class="table cart-table text-center mb-0 align-middle" id="cartTable">
                                <thead class="sticky-top shadow-sm" style="z-index: 5;">
                                    <tr>
                                        <th style="width: 45%;" class="text-start ps-3">المنتج</th>
                                        <th style="width: 25%;">الكمية</th>
                                        <th style="width: 20%;">الإجمالي</th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    {{-- يتم حقن المنتجات هنا عبر JS --}}
                                </tbody>
                            </table>
                        </div>
                        <div id="empty-cart-msg"
                            class="text-center text-muted py-5 bg-light h-100 d-flex flex-column justify-content-center">
                            <i class="mdi mdi-cart-remove fs-1 d-block mb-2 opacity-25 text-danger"></i>
                            <h6 class="fw-bold">السلة فارغة.</h6>
                            <small>اختر منتجات من القائمة لإضافتها.</small>
                        </div>
                    </div>

                    {{-- 💵 منطقة الدفع السفلية 💵 --}}
                    <div class="card-footer bg-white border-top p-4 rounded-bottom-4 shadow-sm">
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label text-muted fw-bold font-13"><i
                                        class="mdi mdi-account-school-outline me-1"></i> الطالب المشتري <span
                                        class="text-danger">*</span></label>
                                <select name="student_id" class="form-select select2 shadow-sm" required
                                    data-placeholder="-- ابحث باسم الطالب --">
                                    <option value=""></option>
                                    @foreach ($students ?? [] as $student)
                                        <option value="{{ $student->id }}">{{ $student->first_name }}
                                            {{ $student->second_name ?? '' }} {{ $student->tribe }}
                                            ({{ $student->grade->name_ar ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted fw-bold font-13"><i
                                        class="mdi mdi-wallet-outline me-1"></i> طريقة السداد</label>
                                <select name="payment_method"
                                    class="form-select shadow-sm border-success text-success fw-bold bg-success bg-opacity-10"
                                    required>
                                    <option value="cash">نقداً (Cash)</option>
                                    <option value="card">بطاقة بنكية (POS / Card)</option>
                                </select>
                            </div>
                        </div>

                        <div class="totals-box shadow-sm">
                            <div class="d-flex justify-content-between mb-2 font-14 fw-bold text-muted">
                                <span>الإجمالي الفرعي:</span>
                                <span class="font-monospace text-dark"><span id="display_sub_total">0.000</span>
                                    OMR</span>
                                <input type="hidden" name="sub_total" id="input_sub_total" value="0">
                            </div>
                            <div class="d-flex justify-content-between mb-3 align-items-center">
                                <span class="font-14 fw-bold text-danger">الخصم (إن وجد):</span>
                                <div class="input-group input-group-sm w-50 shadow-sm">
                                    <input type="number" step="0.001" name="discount" id="input_discount"
                                        class="form-control text-center font-monospace fw-bold border-danger text-danger bg-white shadow-none"
                                        value="0" min="0">
                                    <span class="input-group-text bg-danger text-white border-danger fw-bold">OMR</span>
                                </div>
                            </div>

                            <hr class="border-secondary opacity-25">

                            <div class="text-center mt-3">
                                <span class="d-block text-muted fw-bold font-13 text-uppercase mb-1">المطلوب سداده</span>
                                <div class="grand-total-val" id="display_grand_total">0.000</div>
                                <input type="hidden" name="grand_total" id="input_grand_total" value="0">
                            </div>
                        </div>

                        <button type="submit"
                            class="btn btn-primary w-100 mt-4 py-3 fs-5 fw-bold shadow-sm rounded-pill btn-submit"
                            id="checkoutBtn" disabled>
                            <i class="mdi mdi-cash-register me-2 align-middle fs-3"></i> إتمام الدفع وإصدار الفاتورة
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        let cart = {};

        // 🌟 إشعارات سريعة 🌟
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });

        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            $('.select2').select2({
                theme: "bootstrap-5",
                dir: isRtl ? "rtl" : "ltr",
                allowClear: true
            });

            // تحديث الحسابات عند تغيير الخصم
            $('#input_discount').on('input', updateTotals);

            // 🌟 نظام البحث السريع للمنتجات 🌟
            $("#posSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#productsContainer .product-item-wrapper").filter(function() {
                    $(this).toggle($(this).find('.product-title').text().toLowerCase().indexOf(
                        value) > -1)
                });
            });

            // منع الحفظ العشوائي
            $('#posForm').on('submit', function(e) {
                if (!$('select[name="student_id"]').val()) {
                    e.preventDefault();
                    Swal.fire('تنبيه!', 'يرجى تحديد الطالب المشتري أولاً لتسجيل الفاتورة باسمه.',
                    'warning');
                    return false;
                }

                if (Object.keys(cart).length === 0) {
                    e.preventDefault();
                    Swal.fire('السلة فارغة!', 'يرجى إضافة منتج واحد على الأقل لإصدار الفاتورة.', 'error');
                    return false;
                }

                // تغيير حالة الزر لمنع الدبل كليك
                let btn = $('#checkoutBtn');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-2 fs-3 align-middle"></i> جاري الدفع والإصدار...'
                    );
            });
        });

        // 🛒 دوال السلة 🛒
        function addToCart(id, name, price, maxQty) {
            if (maxQty <= 0) {
                Toast.fire({
                    icon: 'error',
                    title: 'عذراً، هذا المنتج نافذ من المخزن حالياً!'
                });
                return;
            }

            if (cart[id]) {
                if (cart[id].qty < maxQty) {
                    cart[id].qty++;
                    $(`#qty-${id}`).val(cart[id].qty);
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: `الحد الأقصى المتوفر هو ${maxQty} فقط!`
                    });
                    return;
                }
            } else {
                cart[id] = {
                    name: name,
                    price: price,
                    qty: 1,
                    max: maxQty
                };

                // 🔥 استخدام id المنتج كـ Key في مصفوفة الـ Items المُرسلة للباك إند لمنع تداخل البيانات 🔥
                let tr = `
                    <tr id="row-${id}">
                        <td class="text-start ps-3 fw-bold text-dark font-13 text-wrap" style="max-width: 150px;">
                            ${name}
                            <div class="text-muted font-11 font-monospace mt-1">${price.toFixed(3)} OMR</div>
                            <input type="hidden" name="items[${id}][product_id]" value="${id}">
                            <input type="hidden" name="items[${id}][unit_price]" value="${price}">
                        </td>
                        <td>
                            <div class="input-group input-group-sm mx-auto shadow-sm" style="width: 100px;">
                                <button type="button" class="btn btn-light border fw-bold text-danger px-2" onclick="changeQty(${id}, -1)">-</button>
                                <input type="text" name="items[${id}][quantity]" id="qty-${id}" class="form-control text-center font-monospace fw-bold bg-white px-0" value="1" readonly>
                                <button type="button" class="btn btn-light border fw-bold text-success px-2" onclick="changeQty(${id}, 1)">+</button>
                            </div>
                        </td>
                        <td class="align-middle">
                            <span class="font-bold text-primary font-monospace fs-6" id="display-total-${id}">${price.toFixed(3)}</span>
                            <input type="hidden" name="items[${id}][row_total]" id="input-total-${id}" value="${price}">
                        </td>
                        <td class="align-middle">
                            <button type="button" class="btn btn-sm text-danger bg-danger bg-opacity-10 border-0 p-1 rounded-circle shadow-sm" onclick="removeFromCart(${id})" title="حذف">
                                <i class="mdi mdi-close font-18 align-middle"></i>
                            </button>
                        </td>
                    </tr>`;

                $('#cart-items').prepend(tr); // إضافة الجديد في الأعلى ليكون مرئياً فوراً
                $('#empty-cart-msg').hide();
            }

            updateRowTotal(id);
        }

        function changeQty(id, amount) {
            let newQty = cart[id].qty + amount;
            if (newQty > 0 && newQty <= cart[id].max) {
                cart[id].qty = newQty;
                $(`#qty-${id}`).val(newQty);
                updateRowTotal(id);
            } else if (newQty > cart[id].max) {
                Toast.fire({
                    icon: 'warning',
                    title: `لا يوجد سوى ${cart[id].max} قطعة في المخزن.`
                });
            } else if (newQty <= 0) {
                removeFromCart(id); // لو نزل لصفر نحذفه أحسن
            }
        }

        function updateRowTotal(id) {
            let rowTotal = cart[id].qty * cart[id].price;
            $(`#display-total-${id}`).text(rowTotal.toFixed(3));
            $(`#input-total-${id}`).val(rowTotal.toFixed(3));
            updateTotals();
        }

        function removeFromCart(id) {
            $(`#row-${id}`).fadeOut(200, function() {
                $(this).remove();
                delete cart[id];
                if (Object.keys(cart).length === 0) {
                    $('#empty-cart-msg').fadeIn(200);
                }
                updateTotals();
            });
        }

        function clearCart() {
            if (Object.keys(cart).length === 0) return;

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف جميع المنتجات من السلة وإلغاء العملية الحالية.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، أفرغ السلة',
                cancelButtonText: 'تراجع'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#cart-items').empty();
                    cart = {};
                    $('#empty-cart-msg').fadeIn(200);
                    updateTotals();
                    $('#input_discount').val(0); // تصفير الخصم مع السلة
                }
            });
        }

        function updateTotals() {
            let subTotal = 0;
            for (let id in cart) {
                subTotal += cart[id].qty * cart[id].price;
            }

            let discount = parseFloat($('#input_discount').val()) || 0;

            // تأمين الخصم (لا يمكن أن يكون أكبر من الفاتورة ولا يمكن أن يكون بالسالب)
            if (discount > subTotal) {
                discount = subTotal;
                $('#input_discount').val(discount);
            } else if (discount < 0) {
                discount = 0;
                $('#input_discount').val(0);
            }

            let grandTotal = subTotal - discount;

            $('#display_sub_total').text(subTotal.toFixed(3));
            $('#input_sub_total').val(subTotal.toFixed(3));

            $('#display_grand_total').text(grandTotal.toFixed(3));
            $('#input_grand_total').val(grandTotal.toFixed(3));

            // تفعيل أو تعطيل زر الدفع
            if (grandTotal >= 0 && Object.keys(cart).length > 0) {
                $('#checkoutBtn').prop('disabled', false);
            } else {
                $('#checkoutBtn').prop('disabled', true);
            }
        }
    </script>
@endpush
