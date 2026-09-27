<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة السند المالي | {{ $payment->receipt_number }}</title>

    {{-- إضافة Bootstrap للتنسيق الأساسي --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');

        /* 🌟 تنسيقات العرض على الشاشة 🌟 */
        body {
            background-color: #e2e8f0;
            font-family: 'Cairo', 'Tahoma', 'Arial', sans-serif;
            color: #1e293b;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* حاوية ورقة A5 بالعرض */
        .voucher-container {
            width: 210mm;
            min-height: 148mm;
            /* مقاس A5 بالعرض */
            margin: 40px auto;
            background: #fff;
            padding: 12mm 15mm;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* 🎨 ألوان السند حسب النوع 🎨 */
        .voucher-receipt {
            border-top: 15px solid #10b981;
        }

        /* أخضر للقبض */
        .voucher-payment {
            border-top: 15px solid #ef4444;
        }

        /* أحمر للصرف */

        .text-receipt {
            color: #10b981;
        }

        .text-payment {
            color: #ef4444;
        }

        /* تنسيقات الصور (الهيدر والفوتر واللوجو) */
        .header-banner img {
            width: 100%;
            max-height: 90px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .footer-banner {
            margin-top: auto;
            text-align: center;
            padding-top: 15px;
        }

        .footer-banner img {
            width: 100%;
            max-height: 60px;
            object-fit: contain;
        }

        .school-logo {
            max-height: 70px;
            width: auto;
            object-fit: contain;
        }

        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 15px;
        }

        .voucher-title {
            text-align: center;
            border: 2px dashed;
            padding: 8px 30px;
            border-radius: 12px;
            font-weight: 900;
            font-size: 20px;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .amount-box {
            border: 2px solid #e2e8f0;
            padding: 8px 15px;
            border-radius: 8px;
            text-align: center;
            background: #f8fafc;
            display: inline-block;
            min-width: 150px;
        }

        .amount-number {
            font-size: 22px;
            font-weight: 900;
            font-family: 'Courier New', monospace;
        }

        .amount-currency {
            font-size: 14px;
            color: #64748b;
            font-weight: bold;
        }

        /* جدول التفاصيل */
        .details-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .details-table td {
            padding: 8px;
            border-bottom: 1px dotted #cbd5e1;
            font-size: 15px;
            vertical-align: top;
        }

        .details-label {
            font-weight: 800;
            color: #475569;
            width: 25%;
        }

        .details-value {
            font-weight: 700;
            color: #0f172a;
        }

        /* التوقيعات */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            text-align: center;
            font-weight: 700;
            color: #334155;
            font-size: 14px;
        }

        .signature-box {
            width: 30%;
            position: relative;
            min-height: 80px;
        }

        .signature-line {
            width: 80%;
            border-bottom: 1px solid #475569;
            margin: 40px auto 5px;
        }

        /* 🌟 ختم المدرسة والتوقيع 🌟 */
        .seal-img {
            max-height: 90px;
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0.85;
            mix-blend-mode: multiply;
            z-index: -1;
        }

        .signature-img {
            max-height: 60px;
            max-width: 100%;
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            mix-blend-mode: multiply;
        }

        .qr-code {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border: 1px solid #e2e8f0;
            padding: 3px;
            border-radius: 6px;
            background: #fff;
        }

        .btn-print {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            border-radius: 50px;
            padding: 12px 30px;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* 🖨️ تنسيقات الطباعة الصارمة (A5 Landscape) 🖨️ */
        @media print {
            @page {
                size: A5 landscape;
                margin: 0;
            }

            body {
                background-color: #fff;
                margin: 0;
                padding: 0;
            }

            .voucher-container {
                box-shadow: none;
                margin: 0;
                padding: 12mm 15mm;
                width: 210mm;
                height: 147mm;
                /* تقليل 1 ملم لمنع الصفحة البيضاء الإضافية */
                max-height: 148mm;
                border-radius: 0;
                border-top-width: 8px;
                /* تصغير الخط العلوي في الطباعة */
                page-break-after: avoid;
                overflow: hidden;
            }

            .btn-print,
            .back-link {
                display: none !important;
            }

            .signatures {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    {{-- تحديد المتغيرات بناءً على نوع السند --}}
    @php
        $isReceipt = $payment->payment_type == 'installment' || get_class($payment->payable) == 'App\Models\Guardian';
        $voucherClass = $isReceipt ? 'voucher-receipt' : 'voucher-payment';
        $textColor = $isReceipt ? 'text-receipt' : 'text-payment';
        $voucherTitle = $isReceipt ? 'سند قبض' : 'سند صرف';
        $voucherTypeEn = $isReceipt ? 'Receipt Voucher' : 'Payment Voucher';

        // جلب اسم الجهة (ولي أمر أو مورد)
        $entity = $payment->payable;
        $entityName = $isReceipt
            ? $entity->name_ar ?? ($entity->name ?? 'غير محدد')
            : $entity->company_name ?? 'غير محدد';

        // تسمية الحقل حسب النوع
        $partyLabel = $isReceipt ? 'استلمنا من المكرم/ة' : 'يصرف للمكرم/السادة';
        $partyLabelEn = $isReceipt ? 'Received From' : 'Pay To';
    @endphp

    {{-- زر الطباعة العائم والعودة --}}
    <button onclick="window.print()" class="btn btn-dark btn-print no-print">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
            class="bi bi-printer me-2" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
            <path
                d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z" />
        </svg>
        إصدار السند للطباعة
    </button>
    <div class="text-center mt-3 back-link no-print">
        <a href="{{ route('admin.payments.index') }}" class="text-decoration-none fw-bold text-secondary">العودة لسجل
            السندات</a>
    </div>

    {{-- الحاوية الرئيسية للسند (A5 Landscape) --}}
    <div class="voucher-container {{ $voucherClass }}">

        <div>
            {{-- 🔥 هيدر السند (البانر العلوي) من الإعدادات 🔥 --}}
            @if (isset($globalSettings) && $globalSettings->header_image)
                <div class="header-banner">
                    <img src="{{ asset('storage/' . $globalSettings->header_image) }}" alt="Voucher Header">
                </div>
            @endif

            {{-- ترويسة السند (النصوص واللوجو) --}}
            <div class="voucher-header">
                {{-- بيانات المدرسة --}}
                <div class="text-end" style="width: 30%;">
                    <h5 class="fw-bold mb-1 text-dark">{{ $globalSettings->company_name_ar ?? 'إدارة المدارس' }}</h5>
                    <p class="text-muted font-12 mb-0">{{ $globalSettings->address_ar ?? 'سلطنة عمان' }}</p>
                    <p class="text-muted font-12 mb-0">س.ت: <bdi
                            dir="ltr">{{ $globalSettings->commercial_record ?? '---' }}</bdi></p>
                </div>

                {{-- عنوان السند --}}
                <div class="text-center" style="width: 40%;">
                    <div class="voucher-title {{ $textColor }}" style="border-color: currentColor;">
                        {{ $voucherTitle }}<br>
                        <small
                            style="font-size: 13px; font-weight: 700; letter-spacing: 0; color: #64748b;">{{ $voucherTypeEn }}</small>
                    </div>
                </div>

                {{-- لوجو المدرسة --}}
                <div class="text-start" style="width: 30%;">
                    @if (isset($globalSettings) && $globalSettings->logo)
                        <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" class="school-logo">
                    @endif
                </div>
            </div>

            {{-- بيانات السند العلوية (رقم السند، التاريخ، والمبلغ، مع QR Code) --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    {{-- 🔥 تفعيل الـ QR Code للتحقق السريع 🔥 --}}
                    <div>
                        {{-- استخدام خدمة توليد QR Code مبسطة لغرض الطباعة (يمكن ربطها برابط التحقق في مشروعك) --}}
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('admin.payments.index', ['search' => $payment->receipt_number])) }}"
                            class="qr-code" alt="QR Code">
                    </div>
                    <div>
                        <p class="mb-1 fw-bold text-muted font-14">رقم السند (Voucher No) : <span
                                class="text-dark fs-5 font-monospace ms-1">{{ $payment->receipt_number }}</span></p>
                        <p class="mb-0 fw-bold text-muted font-14">التاريخ (Date) : <span
                                class="text-dark font-monospace ms-1">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</span>
                        </p>
                    </div>
                </div>

                {{-- مربع المبلغ --}}
                <div class="amount-box shadow-sm">
                    <span class="amount-currency {{ $textColor }}">OMR</span>
                    <span
                        class="amount-number {{ $textColor }} ms-1">{{ number_format($payment->amount, 3) }}</span>
                </div>
            </div>

            {{-- التفاصيل --}}
            <table class="details-table">
                <tr>
                    <td class="details-label">{{ $partyLabel }} <br><small
                            class="text-muted font-12">{{ $partyLabelEn }}</small></td>
                    <td class="details-value fs-6">{{ $entityName }}</td>
                </tr>
                <tr>
                    <td class="details-label">مبلغاً وقدره <br><small class="text-muted font-12">The Sum Of</small></td>
                    <td class="details-value">
                        <span class="font-monospace text-primary me-2">{{ number_format($payment->amount, 3) }}</span>
                        <span class="text-muted font-14">(فقط وقدره ريال عماني لا غير)</span>
                    </td>
                </tr>
                <tr>
                    <td class="details-label">وذلك عن <br><small class="text-muted font-12">Being For</small></td>
                    <td class="details-value">{{ $payment->transaction_details }}</td>
                </tr>
                <tr>
                    <td class="details-label">طريقة الدفع <br><small class="text-muted font-12">Payment Method</small>
                    </td>
                    <td class="details-value">
                        @if ($payment->check_number)
                            شيك رقم / تحويل بنكي ( <span
                                class="font-monospace text-danger">{{ $payment->check_number }}</span> )
                        @else
                            نقداً (Cash)
                        @endif
                        <span class="ms-3 text-muted font-13">على:
                            {{ $payment->financialAccount->name_ar ?? $payment->financialAccount->name }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div>
            {{-- التوقيعات --}}
            <div class="signatures">
                <div class="signature-box">
                    <span>المحاسب <br><small class="font-12 text-muted">Accountant</small></span>
                    <div class="signature-line"></div>
                    <small class="text-dark">{{ $payment->user->name ?? 'Admin' }}</small>
                </div>

                <div class="signature-box">
                    <span>الختم <br><small class="font-12 text-muted">Stamp</small></span>
                    <div class="signature-line" style="border-color: transparent;"></div>
                    {{-- 🔥 استدعاء ختم المدرسة مع تأثير الدمج الواقعي 🔥 --}}
                    @if (isset($globalSettings) && $globalSettings->seal)
                        <img src="{{ asset('storage/' . $globalSettings->seal) }}" alt="Stamp" class="seal-img">
                    @endif
                </div>

                <div class="signature-box">
                    <span>المستلم / المستفيد <br><small class="font-12 text-muted">Receiver</small></span>

                    {{-- 🔥 عرض توقيع العميل/ولي الأمر إذا وجد في الداتابيز 🔥 --}}
                    @if ($payment->signature_base64 || $payment->signature_path)
                        <img src="{{ $payment->signature_base64 ?: asset('storage/' . $payment->signature_path) }}"
                            class="signature-img" alt="توقيع المستلم">
                    @endif

                    <div class="signature-line"></div>
                </div>
            </div>

            {{-- 🔥 فوتر السند (البانر السفلي) من الإعدادات 🔥 --}}
            @if (isset($globalSettings) && $globalSettings->contract_footer)
                <div class="footer-banner">
                    <img src="{{ asset('storage/' . $globalSettings->contract_footer) }}" alt="Voucher Footer">
                </div>
            @else
                {{-- رسالة بديلة إذا لم يوجد فوتر --}}
                <div class="text-center mt-4 font-11 text-muted fw-bold border-top border-dashed pt-2">
                    تم الإصدار بواسطة نظام <b>سِيدَة</b> لإدارة المتاجر والمؤسسات العُمانية
                </div>
            @endif
        </div>

    </div>

</body>

</html>
