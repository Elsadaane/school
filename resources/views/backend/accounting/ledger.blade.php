@extends('backend.layouts.master')
@section('title', 'دفتر الأستاذ - ' . $account->name_ar)
@section('title_page', 'النظام المالي - دفتر الأستاذ')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .page-wrapper {
            padding: 25px;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
            border-top: 5px solid #004b87;
        }

        .account-header {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .account-code {
            font-family: 'Courier New', monospace;
            font-weight: 800;
            color: #64748b;
            font-size: 14px;
            background: #fff;
            padding: 4px 12px;
            border-radius: 6px;
            display: inline-block;
        }

        .account-name {
            font-weight: 900;
            color: #1e293b;
            font-size: 24px;
            margin: 10px 0 5px;
        }

        .balance-box {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 2px solid #e2e8f0;
        }

        .balance-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .balance-value {
            font-family: 'Courier New', monospace;
            font-weight: 900;
            font-size: 28px;
        }

        .table-ledger thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 800;
            padding: 14px;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-ledger tbody td {
            padding: 12px 14px;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .amount-debit {
            color: #10b981;
            font-weight: 800;
            font-family: 'Courier New', monospace;
        }

        .amount-credit {
            color: #ef4444;
            font-weight: 800;
            font-family: 'Courier New', monospace;
        }

        .amount-balance {
            color: #2563eb;
            font-weight: 900;
            font-family: 'Courier New', monospace;
            font-size: 15px;
        }

        .summary-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 900;
            font-size: 16px;
            color: #2563eb;
            padding-top: 15px;
            margin-top: 5px;
            border-top: 2px solid #3b82f6;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- زر العودة --}}
        <div class="mb-3">
            <a href="{{ route('admin.financial-accounts.index') }}" class="btn btn-light border rounded-pill px-4">
                <i class='bx bx-arrow-back me-1'></i> العودة للحسابات
            </a>
        </div>

        {{-- معلومات الحساب --}}
        <div class="account-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="account-code">{{ $account->account_code }}</span>
                    <h2 class="account-name">{{ $account->name_ar }}</h2>
                    @if ($account->name_en)
                        <p class="text-muted mb-0" dir="ltr">{{ $account->name_en }}</p>
                    @endif
                </div>
                <div class="col-md-4">
                    <div class="balance-box">
                        <div class="balance-label">الرصيد الحالي</div>
                        <div class="balance-value text-primary">
                            {{ number_format($account->balance, 3) }} <small>ر.ع</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- فلاتر التاريخ --}}
        <div class="card-premium mb-4">
            <div class="card-body p-4">
                <form method="GET" class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">من تاريخ</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">إلى تاريخ</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-dark w-100 fw-bold py-2">
                            <i class='bx bx-filter-alt me-1'></i> تصفية
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- جدول الحركات --}}
        <div class="card-premium">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class='bx bx-book-open text-primary me-2'></i>
                    كشف حركات الحساب
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-ledger table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th style="width: 12%;">التاريخ</th>
                                <th style="width: 12%;">المرجع</th>
                                <th style="width: 30%;">البيان</th>
                                <th style="width: 12%;" class="text-end">مدين</th>
                                <th style="width: 12%;" class="text-end">دائن</th>
                                <th style="width: 12%;" class="text-end">الرصيد</th>
                                <th style="width: 10%;">المستخدم</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- صف الرصيد الافتتاحي --}}
                            <tr style="background: #f8fafc;">
                                <td colspan="3" class="fw-bold text-muted">
                                    <i class='bx bx-info-circle me-1'></i> الرصيد الافتتاحي
                                </td>
                                <td></td>
                                <td></td>
                                <td class="text-end amount-balance">
                                    {{ number_format($account->opening_balance ?? 0, 3) }}
                                </td>
                                <td></td>
                            </tr>

                            @forelse($transactions as $trans)
                                <tr>
                                    <td class="font-monospace">{{ \Carbon\Carbon::parse($trans['date'])->format('Y-m-d') }}
                                    </td>
                                    <td class="font-monospace small">{{ $trans['reference'] ?? '-' }}</td>
                                    <td>{{ $trans['description'] ?? '-' }}</td>
                                    <td class="text-end">
                                        @if ($trans['debit'] > 0)
                                            <span class="amount-debit">{{ number_format($trans['debit'], 3) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if ($trans['credit'] > 0)
                                            <span class="amount-credit">{{ number_format($trans['credit'], 3) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end amount-balance">
                                        {{ number_format($trans['balance'], 3) }}
                                    </td>
                                    <td class="small text-muted">{{ $trans['user'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class='bx bx-book-open opacity-25' style="font-size: 60px;"></i>
                                        <h5 class="mt-3 fw-bold">لا توجد حركات في الفترة المحددة</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (count($transactions) > 0)
                            <tfoot style="background: #f8fafc;">
                                <tr>
                                    <td colspan="3" class="fw-bold text-end">الإجماليات:</td>
                                    <td class="text-end amount-debit fw-bold">
                                        {{ number_format($totalDebit, 3) }}
                                    </td>
                                    <td class="text-end amount-credit fw-bold">
                                        {{ number_format($totalCredit, 3) }}
                                    </td>
                                    <td class="text-end amount-balance">
                                        {{ number_format($runningBalance, 3) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- ملخص الإحصائيات --}}
        @if (count($transactions) > 0)
            <div class="row g-4 mt-3">
                <div class="col-md-4">
                    <div class="summary-card">
                        <h6 class="fw-bold text-success mb-3">
                            <i class='bx bx-trending-up me-1'></i> إجمالي المدين
                        </h6>
                        <div class="fs-3 fw-bold font-monospace text-success">
                            {{ number_format($totalDebit, 3) }} <small>ر.ع</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <h6 class="fw-bold text-danger mb-3">
                            <i class='bx bx-trending-down me-1'></i> إجمالي الدائن
                        </h6>
                        <div class="fs-3 fw-bold font-monospace text-danger">
                            {{ number_format($totalCredit, 3) }} <small>ر.ع</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class='bx bx-wallet me-1'></i> الرصيد النهائي
                        </h6>
                        <div class="fs-3 fw-bold font-monospace text-primary">
                            {{ number_format($runningBalance, 3) }} <small>ر.ع</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
