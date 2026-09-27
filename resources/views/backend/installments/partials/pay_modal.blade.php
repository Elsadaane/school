{{-- تأكد من استدعاء ملفات Select2 و SweetAlert2 في الـ Layout الأساسي لديك --}}

<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden border-top border-success border-4">

            <div class="modal-header bg-light border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bolder text-success">
                    <i class="mdi mdi-cash-multiple me-1"></i> تنفيذ حركة مالية (سداد قسط)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="payForm" action="" method="POST" class="prevent-double-submit">
                @csrf
                <div class="modal-body p-4 text-start">

                    <div
                        class="alert alert-info border-info border-opacity-25 bg-info bg-opacity-10 rounded-3 mb-4 shadow-sm">
                        <div
                            class="d-flex justify-content-between mb-2 border-bottom border-info border-opacity-25 pb-2">
                            <span class="text-dark fw-bold"><i class="mdi mdi-information-outline me-1"></i>
                                البيان:</span>
                            <span class="fw-bolder text-primary" id="modalEntityTitle"></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-dark fw-bold"><i class="mdi mdi-currency-usd me-1"></i> إجمالي
                                القسط:</span>
                            <span class="fw-bolder font-monospace" id="modalTotalAmount"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-danger fw-bold"><i class="mdi mdi-alert-circle-outline me-1"></i> المتبقي
                                للسداد:</span>
                            <span class="fw-bolder text-danger fs-5 font-monospace" id="modalRemainingAmount"></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">المبلغ المدفوع / المصروف <span
                                class="text-danger">*</span></label>
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-success text-white border-success"><i
                                    class="mdi mdi-cash fs-4"></i></span>
                            <input type="number" step="0.001" name="amount_paid" id="amountPaidInput"
                                class="form-control fw-bold font-monospace text-success fs-4 text-center border-success"
                                required>
                            <span class="input-group-text bg-white fw-bold border-success text-muted">OMR</span>
                        </div>
                        <small class="text-muted mt-2 d-block" id="paymentWarning" style="display: none !important;">
                            <i class="mdi mdi-alert-outline text-warning"></i> لا يمكن سداد مبلغ أكبر من المتبقي.
                        </small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">حساب الخزينة / البنك <span
                                    class="text-danger">*</span></label>
                            <select name="financial_account_id" class="form-select bg-light shadow-sm select2-pay-modal"
                                required style="width: 100%;">
                                <option value="" disabled selected>-- اختر الحساب المالي --</option>
                                {{-- تأكد من تمرير متغير $financialAccounts للـ View الذي يحتضن هذا المودال --}}
                                @foreach ($financialAccounts ?? [] as $account)
                                    <option value="{{ $account->id }}">{{ $account->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">رقم الشيك / الحوالة</label>
                            <input type="text" name="check_number"
                                class="form-control bg-light shadow-sm font-monospace text-center"
                                placeholder="إن وجد...">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark">ملاحظات على السند (اختياري)</label>
                        <textarea name="notes" class="form-control bg-light shadow-sm" rows="2"
                            placeholder="أية ملاحظات إضافية حول عملية السداد..."></textarea>
                    </div>

                </div>
                <div class="modal-footer bg-light border-top-0 pt-3 pb-3 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary fw-bold px-4 rounded-pill shadow-sm"
                        data-bs-dismiss="modal">إلغاء وتراجع</button>
                    <button type="submit" class="btn btn-success fw-bold px-5 shadow-sm rounded-pill btn-submit">
                        <i class="mdi mdi-check-decagram-outline me-1"></i> تأكيد وحفظ السند
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // 1. تفعيل Select2 عند فتح المودال لمنع مشاكل الـ Z-index
            $('#payModal').on('shown.bs.modal', function() {
                $('.select2-pay-modal').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#payModal')
                });
            });

            // 2. تعبئة البيانات ديناميكياً عند الضغط على زر الدفع في الجداول
            $(document).on('click', '.btn-pay-modal', function() {
                let url = $(this).data('url');
                let total = parseFloat($(this).data('total')) || 0;
                let remaining = parseFloat($(this).data('remaining')) || 0;
                let title = $(this).data('title');

                $('#modalEntityTitle').text(title);
                $('#modalTotalAmount').text(total.toFixed(3) + ' OMR');
                $('#modalRemainingAmount').text(remaining.toFixed(3) + ' OMR');

                // وضع المتبقي كقيمة افتراضية وكحد أقصى مسموح به
                $('#amountPaidInput').val(remaining.toFixed(3)).attr('max', remaining);

                // تغيير وجهة الفورم بناءً على القسم (طالب، أصل، فاتورة)
                $('#payForm').attr('action', url);
            });

            // 3. التحقق المباشر من المبلغ أثناء الكتابة
            $('#amountPaidInput').on('input', function() {
                let max = parseFloat($(this).attr('max'));
                let current = parseFloat($(this).val());

                if (current > max) {
                    $(this).addClass('is-invalid border-danger text-danger').removeClass(
                        'text-success border-success');
                    $('#paymentWarning').show();
                } else {
                    $(this).removeClass('is-invalid border-danger text-danger').addClass(
                        'text-success border-success');
                    $('#paymentWarning').hide();
                }
            });

            // 4. تأمين الإرسال باستخدام SweetAlert و Double Submit Protection
            $('#payForm').on('submit', function(e) {
                e.preventDefault();
                let form = this;
                let btn = $(this).find('.btn-submit');

                let amount = parseFloat($('#amountPaidInput').val());
                let max = parseFloat($('#amountPaidInput').attr('max'));

                // حماية أخيرة قبل الإرسال
                if (amount > max) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ محاسبي!',
                        text: 'المبلغ المدخل أكبر من المبلغ المتبقي للسداد.',
                        confirmButtonColor: '#1e2b3c'
                    });
                    return false;
                }

                Swal.fire({
                    title: 'تأكيد السداد؟',
                    text: 'سيتم تسجيل السند المالي واعتماده في الخزينة المحددة بشكل نهائي.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#27ae60',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، قم بالسداد',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري التنفيذ...');
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
