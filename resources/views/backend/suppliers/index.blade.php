@extends('backend.layouts.master')
@section('title', 'إدارة الموردين والتجار')
@section('title_page', 'إدارة المشتريات والموردين')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .filter-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }

        .table-custom {
            white-space: nowrap;
            /* منع تكسير أسطر الحسابات */
            margin-bottom: 0;
        }

        .table-custom thead th {
            font-weight: 800;
            color: #475569;
            font-size: 13px;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px 12px;
            text-align: center;
            background-color: #f8fafc;
        }

        .table-custom tbody td {
            vertical-align: middle;
            padding: 14px 12px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }

        .col-balance {
            background-color: #fef2f2 !important;
            color: #991b1b !important;
            font-weight: 800;
            font-family: 'Courier New', Courier, monospace;
        }

        .col-opening {
            background-color: #eff6ff !important;
            color: #1e40af !important;
            font-weight: 800;
            font-family: 'Courier New', Courier, monospace;
        }

        /* ستايل الأزرار الموحد */
        .action-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            border: 1px solid transparent;
            color: #64748b;
            background: #fff;
            text-decoration: none;
        }

        .action-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .action-icon.view {
            background: #e0f2fe;
            color: #0284c7;
            border-color: #bae6fd;
        }

        .action-icon.view:hover {
            background: #0284c7;
            color: #fff;
        }

        .action-icon.edit {
            background: #fef3c7;
            color: #d97706;
            border-color: #fde68a;
        }

        .action-icon.edit:hover {
            background: #f59e0b;
            color: #fff;
        }

        .action-icon.delete {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fecaca;
        }

        .action-icon.delete:hover {
            background: #ef4444;
            color: #fff;
        }

        /* 🌟 Pagination Premium 🌟 */
        .pagination {
            margin-bottom: 0;
            justify-content: center;
            gap: 5px;
        }

        .page-item.active .page-link {
            background-color: #004b87;
            border-color: #004b87;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 75, 135, 0.2);
        }

        .page-item .page-link {
            color: #475569;
            font-weight: 700;
            border-radius: 8px !important;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            transition: all 0.2s;
        }

        .page-item .page-link:hover {
            background-color: #f1f5f9;
            color: #004b87;
            transform: translateY(-2px);
        }

        .page-item.disabled .page-link {
            background-color: #f8fafc;
            color: #cbd5e1;
            border-color: #e2e8f0;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .filter-card {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-custom thead th {
            background-color: #3b4652 !important;
            color: #ced4da !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .table-custom tbody td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .table-custom tbody tr:hover td {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .form-control {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .col-balance {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #fca5a5 !important;
        }

        body[data-layout-color="dark"] .col-opening {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #93c5fd !important;
        }

        body[data-layout-color="dark"] .action-icon {
            background: #3b4652;
            border-color: #424e5a;
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .bg-white {
            background-color: #323a46 !important;
        }
    </style>
@endpush

@section('content')
    {{-- 🌟 ترويسة الصفحة 🌟 --}}
    <div class="row mb-3 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold text-dark mb-1"><i
                        class="mdi mdi-truck-delivery text-primary me-1 fs-3 align-middle"></i> إدارة الموردين والتجار</h4>
                <p class="text-muted mb-0 font-14">متابعة حسابات الشركات الموردة، رصد الأرصدة الدائنة، وتصنيف المديونيات.
                </p>
            </div>

            {{-- 🔥 حماية زرار الإضافة بصلاحية الإنشاء 🔥 --}}
            @can('create_suppliers')
                <div>
                    <a href="{{ route('admin.suppliers.create') }}"
                        class="btn btn-primary fw-bold shadow-sm rounded-pill px-4 py-2"
                        style="background-color: #004b87; border:none;">
                        <i class="mdi mdi-plus-circle-outline me-1 align-middle fs-5"></i> إضافة مورد جديد
                    </a>
                </div>
            @endcan
        </div>
    </div>

    {{-- 🌟 كارت الفلترة والبحث 🌟 --}}
    <div class="card filter-card mb-4 border-0 border-top border-primary border-4 shadow-sm"
        style="border-top-color: #004b87 !important;">
        <div class="card-body p-4">
            <form action="{{ route('admin.suppliers.index') }}" method="GET" class="prevent-double-submit">
                <div class="row g-3 align-items-end">
                    <div class="col-md-9">
                        <label class="form-label font-13 fw-bold text-muted mb-2"><i
                                class="mdi mdi-magnify text-primary"></i> محرك البحث السريع</label>
                        <input type="text" name="search" class="form-control shadow-sm fw-bold py-2"
                            placeholder="ابحث باسم الشركة، اسم المسؤول، رقم الهاتف، أو الرقم الضريبي..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-dark shadow-sm w-100 fw-bold py-2 btn-submit">
                            <i class="mdi mdi-filter-variant me-1"></i> تصفية النتائج
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- 🌟 جدول الموردين 🌟 --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                <table class="table table-custom table-hover text-center align-middle mb-0">
                    <thead class="sticky-top" style="z-index: 5;">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th class="text-start ps-4">الشركة / التاجر المورد</th>
                            <th>المسؤول (المندوب)</th>
                            <th>هاتف الاتصال</th>
                            <th>الرقم الضريبي</th>
                            <th class="col-opening">الرصيد الافتتاحي</th>
                            <th class="col-balance">الرصيد الدائن (مستحق له)</th>
                            <th>الحالة</th>

                            {{-- 🔥 إخفاء عمود الإجراءات لو مفيش أي صلاحية 🔥 --}}
                            @canany(['view_suppliers', 'edit_suppliers', 'delete_suppliers'])
                                <th style="width: 160px;">خيارات الإدارة</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $index => $supplier)
                            <tr>
                                <td class="text-muted fw-bold">
                                    {{ method_exists($suppliers, 'firstItem') ? $suppliers->firstItem() + $index : $index + 1 }}
                                </td>
                                <td class="text-start ps-4 fw-bold text-dark">
                                    <i class="mdi mdi-domain text-muted me-1"></i> {{ $supplier->company_name }}
                                </td>
                                <td class="fw-semibold text-secondary">{{ $supplier->contact_person ?? '---' }}</td>
                                <td dir="ltr" class="font-monospace fw-bold">{{ $supplier->phone ?? '---' }}</td>
                                <td class="font-monospace text-muted">{{ $supplier->tax_number ?? '---' }}</td>
                                <td class="col-opening font-monospace fs-6">
                                    {{ number_format($supplier->opening_balance, 3) }} OMR</td>
                                <td class="col-balance font-monospace fs-6">
                                    {{ number_format($supplier->current_balance, 3) }} OMR</td>
                                <td>
                                    @if ($supplier->is_active)
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 shadow-sm font-11">نشط
                                            ومفعل</span>
                                    @else
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 shadow-sm font-11">موقوف
                                            مؤقتاً</span>
                                    @endif
                                </td>

                                @canany(['view_suppliers', 'edit_suppliers', 'delete_suppliers'])
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            @can('view_suppliers')
                                                <a href="{{ route('admin.suppliers.show', $supplier->id) }}"
                                                    class="action-icon view shadow-sm" data-bs-toggle="tooltip"
                                                    title="كشف الحساب والتقييم المالي">
                                                    <i class="mdi mdi-eye-outline fs-5"></i>
                                                </a>
                                            @endcan

                                            @can('edit_suppliers')
                                                <a href="{{ route('admin.suppliers.edit', $supplier->id) }}"
                                                    class="action-icon edit shadow-sm" data-bs-toggle="tooltip"
                                                    title="تعديل بيانات المورد">
                                                    <i class="mdi mdi-pencil-outline fs-5"></i>
                                                </a>
                                            @endcan

                                            @can('delete_suppliers')
                                                <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST"
                                                    class="d-inline-block delete-supplier-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="action-icon delete shadow-sm btn-delete-supplier"
                                                        data-bs-toggle="tooltip" title="إيقاف المورد أو حذفه">
                                                        <i class="mdi mdi-trash-can-outline fs-5"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 bg-white text-muted fw-bold">
                                    <div class="mb-3">
                                        <i class="mdi mdi-account-off-outline text-secondary opacity-50"
                                            style="font-size: 60px;"></i>
                                    </div>
                                    <h5 class="fw-bold">لا يوجد موردين أو تجار مسجلين حالياً!</h5>
                                    <p class="font-13 mb-0">لم يتم العثور على نتائج تطابق معايير الاستعلام المحددة.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🔥 الحفاظ على الفلاتر عند تفعيل روابط التصفح بستايل Premium 🔥 --}}
        @if (method_exists($suppliers, 'hasPages') && $suppliers->hasPages())
            <div
                class="card-footer bg-white border-top border-light pt-4 pb-4 rounded-bottom-4 shadow-sm d-flex justify-content-center">
                {{ $suppliers->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // تفعيل الـ Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // منع الإرسال المزدوج لمحرك البحث
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1 align-middle"></i> جاري البحث...');
            });

            // SweetAlert لتأكيد التعطيل أو الحذف الإداري
            $('.btn-delete-supplier').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد تغيير حالة المورد؟',
                    text: "سيتم تغيير حالة المورد في النظام؛ يرجى الملاحظة أن هذا الإجراء قد يمنع إصدار فواتير مشتريات جديدة لهذا الحساب حتى إعادة التفعيل.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، قم بالتعديل',
                    cancelButtonText: 'إلغاء الأمر',
                    borderRadius: '12px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
