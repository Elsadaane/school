@extends('backend.layouts.master')
@section('title', 'التحويل بين الحسابات المالية')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="page-title font-bold text-dark mb-1">
                <i class="mdi mdi-swap-horizontal-bold text-primary me-1"></i> إدارة التحويلات المالية
            </h4>
            <p class="text-muted font-13 mb-0">نقل الأرصدة ومتابعة السجل التاريخي لحركة الخزائن والبنوك.</p>
        </div>
    </div>

    {{-- رسائل النجاح والأخطاء --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm fw-bold rounded-3 border-0">
            <i class="mdi mdi-check-circle fs-5 align-middle me-1"></i> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm fw-bold rounded-3 border-0">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li><i class="mdi mdi-alert-circle me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 🔥 1. فورم التحويل الجديد (محمي بالصلاحية كلياً) 🔥 --}}
    @can('transfer_financial_accounts')
        <div class="card shadow-sm border-0 mb-4 border-top border-primary border-4 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="font-bold text-primary mb-0"><i class="mdi mdi-bank-transfer"></i> إجراء تحويل مالي جديد</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.account-transfers.store') }}" method="POST" id="transferForm">
                    @csrf
                    <div class="row g-3 align-items-end">

                        {{-- الحساب المصدر --}}
                        <div class="col-md-3">
                            <label class="form-label text-danger font-bold"><i class="mdi mdi-bank-minus"></i> من حساب
                                (المصدر)</label>
                            <select class="form-select select2 border-danger shadow-sm" name="from_account_id" id="from_account"
                                required>
                                <option value="" selected disabled>اختر الحساب...</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}" data-balance="{{ $account->balance }}">
                                        {{ $account->name_ar }} (متاح: {{ number_format($account->balance, 3) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- سهم إرشادي للشاشات الكبيرة --}}
                        <div class="col-md-1 text-center d-none d-md-block">
                            <i class="mdi mdi-arrow-left-bold font-24 text-muted opacity-50"></i>
                        </div>

                        {{-- الحساب الوجهة --}}
                        <div class="col-md-3">
                            <label class="form-label text-success font-bold"><i class="mdi mdi-bank-plus"></i> إلى حساب
                                (الوجهة)</label>
                            <select class="form-select select2 border-success shadow-sm" name="to_account_id" id="to_account"
                                required>
                                <option value="" selected disabled>اختر الحساب...</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- المبلغ المالي --}}
                        <div class="col-md-2">
                            <label class="form-label text-dark font-bold"><i class="mdi mdi-cash-multiple"></i> المبلغ
                                (OMR)</label>
                            <input type="number" step="0.001" min="0.001"
                                class="form-control font-bold font-monospace text-center shadow-sm text-primary" name="amount"
                                id="transfer_amount" required placeholder="0.000">
                        </div>

                        {{-- الملاحظات --}}
                        <div class="col-md-3">
                            <label class="form-label text-muted font-bold">ملاحظات (اختياري)</label>
                            <input type="text" class="form-control shadow-sm" name="notes" placeholder="سبب التحويل...">
                        </div>

                        <div class="col-12 mt-4 text-end border-top pt-3">
                            <button type="submit" class="btn btn-primary px-5 font-bold shadow-sm rounded-pill"
                                id="submitTransferBtn">
                                <i class="mdi mdi-swap-horizontal"></i> تنفيذ واعتماد التحويل
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    {{-- 🔥 2. جدول تاريخ التحويلات مع الفلاتر (Dashboard) 🔥 --}}
    <div class="card shadow-sm border-0 mt-2 rounded-4 overflow-hidden">
        <div class="card-header bg-light border-bottom py-3">
            <form action="{{ route('admin.account-transfers.history') }}" method="GET">
                <div class="row justify-content-between align-items-center g-3">
                    <div class="col-lg-4">
                        <h6 class="mb-0 font-bold text-dark"><i
                                class="mdi mdi-history text-success me-1 font-20 align-middle"></i> سجل التحويلات السابقة
                        </h6>
                    </div>
                    <div class="col-lg-8">
                        <div class="d-flex justify-content-lg-end align-items-end gap-2 flex-wrap">
                            <div style="width: 140px;">
                                <label class="form-label text-muted font-12 mb-1">من تاريخ</label>
                                <input type="date" class="form-control form-control-sm font-bold text-dark"
                                    name="start_date" value="{{ request('start_date', date('Y-m-01')) }}">
                            </div>
                            <div style="width: 140px;">
                                <label class="form-label text-muted font-12 mb-1">إلى تاريخ</label>
                                <input type="date" class="form-control form-control-sm font-bold text-dark"
                                    name="end_date" value="{{ request('end_date', date('Y-m-t')) }}">
                            </div>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm" title="بحث وتصفية"><i
                                        class="mdi mdi-magnify font-16"></i></button>
                                <a href="{{ route('admin.account-transfers.history') }}"
                                    class="btn btn-sm btn-warning text-dark px-3 shadow-sm" title="إعادة ضبط"><i
                                        class="mdi mdi-refresh font-16"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0 text-center align-middle">
                    <thead class="bg-white text-muted">
                        <tr>
                            <th style="width: 50px; border-bottom: 2px solid #e2e8f0;">#</th>
                            <th style="border-bottom: 2px solid #e2e8f0;"><i
                                    class="mdi mdi-arrow-left-circle-outline text-danger fs-5 align-middle me-1"></i> سُحب
                                من حساب</th>
                            <th style="border-bottom: 2px solid #e2e8f0;"><i
                                    class="mdi mdi-arrow-right-circle-outline text-success fs-5 align-middle me-1"></i>
                                أُودع في حساب</th>
                            <th style="border-bottom: 2px solid #e2e8f0;"><i
                                    class="mdi mdi-cash-multiple fs-5 align-middle text-primary me-1"></i> المبلغ (OMR)
                            </th>
                            <th style="border-bottom: 2px solid #e2e8f0;"><i
                                    class="mdi mdi-calendar-clock fs-5 align-middle text-muted me-1"></i> التاريخ</th>
                            <th style="border-bottom: 2px solid #e2e8f0;"><i
                                    class="mdi mdi-note-text-outline fs-5 align-middle text-info me-1"></i> ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $index => $transfer)
                            <tr>
                                {{-- استخدام firstItem لضمان صحة الترقيم مع الـ Pagination --}}
                                <td class="text-muted fw-bold">{{ $transfers->firstItem() + $index }}</td>

                                <td>
                                    <span
                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 font-13 rounded-pill">
                                        {{ $transfer->financialAccount->name_ar ?? 'حساب محذوف' }}
                                    </span>
                                </td>

                                <td>
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 font-13 rounded-pill">
                                        {{ $transfer->payable->name_ar ?? 'حساب محذوف' }}
                                    </span>
                                </td>

                                <td class="font-bold text-dark font-monospace fs-5" dir="ltr">
                                    {{ number_format(abs($transfer->amount), 3) }}
                                </td>

                                <td dir="ltr">
                                    <div class="font-13 text-dark fw-bold">{{ $transfer->payment_date->format('Y-m-d') }}
                                    </div>
                                    <div class="font-11 text-muted">{{ $transfer->payment_date->format('h:i A') }}</div>
                                </td>

                                <td class="text-start">
                                    @if ($transfer->notes)
                                        <span class="text-muted font-13 text-wrap d-inline-block"
                                            style="max-width: 200px;">
                                            {{ $transfer->notes }}
                                        </span>
                                    @else
                                        <span class="text-muted font-12">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="mdi mdi-database-off text-muted opacity-25"
                                            style="font-size: 60px;"></i>
                                    </div>
                                    <h5 class="text-muted fw-bold">لا يوجد تاريخ تحويلات في هذه الفترة</h5>
                                    <p class="text-muted font-13 mb-0">قم بتغيير تواريخ البحث لرؤية نتائج أقدم.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🔥 Pagination مع الحفاظ على معاملات البحث 🔥 --}}
        @if ($transfers->hasPages())
            <div class="card-footer bg-white border-0 pt-3 pb-3 rounded-bottom-4">
                {{ $transfers->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // تهيئة مكتبة Select2 لتظهر بشكل احترافي
            $('.select2').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? 'rtl' : 'ltr'
            });

            // ==========================================
            // 🔥 المنطق الذكي لفلترة الحسابات والأرصدة 🔥
            // ==========================================
            $('#from_account').on('change', function() {
                let selectedFromId = $(this).val();
                let maxBalance = $(this).find(':selected').data('balance');

                // تحديد الحد الأقصى لمبلغ التحويل
                if (maxBalance !== undefined) {
                    $('#transfer_amount').attr('max', maxBalance);
                }

                // لو المحاسب كان مختار الوجهة هي نفس المصدر، قم بتفريغ الخانة
                if ($('#to_account').val() === selectedFromId) {
                    $('#to_account').val('').trigger('change');
                }

                // إخفاء الحساب المصدر من قائمة الحسابات الوجهة المتاحة
                $('#to_account option').each(function() {
                    if ($(this).val() !== "") {
                        if ($(this).val() === selectedFromId) {
                            $(this).attr('disabled', 'disabled');
                        } else {
                            $(this).removeAttr('disabled');
                        }
                    }
                });

                // إعادة تهيئة Select2 لتحديث حالة الخيارات المخفية
                $('#to_account').select2("destroy").select2({
                    theme: "bootstrap-5",
                    dir: isRtl ? 'rtl' : 'ltr'
                });
            });

            // ==========================================
            // 🔥 التحقق من الأخطاء قبل الإرسال (Client-Side) 🔥
            // ==========================================
            $('#transferForm').on('submit', function(e) {
                let from = $('#from_account').val();
                let to = $('#to_account').val();
                let amount = parseFloat($('#transfer_amount').val());
                let maxBalance = parseFloat($('#from_account').find(':selected').data('balance'));

                if (!from || !to) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        text: 'يرجى تحديد الحساب المصدر والحساب الوجهة قبل المتابعة.',
                        confirmButtonColor: '#f59e0b'
                    });
                    return false;
                }

                if (from === to) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        text: 'إجراء غير منطقي! لا يمكن التحويل لنفس الحساب المالي.',
                        confirmButtonColor: '#f59e0b'
                    });
                    return false;
                }

                if (amount > maxBalance) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'رصيد غير كافٍ',
                        text: 'المبلغ المطلوب تحويله يتجاوز الرصيد المتاح حالياً في الحساب المصدر!',
                        confirmButtonColor: '#ef4444'
                    });
                    return false;
                }

                // حماية من إرسال الطلب مرتين
                $('#submitTransferBtn').prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> جاري التنفيذ...');
            });
        });
    </script>
@endpush
