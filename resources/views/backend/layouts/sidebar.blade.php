<style>
    /* ========================================
       🎨 تأثيرات الحركة المتقدمة
       ======================================== */

    /* تأثير النبض للإشعارات */
    @keyframes pulse-notification {

        0%,
        100% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.15);
            opacity: 0.8;
        }
    }

    /* تأثير الاهتزاز للتنبيهات */
    @keyframes shake-alert {

        0%,
        100% {
            transform: translateX(0);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-2px);
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(2px);
        }
    }

    /* تأثير التوهج */
    @keyframes glow-effect {

        0%,
        100% {
            box-shadow: 0 0 5px rgba(220, 53, 69, 0.5);
        }

        50% {
            box-shadow: 0 0 20px rgba(220, 53, 69, 0.8), 0 0 30px rgba(220, 53, 69, 0.4);
        }
    }

    /* تأثير الدوران البطيء */
    @keyframes slow-rotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* تأثير القفز */
    @keyframes bounce-soft {

        0%,
        20%,
        50%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-6px);
        }

        60% {
            transform: translateY(-3px);
        }
    }

    /* تأثير الوميض */
    @keyframes blink-soft {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    /* ========================================
       🏷️ أنماط الإشعارات (Badges)
       ======================================== */

    /* الإشعار الأحمر (تنبيه عاجل) */
    .badge-alert-critical {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        animation: pulse-notification 1.5s infinite, glow-effect 2s infinite;
        border: none;
        font-weight: 800;
        font-size: 11px;
        padding: 4px 8px;
    }

    /* الإشعار البرتقالي (تحذير) */
    .badge-alert-warning {
        background: linear-gradient(135deg, #fd7e14 0%, #e8590c 100%);
        animation: pulse-notification 2s infinite;
        border: none;
        font-weight: 700;
        font-size: 11px;
    }

    /* الإشعار الأخضر (نجاح/جديد) */
    .badge-alert-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        animation: bounce-soft 3s infinite;
        border: none;
        font-weight: 700;
        font-size: 11px;
    }

    /* الإشعار البنفسجي (AI/ذكي) */
    .badge-alert-ai {
        background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
        animation: slow-rotate 8s linear infinite;
        border: none;
        font-weight: 700;
        font-size: 10px;
        padding: 3px 6px;
    }

    /* الإشعار الأزرق (معلومات) */
    .badge-alert-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
        animation: blink-soft 2s infinite;
        border: none;
        font-weight: 700;
        font-size: 11px;
    }

    /* ========================================
       🎯 أنماط الأيقونات المتحركة
       ======================================== */

    .icon-pulse {
        animation: pulse-notification 2s infinite;
        display: inline-block;
    }

    .icon-shake {
        animation: shake-alert 1s infinite;
        display: inline-block;
    }

    .icon-bounce {
        animation: bounce-soft 2s infinite;
        display: inline-block;
    }

    .icon-rotate {
        animation: slow-rotate 10s linear infinite;
        display: inline-block;
    }

    .icon-glow-red {
        filter: drop-shadow(0 0 6px rgba(220, 53, 69, 0.6));
    }

    .icon-glow-green {
        filter: drop-shadow(0 0 6px rgba(25, 135, 84, 0.6));
    }

    .icon-glow-purple {
        filter: drop-shadow(0 0 6px rgba(111, 66, 193, 0.6));
    }

    /* ========================================
       🎨 أنماط عناصر القائمة المميزة
       ======================================== */

    /* عنصر مميز (Premium) */
    .menu-item-premium {
        background: linear-gradient(90deg, rgba(111, 66, 193, 0.08) 0%, transparent 100%) !important;
        border-right: 3px solid #6f42c1;
        border-radius: 0 8px 8px 0;
        margin: 4px 0;
        transition: all 0.3s ease;
    }

    .menu-item-premium:hover {
        background: linear-gradient(90deg, rgba(111, 66, 193, 0.15) 0%, transparent 100%) !important;
        transform: translateX(-5px);
    }

    /* عنصر تنبيه (Alert) */
    .menu-item-alert {
        background: linear-gradient(90deg, rgba(220, 53, 69, 0.08) 0%, transparent 100%) !important;
        border-right: 3px solid #dc3545;
        border-radius: 0 8px 8px 0;
        margin: 4px 0;
        transition: all 0.3s ease;
    }

    .menu-item-alert:hover {
        background: linear-gradient(90deg, rgba(220, 53, 69, 0.15) 0%, transparent 100%) !important;
        transform: translateX(-5px);
    }

    /* عنصر المحادثات */
    .menu-item-chat {
        background: linear-gradient(90deg, rgba(13, 202, 240, 0.08) 0%, transparent 100%) !important;
        border-right: 3px solid #0dcaf0;
        border-radius: 0 8px 8px 0;
        margin: 4px 0;
        transition: all 0.3s ease;
    }

    .menu-item-chat:hover {
        background: linear-gradient(90deg, rgba(13, 202, 240, 0.15) 0%, transparent 100%) !important;
        transform: translateX(-5px);
    }

    /* ========================================
       📋 عناوين الأقسام
       ======================================== */

    .section-title-main {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 15px 20px 8px;
        opacity: 0.7;
        border-bottom: 1px dashed rgba(108, 117, 125, 0.2);
        margin-bottom: 5px;
    }

    .section-title-sub {
        font-size: 10px;
        font-weight: 700;
        opacity: 0.6;
        padding: 8px 20px 4px;
        margin: 5px 0;
        color: #6c757d;
    }

    /* ========================================
       🌙 Dark Mode Support
       ======================================== */

    body[data-layout-color="dark"] .menu-item-premium {
        background: linear-gradient(90deg, rgba(111, 66, 193, 0.15) 0%, transparent 100%) !important;
    }

    body[data-layout-color="dark"] .menu-item-alert {
        background: linear-gradient(90deg, rgba(220, 53, 69, 0.15) 0%, transparent 100%) !important;
    }

    body[data-layout-color="dark"] .menu-item-chat {
        background: linear-gradient(90deg, rgba(13, 202, 240, 0.15) 0%, transparent 100%) !important;
    }
</style>

<div class="left-side-menu">
    <div class="h-100" data-simplebar>

        {{-- 🌟 ملف المستخدم 🌟 --}}
        <div class="user-box text-center">
            <img src="{{ asset('backend/assets/images/users/user-1.jpg') }}" alt="user-img"
                class="rounded-circle img-thumbnail avatar-md shadow-sm">
            <div class="dropdown">
                <a href="#" class="user-name dropdown-toggle h5 mt-2 mb-1 d-block text-dark fw-bold"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    @if (auth('web')->check())
                        {{ auth('web')->user()->name }}
                    @elseif(auth('teacher')->check())
                        {{ auth('teacher')->user()->name_ar }}
                    @elseif(auth('student')->check())
                        {{ auth('student')->user()->first_name }}
                    @elseif(auth('guardian')->check())
                        {{ auth('guardian')->user()->name_ar ?? auth('guardian')->user()->name }}
                    @endif
                </a>
            </div>
            <p class="text-muted left-user-info fw-bold mt-1">
                @if (auth('web')->check())
                    <span class="badge bg-danger rounded-pill px-3 py-1">إدارة النظام</span>
                @elseif(auth('teacher')->check())
                    <span class="badge bg-info rounded-pill px-3 py-1">معلم</span>
                @elseif(auth('student')->check())
                    <span class="badge bg-success rounded-pill px-3 py-1">طالب</span>
                @elseif(auth('guardian')->check())
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1">ولي أمر</span>
                @endif
            </p>
        </div>

        <div id="sidebar-menu">
            <ul id="side-menu">

                @if (auth('web')->check())

                    {{-- ============================================= --}}
                    {{-- 1. القسم الرئيسي --}}
                    {{-- ============================================= --}}
                    <li class="section-title-main text-primary">🏠 الرئيسية</li>

                    @can('view_dashboard')
                        <li>
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="mdi mdi-view-dashboard-outline"></i>
                                <span> لوحة التحكم </span>
                            </a>
                        </li>
                    @endcan

                    {{-- المحادثات --}}
                    <li>
                        <a href="{{ route('chat.index') }}" class="menu-item-chat fw-bold">
                            <i class="mdi mdi-forum text-info icon-pulse"></i>
                            <span> المحادثات </span>
                            <span class="badge badge-alert-info float-end rounded-pill">جديد</span>
                        </a>
                    </li>

                    {{-- ============================================= --}}
                    {{-- 2. شؤون الطلاب --}}
                    {{-- ============================================= --}}
                    @canany(['view_students', 'create_students', 'view_guardians', 'manage_student_leaves',
                        'manage_student_promotions', 'manage_student_transfers'])
                        <li class="section-title-main text-success mt-3">🎓 شؤون الطلاب</li>

                        @php
                            $pendingLeaves = auth()->user()->can('manage_student_leaves')
                                ? \App\Models\StudentLeave::where('status', 'pending')->count()
                                : 0;
                            $pendingTransfers = auth()->user()->can('manage_student_transfers')
                                ? \App\Models\StudentTransfer::where('status', 'pending_parent')->count()
                                : 0;
                        @endphp

                        <li>
                            <a href="#sidebarStudents" data-bs-toggle="collapse">
                                <i class="mdi mdi-account-group-outline"></i>
                                <span> إدارة الطلاب </span>
                                @if ($pendingLeaves > 0 || $pendingTransfers > 0)
                                    <span class="badge badge-alert-critical float-end">
                                        {{ $pendingLeaves + $pendingTransfers }}
                                    </span>
                                @else
                                    <span class="menu-arrow"></span>
                                @endif
                            </a>
                            <div class="collapse" id="sidebarStudents">
                                <ul class="nav-second-level">
                                    @can('create_students')
                                        <li><a href="{{ route('admin.students.create') }}" class="text-success fw-bold">
                                                <i class="mdi mdi-account-plus me-1"></i> تسجيل طالب جديد
                                            </a></li>
                                    @endcan
                                    @can('view_students')
                                        <li><a href="{{ route('admin.students.index') }}">سجل الطلاب</a></li>
                                    @endcan

                                    <li class="section-title-sub">الطلبات والموافقات</li>

                                    @can('manage_student_leaves')
                                        <li>
                                            <a href="{{ route('admin.leave_requests.index') }}"
                                                class="{{ $pendingLeaves > 0 ? 'text-danger fw-bold' : '' }}">
                                                <i class="mdi mdi-hospital-box-outline me-1"></i> الاستئذانات والإجازات
                                                @if ($pendingLeaves > 0)
                                                    <span
                                                        class="badge badge-alert-warning float-end">{{ $pendingLeaves }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan

                                    @can('manage_student_transfers')
                                        <li>
                                            <a href="{{ route('admin.student-transfers.index') }}"
                                                class="{{ $pendingTransfers > 0 ? 'text-warning fw-bold' : '' }}">
                                                <i class="mdi mdi-account-switch me-1"></i> طلبات التحويل
                                                @if ($pendingTransfers > 0)
                                                    <span
                                                        class="badge badge-alert-warning float-end">{{ $pendingTransfers }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan

                                    <li class="section-title-sub">العمليات الأكاديمية</li>

                                    @can('manage_sections')
                                        <li><a href="{{ route('admin.placements.index') }}" class="text-info fw-bold">
                                                <i class="mdi mdi-account-multiple-check me-1"></i> التسكين الأكاديمي
                                            </a></li>
                                    @endcan

                                    @can('manage_student_promotions')
                                        <li><a href="{{ route('admin.promotions.index') }}" class="text-primary fw-bold">
                                                <i class="mdi mdi-arrow-up-bold-hexagon-outline me-1"></i> الترحيل السنوي
                                            </a></li>
                                    @endcan

                                    @can('view_guardians')
                                        <li><a href="{{ route('admin.guardians.index') }}">
                                                <i class="mdi mdi-account-tie me-1"></i> أولياء الأمور
                                            </a></li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcanany

                    {{-- ============================================= --}}
                    {{-- 3. الشؤون الأكاديمية --}}
                    {{-- ============================================= --}}
                    @canany(['view_academics', 'view_branches', 'view_sections', 'view_subjects', 'view_schedules',
                        'view_student_attendances', 'view_virtual_classes'])
                        <li class="section-title-main text-info mt-3">📚 الشؤون الأكاديمية</li>
                        <li>
                            <a href="#sidebarAcademics" data-bs-toggle="collapse">
                                <i class="mdi mdi-school-outline"></i>
                                <span> الإعدادات الأكاديمية </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarAcademics">
                                <ul class="nav-second-level">
                                    <li class="section-title-sub">الهيكل التنظيمي</li>

                                    @can('view_branches')
                                        <li><a href="{{ route('admin.branches.index') }}">
                                                <i class="mdi mdi-office-building me-1"></i> الفروع والمدارس
                                            </a></li>
                                    @endcan
                                    @can('view_academics')
                                        <li><a href="{{ route('admin.academic-years.index') }}">
                                                <i class="mdi mdi-calendar-multiple me-1"></i> السنوات الدراسية
                                            </a></li>
                                    @endcan

                                    <li class="section-title-sub">الصفوف والشعب</li>

                                    @can('view_classroom')
                                        <li><a href="{{ route('admin.classrooms.index') }}">
                                                <i class="mdi mdi-google-classroom me-1"></i> الصفوف الدراسية
                                            </a></li>
                                    @endcan
                                    @can('view_sections')
                                        <li><a href="{{ route('admin.sections.index') }}">
                                                <i class="mdi mdi-account-multiple me-1"></i> الشعب والفصول
                                            </a></li>
                                    @endcan
                                    @can('view_subjects')
                                        <li><a href="{{ route('admin.subjects.index') }}">
                                                <i class="mdi mdi-book-open-variant me-1"></i> المواد الدراسية
                                            </a></li>
                                    @endcan

                                    <li class="section-title-sub">الجداول والحضور</li>

                                    @can('view_schedules')
                                        <li><a href="{{ route('admin.schedules.index') }}" class="text-info fw-bold">
                                                <i class="mdi mdi-calendar-clock me-1"></i> جداول الحصص
                                            </a></li>
                                    @endcan
                                    @can('view_student_attendances')
                                        <li><a href="{{ route('admin.attendances.index') }}">
                                                <i class="mdi mdi-calendar-check me-1"></i> الحضور والغياب
                                            </a></li>
                                    @endcan

                                    <li class="section-title-sub">التعليم عن بُعد</li>

                                    @can('view_virtual_classes')
                                        <li>
                                            <a href="{{ route('admin.virtual-classes.index') }}"
                                                class="menu-item-alert fw-bold">
                                                <i class="mdi mdi-video-outline text-danger icon-glow-red"></i>
                                                الفصول الافتراضية
                                                <span class="badge badge-alert-critical float-end">LIVE</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcanany

                    {{-- ============================================= --}}
                    {{-- 4. المالية والعقود --}}
                    {{-- ============================================= --}}
                    @canany(['view_student_contracts', 'create_student_contracts', 'edit_student_contracts',
                        'collect_installments', 'create_store_invoices'])
                        <li class="section-title-main text-warning mt-3">💰 المالية والعقود</li>
                        <li>
                            <a href="#sidebarSales" data-bs-toggle="collapse">
                                <i class="mdi mdi-file-document-edit-outline"></i>
                                <span> العقود والتحصيل </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarSales">
                                <ul class="nav-second-level">
                                    <li class="section-title-sub">عقود الطلاب</li>

                                    @can('create_student_contracts')
                                        <li><a href="{{ route('admin.student-contracts.create') }}"
                                                class="text-success fw-bold">
                                                <i class="mdi mdi-file-plus me-1"></i> إبرام عقد جديد
                                            </a></li>
                                    @endcan
                                    @can('view_student_contracts')
                                        <li><a href="{{ route('admin.student-contracts.index') }}">
                                                <i class="mdi mdi-file-document-multiple me-1"></i> سجل العقود
                                            </a></li>
                                        <li><a href="{{ route('admin.student-contracts.pending_renewals') }}"
                                                class="text-warning fw-bold">
                                                <i class="mdi mdi-file-clock me-1"></i> عقود بحاجة للتجديد
                                            </a></li>
                                    @endcan

                                    <li class="section-title-sub">التحصيل المالي</li>

                                    @can('collect_installments')
                                        <li><a href="{{ route('admin.installments.index') }}" class="text-primary fw-bold">
                                                <i class="mdi mdi-cash-multiple me-1"></i> تحصيل الأقساط
                                            </a></li>
                                        <li><a href="{{ route('admin.installments.overdue') }}" class="text-danger fw-bold">
                                                <i class="mdi mdi-alert-circle me-1 icon-shake"></i> المتأخرات والمطالبات
                                            </a></li>
                                    @endcan

                                    <li class="section-title-sub">نقطة البيع</li>

                                    @can('create_store_invoices')
                                        <li><a href="{{ route('admin.store-invoices.create') }}"
                                                class="menu-item-premium fw-bold">
                                                <i class="mdi mdi-cart-outline text-success"></i>
                                                نقطة البيع (POS)
                                                <span class="badge badge-alert-success float-end">جديد</span>
                                            </a></li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcanany

                    {{-- ============================================= --}}
                    {{-- 5. المحاسبة والشجرة المحاسبية --}}
                    {{-- ============================================= --}}
                    @canany(['view_payments', 'view_incomes', 'view_expenses', 'view_assets',
                        'view_financial_accounts'])
                        <li class="section-title-main text-primary mt-3">🏦 المحاسبة</li>
                        <li>
                            <a href="#sidebarFinance" data-bs-toggle="collapse">
                                <i class="mdi mdi-bank-outline"></i>
                                <span> النظام المحاسبي </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarFinance">
                                <ul class="nav-second-level">
                                    @can('view_financial_accounts')
                                        <li class="section-title-sub">الإعدادات المالية</li>
                                        <li><a href="{{ route('admin.accounting.chart_of_accounts') }}"
                                                class="text-primary fw-bold">
                                                <i class="mdi mdi-file-tree me-1"></i> الشجرة المحاسبية
                                            </a></li>
                                        <li><a href="{{ route('admin.financial-accounts.index') }}">
                                                <i class="mdi mdi-bank me-1"></i> الحسابات والخزائن
                                            </a></li>
                                        @can('view_fee_items')
                                            <li><a href="{{ route('admin.fee-items.index') }}">
                                                    <i class="mdi mdi-cash-usd me-1"></i> بنود الرسوم
                                                </a></li>
                                        @endcan
                                    @endcan

                                    @can('view_payments')
                                        <li class="section-title-sub">سندات الخزينة</li>
                                        <li><a href="{{ route('admin.payments.index') }}" class="text-success fw-bold">
                                                <i class="mdi mdi-cash-register me-1"></i> سندات القبض والصرف
                                            </a></li>
                                        <li><a href="{{ route('admin.account-transfers.history') }}">
                                                <i class="mdi mdi-swap-horizontal me-1"></i> تحويلات الخزينة
                                            </a></li>
                                    @endcan

                                    @can('view_incomes')
                                        <li class="section-title-sub">الإيرادات</li>
                                        <li><a href="{{ route('admin.incomes.index') }}">
                                                <i class="mdi mdi-arrow-down-bold-circle me-1"></i> سجل الإيرادات
                                            </a></li>
                                        <li><a href="{{ route('admin.incomes.types') }}">
                                                <i class="mdi mdi-tag-multiple me-1"></i> تصنيفات الإيرادات
                                            </a></li>
                                    @endcan

                                    @can('view_expenses')
                                        <li class="section-title-sub">المصروفات</li>
                                        <li><a href="{{ route('admin.expenses.index') }}">
                                                <i class="mdi mdi-arrow-up-bold-circle me-1"></i> سجل المصروفات
                                            </a></li>
                                        <li><a href="{{ route('admin.expenses.types') }}">
                                                <i class="mdi mdi-tag-multiple me-1"></i> تصنيفات المصروفات
                                            </a></li>
                                    @endcan

                                    @can('view_assets')
                                        <li class="section-title-sub">الأصول الثابتة</li>
                                        <li><a href="{{ route('admin.assets.index') }}">
                                                <i class="mdi mdi-package-variant me-1"></i> سجل الأصول
                                            </a></li>
                                        <li><a href="{{ route('admin.assets.types') }}">
                                                <i class="mdi mdi-folder-multiple me-1"></i> تصنيفات الأصول
                                            </a></li>
                                        <li><a href="{{ route('admin.assets.installments') }}" class="text-warning fw-bold">
                                                <i class="mdi mdi-calendar-clock me-1"></i> أقساط الأصول
                                            </a></li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcanany

                    {{-- ============================================= --}}
                    {{-- 6. المخازن والمشتريات --}}
                    {{-- ============================================= --}}
                    @canany(['view_products', 'view_stock', 'view_purchase_invoices', 'view_suppliers',
                        'manage_stock_transfers'])
                        @php
                            $lowStockCount = auth()->user()->can('view_stock')
                                ? \App\Models\Product::where('type', 'product')
                                    ->whereColumn('quantity', '<=', 'alert_quantity')
                                    ->count()
                                : 0;
                            $pendingTransfersStock = auth()->user()->can('manage_stock_transfers')
                                ? \App\Models\StockTransfer::where('status', 'pending')->count()
                                : 0;
                        @endphp

                        <li class="section-title-main text-danger mt-3">📦 المخازن والمشتريات</li>
                        <li>
                            <a href="#sidebarProcurement" data-bs-toggle="collapse">
                                <i class="mdi mdi-warehouse"></i>
                                <span> إدارة المستودعات </span>
                                @if ($lowStockCount > 0)
                                    <span
                                        class="badge badge-alert-critical float-end icon-shake">{{ $lowStockCount }}</span>
                                @elseif ($pendingTransfersStock > 0)
                                    <span class="badge badge-alert-warning float-end">{{ $pendingTransfersStock }}</span>
                                @else
                                    <span class="menu-arrow"></span>
                                @endif
                            </a>
                            <div class="collapse" id="sidebarProcurement">
                                <ul class="nav-second-level">
                                    <li class="section-title-sub">المخزون</li>

                                    @can('view_products')
                                        <li><a href="{{ route('admin.products.index') }}">
                                                <i class="mdi mdi-package me-1"></i> الأصناف والمنتجات
                                            </a></li>
                                    @endcan
                                    @can('view_stock')
                                        <li>
                                            <a href="{{ route('admin.warehouse_stocks.index') }}"
                                                class="{{ $lowStockCount > 0 ? 'text-danger fw-bold' : '' }}">
                                                <i class="mdi mdi-store-search me-1"></i> أرصدة الفروع
                                                @if ($lowStockCount > 0)
                                                    <span class="badge badge-alert-warning float-end">{{ $lowStockCount }}
                                                        نقص</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li><a href="{{ route('admin.reports.inventory_shortages') }}"
                                                class="text-danger fw-bold">
                                                <i class="mdi mdi-alert me-1 icon-shake"></i> تقرير النواقص
                                            </a></li>
                                    @endcan
                                    @can('manage_inventory_adjustments')
                                        <li><a href="{{ route('admin.inventory-adjustments.index') }}"
                                                class="text-info fw-bold">
                                                <i class="mdi mdi-clipboard-check me-1"></i> تسويات الجرد
                                            </a></li>
                                    @endcan

                                    <li class="section-title-sub">التحويلات</li>

                                    @can('manage_stock_transfers')
                                        <li>
                                            <a href="{{ route('admin.stock-transfers.index') }}"
                                                class="{{ $pendingTransfersStock > 0 ? 'text-warning fw-bold' : '' }}">
                                                <i class="mdi mdi-truck-delivery me-1"></i> التحويلات المخزنية
                                                @if ($pendingTransfersStock > 0)
                                                    <span
                                                        class="badge badge-alert-warning float-end">{{ $pendingTransfersStock }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan

                                    <li class="section-title-sub">المشتريات</li>

                                    @can('view_purchase_invoices')
                                        <li><a href="{{ route('admin.purchase-invoices.index') }}">
                                                <i class="mdi mdi-file-document me-1"></i> فواتير المشتريات
                                            </a></li>
                                        <li><a href="{{ route('admin.purchase-returns.index') }}">
                                                <i class="mdi mdi-undo me-1"></i> المرتجعات
                                            </a></li>
                                    @endcan
                                    @can('view_suppliers')
                                        <li><a href="{{ route('admin.suppliers.index') }}">
                                                <i class="mdi mdi-truck me-1"></i> الموردين
                                            </a></li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcanany

                    {{-- ============================================= --}}
                    {{-- 7. الموارد البشرية --}}
                    {{-- ============================================= --}}
                    @canany(['view_hr_settings', 'view_employees', 'manage_hr_attendances', 'view_payrolls'])
                        <li class="section-title-main text-info mt-3">👥 الموارد البشرية</li>

                        @can('view_employees')
                            <li>
                                <a href="#sidebarHREmployees" data-bs-toggle="collapse">
                                    <i class="mdi mdi-card-account-details-outline text-info"></i>
                                    <span> شؤون الموظفين </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarHREmployees">
                                    <ul class="nav-second-level">
                                        <li class="section-title-sub">الإعدادات</li>

                                        @can('view_hr_settings')
                                            <li><a href="{{ route('admin.hr.settings.index') }}">
                                                    <i class="mdi mdi-cog me-1"></i> إعدادات الأجور
                                                </a></li>
                                            <li><a href="{{ route('admin.hr.org_chart.index') }}">
                                                    <i class="mdi mdi-sitemap me-1"></i> الهيكل التنظيمي
                                                </a></li>
                                        @endcan

                                        <li class="section-title-sub">الموظفون</li>

                                        <li><a href="{{ route('admin.hr.employees.index') }}" class="fw-bold">
                                                <i class="mdi mdi-badge-account me-1"></i> دليل الموظفين
                                            </a></li>
                                        <li><a href="{{ route('admin.teachers.index') }}">
                                                <i class="mdi mdi-human-male-board me-1"></i> المعلمون
                                            </a></li>
                                        <li><a href="{{ route('admin.buses.index') }}">
                                                <i class="mdi mdi-bus me-1"></i> السائقون والحافلات
                                            </a></li>
                                        <li><a href="{{ route('admin.transport-routes.index') }}">
                                                <i class="mdi mdi-bus me-1"></i> اتجاهات الحافله
                                            </a></li>

                                        <li><a href="{{ route('admin.hr.trainees.index') }}">
                                                <i class="mdi mdi-school me-1"></i> المتدربون
                                            </a></li>
                                        <li><a href="{{ route('admin.hr.candidates.index') }}" class="text-success fw-bold">
                                                <i class="mdi mdi-account-search me-1"></i> طلبات التوظيف
                                            </a></li>

                                        <li class="section-title-sub">المتابعة</li>

                                        <li><a href="{{ route('admin.hr.custodies.index') }}">
                                                <i class="mdi mdi-package-variant-closed me-1"></i> عُهد الموظفين
                                            </a></li>
                                        <li><a href="{{ route('admin.hr.appraisals.index') }}">
                                                <i class="mdi mdi-star me-1"></i> التقييمات والأداء
                                            </a></li>
                                        <li><a href="{{ route('admin.hr.alerts.index') }}" class="text-warning fw-bold">
                                                <i class="mdi mdi-bell-alert me-1 icon-pulse"></i> تنبيهات الموارد البشرية
                                            </a></li>
                                    </ul>
                                </div>
                            </li>
                        @endcan

                        @canany(['manage_hr_attendances', 'view_payrolls'])
                            <li>
                                <a href="#sidebarHROps" data-bs-toggle="collapse">
                                    <i class="mdi mdi-cash-multiple text-success"></i>
                                    <span> الرواتب والأجور </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarHROps">
                                    <ul class="nav-second-level">
                                        @can('manage_hr_attendances')
                                            <li class="section-title-sub">الحضور</li>
                                            <li><a href="{{ route('admin.hr.shift_rosters.index') }}">
                                                    <i class="mdi mdi-calendar-range me-1"></i> الورديات والشفتات
                                                </a></li>
                                            <li><a href="{{ route('admin.hr.attendance.index') }}">
                                                    <i class="mdi mdi-fingerprint me-1"></i> أجهزة البصمة
                                                </a></li>
                                        @endcan

                                        @can('view_payrolls')
                                            <li class="section-title-sub">الاستحقاقات المالية</li>
                                            <li><a href="{{ route('admin.hr.financials.index') }}" class="text-success fw-bold">
                                                    <i class="mdi mdi-gift me-1"></i> المكافآت والبدلات
                                                </a></li>
                                            <li><a href="{{ route('admin.hr.penalties.index') }}" class="text-danger fw-bold">
                                                    <i class="mdi mdi-alert-octagon me-1"></i> الجزاءات والخصومات
                                                </a></li>
                                            <li><a href="{{ route('admin.hr.loans.index') }}">
                                                    <i class="mdi mdi-bank me-1"></i> السلف والقروض
                                                </a></li>

                                            <li class="section-title-sub">المسيرات</li>

                                            <li><a href="{{ route('admin.hr.payroll.index') }}"
                                                    class="menu-item-premium fw-bold">
                                                    <i class="mdi mdi-file-account me-1"></i>
                                                    مسير الرواتب الشهري
                                                </a></li>
                                            <li><a href="{{ route('admin.hr.eos.index') }}" class="text-warning fw-bold">
                                                    <i class="mdi mdi-logout me-1"></i> مكافأة نهاية الخدمة
                                                </a></li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                        @endcanany

                        {{-- ✅ العقود (الرابط الناقص) --}}
                        @can('view_hr_settings')
                            <li>
                                <a href="{{ route('admin.hr.contracts.index') }}">
                                    <i class="mdi mdi-file-document-sign text-primary"></i>
                                    <span> عقود الموظفين </span>
                                </a>
                            </li>
                        @endcan
                    @endcanany

                    {{-- ============================================= --}}
                    {{-- 8. التقارير الذكية --}}
                    {{-- ============================================= --}}
                    @canany(['view_financial_reports', 'view_student_financial_reports', 'view_academic_reports',
                        'view_hr_reports', 'view_radar_alerts', 'view_ai_analysis'])
                        <li class="section-title-main mt-3" style="color: #6f42c1;">📊 التقارير الذكية</li>
                        <li>
                            <a href="#sidebarReports" data-bs-toggle="collapse">
                                <i class="mdi mdi-chart-box-outline" style="color: #6f42c1;"></i>
                                <span style="color: #6f42c1;"> مركز التقارير </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarReports">
                                <ul class="nav-second-level">
                                    @can('view_financial_reports')
                                        <li class="section-title-sub">التقارير المالية</li>
                                        <li><a href="{{ route('admin.reports.overall') }}">
                                                <i class="mdi mdi-chart-line me-1"></i> الأرباح والخسائر (P&L)
                                            </a></li>
                                        <li><a href="{{ route('admin.reports.cash_flow') }}">
                                                <i class="mdi mdi-cash-flow me-1"></i> التدفقات النقدية
                                            </a></li>
                                        <li><a href="{{ route('admin.reports.tax_report') }}">
                                                <i class="mdi mdi-receipt me-1"></i> التقرير الضريبي (VAT)
                                            </a></li>
                                        <li><a href="{{ route('admin.reports.branch_performance') }}">
                                                <i class="mdi mdi-chart-bar me-1"></i> أداء الفروع
                                            </a></li>
                                        <li><a href="{{ route('admin.reports.branch_comparison') }}">
                                                <i class="mdi mdi-compare me-1"></i> مقارنة الفروع
                                            </a></li>
                                        <li><a href="{{ route('admin.reports.comparison') }}">
                                                <i class="mdi mdi-chart-timeline me-1"></i> مقارنة السنوات
                                            </a></li>
                                    @endcan

                                    @can('view_student_financial_reports')
                                        <li class="section-title-sub">تقارير الطلاب</li>
                                        <li><a href="{{ route('admin.reports.students_financial') }}">
                                                <i class="mdi mdi-account-cash me-1"></i> المديونيات والمتأخرات
                                            </a></li>
                                        <li><a href="{{ route('admin.reports.guardian_statement') }}">
                                                <i class="mdi mdi-file-account me-1"></i> كشف حساب ولي الأمر
                                            </a></li>
                                        <li><a href="{{ route('admin.reports.student-statement') }}">
                                                <i class="mdi mdi-file-search me-1"></i> كشف حساب طالب
                                            </a></li>
                                        <li><a href="{{ route('admin.reports.discounts') }}">
                                                <i class="mdi mdi-ticket-percent me-1"></i> الخصومات والإعفاءات
                                            </a></li>
                                    @endcan

                                    <li><a href="{{ route('admin.reports.buses') }}">
                                            <i class="mdi mdi-bus me-1"></i> تقارير النقل والباصات
                                        </a></li>

                                    @can('view_hr_reports')
                                        <li class="section-title-sub">تقارير الموارد البشرية</li>
                                        <li><a href="{{ route('admin.reports.hr') }}">
                                                <i class="mdi mdi-human-male-board me-1"></i> تقرير الرواتب
                                            </a></li>
                                        <li><a href="{{ route('admin.reports.attendance') }}">
                                                <i class="mdi mdi-calendar-check me-1"></i> تقرير الحضور
                                            </a></li>
                                    @endcan

                                    {{-- ✅ الروابط الناقصة: الذكاء الاصطناعي والتنبيهات --}}
                                    @canany(['view_ai_analysis', 'view_radar_alerts'])
                                        <li>
                                            <hr class="dropdown-divider border-secondary opacity-25 my-2">
                                        </li>

                                        @can('view_ai_analysis')
                                            <li>
                                                <a href="{{ route('admin.reports.ai_analysis') }}"
                                                    class="menu-item-premium fw-bold">
                                                    <i class="mdi mdi-brain text-purple icon-rotate" style="color: #6f42c1;"></i>
                                                    التحليل الذكي للبيانات
                                                    <span class="badge badge-alert-ai float-end">AI</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('view_radar_alerts')
                                            <li>
                                                <a href="{{ route('admin.reports.smart_alerts') }}"
                                                    class="menu-item-alert fw-bold">
                                                    <i class="mdi mdi-radar text-danger icon-glow-red icon-pulse"></i>
                                                    رادار التنبيهات المبكرة
                                                    <span class="badge badge-alert-critical float-end">
                                                        <i class="mdi mdi-bell-ring"></i>
                                                    </span>
                                                </a>
                                            </li>
                                        @endcan
                                    @endcanany
                                </ul>
                            </div>
                        </li>
                    @endcanany

                    {{-- ============================================= --}}
                    {{-- 9. إدارة النظام --}}
                    {{-- ============================================= --}}
                    @canany(['view_users', 'view_roles', 'manage_settings', 'view_audit_logs'])
                        <li class="section-title-main text-dark mt-3">⚙️ إدارة النظام</li>

                        @canany(['view_users', 'view_roles'])
                            <li>
                                <a href="#sidebarUsersPermissions" data-bs-toggle="collapse">
                                    <i class="mdi mdi-account-key-outline text-primary"></i>
                                    <span> الصلاحيات والمستخدمين </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarUsersPermissions">
                                    <ul class="nav-second-level">
                                        @can('view_users')
                                            <li><a href="{{ route('admin.users.index') }}">
                                                    <i class="mdi mdi-account-multiple me-1"></i> المستخدمون
                                                </a></li>
                                        @endcan
                                        @can('view_roles')
                                            <li><a href="{{ route('admin.roles.index') }}">
                                                    <i class="mdi mdi-shield-key me-1"></i> الأدوار والصلاحيات
                                                </a></li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                        @endcanany

                        @can('manage_settings')
                            <li>
                                <a href="{{ route('admin.settings.index') }}">
                                    <i class="mdi mdi-cog-outline text-secondary"></i>
                                    <span> الإعدادات العامة </span>
                                </a>
                            </li>
                        @endcan

                        @can('view_audit_logs')
                            <li>
                                <a href="{{ route('admin.audit-logs.index') }}" class="menu-item-alert fw-bold">
                                    <i class="mdi mdi-shield-search text-danger icon-glow-red"></i>
                                    <span> سجل أمان النظام </span>
                                </a>
                            </li>
                        @endcan
                    @endcanany


                    {{-- ============================================= --}}
                    {{-- 👨‍🏫 بوابة المعلم --}}
                    {{-- ============================================= --}}
                @elseif(auth('teacher')->check())
                    <li class="section-title-main text-info">👨‍🏫 بوابة المعلم</li>

                    <li><a href="{{ route('teacher.dashboard') }}">
                            <i class="mdi mdi-view-dashboard-outline"></i>
                            <span> لوحة التحكم </span>
                        </a></li>

                    <li class="section-title-main text-primary mt-3">📚 الشؤون الأكاديمية</li>

                    <li><a href="{{ route('teacher.schedule') }}">
                            <i class="mdi mdi-calendar-month-outline text-primary"></i>
                            <span> جدولي الدراسي </span>
                        </a></li>

                    <li><a href="{{ route('teacher.schedule.index') }}">
                            <i class="mdi mdi-calendar-clock text-info"></i>
                            <span> جدول الحصص الأسبوعي </span>
                        </a></li>

                    <li>
                        <a href="#sidebarTeacherGrading" data-bs-toggle="collapse">
                            <i class="mdi mdi-marker-check"></i>
                            <span> الحضور والتقييم </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarTeacherGrading">
                            <ul class="nav-second-level">
                                {{-- ✅ تصحيح الروابط --}}
                                <li><a href="{{ route('teacher.attendance.create', ['schedule_id' => 0]) }}">
                                        <i class="mdi mdi-check-all me-1"></i> رصد الغياب
                                    </a></li>
                                <li><a
                                        href="{{ route('teacher.marks.index', ['section_id' => 0, 'subject_id' => 0]) }}">
                                        <i class="mdi mdi-format-list-checks me-1"></i> رصد الدرجات
                                    </a></li>
                                <li><a href="{{ route('teacher.evaluations.create', ['schedule_id' => 0]) }}">
                                        <i class="mdi mdi-star me-1"></i> التقييمات
                                    </a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="section-title-main text-danger mt-3">🎥 التواصل</li>

                    <li>
                        <a href="{{ route('teacher.virtual_room', ['id' => 0]) }}" class="menu-item-alert fw-bold">
                            <i class="mdi mdi-video-outline text-danger icon-glow-red icon-pulse"></i>
                            <span> فصول أونلاين </span>
                            <span class="badge badge-alert-critical float-end">LIVE</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('chat.index') }}" class="menu-item-chat fw-bold">
                            <i class="mdi mdi-forum text-info icon-pulse"></i>
                            <span> المحادثات </span>
                        </a>
                    </li>


                    {{-- ============================================= --}}
                    {{-- 🎓 بوابة الطالب --}}
                    {{-- ============================================= --}}
                @elseif(auth('student')->check())
                    <li class="section-title-main text-success">🎓 بوابة الطالب</li>

                    <li class="{{ Route::is('student.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('student.dashboard') }}">
                            <i class="mdi mdi-view-dashboard-outline"></i>
                            <span> لوحة التحكم </span>
                        </a>
                    </li>

                    {{-- تم ربطها مستقبلاً بجدول الحصص الكامل للطالب --}}
                    <li>
                        <a href="#">
                            <i class="mdi mdi-calendar-text text-info"></i>
                            <span> الجدول الدراسي الكامل </span>
                        </a>
                    </li>

                    {{-- موديول الواجبات والاختبارات --}}
                    <li>
                        <a href="#">
                            <i class="mdi mdi-file-document-edit-outline text-warning"></i>
                            <span> الواجبات المدرسية </span>
                            <span class="badge bg-warning float-end font-11 rounded-circle px-1 mt-1">3</span>
                        </a>
                    </li>

                    {{-- موديول الشهادات والدرجات الفصيلة --}}
                    <li>
                        <a href="#">
                            <i class="mdi mdi-chart-box-outline text-success"></i>
                            <span> سجل الشهادات والدرجات </span>
                        </a>
                    </li>

                    <li class="{{ Route::is('chat.index') ? 'active' : '' }}">
                        <a href="{{ route('chat.index') }}" class="menu-item-chat fw-bold">
                            <i class="mdi mdi-forum text-info icon-pulse"></i>
                            <span> غرف المراسلات الفورية </span>
                        </a>
                    </li>

                    {{-- ============================================= --}}
                    {{-- 👨‍👩‍👧‍👦 بوابة ولي الأمر --}}
                    {{-- ============================================= --}}
                @elseif(auth('guardian')->check())
                    <li class="section-title-main text-warning">👨‍👩‍👧‍👦 بوابة ولي الأمر</li>

                    <li><a href="{{ route('parent.dashboard') }}">
                            <i class="mdi mdi-view-dashboard-outline"></i>
                            <span> لوحة التحكم </span>
                        </a></li>

                    <li><a href="{{ route('parent.student.profile', ['id' => 0]) }}">
                            <i class="mdi mdi-account-child-outline text-success"></i>
                            <span> ملفات الأبناء </span>
                        </a></li>

                    <li><a href="#">
                            <i class="mdi mdi-cash-multiple text-warning"></i>
                            <span> الأقساط والماليات </span>
                        </a></li>

                    <li>
                        <a href="{{ route('chat.index') }}" class="menu-item-chat fw-bold">
                            <i class="mdi mdi-forum text-info icon-pulse"></i>
                            <span> تواصل مع المدرسة </span>
                            <span class="badge badge-alert-info float-end">رسائل</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
