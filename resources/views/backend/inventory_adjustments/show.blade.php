@extends('backend.layouts.master')
@section('title', 'تفاصيل سند الجرد')
@section('title_page', 'تفاصيل السند')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .section-title {
            font-size: 16px;
            font-weight: 800;
            color: #334155;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .info-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 15px;
            color: #0f172a;
            font-weight: bold;
        }

        .table-erp {
            white-space: nowrap;
            /* منع تكسير النصوص في الشاشات الصغيرة */
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 13px;
            padding: 15px;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
        }

        .diff-badge {
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
            font-weight: 900;
            padding: 6px 14px;
            border-radius: 8px;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        .diff-negative {
            background-color: #fef2f2;
            color: #ef4444;
            border: 1px solid #fca5a5;
        }

        .diff-positive {
            background-color: #ecfdf5;
            color: #10b981;
            border: 1px solid #6ee7b7;
        }

        .diff-zero {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 13px;
            border: 1px solid;
            display: inline-block;
        }

        .status-pending {
            background-color: #fffbeb;
            color: #d97706;
            border-color: #fde68a;
        }

        .status-approved {
            background-color: #ecfdf5;
            color: #10b981;
            border-color: #a7f3d0;
        }

        .status-rejected {
            background-color: #fef2f2;
            color: #ef4444;
            border-color: #fca5a5;
        }

        /* Dark Mode */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
        }

        body[data-layout-color="dark"] .section-title {
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .info-label {
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .info-value {
            color: #fff;
        }

        body[data-layout-color="dark"] .table-erp thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody td {
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .diff-negative {
            background-color: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
        }

        body[data-layout-color="dark"] .diff-positive {
            background-color: rgba(16, 185, 129, 0.1);
            border-color: #10b981;
        }

        body[data-layout-color="dark"] .diff-zero {
            background-color: #424e5a;
            color: #adb5bd;
            border-color: #64748b;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold mb-1">تفاصيل السند: <span class="text-danger font-monospace"
                        dir="ltr">#{{ $adjustment->adjustment_number }}</span></h4>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.inventory-adjustments.index') }}"
                    class="btn btn-light rounded-pill shadow-sm border font-bold px-4">
                    <i class="mdi mdi-arrow-right me-1"></i> رجوع للقائمة
                </a>
                <button onclick="window.print()" class="btn btn-secondary rounded-pill shadow-sm font-bold px-4">
                    <i class="mdi mdi-printer me-1"></i> طباعة السند
                </button>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-4">
            <ul class="mb-0 ps-3 fw-bold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- 🌟 الجزء الأيمن: بيانات السند 🌟 --}}
        <div class="col-lg-4 order-lg-1 order-2">
            <div class="card card-premium mb-4">
                <div class="card-body p-4">
                    <h5 class="section-title"><i class="mdi mdi-information-outline text-primary me-1"></i> معلومات السند
                        الأساسية</h5>

                    <div class="mb-3">
                        <div class="info-label">المستودع (الفرع)</div>
                        <div class="info-value text-primary">
                            <i class="mdi mdi-store-marker-outline me-1"></i>{{ $adjustment->branch->name_ar ?? '---' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="info-label">تاريخ الجرد الفعلي</div>
                        <div class="info-value font-monospace">
                            <i
                                class="mdi mdi-calendar-blank me-1 text-muted"></i>{{ \Carbon\Carbon::parse($adjustment->adjustment_date)->format('Y-m-d') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="info-label">أنشأ بواسطة (المُدخل)</div>
                        <div class="info-value">
                            <i
                                class="mdi mdi-account-circle-outline me-1 text-muted"></i>{{ $adjustment->user->name ?? 'Admin' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="info-label">حالة السند</div>
                        <div class="mt-1">
                            @if ($adjustment->status == 'pending')
                                <span class="status-badge status-pending shadow-sm"><i
                                        class="mdi mdi-clock-outline me-1"></i> بانتظار الاعتماد</span>
                            @elseif($adjustment->status == 'approved')
                                <span class="status-badge status-approved shadow-sm"><i
                                        class="mdi mdi-check-decagram me-1"></i> معتمد ومُنفذ</span>
                            @else
                                <span class="status-badge status-rejected shadow-sm"><i
                                        class="mdi mdi-close-circle-outline me-1"></i> مرفوض ومُغلق</span>
                            @endif
                        </div>
                    </div>

                    @if ($adjustment->approver)
                        <div class="mb-3 border-top pt-3">
                            <div class="info-label">تمت المراجعة والقرار بواسطة</div>
                            <div class="info-value text-success">
                                <i class="mdi mdi-account-check-outline me-1"></i>{{ $adjustment->approver->name }}
                            </div>
                        </div>
                    @endif

                    @if ($adjustment->notes)
                        <div class="mb-0 border-top pt-3">
                            <div class="info-label">ملاحظات مرفقة</div>
                            <div class="info-value text-muted font-13 text-wrap">{{ $adjustment->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🌟 لو السند لسه مسودة (Pending) نظهر زراير الاعتماد والرفض 🌟 --}}
            @if ($adjustment->status == 'pending')
                @can('approve_inventory_adjustments')
                    <div class="card card-premium bg-light border border-warning border-opacity-50 mb-4">
                        <div class="card-body p-4 text-center">
                            <h6 class="fw-bold mb-3 text-dark">قرار الإدارة (الاعتماد)</h6>
                            <p class="text-muted font-13 mb-4">بمجرد الاعتماد، سيتم تحديث أرصدة المنتجات فوراً في المستودع بناءً
                                على الفروقات.</p>

                            <div class="d-grid gap-3">
                                <button type="button"
                                    class="btn btn-success btn-lg fw-bold shadow-sm rounded-pill btn-action-confirm"
                                    data-action="approve" data-id="{{ $adjustment->id }}">
                                    <i class="mdi mdi-check-decagram-outline me-1 fs-5"></i> اعتماد وتحديث المخزن
                                </button>
                                <button type="button" class="btn btn-outline-danger fw-bold rounded-pill btn-action-confirm"
                                    data-action="reject" data-id="{{ $adjustment->id }}">
                                    <i class="mdi mdi-cancel me-1 fs-5"></i> رفض السند
                                </button>
                            </div>

                            {{-- فورمات مخفية للارسال عبر JS مع حماية الإرسال المزدوج --}}
                            <form id="form-approve-{{ $adjustment->id }}" class="prevent-double-submit"
                                action="{{ route('admin.inventory-adjustments.approve', $adjustment->id) }}" method="POST"
                                style="display:none;">@csrf</form>
                            <form id="form-reject-{{ $adjustment->id }}" class="prevent-double-submit"
                                action="{{ route('admin.inventory-adjustments.reject', $adjustment->id) }}" method="POST"
                                style="display:none;">@csrf</form>
                        </div>
                    </div>
                @endcan
            @endif
        </div>

        {{-- 🌟 الجزء الأيسر: بنود الجرد والفروقات 🌟 --}}
        <div class="col-lg-8 order-lg-2 order-1 mb-4">
            <div class="card card-premium h-100">
                <div class="card-body p-0">
                    <div class="p-4 border-bottom bg-light bg-opacity-50 rounded-top-4">
                        <h5 class="section-title mb-0 border-0 pb-0 text-dark">
                            <i class="mdi mdi-package-variant-closed text-warning me-2 fs-4 align-middle"></i> تفاصيل
                            الفروقات والبنود المجرودة
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-erp table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th class="text-start ps-4">المنتج / الصنف</th>
                                    <th>رصيد النظام</th>
                                    <th>الجرد الفعلي</th>
                                    <th>الفرق</th>
                                    <th class="text-start" style="width: 25%;">سبب العجز/الزيادة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($adjustment->items as $index => $item)
                                    <tr>
                                        <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                        <td class="text-start ps-4">
                                            <div class="fw-bold text-dark text-wrap" style="max-width: 250px;">
                                                {{ $item->product->name_ar ?? 'منتج محذوف' }}</div>
                                            @if ($item->product && $item->product->sku)
                                                <small class="text-muted font-monospace d-block mt-1"><i
                                                        class="mdi mdi-barcode me-1"></i>{{ $item->product->sku }}</small>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-secondary fs-5 font-monospace">
                                            {{ $item->system_quantity }}</td>
                                        <td class="fw-bold text-primary fs-5 font-monospace">{{ $item->actual_quantity }}
                                        </td>
                                        <td>
                                            @if ($item->difference < 0)
                                                <span
                                                    class="diff-badge diff-negative shadow-sm">{{ $item->difference }}</span>
                                            @elseif ($item->difference > 0)
                                                <span
                                                    class="diff-badge diff-positive shadow-sm">+{{ $item->difference }}</span>
                                            @else
                                                <span class="diff-badge diff-zero shadow-sm">0</span>
                                            @endif
                                        </td>
                                        <td class="text-muted font-13 text-wrap text-start" style="max-width: 200px;">
                                            {{ $item->reason ?? '---' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // حماية الفورم من الإرسال المتكرر
            $('.prevent-double-submit').on('submit', function() {
                $('.btn-action-confirm').prop('disabled', true);
            });

            // سكريبت الاعتماد والرفض مع SweetAlert2
            $('.btn-action-confirm').click(function(e) {
                e.preventDefault();
                let action = $(this).data('action');
                let id = $(this).data('id');
                let btn = $(this);

                let config = {
                    title: action === 'approve' ? 'هل تود اعتماد الجرد؟' : 'هل تود رفض هذا السند؟',
                    text: action === 'approve' ?
                        "سيتم تحديث أرصدة المنتجات في المخازن أوتوماتيكياً حسب الجرد الفعلي، ولا يمكن التراجع عن هذه الخطوة!" :
                        "لن يتم إجراء أي تغيير على المخزون وسيتم إغلاق السند كمرفوض.",
                    icon: action === 'approve' ? 'warning' : 'error',
                    showCancelButton: true,
                    confirmButtonColor: action === 'approve' ? '#10b981' : '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: action === 'approve' ? 'نعم، اعتمد السند' : 'نعم، ارفض السند',
                    cancelButtonText: 'تراجع'
                };

                Swal.fire(config).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true); // منع الضغط المتكرر
                        Swal.fire({
                            title: 'جاري المعالجة والترحيل...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        $('#form-' + action + '-' + id).submit();
                    }
                });
            });
        });
    </script>
@endpush
