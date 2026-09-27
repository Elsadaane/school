<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استمارة نقل طالب - {{ $transfer->student?->first_name ?? '' }} {{ $transfer->student?->last_name ?? '' }}
    </title>

    {{-- إضافة Bootstrap للتنسيق الأساسي --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">

    {{-- خط Cairo للمطبوعات الرسمية --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* 🌟 تنسيقات العرض على الشاشة 🌟 */
        body {
            font-family: 'Cairo', 'Tahoma', sans-serif;
            background: #e2e8f0;
            margin: 0;
            padding: 20px;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* حاوية ورقة A4 بالطول */
        .print-wrapper {
            width: 210mm;
            min-height: 297mm;
            /* مقاس A4 بالطول */
            margin: 10px auto 40px auto;
            background: #fff;
            padding: 15mm;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            border-top: 15px solid #1e293b;
            /* خط علوي يعطي طابع رسمي */
        }

        /* 🌟 البانرات 🌟 */
        .header-banner img {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .footer-banner {
            margin-top: auto;
            /* لدفع الفوتر للأسفل دائمًا */
            text-align: center;
            padding-top: 20px;
        }

        .footer-banner img {
            width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        /* 🌟 الترويسة (Header) 🌟 */
        .official-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px double #cbd5e1;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header-right {
            width: 30%;
            text-align: right;
        }

        .header-center {
            width: 40%;
            text-align: center;
        }

        .header-left {
            width: 30%;
            text-align: left;
        }

        .school-logo {
            max-height: 75px;
            width: auto;
            object-fit: contain;
        }

        .document-title {
            font-size: 24px;
            font-weight: 900;
            color: #1e293b;
            text-decoration: underline;
            text-underline-offset: 6px;
            margin-bottom: 5px;
        }

        /* 🌟 الجداول والعناوين 🌟 */
        .section-title {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 800;
            font-size: 15px;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-bottom: none;
            margin-top: 25px;
            margin-bottom: 0;
            border-radius: 6px 6px 0 0;
            page-break-after: avoid;
            /* منع فصل العنوان عن الجدول التالي */
        }

        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* 🔥 تأمين عدم انقسام الأسطر بين الصفحات 🔥 */
        .table-custom tr {
            page-break-inside: avoid;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #cbd5e1;
            padding: 12px;
            vertical-align: middle;
        }

        .table-custom th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 800;
            width: 25%;
        }

        .table-custom td {
            color: #0f172a;
            font-weight: 600;
        }

        /* 🌟 التوقيعات 🌟 */
        .signatures-area {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
            /* تماسك كتلة التوقيعات */
        }

        .sign-box {
            width: 23%;
            text-align: center;
            position: relative;
        }

        .sign-title {
            font-weight: 800;
            font-size: 14px;
            color: #334155;
            margin-bottom: 45px;
            display: block;
        }

        .sign-line {
            border-top: 1px dashed #475569;
            margin: 10px auto;
            width: 90%;
            position: relative;
            z-index: 2;
            /* فوق الختم */
        }

        .sign-label {
            font-size: 12px;
            color: #64748b;
            font-weight: bold;
        }

        /* الختم الواقعي خلف التوقيع */
        .seal-img {
            max-height: 95px;
            position: absolute;
            top: 5px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0.85;
            mix-blend-mode: multiply;
            z-index: 1;
            /* خلف خط التوقيع المقطع */
        }

        /* 🌟 زر الطباعة العائم 🌟 */
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
            transition: 0.3s ease-in-out;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print:hover {
            background: #0f172a;
            transform: translateY(-2px);
        }

        /* 🖨️ تنسيقات الطباعة الصارمة والنهائية 🖨️ */
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
            class="bi bi-printer align-middle" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
            <path
                d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z" />
        </svg>
        إصدار وطباعة الاستمارة
    </button>

    {{-- شريط التنقل الشاشي --}}
    <div class="text-center mt-3 no-print">
        <a href="{{ route('admin.student-contracts.index') }}"
            style="text-decoration: none; font-weight: bold; color: #64748b;"><i
                class="bx bx-left-arrow-alt align-middle"></i> العودة لسجل العقود والتحويلات</a>
    </div>

    {{-- الحاوية الرئيسية للاستمارة (A4 Portrait) --}}
    <div class="print-wrapper">

        {{-- 🔥 هيدر الاستمارة (البانر العلوي) من الإعدادات 🔥 --}}
        @if (isset($globalSettings) && $globalSettings->header_image)
            <div class="header-banner text-center">
                <img src="{{ asset('storage/' . $globalSettings->header_image) }}" alt="Header Banner">
            </div>
        @endif

        {{-- الترويسة الرسمية --}}
        <div class="official-header">
            <div class="header-right">
                <h5 class="fw-bold mb-1 text-dark" style="font-size: 16px;">
                    {{ $globalSettings->company_name_ar ?? 'إدارة المدارس' }}</h5>
                <p class="mb-0 text-muted fw-bold" style="font-size: 12px;">إدارة شؤون الطلاب والتسجيل</p>
            </div>

            <div class="header-center">
                <h3 class="document-title">استمارة نقل داخلي</h3>
                <span class="badge bg-light text-dark border px-3 py-1 font-12 shadow-sm fw-bold">(إخلاء طرف وقبول
                    شعبة)</span>
            </div>

            <div class="header-left d-flex justify-content-end align-items-center">
                <div class="text-end me-3">
                    <p class="mb-1 fw-bold" style="font-size: 13px; color: #475569;">رقم الطلب: <span dir="ltr"
                            class="text-primary font-monospace">TRF-{{ str_pad($transfer->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </p>
                    <p class="mb-0 fw-bold" style="font-size: 13px; color: #475569;">التاريخ: <span dir="ltr"
                            class="text-dark font-monospace">{{ \Carbon\Carbon::parse($transfer->transfer_date)->format('Y/m/d') }}</span>
                    </p>
                </div>
                @if (isset($globalSettings) && $globalSettings->logo)
                    <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" class="school-logo">
                @endif
            </div>
        </div>

        <div class="content-wrapper">
            {{-- 1. بيانات الطالب --}}
            <div class="section-title"><i class="mdi mdi-account-circle-outline me-1"></i> أولاً: بيانات الطالب الأساسية
            </div>
            <table class="table-custom">
                <tbody>
                    <tr>
                        <th>اسم الطالب (رباعي)</th>
                        <td colspan="3" class="fw-bold fs-5 text-primary">
                            {{ $transfer->student?->first_name ?? '' }} {{ $transfer->student?->second_name ?? '' }}
                            {{ $transfer->student?->third_name ?? '' }} {{ $transfer->student?->last_name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>كود (رقم) الطالب</th>
                        <td class="fw-bold font-monospace text-danger fs-6">{{ $transfer->student_id }}</td>
                        <th>المرحلة والصف الدراسي</th>
                        <td class="fw-bold">{{ $transfer->student?->grade?->name_ar ?? 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <th>سبب التحويل الإداري</th>
                        <td colspan="3" class="fw-semibold text-muted" style="font-size: 13px;">
                            {{ $transfer->transfer_reason ?? 'بناءً على رغبة ولي الأمر وموافقة الإدارة.' }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- 2. تفاصيل الفروع --}}
            <div class="section-title"><i class="mdi mdi-map-marker-distance me-1"></i> ثانياً: مسار النقل والفروع</div>
            <table class="table-custom">
                <tbody>
                    <tr>
                        <th>الفرع المُرسِل (القديم)</th>
                        <td class="fw-bold text-danger fs-6">{{ $transfer->fromBranch?->name_ar ?? '---' }}</td>
                        <th>الفرع المُستقبِل (الجديد)</th>
                        <td class="fw-bold text-success fs-6">{{ $transfer->toBranch?->name_ar ?? '---' }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- 3. الموافقات الإدارية والمالية --}}
            <div class="section-title"><i class="mdi mdi-shield-check-outline me-1"></i> ثالثاً: الموافقات الرسمية
                والحالة المالية</div>
            <table class="table-custom">
                <tbody>
                    <tr>
                        <th>موافقة الوزارة / المديرية</th>
                        <td colspan="3" class="fw-semibold">
                            @if ($transfer->with_ministry_approval && $transfer->ministry_approval_number)
                                مُعتمد بموجب قرار رقم: <strong
                                    class="ms-1 text-primary font-monospace fs-6">{{ $transfer->ministry_approval_number }}</strong>
                                &nbsp;|&nbsp; بتاريخ: <strong
                                    class="ms-1 font-monospace">{{ \Carbon\Carbon::parse($transfer->ministry_approval_date)->format('Y-m-d') }}</strong>
                            @else
                                <span class="text-muted fw-bold"><i class="mdi mdi-minus-circle-outline"></i> نقل داخلي
                                    مباشر (لا يتطلب موافقة وزارة أو مستوفى إلكترونياً)</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>رسوم التحويل الإدارية</th>
                        <td class="fw-bold font-monospace text-dark fs-6">
                            {{ number_format($transfer->transfer_fee ?? 0, 3) }} OMR</td>
                        <th>حالة تحصيل الرسوم</th>
                        <td class="fw-bold fs-6">
                            @if ($transfer->is_fee_paid)
                                <span class="text-success"><i class="mdi mdi-check-circle me-1"></i> تم سداد الرسوم
                                    واعتماد الحسابات</span>
                            @else
                                <span class="text-danger"><i class="mdi mdi-alert-circle me-1"></i> لم يتم سداد الرسوم
                                    (معلق مالياً)</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- 4. إقرار ولي الأمر --}}
            <div class="mt-4 p-4 border rounded shadow-sm"
                style="background-color: #f8fafc; border-color: #cbd5e1 !important;">
                <h6 class="fw-bold text-decoration-underline mb-2 text-dark">إقرار ولي الأمر (الطرف الثاني):</h6>
                <p class="mb-0 lh-lg text-justify" style="font-size: 13.5px; color: #334155; font-weight: 500;">
                    أقر أنا ولي أمر الطالب المذكور أعلاه، برغبتي الكاملة في نقل نجلي/ابنتي إلى الفرع المذكور، وأتعهد
                    بتسوية كافة
                    المستحقات المالية والمتأخرات بالفرع القديم (إن وجدت) قبل إتمام نقل الملفات أكاديمياً، وموافقتي
                    التامة على دفع رسوم النقل
                    الإدارية المقررة والموافقة على لوائح وأنظمة المدرسة الفرعية.
                </p>
            </div>
        </div>

        {{-- 5. التوقيعات والأختام --}}
        <div class="signatures-area">
            <div class="sign-box">
                <span class="sign-title">توقيع ولي الأمر</span>
                <div class="sign-line"></div>
                <span class="sign-label">الاسم: ........................</span>
            </div>

            <div class="sign-box">
                <span class="sign-title">شؤون الطلاب (الفرع القديم)</span>
                <div class="sign-line"></div>
                <span class="sign-label">ختم وتوقيع إخلاء الطرف</span>
            </div>

            <div class="sign-box">
                <span class="sign-title">شؤون الطلاب (الفرع الجديد)</span>
                <div class="sign-line"></div>
                <span class="sign-label">ختم وقبول قيد الطالب</span>
            </div>

            <div class="sign-box">
                <span class="sign-title">المدير العام / الاعتماد</span>
                <div class="sign-line" style="border-color: transparent;"></div>

                {{-- 🔥 ختم المدرسة الرقمي الشفاف يظهر فوق العناوين في الخلفية 🔥 --}}
                @if (isset($globalSettings) && $globalSettings->seal)
                    <img src="{{ asset('storage/' . $globalSettings->seal) }}" alt="Stamp" class="seal-img">
                @endif

                <span class="sign-label text-dark">الختم الرسمي للمدرسة</span>
            </div>
        </div>

        {{-- 🔥 فوتر الاستمارة (البانر السفلي) من الإعدادات 🔥 --}}
        @if (isset($globalSettings) && $globalSettings->contract_footer)
            <div class="footer-banner">
                <img src="{{ asset('storage/' . $globalSettings->contract_footer) }}" alt="Footer Banner">
            </div>
        @endif

        <div class="text-center mt-3 no-print" style="font-size: 11px; color: #94a3b8; font-weight: bold;">
            طُبع بواسطة المحاسب المعتمد: {{ auth()->user()->name ?? 'مدير النظام' }} | تاريخ وطباعة مستند نظام سِيدَة:
            {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>
</body>

</html>
