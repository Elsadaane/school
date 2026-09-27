<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إذن تحويل بين الفروع - {{ $transfer->transfer_number }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            background: #fff;
            color: #000;
        }

        .print-header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000 !important;
            padding: 8px;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
            font-weight: bold;
        }

        .signatures {
            margin-top: 60px;
        }

        .signature-box {
            text-align: center;
            margin-bottom: 20px;
        }

        .signature-line {
            border-top: 1px dashed #000;
            width: 80%;
            margin: 40px auto 10px;
        }

        /* إخفاء أي شيء غير ضروري عند الطباعة */
        @media print {
            @page {
                margin: 1cm;
                size: A4 portrait;
            }

            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container mt-4">
        {{-- الهيدر --}}
        <div class="row print-header align-items-center">
            <div class="col-4">
                {{-- ممكن تحط اللوجو هنا لو حابب --}}
                <h4 class="fw-bold mb-0">مدارس الطالب الذكي</h4>
                <small>قسم إدارة المخازن والفروع</small>
            </div>
            <div class="col-4 text-center">
                <h3 class="fw-bold text-decoration-underline mb-0">إذن تحويل عُهد / منتجات</h3>
            </div>
            <div class="col-4 text-start">
                <p class="mb-1 fw-bold">رقم الإذن: <span dir="ltr">{{ $transfer->transfer_number }}</span></p>
                <p class="mb-0">التاريخ: {{ \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') }}</p>
            </div>
        </div>

        {{-- تفاصيل الفروع --}}
        <div class="row mb-4">
            <div class="col-6">
                <div class="border p-2 rounded" style="background-color: #fcfcfc;">
                    <p class="mb-1 fw-bold text-decoration-underline">من الفرع (المُرسِل):</p>
                    <p class="mb-0 fs-5 fw-bold">{{ $transfer->fromBranch->name_ar ?? '-' }}</p>
                </div>
            </div>
            <div class="col-6">
                <div class="border p-2 rounded" style="background-color: #fcfcfc;">
                    <p class="mb-1 fw-bold text-decoration-underline">إلى الفرع (المُستقبِل):</p>
                    <p class="mb-0 fs-5 fw-bold">{{ $transfer->toBranch->name_ar ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- جدول الأصناف --}}
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">كود الصنف (SKU)</th>
                    <th style="width: 55%;">اسم الصنف / المنتج</th>
                    <th style="width: 20%;">الكمية المحولة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transfer->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td dir="ltr">{{ $item->product->sku ?? '---' }}</td>
                        <td class="text-start fw-bold">
                            {{ $item->product->name_ar ?? $item->product->name_en }}
                        </td>
                        <td class="fw-bold fs-5">{{ $item->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- الملاحظات --}}
        @if ($transfer->notes)
            <div class="mt-3">
                <p class="fw-bold mb-1">ملاحظات / سبب التحويل:</p>
                <p class="border p-2 rounded" style="min-height: 50px;">{{ $transfer->notes }}</p>
            </div>
        @endif

        {{-- التوقيعات --}}
        <div class="row signatures">
            <div class="col-4 signature-box">
                <p class="fw-bold">مسؤول الفرع المُرسِل</p>
                <div class="signature-line"></div>
                <p>الاسم: ........................</p>
                <p>التوقيع: ........................</p>
            </div>
            <div class="col-4 signature-box">
                <p class="fw-bold">توقيع الناقل / المندوب</p>
                <div class="signature-line"></div>
                <p>الاسم: ........................</p>
                <p>التوقيع: ........................</p>
            </div>
            <div class="col-4 signature-box">
                <p class="fw-bold">مسؤول الفرع المُستلم</p>
                <div class="signature-line"></div>
                <p>الاسم: ........................</p>
                <p>التوقيع: ........................</p>
            </div>
        </div>

        <div class="text-center mt-5">
            <small class="text-muted">طُبع بواسطة: {{ auth()->user()->name ?? 'النظام' }} | في تاريخ:
                {{ now()->format('Y-m-d H:i') }}</small>
        </div>
    </div>
</body>

</html>
