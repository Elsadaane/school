@extends('backend.layouts.master')
@section('title', 'تقرير نواقص المخزون')
@section('title_page', 'التقارير التحليلية - المخزون')

@push('css')
    <style>
        .kpi-card {
            border-radius: 12px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .table-custom {
            white-space: nowrap;
        }

        .table-custom thead th {
            background-color: #f8f9fa;
            color: #475569;
            font-weight: 800;
            text-align: center;
        }

        .table-custom tbody td {
            vertical-align: middle;
            text-align: center;
        }

        body[data-layout-color="dark"] .table-custom th {
            background-color: #3b4652;
            color: #ced4da;
        }

        body[data-layout-color="dark"] .table-custom td {
            color: #adb5bd;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        {{-- كروت الـ KPI --}}
        <div class="row">
            <div class="col-md-4">
                <div class="kpi-card bg-primary">
                    <h6 class="text-white opacity-75">إجمالي الأصناف الناقصة</h6>
                    <h2 class="fw-bold">{{ $kpis['total_shortages'] }} صنف</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card bg-danger">
                    <h6 class="text-white opacity-75">أصناف نفذت تماماً (صفر)</h6>
                    <h2 class="fw-bold">{{ $kpis['out_of_stock'] }} صنف</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card bg-warning">
                    <h6 class="text-white opacity-75">تكلفة إعادة التوريد المطلوبة</h6>
                    <h2 class="fw-bold">{{ number_format($kpis['restock_cost'], 3) }} OMR</h2>
                </div>
            </div>
        </div>

        {{-- الجدول --}}
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="mdi mdi-alert-circle-outline text-danger me-1"></i> تفاصيل
                    النواقص</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>الرصيد الحالي</th>
                            <th>حد الطلب (الحد الأدنى)</th>
                            <th>العجز (الفرق)</th>
                            <th>تكلفة الوحدة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shortages as $item)
                            <tr class="{{ $item->quantity <= 0 ? 'table-danger' : '' }}">
                                <td class="fw-bold text-dark">{{ $item->name_ar }}</td>
                                <td class="fw-bold font-monospace fs-5">{{ $item->quantity }}</td>
                                <td class="fw-bold font-monospace">{{ $item->alert_quantity }}</td>
                                <td class="fw-bolder text-danger font-monospace fs-5">
                                    {{ $item->alert_quantity - $item->quantity }}</td>
                                <td class="font-monospace">{{ number_format($item->cost_price, 3) }} OMR</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted fw-bold">
                                    <i class="mdi mdi-check-decagram display-4 text-success d-block mb-3"></i>
                                    كل شيء تمام! لا توجد نواقص في المخزون حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
