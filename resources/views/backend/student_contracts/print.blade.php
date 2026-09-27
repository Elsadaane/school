<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة عقد | {{ $contract->contract_number }}</title>

    {{-- خط Cairo للمطبوعات الرسمية --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* 🌟 تنسيقات العرض على الشاشة 🌟 */
        body {
            font-family: 'Cairo', 'Times New Roman', Tahoma, Arial, sans-serif;
            background-color: #e2e8f0;
            margin: 0;
            padding: 20px;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* 🌟 حاوية A4 بالطول 🌟 */
        .print-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 15mm;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            border-top: 15px solid #1e293b;
            border-radius: 8px;
        }

        /* 🌟 البانرات 🌟 */
        .header-banner img {
            width: 100%;
            max-height: 130px;
            object-fit: contain;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .footer-banner {
            margin-top: 20px;
            text-align: center;
        }

        .footer-banner img {
            width: 100%;
            max-height: 90px;
            object-fit: contain;
        }

        /* 🌟 الترويسة 🌟 */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .school-logo {
            max-height: 80px;
            width: auto;
            object-fit: contain;
        }

        .header-title {
            text-align: center;
            flex-grow: 1;
        }

        .header-title h2 {
            margin: 0 0 5px 0;
            font-size: 26px;
            font-weight: 900;
            color: #1e293b;
        }

        .header-title p {
            margin: 0;
            font-size: 14px;
            color: #475569;
            font-weight: 700;
        }

        /* 🌟 الجداول والنصوص 🌟 */
        .content-wrapper {
            flex-grow: 1;
        }

        .section-title {
            font-weight: 900;
            font-size: 16px;
            color: #1e293b;
            margin-top: 20px;
            margin-bottom: 10px;
            background: #f1f5f9;
            padding: 8px 15px;
            border-right: 4px solid #3b82f6;
            border-radius: 4px;
        }

        .terms-box {
            font-size: 13px;
            line-height: 1.8;
            text-align: justify;
            color: #334155;
            padding: 0 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }

        table tr {
            page-break-inside: avoid;
        }

        table,
        th,
        td {
            border: 1px solid #cbd5e1;
        }

        th {
            background-color: #f8fafc;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
        }

        td {
            padding: 8px 10px;
            color: #0f172a;
            font-weight: 600;
        }

        .info-table td.label {
            font-weight: 800;
            background-color: #f8fafc;
            width: 20%;
            color: #475569;
        }

        /* 🌟 التوقيعات 🌟 */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding: 0 20px;
            page-break-inside: avoid;
        }

        .sign-box {
            width: 40%;
            text-align: center;
            position: relative;
        }

        .sign-box p {
            margin: 5px 0;
            font-size: 14px;
            font-weight: bold;
            color: #334155;
        }

        .sign-img {
            max-width: 180px;
            max-height: 80px;
            margin-top: 10px;
            mix-blend-mode: multiply;
        }

        .seal-img {
            max-height: 110px;
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0.85;
            mix-blend-mode: multiply;
            z-index: 1;
        }

        /* 🌟 شارة حالة العقد 🌟 */
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

        /* 🌟 قسم المواصلات 🌟 */
        .transport-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .transport-section h4 {
            color: #0369a1;
            font-size: 14px;
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
            font-size: 13px;
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

        /* 🌟 زر الطباعة 🌟 */
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
            background: #2563eb;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Cairo', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        /* 🖨️ تنسيقات الطباعة الصارمة 🖨️ */
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
                padding: 10mm;
                width: 100%;
                border-radius: 0;
                border-top-width: 8px;
            }

            .no-print {
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
        طباعة العقد
    </button>

    <div class="text-center mt-3 mb-2 no-print">
        <a href="{{ route('admin.student-contracts.index') }}"
            style="text-decoration: none; font-weight: bold; color: #64748b;">العودة لسجل العقود</a>
    </div>

    <div class="print-container">

        {{-- 🔥 هيدر العقد (البانر العلوي) من الإعدادات 🔥 --}}
        @if (isset($globalSettings) && $globalSettings->contract_header)
            <div class="header-banner text-center">
                <img src="{{ asset('storage/' . $globalSettings->contract_header) }}" alt="Contract Header">
            </div>
        @endif

        {{-- الترويسة واللوجو --}}
        <div class="header">
            <div style="width: 25%; text-align: right;">
                <p style="margin:0; font-size: 13px; color: #64748b; font-weight: bold;">التاريخ: <span dir="ltr"
                        style="color: #0f172a;">{{ \Carbon\Carbon::parse($contract->contract_date)->format('Y/m/d') }}</span>
                </p>
                <p style="margin:5px 0 0; font-size: 13px; color: #64748b; font-weight: bold;">رقم العقد: <span
                        style="color: #e11d48; font-family: monospace;">{{ $contract->contract_number }}</span>
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

            <div class="header-title">
                <h2>عقد تسجيل وخدمات طالب</h2>
                <p>{{ $globalSettings->company_name_ar ?? 'إدارة المدارس' }}</p>
            </div>

            <div style="width: 25%; text-align: left;">
                @if (isset($globalSettings) && $globalSettings->logo)
                    <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" class="school-logo">
                @endif
            </div>
        </div>

        {{-- محتوى العقد الأساسي --}}
        <div class="content-wrapper">

            <div class="section-title">أطراف التعاقد</div>
            <table class="info-table">
                <tr>
                    <td class="label">الطرف الأول (المدرسة):</td>
                    <td>{{ $globalSettings->company_name_ar ?? 'إدارة المدارس' }}</td>
                    <td class="label">الفرع المٌقيد به:</td>
                    <td>{{ $contract->branch?->name_ar ?? '---' }}</td>
                </tr>
                <tr>
                    <td class="label">الطرف الثاني (ولي الأمر):</td>
                    <td colspan="3"><strong
                            style="font-size: 15px;">{{ $contract->student?->parent?->name_ar ?? '---' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">رقم الهوية:</td>
                    <td>{{ $contract->student?->parent?->national_id ?? '---' }}</td>
                    <td class="label">رقم الهاتف:</td>
                    <td dir="ltr">{{ $contract->student?->parent?->phone ?? '---' }}</td>
                </tr>
                <tr>
                    <td class="label">اسم الطالب/ـة:</td>
                    <td colspan="3"><strong
                            style="font-size: 16px; color: #2563eb;">{{ $contract->student?->first_name ?? '' }}
                            {{ $contract->student?->tribe ?? '' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">العام الدراسي:</td>
                    <td>{{ $contract->academicYear?->name ?? '---' }}</td>
                    <td class="label">الـصـف:</td>
                    <td>{{ $contract->student?->grade?->name_ar ?? '---' }}</td>
                </tr>
            </table>

            {{-- 🆕 جدول بنود الفاتورة التفصيلي --}}
            <div class="section-title">تفاصيل بنود الفاتورة</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="45%">البند</th>
                        <th width="12%">سعر الوحدة</th>
                        <th width="10%">الكمية</th>
                        <th width="13%">الضريبة %</th>
                        <th width="15%">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contract->items as $index => $item)
                        <tr>
                            <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                            <td>
                                @if ($item->product_id)
                                    {{ $item->product?->name_ar ?? 'بند دراسي' }}
                                @else
                                    {{ $item->description ?? 'بند مخصص' }}
                                @endif
                            </td>
                            <td style="text-align: center;">{{ number_format($item->unit_price, 3) }}</td>
                            <td style="text-align: center;">{{ $item->quantity }}</td>
                            <td style="text-align: center;">{{ $item->tax_percent }}%</td>
                            <td style="text-align: center; font-weight: 800;">{{ number_format($item->row_total, 3) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 15px; color: #64748b;">لا توجد بنود
                                مسجلة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- 🆕 قسم خدمات النقل المدرسي (إذا وجدت) --}}
            @if ($contract->transportRoute || $contract->morningBus || $contract->afternoonBus)
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
            @endif

            <div class="section-title">الملخص المالي للعقد</div>
            <table>
                <tr>
                    <th>الإجمالي الفرعي</th>
                    <th>الخصم الممنوح</th>
                    <th>إجمالي الضريبة</th>
                    @if ($contract->previous_debt > 0)
                        <th style="background-color: #fef2f2; color: #991b1b;">المديونية المُرحلة</th>
                    @endif
                    <th style="background-color: #e0f2fe; color: #0369a1;">الإجمالي الشامل (ر.ع)</th>
                </tr>
                <tr style="text-align: center; font-weight: 800;">
                    <td>{{ number_format($contract->sub_total, 3) }}</td>
                    <td style="color: #ef4444;">{{ number_format($contract->discount, 3) }}</td>
                    <td>{{ number_format($contract->tax_amount, 3) }}</td>
                    @if ($contract->previous_debt > 0)
                        <td style="color: #991b1b;">{{ number_format($contract->previous_debt, 3) }}</td>
                    @endif
                    <td style="font-size: 18px; color: #0369a1; font-family: 'Courier New', monospace;">
                        {{ number_format($contract->grand_total, 3) }}</td>
                </tr>
            </table>

            <div class="section-title">جدول الأقساط والحالة المالية</div>
            <table>
                <tr>
                    <th width="5%">#</th>
                    <th width="25%">تاريخ الاستحقاق</th>
                    <th width="20%">قيمة القسط (ر.ع)</th>
                    <th width="20%">المدفوع (ر.ع)</th>
                    <th width="15%">المتبقي (ر.ع)</th>
                    <th width="15%">الحالة</th>
                </tr>
                @forelse ($contract->installments as $index => $inst)
                    @php
                        $remaining = $inst->amount - $inst->paid_amount;
                    @endphp
                    <tr style="text-align: center;">
                        <td style="color: #64748b;">{{ $index + 1 }}</td>
                        <td dir="ltr" style="font-family: 'Courier New', monospace;">
                            {{ \Carbon\Carbon::parse($inst->due_date)->format('Y-m-d') }}</td>
                        <td style="font-weight: 800;">{{ number_format($inst->amount, 3) }}</td>
                        <td style="color: #166534; font-weight: 800;">{{ number_format($inst->paid_amount, 3) }}</td>
                        <td style="color: #991b1b; font-weight: 800;">{{ number_format($remaining, 3) }}</td>
                        <td style="font-weight: bold; font-size: 12px;">
                            @if ($inst->status == 'paid' || $remaining <= 0)
                                <span
                                    style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px;">مسدد</span>
                            @elseif ($inst->paid_amount > 0)
                                <span
                                    style="background: #fef9c3; color: #9a3412; padding: 4px 8px; border-radius: 4px;">جزئي</span>
                            @else
                                <span
                                    style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px;">مستحق</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 15px; color: #64748b;">لا توجد أقساط
                            مسجلة لهذا العقد.</td>
                    </tr>
                @endforelse
            </table>

            <div class="section-title">شروط وأحكام العقد</div>
            <div class="terms-box">
                @if (isset($globalSettings) && !empty($globalSettings->student_contract_terms_ar))
                    {!! $globalSettings->student_contract_terms_ar !!}
                @else
                    <p>1. يوثق هذا العقد العلاقة التربوية والإدارية والمالية بين الطرفين، بما يضمن وضوح الحقوق والواجبات
                        للطرفين خلال فترة دراسة الطالب/ـة في المدرسة.</p>
                    <p>2. يلتزم الطرف الثاني بسداد الرسوم الدراسية في المواعيد المحددة بجدول الأقساط الموضح أعلاه.</p>
                    <p>3. في حال تأخر السداد عن الموعد المحدد، يحق للمدرسة تطبيق غرامة تأخير حسب اللائحة المالية
                        المعتمدة.</p>
                    <p>4. هذا العقد ساري المفعول طوال العام الدراسي {{ $contract->academicYear?->name ?? '---' }}.</p>
                @endif
            </div>

        </div>

        {{-- التوقيعات والختم --}}
        <div>
            <div class="signatures">
                <div class="sign-box">
                    <p style="color: #2563eb;">الطرف الأول (إدارة المدرسة)</p>
                    @if (isset($globalSettings) && $globalSettings->seal)
                        <img src="{{ asset('storage/' . $globalSettings->seal) }}" alt="ختم المدرسة"
                            class="seal-img">
                    @endif
                    <p style="margin-top: 50px; font-weight: normal; font-size: 13px;">الختم الرسمي للمدرسة</p>
                </div>

                <div class="sign-box">
                    <p style="color: #2563eb;">الطرف الثاني (ولي الأمر)</p>
                    <p style="font-size: 13px;">الاسم: {{ $contract->student?->parent?->name_ar ?? '---' }}</p>
                    @if ($contract->guardian_signature)
                        <img src="{{ $contract->guardian_signature }}" class="sign-img" alt="توقيع ولي الأمر">
                        <p style="font-size: 11px; color: #64748b;">(موقع إلكترونياً بتاريخ:
                            {{ \Carbon\Carbon::parse($contract->signed_at)->format('Y/m/d') }})</p>
                    @else
                        <p
                            style="margin-top: 40px; color: #ef4444; border-top: 1px dashed #cbd5e1; padding-top: 10px; width: 80%; margin-left: auto; margin-right: auto;">
                            التوقيع</p>
                    @endif
                </div>
            </div>

            @if (isset($globalSettings) && $globalSettings->contract_footer)
                <div class="footer-banner">
                    <img src="{{ asset('storage/' . $globalSettings->contract_footer) }}" alt="Contract Footer">
                </div>
            @endif
        </div>

    </div>

</body>

</html>
