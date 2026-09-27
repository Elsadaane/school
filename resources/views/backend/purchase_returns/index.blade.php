@extends('backend.layouts.master')
@section('title', 'سجل مرتجعات المشتريات')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

    <style>
        .filter-card {
            background-color: #fffaf0;
            border: 1px dashed #f59e0b;
            border-radius: 12px;
        }

        .table-custom {
            white-space: nowrap;
            /* منع تكسير الأسماء والأرقام في الشاشات الصغيرة */
        }

        .table-erp th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
        }

        .table-erp td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
            padding: 14px;
        }

        .return-badge {
            font-family: monospace;
            font-weight: bold;
            font-size: 13px;
            padding: 5px 10px;
            border-radius: 6px;
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .invoice-link {
            font-family: monospace;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
            padding: 3px 8px;
            background: #dbeafe;
            border-radius: 4px;
            transition: 0.2s;
        }

        .invoice-link:hover {
            background: #bfdbfe;
            color: #1d4ed8;
        }

        .amount-text {
            font-family: 'Courier New', monospace;
            font-weight: 900;
            font-size: 15px;
            color: #ef4444;
        }

        /* Dark Mode */
        body[data-layout-color="dark"] .filter-card {
            background-color: rgba(245, 158, 11, 0.05);
            border-color: #d97706;
        }

        body[data-layout-color="dark"] .table-erp th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .return-badge {
            background: rgba(220, 38, 38, 0.15);
            color: #ef4444;
            border-color: rgba(220, 38, 38, 0.3);
        }

        body[data-layout-color="dark"] .invoice-link {
            background: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
        }

        body[data-layout-color="dark"] .invoice-link:hover {
            background: rgba(37, 99, 235, 0.3);
            color: #93c5fd;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h4 class="page-title font-bold text-danger mb-0">
                <i class="mdi mdi-keyboard-return me-1 fs-4 align-middle"></i> سجل مرتجعات المشتريات
            </h4>

            <div class="d-flex gap-2 align-items-center flex-wrap">
                {{-- 🔥 فلتر البحث السريع 🔥 --}}
                <form action="{{ route('admin.purchase-returns.index') }}" method="GET" class="d-flex gap-2"
                    style="min-width: 250px;">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-warning text-warning border-end-0"><i
                                class="mdi mdi-magnify"></i></span>
                        <input type="text" name="search" class="form-control border-warning border-start-0 ps-0 fw-bold"
                            placeholder="رقم المرتجع، الفاتورة..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-warning text-dark fw-bold">بحث</button>
                    </div>
                </form>

                {{-- 🔥 حماية زر إنشاء المرتجع بصلاحية الإنشاء 🔥 --}}
                @can('create_purchase_returns')
                    <a href="{{ route('admin.purchase-returns.create') }}"
                        class="btn btn-danger fw-bold shadow-sm rounded-pill px-4">
                        <i class="mdi mdi-plus-circle-outline me-1 fs-5 align-middle"></i> إنشاء إشعار مرتجع جديد
                    </a>
                @endcan
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm fw-bold border-0 rounded-3"><i class="mdi mdi-check-circle me-1"></i>
            {{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm fw-bold border-0 rounded-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li><i class="mdi mdi-alert-circle-outline me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="border-top: 4px solid #ef4444 !important;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-erp table-custom table-hover text-center align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>رقم المرتجع</th>
                            <th>رقم الفاتورة الأصلية</th>
                            <th class="text-start">المورد / الشركة</th>
                            <th>تاريخ المرتجع</th>
                            <th>إجمالي قيمة المرتجع</th>

                            {{-- 🔥 إخفاء عمود الإجراءات لو مفيش صلاحية للعرض أو الطباعة 🔥 --}}
                            @can('view_purchase_returns')
                                <th>إجراءات</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $index => $return)
                            <tr>
                                <td class="text-muted fw-bold">{{ $returns->firstItem() + $index }}</td>

                                <td>
                                    <span class="return-badge shadow-sm">{{ $return->return_number }}</span>
                                </td>

                                <td>
                                    @if ($return->purchaseInvoice)
                                        <a href="{{ route('admin.purchase-invoices.show', $return->purchase_invoice_id) }}"
                                            class="invoice-link shadow-sm" title="عرض الفاتورة الأصلية">
                                            {{ $return->purchaseInvoice->invoice_number }}
                                            <i class="mdi mdi-open-in-new font-11 ms-1"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">فاتورة محذوفة</span>
                                    @endif
                                </td>

                                <td class="text-start fw-bold text-dark text-wrap" style="max-width: 200px;">
                                    <i class="mdi mdi-truck-delivery-outline text-muted me-1 fs-5 align-middle"></i>
                                    {{ $return->supplier->company_name ?? 'غير محدد' }}
                                </td>

                                <td dir="ltr" class="text-muted font-monospace fw-bold">
                                    {{ \Carbon\Carbon::parse($return->return_date)->format('Y-m-d') }}
                                </td>

                                <td dir="ltr">
                                    <span class="amount-text">{{ number_format($return->total_return_amount, 3) }}</span>
                                    <span class="text-muted fw-bold font-12">OMR</span>
                                </td>

                                @can('view_purchase_returns')
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            {{-- زر عرض تفاصيل المرتجع --}}
                                            <a href="{{ route('admin.purchase-returns.show', $return->id) }}"
                                                class="btn btn-sm btn-outline-info rounded-circle shadow-sm"
                                                title="عرض التفاصيل">
                                                <i class="mdi mdi-eye-outline font-18"></i>
                                            </a>

                                            {{-- زر طباعة المرتجع (إذا تم برمجته لاحقاً) --}}
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary rounded-circle shadow-sm"
                                                title="طباعة إشعار المرتجع" onclick="window.print()">
                                                <i class="mdi mdi-printer font-18"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user()->can('view_purchase_returns') ? 7 : 6 }}"
                                    class="text-center py-5">
                                    <div class="mb-3 mt-2">
                                        <i class="mdi mdi-file-undo-outline text-danger opacity-25"
                                            style="font-size: 60px;"></i>
                                    </div>
                                    <h5 class="text-dark fw-bold">لا توجد مرتجعات!</h5>
                                    <p class="text-muted font-14">لم يتم تسجيل أي مرتجعات مشتريات مطابقة للبحث حتى الآن.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 🔥 الحفاظ على الفلاتر في الـ Pagination 🔥 --}}
            @if ($returns->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $returns->appends(request()->query())->links() }}
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
            // تفعيل الـ Select2 إن تم إضافتها مستقبلاً في فلاتر إضافية
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                dir: "{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
            });
        });
    </script>
@endpush
