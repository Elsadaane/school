@extends('backend.layouts.master')

@section('title', 'الشهادة الأكاديمية - ' . $student->first_name)

@push('css')
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Tajawal:wght@400;500;700;900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', 'Tajawal', Arial, sans-serif;
            background: #e2e8f0;
            padding: 20px;
        }

        /* 🌟 تنسيق وتأطير ورقة الـ A4 البريميوم 🌟 */
        .certificate-container {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            margin: 0 auto;
            padding: 15mm 15mm 30mm;
            /* زيادة الهامش السفلي لاحتواء الفوتر */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            position: relative;
            border: 12px solid #5b69bc;
            outline: 2px solid #e2e8f0;
            outline-offset: -18px;
            box-sizing: border-box;
            background-image: radial-gradient(rgba(91, 105, 188, 0.04) 2px, transparent 0);
            background-size: 30px 30px;
        }

        /* 🌟 البانرات والترويسة الرسمية 🌟 */
        .print-banner {
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
        }

        .print-banner img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .header-banner img {
            max-height: 110px;
        }

        .footer-banner {
            position: absolute;
            bottom: 5mm;
            left: 15mm;
            right: 15mm;
        }

        .footer-banner img {
            max-height: 80px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #5b69bc;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header img.school-logo {
            max-height: 85px;
            margin-bottom: 10px;
            object-fit: contain;
        }

        .header h1 {
            color: #5b69bc;
            margin: 0 0 5px;
            font-weight: 900;
            font-size: 26px;
        }

        /* 🌟 بطاقة بيانات الطالب (Bilingual) 🌟 */
        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }

        .student-info p {
            margin: 4px 0;
            font-size: 14px;
            font-weight: 700;
            color: #475569;
        }

        .student-info strong {
            color: #1e293b;
            font-size: 15px;
        }

        /* 🌟 جدول الدرجات المطور (Bilingual) 🌟 */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .grades-table th,
        .grades-table td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: center;
            font-size: 15px;
            vertical-align: middle;
        }

        .grades-table th {
            background-color: #5b69bc !important;
            color: #fff !important;
            font-weight: 800;
            font-size: 13px;
            line-height: 1.4;
        }

        .grades-table th span {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #e0e7ff;
        }

        .grades-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .total-row {
            font-weight: 900;
            background-color: #e2e8f0 !important;
            font-size: 16px;
            color: #0f172a;
        }

        /* 🌟 تقرير المستشار الذكي 🌟 */
        .ai-advisor {
            background: #f0fdf4;
            border-right: 5px solid #16a34a;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 1px solid #bbf7d0;
            border-top: 1px solid #bbf7d0;
            border-bottom: 1px solid #bbf7d0;
        }

        .ai-advisor h5 {
            margin-top: 0;
            margin-bottom: 8px;
            color: #14532d;
            font-weight: 800;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ai-advisor p {
            margin: 0;
            line-height: 1.8;
            color: #1e293b;
            font-size: 14px;
            text-align: justify;
        }

        /* 🌟 التوقيعات والأختام والـ QR 🌟 */
        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 30px;
            margin-bottom: 15mm;
            /* مسافة للفوتر */
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 220px;
            position: relative;
        }

        .signature-box img.seal-img {
            max-height: 105px;
            position: absolute;
            top: -55px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            mix-blend-mode: multiply;
            opacity: 0.85;
        }

        .signature-box p {
            border-top: 2px dashed #64748b;
            padding-top: 10px;
            margin-top: 5px;
            font-weight: 800;
            font-size: 13px;
            color: #1e293b;
            position: relative;
            z-index: 5;
            line-height: 1.4;
        }

        .qr-code-box {
            text-align: center;
        }

        .qr-code-box p {
            margin-top: 8px;
            font-size: 11px;
            color: #475569;
            font-weight: 700;
            line-height: 1.5;
        }

        /* زر الإجراء العائم المتناسق */
        .btn-print {
            display: block;
            width: 210mm;
            margin: 0 auto 20px;
            padding: 12px;
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 700;
            border: none;
            box-shadow: 0 4px 10px rgba(22, 163, 74, 0.3);
            transition: all 0.3s;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(22, 163, 74, 0.4);
        }

        /* 🖨️ التهيئات الصارمة لإخراج ملف الطباعة والـ PDF 🖨️ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            body {
                background: #fff !important;
                padding: 0;
                margin: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .certificate-container {
                box-shadow: none !important;
                border: 12px solid #5b69bc !important;
                width: 210mm;
                height: 297mm;
                /* التزام بحدود الورقة */
                padding: 10mm 15mm 25mm;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            .btn-print,
            .no-print {
                display: none !important;
            }

            .footer-banner {
                position: fixed;
                bottom: 0;
                left: 15mm;
                right: 15mm;
            }
        }
    </style>
@endpush

@section('content')

    {{-- زر التحكم العائم للشاشة --}}
    <button onclick="window.print()" class="btn-print no-print"><i class="bx bx-printer align-middle me-1"></i> إصدار وطباعة
        الشهادة الرسمية</button>

    <div class="certificate-container">

        {{-- 🔥 1. البانر العلوي (Header Image) الموحد للنظام 🔥 --}}
        @if (isset($globalSettings) && $globalSettings->header_image)
            <div class="print-banner header-banner">
                <img src="{{ asset('storage/' . $globalSettings->header_image) }}" alt="الشعار الإضافي للمدرسة">
            </div>
        @endif

        {{-- ترويسة الشهادة الفاخرة --}}
        <div class="header">
            @if (isset($globalSettings) && $globalSettings->logo)
                <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="شعار المدرسة" class="school-logo">
            @endif
            <h1>{{ $globalSettings->company_name_ar ?? 'مدارس الطالب الذكي' }}</h1>
            <h4 class="mb-2">{{ $globalSettings->governorate_ar ?? 'سلطنة عُمان' }} -
                {{ $student->branch->name_ar ?? 'الفرع الرئيسي' }}</h4>
            <h3 style="margin-top: 15px; color: #1e293b; font-weight: 900; font-size: 22px;">الشهادة الأكاديمية -
                {{ $term->name_ar }}</h3>
            <h6 style="color: #64748b; font-weight: 700; font-size: 14px;">Academic Certificate -
                {{ $term->name_en ?? 'Term Report' }}</h6>
        </div>

        {{-- مربع بيانات الطالب (Bilingual) --}}
        <div class="student-info">
            <div style="width: 48%;">
                <p>اسم الطالب / Name: <br><strong>{{ $student->first_name }} {{ $student->second_name }}
                        {{ $student->tribe }}</strong></p>
                <p>الرقم المدني / ID No: <br><strong class="font-monospace">{{ $student->national_id ?? '---' }}</strong>
                </p>
            </div>
            <div style="width: 48%;">
                <p>الصف الدراسي / Grade: <br><strong>{{ $student->grade->name_ar ?? '---' }}</strong></p>
                <p>الشعبة / Section: <br><strong>{{ $student->section->name_ar ?? '---' }}</strong></p>
            </div>
        </div>

        {{-- جدول كشف الدرجات (Bilingual Headers) --}}
        <table class="grades-table">
            <thead>
                <tr>
                    <th style="width: 35%;">المادة الدراسية <span>Subject</span></th>
                    <th style="width: 15%;">النهاية العظمى <span>Max Mark</span></th>
                    <th style="width: 20%;">الدرجة الحاصل عليها <span>Obtained</span></th>
                    <th style="width: 15%;">النسبة المئوية <span>Percentage</span></th>
                    <th style="width: 15%;">التقدير <span>Grade</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportCard as $row)
                    <tr>
                        <td style="text-align: right; font-weight: 700; padding-right: 15px; color: #1e293b;">
                            {{ $row['subject'] }}</td>
                        <td class="font-monospace">{{ floatval($row['max']) }}</td>
                        <td style="font-weight: 800; color: #16a34a;" class="font-monospace">
                            {{ floatval($row['obtained']) }}</td>
                        <td dir="ltr" class="font-bold font-monospace text-secondary">{{ $row['percent'] }}%</td>
                        <td class="font-bold">{{ $row['grade_letter'] ?? '---' }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td style="text-align: right; padding-right: 15px;">المجموع الكلي / Total</td>
                    <td class="font-monospace">{{ floatval($totalMax) }}</td>
                    <td class="font-monospace text-primary">{{ floatval($totalObtained) }}</td>
                    <td dir="ltr" class="font-monospace text-primary">
                        {{ $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 1) : 0 }}%
                    </td>
                    <td class="text-primary">
                        @php $finalPercent = $totalMax > 0 ? ($totalObtained / $totalMax) * 100 : 0; @endphp
                        @if ($finalPercent >= 90)
                            ممتاز
                        @elseif($finalPercent >= 80)
                            جيد جداً
                        @elseif($finalPercent >= 70)
                            جيد
                        @elseif($finalPercent >= 60)
                            مقبول
                        @else
                            ضعيف
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- تقرير الرأي الأكاديمي والتحليل الذكي --}}
        <div class="ai-advisor shadow-sm">
            <h5><i class="bx bx-brain fs-5"></i> تقرير وتحليل المستشار الأكاديمي:</h5>
            <p>{{ $smartAdvice }}</p>
        </div>

        {{-- قسم الفوتر والتوقيع والـ QR المعزز للموثوقية --}}
        <div class="footer-section">
            <div class="signature-box">
                <p>شؤون الطلاب<br><span style="font-size: 11px; color:#64748b; font-weight:600;">Student Affairs</span></p>
            </div>

            <div class="qr-code-box">
                {!! $qrCode !!}
                <p>امسح الكود للتحقق من الموثوقية<br><span style="font-weight:500;">Scan to verify authenticity</span></p>
            </div>

            <div class="signature-box">
                @if (isset($globalSettings) && $globalSettings->seal)
                    <img src="{{ asset('storage/' . $globalSettings->seal) }}" alt="ختم المدرسة الرسمي" class="seal-img">
                @endif
                <p>مدير المدرسة<br><span style="font-size: 11px; color:#64748b; font-weight:600;">School Principal</span>
                </p>
            </div>
        </div>

        {{-- 🔥 2. الفوتر السفلي (Footer Image) وتمت إزالة الـ no-print لضمان طباعته 🔥 --}}
        @if (isset($globalSettings) && $globalSettings->contract_footer)
            <div class="print-banner footer-banner">
                <img src="{{ asset('storage/' . $globalSettings->contract_footer) }}" alt="معلومات الاتصال بالمدارس">
            </div>
        @endif

    </div>
@endsection
