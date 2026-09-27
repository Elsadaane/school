@extends('backend.layouts.master')
@section('title', 'الفصل الافتراضي | ' . ($virtualClass->topic ?? 'مادة دراسية'))

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    {{-- مكتبة Jitsi الأساسية لعمل البث --}}
    <script src="https://meet.jit.si/external_api.js"></script>

    <style>
        /* 🌟 إخفاء قوائم النظام الافتراضية لعمل Full Screen 🌟 */
        .navbar-custom,
        .left-side-menu,
        .footer,
        .page-title-box {
            display: none !important;
        }

        .content-page {
            margin: 0 !important;
            padding: 0 !important;
            min-height: 100vh;
            background-color: var(--room-bg);
        }

        .container-fluid {
            padding: 0 !important;
            max-width: 100%;
        }

        /* 🌟 المتغيرات والألوان (Light Theme) 🌟 */
        :root {
            --room-bg: #f8fafc;
            --panel-bg: #ffffff;
            --border-color: #e2e8f0;
            --primary-accent: #0d6efd;
            --danger-accent: #ef4444;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --header-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.7) 100%);
            --controls-bg: rgba(255, 255, 255, 0.85);
        }

        body {
            background-color: var(--room-bg);
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .virtual-room-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100vw;
            color: var(--text-main);
        }

        /* 🌟 الترويسة العلوية 🌟 */
        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            background: var(--header-gradient);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .class-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .class-title {
            font-size: 18px;
            font-weight: 800;
            margin: 0;
            color: var(--text-main);
        }

        .class-meta {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .recording-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-accent);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .recording-dot {
            width: 8px;
            height: 8px;
            background-color: var(--danger-accent);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* 🌟 منطقة الفيديو المتصلة بالـ API 🌟 */
        .room-body {
            display: flex;
            flex-grow: 1;
            height: calc(100vh - 80px);
            position: relative;
            padding-top: 75px;
            padding-bottom: 90px;
            /* مساحة لأزرار التحكم */
        }

        .video-area {
            flex-grow: 1;
            width: 100%;
            height: 100%;
            background: #000;
            /* خلفية سوداء للفيديو نفسه */
            border-radius: 12px;
            overflow: hidden;
            margin: 0 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        /* 🌟 شريط التحكم السفلي 🌟 */
        .room-controls {
            height: 80px;
            background: var(--controls-bg);
            backdrop-filter: blur(15px);
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.02);
        }

        .control-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-main);
            font-size: 22px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .control-btn:hover {
            background: #f1f5f9;
            transform: translateY(-3px);
        }

        .control-btn.danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-accent);
            border-color: rgba(239, 68, 68, 0.2);
        }

        .control-btn.leave {
            background: var(--danger-accent);
            color: #fff;
            width: auto;
            padding: 0 25px;
            border-radius: 25px;
            font-size: 15px;
            font-weight: bold;
            border: none;
            position: absolute;
            right: 25px;
        }

        .control-btn.leave:hover {
            background: #dc2626;
        }
    </style>
@endpush

@section('content')
    <div class="virtual-room-wrapper">

        {{-- 🌟 1. الترويسة العلوية 🌟 --}}
        <div class="room-header">
            <div class="class-info">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary border border-primary border-opacity-25">
                    <i class="mdi mdi-video-outline fs-4"></i>
                </div>
                <div>
                    <h1 class="class-title">{{ $virtualClass->topic ?? 'حصة افتراضية' }}</h1>
                    <span class="class-meta">
                        <i class="mdi mdi-account-tie text-primary me-1"></i> المعلم:
                        {{ $virtualClass->teacher->name_ar ?? 'غير محدد' }}
                        <span class="mx-2">|</span>
                        <i class="mdi mdi-clock-outline text-warning me-1"></i> وقت البدء:
                        {{ \Carbon\Carbon::parse($virtualClass->start_time ?? now())->format('h:i A') }}
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="recording-badge" id="rec-status" style="display: none;">
                    <div class="recording-dot"></div> قيد التسجيل
                </div>
                <div class="text-success font-13 fw-bold bg-success bg-opacity-10 px-3 py-1 rounded-pill border border-success border-opacity-25"
                    id="network-status" data-bs-toggle="tooltip" title="اتصال مستقر">
                    <i class="mdi mdi-wifi"></i> متصل
                </div>
            </div>
        </div>

        {{-- 🌟 2. حاوية الفيديو المدمجة مع API 🌟 --}}
        <div class="room-body">
            <div class="video-area" id="jitsi-container"></div>
        </div>

        {{-- 🌟 3. شريط التحكم السفلي المربوط بالـ API 🌟 --}}
        <div class="room-controls">
            <button class="control-btn" data-bs-toggle="tooltip" title="كتم / تشغيل المايك" id="toggle-mic">
                <i class="mdi mdi-microphone"></i>
            </button>

            <button class="control-btn" data-bs-toggle="tooltip" title="إيقاف / تشغيل الكاميرا" id="toggle-cam">
                <i class="mdi mdi-video"></i>
            </button>

            <button class="control-btn" data-bs-toggle="tooltip" title="مشاركة الشاشة" id="share-screen">
                <i class="mdi mdi-monitor-share"></i>
            </button>

            <button class="control-btn" data-bs-toggle="tooltip" title="رفع اليد للمشاركة" id="raise-hand">
                <i class="mdi mdi-hand-back-right-outline"></i>
            </button>

            <button class="control-btn leave" id="leave-room-btn">
                <i class="mdi mdi-phone-hangup me-2"></i> مغادرة الفصل
            </button>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // تهيئة الـ Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    boundary: document.body
                });
            });

            // 🌟 1. تهيئة Jitsi Meet API 🌟
            // const domain = 'meet.jit.si'; // يمكنك تغييره لخادم Jitsi الخاص بك إذا وجد
            // const domain = 'meet.ffmuc.net';
            const domain = 'meet.jit.si';
            const options = {
                roomName: '{{ $virtualClass->meeting_id ?? 'SmartClass_' . rand(1000, 9999) }}',
                width: '100%',
                height: '100%',
                parentNode: document.querySelector('#jitsi-container'),
                userInfo: {
                    displayName: '{{ $displayName ?? auth()->user()->name }}'
                },
                configOverwrite: {
                    startWithAudioMuted: false,
                    startWithVideoMuted: false,
                    prejoinPageEnabled: false // تخطي صفحة الانتظار ليدخل مباشرة
                },
                interfaceConfigOverwrite: {
                    // إخفاء أزرار Jitsi الافتراضية للاعتماد على تصميمنا
                    TOOLBAR_BUTTONS: ['chat', 'participants-pane', 'settings', 'tileview'],
                    SHOW_CHROME_EXTENSION_BANNER: false
                }
            };

            const api = new JitsiMeetExternalAPI(domain, options);

            // 🌟 2. ربط أزرار التصميم الخاص بنا بـ Jitsi API 🌟

            // المايك
            let isAudioMuted = false;
            $('#toggle-mic').click(function() {
                api.executeCommand('toggleAudio');
                isAudioMuted = !isAudioMuted;
                $(this).toggleClass('danger');
                let icon = $(this).find('i');
                isAudioMuted ? icon.removeClass('mdi-microphone').addClass('mdi-microphone-off') :
                    icon.removeClass('mdi-microphone-off').addClass('mdi-microphone');
            });

            // الكاميرا
            let isVideoMuted = false;
            $('#toggle-cam').click(function() {
                api.executeCommand('toggleVideo');
                isVideoMuted = !isVideoMuted;
                $(this).toggleClass('danger');
                let icon = $(this).find('i');
                isVideoMuted ? icon.removeClass('mdi-video').addClass('mdi-video-off') :
                    icon.removeClass('mdi-video-off').addClass('mdi-video');
            });

            // مشاركة الشاشة
            $('#share-screen').click(function() {
                api.executeCommand('toggleShareScreen');
                $(this).toggleClass('bg-primary text-white border-primary');
            });

            // رفع اليد
            $('#raise-hand').click(function() {
                api.executeCommand('toggleRaiseHand');
                $(this).toggleClass('bg-warning text-dark border-warning');
            });

            // تتبع حالة التسجيل من Jitsi لإنارة الـ Badge
            api.addListener('recordingStatusChanged', function(event) {
                if (event.on) {
                    $('#rec-status').fadeIn();
                } else {
                    $('#rec-status').fadeOut();
                }
            });

            // 🌟 3. تأكيد مغادرة الغرفة 🌟
            $('#leave-room-btn').click(function() {
                Swal.fire({
                    title: 'مغادرة الفصل؟',
                    text: "هل أنت متأكد من رغبتك في الخروج من البث المباشر؟",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، غادر',
                    cancelButtonText: 'البقاء في الفصل'
                }).then((result) => {
                    if (result.isConfirmed) {
                        api.dispose(); // إنهاء الاتصال بـ Jitsi
                        // توجيه المستخدم للوحة التحكم
                        window.location.href =
                            "{{ auth('student')->check() ? route('student.dashboard') : route('admin.virtual-classes.index') }}";
                    }
                });
            });
        });
    </script>
@endpush
