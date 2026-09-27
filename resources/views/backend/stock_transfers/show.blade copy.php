@extends('backend.layouts.master')
@section('title', 'تفاصيل التحويل بين الفروع')
@section('title_page', 'تفاصيل التحويل بين الفروع')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
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

        /* شريط المراحل المتطور (Stepper) */
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

        /* تنسيق الأزرار العلوية */
        .btn-header {
            padding: 8px 18px;
            font-weight: bold;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            color: white;
            transition: 0.3s;
            cursor: pointer;
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

        /* برتقالي للإلغاء */
        .btn-delete {
            background-color: #e74c3c;
        }

        /* أحمر للحذف النهائي */
        .btn-back {
            background-color: #7f8c8d;
            text-decoration: none;
        }

        .btn-print {
            background-color: #34495e;
            text-decoration: none;
        }

        .btn-header:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">

            @php
                $status = $transfer->status;
            @endphp

            {{-- 1. شريط مراحل التحويل (Stepper) --}}
            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-body py-4 px-5">
                    @if ($status == 'cancelled')
                        <div class="alert alert-danger text-center m-0 fw-bold fs-5 rounded-4">
                            <i class="bx bx-x-circle fs-4 align-middle"></i> تم إلغاء طلب التحويل هذا وتم استرجاع العُهد
                            للفرع المُرسِل
                        </div>
                    @else
                        <div class="stepper-wrapper">
                            <div class="stepper-item {{ $status == 'pending' ? 'active' : 'completed' }}">
                                <div class="step-counter"><i
                                        class="bx {{ $status == 'pending' ? 'bx-file' : 'bx-check' }}"></i></div>
                                <div class="step-name">طلب تحويل</div>
                            </div>
                            <div
                                class="stepper-item {{ $status == 'shipped' ? 'active' : (in_array($status, ['completed', 'received']) ? 'completed' : '') }}">
                                <div class="step-counter">
                                    <i
                                        class="bx {{ $status == 'shipped' ? 'bx-truck bx-flashing' : (in_array($status, ['completed', 'received']) ? 'bx-check' : 'bx-time') }}"></i>
                                </div>
                                <div class="step-name">جاري الشحن</div>
                            </div>
                            <div class="stepper-item {{ in_array($status, ['completed', 'received']) ? 'completed' : '' }}">
                                <div class="step-counter"><i
                                        class="bx {{ in_array($status, ['completed', 'received']) ? 'bx-check-double' : 'bx-package' }}"></i>
                                </div>
                                <div class="step-name">تم الاستلام بالفرع</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. رأس الصفحة والأزرار --}}
            <div class="d-flex justify-content-between align-items-center mb-4 mt-2 flex-wrap gap-2">
                <h5 class="fw-bold mb-0">تفاصيل التحويل : <span class="text-primary"
                        dir="ltr">{{ $transfer->transfer_number }}</span>
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.stock-transfers.index') }}" class="btn-header btn-back shadow-sm">
                        <i class="bx bx-share bx-flip-horizontal"></i> رجوع
                    </a>

                    <a href="{{ route('admin.stock-transfers.print', $transfer->id) }}" target="_blank"
                        class="btn-header btn-print shadow-sm">
                        <i class="bx bx-printer"></i> طباعة إذن النقل
                    </a>

                    {{-- أزرار مرحلة "قيد الانتظار" --}}
                    @if ($status == 'pending')
                        <button type="button" class="btn-header btn-ship shadow-sm" onclick="confirmAction('ship')">
                            <i class="bx bx-truck"></i> شحن التحويل
                        </button>
                    @endif

                    {{-- أزرار مرحلة "جاري الشحن" --}}
                    @if ($status == 'shipped')
                        <button type="button" class="btn-header btn-receive shadow-sm" onclick="confirmAction('receive')">
                            <i class="bx bx-package"></i> استلام البضاعة في الفرع
                        </button>
                    @endif

                    {{-- زر الإلغاء (يظهر في حالة pending أو shipped) --}}
                    @if (in_array($status, ['pending', 'shipped']))
                        <button type="button" class="btn-header btn-cancel shadow-sm" onclick="confirmAction('cancel')">
                            <i class="bx bx-x-circle"></i> إلغاء التحويل
                        </button>
                    @endif

                    {{-- زر الحذف النهائي --}}
                    @if ($status !== 'completed')
                        <button type="button" class="btn-header btn-delete shadow-sm" onclick="confirmAction('delete')">
                            <i class="bx bx-trash"></i> حذف
                        </button>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                {{-- 3. معلومات التحويل (اليمين) --}}
                <div class="col-md-7">
                    <div class="card shadow-sm border-0 h-100 rounded-4">
                        <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                            <h6 class="mb-0 fw-bold"><i class="bx bx-info-circle text-primary"></i> معلومات التحويل</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0 info-table border-start-0 border-end-0">
                                <tbody>
                                    <tr>
                                        <th>رقم التحويل</th>
                                        <td class="fw-bold text-primary" dir="ltr">{{ $transfer->transfer_number }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>الفرع (المُرسِل)</th>
                                        <td class="fw-bold text-danger">{{ $transfer->fromBranch->name_ar ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>الفرع (المُستقبِل)</th>
                                        <td class="fw-bold text-success">{{ $transfer->toBranch->name_ar ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ التحويل</th>
                                        <td>{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ الاستلام المتوقع</th>
                                        <td>{{ $transfer->expected_receipt_date ? \Carbon\Carbon::parse($transfer->expected_receipt_date)->format('Y-m-d') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>الحالة</th>
                                        <td>
                                            @if (in_array($status, ['completed', 'received']))
                                                <span class="badge bg-success shadow-sm"
                                                    style="padding: 6px 12px; border-radius: 4px;">مكتمل بالفرع</span>
                                            @elseif($status == 'shipped')
                                                <span class="badge bg-info text-white shadow-sm"
                                                    style="padding: 6px 12px; border-radius: 4px;">جاري الشحن</span>
                                            @elseif($status == 'pending')
                                                <span class="badge bg-warning text-dark shadow-sm"
                                                    style="padding: 6px 12px; border-radius: 4px;">قيد الانتظار</span>
                                            @else
                                                <span class="badge bg-danger shadow-sm"
                                                    style="padding: 6px 12px; border-radius: 4px;">ملغي</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>أنشأ الطلب بواسطة</th>
                                        <td>{{ $transfer->user->name ?? 'Admin' }}</td>
                                    </tr>
                                    <tr>
                                        <th>ملاحظات</th>
                                        <td class="text-muted small">{{ $transfer->notes ?? '-' }}</td>
                                    </tr>

                                    @if ($transfer->shipped_at)
                                        <tr class="bg-light">
                                            <th>تم الشحن بواسطة</th>
                                            <td>
                                                <span
                                                    class="fw-bold">{{ $transfer->shippedBy->name ?? 'غير معروف' }}</span>
                                                <br><small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($transfer->shipped_at)->format('Y-m-d h:i A') }}</small>
                                            </td>
                                        </tr>
                                    @endif

                                    @if ($transfer->received_at)
                                        <tr class="bg-light">
                                            <th>تم الاستلام بالفرع بواسطة</th>
                                            <td>
                                                <span
                                                    class="fw-bold">{{ $transfer->receivedBy->name ?? 'غير معروف' }}</span>
                                                <br><small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($transfer->received_at)->format('Y-m-d h:i A') }}</small>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 4. عناصر التحويل (اليسار) --}}
                <div class="col-md-5">
                    <div class="card shadow-sm border-0 h-100 rounded-4">
                        <div class="card-header bg-white py-3 text-center border-bottom-0 rounded-top-4">
                            <h6 class="mb-0 fw-bold"><i class="bx bx-box text-primary"></i> الأصناف المُراد تحويلها</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-bordered text-center align-middle mb-0 border-start-0 border-end-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="text-start ps-3">اسم المنتج / الصنف</th>
                                            <th style="width: 30%">الكمية المحولة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transfer->items as $item)
                                            <tr>
                                                <td class="text-start fw-bold ps-3">
                                                    {{ $item->product->name_ar ?? $item->product->name_en }}
                                                    @if ($item->product->sku)
                                                        <br><small
                                                            class="text-muted font-monospace">{{ $item->product->sku }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-primary fw-bolder fs-5">{{ $item->quantity }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmAction(type) {
            let title, text, url, icon, color;
            let payload = {
                _token: "{{ csrf_token() }}"
            };

            // تحديد بيانات المودال حسب نوع الأكشن
            if (type === 'ship') {
                title = 'هل تريد شحن التحويل؟';
                text = 'سيتم تغيير الحالة إلى "جاري الشحن".';
                url = "{{ route('admin.stock-transfers.ship', $transfer->id) }}";
                icon = 'info';
                color = '#3498db';
            } else if (type === 'receive') {
                title = 'تأكيد استلام البضاعة بالفرع؟';
                text = 'سيتم إضافة الأصناف إلى مخزن الفرع المُستقبِل وإغلاق العملية بنجاح.';
                url = "{{ route('admin.stock-transfers.receive', $transfer->id) }}";
                icon = 'success';
                color = '#2ecc71';
            } else if (type === 'cancel') {
                title = 'إلغاء التحويل؟';
                text = 'سيتم إلغاء العملية وإرجاع الأصناف إلى الفرع المُرسِل.';
                url = "{{ route('admin.stock-transfers.cancel', $transfer->id) }}";
                icon = 'warning';
                color = '#e67e22';
            } else if (type === 'delete') {
                title = 'هل أنت متأكد من الحذف النهائي؟';
                text = 'سيتم مسح هذا السجل من النظام تماماً واسترجاع الأرصدة للفرع الأصلي.';
                url = "{{ route('admin.stock-transfers.destroy', $transfer->id) }}";
                icon = 'error';
                color = '#e74c3c';
                payload._method = 'DELETE'; // عشان لارافيل يفهم إنه طلب حذف
            }

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: color,
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'نعم، نفذ الآن',
                cancelButtonText: 'تراجع',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: url,
                        type: "POST", // لارافيل بيقرأ الـ _method من الـ Payload
                        data: payload
                    }).done(function(response) {
                        return response;
                    }).fail(function(xhr) {
                        let errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                            'حدث خطأ غير متوقع';
                        Swal.showValidationMessage(`خطأ: ${errorMsg}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت العملية!',
                        text: result.value.message,
                        timer: 1500,
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
