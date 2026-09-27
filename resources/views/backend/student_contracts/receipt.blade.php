<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند قبض - {{ $payment->receipt_number }}</title>

    {{-- خط Cairo للمطبوعات الرسمية --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* 🌟 تنسيقات العرض على الشاشة 🌟 */
        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Arial, sans-serif;
            background: #e2e8f0;
            margin: 0;
            padding: 20px;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .receipt-container {
            max-width: 210mm;
            margin: 10px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            border-top: 10px solid #10b981;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            min-height: 50vh;
        }

        /* 🌟 البانرات واللوجو 🌟 */
        .header-banner img {
            width: 100%;
            max-height: 100px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .footer-banner {
            margin-top: auto;
            text-align: center;
            padding-top: 30px;
        }

        .footer-banner img {
            width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        .school-logo {
            max-height: 75px;
            width: auto;
            object-fit: contain;
        }

        /* 🌟 الهيدر المقسم 🌟 */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .school-info h2 {
            margin: 0 0 5px 0;
            color: #1e293b;
            font-weight: 900;
            font-size: 22px;
        }

        .school-info p {
            margin: 2px 0;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
        }

        .receipt-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .receipt-title h1 {
            margin: 0;
            background: #f1f5f9;
            color: #1e293b;
            display: inline-block;
            padding: 8px 40px;
            border-radius: 8px;
            border: 2px dashed #94a3b8;
            font-size: 24px;
            font-weight: 900;
        }

        /* 🌟 شبكة التفاصيل 🌟 */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .details-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 25px;
        }

        .detail-box {
            background: #f8fafc;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .detail-box span {
            display: block;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 700;
        }

        .detail-box strong {
            font-size: 15px;
            color: #0f172a;
            font-weight: 800;
        }

        /* 🆕 كرت المبلغ بالكلمات */
        .amount-in-words {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .amount-in-words .label {
            font-size: 12px;
            color: #92400e;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .amount-in-words .value {
            font-size: 16px;
            color: #78350f;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .amount-box {
            background: #ecfdf5;
            border: 2px solid #a7f3d0;
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .amount-box h2 {
            margin: 0;
            color: #059669;
            font-size: 34px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .amount-box span {
            color: #047857;
            font-weight: bold;
            font-size: 14px;
        }

        /* 🆕 كرت تفاصيل القسط */
        .installment-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #93c5fd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .installment-info h4 {
            margin: 0 0 10px 0;
            color: #1e40af;
            font-size: 14px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .installment-info .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dashed #93c5fd;
            font-size: 13px;
        }

        .installment-info .info-row:last-child {
            border-bottom: none;
        }

        .installment-info .info-row .label {
            color: #1e40af;
            font-weight: 700;
        }

        .installment-info .info-row .value {
            color: #0f172a;
            font-weight: 800;
            font-family: 'Courier New', monospace;
        }

        /* 🌟 التوقيعات 🌟 */
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            text-align: center;
            position: relative;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 30%;
            position: relative;
        }

        .signature-box p {
            margin: 0;
            font-weight: bold;
            color: #334155;
            font-size: 14px;
        }

        .signature-line {
            border-top: 1px dashed #475569;
            width: 80%;
            margin: 40px auto 10px auto;
            position: relative;
            z-index: 2;
        }

        .seal-img {
            max-height: 110px;
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0.85;
            mix-blend-mode: multiply;
            z-index: 1;
        }

        /* 🌟 الأزرار 🌟 */
        .no-print {
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-print {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            font-family: 'Cairo', sans-serif;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
            transition: 0.3s;
        }

        .btn-print:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .btn-back {
            background: #fff;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 10px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        /* 🖨️ إعدادات الطباعة 🖨️ */
        @media print {
            @page {
                size: A5 landscape;
                margin: 5mm;
            }

            body {
                background: #fff;
                padding: 0;
                margin: 0;
            }

            .receipt-container {
                box-shadow: none;
                max-width: 100%;
                padding: 5mm;
                margin: 0;
                border-top-width: 5px;
                min-height: auto;
                border-radius: 0;
            }

            .no-print {
                display: none !important;
            }

            .header,
            .details-grid,
            .amount-box,
            .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <a href="{{ url()->previous() }}" class="btn-back">العودة</a>
        <button onclick="window.print()" class="btn-print">🖨️ طباعة السند</button>
    </div>

    <div class="receipt-container">

        {{-- 🔥 هيدر السند (البانر العلوي) من الإعدادات 🔥 --}}
        @if (isset($globalSettings) && $globalSettings->header_image)
            <div class="header-banner">
                <img src="{{ asset('storage/' . $globalSettings->header_image) }}" alt="Header Banner">
            </div>
        @endif

        <div class="header">
            <div class="school-info">
                <h2>{{ $globalSettings->company_name_ar ?? 'إدارة المدارس' }}</h2>
                <p>فرع: {{ $contract->branch?->name_ar ?? 'الفرع الرئيسي' }}</p>
                <p>س.ت: <span dir="ltr">{{ $globalSettings->cr_number ?? '---' }}</span> | الرقم الضريبي: <span
                        dir="ltr">{{ $globalSettings->tax_number ?? '---' }}</span></p>
                <p>العنوان: {{ $globalSettings->address_ar ?? '---' }} | هاتف: <span
                        dir="ltr">{{ $globalSettings->phone ?? '---' }}</span></p>
            </div>

            <div style="text-align: center;">
                @if (isset($globalSettings) && $globalSettings->logo)
                    <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" class="school-logo">
                @endif
            </div>

            <div style="text-align: left; line-height: 1.8;">
                <p><strong>التاريخ:</strong> <span dir="ltr"
                        style="font-family: monospace;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</span>
                </p>
                <p><strong>رقم السند:</strong> <span
                        style="font-family: monospace; color: #e11d48; font-weight: 900; font-size: 16px;">{{ $payment->receipt_number }}</span>
                </p>
                {{-- 🆕 عرض نوع الدفع --}}
                @if ($payment->payment_type)
                    <p><strong>نوع السند:</strong>
                        <span
                            style="background: #dcfce7; color: #166534; padding: 2px 10px; border-radius: 4px; font-size: 12px; font-weight: 800;">
                            @if ($payment->payment_type === 'advance')
                                دفعة مقدمة
                            @elseif($payment->payment_type === 'installment')
                                سداد قسط
                            @else
                                {{ $payment->payment_type }}
                            @endif
                        </span>
                    </p>
                @endif
            </div>
        </div>

        <div class="receipt-title">
            <h1>سند قبض مالي <br> <small
                    style="font-size: 14px; font-weight: normal; color: #475569; letter-spacing: 1px;">Cash Receipt
                    Voucher</small></h1>
        </div>

        <div class="amount-box">
            <span>المبلغ المستلم (ر.ع)</span>
            <h2>{{ number_format($payment->amount, 3) }}</h2>
        </div>

        {{-- 🆕 المبلغ كتابة --}}
        <div class="amount-in-words">
            <div class="label">المبلغ كتابة (بالأحرف)</div>
            <div class="value">
                {{-- استخدام دالة مساعدة لتحويل الرقم إلى كلمات --}}
                @php
                    $amountInWords = \App\Helpers\NumberToArabicWords::convert($payment->amount);
                @endphp
                {{ $amountInWords }} ريال عماني فقط لا غير
            </div>
        </div>

        {{-- 🆕 تفاصيل القسط (إذا كان الدفع لقسط محدد) --}}
        @if ($payment->payment_type === 'installment' && $installment)
            <div class="installment-info">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#1e40af"
                        viewBox="0 0 16 16">
                        <path
                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                    </svg>
                    تفاصيل القسط المسدد
                </h4>
                <div class="info-row">
                    <span class="label">رقم القسط:</span>
                    <span class="value">#{{ $installment->id }}</span>
                </div>
                <div class="info-row">
                    <span class="label">تاريخ الاستحقاق:</span>
                    <span class="value"
                        dir="ltr">{{ \Carbon\Carbon::parse($installment->due_date)->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">قيمة القسط الأصلية:</span>
                    <span class="value">{{ number_format($installment->amount, 3) }} OMR</span>
                </div>
                <div class="info-row">
                    <span class="label">المدفوع سابقاً:</span>
                    <span class="value"
                        style="color: #059669;">{{ number_format($installment->paid_amount - $payment->amount, 3) }}
                        OMR</span>
                </div>
                <div class="info-row">
                    <span class="label">المدفوع الآن:</span>
                    <span class="value"
                        style="color: #059669; font-size: 15px;">{{ number_format($payment->amount, 3) }} OMR</span>
                </div>
                <div class="info-row">
                    <span class="label">المتبقي من القسط:</span>
                    <span class="value" style="color: #dc2626;">{{ number_format($installment->remaining_amount, 3) }}
                        OMR</span>
                </div>
            </div>
        @endif

        <div class="details-grid">
            <div class="detail-box">
                <span>استلمنا من المكرم / ولي الأمر:</span>
                <strong>{{ $student->parent?->name_ar ?? '---' }}</strong>
                @if ($student->parent?->national_id)
                    <small style="display: block; margin-top: 5px; color: #64748b; font-size: 11px;">
                        <i>رقم الهوية: <span dir="ltr">{{ $student->parent->national_id }}</span></i>
                    </small>
                @endif
            </div>
            <div class="detail-box">
                <span>عن الطالب/ـة:</span>
                <strong>{{ $student->first_name ?? '' }} {{ $student->tribe ?? '' }}</strong>
                <small style="display: block; margin-top: 5px; color: #3b82f6; font-size: 12px;">
                    الصف: {{ $student->grade?->name_ar ?? '---' }}
                    @if ($student->section)
                        | الشعبة: {{ $student->section->name_ar }}
                    @endif
                </small>
                <small style="display: block; color: #64748b; font-size: 11px;">
                    عقد رقم: {{ $contract->contract_number ?? '---' }}
                </small>
            </div>
            <div class="detail-box">
                <span>وذلك عبارة عن:</span>
                <strong>{{ $payment->transaction_details ?? 'سداد دفعة نقدية' }}</strong>
            </div>
            <div class="detail-box">
                <span>طريقة الدفع / الخزينة:</span>
                <strong>{{ $payment->financialAccount?->name_ar ?? 'الخزينة العامة' }}</strong>
                @if ($payment->check_number)
                    <small style="display: block; margin-top: 5px; color: #64748b; font-size: 11px;">
                        <i>رقم المرجع/الشيك: <span dir="ltr">{{ $payment->check_number }}</span></i>
                    </small>
                @endif
            </div>
        </div>

        @if ($contract)
            <div class="details-grid-3">
                <div class="detail-box" style="background: #fff; text-align: center;">
                    <span>إجمالي العقد:</span>
                    <strong style="font-family: monospace;">{{ number_format($contract->grand_total, 3) }} OMR</strong>
                </div>
                <div class="detail-box" style="background: #fff; text-align: center;">
                    <span>إجمالي المدفوع حتى الآن:</span>
                    <strong
                        style="color: #059669; font-family: monospace;">{{ number_format($contract->paid_amount, 3) }}
                        OMR</strong>
                </div>
                <div class="detail-box" style="background: #fff; text-align: center;">
                    <span>المديونية المتبقية:</span>
                    <strong
                        style="color: #dc2626; font-family: monospace;">{{ number_format($contract->total_remaining, 3) }}
                        OMR</strong>
                </div>
            </div>
        @endif

        <div class="footer">
            <div class="signature-box">
                <p>توقيع المستلم (المحاسب)</p>
                <div class="signature-line"></div>
                <p style="font-size: 13px; color: #64748b; font-weight: 600;">
                    {{ $payment->user?->name ?? 'إدارة الحسابات' }}</p>
            </div>

            <div class="signature-box">
                <p>الختم الرسمي للمدرسة</p>
                @if (isset($globalSettings) && $globalSettings->seal)
                    <img src="{{ asset('storage/' . $globalSettings->seal) }}" alt="Stamp" class="seal-img">
                @endif
                <div class="signature-line" style="border-color: transparent;"></div>
            </div>

            <div class="signature-box">
                <p>توقيع دافع المبلغ</p>
                <div class="signature-line"></div>
                <p style="font-size: 13px; color: #64748b; font-weight: 600;">.......................</p>
            </div>
        </div>

        {{-- 🔥 فوتر السند (البانر السفلي) من الإعدادات 🔥 --}}
        @if (isset($globalSettings) && $globalSettings->contract_footer)
            <div class="footer-banner">
                <img src="{{ asset('storage/' . $globalSettings->contract_footer) }}" alt="Contract Footer">
            </div>
        @endif

    </div>

</body>

</html>
