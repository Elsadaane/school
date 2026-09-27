<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عقد عمل أكاديمي | {{ $teacher->name_ar ?? 'معلم' }}</title>

    {{-- استدعاء Bootstrap و خط Cairo و Boxicons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        /* 🌟 تنسيقات العرض على الشاشة 🌟 */
        body {
            font-family: 'Cairo', Tahoma, Arial, sans-serif;
            background-color: #cbd5e1;
            color: #0f172a;
            margin: 0;
            padding: 40px 20px 80px 20px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* 🌟 حاوية ورقة A4 بالطول 🌟 */
        .print-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 15mm;
            border-radius: 8px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            border-top: 15px solid #1e293b;
            overflow: hidden;
        }

        /* 🌟 العلامة المائية (Watermark) 🌟 */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            z-index: 0;
            width: 70%;
            pointer-events: none;
        }

        /* محتوى العقد فوق العلامة المائية */
        .contract-content {
            position: relative;
            z-index: 1;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        /* 🌟 البانرات 🌟 */
        .header-banner img {
            width: 100%;
            max-height: 140px;
            object-fit: contain;
            margin-bottom: 25px;
            border-radius: 6px;
        }

        .footer-banner {
            margin-top: auto;
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
        }

        .footer-banner img {
            width: 100%;
            max-height: 90px;
            object-fit: contain;
        }

        /* 🌟 الترويسة الرسمية 🌟 */
        .contract-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px double #94a3b8;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .school-logo {
            max-height: 85px;
            width: auto;
            object-fit: contain;
        }

        .contract-title {
            text-align: center;
            font-weight: 900;
            margin-bottom: 35px;
            color: #0f172a;
            text-decoration: underline;
            text-underline-offset: 8px;
            font-size: 24px;
            letter-spacing: 0.5px;
        }

        /* 🌟 الجداول والعناوين 🌟 */
        .section-title {
            font-weight: 800;
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 15px;
            background: #f8fafc;
            padding: 8px 15px;
            border-right: 5px solid #10b981;
            /* لون أخضر رسمي */
            border-radius: 4px;
            page-break-after: avoid;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 14px;
            page-break-inside: avoid;
        }

        .info-table th,
        .info-table td {
            border: 1px solid #cbd5e1;
            padding: 12px 15px;
            vertical-align: middle;
        }

        .info-table th {
            width: 22%;
            background-color: #f1f5f9 !important;
            font-weight: 800;
            color: #334155;
        }

        .info-table td {
            color: #0f172a;
            font-weight: 700;
            width: 28%;
        }

        /* 🌟 منطقة الشروط 🌟 */
        .terms-box {
            border: 2px solid #e2e8f0;
            padding: 25px;
            border-radius: 8px;
            background-color: #fff;
            line-height: 1.9;
            text-align: justify;
            font-size: 14px;
            color: #334155;
            margin-bottom: 40px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        /* 🌟 التوقيعات والختم 🌟 */
        .signature-area {
            margin-top: 30px;
            page-break-inside: avoid;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 20px;
        }

        .signature-box {
            text-align: center;
            position: relative;
            width: 45%;
        }

        .sign-title {
            font-weight: 800;
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 50px;
        }

        .signature-img {
            max-width: 200px;
            max-height: 90px;
            margin-top: 10px;
            mix-blend-mode: multiply;
            position: relative;
            z-index: 2;
        }

        .seal-img {
            max-height: 110px;
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0.85;
            mix-blend-mode: multiply;
            z-index: 1;
        }

        .sign-line {
            border-top: 2px dashed #94a3b8;
            margin: 10px auto;
            width: 85%;
            position: relative;
            z-index: 2;
        }

        /* 🌟 أزرار التحكم العائمة (مخفية في الطباعة) 🌟 */
        .floating-actions {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 12px 25px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            gap: 15px;
            z-index: 1000;
            border: 1px solid #e2e8f0;
        }

        .btn-action {
            border-radius: 50px;
            padding: 10px 25px;
            font-size: 15px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-print {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-back {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-back:hover {
            background: #e2e8f0;
            color: #0f172a;
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

            .print-container {
                box-shadow: none;
                margin: 0;
                padding: 15mm;
                width: 100%;
                min-height: 100vh;
                border-radius: 0;
                border-top-width: 10px;
            }

            .floating-actions,
            .no-print {
                display: none !important;
            }

            .terms-box {
                box-shadow: none;
                border-color: #000;
            }

            .info-table th,
            .info-table td {
                border-color: #000;
            }
        }
    </style>
</head>

<body>

    {{-- أزرار التحكم العائمة للطباعة والرجوع --}}
    <div class="floating-actions no-print">
        <a href="{{ route('admin.teachers.index') }}" class="btn-action btn-back">
            <i class='bx bx-arrow-back fs-5'></i> العودة للقائمة
        </a>
        <button onclick="window.print()" class="btn-action btn-print">
            <i class='bx bx-printer fs-5'></i> إصدار العقد للطباعة
        </button>
    </div>

    <div class="print-container">

        {{-- 🔥 العلامة المائية باللوجو 🔥 --}}
        @if (isset($globalSettings) && $globalSettings->logo)
            <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Watermark" class="watermark">
        @endif

        <div class="contract-content">
            {{-- 🔥 هيدر العقد (البانر العلوي) من الإعدادات 🔥 --}}
            @if (isset($globalSettings) && $globalSettings->contract_header)
                <div class="header-banner text-center">
                    <img src="{{ asset('storage/' . $globalSettings->contract_header) }}" alt="Contract Header">
                </div>
            @endif

            {{-- ترويسة العقد الرسمية --}}
            <div class="contract-header">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">{{ $globalSettings->company_name_ar ?? 'إدارة المدارس' }}</h5>
                    <small class="text-muted fw-bold">{{ $globalSettings->governorate_ar ?? 'سلطنة عمان' }}</small>
                </div>

                <div class="text-center">
                    @if (isset($globalSettings) && $globalSettings->logo)
                        <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" class="school-logo">
                    @endif
                </div>

                <div class="text-end">
                    <h5 class="fw-bold mb-1 text-dark" dir="ltr">
                        {{ $globalSettings->company_name_en ?? 'Smart Student Schools' }}</h5>
                    <small class="text-muted fw-bold"
                        dir="ltr">{{ $globalSettings->governorate_en ?? 'Sultanate of Oman' }}</small>
                </div>
            </div>

            <h3 class="contract-title">عقد عمل وتسكين أكاديمي <br><small
                    style="font-size: 16px; color: #475569; text-decoration: none;">(Employment & Academic Assignment
                    Contract)</small></h3>

            {{-- بيانات الطرفين (مدمج معها بيانات الـ HR) --}}
            <div class="section-title">بيانات الطرف الثاني (المعلم):</div>
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>الاسم الرباعي</th>
                        <td class="text-primary fw-bolder">{{ $teacher->name_ar ?? '---' }}</td>
                        <th>الجنسية</th>
                        <td>{{ $teacher->nationality ?? 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <th>رقم الهوية / الجواز</th>
                        <td class="font-monospace text-danger fw-bolder">{{ $teacher->national_id ?? '---' }}</td>
                        <th>الرقم الوظيفي</th>
                        <td class="font-monospace fw-bolder">{{ $teacher->hrEmployee->employee_code ?? '---' }}</td>
                    </tr>
                    <tr>
                        <th>الفرع المٌقيد به</th>
                        <td class="fw-bolder">{{ $teacher->branch?->name_ar ?? 'الفرع الرئيسي' }}</td>
                        <th>الراتب الأساسي</th>
                        <td class="font-monospace text-success fw-bolder" dir="ltr">
                            {{ $teacher->hrEmployee ? number_format($teacher->hrEmployee->basic_salary, 3) : '0.000' }}
                            OMR
                        </td>
                    </tr>
                    <tr>
                        <th>التخصص (المواد)</th>
                        <td colspan="3" class="fw-bolder">
                            @if ($teacher->subjects && $teacher->subjects->count() > 0)
                                {{ implode(' ، ', $teacher->subjects->pluck('name_ar')->toArray()) }}
                            @else
                                غير محدد
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>رقم الهاتف</th>
                        <td dir="ltr" class="font-monospace fw-bolder">{{ $teacher->phone ?? '---' }}</td>
                        <th>تاريخ التعيين</th>
                        <td dir="ltr" class="font-monospace">
                            {{ $teacher->hrEmployee->join_date ?? ($teacher->created_at ? $teacher->created_at->format('Y-m-d') : date('Y-m-d')) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- شروط العقد --}}
            <div class="section-title">الشروط والأحكام الخاصة بالتعاقد:</div>
            <div class="terms-box">
                <div class="mb-3">
                    {{-- 🔥 سحب شروط المعلم الخاصة، لو مفيش يسحب الشروط العامة لعقود المعلمين من الإعدادات 🔥 --}}
                    {!! $teacher->contract_terms_ar ??
                        ($globalSettings->teacher_contract_terms_ar ??
                            '<p>لا توجد شروط مسجلة. تخضع هذه الاتفاقية لقانون العمل واللوائح الداخلية المعتمدة.</p>') !!}
                </div>

                @if ($teacher->contract_terms_en || (isset($globalSettings) && $globalSettings->teacher_contract_terms_en))
                    <hr style="border-color: #cbd5e1; margin: 25px 0;">
                    <div class="mt-3" dir="ltr" style="text-align: left; font-family: Tahoma, sans-serif;">
                        {!! $teacher->contract_terms_en ?? $globalSettings->teacher_contract_terms_en !!}
                    </div>
                @endif
            </div>

            {{-- التوقيعات --}}
            <div class="signature-area">
                <div class="signature-box">
                    <div class="sign-title">الطرف الأول (إدارة المؤسسة)</div>

                    {{-- 🔥 ختم المؤسسة يظهر كأنه مطبوع فوق الورقة 🔥 --}}
                    @if (isset($globalSettings) && $globalSettings->seal)
                        <img src="{{ asset('storage/' . $globalSettings->seal) }}" alt="Stamp" class="seal-img">
                    @endif

                    <div class="sign-line"></div>
                    <span style="font-size: 14px; color: #475569; font-weight: bold;">الختم الرسمي والتوقيع
                        المعتمد</span>
                </div>

                <div class="signature-box">
                    <div class="sign-title">الطرف الثاني (المعلم)</div>

                    @if ($teacher->signature_data)
                        <img src="{{ $teacher->signature_data }}" class="signature-img" alt="توقيع المعلم">
                        <p style="margin: 5px 0 0 0; font-size: 15px; font-weight: bold; color: #1e293b;">
                            {{ $teacher->name_ar }}</p>
                    @else
                        <div class="sign-line" style="margin-top: 70px;"></div>
                        <span style="font-size: 14px; color: #475569; font-weight: bold;">الاسم والتوقيع</span>
                        <p style="margin-top: 5px; font-size: 15px; font-weight: bold; color: #1e293b;">
                            {{ $teacher->name_ar }}</p>
                    @endif
                </div>
            </div>

            {{-- 🔥 فوتر العقد (البانر السفلي) من الإعدادات 🔥 --}}
            @if (isset($globalSettings) && $globalSettings->contract_footer)
                <div class="footer-banner">
                    <img src="{{ asset('storage/' . $globalSettings->contract_footer) }}" alt="Contract Footer">
                </div>
            @endif
        </div>

    </div>

</body>

</html>
