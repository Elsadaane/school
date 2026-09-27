@extends('backend.layouts.master')
@section('title', 'توقيع العقد رقم | ' . $contract->contract_number)

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .contract-paper {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            padding: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            font-family: 'Traditional Arabic', 'Tajawal', Tahoma, sans-serif;
            color: #0f172a;
            border-top: 15px solid #2563eb;
        }

        .contract-banner img {
            width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 8px;
        }

        .header-banner img {
            max-height: 140px;
            margin-bottom: 25px;
        }

        .footer-banner img {
            max-height: 100px;
            margin-top: 40px;
        }

        .contract-header {
            text-align: center;
            margin-bottom: 35px;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 20px;
        }

        .contract-header h2 {
            font-weight: 900;
            color: #1e293b;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .contract-header p {
            margin: 0;
            color: #475569;
            font-weight: bold;
            font-size: 15px;
        }

        /* 🆕 شارة حالة العقد */
        .contract-status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            margin-right: 8px;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .status-draft {
            background: #fef9c3;
            color: #9a3412;
            border: 1px solid #fde047;
        }

        .status-canceled {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .contract-section {
            margin-bottom: 30px;
        }

        .contract-title {
            font-weight: 900;
            color: #1d4ed8;
            font-size: 18px;
            margin-bottom: 15px;
            background: #eff6ff;
            padding: 8px 15px;
            border-right: 4px solid #2563eb;
            border-radius: 4px;
        }

        .info-table,
        .financial-table,
        .items-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }

        .info-table td,
        .financial-table th,
        .financial-table td,
        .items-table th,
        .items-table td {
            padding: 12px;
            border: 1px solid #cbd5e1;
            font-size: 15px;
            color: #1e293b;
        }

        .info-table td.label {
            font-weight: 800;
            background-color: #f8fafc;
            width: 22%;
            color: #475569;
        }

        .financial-table th,
        .items-table th {
            background-color: #f1f5f9;
            font-weight: 800;
            text-align: center;
        }

        .financial-table td,
        .items-table td {
            text-align: center;
            font-weight: bold;
        }

        /* 🆕 قسم المواصلات */
        .transport-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .transport-section h4 {
            color: #0369a1;
            font-size: 16px;
            font-weight: 800;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .transport-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #bae6fd;
            font-size: 14px;
        }

        .transport-item:last-child {
            border-bottom: none;
        }

        .transport-item .label {
            color: #475569;
            font-weight: 700;
        }

        .transport-item .value {
            color: #0f172a;
            font-weight: 800;
        }

        .terms-box {
            background: #fdfdfd;
            border: 1px dashed #cbd5e1;
            padding: 25px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 2;
            text-align: justify;
            color: #334155;
        }

        .signature-pad-wrapper {
            border: 2px dashed #94a3b8;
            border-radius: 12px;
            background: #f8fafc;
            padding: 20px;
            text-align: center;
            position: relative;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        canvas#signature-pad {
            border: 1px solid #cbd5e1;
            background: #fff;
            cursor: crosshair;
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
            height: 250px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            touch-action: none;
        }

        body[data-layout-color="dark"] {
            background-color: #2b313a;
        }

        body[data-layout-color="dark"] .contract-paper {
            border-color: #424e5a;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
            <h4 class="fw-bold text-dark mb-0"><i class="mdi mdi-draw-pen text-primary me-2 fs-3 align-middle"></i> البوابة
                الرقمية لتوقيع العقد</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.student-contracts.show', $contract->id) }}" target="_blank"
                    class="btn btn-outline-dark border shadow-sm rounded-pill fw-bold px-4">
                    <i class="mdi mdi-printer me-1 align-middle"></i> طباعة العقد
                </a>
                <a href="{{ route('admin.student-contracts.index') }}"
                    class="btn btn-light border shadow-sm rounded-pill fw-bold px-4">
                    <i class="mdi mdi-arrow-left me-1 align-middle"></i> العودة للسجل
                </a>
            </div>
        </div>

        <div class="contract-paper position-relative">

            @if (isset($globalSettings) && $globalSettings->contract_header)
                <div class="contract-banner header-banner text-center">
                    <img src="{{ asset('storage/' . $globalSettings->contract_header) }}" alt="Contract Header">
                </div>
            @endif

            <div class="contract-header">
                <h2>عقد تسجيل وخدمات طالب</h2>
                <p class="mb-1">تاريخ العقد: <span
                        class="font-monospace text-dark">{{ \Carbon\Carbon::parse($contract->contract_date)->format('Y/m/d') }}</span>
                </p>
                <p class="mb-0">
                    <span class="text-danger">رقم المرجع: <span
                            class="font-monospace">{{ $contract->contract_number }}</span></span>
                    {{-- 🆕 شارة حالة العقد --}}
                    @if ($contract->contract_status === 'active')
                        <span class="contract-status-badge status-active">نشط</span>
                    @elseif($contract->contract_status === 'draft')
                        <span class="contract-status-badge status-draft">مسودة</span>
                    @elseif($contract->contract_status === 'canceled')
                        <span class="contract-status-badge status-canceled">ملغي</span>
                    @endif
                </p>
            </div>

            <div class="contract-section">
                <div class="contract-title">أطراف التعاقد</div>
                <table class="info-table">
                    <tr>
                        <td class="label">الطرف الأول (المدرسة):</td>
                        <td class="fw-bold text-primary" colspan="3">
                            {{ $globalSettings->company_name_ar ?? 'إدارة المدارس' }}</td>
                    </tr>
                    <tr>
                        <td class="label">الطرف الثاني (ولي الأمر):</td>
                        <td class="fw-bold fs-6">{{ $contract->student?->parent?->name_ar ?? '---' }}</td>
                        <td class="label">الجنسية:</td>
                        <td>{{ $contract->student?->parent?->nationality_ar ?? '---' }}</td>
                    </tr>
                    <tr>
                        <td class="label">الهوية/الإقامة:</td>
                        <td class="font-monospace fw-bold">{{ $contract->student?->parent?->national_id ?? '---' }}</td>
                        <td class="label">رقم الهاتف:</td>
                        <td dir="ltr" class="font-monospace fw-bold">{{ $contract->student?->parent?->phone ?? '---' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">اسم الطالب/ـة:</td>
                        <td colspan="3" class="fw-bold text-primary fs-5">{{ $contract->student?->first_name ?? '' }}
                            {{ $contract->student?->tribe ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="label">العام الدراسي:</td>
                        <td class="fw-bold">{{ $contract->academicYear?->name ?? '---' }}</td>
                        <td class="label">الـصـف (الفرع):</td>
                        {{-- ✅ تم الإصلاح: grade من الطالب وليس من العقد --}}
                        <td class="fw-bold">{{ $contract->student?->grade?->name_ar ?? '---' }}
                            ({{ $contract->branch?->name_ar ?? '-' }})</td>
                    </tr>
                </table>
            </div>

            {{-- 🆕 قسم المواصلات --}}
            @if ($contract->transportRoute || $contract->morningBus || $contract->afternoonBus)
                <div class="contract-section">
                    <div class="transport-section">
                        <h4>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#0369a1"
                                viewBox="0 0 16 16">
                                <path
                                    d="M4 6.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-7z" />
                                <path
                                    d="M2 3a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 0 2 2 2 2 0 0 1-2 2H2a2 2 0 0 1-2-2 2 2 0 0 0 2-2V3zm10-1H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z" />
                            </svg>
                            خدمات النقل المدرسي
                        </h4>

                        @if ($contract->transportRoute)
                            <div class="transport-item">
                                <span class="label">خط السير:</span>
                                <span class="value">{{ $contract->transportRoute->name }}</span>
                            </div>
                        @endif

                        @if ($contract->morningBus)
                            <div class="transport-item">
                                <span class="label">باص الذهاب (الصباحي):</span>
                                <span class="value">{{ $contract->morningBus->bus_number }}</span>
                            </div>
                        @endif

                        @if ($contract->afternoonBus)
                            <div class="transport-item">
                                <span class="label">باص العودة (المسائي):</span>
                                <span class="value">{{ $contract->afternoonBus->bus_number }}</span>
                            </div>
                        @endif

                        @if ($contract->transportRoute)
                            <div class="transport-item">
                                <span class="label">أجرة الاشتراك الشهري:</span>
                                <span class="value" style="color: #0369a1;">
                                    @if ($contract->morningBus && $contract->afternoonBus)
                                        {{ number_format($contract->transportRoute->monthly_fare_both, 3) }} ر.ع (ذهاب
                                        وإياب)
                                    @elseif($contract->morningBus || $contract->afternoonBus)
                                        {{ number_format($contract->transportRoute->monthly_fare_single, 3) }} ر.ع (اتجاه
                                        واحد)
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="contract-section">
                <div class="contract-title">تفاصيل بنود الفاتورة:</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="50%">البند</th>
                            <th width="15%">سعر الوحدة</th>
                            <th width="10%">الكمية</th>
                            <th width="20%">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contract->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-start">
                                    @if ($item->product_id)
                                        {{ $item->product?->name_ar ?? 'بند دراسي' }}
                                    @else
                                        {{ $item->description ?? 'بند مخصص' }}
                                    @endif
                                </td>
                                <td class="font-monospace">{{ number_format($item->unit_price, 3) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="font-monospace fw-bold">{{ number_format($item->row_total, 3) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted py-3">لا توجد بنود مسجلة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="contract-section">
                <div class="contract-title">الرسوم الدراسية والأقساط:</div>
                <table class="financial-table">
                    <tr>
                        <th>الإجمالي الفرعي</th>
                        <th>الخصم الممنوح</th>
                        <th>إجمالي الضريبة</th>
                        @if ($contract->previous_debt > 0)
                            <th style="background-color: #fef2f2; color: #991b1b;">المديونية المُرحلة</th>
                        @endif
                        <th style="background-color: #e0f2fe; color: #0369a1;">الإجمالي الشامل (ر.ع)</th>
                    </tr>
                    <tr>
                        <td class="font-monospace fs-6">{{ number_format($contract->sub_total, 3) }}</td>
                        <td class="font-monospace fs-6 text-danger">{{ number_format($contract->discount, 3) }}</td>
                        <td class="font-monospace fs-6 text-warning">{{ number_format($contract->tax_amount, 3) }}</td>
                        @if ($contract->previous_debt > 0)
                            <td class="font-monospace fs-6 text-danger">{{ number_format($contract->previous_debt, 3) }}
                            </td>
                        @endif
                        <td class="fw-bold font-monospace" style="font-size: 20px; color: #0369a1;">
                            {{ number_format($contract->grand_total, 3) }}</td>
                    </tr>
                </table>

                <h6 class="fw-bold mt-4 mb-3 text-center text-decoration-underline text-dark">جدول استحقاق الأقساط المتفق
                    عليه</h6>
                <table class="financial-table">
                    <tr>
                        <th width="10%">م</th>
                        <th>تاريخ الاستحقاق</th>
                        <th>المبلغ المستحق (ر.ع)</th>
                        <th>الحالة</th>
                    </tr>
                    @forelse ($contract->installments as $index => $inst)
                        @php
                            $remaining = $inst->amount - $inst->paid_amount;
                        @endphp
                        <tr>
                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                            <td dir="ltr" class="font-monospace fw-bold text-dark">
                                {{ \Carbon\Carbon::parse($inst->due_date)->format('Y-m-d') }}</td>
                            <td class="font-monospace fw-bold fs-6">{{ number_format($inst->amount, 3) }}</td>
                            <td>
                                @if ($inst->status == 'paid' || $remaining <= 0)
                                    <span
                                        style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 12px;">مسدد</span>
                                @elseif ($inst->paid_amount > 0)
                                    <span
                                        style="background: #fef9c3; color: #9a3412; padding: 4px 8px; border-radius: 4px; font-size: 12px;">جزئي</span>
                                @else
                                    <span
                                        style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 12px;">مستحق</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted py-3">لا توجد أقساط مجدولة.</td>
                        </tr>
                    @endforelse
                </table>
            </div>

            <div class="contract-section">
                <div class="contract-title">شروط وأحكام العقد:</div>
                <div class="terms-box">
                    @if (isset($globalSettings) && !empty($globalSettings->student_contract_terms_ar))
                        {!! $globalSettings->student_contract_terms_ar !!}
                    @else
                        <p>1. يوثق هذا العقد العلاقة التربوية والإدارية والمالية بين الطرفين، بما يضمن وضوح الحقوق والواجبات
                            خلال فترة دراسة الطالب/ـة في المدرسة.</p>
                        <p>2. يلتزم الطرف الثاني بسداد الرسوم الدراسية في المواعيد المحددة بجدول الأقساط الموضح أعلاه.</p>
                        <p>3. في حال تأخر السداد عن الموعد المحدد، يحق للمدرسة تطبيق غرامة تأخير حسب اللائحة المالية
                            المعتمدة.</p>
                        <p>4. هذا العقد ساري المفعول طوال العام الدراسي {{ $contract->academicYear?->name ?? '---' }}.</p>
                    @endif
                </div>
            </div>

            <hr class="my-5 border-secondary opacity-25">

            <div class="contract-section bg-light p-4 rounded-4 border shadow-sm">
                <h5 class="fw-bolder text-center mb-3 text-danger"><i class="mdi mdi-draw"></i> توقيع ولي الأمر (الطرف
                    الثاني)</h5>
                <div class="signature-pad-wrapper">
                    <p class="text-muted font-14 mb-3 fw-bold">أقر أنا الموقع أدناه باطلاعي على كافة الشروط المالية
                        والأكاديمية وموافقتي التامة عليها.</p>
                    <canvas id="signature-pad"></canvas>
                    <div class="mt-4 d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-danger px-4 rounded-pill fw-bold shadow-sm"
                            id="clear-signature">
                            <i class="mdi mdi-eraser me-1"></i> مسح وإعادة التوقيع
                        </button>
                    </div>
                </div>

                <form action="{{ route('admin.student-contracts.save-signature', $contract->id) }}" method="POST"
                    id="signatureForm" class="mt-4 text-center">
                    @csrf
                    <input type="hidden" name="signature_data" id="signature_data">
                    <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill shadow fw-bold"
                        id="save-signature">
                        <i class="mdi mdi-check-decagram me-2 fs-4 align-middle"></i> حفظ التوقيع واعتماد العقد نهائياً
                    </button>
                </form>
            </div>

            @if (isset($globalSettings) && $globalSettings->contract_footer)
                <div class="contract-banner footer-banner text-center mt-5">
                    <img src="{{ asset('storage/' . $globalSettings->contract_footer) }}" alt="Contract Footer">
                </div>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var canvas = document.getElementById('signature-pad');

            function resizeCanvas() {
                var signatureData = signaturePad ? signaturePad.toData() : null;

                var ratio = Math.max(window.devicePixelRatio || 1, 1);
                setTimeout(function() {
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);

                    if (signatureData && signatureData.length > 0) {
                        signaturePad.fromData(signatureData);
                    }
                }, 100);
            }

            var signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 1)',
                penColor: 'rgb(0, 0, 128)',
                minWidth: 1.5,
                maxWidth: 3
            });

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            document.getElementById('clear-signature').addEventListener('click', function() {
                signaturePad.clear();
            });

            document.getElementById('signatureForm').addEventListener('submit', function(e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يرجى من ولي الأمر إدخال التوقيع في المربع المخصص أولاً قبل الحفظ!',
                        confirmButtonColor: '#3b82f6',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    var dataURL = signaturePad.toDataURL('image/png');
                    document.getElementById('signature_data').value = dataURL;

                    let btn = document.getElementById('save-signature');
                    btn.disabled = true;
                    btn.innerHTML =
                        '<i class="mdi mdi-spin mdi-loading me-2 fs-4 align-middle"></i> جاري رفع التوقيع واعتماد العقد...';
                }
            });
        });
    </script>
@endpush
