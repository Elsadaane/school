<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إذن نقل مخزني | {{ $transfer->transfer_number }}</title>

    {{-- إضافة Bootstrap للتنسيق الأساسي --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    {{-- خط Cairo للمطبوعات الرسمية --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* 🌟 تنسيقات العرض على الشاشة 🌟 */
        body {
            background-color: #e2e8f0;
            font-family: 'Cairo', 'Tahoma', sans-serif;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .print-wrapper {
            width: 210mm;
            min-height: 297mm;
            /* مقاس A4 بالطول */
            margin: 40px auto;
            background: #fff;
            padding: 15mm;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            border-top: 15px solid #3b82f6;
        }

        /* 🌟 البانرات واللوجو 🌟 */
        .header-banner img {
            width: 100%;
            max-height: 110px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .footer-banner {
            margin-top: auto;
            /* لدفع الفوتر لأسفل الورقة دائمًا */
            text-align: center;
            padding-top: 20px;
        }

        .footer-banner img {
            width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        .school-logo {
            max-height: 80px;
            width: auto;
            object-fit: contain;
        }

        /* 🌟 الهيدر المقسم 🌟 */
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-content .logo-area {
            width: 30%;
            text-align: right;
        }

        .header-content .title-area {
            width: 40%;
            text-align: center;
        }

        .header-content .info-area {
            width: 30%;
            text-align: left;
            line-height: 1.6;
        }

        .title-box {
            border: 2px dashed #3b82f6;
            padding: 8px 30px;
            border-radius: 12px;
            display: inline-block;
        }

        .title-box h2 {
            margin: 0;
            color: #1e293b;
            font-size: 22px;
            font-weight: 900;
        }

        .title-box p {
            margin: 0;
            font-size: 13px;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* 🌟 صندوق المعلومات 🌟 */
        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            padding: 15px 20px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .info-box div {
            width: 48%;
            line-height: 1.8;
        }

        .info-box p {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            color: #475569;
        }

        .info-box span {
            font-weight: 700;
            color: #0f172a;
            font-family: 'Courier New', Courier, monospace;
            font-size: 15px;
        }

        /* 🌟 جدول الأصناف 🌟 */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        /* 🔥 تأمين عدم انقسام السطر بين صفحتين 🔥 */
        .items-table tr {
            page-break-inside: avoid;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #cbd5e1;
            padding: 12px;
            text-align: center;
            font-size: 14px;
        }

        .items-table th {
            background-color: #f1f5f9;
            font-weight: 800;
            color: #1e293b;
        }

        .items-table td {
            font-weight: 600;
        }

        .items-table tfoot th {
            background-color: #e2e8f0;
            font-size: 16px;
        }

        /* 🌟 التوقيعات والختم 🌟 */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding: 0 10px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 30%;
            text-align: center;
            position: relative;
        }

        .signature-title {
            font-weight: 800;
            font-size: 14px;
            color: #334155;
            display: block;
        }

        .signature-title small {
            color: #64748b;
            font-weight: 600;
        }

        .signature-line {
            width: 80%;
            border-bottom: 1px dashed #475569;
            margin: 40px auto 10px;
            position: relative;
            z-index: 2;
            /* إبقاء الخط فوق الختم */
        }

        /* الختم الواقعي */
        .seal-img {
            max-height: 100px;
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0.85;
            mix-blend-mode: multiply;
            z-index: 1;
            /* أسفل خط التوقيع */
        }

        /* زر الطباعة */
        .btn-print {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            border-radius: 50px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            background: #1e293b;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-print:hover {
            background: #0f172a;
            transform: translateY(-2px);
        }

        /* 🖨️ إعدادات الطباعة الصارمة 🖨️ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }

            .print-wrapper {
                box-shadow: none;
                margin: 0;
                padding: 15mm;
                width: 100%;
                min-height: 100vh;
                border-radius: 0;
                border-top-width: 10px;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    {{-- زر الطباعة العائم --}}
    <button onclick="window.print()" class="no-print btn-print">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
            class="bi bi-printer me-2 align-middle" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
            <path
                d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z" />
        </svg>
        إصدار إذن النقل
    </button>

    <div class="text-center mt-3 mb-2 no-print">
        <a href="{{ route('admin.stock-transfers.index') }}" class="text-decoration-none fw-bold text-secondary">العودة
            لسجل التحويلات</a>
    </div>

    {{-- الحاوية الرئيسية للإذن (A4 Portrait) --}}
    <div class="print-wrapper">

        <div>
            {{-- 🔥 هيدر الإذن (البانر العلوي) من الإعدادات 🔥 --}}
            @if (isset($globalSettings) && $globalSettings->header_image)
                <div class="header-banner">
                    <img src="{{ asset('storage/' . $globalSettings->header_image) }}" alt="Header Banner">
                </div>
            @endif

            {{-- ترويسة الإذن المقسمة --}}
            <div class="header-content">
                {{-- اللوجو --}}
                <div class="logo-area">
                    @if (isset($globalSettings) && $globalSettings->logo)
                        <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" class="school-logo">
                    @endif
                </div>

                {{-- عنوان المستند --}}
                <div class="title-area">
                    <div class="title-box">
                        <h2>إذن تحويل مخزني</h2>
                        <p>Stock Transfer Voucher</p>
                    </div>
                </div>

                {{-- بيانات المدرسة ورقم الإذن --}}
                <div class="info-area">
                    <strong
                        style="font-size: 16px; color: #1e293b;">{{ $globalSettings->company_name_ar ?? 'إدارة المدارس' }}</strong><br>
                    <span style="color: #64748b;">س.ت:</span> <strong
                        dir="ltr">{{ $globalSettings->commercial_record ?? '---' }}</strong><br>
                    <span style="color: #64748b;">رقم الإذن:</span> <strong
                        style="font-size: 16px; color: #3b82f6;">{{ $transfer->transfer_number }}</strong>
                </div>
            </div>

            {{-- صندوق المعلومات الأساسية --}}
            <div class="info-box">
                <div>
                    <p>من فرع (المُرسل): <span class="text-danger">{{ $transfer->fromBranch?->name_ar ?? '-' }}</span>
                    </p>
                    <p>تاريخ الطلب: <span>{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') }}</span>
                    </p>
                    <p>أنشأ بواسطة: <span>{{ $transfer->user?->name ?? 'مدير النظام' }}</span></p>
                </div>
                <div>
                    <p>إلى فرع (المُستقبِل): <span
                            class="text-success">{{ $transfer->toBranch?->name_ar ?? '-' }}</span></p>
                    <p>حالة الطلب:
                        @if ($transfer->status == 'completed')
                            <span class="text-success">مكتمل ومُستلم</span>
                        @elseif($transfer->status == 'shipped')
                            <span class="text-primary">جاري الشحن</span>
                        @elseif($transfer->status == 'pending')
                            <span class="text-warning">قيد الانتظار</span>
                        @else
                            <span class="text-danger">ملغي</span>
                        @endif
                    </p>
                    <p>تاريخ الاستلام:
                        <span>{{ $transfer->received_at ? \Carbon\Carbon::parse($transfer->received_at)->format('Y-m-d') : 'لم يتم الاستلام بعد' }}</span>
                    </p>
                </div>
            </div>

            {{-- جدول الأصناف --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">م</th>
                        <th style="width: 52%; text-align: right; padding-right: 15px;">اسم الصنف / المنتج</th>
                        <th style="width: 20%;">كود الصنف (SKU)</th>
                        <th style="width: 20%;">الكمية المحولة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transfer->items as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td style="text-align: right; padding-right: 15px; color: #0f172a;">
                                {{ $item->product?->name_ar ?? ($item->product?->name_en ?? 'صنف غير معروف') }}
                            </td>
                            <td dir="ltr" class="text-muted">{{ $item->product?->sku ?? '---' }}</td>
                            <td style="font-size: 16px; color: #1e293b;">{{ number_format($item->quantity) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-muted">لا توجد أصناف مسجلة في هذا الإذن.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align: right; padding-right: 15px;">إجمالي الكميات المنقولة:</th>
                        <th style="color: #3b82f6;">{{ number_format($transfer->items->sum('quantity')) }}</th>
                    </tr>
                </tfoot>
            </table>

            @if ($transfer->notes)
                <div
                    style="margin-top: 20px; font-size: 14px; border: 1px dashed #cbd5e1; padding: 15px; border-radius: 8px; background: #f8fafc;">
                    <strong class="text-primary">ملاحظات التحويل:</strong> <span
                        class="text-dark fw-bold">{{ $transfer->notes }}</span>
                </div>
            @endif
        </div>

        <div>
            {{-- التوقيعات (يتم إجبارها على البقاء معاً لتجنب القص) --}}
            <div class="signatures">
                <div class="signature-box">
                    <span class="signature-title">مُسلم العهدة (المُرسِل) <br><small>Sender</small></span>
                    <div class="signature-line"></div>
                    {{-- 🔥 تأمين العلاقة بـ Null-safe Operator 🔥 --}}
                    <span
                        style="font-size: 13px; color: #475569;">{{ $transfer->shippedBy?->name ?? '.......................' }}</span>
                </div>

                <div class="signature-box">
                    {{-- 🔥 ختم المدرسة يظهر خلف التوقيع 🔥 --}}
                    @if (isset($globalSettings) && $globalSettings->seal)
                        <img src="{{ asset('storage/' . $globalSettings->seal) }}" alt="Stamp" class="seal-img">
                    @endif
                    <span class="signature-title">السائق / الناقل <br><small>Driver / Courier</small></span>
                    <div class="signature-line" style="border-color: transparent;"></div>
                    <span style="font-size: 13px; color: #475569;">.......................</span>
                </div>

                <div class="signature-box">
                    <span class="signature-title">مُستلم العهدة (المُستقبِل) <br><small>Receiver</small></span>
                    <div class="signature-line"></div>
                    <span
                        style="font-size: 13px; color: #475569;">{{ $transfer->receivedBy?->name ?? '.......................' }}</span>
                </div>
            </div>

            {{-- 🔥 فوتر الإذن (البانر السفلي) من الإعدادات 🔥 --}}
            @if (isset($globalSettings) && $globalSettings->contract_footer)
                <div class="footer-banner">
                    <img src="{{ asset('storage/' . $globalSettings->contract_footer) }}" alt="Footer Banner">
                </div>
            @endif
        </div>

    </div>

</body>

</html>
