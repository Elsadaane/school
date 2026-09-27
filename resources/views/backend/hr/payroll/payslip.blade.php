<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>مفردات راتب | {{ $payroll->employee->full_name_ar ?? 'موظف' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

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
            --warning-light: #fef3c7;
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
        .payslip-container {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            padding: 15mm;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            border-top: 12px solid var(--primary);
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

        /* 🌟 عنوان الوثيقة 🌟 */
        .document-title {
            text-align: center;
            margin-bottom: 25px;
            background: linear-gradient(135deg, var(--primary-light) 0%, #eff6ff 100%);
            padding: 15px 20px;
            border-radius: 12px;
            border: 1px solid #bfdbfe;
        }

        .document-title h3 {
            color: var(--primary);
            font-weight: 900;
            font-size: 20px;
            margin: 0;
        }

        .document-title .month-badge {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            margin-top: 8px;
        }

        /* 🌟 معلومات الموظف 🌟 */
        .emp-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .emp-info td {
            padding: 12px 15px;
            font-weight: 600;
            font-size: 13px;
            border-bottom: 1px solid var(--border);
        }

        .emp-info td.label {
            background-color: var(--light);
            color: var(--gray);
            width: 18%;
            font-weight: 700;
            font-size: 12px;
        }

        .emp-info td.value {
            color: var(--dark);
            font-weight: 800;
        }

        .emp-info tr:last-child td {
            border-bottom: none;
        }

        /* 🌟 جدول البنود 🌟 */
        .salary-section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: 800;
            font-size: 14px;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title.allowances {
            background: var(--success-light);
            color: #065f46;
            border: 1px solid #6ee7b7;
            border-bottom: none;
        }

        .section-title.deductions {
            background: var(--danger-light);
            color: #991b1b;
            border: 1px solid #fca5a5;
            border-bottom: none;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .salary-table th {
            background-color: var(--light);
            color: var(--gray);
            text-align: right;
            font-size: 12px;
            font-weight: 700;
            padding: 10px 15px;
            border: 1px solid var(--border);
            text-transform: uppercase;
        }

        .salary-table td {
            padding: 10px 15px;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid var(--border);
        }

        .salary-table td.amount {
            text-align: left;
            direction: ltr;
            font-family: 'Courier New', monospace;
            font-weight: 800;
            font-size: 14px;
        }

        .salary-table tr.allowance-row td.amount {
            color: var(--success);
        }

        .salary-table tr.deduction-row td.amount {
            color: var(--danger);
        }

        /* 🌟 ملخص الراتب 🌟 */
        .summary-box {
            background: var(--light);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed var(--border);
            font-size: 14px;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row .label {
            color: var(--gray);
            font-weight: 600;
        }

        .summary-row .value {
            font-family: 'Courier New', monospace;
            font-weight: 800;
            font-size: 14px;
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

        /* 🌟 معلومات الدفع 🌟 */
        .payment-info {
            background: var(--primary-light);
            border: 1px solid #93c5fd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .payment-info h4 {
            color: var(--primary);
            font-size: 14px;
            font-weight: 800;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-info .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 13px;
        }

        .payment-info .info-row .label {
            color: var(--gray);
            font-weight: 600;
        }

        .payment-info .info-row .value {
            color: var(--dark);
            font-weight: 700;
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

        .signature-name {
            font-weight: 800;
            color: var(--dark);
            font-size: 14px;
            margin-top: 5px;
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

            .payslip-container {
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
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
            <path
                d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z" />
        </svg>
        طباعة المفردات
    </button>

    <a href="{{ route('admin.hr.payroll.index') }}" class="btn-back no-print">
        ← العودة لمسير الرواتب
    </a>

    <div class="payslip-container">

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

        {{-- 🌟 عنوان الوثيقة 🌟 --}}
        <div class="document-title">
            <h3>كشف مفردات الراتب الشهري</h3>
            <span class="month-badge">
                شهر
                {{ ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$payroll->month - 1] }}
                {{ $payroll->year }}
            </span>
        </div>

        {{-- 🌟 معلومات الموظف 🌟 --}}
        <table class="emp-info">
            <tr>
                <td class="label">اسم الموظف</td>
                <td class="value">{{ $payroll->employee->full_name_ar ?? '---' }}</td>
                <td class="label">الرقم الوظيفي</td>
                <td class="value" style="font-family: monospace;">{{ $payroll->employee->employee_code ?? '---' }}
                </td>
            </tr>
            <tr>
                <td class="label">القسم</td>
                <td class="value">{{ $payroll->employee->department->name_ar ?? '---' }}</td>
                <td class="label">المسمى الوظيفي</td>
                <td class="value">{{ $payroll->employee->job_title ?? '---' }}</td>
            </tr>
            <tr>
                <td class="label">الجنسية</td>
                <td class="value">{{ $payroll->employee->nationality ?? '---' }}</td>
                <td class="label">تاريخ التعيين</td>
                <td class="value">
                    {{ $payroll->employee->join_date ? \Carbon\Carbon::parse($payroll->employee->join_date)->format('Y-m-d') : '---' }}
                </td>
            </tr>
        </table>

        {{-- 🌟 معلومات الحضور 🌟 --}}
        <div class="summary-box">
            <div class="summary-row">
                <span class="label">أيام العمل الفعلية</span>
                <span class="value">{{ $payroll->working_days ?? 30 }} يوم</span>
            </div>
            <div class="summary-row">
                <span class="label">أيام الغياب</span>
                <span class="value" style="color: var(--danger);">{{ $payroll->absent_days ?? 0 }} يوم</span>
            </div>
        </div>

        {{-- 🌟 الاستحقاقات 🌟 --}}
        <div class="salary-section">
            <div class="section-title allowances">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path
                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                </svg>
                الاستحقاقات والإضافات
            </div>
            <table class="salary-table">
                <thead>
                    <tr>
                        <th style="width: 75%;">البيان</th>
                        <th style="text-align: left; direction: ltr;">المبلغ (ر.ع)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="allowance-row">
                        <td>الراتب الأساسي</td>
                        <td class="amount">{{ number_format($payroll->basic_salary, 3) }}</td>
                    </tr>

                    @if (isset($payroll->details) && $payroll->details->where('type', 'allowance')->count() > 0)
                        @foreach ($payroll->details->where('type', 'allowance') as $detail)
                            @if ($detail->item_name !== 'الراتب الأساسي')
                                <tr class="allowance-row">
                                    <td>{{ $detail->item_name }}</td>
                                    <td class="amount">+{{ number_format($detail->amount, 3) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @else
                        @if ($payroll->total_allowances > 0)
                            <tr class="allowance-row">
                                <td>إجمالي البدلات والعلاوات</td>
                                <td class="amount">+{{ number_format($payroll->total_allowances, 3) }}</td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>
        </div>

        {{-- 🌟 الخصومات 🌟 --}}
        <div class="salary-section">
            <div class="section-title deductions">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z" />
                </svg>
                الخصومات والاستقطاعات
            </div>
            <table class="salary-table">
                <thead>
                    <tr>
                        <th style="width: 75%;">البيان</th>
                        <th style="text-align: left; direction: ltr;">المبلغ (ر.ع)</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($payroll->pasi_deduction > 0)
                        <tr class="deduction-row">
                            <td>التأمينات الاجتماعية (7%)</td>
                            <td class="amount">-{{ number_format($payroll->pasi_deduction, 3) }}</td>
                        </tr>
                    @endif

                    @if (isset($payroll->details) && $payroll->details->where('type', 'deduction')->count() > 0)
                        @foreach ($payroll->details->where('type', 'deduction') as $detail)
                            <tr class="deduction-row">
                                <td>{{ $detail->item_name }}</td>
                                <td class="amount">-{{ number_format($detail->amount, 3) }}</td>
                            </tr>
                        @endforeach
                    @else
                        @if ($payroll->total_deductions > 0)
                            <tr class="deduction-row">
                                <td>إجمالي الخصومات</td>
                                <td class="amount">-{{ number_format($payroll->total_deductions, 3) }}</td>
                            </tr>
                        @endif
                    @endif

                    @if ($payroll->loan_deduction > 0)
                        <tr class="deduction-row">
                            <td>أقساط السلف والقروض</td>
                            <td class="amount">-{{ number_format($payroll->loan_deduction, 3) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- 🌟 صندوق الصافي 🌟 --}}
        <div class="net-box">
            <div class="label">صافي الراتب المستحق</div>
            <div class="amount">
                <span class="currency">ر.ع</span>
                {{ number_format($payroll->net_salary, 3) }}
            </div>
        </div>

        {{-- 🌟 معلومات الدفع 🌟 --}}
        @if ($payroll->status === 'paid')
            <div class="payment-info">
                <h4>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                        viewBox="0 0 16 16">
                        <path
                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v1H0V4zm0 3v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7H0zm3 2h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1z" />
                    </svg>
                    معلومات الصرف
                </h4>
                <div class="info-row">
                    <span class="label">تاريخ الصرف:</span>
                    <span
                        class="value">{{ $payroll->payment_date ? \Carbon\Carbon::parse($payroll->payment_date)->format('Y-m-d') : '---' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">حساب الصرف:</span>
                    <span class="value">{{ $payroll->financialAccount->name_ar ?? '---' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">تم الصرف بواسطة:</span>
                    <span class="value">{{ $payroll->paidBy->name ?? '---' }}</span>
                </div>
            </div>
        @endif

        {{-- 🌟 التوقيعات 🌟 --}}
        <div class="footer-signatures">
            <div class="signature-box">
                <div class="title">توقيع الموظف المستلم</div>
                @if ($payroll->signature_path)
                    @if (\Illuminate\Support\Str::endsWith($payroll->signature_path, '.pdf'))
                        <div
                            style="position: absolute; top: 30px; width: 100%; text-align: center; color: var(--success); font-size: 11px; font-weight: 700;">
                            (تم إرفاق إيصال تحويل بنكي)
                        </div>
                    @else
                        <img src="{{ Str::startsWith($payroll->signature_path, 'data:image') ? $payroll->signature_path : asset('storage/' . $payroll->signature_path) }}"
                            class="signature-img" alt="توقيع الموظف">
                    @endif
                @endif
                <div class="signature-line"></div>
                <div class="signature-name">{{ $payroll->employee->full_name_ar }}</div>
            </div>

            <div class="signature-box">
                <div class="title">اعتماد المدير / المحاسب</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $payroll->paidBy->name ?? '---' }}</div>
            </div>
        </div>

        {{-- 🌟 الفوتر 🌟 --}}
        <div class="system-footer">
            تم إصدار هذه الوثيقة بواسطة نظام <b>Smart HR</b> لإدارة الموارد البشرية - © {{ date('Y') }}
            <br>
            <small style="color: #94a3b8;">هذا المستند صادر إلكترونياً ولا يحتاج إلى توقيع أو ختم</small>
        </div>

    </div>

</body>

</html>
