@extends('backend.layouts.master')
@section('title', 'إضافة خط سير جديد')
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
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="page-title font-bold mb-1 text-dark">
                    <i class="bx bx-plus-circle text-info fs-3 align-middle me-1"></i> إضافة خط سير جديد
                </h4>
                <p class="text-muted mb-0">إدخال بيانات خط السير وتحديد التسعيرة</p>
            </div>
        </div>

        @if ($errors->any())
            <div
                class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4 rounded-3 fw-bold">
                <i class="bx bx-error-circle me-1 align-middle fs-5"></i>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card card-premium shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.transport-routes.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الفرع <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">اختر الفرع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">اسم خط السير <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                placeholder="مثال: السيب - المعبيلة الجنوبية" value="{{ old('name') }}" required>
                            <small class="text-muted">اكتب اسم الخط بشكل واضح (من - إلى)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bx bx-sort-alt-2 text-success me-1"></i>
                                تسعيرة الذهاب والعودة (شهرياً) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.001" name="monthly_fare_both"
                                    class="form-control text-center fw-bold" placeholder="0.000"
                                    value="{{ old('monthly_fare_both', 0) }}" required>
                                <span class="input-group-text bg-success text-white">OMR</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bx bx-right-arrow-alt text-warning me-1"></i>
                                تسعيرة الاتجاه الواحد (شهرياً) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.001" name="monthly_fare_single"
                                    class="form-control text-center fw-bold" placeholder="0.000"
                                    value="{{ old('monthly_fare_single', 0) }}" required>
                                <span class="input-group-text bg-warning text-white">OMR</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">ملاحظات (اختياري)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية عن هذا الخط...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">
                            <i class="bx bx-check me-1"></i> حفظ خط السير
                        </button>
                        <a href="{{ route('admin.transport-routes.index') }}" class="btn btn-light rounded-pill px-4">
                            <i class="bx bx-x me-1"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // 🔥 التحقق من صحة التسعيرة قبل الحفظ 🔥
            $('form').on('submit', function(e) {
                let fareBoth = parseFloat($('input[name="monthly_fare_both"]').val()) || 0;
                let fareSingle = parseFloat($('input[name="monthly_fare_single"]').val()) || 0;

                // إذا كان سعر الذهاب والعودة أقل من أو يساوي سعر الاتجاه الواحد
                if (fareBoth > 0 && fareSingle > 0 && fareBoth <= fareSingle) {
                    e.preventDefault(); // إيقاف إرسال النموذج
                    Swal.fire({
                        icon: 'error',
                        title: 'تسعيرة غير منطقية! 🤔',
                        text: 'لا يمكن أن يكون سعر (الذهاب والعودة) مساوياً أو أقل من سعر (الاتجاه الواحد). يرجى المراجعة.',
                        confirmButtonText: 'تعديل السعر',
                        confirmButtonColor: '#ef4444'
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
