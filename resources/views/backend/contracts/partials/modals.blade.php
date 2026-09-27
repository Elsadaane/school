{{-- ===================== Modals Partials ===================== --}}
{{-- المسار المقترح: resources/views/backend/contracts/partials/modals.blade.php --}}

{{-- 1. Modal إضافة رسوم / منتج جديد بالداتابيز --}}
<div class="modal fade" id="addFeeModal" tabindex="-1" aria-labelledby="addFeeModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg border-top border-info border-4">
            <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="addFeeModalLabel">
                    <i class="mdi mdi-database-plus text-info me-1 fs-4 align-middle"></i> تعريف بند / رسوم جديدة
                    بالنظام
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-start bg-white">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-13 fw-bold text-muted">الفرع التابع له البند <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light fw-bold modal-branch-name shadow-sm border-0"
                            readonly>
                        <input type="hidden" id="m_fee_branch_id" class="modal-branch-id">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-13 fw-bold text-muted">نوع البند <span
                                class="text-danger">*</span></label>
                        <select id="m_fee_type" class="form-select fw-bold shadow-sm border-info">
                            <option value="service">خدمة / رسوم دراسية (بدون مخزون)</option>
                            <option value="product">منتج ملموس (له كمية ومخزون)</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label font-13 fw-bold text-muted">كود الصنف (SKU)</label>
                        <input type="text" id="m_fee_sku" class="form-control font-monospace shadow-sm"
                            placeholder="اتركه فارغاً للتوليد التلقائي">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-13 fw-bold text-muted">الاسم بالعربي <span
                                class="text-danger">*</span></label>
                        <input type="text" id="m_fee_name_ar" class="form-control fw-bold shadow-sm"
                            placeholder="مثال: رسوم دراسية قسط أول" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-13 fw-bold text-muted">الاسم بالإنجليزي</label>
                        <input type="text" id="m_fee_name_en" class="form-control shadow-sm" dir="ltr"
                            placeholder="e.g. Tuition Fee">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-13 fw-bold text-muted">سعر التكلفة (OMR)</label>
                        <input type="number" step="0.001" id="m_fee_cost"
                            class="form-control font-monospace text-center shadow-sm" value="0" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-13 fw-bold text-muted">سعر البيع للطالب <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.001" id="m_fee_sell"
                            class="form-control font-monospace text-center fw-bold border-primary shadow-sm"
                            value="0" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label font-13 fw-bold text-muted">نسبة الضريبة (%)</label>
                        <input type="number" step="0.01" id="m_fee_tax"
                            class="form-control font-monospace text-center shadow-sm" value="0" min="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light rounded-bottom-4 justify-content-between">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm"
                    data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-info rounded-pill text-white fw-bold px-5 shadow-sm"
                    id="btn-save-modal-fee">
                    <i class="mdi mdi-check-decagram-outline me-1 align-middle"></i> حفظ واعتماد البند
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 2. Modal إضافة حافلة (باص) جديد بالداتابيز --}}
<div class="modal fade" id="addBusModal" tabindex="-1" aria-labelledby="addBusModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg border-top border-warning border-4">
            <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="addBusModalLabel">
                    <i class="mdi mdi-bus-multiple text-warning me-1 fs-4 align-middle"></i> تعريف حافلة جديدة بالنظام
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-start bg-white">
                <div class="row g-3">
                    <div class="col-md-12 mb-2">
                        <label class="form-label font-13 fw-bold text-muted">الفرع <span
                                class="text-danger">*</span></label>
                        <input type="text"
                            class="form-control bg-light fw-bold modal-branch-name shadow-sm border-0" readonly>
                        <input type="hidden" id="m_bus_branch_id" class="modal-branch-id">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-13 fw-bold text-muted">رقم الحافلة (اللوحة) <span
                                class="text-danger">*</span></label>
                        <input type="text" id="m_bus_number" class="form-control fw-bold shadow-sm"
                            placeholder="مثال: 1234 أ ب" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-13 fw-bold text-muted">عدد الكراسي (السعة) <span
                                class="text-danger">*</span></label>
                        <input type="number" id="m_bus_capacity"
                            class="form-control font-monospace text-center fw-bold shadow-sm" value="30"
                            min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-13 fw-bold text-success">تسعيرة ذهاب وعودة <span
                                class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <input type="number" step="0.001" id="m_fare_both"
                                class="form-control font-monospace text-center fw-bold border-success" value="0"
                                required>
                            <span
                                class="input-group-text bg-success bg-opacity-10 text-success border-success">OMR</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-13 fw-bold text-info">تسعيرة اتجاه واحد <span
                                class="text-danger">*</span></label>
                        <div class="input-group shadow-sm">
                            <input type="number" step="0.001" id="m_fare_single"
                                class="form-control font-monospace text-center fw-bold border-info" value="0"
                                required>
                            <span class="input-group-text bg-info bg-opacity-10 text-info border-info">OMR</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light rounded-bottom-4 justify-content-between">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm"
                    data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-warning rounded-pill text-dark fw-bold px-5 shadow-sm"
                    id="btn-save-modal-bus">
                    <i class="mdi mdi-check-decagram-outline me-1 align-middle"></i> حفظ الحافلة
                </button>
            </div>
        </div>
    </div>
</div>
