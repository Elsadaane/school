@extends('backend.layouts.master')
@section('title', 'تفاصيل التحويل المخزني')
@section('title_page', 'تفاصيل التحويل')

@push('css')
    {{-- 🔥 استدعاء مكتبة Boxicons للرموز المميزة + SweetAlert 🔥 --}}
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #fff;
        }

        .info-table th {
            background-color: #f8f9fa;
            width: 35%;
            color: #555;
            font-weight: 600;
            padding: 12px;
        }

        .info-table td {
            padding: 12px;
        }

        /* 🌟 شريط المراحل المتطور (Stepper) 🌟 */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            position: relative;
        }

        .stepper-item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            z-index: 2;
        }

        .stepper-item::before {
            content: "";
            position: absolute;
            top: 25px;
            right: -50%;
            width: 100%;
            height: 4px;
            background-color: #e0e0e0;
            z-index: -1;
            transition: 0.4s;
        }

        .stepper-item:first-child::before {
            display: none;
        }

        .stepper-item.completed::before {
            background-color: #2ecc71;
        }

        .stepper-item.active::before {
            background-color: #3498db;
        }

        .stepper-item .step-counter {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #e0e0e0;
            font-size: 22px;
            color: #ccc;
            transition: 0.4s;
            box-shadow: 0 0 0 5px #fff;
        }

        .stepper-item.completed .step-counter {
            background-color: #2ecc71;
            border-color: #2ecc71;
            color: #fff;
        }

        .stepper-item.completed .step-name {
            color: #2ecc71;
            font-weight: bold;
        }

        .stepper-item.active .step-counter {
            background-color: #3498db;
            border-color: #3498db;
            color: #fff;
            box-shadow: 0 0 0 5px #fff, 0 0 15px rgba(52, 152, 219, 0.5);
            transform: scale(1.1);
        }

        .stepper-item.active .step-name {
            color: #3498db;
            font-weight: 900;
        }

        .stepper-item .step-name {
            margin-top: 15px;
            font-size: 14px;
            font-weight: 600;
            color: #999;
            transition: 0.3s;
        }

        /* 🌟 تنسيق الأزرار العلوية 🌟 */
        .btn-header {
            padding: 8px 18px;
            font-weight: bold;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            color: white;
            transition: 0.3s;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-ship {
            background-color: #3498db;
        }

        .btn-receive {
            background-color: #2ecc71;
        }

        .btn-cancel {
            background-color: #e67e22;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-back {
            background-color: #7f8c8d;
        }

        .btn-print {
            background-color: #34495e;
        }

        .btn-header:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .info-table th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .info-table td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .stepper-item .step-counter {
            background-color: #323a46;
            box-shadow: 0 0 0 5px #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .stepper-item::before {
            background-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.02);
        }

        body[data-layout-color="dark"] .table-hover tbody tr:hover {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .table thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table td {
            border-color: #424e5a;
            color: #adb5bd;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        @php
            $status = $transfer->status;
        @endphp

        {{-- 1. شريط مراحل التحويل (Stepper) --}}
        <div class="card card-premium shadow-sm border-0 mb-4 border-top border-primary border-4">
            <div class="card-body py-4 px-2 px-md-5">
                @if ($status == 'cancelled')
                    <div class="alert alert-danger text-center m-0 fw-bold fs-5 border-0 shadow-sm rounded-3">
                        <i class="bx bx-x-circle fs-4 align-middle me-1"></i> تم إلغاء طلب التحويل هذا وتم إرجاع الأرصدة
                        للفرع المُرسل!
                    </div>
                @else
                    <div class="stepper-wrapper">
                        <div class="stepper-item {{ $status == 'pending' ? 'active' : 'completed' }}">
                            <div class="step-counter"><i class="bx {{ $status == 'pending' ? 'bx-file' : 'bx-check' }}"></i>
                            </div>
                            <div class="step-name">طلب تحويل</div>
                        </div>
                        <div
                            class="stepper-item {{ $status == 'shipped' ? 'active' : (in_array($status, ['completed', 'received']) ? 'completed' : '') }}">
                            <div class="step-counter">
                                <i
                                    class="bx {{ $status == 'shipped' ? 'bx-truck bx-flashing' : (in_array($status, ['completed', 'received']) ? 'bx-check' : 'bx-time') }}"></i>
                            </div>
                            <div class="step-name">تم الشحن / بالطريق</div>
                        </div>
                        <div class="stepper-item {{ in_array($status, ['completed', 'received']) ? 'completed' : '' }}">
                            <div class="step-counter">
                                <i
                                    class="bx {{ in_array($status, ['completed', 'received']) ? 'bx-check-double' : 'bx-package' }}"></i>
                            </div>
                            <div class="step-name">تم الاستلام بنجاح</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. رأس الصفحة والأزرار --}}
        <div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-3">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bx bx-receipt text-primary me-1 fs-4 align-middle"></i> تفاصيل إذن النقل: <span
                    class="text-primary font-monospace">{{ $transfer->transfer_number }}</span>
            </h5>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.stock-transfers.index') }}" class="btn-header btn-back shadow-sm">
                    <i class="bx bx-share bx-flip-horizontal"></i> رجوع للقائمة
                </a>

                <a href="{{ route('admin.stock-transfers.print', $transfer->id) }}" target="_blank"
                    class="btn-header btn-print shadow-sm">
                    <i class="bx bx-printer"></i> طباعة إذن النقل
                </a>

                {{-- 🔥 حماية أزرار المعالجة בצلاحية التعديل 🔥 --}}
                @can('edit_stock_transfers')
                    {{-- أزرار مرحلة "قيد الانتظار" --}}
                    @if ($status == 'pending')
                        <button type="button" class="btn-header btn-ship shadow-sm" onclick="confirmAction('ship')">
                            <i class="bx bx-truck"></i> شحن التحويل
                        </button>
                    @endif

                    {{-- أزرار مرحلة "جاري الشحن" --}}
                    @if ($status == 'shipped')
                        <button type="button" class="btn-header btn-receive shadow-sm" onclick="confirmAction('receive')">
                            <i class="bx bx-package"></i> استلام البضاعة بالفرع
                        </button>
                    @endif

                    {{-- زر الإلغاء (يظهر في حالة pending أو shipped) --}}
                    @if (in_array($status, ['pending', 'shipped']))
                        <button type="button" class="btn-header btn-cancel shadow-sm" onclick="confirmAction('cancel')">
                            <i class="bx bx-x-circle"></i> إلغاء وإرجاع
                        </button>
                    @endif
                @endcan

                {{-- 🔥 حماية زر الحذف בצلاحية الحذف 🔥 --}}
                @can('delete_stock_transfers')
                    @if ($status !== 'completed')
                        <button type="button" class="btn-header btn-delete shadow-sm" onclick="confirmAction('delete')">
                            <i class="bx bx-trash"></i> حذف السجل
                        </button>
                    @endif
                @endcan
            </div>
        </div>

        <div class="row g-4">
            {{-- 3. معلومات التحويل (اليمين) --}}
            <div class="col-xl-5 col-lg-6">
                <div class="card card-premium shadow-sm border-0 h-100 rounded-4 overflow-hidden">
                    <div class="card-header bg-light py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-dark"><i
                                class="bx bx-info-circle text-primary me-1 font-18 align-middle"></i> معلومات وبيانات
                            التحويل</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0 info-table align-middle">
                            <tbody>
                                <tr>
                                    <th>رقم التحويل</th>
                                    <td class="fw-bold text-primary font-monospace fs-6">{{ $transfer->transfer_number }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>من فرع (المُرسِل)</th>
                                    <td class="fw-bold text-danger">{{ $transfer->fromBranch?->name_ar ?? 'فرع محذوف' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>إلى فرع (المُستقبِل)</th>
                                    <td class="fw-bold text-success">{{ $transfer->toBranch?->name_ar ?? 'فرع محذوف' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>تاريخ التحويل</th>
                                    <td class="font-monospace text-dark fw-bold">
                                        {{ \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>حالة الطلب</th>
                                    <td>
                                        @if ($status == 'completed')
                                            <span class="badge bg-success px-3 py-2 font-12 rounded-pill shadow-sm"><i
                                                    class="bx bx-check-double"></i> مكتمل ومُستلم</span>
                                        @elseif($status == 'shipped')
                                            <span
                                                class="badge bg-info text-white px-3 py-2 font-12 rounded-pill shadow-sm"><i
                                                    class="bx bxs-truck"></i> مشحون / بالطريق</span>
                                        @elseif($status == 'pending')
                                            <span
                                                class="badge bg-warning text-dark px-3 py-2 font-12 rounded-pill shadow-sm"><i
                                                    class="bx bx-time"></i> قيد الانتظار</span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2 font-12 rounded-pill shadow-sm"><i
                                                    class="bx bx-x-circle"></i> ملغي ومُسترجع</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>أنشأ بواسطة</th>
                                    <td><span
                                            class="fw-bold text-dark">{{ $transfer->user?->name ?? 'مدير النظام' }}</span>
                                    </td>
                                </tr>
                                @if ($transfer->notes)
                                    <tr>
                                        <th>ملاحظات الأمانة</th>
                                        <td class="text-muted font-13 fw-bold">{{ $transfer->notes }}</td>
                                    </tr>
                                @endif

                                @if ($transfer->shipped_at)
                                    <tr>
                                        <th>تم الشحن بواسطة</th>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $transfer->shippedBy?->name ?? 'غير معروف' }}
                                            </div>
                                            <small class="text-muted font-monospace"><i class="bx bx-time"></i>
                                                {{ \Carbon\Carbon::parse($transfer->shipped_at)->format('Y-m-d h:i A') }}</small>
                                        </td>
                                    </tr>
                                @endif

                                @if ($transfer->received_at)
                                    <tr>
                                        <th>تم الاستلام بواسطة</th>
                                        <td>
                                            <div class="fw-bold text-success">
                                                {{ $transfer->receivedBy?->name ?? 'غير معروف' }}</div>
                                            <small class="text-muted font-monospace"><i class="bx bx-time"></i>
                                                {{ \Carbon\Carbon::parse($transfer->received_at)->format('Y-m-d h:i A') }}</small>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 4. عناصر التحويل (اليسار) --}}
            <div class="col-xl-7 col-lg-6">
                <div class="card card-premium shadow-sm border-0 h-100 rounded-4 overflow-hidden">
                    <div class="card-header bg-light py-3 border-bottom text-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bx bx-box text-warning me-1 font-18 align-middle"></i>
                            تفاصيل العناصر والأصناف المحولة</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-striped table-hover text-center align-middle mb-0"
                                style="white-space: nowrap;">
                                <thead class="bg-light text-muted sticky-top">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th class="text-start ps-3">المنتج / الصنف</th>
                                        <th>كود المنتج (SKU)</th>
                                        <th>الكمية المحولة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transfer->items as $index => $item)
                                        <tr>
                                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                            <td class="text-start ps-3 fw-bold text-dark text-wrap"
                                                style="max-width: 200px;">
                                                {{ $item->product?->name_ar ?? ($item->product?->name_en ?? 'صنف غير معروف') }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-light text-secondary border px-3 py-1 font-monospace shadow-sm">{{ $item->product?->sku ?? '---' }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-primary px-3 py-2 font-15 rounded-pill font-monospace shadow-sm">{{ number_format($item->quantity) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted py-5 fw-bold bg-white text-center">
                                                <i class="bx bx-package fs-1 d-block mb-2 opacity-50"></i>
                                                لا توجد منتجات مسجلة في قائمة هذا التحويل!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top text-start py-3 rounded-bottom-4">
                        <span class="fw-bold text-muted"><i class="mdi mdi-sigma me-1"></i> إجمالي الأصناف المحولة:
                            <span
                                class="text-primary fs-5 ms-1 font-monospace fw-bolder">{{ number_format($transfer->items->sum('quantity')) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        function confirmAction(type) {
            let title, text, url, icon, color;
            let payload = {
                _token: "{{ csrf_token() }}"
            };

            // تحديد بيانات المودال حسب نوع الأكشن
            if (type === 'ship') {
                title = 'تأكيد شحن التحويل؟';
                text = 'سيتم تغيير الحالة إلى "جاري الشحن" وخصم الكميات مؤقتاً.';
                url = "{{ route('admin.stock-transfers.ship', $transfer->id) }}";
                icon = 'info';
                color = '#3498db';
            } else if (type === 'receive') {
                title = 'تأكيد استلام البضاعة؟';
                text = 'سيتم إضافة الأصناف إلى مخزن الفرع المُستقبِل وإغلاق العملية بنجاح.';
                url = "{{ route('admin.stock-transfers.receive', $transfer->id) }}";
                icon = 'success';
                color = '#2ecc71';
            } else if (type === 'cancel') {
                title = 'تأكيد إلغاء التحويل؟';
                text = 'سيتم إلغاء العملية وإرجاع الأصناف إلى عهدة الفرع المُرسِل.';
                url = "{{ route('admin.stock-transfers.cancel', $transfer->id) }}";
                icon = 'warning';
                color = '#e67e22';
            } else if (type === 'delete') {
                title = 'تأكيد الحذف النهائي؟';
                text = 'سيتم مسح هذا السجل من النظام تماماً واسترجاع الأرصدة للفرع الأصلي. لا يمكن التراجع!';
                url = "{{ route('admin.stock-transfers.destroy', $transfer->id) }}";
                icon = 'error';
                color = '#e74c3c';
                payload._method = 'DELETE'; // ضروري لارافيل يفهم إنه طلب حذف
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: color,
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'نعم، قم بالتنفيذ',
                cancelButtonText: 'تراجع وإلغاء',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: url,
                        type: "POST",
                        data: payload
                    }).done(function(response) {
                        return response;
                    }).fail(function(xhr) {
                        let errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                            'حدث خطأ غير متوقع بالخادم';
                        Swal.showValidationMessage(`خطأ: ${errorMsg}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value && result.value.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت العملية بنجاح!',
                        text: result.value.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // لو اليوزر حذف السجل، نرجعه لصفحة الجدول، غير كدة نحدث نفس الصفحة
                        if (type === 'delete') {
                            window.location.href = "{{ route('admin.stock-transfers.index') }}";
                        } else {
                            window.location.reload();
                        }
                    });
                }
            });
        }
    </script>
@endpush
