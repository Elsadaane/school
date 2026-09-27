<div class="navbar-custom">
    <div class="container-fluid ps-0">
        <ul class="list-unstyled topnav-menu float-end mb-0">

            {{-- 🔥 حماية جرس التنبيهات بالصلاحيات 🔥 --}}
            @can('view_notifications')
                @php
                    $dbNotifications = auth()->check() ? auth()->user()->unreadNotifications : collect();
                    $stockCount = isset($lowStockCount) ? $lowStockCount : 0;
                    $totalNotifications = $stockCount + $dbNotifications->count();
                @endphp

                {{-- 🌟 جرس التنبيهات الشامل 🌟 --}}
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fe-bell noti-icon"></i>
                        @if ($totalNotifications > 0)
                            <span class="badge bg-danger rounded-circle noti-icon-badge shadow"
                                style="animation: pulse-bell 1.5s infinite;">{{ $totalNotifications }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-lg shadow-lg border-0 rounded-4 mt-2">
                        <div class="dropdown-item noti-title bg-light border-bottom rounded-top-4 py-3">
                            <h5 class="m-0 text-dark fw-bold">
                                <i class="mdi mdi-bell-ring-outline text-primary me-1"></i> الإشعارات والتنبيهات
                            </h5>
                        </div>
                        <div class="noti-scroll" data-simplebar style="max-height: 300px;">

                            @if ($totalNotifications > 0)
                                {{-- 1. عرض إشعارات النظام --}}
                                @foreach ($dbNotifications->take(5) as $notification)
                                    <a href="{{ $notification->data['url'] ?? '#' }}"
                                        class="dropdown-item notify-item border-bottom bg-primary bg-opacity-10 transition-all">
                                        <div class="notify-icon bg-primary text-white shadow-sm">
                                            <i class="{{ $notification->data['icon'] ?? 'mdi mdi-bell-outline' }}"></i>
                                        </div>
                                        <p class="notify-details fw-bold mb-1 text-dark">
                                            {{ $notification->data['title'] ?? 'إشعار جديد' }}
                                        </p>
                                        <p class="text-muted mb-0 user-msg font-12 fw-semibold">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                        <small class="text-muted font-11"><i class="mdi mdi-clock-outline"></i>
                                            {{ $notification->created_at->diffForHumans() }}</small>
                                    </a>
                                @endforeach

                                {{-- 2. عرض إشعارات نواقص المخزن --}}
                                @can('view_stock')
                                    @if (isset($lowStockCount) && $lowStockCount > 0)
                                        @foreach ($lowStockAlerts->take(5) as $alertProduct)
                                            <a href="{{ route('admin.reports.inventory_shortages') }}"
                                                class="dropdown-item notify-item border-bottom bg-danger bg-opacity-10 transition-all">
                                                <div class="notify-icon bg-danger text-white shadow-sm">
                                                    <i class="mdi mdi-alert-outline"></i>
                                                </div>
                                                <p class="notify-details fw-bold mb-1 text-danger">{{ $alertProduct->name_ar }}
                                                </p>
                                                <p class="text-muted mb-0 user-msg font-12 fw-semibold">
                                                    الرصيد الحالي: <strong
                                                        class="text-danger">{{ $alertProduct->quantity }}</strong> | فرع:
                                                    {{ $alertProduct->branch->name_ar ?? 'الرئيسي' }}
                                                </p>
                                            </a>
                                        @endforeach
                                    @endif
                                @endcan
                            @else
                                <div class="text-center py-4">
                                    <i class="mdi mdi-bell-sleep-outline font-24 text-muted mb-2 d-block opacity-50"
                                        style="font-size: 40px;"></i>
                                    <p class="text-muted font-13 mb-0 fw-bold">لا توجد تنبيهات جديدة حالياً</p>
                                </div>
                            @endif

                        </div>
                        <a href="javascript:void(0);"
                            class="dropdown-item text-center text-primary notify-item notify-all border-top fw-bold rounded-bottom-4 py-2 bg-light">
                            عرض كل الإشعارات <i class="fe-arrow-right align-middle"></i>
                        </a>
                    </div>
                </li>
            @endcan

            {{-- 🌟 قائمة المستخدم 🌟 --}}
            @php
                // تحديد اسم المستخدم حسب نوع الحساب (Guard)
                $userName = 'مستخدم';
                $guardType = 'web';

                if (auth('web')->check()) {
                    $userName = auth('web')->user()->name;
                } elseif (auth('teacher')->check()) {
                    $userName = auth('teacher')->user()->name_ar;
                    $guardType = 'teacher';
                } elseif (auth('student')->check()) {
                    $userName = auth('student')->user()->first_name;
                    $guardType = 'student';
                } elseif (auth('guardian')->check()) {
                    $userName = auth('guardian')->user()->name_ar ?? auth('guardian')->user()->name;
                    $guardType = 'guardian';
                }
            @endphp

            <li class="dropdown notification-list topbar-dropdown">
                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light d-flex align-items-center"
                    data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="{{ asset('backend/assets/images/users/user-1.jpg') }}" alt="user-image"
                        class="rounded-circle shadow-sm border border-light">
                    <span class="pro-user-name ms-2 fw-bold text-dark d-none d-md-block">
                        {{ $userName }} <i class="mdi mdi-chevron-down align-middle"></i>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end profile-dropdown shadow-sm border-0 rounded-3 mt-2">
                    <div class="dropdown-header noti-title bg-light rounded-top-3">
                        <h6 class="text-overflow m-0 fw-bold"><i class="mdi mdi-hand-wave text-warning me-1"></i> مرحباً
                            بك!</h6>
                    </div>

                    {{-- تفعيل زر حسابي للإدارة فقط كمثال --}}
                    @if ($guardType === 'web' && Route::has('admin.profile.edit'))
                        <a href="{{ route('admin.profile.edit') }}"
                            class="dropdown-item notify-item fw-bold text-secondary">
                            <i class="fe-user align-middle me-1"></i> <span>إعدادات حسابي</span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endif

                    <form action="{{ route('logout.custom', $guardType) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="dropdown-item notify-item border-0 bg-transparent w-100 text-start fw-bold text-danger">
                            <i class="fe-log-out align-middle me-1"></i> <span>تسجيل الخروج</span>
                        </button>
                    </form>
                </div>
            </li>

            {{-- 🔥 حماية الترس الخاص بإعدادات الثيم 🔥 --}}
            @can('manage_settings')
                <li class="dropdown notification-list">
                    <a href="javascript:void(0);" class="nav-link waves-effect waves-light" data-bs-toggle="offcanvas"
                        data-bs-target="#theme-settings-offcanvas">
                        <i class="fe-settings noti-icon spin-icon-hover"></i>
                    </a>
                </li>
            @endcan
        </ul>

        {{-- منطقة اللوجو --}}
        <div class="logo-box">
            <a href="/" class="logo logo-light text-center">
                <span class="logo-sm">
                    @if (isset($globalSettings) && $globalSettings->logo)
                        <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" height="25"
                            style="object-fit: contain;">
                    @else
                        <h4 class="text-white mb-0 fw-bold mt-3">SIS</h4>
                    @endif
                </span>
                <span class="logo-lg">
                    @if (isset($globalSettings) && $globalSettings->logo)
                        <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" height="45"
                            style="object-fit: contain; max-width: 100%;">
                    @else
                        <h4 class="text-white mb-0 fw-bold mt-3">
                            {{ $globalSettings->company_name_ar ?? 'نظام المدارس' }}</h4>
                    @endif
                </span>
            </a>
            <a href="/" class="logo logo-dark text-center">
                <span class="logo-sm">
                    @if (isset($globalSettings) && $globalSettings->logo)
                        <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" height="25"
                            style="object-fit: contain;">
                    @else
                        <h4 class="text-dark mb-0 fw-bold mt-3">SIS</h4>
                    @endif
                </span>
                <span class="logo-lg">
                    @if (isset($globalSettings) && $globalSettings->logo)
                        <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo" height="45"
                            style="object-fit: contain; max-width: 100%;">
                    @else
                        <h4 class="text-dark mb-0 fw-bold mt-3">
                            {{ $globalSettings->company_name_ar ?? 'نظام المدارس' }}</h4>
                    @endif
                </span>
            </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
            <li>
                <button class="button-menu-mobile waves-effect">
                    <i class="fe-menu"></i>
                </button>
            </li>
            <li class="d-none d-lg-flex align-items-center">
                <h4 class="page-title-main text-dark fw-bold mb-0 ms-2">@yield('title_page', 'لوحة التحكم')</h4>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
</div>

<style>
    /* حركة النبض لجرس الإشعارات */
    @keyframes pulse-bell {
        0% {
            transform: scale(1);
            opacity: 1;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        }

        50% {
            transform: scale(1.15);
            opacity: 0.8;
            box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
        }

        100% {
            transform: scale(1);
            opacity: 1;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
        }
    }

    /* حركة بسيطة لترس الإعدادات عند الوقوف عليه */
    .spin-icon-hover:hover {
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    /* تحسين شكل عناصر الإشعارات عند الوقوف عليها */
    .notify-item.transition-all {
        transition: all 0.2s ease-in-out;
    }

    .notify-item.transition-all:hover {
        background-color: #f1f5f9 !important;
        transform: translateX(-3px);
    }
</style>
