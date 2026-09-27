@extends('backend.layouts.master')
@section('title', 'سجل التسويات الجردية')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

    <style>
        body {
            background-color: #f4f7fc;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        /* تنسيق الحالات (Status) */
        .status-badge {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 12px;
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

        .adj-number {
            font-family: monospace;
            font-weight: bold;
            color: #1e293b;
            background: #e2e8f0;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .table-erp {
            white-space: nowrap;
            /* منع التكسير في الشاشات الصغيرة */
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

        .btn-action-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            border: none;
            transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-action-icon:hover {
            opacity: 0.8;
            transform: scale(1.1);
        }

        .btn-approve {
            background: #10b981;
            color: #fff;
        }

        .btn-reject {
            background: #ef4444;
            color: #fff;
        }

        .btn-view {
            background: #673ab7;
            color: #fff;
            text-decoration: none;
        }

        /* Dark Mode */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
        }

        body[data-layout-color="dark"] .table-erp thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody td {
            border-color: #424e5a;
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .adj-number {
            background: #424e5a;
            color: #ced4da;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select,
        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 الهيدر --}}
        <div class="card card-premium mb-4 border-top border-danger border-4">
            <div class="card-body d-flex justify-content-between align-items-center p-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger shadow-sm">
                        <i class="mdi mdi-clipboard-check-outline fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">إدارة التسويات الجردية</h4>
                        <p class="text-muted mb-0 font-13">مراجعة واعتماد فروقات الجرد الفعلي للمخازن.</p>
                    </div>
                </div>
                <div>
                    {{-- 🔥 حماية زرار الإضافة بصلاحية الإنشاء 🔥 --}}
                    @can('create_inventory_adjustments')
                        <a href="{{ route('admin.inventory-adjustments.create') }}"
                            class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                            <i class="mdi mdi-plus-circle-outline font-18 align-middle me-1"></i> إجراء جرد جديد
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- 🌟 الفلاتر والجدول --}}
        <div class="card card-premium overflow-hidden">

            {{-- قسم الفلاتر والبحث --}}
            <div class="card-header bg-white py-3 border-bottom rounded-top-4">
                <form action="{{ route('admin.inventory-adjustments.index') }}" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">رقم السند</label>
                            <input type="text" name="search" class="form-control shadow-sm font-monospace fw-bold"
                                placeholder="بحث بـ ADJ..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">الفرع (المستودع)</label>
                            <select name="branch_id" class="form-select select2-filter shadow-sm">
                                <option value="">كل الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">الحالة</label>
                            <select name="status" class="form-select shadow-sm fw-bold">
                                <option value="">الكل</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>بانتظار
                                    الاعتماد</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold rounded-pill"><i
                                    class="mdi mdi-filter-variant me-1"></i> تصفية</button>
                            <a href="{{ route('admin.inventory-adjustments.index') }}"
                                class="btn btn-light px-3 shadow-sm border rounded-circle" title="إعادة ضبط الفلاتر">
                                <i class="mdi mdi-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-erp table-hover table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم السند</th>
                                <th class="text-start ps-3">الفرع</th>
                                <th>تاريخ الجرد</th>
                                <th>بواسطة</th>
                                <th>الحالة</th>
                                <th>الملاحظات</th>
                                {{-- 🔥 إخفاء عمود الإجراءات لو مفيش صلاحية العرض أو الاعتماد 🔥 --}}
                                @canany(['view_inventory_adjustments', 'approve_inventory_adjustments'])
                                    <th>الإجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($adjustments as $index => $adj)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $adjustments->firstItem() + $index }}</td>
                                    <td><span class="adj-number">{{ $adj->adjustment_number }}</span></td>
                                    <td class="fw-bold text-start ps-3 text-dark">{{ $adj->branch->name_ar ?? '---' }}</td>
                                    <td dir="ltr" class="text-muted font-monospace fw-bold">
                                        {{ \Carbon\Carbon::parse($adj->adjustment_date)->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <i class="mdi mdi-account-circle text-primary font-18"></i>
                                            <span
                                                class="font-13 fw-bold text-dark">{{ $adj->user->name ?? 'Admin' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($adj->status == 'pending')
                                            <span class="status-badge status-pending shadow-sm"><i
                                                    class="mdi mdi-clock-outline me-1"></i> بانتظار الاعتماد</span>
                                        @elseif($adj->status == 'approved')
                                            <span class="status-badge status-approved shadow-sm"><i
                                                    class="mdi mdi-check-decagram me-1"></i> معتمد</span>
                                        @else
                                            <span class="status-badge status-rejected shadow-sm"><i
                                                    class="mdi mdi-close-circle-outline me-1"></i> مرفوض</span>
                                        @endif
                                    </td>
                                    <td class="text-muted font-12 text-wrap" style="max-width: 200px;">
                                        {{ Str::limit($adj->notes, 25) ?? '---' }}</td>

                                    @canany(['view_inventory_adjustments', 'approve_inventory_adjustments'])
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center">
                                                {{-- 🔥 زر التفاصيل محمي بصلاحية العرض 🔥 --}}
                                                @can('view_inventory_adjustments')
                                                    <a href="{{ route('admin.inventory-adjustments.show', $adj->id) }}"
                                                        class="btn-action-icon btn-view shadow-sm" title="عرض التفاصيل">
                                                        <i class="mdi mdi-eye-outline font-16"></i>
                                                    </a>
                                                @endcan

                                                {{-- أزرار الاعتماد والرفض (تظهر فقط لو السند معلق ومحمية بالصلاحية) --}}
                                                @if ($adj->status == 'pending')
                                                    @can('approve_inventory_adjustments')
                                                        <button type="button"
                                                            class="btn-action-icon btn-approve btn-action-confirm shadow-sm"
                                                            data-action="approve" data-id="{{ $adj->id }}"
                                                            title="اعتماد وتحديث المخزن">
                                                            <i class="mdi mdi-check-bold font-16"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn-action-icon btn-reject btn-action-confirm shadow-sm"
                                                            data-action="reject" data-id="{{ $adj->id }}"
                                                            title="رفض وإلغاء السند">
                                                            <i class="mdi mdi-cancel font-16"></i>
                                                        </button>

                                                        {{-- فورمات مخفية للارسال عبر JS مع كلاس منع الإرسال المزدوج --}}
                                                        <form id="form-approve-{{ $adj->id }}"
                                                            action="{{ route('admin.inventory-adjustments.approve', $adj->id) }}"
                                                            method="POST" class="prevent-double-submit" style="display:none;">@csrf
                                                        </form>
                                                        <form id="form-reject-{{ $adj->id }}"
                                                            action="{{ route('admin.inventory-adjustments.reject', $adj->id) }}"
                                                            method="POST" class="prevent-double-submit" style="display:none;">
                                                            @csrf</form>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->canany(['view_inventory_adjustments', 'approve_inventory_adjustments']) ? 8 : 7 }}"
                                        class="py-5 text-muted text-center fw-bold">
                                        <i class="mdi mdi-clipboard-off-outline fs-1 d-block mb-2 opacity-25"></i>
                                        لا توجد سجلات تسوية جردية حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($adjustments->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $adjustments->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تفعيل الـ Select2 للفلاتر بتنسيق Bootstrap 5
            $('.select2-filter').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? "rtl" : "ltr"
            });

            // حماية الفورم من الـ Double Submit لمنع تكرار الاعتماد
            $('.prevent-double-submit').on('submit', function() {
                $('.btn-action-confirm').prop('disabled', true);
            });

            // سكريبت الاعتماد والرفض
            $('.btn-action-confirm').click(function() {
                let action = $(this).data('action');
                let id = $(this).data('id');
                let btn = $(this);

                let config = {
                    title: action === 'approve' ? 'هل تود اعتماد الجرد؟' : 'هل تود رفض هذا السند؟',
                    text: action === 'approve' ?
                        "بمجرد الاعتماد، سيتم تحديث أرصدة المنتجات في المخازن أوتوماتيكياً حسب الجرد الفعلي!" :
                        "لن يتم إجراء أي تغيير على المخزون وسيتم إغلاق السند كمرفوض.",
                    icon: action === 'approve' ? 'success' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: action === 'approve' ? '#10b981' : '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: action === 'approve' ? 'نعم، اعتمد الآن' : 'نعم، ارفض السند',
                    cancelButtonText: 'تراجع'
                };

                Swal.fire(config).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true); // تعطيل الزر لتفادي التكرار
                        Swal.fire({
                            title: 'جاري المعالجة...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        $('#form-' + action + '-' + id).submit();
                    }
                });
            });

            // إشعارات النظام
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'مرفوض أمنياً',
                    text: "{{ session('error') }}"
                });
            @endif
        });
    </script>
@endpush
