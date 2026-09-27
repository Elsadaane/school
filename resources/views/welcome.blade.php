<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>بوابة الدخول | {{ $globalSettings->company_name_ar ?? 'نظام إدارة المدارس' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css">

    <style>
        /* ================= Reset & Basics ================= */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f4f7f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            padding: 20px;
            z-index: 10;
        }

        a {
            text-decoration: none;
        }

        h1,
        h2,
        h3,
        h4 {
            letter-spacing: -0.5px;
        }

        /* ================= Header ================= */
        .header-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .logo-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 15px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            min-height: 100px;
            min-width: 250px;
        }

        .logo-box img {
            max-height: 70px;
            object-fit: contain;
        }

        .header-section h1 {
            font-size: 2.8rem;
            font-weight: 900;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .header-section p {
            font-size: 1.2rem;
            color: #64748b;
            font-weight: 600;
        }

        /* ================= Cards Grid ================= */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }

        .magic-card {
            border-radius: 40px;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            color: white;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            cursor: pointer;
        }

        /* ألوان الكروت وتدرجاتها */
        .card-admin {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.5);
        }

        .card-teacher {
            background: linear-gradient(135deg, #3b82f6, #4f46e5);
            box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.5);
        }

        .card-student {
            background: linear-gradient(135deg, #10b981, #047857);
            box-shadow: 0 20px 40px -10px rgba(4, 120, 87, 0.5);
        }

        .card-parent {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 20px 40px -10px rgba(217, 119, 6, 0.5);
        }

        /* حركة الكارت عند مرور الماوس وتأثير الإضاءة (Neon Hover) */
        .magic-card:hover {
            transform: translateY(-15px) scale(1.03);
            filter: drop-shadow(0 15px 15px rgba(0, 0, 0, 0.15));
        }

        /* الأيقونات داخل الكروت */
        .icon-circle {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            width: 90px;
            height: 90px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .icon-circle i {
            font-size: 3rem;
            color: white;
        }

        .magic-card h2 {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .magic-card p {
            font-size: 0.95rem;
            opacity: 0.85;
        }

        /* ================= Banner ================= */
        .registration-banner {
            background: white;
            border-radius: 25px;
            padding: 15px 20px 15px 30px;
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 800px;
            margin: 0 auto;
            flex-wrap: wrap;
            gap: 20px;
        }

        .banner-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .banner-icon {
            background: #eff6ff;
            color: #2563eb;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .banner-text h3 {
            color: #1e293b;
            font-size: 1.3rem;
            font-weight: 800;
        }

        .banner-text p {
            color: #64748b;
            font-size: 0.95rem;
            margin-top: 4px;
        }

        .banner-btn {
            background: #0f172a;
            color: white;
            padding: 15px 35px;
            border-radius: 15px;
            font-weight: 800;
            font-size: 1.1rem;
            transition: 0.3s;
        }

        .banner-btn:hover {
            background: #334155;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* ================= Footer ================= */
        .footer {
            text-align: center;
            margin-top: 60px;
            color: #94a3b8;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .footer span {
            color: #2563eb;
            font-weight: 800;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 2.2rem;
            }

            .registration-banner {
                justify-content: center;
                text-align: center;
                padding: 25px;
            }

            .banner-info {
                flex-direction: column;
                gap: 10px;
            }

            .banner-btn {
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .header-section h1 {
                font-size: 1.8rem !important;
            }

            .cards-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 0 10px;
            }

            .magic-card {
                padding: 30px 15px;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header-section">
            <div class="logo-box">
                {{-- 🔥 سحب اللوجو من الإعدادات العامة (Public Settings) 🔥 --}}
                @if (isset($globalSettings) && $globalSettings->logo)
                    <img src="{{ asset('storage/' . $globalSettings->logo) }}" alt="Logo">
                @else
                    <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="Logo">
                @endif
            </div>

            {{-- 🔥 سحب اسم المدرسة من الإعدادات العامة 🔥 --}}
            <h1>{{ $globalSettings->company_name_ar ?? 'مدارس الطالب الذكي' }}</h1>
            <p>خطوتك الأولى نحو تجربة تعليمية استثنائية</p>
        </div>

        <div class="cards-grid">

            <a href="{{ route('login.show', 'web') }}" class="magic-card card-admin">
                <div class="icon-circle">
                    <i class="mdi mdi-shield-crown-outline"></i>
                </div>
                <h2>بوابة الإدارة</h2>
                <p>التحكم والإشراف العام</p>
            </a>

            <a href="{{ route('login.show', 'teacher') }}" class="magic-card card-teacher">
                <div class="icon-circle">
                    <i class="mdi mdi-human-male-board"></i>
                </div>
                <h2>بوابة المعلم</h2>
                <p>إدارة الفصول والدرجات</p>
            </a>

            <a href="{{ route('login.show', 'student') }}" class="magic-card card-student">
                <div class="icon-circle">
                    <i class="mdi mdi-school-outline"></i>
                </div>
                <h2>بوابة الطالب</h2>
                <p>الواجبات والمقررات</p>
            </a>

            <a href="{{ route('login.show', 'parent') }}" class="magic-card card-parent">
                <div class="icon-circle">
                    <i class="mdi mdi-human-male-child"></i>
                </div>
                <h2>بوابة ولي الأمر</h2>
                <p>متابعة الأبناء والماليات</p>
            </a>

        </div>

        <div class="registration-banner">
            <div class="banner-info">
                <div class="banner-icon">
                    <i class="mdi mdi-file-document-edit-outline"></i>
                </div>
                <div class="banner-text">
                    <h3>هل أنتم طلاب جُدد؟</h3>
                    <p>استمارة التسجيل للعام الدراسي الجديد مفتوحة الآن.</p>
                </div>
            </div>
            <a href="#" class="banner-btn" rel="noopener noreferrer">سجل الآن</a>
        </div>

        <div class="footer">
            برمجة وتطوير <span>عبدالرحمن السعدني</span> &copy; {{ date('Y') }}
        </div>

    </div>

</body>

</html>
