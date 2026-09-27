@extends('backend.layouts.master')
@section('title', 'إدارة خطوط السير')
@section('title_page', 'إدارة الحركة والنقل')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
            border-top: 5px solid #0ea5e9;
        }

        .route-badge {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            display: inline-block;
        }

        .fare-box {
            background: #f0f9ff;
            border: 2px solid #0ea5e9;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
        }

        .fare-amount {
            font-size: 24px;
            font-weight: 900;
            color: #0284c7;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title font-bold mb-1 text-dark">
                        <i class="bx bx-map text-info fs-3 align-middle me-1"></i> إدارة خطوط السير
                    </h4>
                    <p class="text-muted mb-0">إدارة خطوط السير وتحديد التسعيرة للطلاب</p>
                </div>
                <a href="{{ route('admin.transport-routes.create') }}"
                    class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bx bx-plus-circle me-1"></i> إضافة خط سير جديد
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4 rounded-3 fw-bold">
                <i class="bx bx-check-circle me-1 align-middle fs-5"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div
                class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4 rounded-3 fw-bold">
                <i class="bx bx-error-circle me-1 align-middle fs-5"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card card-premium shadow-sm">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>اسم خط السير</th>
                                <th>الفرع</th>
                                <th>تسعيرة الذهاب والعودة</th>
                                <th>تسعيرة الاتجاه الواحد</th>
                                <th>عدد الحافلات</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($routes as $index => $route)
                                <tr>
                                    <td class="fw-bold">{{ $routes->firstItem() + $index }}</td>
                                    <td>
                                        <span class="route-badge">
                                            <i class="bx bx-map-pin me-1"></i> {{ $route->name }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">{{ $route->branch->name_ar ?? '---' }}</td>
                                    <td>
                                        <div class="fare-box">
                                            <div class="fare-amount">{{ number_format($route->monthly_fare_both, 3) }}</div>
                                            <small class="text-muted">OMR / شهرياً</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fare-box" style="background: #fef3c7; border-color: #f59e0b;">
                                            <div class="fare-amount" style="color: #d97706;">
                                                {{ number_format($route->monthly_fare_single, 3) }}</div>
                                            <small class="text-muted">OMR / شهرياً</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2">
                                            <i class="bx bx-bus me-1"></i> {{ $route->buses()->count() }} حافلة
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.transport-routes.edit', $route->id) }}"
                                                class="btn btn-sm btn-warning rounded-pill px-3">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.transport-routes.destroy', $route->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذا الخط؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bx bx-map fs-1 text-muted mb-3 d-block"></i>
                                        <h5 class="text-muted">لا توجد خطوط سير مسجلة</h5>
                                        <p class="text-muted">قم بإضافة أول خط سير للبدء</p>
                                        <a href="{{ route('admin.transport-routes.create') }}"
                                            class="btn btn-primary rounded-pill px-4">
                                            <i class="bx bx-plus-circle me-1"></i> إضافة خط سير
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($routes->hasPages())
                    <div class="mt-4">
                        {{ $routes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
