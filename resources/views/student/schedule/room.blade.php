@extends('backend.layouts.master')
@section('title', 'غرفة البث المباشر')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        /* 🌟 حاوية البث المباشر 🌟 */
        .virtual-room-wrapper {
            background-color: #000;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        #jitsi-meet-container {
            width: 100%;
            height: 75vh;
            border-radius: 12px;
        }

        /* 🌟 اللوحة الجانبية (معلومات وقواعد) 🌟 */
        .class-info-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            height: 75vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
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

        .rules-list {
            padding: 0;
            list-style: none;
            margin: 0;
        }

        .rules-list li {
            padding: 12px 15px;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #f1f5f9;
        }

        .rules-list li i {
            font-size: 20px;
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
                    <i class="mdi mdi-account-tie text-info me-1"></i> المعلم: أ. <strong>{{ $teacherName }}</strong>
                </p>
            </div>
            <button id="leave-btn" class="btn btn-danger rounded-pill shadow-sm font-bold px-4">
                <i class="mdi mdi-phone-hangup me-1"></i> مغادرة الحصة
            </button>
        </div>
    </div>

    <div class="row g-3">
        {{-- 🔥 حاوية البث المباشر (تأخذ 9 أعمدة) 🔥 --}}
        <div class="col-lg-9">
            <div class="virtual-room-wrapper">
                <div id="jitsi-meet-container"></div>
            </div>
        </div>

        {{-- 🔥 اللوحة الجانبية للطالب (تأخذ 3 أعمدة) 🔥 --}}
        <div class="col-lg-3">
            <div class="class-info-card p-3">
                <div class="text-center mb-4 mt-2">
                    <div class="avatar-md mx-auto mb-2">
                        <span class="avatar-title bg-soft-primary text-primary rounded-circle fs-3 shadow-sm">
                            <i class="mdi mdi-book-open-variant"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-0">{{ $subjectName }}</h5>
                    <span class="badge bg-success bg-opacity-10 text-success mt-2 rounded-pill px-3 py-1">الاتصال آمن
                        ومشفر</span>
                </div>

                <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                    <i class="mdi mdi-shield-alert-outline text-warning me-1"></i> آداب الفصل الافتراضي
                </h6>

                <ul class="rules-list flex-grow-1">
                    <li>
                        <i class="mdi mdi-microphone-off text-danger"></i>
                        الميكروفون مغلق تلقائياً، افتحه فقط عند إذن المعلم.
                    </li>
                    <li>
                        <i class="mdi mdi-hand-back-right text-warning"></i>
                        استخدم زر "رفع اليد" إذا أردت المشاركة أو طرح سؤال.
                    </li>
                    <li>
                        <i class="mdi mdi-message-text-outline text-info"></i>
                        استخدم الدردشة (Chat) للأسئلة المتعلقة بالدرس فقط.
                    </li>
                    <li>
                        <i class="mdi mdi-video-off text-secondary"></i>
                        الكاميرا مغلقة لضمان الخصوصية وسرعة الاتصال.
                    </li>
                </ul>

                <div class="mt-auto border-top pt-3 text-center">
                    <p class="text-muted font-11 mb-0">
                        <i class="mdi mdi-help-circle-outline"></i> إذا واجهت مشكلة في الصوت، تأكد من إعدادات المتصفح.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- استدعاء مكتبة Jitsi الرسمية --}}
    <script src="https://meet.jit.si/external_api.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // const domain = 'meet.jit.si';
            // const domain = 'meet.ffmuc.net';
            const domain = 'meet.jit.si';

            const options = {
                roomName: "{{ $roomName }}",
                width: '100%',
                height: '100%',
                parentNode: document.querySelector('#jitsi-meet-container'),
                userInfo: {
                    displayName: "{{ auth('student')->user()->first_name ?? 'طالب' }} {{ auth('student')->user()->tribe ?? '' }}"
                },
                configOverwrite: {
                    prejoinPageEnabled: false, // الدخول مباشرة للغرفة بدون شاشة انتظار
                    startWithAudioMuted: true, // 🔇 الطالب يدخل والمايك مقفول
                    startWithVideoMuted: true, // 📷 الطالب يدخل والكاميرا مقفولة
                    disableDeepLinking: true, // البقاء داخل المتصفح وعدم فتح التطبيق الخارجي
                    defaultLanguage: 'ar' // إجبار الواجهة على اللغة العربية
                },
                interfaceConfigOverwrite: {
                    SHOW_JITSI_WATERMARK: false,
                    SHOW_WATERMARK_FOR_GUESTS: false,
                    SHOW_BRAND_WATERMARK: false,
                    SHOW_PROMOTIONAL_CLOSE_PAGE: false,
                    // 🔥 تحديد صلاحيات الطالب (إخفاء أزرار التحكم الخاصة بالمعلم) 🔥
                    TOOLBAR_BUTTONS: [
                        'microphone', 'camera', 'desktop', 'fullscreen',
                        'hangup', 'chat', 'raisehand', 'videoquality', 'tileview'
                    ],
                    DEFAULT_BACKGROUND: '#1e293b', // لون خلفية مريح للعين
                }
            };

            // تشغيل الميتنج
            const api = new JitsiMeetExternalAPI(domain, options);

            // رسالة ترحيب عند نجاح الدخول
            api.addListener('videoConferenceJoined', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: 'success',
                    title: 'تم الاتصال بالغرفة بنجاح!'
                });
            });

            // توجيه الطالب لجدول الحصص فور ضغطه على زر المغادرة داخل الميتنج
            api.addListener('videoConferenceLeft', function() {
                window.location.href = "{{ route('student.dashboard') }}";
            });

            // زر المغادرة الخارجي مع رسالة تأكيد
            $('#leave-btn').on('click', function() {
                Swal.fire({
                    title: 'مغادرة الحصة؟',
                    text: "هل أنت متأكد من رغبتك في الخروج من البث المباشر؟",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، غادر',
                    cancelButtonText: 'بقاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        api.dispose(); // إغلاق iframe
                        window.location.href = "{{ route('student.dashboard') }}";
                    }
                });
            });
        });
    </script>
@endpush
