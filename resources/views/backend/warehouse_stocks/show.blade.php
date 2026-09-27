@extends('backend.layouts.master')
@section('title', 'كارت الصنف - تفاصيل المنتج')
@section('title_page', 'إدارة المخازن والجرد')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
        }

        .stat-box {
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background: #fff;
            transition: 0.3s;
        }

        .stat-box:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .bg-light-primary {
            background-color: #eff6ff !important;
            border-color: #bfdbfe !important;
        }

        .bg-light-danger {
            background-color: #fef2f2 !important;
            border-color: #fecaca !important;
        }

        .bg-light-success {
            background-color: #f0fdf4 !important;
            border-color: #bbf7d0 !important;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 800;
            font-size: 13px;
            width: 35%;
            padding: 14px;
        }

        .table-custom td {
            color: #0f172a;
            font-weight: 700;
            font-size: 14px;
            padding: 14px;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium,
        body[data-layout-color="dark"] .stat-box {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .table-custom th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light-primary {
            background-color: rgba(59, 130, 246, 0.1) !important;
            border-color: rgba(59, 130, 246, 0.2) !important;
        }

        body[data-layout-color="dark"] .bg-light-danger {
            background-color: rgba(239, 68, 68, 0.1) !important;
            border-color: rgba(239, 68, 68, 0.2) !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 رأس الصفحة 🌟 --}}
        <div class="row mb-4 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="page-title font-bold text-dark mb-1">
                        <i class="bx bx-info-circle text-primary me-1 fs-3 align-middle"></i>
                        بطاقة الصنف المخزني: <span class="text-primary">{{ $product->name_ar }}</span>
                    </h4>
                    <p class="text-muted mb-0 font-14">متابعة أرصدة المنتج، التسعير، والحركات المخزنية داخل الفرع.</p>
                </div>
                <div>
                    <a href="{{ route('admin.warehouse_stocks.index') }}"
                        class="btn btn-light rounded-pill border fw-bold shadow-sm px-4">
                        <i class="bx bx-arrow-back align-middle me-1"></i> العودة للمخزون
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            {{-- الفرع --}}
            <div class="col-md-3">
                <div class="stat-box shadow-sm">
                    <small class="text-muted d-block mb-2 font-13 fw-bold"><i
                            class="bx bx-store-alt fs-5 align-middle me-1"></i> الفرع المتواجد به</small>
                    <h6 class="fw-bold mb-0 text-dark">{{ $product->branch?->name_ar ?? 'فرع محذوف أو غير محدد' }}</h6>
                </div>
            </div>

            {{-- الكود --}}
            <div class="col-md-3">
                <div class="stat-box shadow-sm">
                    <small class="text-muted d-block mb-2 font-13 fw-bold"><i
                            class="bx bx-barcode fs-5 align-middle me-1"></i> كود الصنف (SKU)</small>
                    <h6 class="fw-bolder mb-0 font-monospace text-dark fs-5" dir="ltr">{{ $product->sku ?? '---' }}
                    </h6>
                </div>
            </div>

            {{-- إجمالي الرصيد --}}
            <div class="col-md-3">
                <div class="stat-box bg-light-primary shadow-sm border-primary border-opacity-50 border-2">
                    <small class="text-primary d-block mb-1 fw-bold font-13">الرصيد الفعلي المتاح بالمخزن</small>
                    <h2 class="fw-bolder mb-0 text-primary font-monospace">{{ $product->quantity }} <span
                            class="fs-6 font-sans">وحدة</span></h2>
                </div>
            </div>

            {{-- حد التنبيه --}}
            <div class="col-md-3">
                <div class="stat-box bg-light-danger shadow-sm border-danger border-opacity-25">
                    <small class="text-danger d-block mb-1 fw-bold font-13"><i
                            class="bx bx-error fs-5 align-middle me-1"></i> حد الخطر (إعادة الطلب)</small>
                    <h3 class="fw-bolder mb-2 text-danger font-monospace">{{ $product->alert_quantity }}</h3>

                    @if ($product->quantity <= $product->alert_quantity)
                        <span class="badge bg-danger rounded-pill px-3 shadow-sm"><i class="bx bx-trending-down me-1"></i>
                            رصيد منخفض جداً!</span>
                    @else
                        <span
                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 shadow-sm"><i
                                class="bx bx-check-circle me-1"></i> الرصيد آمن</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- التفاصيل المالية والأساسية --}}
            <div class="col-xl-6">
                <div class="card card-premium shadow-sm h-100 border-top border-secondary border-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i
                                class="bx bx-list-ul text-secondary me-1 fs-5 align-middle"></i> البيانات المالية وتفاصيل
                            المنتج</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-custom mb-0">
                            <tbody>
                                <tr>
                                    <th>الاسم باللغة الإنجليزية</th>
                                    <td dir="ltr" class="font-monospace fw-bold">{{ $product->name_en ?? '---' }}</td>
                                </tr>
                                <tr>
                                    <th>سعر التكلفة (سعر الشراء)</th>
                                    <td class="text-danger font-monospace fs-6">{{ number_format($product->cost_price, 3) }}
                                        OMR</td>
                                </tr>
                                <tr>
                                    <th>سعر البيع للطلاب</th>
                                    <td class="text-success font-monospace fs-6 fw-bolder">
                                        {{ number_format($product->selling_price, 3) }} OMR</td>
                                </tr>
                                <tr>
                                    <th>إجمالي المبيعات السابقة</th>
                                    <td><span
                                            class="badge bg-primary px-3 py-1 font-13 rounded-pill">{{ $product->sold_quantity ?? 0 }}
                                            وحدة مباعة</span></td>
                                </tr>
                                <tr>
                                    <th>حالة المنتج</th>
                                    <td>
                                        @if ($product->is_active)
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill"><i
                                                    class="bx bx-check"></i> مفعل ومتاح للبيع</span>
                                        @else
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill"><i
                                                    class="bx bx-x"></i> موقوف حالياً</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- الوصف التفصيلي --}}
            <div class="col-xl-6">
                <div class="card card-premium shadow-sm h-100 border-top border-info border-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-detail text-info me-1 fs-5 align-middle"></i>
                            الوصف وملاحظات الصنف</h6>
                    </div>
                    <div class="card-body p-4 bg-light d-flex align-items-center justify-content-center">
                        @if ($product->description_ar)
                            <p class="mb-0 text-dark lh-lg font-14 fw-semibold text-justify w-100">
                                {{ $product->description_ar }}</p>
                        @else
                            <div class="text-center text-muted opacity-50">
                                <i class="bx bx-note d-block mb-2" style="font-size: 50px;"></i>
                                <h6 class="fw-bold">لا يوجد وصف مسجل لهذا المنتج.</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- سجل الحركات المخزنية (إضافة مقترحة لتعزيز جودة النظام) --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-premium shadow-sm border-top border-dark border-4">
                    <div class="card-header bg-white border-bottom py-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-history text-dark me-1 fs-5 align-middle"></i>
                            سجل الحركات المخزنية المعتمدة لهذا الصنف</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-custom text-center align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>تاريخ الحركة</th>
                                        <th>نوع الحركة</th>
                                        <th>الكمية المتأثرة</th>
                                        <th class="text-start ps-3">تفاصيل وملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- هذا مثال هيكلي (Placeholder) يمكنك ربطه لاحقاً بجدول الـ Stock Movements --}}
                                    <tr>
                                        <td colspan="4" class="py-5 text-muted bg-white text-center">
                                            <i class="bx bx-data fs-1 d-block mb-2 opacity-50"></i>
                                            <h6 class="fw-bold">لم يتم تسجيل حركات مخزنية لهذا الصنف مؤخراً.</h6>
                                            <p class="font-13 mb-0">سيبدأ السجل بالظهور عند الشراء، الإرجاع، أو البيع
                                                للطلاب.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
