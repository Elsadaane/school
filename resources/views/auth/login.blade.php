<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول | مدارس الطالب الذكي</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">

    <style>
        /* ================= Reset & Basics ================= */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: #f4f7f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
        }

        /* ================= Layout ================= */
        .login-wrapper {
            width: 100%;
            max-width: 1000px;
            background: #ffffff;
            border-radius: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            display: flex;
            overflow: hidden;
            min-height: 600px;
            margin: 20px;
        }

        /* ================= Sidebar (Right Side) ================= */
        .login-sidebar {
            width: 50%;
            /* الألوان هنا هتاخدها ديناميك من الـ PHP تحت */
            background: linear-gradient(135deg, var(--theme-c1), var(--theme-c2));
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: relative;
            text-align: center;
        }

        /* أشكال هندسية في الخلفية الملونة */
        .sidebar-shape-1 {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            filter: blur(30px);
        }

        .sidebar-shape-2 {
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 300px;
            height: 300px;
            background: rgba(0, 0, 0, 0.15);
            border-radius: 50%;
            filter: blur(40px);
        }

        .huge-icon {
            position: absolute;
            right: -40px;
            bottom: -40px;
            font-size: 250px;
            opacity: 0.05;
            transform: rotate(-15deg);
        }

        .sidebar-content {
            position: relative;
            z-index: 10;
        }

        .sidebar-icon-box {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 25px;
            display: inline-block;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .sidebar-icon-box i {
            font-size: 60px;
        }

        .sidebar-content h2 {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 15px;
        }

        .sidebar-subtitle {
            background: rgba(0, 0, 0, 0.2);
            padding: 8px 25px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 20px;
        }

        .sidebar-text {
            font-size: 0.95rem;
            opacity: 0.8;
            line-height: 1.8;
            max-width: 80%;
            margin: 0 auto;
        }

        /* ================= Form Area (Left Side) ================= */
        .login-form-area {
            width: 50%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* لوجو الموبايل المخفي في الشاشات الكبيرة */
        .mobile-logo {
            display: none;
            text-align: center;
            margin-bottom: 30px;
        }

        .mobile-logo img {
            height: 70px;
            margin-bottom: 15px;
        }

        .form-header {
            margin-bottom: 40px;
            text-align: right;
        }

        .form-header h3 {
            font-size: 2.2rem;
            font-weight: 900;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .form-header p {
            color: #64748b;
            font-weight: 700;
            font-size: 1rem;
        }

        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-weight: 800;
            color: #334155;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            color: #94a3b8;
            transition: 0.3s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 18px 50px 18px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            background: #f8fafc;
            transition: 0.3s;
            text-align: left;
            direction: ltr;
        }

        .input-wrapper input:focus {
            background: #ffffff;
            border-color: var(--theme-btn);
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.05);
        }

        .input-wrapper input:focus+i {
            color: var(--theme-btn);
        }

        .error-msg {
            color: #ef4444;
            font-size: 0.85rem;
            font-weight: 700;
            margin-top: 8px;
            display: block;
        }

        /* خيارات إضافية */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .checkbox-wrapper input {
            width: 18px;
            height: 18px;
            margin-left: 10px;
            cursor: pointer;
            accent-color: var(--theme-btn);
        }

        .checkbox-wrapper span {
            font-weight: 700;
            color: #475569;
            font-size: 0.95rem;
        }

        .forgot-pass {
            font-weight: 800;
            color: var(--theme-btn);
            font-size: 0.95rem;
            transition: 0.3s;
        }

        .forgot-pass:hover {
            opacity: 0.8;
        }

        /* زر الدخول */
        .submit-btn {
            width: 100%;
            background: var(--theme-btn);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: 900;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
            box-shadow: 0 10px 20px -5px var(--theme-btn);
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -5px var(--theme-btn);
        }

        /* زر العودة */
        .back-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-weight: 800;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .back-btn:hover {
            color: #0f172a;
        }

        .back-btn i {
            font-size: 20px;
        }

        /* ================= Responsive (Mobiles) ================= */
        @media (max-width: 900px) {
            .login-sidebar {
                display: none;
            }

            .login-form-area {
                width: 100%;
                padding: 40px 30px;
            }

            .mobile-logo {
                display: block;
            }

            .form-header {
                text-align: center;
            }

            .form-options {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>

{{-- 🔥 خوارزمية الألوان الديناميكية (PHP) 🔥 --}}
@php
    $theme = match ($type) {
        'web' => [
            'c1' => '#1e293b',
            'c2' => '#0f172a',
            'btn' => '#1e293b',
            'icon' => 'mdi-shield-crown-outline',
            'title' => 'بوابة الإدارة',
            'sub' => 'التحكم والإشراف العام',
        ],
        'teacher' => [
            'c1' => '#3b82f6',
            'c2' => '#4f46e5',
            'btn' => '#4f46e5',
            'icon' => 'mdi-human-male-board',
            'title' => 'بوابة المعلم',
            'sub' => 'إدارة الفصول والدرجات',
        ],
        'student' => [
            'c1' => '#10b981',
            'c2' => '#047857',
            'btn' => '#059669',
            'icon' => 'mdi-school-outline',
            'title' => 'بوابة الطالب',
            'sub' => 'الواجبات والمقررات',
        ],
        'parent' => [
            'c1' => '#f59e0b',
            'c2' => '#ea580c',
            'btn' => '#ea580c',
            'icon' => 'mdi-human-male-child',
            'title' => 'بوابة ولي الأمر',
            'sub' => 'متابعة الأبناء والماليات',
        ],
        default => [
            'c1' => '#1e293b',
            'c2' => '#0f172a',
            'btn' => '#1e293b',
            'icon' => 'mdi-login',
            'title' => 'تسجيل الدخول',
            'sub' => 'مرحباً بك',
        ],
    };
@endphp

<body style="--theme-c1: {{ $theme['c1'] }}; --theme-c2: {{ $theme['c2'] }}; --theme-btn: {{ $theme['btn'] }};">

    <div class="login-wrapper">

        <div class="login-sidebar">
            <div class="sidebar-shape-1"></div>
            <div class="sidebar-shape-2"></div>
            <i class="mdi {{ $theme['icon'] }} huge-icon"></i>

            <div class="sidebar-content">
                <div class="sidebar-icon-box">
                    <i class="mdi {{ $theme['icon'] }}"></i>
                </div>
                <h2>{{ $theme['title'] }}</h2>
                <div class="sidebar-subtitle">{{ $theme['sub'] }}</div>
                <p class="sidebar-text">
                    يرجى إدخال بيانات الاعتماد الخاصة بك للوصول إلى لوحة التحكم المخصصة لك بأمان وسرية.
                </p>
            </div>
        </div>

        <div class="login-form-area">

            <div class="mobile-logo">
                <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="Logo">
                <h2>{{ $theme['title'] }}</h2>
            </div>

            <div class="form-header">
                <h3>مرحباً بعودتك! 👋</h3>
                <p>أدخل بريدك الإلكتروني وكلمة المرور للمتابعة</p>
            </div>

            @if (session('status'))
                <div
                    style="background: #fee2e2; color: #ef4444; padding: 15px; border-radius: 10px; font-weight: bold; margin-bottom: 20px; text-align: center;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.custom') }}">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="input-group">
                    <label for="email">البريد الإلكتروني</label>
                    <div class="input-wrapper">
                        <input id="email" name="email" type="email" autocomplete="email" required autofocus
                            value="{{ old('email') }}" placeholder="example@school.com">
                        <i class="mdi mdi-email-outline"></i>
                    </div>
                    @error('email')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-wrapper">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            placeholder="••••••••">
                        <i class="mdi mdi-lock-outline"></i>
                    </div>
                    @error('password')
                        <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember" id="remember_me">
                        <span>تذكرني</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-pass">نسيت كلمة المرور؟</a>
                    @endif
                </div>

                <button type="submit" class="submit-btn">
                    <span>تسجيل الدخول</span>
                    <i class="mdi mdi-arrow-left-thick"></i>
                </button>
            </form>

            <a href="{{ url('/') }}" class="back-btn">
                <i class="mdi mdi-home-circle-outline"></i>
                العودة للبوابة الرئيسية
            </a>

        </div>
    </div>

</body>

</html>
