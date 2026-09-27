<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>مخالصة نهاية الخدمة | {{ $eos->employee->full_name_ar ?? 'موظف' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        @page {
            size: A4;
            margin: 0;
        }

        :root {
            --primary: #2563eb;
            --primary-light: #dbeafe;
            --success: #10b981;
            --success-light: #d1fae5;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --warning: #f59e0b;
            --dark: #1e293b;
            --gray: #64748b;
            --light: #f8fafc;
            --border: #e2e8f0;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Arial, sans-serif;
            background-color: #e2e8f0;
            margin: 0;
            padding: 20px;
            color: var(--dark);
        }

        /* 🌟 زر الطباعة 🌟 */
        .btn-print {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 14px 30px;
            font-size: 16px;
            font-weight: 800;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-back {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 1000;
            background: white;
            color: var(--gray);
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 14px 25px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: var(--light);
            color: var(--dark);
        }

        /* 🌟 حاوية A4 🌟 */
        .document-container {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            padding: 15mm;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            border-top: 12px solid var(--danger);
        }

        /* 🌟 الهيدر 🌟 */
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--border);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-info {
            flex: 1;
        }

        .company-info-ar {
            text-align: right;
        }

        .company-info-en {
            text-align: left;
            direction: ltr;
        }

        .company-info h2 {
            margin: 0 0 5px 0;
            color: var(--dark);
            font-size: 18px;
            font-weight: 900;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 12px;
            color: var(--gray);
            font-weight: 600;
        }

        .company-logo {
            flex: 0 0 100px;
            text-align: center;
        }

        .company-logo img {
            max-height: 70px;
            width: 100%;
            object-fit: contain;
        }

        /* 🌟 معلومات المستند 🌟 */
        .document-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 12px;
            color: var(--gray);
            font-weight: 700;
        }

        .meta-item {
            background: var(--light);
            padding: 8px 15px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        /* 🌟 عنوان الوثيقة 🌟 */
        .document-title {
            text-align: center;
            margin-bottom: 25px;
            background: linear-gradient(135deg, var(--danger-light) 0%, #fecaca 100%);
            padding: 15px 20px;
            border-radius: 12px;
            border: 1px solid #fca5a5;
        }

        .document-title h3 {
            color: var(--danger);
            font-weight: 900;
            font-size: 20px;
            margin: 0;
        }

        .document-title .subtitle {
            color: #991b1b;
            font-size: 14px;
            margin-top: 5px;
            font-weight: 600;
        }

        /* 🌟 الجداول 🌟 */
        .info-table,
        .financial-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .info-table td {
            padding: 12px 15px;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
        }

        .info-table td.label {
            background-color: var(--light);
            color: var(--gray);
            width: 18%;
            font-weight: 700;
            font-size: 13px;
        }

        .financial-table th {
            background-color: var(--light);
            color: var(--gray);
            text-align: right;
            font-size: 12px;
            font-weight: 800;
            padding: 14px;
            text-transform: uppercase;
            border-bottom: 2px solid var(--border);
        }

        .financial-table td {
            padding: 14px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        .font-monospace {
            font-family: 'Courier New', Courier, monospace !important;
            font-weight: 800;
        }

        .amount-positive {
            color: var(--success);
        }

        .amount-negative {
            color: var(--danger);
        }

        /* 🌟 صندوق الصافي 🌟 */
        .net-box {
            background: linear-gradient(135deg, var(--success-light) 0%, #ecfdf5 100%);
            border: 2px solid var(--success);
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
            border-radius: 12px;
        }

        .net-box .label {
            color: #065f46;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .net-box .amount {
            color: var(--success);
            font-size: 32px;
            font-weight: 900;
            font-family: 'Courier New', monospace;
        }

        .net-box .currency {
            font-size: 16px;
            color: var(--gray);
            margin-right: 5px;
        }

        /* 🌟 معلومات إضافية 🌟 */
        .additional-info {
            background: var(--primary-light);
            border: 1px solid #93c5fd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .additional-info h4 {
            color: var(--primary);
            font-size: 14px;
            font-weight: 800;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 13px;
        }

        .info-row .label {
            color: var(--gray);
            font-weight: 600;
        }

        .info-row .value {
            color: var(--dark);
            font-weight: 700;
        }

        /* 🌟 الإقرار 🌟 */
        .declaration {
            background: var(--light);
            border-right: 5px solid var(--primary);
            padding: 15px;
            margin-bottom: 30px;
            line-height: 1.8;
            font-weight: 600;
            font-size: 13px;
            border-radius: 4px;
            text-align: justify;
        }

        /* 🌟 التوقيعات 🌟 */
        .footer-signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
        }

        .signature-box {
            width: 40%;
            text-align: center;
            position: relative;
            min-height: 100px;
        }

        .signature-box .title {
            font-weight: 700;
            font-size: 13px;
            color: var(--gray);
            margin-bottom: 5px;
        }

        .signature-line {
            border-top: 2px dashed var(--border);
            margin: 50px auto 10px;
            width: 80%;
        }

        .signature-name {
            font-weight: 800;
            color: var(--dark);
            font-size: 14px;
            margin-top: 5px;
        }

        .signature-img {
            max-height: 50px;
            max-width: 150px;
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            mix-blend-mode: multiply;
            opacity: 0.9;
        }

        /* 🌟 الفوتر 🌟 */
        .system-footer {
            text-align: center;
            font-size: 11px;
            color: var(--gray);
            font-weight: 600;
            border-top: 1px dashed var(--border);
            padding-top: 15px;
            margin-top: 30px;
        }

        .system-footer b {
            color: var(--primary);
        }

        /* 🌟 الطباعة 🌟 */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .document-container {
                box-shadow: none;
                padding: 10mm;
                width: 100%;
                min-height: 100vh;
                border-top-width: 8px;
            }

            .btn-print,
            .btn-back {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="btn-print no-print">
        <i class='bx bx-printer'></i>
        طباعة المخالصة
    </button>

    <a href="{{ route('admin.hr.eos.index') }}" class="btn-back no-print">
        <i class='bx bx-arrow-back me-1'></i> العودة للسجل
    </a>

    <div class="document-container">

        {{-- 🌟 الهيدر 🌟 --}}
        <div class="print-header">
            <div class="company-info company-info-ar">
                <h2>{{ $globalSettings->company_name_ar ?? 'إدارة الشركات' }}</h2>
                <p>سجل تجاري: {{ $globalSettings->cr_number ?? '---' }}</p>
                <p>{{ $globalSettings->address_ar ?? 'سلطنة عُمان' }}</p>
            </div>
            <div class="company-logo">
                @if (isset($globalSettings) && $globalSettings->logo)
                    <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo">
                @endif
            </div>
            <div class="company-info company-info-en">
                <h2>{{ $globalSettings->company_name_en ?? 'Company Management' }}</h2>
                <p>CR: {{ $globalSettings->cr_number ?? '---' }}</p>
                <p>{{ $globalSettings->address_en ?? 'Sultanate of Oman' }}</p>
            </div>
        </div>

        {{-- 🌟 معلومات المستند 🌟 --}}
        <div class="document-meta">
            <div class="meta-item">
                <i class='bx bx-calendar me-1'></i>
                تاريخ الإصدار: {{ \Carbon\Carbon::now()->format('Y-m-d') }}
            </div>
            <div class="meta-item">
                <i class='bx bx-hash me-1'></i>
                رقم المخالصة: EOS-{{ $eos->id }}
            </div>
        </div>

        {{-- 🌟 عنوان الوثيقة 🌟 --}}
        <div class="document-title">
            <h3>مخالصة مالية نهائية وإخلاء طرف</h3>
            <div class="subtitle">Final Clearance & End of Service Settlement</div>
        </div>

        {{-- 🌟 معلومات الموظف 🌟 --}}
        <table class="info-table">
            <tr>
                <td class="label">اسم الموظف</td>
                <td>{{ $eos->employee->full_name_ar ?? '---' }}</td>
                <td class="label">الرقم الوظيفي</td>
                <td class="font-monospace">{{ $eos->employee->employee_code ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label">القسم</td>
                <td>{{ $eos->employee->department->name_ar ?? '---' }}</td>
                <td class="label">المسمى الوظيفي</td>
                <td>{{ $eos->employee->job_title ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label">تاريخ التعيين</td>
                <td class="font-monospace">
                    {{ $eos->employee->join_date ? \Carbon\Carbon::parse($eos->employee->join_date)->format('Y-m-d') : '---' }}
                </td>
                <td class="label">تاريخ إنهاء الخدمة</td>
                <td class="font-monospace" style="color: var(--danger);">
                    {{ \Carbon\Carbon::parse($eos->termination_date)->format('Y-m-d') }}
                </td>
            </tr>
        </table>

        {{-- 🌟 معلومات إضافية 🌟 --}}
        <div class="additional-info">
            <h4>
                <i class='bx bx-info-circle'></i>
                معلومات الخدمة
            </h4>
            <div class="info-row">
                <span class="label">سبب إنهاء الخدمة:</span>
                <span class="value">{{ $eos->reason_text }}</span>
            </div>
            <div class="info-row">
                <span class="label">مدة الخدمة:</span>
                <span class="value">{{ $eos->service_years_text }}</span>
            </div>
        </div>

        {{-- 🌟 الجدول المالي 🌟 --}}
        <table class="financial-table">
            <thead>
                <tr>
                    <th style="width: 75%;">البيان</th>
                    <th style="text-align: left; direction: ltr;">المبلغ (ر.ع)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>مكافأة نهاية الخدمة المستحقة</td>
                    <td style="text-align: left; direction: ltr;">
                        <span class="font-monospace amount-positive">
                            + {{ number_format($eos->gratuity_amount ?? 0, 3) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>بدل رصيد الإجازات غير المستخدمة</td>
                    <td style="text-align: left; direction: ltr;">
                        <span class="font-monospace amount-positive">
                            + {{ number_format($eos->leave_encashment ?? 0, 3) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>خصم السلف والقروض المتبقية</td>
                    <td style="text-align: left; direction: ltr;">
                        <span class="font-monospace amount-negative">
                            - {{ number_format($eos->loan_deductions ?? 0, 3) }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- 🌟 صندوق الصافي 🌟 --}}
        <div class="net-box">
            <div class="label">صافي المبلغ النهائي المستحق للدفع</div>
            <div class="amount">
                <span class="currency">ر.ع</span>
                {{ number_format($eos->net_payout ?? 0, 3) }}
            </div>
        </div>

        {{-- 🌟 الإقرار 🌟 --}}
        <div class="declaration">
            أقر أنا الموقع أدناه، بأنني استلمت كافة مستحقاتي المالية والنظامية عن فترة عملي بالشركة،
            بما في ذلك راتب الشهر الأخير ومكافأة نهاية الخدمة وبدل الإجازات، وبذلك أبرئ ذمة الشركة
            إبراءً تاماً شاملاً نهائياً لا رجعة فيه من أي مطالبات مالية أو قانونية مستقبلية.
        </div>

        {{-- 🌟 التوقيعات 🌟 --}}
        <div class="footer-signatures">
            <div class="signature-box">
                <div class="title">توقيع الموظف (المُقر بما فيه)</div>
                @if ($eos->signature_path)
                    <img src="{{ Str::startsWith($eos->signature_path, 'data:image') ? $eos->signature_path : asset('storage/' . $eos->signature_path) }}"
                        class="signature-img" alt="توقيع الموظف">
                @endif
                <div class="signature-line"></div>
                <div class="signature-name">{{ $eos->employee->full_name_ar }}</div>
            </div>

            <div class="signature-box">
                <div class="title">اعتماد إدارة الموارد البشرية والمالية</div>
                <div class="signature-line"></div>
                <div class="signature-name">HR & Finance Approval</div>
            </div>
        </div>

        {{-- 🌟 الفوتر 🌟 --}}
        <div class="system-footer">
            تم إصدار هذه الوثيقة بواسطة نظام <b>Smart HR</b> لإدارة الموارد البشرية - © {{ date('Y') }}
            <br>
            <small style="color: #94a3b8;">هذا المستند صادر إلكترونياً ويعتبر وثيقة رسمية</small>
        </div>

    </div>

</body>

</html>
