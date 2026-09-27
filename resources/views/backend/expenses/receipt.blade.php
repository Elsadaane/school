<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند صرف - {{ $expense->expense_code }}</title>
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

        /* إعدادات الطباعة لإخفاء أي عناصر غير مرغوبة للـ Browser */
        @media print {
            body {
                padding: 0;
                background: #fff;
            }

            .receipt-container {
                border: 2px solid #000;
                box-shadow: none;
            }

            .btn-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="text-center mb-3">
        <button onclick="window.print();" class="btn btn-dark btn-elegant btn-print px-4 fw-bold shadow-sm">
            <i class="bx bx-printer"></i> اضغط للطباعة الفورية
        </button>
    </div>

    <div class="receipt-container">
        {{-- الهيدر الشيك للمؤسسة --}}
        <div class="receipt-header d-flex justify-content-between align-items-center">
            <div class="text-start">
                <h5 class="fw-bold m-0 text-dark">{{ $expense->branch->name_ar ?? 'الفرع الرئيسي' }}</h5>
                <small class="text-muted">قسم الحسابات والمالية</small>
            </div>
            <div>
                <span class="receipt-title">سند صرف مالي</span>
            </div>
            <div class="text-end text-muted font-monospace" style="font-size: 13px;">
                <div>رقم السند: <span class="text-danger fw-bold">{{ $expense->expense_code }}</span></div>
                <div>التاريخ: {{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}</div>
            </div>
        </div>

        {{-- تفاصيل السند الأساسية --}}
        <div class="row mb-4">
            <div class="col-6">
                <table class="meta-info">
                    <tr>
                        <td class="fw-bold text-secondary">إلى حساب الخزنة:</td>
                        <td class="fw-bold text-dark">{{ $expense->financialAccount->name_ar ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-secondary">الجهة / المورد:</td>
                        <td class="fw-bold text-dark">{{ $expense->supplier->name ?? 'مصروف عام (بدون مورد)' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6 text-end">
                <table class="meta-info ms-auto">
                    <tr>
                        <td class="fw-bold text-secondary">رقم الفاتورة:</td>
                        <td class="font-monospace fw-bold">{{ $expense->supplier_invoice_no ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-secondary">المستخدم المنفذ:</td>
                        <td class="fw-bold text-muted">{{ $expense->user->name ?? 'Admin' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- جدول المبالغ المالي والبيان --}}
        <table class="table table-bordered table-details text-center align-middle">
            <thead>
                <tr>
                    <th width="15%">المبلغ الأساسي</th>
                    <th width="15%">الضريبة المضافة</th>
                    <th width="20%">إجمالي المبلغ المدفوع</th>
                    <th class="text-start">البيان والشرح تفصيلياً</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-monospace fw-bold">{{ number_format($expense->amount, 3) }} ر.ع.</td>
                    <td class="font-monospace text-danger fw-bold">{{ number_format($expense->tax_amount, 3) }} ر.ع.
                    </td>
                    <td class="font-monospace text-success fw-bold fs-5 bg-light">
                        {{ number_format($expense->total_amount, 3) }} ر.ع.</td>
                    <td class="text-start fw-bold text-dark">{{ $expense->transaction_details }}</td>
                </tr>
            </tbody>
        </table>

        @if ($expense->notes)
            <div class="mt-3 p-2 bg-light rounded" style="font-size: 13px; border: 1px solid #ddd;">
                <strong>ملاحظات إضافية:</strong> {{ $expense->notes }}
            </div>
        @endif

        {{-- التوقيعات الرسمية للإخلاء والمسؤولية --}}
        <div class="signature-section d-flex justify-content-between text-center">
            <div>
                <div class="sig-box">توقيع المستلم</div>
            </div>
            <div>
                <div class="sig-box">تنفيذ المحاسب</div>
            </div>
            <div>
                <div class="sig-box">اعتماد الإدارة المالي</div>
            </div>
        </div>
    </div>

    {{-- أمر تشغيل الطباعة التلقائي بمجرد التحميل --}}
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        }
    </script>
</body>

</html>
