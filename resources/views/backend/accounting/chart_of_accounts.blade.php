@extends('backend.layouts.master')
@section('title', __('accounting.chart_of_accounts') ?? 'شجرة الحسابات | Chart of Accounts')
@section('title_page', __('accounting.chart_of_accounts') ?? 'الدليل المحاسبي والمركز المالي')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .page-wrapper {
            background-color: #f4f6f9;
            padding: 20px;
            font-family: 'Tajawal', sans-serif;
        }

        .finance-card {
            border: none;
            border-radius: 12px;
            color: #fff;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .finance-card:hover {
            transform: translateY(-5px);
        }

        .finance-card .card-body {
            padding: 25px;
            z-index: 2;
            position: relative;
        }

        .finance-card h6,
        .finance-card h3,
        .finance-card span {
            color: #ffffff !important;
        }

        .finance-icon {
            position: absolute;
            left: -10px;
            bottom: -20px;
            font-size: 100px;
            opacity: 0.15;
            transform: rotate(15deg);
            color: #fff;
        }

        .bg-revenue {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
        }

        .bg-expense {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
        }

        .bg-profit {
            background: linear-gradient(135deg, #2980b9, #3498db);
        }

        .bg-equity {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }

        .tree-container {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        }

        .tree-list {
            list-style: none;
            padding-right: 20px;
            border-right: 1px dashed #ced4da;
            margin-top: 10px;
        }

        .tree-list.root-list {
            border-right: none;
            padding-right: 0;
            margin-top: 0;
        }

        .tree-item {
            margin-bottom: 8px;
            position: relative;
        }

        .tree-item::before {
            content: '';
            position: absolute;
            top: 15px;
            right: -20px;
            width: 20px;
            height: 1px;
            border-top: 1px dashed #ced4da;
        }

        .root-list>.tree-item::before {
            display: none;
        }

        .account-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            cursor: pointer;
            transition: 0.2s;
        }

        .account-box:hover {
            background: #eef2f5;
        }

        .account-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .account-code {
            background: #2c3e50;
            color: #fff;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .account-name {
            font-weight: bold;
            color: #34495e;
            font-size: 15px;
        }

        .account-balance {
            font-weight: bold;
            color: #27ae60;
            font-size: 14px;
        }

        .toggle-icon {
            font-size: 20px;
            color: #7f8c8d;
            transition: transform 0.3s;
        }

        .collapsed>.account-box .toggle-icon {
            transform: rotate(180deg);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 1. لوحة المركز المالي السريعة (مبنية على السنة الأولى المختارة) --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
            <div class="col">
                <div class="card finance-card bg-revenue">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 opacity-75">إيرادات سنة ({{ $year1 }})</h6>
                        <h3 class="mb-0 fw-bolder">{{ number_format($kpis['total_revenues'] ?? 0, 3) }} <span
                                class="fs-6">ر.ع</span></h3>
                        <i class="bx bx-trending-up finance-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card finance-card bg-expense">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 opacity-75">مصروفات سنة ({{ $year1 }})</h6>
                        <h3 class="mb-0 fw-bolder">{{ number_format($kpis['total_expenses'] ?? 0, 3) }} <span
                                class="fs-6">ر.ع</span></h3>
                        <i class="bx bx-trending-down finance-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card finance-card bg-profit">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 opacity-75">صافي ربح ({{ $year1 }})</h6>
                        <h3 class="mb-0 fw-bolder">{{ number_format($kpis['net_profit'] ?? 0, 3) }} <span
                                class="fs-6">ر.ع</span></h3>
                        <i class="bx bx-line-chart finance-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card finance-card bg-equity">
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 opacity-75">الأرباح المرحلة (سنين سابقة)</h6>
                        <h3 class="mb-0 fw-bolder">{{ number_format($kpis['retained_earnings'] ?? 0, 3) }} <span
                                class="fs-6">ر.ع</span></h3>
                        <i class="bx bx-archive-in finance-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. 🔥 رسم بياني للمقارنة بين سنتين 🔥 --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4" style="background: #fff;">
                    <div
                        class="card-header bg-white border-bottom pb-3 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="fw-bold text-dark mb-0"><i
                                class="bx bx-bar-chart-alt-2 text-primary fs-4 align-middle"></i> مقارنة الأداء المالي بين
                            سنتين</h5>

                        {{-- فلتر السنين --}}
                        <form action="{{ route('admin.accounting.chart_of_accounts') ?? '#' }}" method="GET"
                            class="d-flex align-items-center gap-2">
                            <select name="year1" class="form-select form-select-sm fw-bold shadow-sm"
                                style="width: 120px;">
                                @foreach ($availableYears ?? [] as $y)
                                    <option value="{{ $y }}" {{ $year1 == $y ? 'selected' : '' }}>سنة
                                        {{ $y }}</option>
                                @endforeach
                            </select>
                            <span class="text-muted fw-bold">VS</span>
                            <select name="year2" class="form-select form-select-sm fw-bold shadow-sm"
                                style="width: 120px;">
                                @foreach ($availableYears ?? [] as $y)
                                    <option value="{{ $y }}" {{ $year2 == $y ? 'selected' : '' }}>سنة
                                        {{ $y }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-dark fw-bold"><i class="bx bx-filter-alt"></i>
                                قارن</button>
                        </form>
                    </div>
                    <div class="card-body p-4">
                        <div id="profitComparisonChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. شجرة الحسابات (Chart of Accounts Tree) --}}
        <div class="tree-container">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <h5 class="fw-bold text-dark mb-0"><i class="bx bx-sitemap text-primary fs-4 align-middle"></i> الدليل
                    المحاسبي (شجرة الحسابات)</h5>

                <div class="d-flex gap-2">
                    {{-- 🔥 زرار إقفال السنة (محمي بالصلاحية) 🔥 --}}
                    @can('manage_financial_accounts')
                        <button type="button" class="btn btn-danger fw-bold shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#closeYearModal">
                            <i class="bx bx-lock-alt"></i> إقفال السنة المالية ({{ date('Y') }})
                        </button>
                    @endcan

                    {{-- 🔥 زرار إضافة حساب رئيسي (محمي بالصلاحية) 🔥 --}}
                    @can('create_financial_accounts')
                        <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#addAccountModal" id="btn-add-main-account">
                            <i class="bx bx-plus"></i> إضافة حساب رئيسي
                        </button>
                    @endcan
                </div>
            </div>

            <ul class="tree-list root-list">
                @forelse ($accountsTree ?? [] as $account)
                    @include('backend.accounting.partials.account_node', ['account' => $account])
                @empty
                    <li class="text-center text-muted py-4">لا توجد حسابات مالية مضافة حتى الآن.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- الموديلز (Modals) الخاصة بالحسابات والإقفال --}}
    {{-- ======================================================= --}}

    @can('manage_financial_accounts')
        {{-- 1. مودال إقفال السنة المالية --}}
        <div class="modal fade" id="closeYearModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="bx bx-error-circle"></i> تحذير: إقفال السنة المالية</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.accounting.close_year') ?? '#' }}" method="POST">
                        @csrf
                        <div class="modal-body p-4 text-center">
                            <i class="bx bx-error text-warning mb-3" style="font-size: 80px;"></i>
                            <h4 class="fw-bold text-dark">هل أنت متأكد من هذا الإجراء؟</h4>
                            <p class="text-muted mb-4">
                                إقفال السنة المالية سيقوم بـ: <br>
                                1. <b>تصفير</b> جميع أرصدة (الإيرادات والمصروفات).<br>
                                2. ترحيل صافي الربح لحساب <b>(الأرباح المرحلة)</b>.<br>
                                <span class="text-danger fw-bold">هذا الإجراء لا يمكن التراجع عنه نهائياً!</span>
                            </p>
                            <input type="hidden" name="year" value="{{ date('Y') }}">

                            <div class="form-check text-start mt-3 mb-2 px-5">
                                <input class="form-check-input" type="checkbox" id="confirmClose" required>
                                <label class="form-check-label fw-bold text-danger" for="confirmClose">
                                    أقر بأنني قمت بمراجعة كافة القيود وأوافق على الإقفال.
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-center bg-light">
                            <button type="button" class="btn btn-secondary px-4 fw-bold"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger px-4 fw-bold"><i class="bx bx-lock"></i> إقفال السنة
                                الآن</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('create_financial_accounts')
        {{-- 2. مودال إضافة حساب جديد --}}
        <div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold"><i class="bx bx-plus-circle"></i> إضافة حساب محاسبي جديد</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.accounting.store_account') ?? '#' }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">كود الحساب</label>
                                    <input type="text" name="account_code" class="form-control" placeholder="مثال: 1001">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">اسم الحساب (عربي) *</label>
                                    <input type="text" name="name_ar" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">نوع الحساب *</label>
                                    <select name="account_type" class="form-select" required>
                                        <option value="asset">أصول (Assets)</option>
                                        <option value="liability">خصوم (Liabilities)</option>
                                        <option value="equity">حقوق ملكية (Equity)</option>
                                        <option value="revenue">إيرادات (Revenues)</option>
                                        <option value="expense">مصروفات (Expenses)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">الحساب الأب (متفرع من)</label>
                                    <select name="parent_id" id="add_parent_id" class="form-select">
                                        <option value="">-- حساب رئيسي (بدون أب) --</option>
                                        {{-- تم استبدال القائمة المعقدة هنا بمتغير $allAccounts للمرونة السريعة --}}
                                        @foreach ($allAccounts ?? [] as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->account_code }} -
                                                {{ $acc->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">طبيعة الحساب</label>
                                    <select name="normal_balance" class="form-select">
                                        <option value="debit">مدين (Debit)</option>
                                        <option value="credit">دائن (Credit)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">الرصيد الافتتاحي</label>
                                    <input type="number" step="0.001" name="opening_balance" class="form-control"
                                        value="0.000">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary fw-bold"><i class="bx bx-save"></i> حفظ
                                الحساب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('edit_financial_accounts')
        {{-- 3. مودال تعديل الحساب --}}
        <div class="modal fade" id="editAccountModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold"><i class="bx bx-edit"></i> تعديل حساب محاسبي</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="#" method="POST" id="editAccountForm">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">كود الحساب</label>
                                    <input type="text" name="account_code" id="edit_account_code" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">اسم الحساب (عربي) *</label>
                                    <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">نوع الحساب *</label>
                                    <select name="account_type" id="edit_account_type" class="form-select" required>
                                        <option value="asset">أصول (Assets)</option>
                                        <option value="liability">خصوم (Liabilities)</option>
                                        <option value="equity">حقوق ملكية (Equity)</option>
                                        <option value="revenue">إيرادات (Revenues)</option>
                                        <option value="expense">مصروفات (Expenses)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">الرصيد</label>
                                    <input type="number" step="0.001" name="balance" id="edit_balance"
                                        class="form-control bg-light" readonly>
                                    <small class="text-danger">تعديل الرصيد يتم من خلال القيود اليومية فقط.</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success fw-bold"><i class="bx bx-save"></i> تحديث
                                الحساب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function() {
            // تشغيل طي وفتح فروع الشجرة
            $('.account-box').on('click', function(e) {
                if ($(e.target).closest('button').length > 0 || $(e.target).closest('a').length > 0) return;
                let parentLi = $(this).parent('.tree-item');
                let childrenList = parentLi.children('.tree-list');
                if (childrenList.length > 0) {
                    childrenList.slideToggle(200);
                    parentLi.toggleClass('collapsed');
                    let icon = $(this).find('.toggle-icon');
                    if (parentLi.hasClass('collapsed')) {
                        icon.removeClass('bx-chevron-down').addClass('bx-chevron-left');
                    } else {
                        icon.removeClass('bx-chevron-left').addClass('bx-chevron-down');
                    }
                }
            });

            // 🔥 تعبئة بيانات مودال إضافة حساب فرعي تلقائياً 🔥
            $(document).on('click', '.add-sub-account-btn', function() {
                let parentId = $(this).data('parent-id');
                // تصفير الفورم الأول لو كان اليوزر كاتب حاجة
                $('#addAccountModal form')[0].reset();
                if (parentId) {
                    $('#add_parent_id').val(parentId).trigger('change');
                }
            });

            // تصفير الأب لو ضغط على زرار "إضافة حساب رئيسي"
            $('#btn-add-main-account').on('click', function() {
                $('#addAccountModal form')[0].reset();
                $('#add_parent_id').val('').trigger('change');
            });

            // تعبئة بيانات مودال التعديل
            $(document).on('click', '.edit-account-btn', function() {
                let id = $(this).data('id');
                $('#edit_account_code').val($(this).data('code'));
                $('#edit_name_ar').val($(this).data('name'));
                $('#edit_account_type').val($(this).data('type'));
                $('#edit_balance').val($(this).data('balance'));
                $('#editAccountForm').attr('action',
                    "{{ route('admin.accounting.update_account', ':id') }}".replace(':id', id));
            });

            // 🔥 كود الرسم البياني للمقارنة (سنة 1 vs سنة 2) 🔥
            var options = {
                series: [{
                        name: 'سنة {{ $comparison['year1'] ?? date('Y') }}',
                        data: @json($comparison['data1'] ?? [0, 0, 0]) // [الإيرادات, المصروفات, الربح]
                    },
                    {
                        name: 'سنة {{ $comparison['year2'] ?? date('Y', strtotime('-1 year')) }}',
                        data: @json($comparison['data2'] ?? [0, 0, 0]) // [الإيرادات, المصروفات, الربح]
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 350,
                    fontFamily: 'Tajawal, sans-serif',
                    toolbar: {
                        show: false
                    },
                    // ✅ اكتشاف الوضع الليلي وتغيير لون النصوص
                    foreColor: document.body.getAttribute('data-layout-color') === 'dark' ? '#adb5bd' :
                        '#304758',
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '45%',
                        borderRadius: 4,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                colors: ['#2980b9', '#95a5a6'], // كحلي للسنة الأولى، رمادي للثانية
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return Number(val).toLocaleString();
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ['#304758']
                    }
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['إجمالي الإيرادات', 'إجمالي المصروفات', 'صافي الربح'],
                    labels: {
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return Number(val).toLocaleString() + " ر.ع";
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return Number(val).toLocaleString() + " ر.ع";
                        }
                    }
                },
                legend: {
                    position: 'top',
                    fontSize: '14px',
                    fontWeight: 'bold'
                }
            };

            var chart = new ApexCharts(document.querySelector("#profitComparisonChart"), options);
            chart.render();
        });
    </script>
@endpush
