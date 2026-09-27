@extends('backend.layouts.master')
@section('title', 'التحويلات بين الفروع')
@section('title_page', 'سجل التحويلات بين المدارس')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    {{-- 🔥 استدعاء مكتبة Select2 والثيم المتوافق مع Bootstrap 5 🔥 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
        }

        /* ستايل الأزرار الموحد */
        .action-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: white !important;
            border: none;
            transition: 0.2s ease-in-out;
            text-decoration: none;
        }

        .btn-view {
            background-color: #6366f1;
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.3);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .badge-status {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .table-erp {
            white-space: nowrap;
            /* تأمين الجداول من التكسير */
            margin-bottom: 0;
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 14px;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding: 12px;
            text-align: center;
        }

        /* 🌟 تنسيقات Select2 🌟 */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 40px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            font-weight: 600;
        }

        /* 🌟 Dark Mode Adjustments 🌟 */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border: 1px solid #424e5a;
        }

        body[data-layout-color="dark"] .table-erp thead th {
            background-color: #3b4652 !important;
            color: #ced4da !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .table-erp tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody tr:hover td {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        /* 🖨️ إعدادات الطباعة النظيفة 🖨️ */
        @media print {
            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print,
            .left-side-menu,
            .navbar-custom,
            .page-title-box,
            .footer,
            .action-col {
                display: none !important;
            }

            .card-premium {
                box-shadow: none !important;
                border: 1px solid #ddd;
                break-inside: avoid;
            }

            .table-responsive {
                overflow: visible !important;
                max-height: none !important;
            }

            .table-erp th,
            .table-erp td {
                border: 1px solid #ccc !important;
                white-space: normal !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3 d-print-none">
            <div>
                <h4 class="page-title font-bold mb-1 text-dark"><i
                        class="bx bx-transfer-alt text-primary me-1 fs-4 align-middle"></i> إدارة التحويلات المخزنية</h4>
                <p class="text-muted mb-0 font-14">متابعة الأرصدة والتحويلات اللوجستية بين المدارس والفروع.</p>
            </div>

            {{-- 🔥 حماية زر إضافة تحويل جديد بصلاحية الإنشاء 🔥 --}}
            @can('create_stock_transfers')
                <div>
                    <a href="{{ route('admin.stock-transfers.create') }}"
                        class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                        <i class="bx bxs-plus-square me-1 align-middle fs-5"></i> إضافة تحويل جديد
                    </a>
                </div>
            @endcan
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 card-premium border-top border-primary border-4">

                {{-- قسم الفلاتر والبحث --}}
                <div class="card-header bg-white py-4 border-bottom-0 rounded-top-4 d-print-none">
                    <form action="{{ route('admin.stock-transfers.index') }}" method="GET" class="prevent-double-submit">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label font-13 fw-bold text-muted">بحث برقم التحويل</label>
                                <input type="text" name="search" class="form-control shadow-sm fw-bold font-monospace"
                                    placeholder="TRF-..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-13 fw-bold text-muted">الفرع المُرسل / المُستقبل</label>
                                <select name="branch_id" class="form-select select2-filter shadow-sm">
                                    <option value="">-- كل الفروع --</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-13 fw-bold text-muted">الحالة اللوجستية</label>
                                <select name="status" class="form-select select2-filter shadow-sm">
                                    <option value="">-- كل الحالات --</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد
                                        الانتظار والمعالجة</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>جاري
                                        الشحن / بالطريق</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل
                                        ومُستلم</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي
                                        ومرفوض</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm btn-submit">
                                    <i class="bx bx-filter-alt me-1"></i> تصفية
                                </button>
                                <a href="{{ route('admin.stock-transfers.index') }}"
                                    class="btn btn-light px-3 shadow-sm border fw-bold text-dark" title="إعادة ضبط الفلاتر">
                                    <i class="bx bx-reset fs-5 align-middle"></i>
                                </a>
                                <button type="button" class="btn btn-dark px-3 shadow-sm" onclick="window.print()"
                                    title="طباعة السجل">
                                    <i class="mdi mdi-printer fs-5 align-middle"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                        <table class="table table-hover table-erp text-center align-middle mb-0">
                            <thead class="sticky-top" style="z-index: 10;">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>رقم أمر التحويل</th>
                                    <th>من فرع (المُرسِل)</th>
                                    <th>إلى فرع (المُستقبِل)</th>
                                    <th>تاريخ الإصدار</th>
                                    <th>حالة الطلب</th>

                                    {{-- 🔥 إخفاء عمود الإجراءات لو مفيش صلاحية للعرض 🔥 --}}
                                    @can('view_stock_transfers')
                                        <th style="width: 80px;" class="action-col d-print-none">إجراء</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transfers as $index => $transfer)
                                    <tr>
                                        {{-- حماية الترقيم مع الـ Pagination --}}
                                        <td class="text-muted fw-bold">
                                            {{ method_exists($transfers, 'firstItem') ? $transfers->firstItem() + $index : $index + 1 }}
                                        </td>

                                        <td class="fw-bolder text-primary font-monospace fs-6">
                                            {{ $transfer->transfer_number }}</td>

                                        <td>
                                            <div class="d-flex flex-column align-items-center">
                                                <span
                                                    class="fw-bold text-dark">{{ $transfer->fromBranch->name_ar ?? '---' }}</span>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 mt-1 px-2 font-11">مُرسل
                                                    <i class="mdi mdi-upload"></i></span>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column align-items-center">
                                                <span
                                                    class="fw-bold text-dark">{{ $transfer->toBranch->name_ar ?? '---' }}</span>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 mt-1 px-2 font-11">مُستقبل
                                                    <i class="mdi mdi-download"></i></span>
                                            </div>
                                        </td>

                                        <td class="font-monospace text-muted fw-bold">
                                            {{ \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') }}</td>

                                        <td>
                                            @if ($transfer->status == 'completed')
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success badge-status shadow-sm">
                                                    <i class="bx bx-check-double fs-6"></i> مكتمل ومستلم
                                                </span>
                                            @elseif($transfer->status == 'pending')
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-warning text-dark border border-warning badge-status shadow-sm">
                                                    <i class="bx bx-time fs-6"></i> قيد الانتظار
                                                </span>
                                            @elseif($transfer->status == 'shipped')
                                                <span
                                                    class="badge bg-info bg-opacity-10 text-info border border-info badge-status shadow-sm">
                                                    <i class="bx bxs-truck fs-6"></i> جاري الشحن
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger badge-status shadow-sm">
                                                    <i class="bx bx-x-circle fs-6"></i> ملغي ومرفوض
                                                </span>
                                            @endif
                                        </td>

                                        @can('view_stock_transfers')
                                            <td class="action-col d-print-none">
                                                <a href="{{ route('admin.stock-transfers.show', $transfer->id) }}"
                                                    class="action-btn btn-view" title="عرض التفاصيل ومعالجة الطلب">
                                                    <i class="bx bx-show fs-5"></i>
                                                </a>
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5 bg-light">
                                            <i class="bx bx-transfer fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                            <h5 class="fw-bold">لا توجد أوامر تحويل مخزنية!</h5>
                                            <p class="font-14 mb-0">لم يتم العثور على أي تحويلات مسجلة بين الفروع أو مطابقة
                                                للبحث.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 🔥 الحفاظ على الفلاتر في الترقيم 🔥 --}}
                @if (method_exists($transfers, 'hasPages') && $transfers->hasPages())
                    <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4 d-print-none">
                        {{ $transfers->appends(request()->query())->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل الـ Select2 للفلاتر العلوية مع دعم Bootstrap 5
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? "rtl" : "ltr"
            });

            // تأمين الفورم ضد الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="bx bx-loader bx-spin me-1 align-middle"></i> تصفية...');
            });
        });
    </script>
@endpush
