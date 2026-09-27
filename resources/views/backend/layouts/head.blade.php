<head>
    <meta charset="utf-8" />

    {{-- 🔥 عنوان الصفحة ديناميكي ومربوط باسم المدرسة من الإعدادات 🔥 --}}
    <title>@yield('title', $globalSettings->company_name_ar ?? 'Smart Students')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ $globalSettings->company_name_ar ?? 'نظام إدارة مدارس الطلاب الأذكياء' }}" name="description" />
    <meta content="عبدالرحمن السيد السعدني" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <style>
        #cke_notification_warning,
        .cke_notification {
            display: none;
        }
    </style>

    {{-- توكن الـ CSRF - ضروري لعمليات الـ AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 🔥 أيقونة المتصفح (Favicon) ديناميكية من لوجو المدرسة 🔥 --}}
    @if (isset($globalSettings) && $globalSettings->logo)
        <link rel="shortcut icon" href="{{ asset('storage/' . $globalSettings->logo) }}">
    @else
        <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">
    @endif

    <script src="{{ asset('backend/assets/js/config.js') }}"></script>

    {{-- دعم تعدد اللغات (RTL & LTR) --}}
    @if (app()->getLocale() == 'ar')
        <link href="{{ asset('backend/assets/css/app-rtl.min.css') }}" rel="stylesheet" type="text/css"
            id="app-style" />
    @else
        <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    @endif

    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    {{-- مكان مخصص لو عايز تضيف CSS لصفحة معينة بس --}}
    @stack('css')
</head>
