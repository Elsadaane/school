@extends('backend.layouts.master')
@section('title', 'رادار النواقص والتنبيهات')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <style>
        .page-wrapper {
            background-color: #f8f9fa;
            padding: 25px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .filter-card {
            background-color: #fffaf0;
            border: 1px dashed #f59e0b;
            border-radius: 12px;
        }

        .table-custom {
            white-space: nowrap;
            /* منع تكسير النصوص في الشاشات الصغيرة */
        }

        .table-custom thead th {
            padding: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-custom tbody td {
            vertical-align: middle;
            padding: 15px;
            border-bottom: 1px dashed #eee;
        }

        body[data-layout-color="dark"] .filter-card {
            background-color: rgba(245, 158, 11, 0.05);
            border-color: #d97706;
        }

        body[data-layout-color="dark"] .table-light {
            background-color: #323a46 !important;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom tbody td {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .badge.bg-secondary.bg-opacity-10 {
            background-color: #424e5a !important;
            color: #ced4da !important;
            border-color: #64748b !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h4 class="page-title font-bold text-danger mb-0">
                    <i class="mdi mdi-alert-decagram me-1 fs-3 align-middle"></i> رادار النواقص والتنبيهات
                </h4>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    {{-- 🔥 فلتر الفروع مع Select2 🔥 --}}
                    <form action="{{ route('admin.products.low-stock') }}" method="GET" class="d-flex gap-2"
                        style="min-width: 250px;">
                        <select name="branch_id"
                            class="form-select select2-filter border-warning shadow-sm fw-bold text-dark"
                            onchange="this.form.submit()">
                            <option value="">-- عرض نواقص كل الفروع --</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    {{-- 🔥 حماية زر المشتريات بصلاحية المشتريات 🔥 --}}
                    @can('create_purchase_invoices')
                        <a href="{{ route('admin.purchase-invoices.create') }}"
                            class="btn btn-danger fw-bold shadow-sm rounded-pill px-4">
                            <i class="mdi mdi-cart-arrow-down me-1"></i> إنشاء طلبية شراء
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="border-top: 4px solid #ef4444 !important;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover table-centered text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الفرع التابع له</th>
                                <th>كود الصنف (SKU)</th>
                                <th class="text-start">المنتج المجرود</th>
                                <th>الرصيد الفعلي</th>
                                <th>حد الخطر (التنبيه)</th>
                                <th>الحالة الحرجة</th>
                                @can('create_purchase_invoices')
                                    <th>إجراء سريع</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <span
                                            class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 font-12 shadow-sm rounded-pill">
                                            <i class="mdi mdi-store-marker-outline"></i>
                                            {{ $product->branch->name_ar ?? 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td dir="ltr" class="fw-bold text-muted font-monospace">
                                        {{ $product->sku ?? '---' }}</td>
                                    <td class="text-start fw-bold text-dark text-wrap" style="max-width: 250px;">
                                        {{ $product->name_ar }}
                                    </td>

                                    {{-- تمييز الكمية باللون الأحمر المشتعل لو صفر --}}
                                    <td>
                                        <span
                                            class="font-18 fw-900 font-monospace {{ $product->quantity == 0 ? 'text-danger' : 'text-warning text-dark' }}">
                                            {{ $product->quantity }}
                                        </span>
                                    </td>

                                    <td class="text-muted fw-bold font-monospace">{{ $product->alert_quantity }}</td>

                                    <td>
                                        @if ($product->quantity == 0)
                                            <span class="badge bg-danger rounded-pill px-3 py-2 font-12 shadow-sm">
                                                <i class="mdi mdi-close-octagon me-1"></i> نفذت الكمية تماماً
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-warning text-dark rounded-pill px-3 py-2 font-12 shadow-sm">
                                                <i class="mdi mdi-alert me-1"></i> كمية حرجة (يجب الشراء)
                                            </span>
                                        @endif
                                    </td>

                                    @can('create_purchase_invoices')
                                        <td>
                                            <a href="{{ route('admin.purchase-invoices.create') }}?branch_id={{ $product->branch_id }}&product_id={{ $product->id }}"
                                                class="btn btn-sm btn-outline-danger rounded-pill fw-bold px-3 shadow-sm">
                                                <i class="mdi mdi-cart-plus me-1"></i> شراء الآن
                                            </a>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('create_purchase_invoices') ? 7 : 6 }}"
                                        class="text-center py-5">
                                        <div class="mb-3 mt-2">
                                            <i class="mdi mdi-check-decagram text-success opacity-75"
                                                style="font-size: 60px;"></i>
                                        </div>
                                        <h4 class="text-dark fw-bold">المخازن في حالة ممتازة!</h4>
                                        <p class="text-muted font-14">لا توجد أي منتجات وصلت لحد التنبيه في هذا الفرع.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 الحفاظ على الفلاتر في الـ Pagination 🔥 --}}
            @if ($products->hasPages())
                <div class="card-footer bg-white border-top pt-3 pb-3 rounded-bottom-4">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // تفعيل Select2 لفلتر الفروع
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                dir: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
            });
        });
    </script>
@endpush
