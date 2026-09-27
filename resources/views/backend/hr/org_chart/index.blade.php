@extends('backend.layouts.master')
@section('title', 'الهيكل التنظيمي للمؤسسة')
@section('title_page', 'الموارد البشرية - الهيكل التنظيمي')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/orgchart/3.8.0/css/jquery.orgchart.min.css">

    <style>
        .page-wrapper {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 25px;
            min-height: 80vh;
        }

        /* 🌟 الكروت والأزرار الموحدة 🌟 */
        .card-premium {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
            overflow: hidden;
        }

        .btn-modern {
            border-radius: 8px;
            font-weight: 600;
            padding: 9px 18px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-dark-modern {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: white;
            border: none;
            box-shadow: 0 4px 10px rgba(30, 41, 59, 0.2);
        }

        .btn-dark-modern:hover {
            background: linear-gradient(135deg, #0f172a, #000000);
            color: white;
            transform: translateY(-2px);
        }

        /* 🌟 تنسيقات حاوية الشجرة التفاعلية 🌟 */
        #chart-container {
            width: 100%;
            height: 650px;
            background-color: #f8fafc;
            border-radius: 10px;
            overflow: auto;
            text-align: center;
            border: 2px dashed #e2e8f0;
            position: relative;
        }

        .orgchart {
            background-image: none !important;
            padding: 20px;
        }

        /* تنسيق الصناديق الافتراضية للموظفين */
        .orgchart .node {
            width: 220px !important;
        }

        .orgchart .node .title {
            background-color: #1e293b !important;
            font-family: inherit;
            border-radius: 8px 8px 0 0;
            font-weight: 700;
            padding: 10px !important;
            font-size: 14px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .orgchart .node .content {
            border: 1px solid #1e293b !important;
            border-top: none !important;
            background-color: #fff !important;
            font-family: inherit;
            font-weight: 600;
            font-size: 12px;
            color: #475569;
            border-radius: 0 0 8px 8px;
            padding: 10px !important;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            text-align: right;
            gap: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        /* 🌟 تنسيق الصورة الشخصية داخل الصندوق 🌟 */
        .orgchart .node .node-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            flex-shrink: 0;
            background-color: #f1f5f9;
            padding: 2px;
        }

        /* 🌟 التمييز اللوني للإدارة العليا (Top Management) 🌟 */
        .orgchart .top-level .title {
            background-color: #dc2626 !important;
            font-size: 15px;
        }

        .orgchart .top-level .content {
            border-color: #dc2626 !important;
            color: #b91c1c;
            font-weight: 800;
            background-color: #fef2f2 !important;
        }

        .orgchart .top-level .node-avatar {
            border-color: #fca5a5;
        }

        /* 🌟 إصلاح شكل زر تصدير الصورة الافتراضي من المكتبة 🌟 */
        .oc-export-btn {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: #fff !important;
            border: none !important;
            padding: 10px 20px !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
            font-family: inherit !important;
            top: 20px !important;
            right: 20px !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3) !important;
            transition: all 0.3s ease !important;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .oc-export-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4) !important;
        }

        /* إعدادات الطباعة */
        @media print {
            .page-wrapper {
                background: white;
                padding: 0;
            }

            .card-premium,
            #chart-container {
                box-shadow: none;
                border: none;
            }

            .btn,
            .alert,
            .oc-export-btn,
            .card-header {
                display: none !important;
            }

            #chart-container {
                height: auto;
                overflow: visible;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🔥 حماية الشاشة بالكامل بصلاحية مشاهدة الموظفين 🔥 --}}
        @can('view_employees')
            <div class="card card-premium mb-4">
                <div class="card-body d-flex justify-content-between align-items-center p-4 flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary shadow-sm">
                            <i class="bx bx-sitemap fs-2"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold text-dark">الهيكل التنظيمي للمؤسسة</h4>
                            <p class="text-muted mb-0 font-13">عرض تسلسل الإدارات والموظفين بشكل تفاعلي وذكي.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-modern btn-dark-modern" onclick="window.print()">
                            <i class='bx bx-printer fs-5'></i> طباعة المخطط
                        </button>
                    </div>
                </div>
            </div>

            <div class="card card-premium">
                <div class="card-body p-4">
                    <div
                        class="alert alert-info border-0 bg-info bg-opacity-10 text-dark fw-bold mb-4 d-flex align-items-center shadow-sm rounded-3">
                        <i class='bx bx-bulb text-info fs-3 me-3 flex-shrink-0'></i>
                        <span>
                            <strong class="text-info fs-6">تلميح ذكي:</strong> يمكنك التقريب والتبعيد عبر عجلة الماوس (Scroll)
                            وسحب الشاشة يميناً ويساراً لرؤية الشجرة الإدارية. النقر على أي صندوق يقوم بطي أو تمديد الإدارات
                            التابعة له.
                        </span>
                    </div>

                    {{-- حاوية رسم الهيكل الإداري --}}
                    <div id="chart-container"></div>
                </div>
            </div>
        @else
            <div class="alert alert-danger text-center py-5 shadow-sm rounded-4 border-0 mt-5 bg-white">
                <i class="bx bx-shield-x text-danger mb-3" style="font-size: 80px;"></i>
                <h3 class="fw-bolder text-danger">عفواً، الدخول محظور!</h3>
                <p class="text-dark fw-bold font-15 mb-4">أنت لا تملك الصلاحيات الإدارية للاطلاع على الهيكل التنظيمي للشركة.</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-dark-modern px-5 py-2 fw-bold">العودة للرئيسية</a>
            </div>
        @endcan

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/orgchart/3.8.0/js/jquery.orgchart.min.js"></script>
    {{-- إضافة مكتبة html2canvas لأن orgchart بيحتاجها عشان ينزل الشارت كصورة --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        $(document).ready(function() {
            @can('view_employees')
                // استلام مصفوفة الشجرة القادمة من الكنترولر وتحويلها برمجياً لـ JSON
                var datasource = @json($treeData ?? []);

                // صورة افتراضية في حالة عدم وجود صورة للموظف
                var defaultAvatar = "{{ asset('backend/assets/images/users/avatar-1.png') }}";

                // تشغيل وإعداد مكتبة رسم الخرائط التنظيمية
                $('#chart-container').orgchart({
                    'data': datasource,
                    'nodeContent': 'title',
                    'pan': true, // تفعيل السحب (Drag)
                    'zoom': true, // تفعيل الزووم (Scroll)
                    'direction': 't2b', // من الأعلى للأسفل
                    'exportButton': true, // تفعيل زر تصدير الصورة
                    'exportFilename': 'Org_Chart',

                    // 🌟 السحر هنا: حقن الصورة الشخصية للموظف بشكل تفاعلي وديناميكي جوه النود 🌟
                    'createNode': function($node, data) {
                        let imageUrl = data.image ? data.image : defaultAvatar;

                        // إضافة onerror لحماية الصورة لو الرابط مكسور
                        let avatarHtml =
                            `<img src="${imageUrl}" class="node-avatar shadow-sm" onerror="this.onerror=null; this.src='${defaultAvatar}'">`;

                        // وضع الصورة بجوار المسمى الوظيفي
                        $node.find('.content').prepend(avatarHtml);

                        // إذا كان ده المدير التنفيذي أو الإدارة العليا نلونه أحمر (عن طريق كلاس مخصص)
                        if (data.id == 1 || data.className == 'top-level') {
                            $node.addClass('top-level');
                        }
                    }
                });

                // تغيير نص زرار الـ Export الإنجليزي الافتراضي ليصبح عربي بتصميم متناسق
                setTimeout(() => {
                    $('.oc-export-btn').html("<i class='bx bx-download fs-5'></i> حفظ المخطط كصورة");
                }, 500);
            @endcan
        });
    </script>
@endpush
