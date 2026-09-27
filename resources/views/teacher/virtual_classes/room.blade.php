@extends('backend.layouts.master')
@section('title', 'الفصل الافتراضي')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        .virtual-room-wrapper {
            background-color: #000;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        #jitsi-meet-container {
            width: 100%;
            height: 75vh;
            border-radius: 12px;
        }

        .teacher-tools-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            height: 75vh;
            display: flex;
            flex-direction: column;
        }

        .pulse-live {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: #dc3545;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse-red 1.5s infinite;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        .tool-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
            text-decoration: none;
            color: #475569;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .tool-btn:hover {
            background-color: #e0e7ff;
            color: #3730a3;
            border-color: #c7d2fe;
            transform: translateX(-5px);
        }

        .tool-btn i {
            font-size: 22px;
        }

        /* 🌟 Dark Mode 🌟 */
        body[data-layout-color="dark"] .teacher-tools-card {
            background: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .tool-btn {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #ced4da;
        }

        body[data-layout-color="dark"] .tool-btn:hover {
            background-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold text-dark mb-1">
                    <span class="pulse-live"></span>
                    بث مباشر: <span class="text-primary">{{ $subjectName }}</span>
                </h4>
                <p class="text-muted font-14 mb-0">
                    <i class="mdi mdi-google-classroom text-info me-1"></i> شعبة: <strong>{{ $sectionName }}</strong> |
                    <i class="mdi mdi-shield-check text-success ms-2 me-1"></i> الاتصال مشفر وآمن
                </p>
            </div>
            <div>
                <button id="end-call-btn" class="btn btn-danger rounded-pill shadow-sm font-bold px-4">
                    <i class="mdi mdi-phone-hangup me-1"></i> إنهاء الحصة والمغادرة
                </button>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- 🔥 حاوية البث المباشر (تأخذ 9 أعمدة) 🔥 --}}
        <div class="col-lg-9">
            <div class="virtual-room-wrapper">
                <div id="jitsi-meet-container"></div>
            </div>
        </div>

        {{-- 🔥 أدوات المعلم الجانبية (تأخذ 3 أعمدة) 🔥 --}}
        <div class="col-lg-3">
            <div class="teacher-tools-card p-3 shadow-sm">
                <h5 class="fw-bold border-bottom pb-3 mb-3 text-dark">
                    <i class="mdi mdi-toolbox-outline text-primary me-1"></i> أدوات المعلم
                </h5>

                <div class="d-flex flex-column gap-3 flex-grow-1">
                    {{-- هنفترض أنك تمرر $schedule_id من الكنترولر مع البيانات التانية --}}

                    <a href="{{ route('teacher.attendance.create', $schedule_id ?? 1) }}" target="_blank" class="tool-btn">
                        <i class="mdi mdi-account-check-outline text-success"></i>
                        <span>أخذ الغياب (الحضور)</span>
                    </a>

                    <a href="{{ route('teacher.evaluations.create', $schedule_id ?? 1) }}" target="_blank" class="tool-btn">
                        <i class="mdi mdi-star-outline text-warning"></i>
                        <span>رصد التقييمات والملاحظات</span>
                    </a>

                    <div class="mt-auto border-top pt-3">
                        <p class="text-muted font-12 mb-2 fw-bold">حالة الجلسة:</p>
                        <div class="bg-light p-2 rounded-3 text-center border">
                            <span class="d-block font-24 fw-bold text-primary" id="participants-count">1</span>
                            <span class="font-12 text-muted">متصل الآن</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://meet.jit.si/external_api.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const domain = 'meet.jit.si';

            // تهيئة إعدادات Jitsi
            const options = {
                roomName: "{{ $roomName }}",
                width: '100%',
                height: '100%',
                parentNode: document.querySelector('#jitsi-meet-container'),
                userInfo: {
                    email: "{{ auth()->user()->email ?? '' }}",
                    displayName: "{{ auth()->user()->name_ar ?? (auth()->user()->name ?? 'المعلم') }}"
                },
                configOverwrite: {
                    prejoinPageEnabled: false,
                    startWithAudioMuted: false,
                    startWithVideoMuted: false,
                    disableDeepLinking: true,
                    defaultLanguage: 'ar'
                },
                interfaceConfigOverwrite: {
                    SHOW_JITSI_WATERMARK: false,
                    SHOW_WATERMARK_FOR_GUESTS: false,
                    SHOW_BRAND_WATERMARK: false,
                    TOOLBAR_BUTTONS: [
                        'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                        'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                        'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                        'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                        'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone', 'security'
                    ],
                    DEFAULT_BACKGROUND: '#1e293b',
                    LANG_DETECTION: true,
                }
            };

            // تشغيل الـ API
            const api = new JitsiMeetExternalAPI(domain, options);

            // 🔥 تغيير اسم الحصة من الداخل ليظهر للطلاب بوضوح 🔥
            api.executeCommand('subject', '{{ $subjectName }} - {{ $sectionName }}');

            // 1. تتبع الانضمام والمغادرة لضبط العداد (Participants Count)
            let participantsCount = 1; // المعلم

            api.addListener('participantJoined', function(participant) {
                participantsCount++;
                $('#participants-count').text(participantsCount);

                // إشعار غير مزعج بدخول الطالب
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: 'success',
                    title: `${participant.formattedDisplayName} انضم للحصة`
                });
            });

            api.addListener('participantLeft', function(participant) {
                if (participantsCount > 1) participantsCount--;
                $('#participants-count').text(participantsCount);
            });

            // 2. مغادرة المعلم وإنهاء الحصة
            api.addListener('videoConferenceLeft', function() {
                window.location.href = "{{ route('teacher.schedule') }}";
            });

            // زر الإنهاء الخارجي
            $('#end-call-btn').on('click', function() {
                Swal.fire({
                    title: 'إنهاء البث؟',
                    text: "هل أنت متأكد من رغبتك في إنهاء الحصة ومغادرة البث المباشر؟",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، قم بالإنهاء',
                    cancelButtonText: 'تراجع'
                }).then((result) => {
                    if (result.isConfirmed) {
                        api.dispose(); // إنهاء الـ Iframe
                        window.location.href = "{{ route('teacher.schedule') }}";
                    }
                });
            });
        });
    </script>
@endpush
