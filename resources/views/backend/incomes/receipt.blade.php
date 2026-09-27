<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند قبض مالي - {{ $income->income_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background-color: #fff;
            padding: 30px;
        }

        .receipt-container {
            border: 2px solid #333;
            padding: 30px;
            border-radius: 8px;
            position: relative;
            max-width: 850px;
            margin: 0 auto;
        }

        .receipt-header {
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: 800;
            border: 2px solid #333;
            padding: 5px 25px;
            border-radius: 4px;
            background-color: #f8fafc;
        }

        .meta-info td {
            padding: 4px 8px;
            font-size: 14px;
        }

        .table-details {
            margin-top: 20px;
            border: 1px solid #333;
        }

        .table-details th {
            background-color: #f1f5f9;
            color: #000;
            border: 1px solid #333;
            font-weight: 700;
        }

        .table-details td {
            border: 1px solid #333;
            padding: 12px;
        }

        .signature-section {
            margin-top: 50px;
        }

        .sig-box {
            border-top: 1px dashed #333;
            width: 180px;
            margin-top: 40px;
            display: inline-block;
            font-weight: bold;
        }

        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .receipt-container {
                border: 2px solid #000;
                box-shadow: none;
                margin-top: 0;
            }

            .btn-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="text-center mb-3">
        <button onclick="window.print();" class="btn btn-dark btn-print px-4 fw-bold shadow-sm">
            <i class="bx bx-printer"></i> اضغط للطباعة الفورية
        </button>
    </div>

    <div class="receipt-container">
        {{-- الهيدر --}}
        <div class="receipt-header d-flex justify-content-between align-items-center">
            <div class="text-start">
                <h5 class="fw-bold m-0 text-dark">{{ $income->branch->name_ar ?? 'الفرع الرئيسي' }}</h5>
                <small class="text-muted">قسم الحسابات والمالية</small>
            </div>
            <div>
                <span class="receipt-title">سند قبض مالي</span>
            </div>
            <div class="text-end text-muted font-monospace" style="font-size: 13px;">
                <div>رقم السند: <span class="text-success fw-bold">{{ $income->income_code }}</span></div>
                <div>التاريخ: {{ \Carbon\Carbon::parse($income->income_date)->format('Y-m-d') }}</div>
            </div>
        </div>

        {{-- تفاصيل السند --}}
        <div class="row mb-4">
            <div class="col-6">
                <table class="meta-info">
                    <tr>
                        <td class="fw-bold text-secondary">إيداع إلى خزنة:</td>
                        <td class="fw-bold text-dark">{{ $income->financialAccount->name_ar ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-secondary">الجهة المُوردة:</td>
                        <td class="fw-bold text-dark">{{ $income->source_name ?? 'إيراد عام' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6 text-end">
                <table class="meta-info ms-auto">
                    <tr>
                        <td class="fw-bold text-secondary">رقم المرجع/الفاتورة:</td>
                        <td class="font-monospace fw-bold">{{ $income->reference_no ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-secondary">المستخدم المنفذ:</td>
                        <td class="fw-bold text-muted">{{ $income->user->name ?? 'Admin' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- جدول التفاصيل المالية --}}
        <table class="table table-bordered table-details text-center align-middle">
            <thead>
                <tr>
                    <th width="15%">المبلغ الأساسي</th>
                    <th width="15%">الضريبة (إن وجدت)</th>
                    <th width="20%">إجمالي المبلغ المُستلم</th>
                    <th class="text-start">البيان والتفاصيل</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-monospace fw-bold">{{ number_format($income->amount, 3) }}</td>
                    <td class="font-monospace text-danger fw-bold">{{ number_format($income->tax_amount, 3) }}</td>
                    <td class="font-monospace text-success fw-bold fs-5 bg-light">
                        {{ number_format($income->total_amount, 3) }}</td>
                    <td class="text-start fw-bold text-dark">{{ $income->transaction_details }}</td>
                </tr>
            </tbody>
        </table>

        @if ($income->notes)
            <div class="mt-3 p-2 bg-light rounded" style="font-size: 13px; border: 1px solid #ddd;">
                <strong>ملاحظات إضافية:</strong> {{ $income->notes }}
            </div>
        @endif

        {{-- التوقيعات --}}
        <div class="signature-section d-flex justify-content-between text-center">
            <div>
                <div class="sig-box">توقيع المورد / المُسلم</div>
            </div>
            <div>
                <div class="sig-box">استلام المحاسب</div>
            </div>
            <div>
                <div class="sig-box">اعتماد الإدارة المالي</div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        }
    </script>
</body>

</html>
