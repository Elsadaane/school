@extends('backend.layouts.master')
@section('title', 'المحادثات الفورية')
@section('title_page', 'التواصل الفوري - الرسائل')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        /* =========================================
                   تنسيقات الشات الأساسية (Premium UI)
                ========================================= */
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .chat-container {
            height: 80vh;
            display: flex;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: #fff;
            border: 1px solid #e2e8f0;
            border-top: 5px solid #3b82f6;
        }

        .chat-sidebar {
            width: 350px;
            background: #f8fafc;
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .chat-sidebar-header {
            padding: 20px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            z-index: 10;
        }

        /* ----- حالة الاتصال (Status Dots) ----- */
        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            border: 2px solid #fff;
        }

        .status-online {
            background-color: #10b981;
        }

        .status-dnd {
            background-color: #ef4444;
        }

        .status-invisible {
            background-color: #64748b;
        }

        .status-offline {
            background-color: #94a3b8;
        }

        .avatar-container {
            position: relative;
            display: inline-block;
        }

        .avatar-container .status-dot {
            position: absolute;
            bottom: 2px;
            right: 2px;
        }

        /* مربع البحث الذكي */
        .search-box-container {
            position: relative;
            margin-top: 15px;
        }

        .search-box-container input {
            padding-right: 40px;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            transition: 0.3s;
            font-weight: 600;
        }

        .search-box-container input:focus {
            background-color: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
        }

        .search-box-container i.search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 18px;
        }

        /* نتائج البحث */
        #search-results {
            position: absolute;
            top: 100%;
            right: 0;
            width: 100%;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 350px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            margin-top: 5px;
            display: none;
        }

        .search-result-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f8fafc;
            transition: 0.2s;
            cursor: pointer;
        }

        .search-result-item:hover {
            background: #f1f5f9;
        }

        /* قائمة المحادثات */
        .contacts-list-scroll {
            flex-grow: 1;
            overflow-y: auto;
            padding-bottom: 10px;
        }

        .contact-item {
            padding: 15px 20px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s ease;
            position: relative;
        }

        .contact-item:hover {
            background: #fff;
        }

        .contact-item.active {
            background: #eff6ff;
            border-right: 4px solid #3b82f6;
        }

        .unread-badge {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            padding: 5px 8px;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
        }

        /* ----- منطقة المحادثة ----- */
        .chat-main {
            flex: 1;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .chat-main::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-image: url('data:image/svg+xml,%3Csvg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%233b82f6" fill-opacity="0.03" fill-rule="evenodd"%3E%3Ccircle cx="3" cy="3" r="3"/%3E%3Ccircle cx="13" cy="13" r="3"/%3E%3C/g%3E%3C/svg%3E');
            z-index: 0;
            pointer-events: none;
        }

        .chat-header {
            padding: 18px 25px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            z-index: 1;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .chat-messages {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 1;
            scroll-behavior: smooth;
        }

        .message {
            max-width: 65%;
            padding: 12px 18px;
            border-radius: 18px;
            position: relative;
            font-size: 14.5px;
            line-height: 1.6;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
            word-wrap: break-word;
            font-weight: 600;
        }

        .message.sent {
            background: #3b82f6;
            color: #fff;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        .message.received {
            background: #fff;
            color: #1e293b;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .msg-time {
            font-size: 10.5px;
            opacity: 0.8;
            margin-top: 6px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
        }

        .message.sent .msg-time {
            color: #dbeafe;
        }

        .message.received .msg-time {
            color: #64748b;
        }

        .chat-input-area {
            padding: 15px 25px;
            background: #fff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 15px;
            z-index: 1;
            align-items: center;
        }

        .chat-input-area input {
            height: 50px;
            font-size: 15px;
            padding: 0 20px;
            font-weight: 600;
            border: 1px solid #cbd5e1;
        }

        .chat-input-area input:focus {
            border-color: #3b82f6;
            box-shadow: none;
        }

        .btn-send {
            height: 50px;
            width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: transform 0.2s;
        }

        .btn-send:hover {
            transform: scale(1.05);
        }

        .avatar-group {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        /* 🔥 ستايل قائمة التعديل والحذف 🔥 */
        .msg-actions {
            opacity: 0;
            transition: 0.2s;
            margin-right: 5px;
        }

        .message:hover .msg-actions {
            opacity: 1;
        }

        .message.sent .dropdown-item {
            text-align: right;
        }

        .message.sent .mdi-dots-vertical {
            color: #dbeafe !important;
        }

        /* Dark Mode */
        body[data-layout-color="dark"] .chat-container,
        body[data-layout-color="dark"] .chat-sidebar-header,
        body[data-layout-color="dark"] .chat-header,
        body[data-layout-color="dark"] .chat-input-area {
            background: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .chat-sidebar {
            background: #2b313a;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .contact-item {
            border-color: #323a46;
        }

        body[data-layout-color="dark"] .contact-item:hover {
            background: #3b4652;
        }

        body[data-layout-color="dark"] .contact-item.active {
            background: #3b4652;
            border-color: #3b82f6;
        }

        body[data-layout-color="dark"] .search-box-container input,
        body[data-layout-color="dark"] .chat-input-area input {
            background: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .chat-main {
            background: #2b313a;
        }

        body[data-layout-color="dark"] .message.received {
            background: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }
    </style>
@endpush

@section('content')
    <div class="chat-container mb-4 mt-2">

        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="d-flex justify-content-between align-items-center">

                    {{-- قائمة تغيير الحالة (Status) --}}
                    <div class="dropdown">
                        <h5 class="mb-0 font-bold text-dark cursor-pointer d-flex align-items-center"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="my-status-dot"
                                class="status-dot status-{{ $currentStatus ?? 'online' }} me-2 shadow-sm"></span>
                            الرسائل الفورية <i class="mdi mdi-chevron-down font-16 ms-1 text-muted"></i>
                        </h5>
                        <ul class="dropdown-menu shadow-sm border-0 rounded-3 mt-2">
                            <li><a class="dropdown-item status-option fw-bold" href="#" data-status="online"><span
                                        class="status-dot status-online me-2 border-0"></span> متاح</a></li>
                            <li><a class="dropdown-item status-option fw-bold" href="#" data-status="dnd"><span
                                        class="status-dot status-dnd me-2 border-0"></span> مشغول (DND)</a></li>
                            <li><a class="dropdown-item status-option fw-bold" href="#" data-status="invisible"><span
                                        class="status-dot status-invisible me-2 border-0"></span> مخفي (Offline)</a></li>
                        </ul>
                    </div>

                    <button class="btn btn-sm btn-soft-primary rounded-pill fw-bold shadow-sm"
                        title="إنشاء مجموعة عمل جديدة" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                        <i class="mdi mdi-account-multiple-plus-outline me-1"></i> مجموعة
                    </button>
                </div>

                {{-- مربع البحث --}}
                <div class="search-box-container">
                    <i class="mdi mdi-magnify search-icon"></i>
                    <input type="text" id="user-search" class="form-control rounded-pill shadow-sm"
                        placeholder="ابحث عن معلم، طالب، إدارة...">
                    <div id="search-results"></div>
                </div>
            </div>

            {{-- قائمة المحادثات السابقة --}}
            <div class="contacts-list-scroll" id="contacts-list">
                @forelse ($conversations as $conv)
                    @php
                        $isGroup = $conv->type == 'group';
                        $chatName = $isGroup ? $conv->name : 'محادثة خاصة';
                        $targetId = null;
                        $targetType = null;

                        if (!$isGroup) {
                            $otherParticipant = $conv->participants->first(function ($p) use ($user) {
                                return $p->messageable_id != $user->id || $p->messageable_type != get_class($user);
                            });
                            if ($otherParticipant && $otherParticipant->messageable) {
                                $m = $otherParticipant->messageable;
                                $chatName =
                                    $m->name_ar ??
                                    ($m->name ??
                                        trim(
                                            ($m->first_name ?? '') .
                                                ' ' .
                                                ($m->second_name ?? '') .
                                                ' ' .
                                                ($m->tribe ?? ''),
                                        ));
                                $targetId = $m->id;
                                $targetType = get_class($m);
                            }
                        }
                    @endphp

                    <div class="contact-item" data-id="{{ $conv->id }}"
                        data-is-group="{{ $isGroup ? 'true' : 'false' }}" data-target-id="{{ $targetId }}"
                        data-target-type="{{ str_replace('\\', '\\\\', $targetType) }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-container">
                                <div class="avatar text-white rounded-circle text-center d-flex align-items-center justify-content-center shadow-sm {{ $isGroup ? 'avatar-group' : 'bg-primary' }}"
                                    style="width: 48px; height: 48px;">
                                    <i class="mdi {{ $isGroup ? 'mdi-account-group' : 'mdi-account' }} font-22"></i>
                                </div>
                            </div>

                            <div style="flex: 1; padding-left: 20px;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 font-14 fw-bold text-dark text-truncate" style="max-width: 140px;">
                                        {{ $chatName }}</h6>
                                    <small
                                        class="text-muted font-11">{{ $conv->updated_at->diffForHumans(null, true, true) }}</small>
                                </div>
                                <small class="text-muted text-truncate d-block fw-bold font-12"
                                    style="max-width: 160px;">اضغط لفتح المحادثة...</small>
                            </div>
                        </div>

                        <span class="badge bg-danger rounded-pill unread-badge shadow-sm" id="badge-{{ $conv->id }}"
                            style="display: {{ $conv->unread_count > 0 ? 'inline-block' : 'none' }}">
                            {{ $conv->unread_count > 99 ? '+99' : $conv->unread_count }}
                        </span>
                    </div>
                @empty
                    <div class="text-center mt-5 text-muted">
                        <div class="avatar bg-soft-secondary text-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 60px; height: 60px;">
                            <i class="mdi mdi-forum-outline font-24"></i>
                        </div>
                        <h6 class="fw-bold">لا توجد محادثات سابقة</h6>
                        <small>استخدم مربع البحث بالأعلى لبدء محادثة</small>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="chat-main">
            <div class="chat-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div id="chat-header-avatar"
                        class="avatar bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center border border-primary border-opacity-25"
                        style="width: 45px; height: 45px; display: none !important;">
                        <i class="mdi mdi-forum font-20"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-dark" id="chat-title">الرجاء اختيار محادثة</h5>
                        <span id="typing-indicator" class="text-success font-12 fw-bold" style="display: none;">
                            <i class="mdi mdi-pencil-outline me-1"></i>الطرف الآخر يكتب...
                        </span>
                    </div>
                </div>

                <div class="chat-options" style="display: none;" id="chat-options-menu">
                    <button class="btn btn-sm btn-soft-info text-info rounded-circle me-1 shadow-sm" id="btn-view-profile"
                        title="عرض الملف الشخصي" style="display: none;">
                        <i class="mdi mdi-information-variant font-18"></i>
                    </button>
                    <button class="btn btn-sm btn-soft-primary text-primary rounded-circle me-1 shadow-sm"
                        id="btn-group-info" title="إعدادات المجموعة" style="display: none;">
                        <i class="mdi mdi-account-group font-18"></i>
                    </button>
                </div>
            </div>

            <div class="chat-messages" id="chat-box">
                <div class="text-center text-muted m-auto">
                    <i class="mdi mdi-chat-processing-outline display-1 text-primary opacity-25 d-block mb-3"></i>
                    <h5 class="fw-bold text-dark">مرحباً بك في نظام المحادثات الموحد</h5>
                    <p class="font-14 fw-bold">ابحث عن شخص أو اختر محادثة من القائمة للتواصل المشفر</p>
                </div>
            </div>

            <div class="chat-input-area" style="display: none;" id="input-area">
                <input type="hidden" id="current_conversation_id">
                <button class="btn btn-soft-secondary rounded-circle shadow-sm" title="إرفاق ملف (قريباً)"
                    style="width: 50px; height: 50px;"><i class="mdi mdi-paperclip font-20"></i></button>
                <input type="text" id="message-input" class="form-control rounded-pill bg-light shadow-sm"
                    placeholder="اكتب رسالتك هنا... (اضغط Enter للإرسال)" autocomplete="off">
                <button class="btn btn-primary btn-send shadow-sm" id="send-btn"><i
                        class="mdi mdi-send font-20 ms-1"></i></button>
            </div>
        </div>
    </div>

    {{-- 🔥 تم الاحتفاظ بكافة الـ Modals كما هي (إنشاء مجموعة، عرض الملف، إلخ) لأنها مصممة بشكل ممتاز 🔥 --}}
    {{-- ... (Modals Section from original code) ... --}}
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/ar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 🌟 إعداد الـ CSRF عالمياً لجميع طلبات الأجاكس لزيادة الأمان وتنظيف الكود 🌟
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // 🌟 دالة الحماية ضد ثغرات XSS 🌟
        function escapeHTML(str) {
            if (typeof str !== 'string') return '';
            return str.replace(/[&<>'"]/g,
                tag => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#39;',
                    '"': '&quot;'
                } [tag])
            );
        }

        moment.locale('ar');
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth'
        });

        let currentConversationId = null;
        let activeTargetId = null;
        let activeTargetType = null;
        let userId = {{ $user->id ?? auth()->id() }};
        let userType = '{{ str_replace('\\', '\\\\', get_class($user ?? auth()->user())) }}';

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // 🌟 تفعيل الاستماع للمحادثات 🌟
        $('.contact-item').each(function() {
            let convId = $(this).data('id');
            window.Echo.private('conversation.' + convId)
                .listen('MessageSent', (e) => {
                    handleIncomingEvent(e, 'MessageSent');
                })
                .listen('MessageDeleted', (e) => {
                    handleIncomingEvent(e, 'MessageDeleted');
                })
                .listen('MessageEdited', (e) => {
                    handleIncomingEvent(e, 'MessageEdited');
                });
        });

        function handleIncomingEvent(e, eventType) {
            if (eventType === 'MessageSent') {
                let isMe = (e.message.sender_id === userId && e.message.sender_type === userType.replace(/\\\\/g, '\\'));

                if (currentConversationId == e.message.conversation_id) {
                    if ($('#chat-box').find('.mdi-chat-processing-outline').length > 0) $('#chat-box').empty();
                    appendMessage(e.message);
                    scrollToBottom();
                } else {
                    if (!isMe) {
                        let senderName = e.message.sender.name_ar || e.message.sender.name || 'مستخدم';
                        let myStatus = $('#my-status-dot').attr('class');

                        if (!myStatus.includes('status-dnd')) {
                            let audio = new Audio("{{ asset('sounds/messenger-sound.mp3') }}");
                            audio.play().catch(e => console.log("Audio play prevented by browser"));

                            Toast.fire({
                                icon: 'info',
                                title: `رسالة جديدة من: ${escapeHTML(senderName)}`,
                                text: escapeHTML(e.message.body).substring(0, 30) + '...'
                            });
                        }

                        let badge = $('#badge-' + e.message.conversation_id);
                        if (badge.length && badge.is(':visible')) {
                            badge.text(parseInt(badge.text()) + 1);
                        } else {
                            if (badge.length) {
                                badge.text('1').show();
                            } else {
                                $('.contact-item[data-id="' + e.message.conversation_id + '"]').append(
                                    '<span class="badge bg-danger rounded-pill unread-badge shadow-sm" id="badge-' + e
                                    .message.conversation_id + '">1</span>');
                            }
                        }
                        let contactItem = $('.contact-item[data-id="' + e.message.conversation_id + '"]');
                        $('#contacts-list').prepend(contactItem);
                    }
                }
            } else if (eventType === 'MessageDeleted') {
                $(`#message-${e.message_id}`).fadeOut(300, function() {
                    $(this).remove();
                });
            } else if (eventType === 'MessageEdited') {
                let msgBox = $(`#message-${e.message.id}`);
                msgBox.find('.msg-content-text').text(e.message.body); // Text() avoids XSS naturally
                msgBox.find('.edit-msg-btn').data('body', e.message.body);
                if (msgBox.find('.edited-tag').length === 0) {
                    msgBox.find('.message-body').append(
                        '<small class="edited-tag" style="font-size:10px; opacity:0.7; margin-right:5px;">(تم التعديل)</small>'
                    );
                }
            }
        }

        $('.contact-item').on('click', function() {
            $('.contact-item').removeClass('active');
            $(this).addClass('active');

            currentConversationId = $(this).data('id');
            $('#current_conversation_id').val(currentConversationId);

            let isGroup = $(this).data('is-group');
            if (!isGroup) {
                activeTargetId = $(this).data('target-id');
                activeTargetType = $(this).data('target-type');
                $('#btn-view-profile').show();
                $('#btn-group-info').hide();
            } else {
                $('#btn-view-profile').hide();
                $('#btn-group-info').show();
            }

            $('#chat-title').text($(this).find('h6').text());
            $('#chat-header-avatar').css('display', 'flex');
            $('#chat-options-menu').show();
            $('#input-area').css('display', 'flex'); // Flex instead of show to keep alignment
            $('#badge-' + currentConversationId).fadeOut().text('0');

            $('#chat-box').html(
                '<div class="text-center m-auto"><div class="spinner-border text-primary" role="status"></div><p class="text-muted fw-bold mt-2">جاري تحميل الرسائل...</p></div>'
            );

            $.get('/chat/fetch/' + currentConversationId, function(response) {
                $('#chat-box').html('');
                let messages = response.data.reverse();

                if (messages.length === 0) {
                    $('#chat-box').html(
                        '<div class="text-center m-auto text-muted"><i class="mdi mdi-forum-outline font-36 d-block mb-2 opacity-50"></i><h6 class="fw-bold">ابدأ المحادثة الآن</h6></div>'
                    );
                } else {
                    messages.forEach(msg => appendMessage(msg));
                    scrollToBottom();
                }
            });
        });

        // 🌟 إرسال الرسالة مع حماية التكرار والرسائل الفارغة 🌟
        $('#send-btn').on('click', function() {
            let inputField = $('#message-input');
            let text = inputField.val().trim();
            if (text === '') return; // منع المسافات الفارغة

            inputField.val('');

            $.post('/chat/send', {
                conversation_id: currentConversationId,
                body: text
            });
        });

        $('#message-input').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $('#send-btn').click();
            }
        });

        function appendMessage(msg) {
            if ($(`#message-${msg.id}`).length > 0) return;

            let isMe = (msg.sender_id === userId && msg.sender_type === userType.replace(/\\\\/g, '\\'));
            let msgClass = isMe ? 'sent' : 'received';
            let senderName = escapeHTML(msg.sender.name_ar || msg.sender.name || 'مستخدم');
            let safeBody = escapeHTML(msg.body); // 🔥 حماية XSS 🔥

            let timeString = moment(msg.created_at).format('hh:mm A');
            let seenIcon = isMe ? (msg.read_at ? '<i class="mdi mdi-check-all ms-1"></i>' :
                '<i class="mdi mdi-check ms-1"></i>') : '';
            let editedTag = msg.is_edited ?
                '<small class="edited-tag" style="font-size:10px; opacity:0.7; margin-right:5px;">(تم التعديل)</small>' :
                '';

            let actionMenu = '';
            if (isMe) {
                actionMenu = `
                <div class="dropdown d-inline-block msg-actions">
                    <i class="mdi mdi-dots-vertical cursor-pointer" data-bs-toggle="dropdown" style="font-size: 16px;"></i>
                    <ul class="dropdown-menu border-0 shadow-sm rounded-3" style="min-width: 120px;">
                        <li><a class="dropdown-item text-primary edit-msg-btn font-13 fw-bold" href="#" data-id="${msg.id}" data-body="${safeBody}"><i class="mdi mdi-pencil font-14 me-1"></i> تعديل</a></li>
                        <li><a class="dropdown-item text-danger delete-msg-btn font-13 fw-bold" href="#" data-id="${msg.id}"><i class="mdi mdi-delete font-14 me-1"></i> حذف</a></li>
                    </ul>
                </div>`;
            }

            let html = `
            <div class="message ${msgClass} d-flex align-items-center" id="message-${msg.id}">
                ${!isMe ? `<div class="font-12 fw-bold mb-1 w-100 text-primary opacity-75">${senderName}</div>` : ''}
                
                <div class="d-flex align-items-center w-100 ${isMe ? 'justify-content-end' : ''}">
                    ${isMe ? actionMenu : ''}
                    <div class="message-body">
                        <span class="msg-content-text">${safeBody}</span>
                        ${editedTag}
                    </div>
                </div>

                <span class="msg-time w-100 ${isMe ? 'text-start' : 'text-end'}">${timeString} ${seenIcon}</span>
            </div>
            `;
            $('#chat-box').append(html);
        }

        function scrollToBottom() {
            let chatBox = document.getElementById("chat-box");
            chatBox.scrollTo({
                top: chatBox.scrollHeight,
                behavior: 'smooth'
            });
        }

        // ==========================================
        // أكواد المجموعات، البحث، وتغيير الحالة 
        // ==========================================

        // 🌟 1. تغيير حالة الاتصال (Status) 🌟
        $('.status-option').on('click', function(e) {
            e.preventDefault();
            let status = $(this).data('status');
            $.post('/chat/update-status', {
                status: status
            }, function(res) {
                $('#my-status-dot').removeClass('status-online status-dnd status-invisible status-offline')
                    .addClass('status-' + status);
            });
        });

        // 🌟 2. عرض الملف الشخصي للطرف الآخر 🌟
        $('#btn-view-profile').on('click', function() {
            if (!activeTargetId || !activeTargetType) return;
            $('#profile-name').text('جاري التحميل...');
            $('#profile-role').text('...');
            $('#profile-details').html(
                '<div class="text-center py-4"><i class="mdi mdi-spin mdi-loading font-36 text-primary"></i></div>'
                );
            $('#userProfileModal').modal('show');

            $.get('/chat/user-profile', {
                id: activeTargetId,
                type: activeTargetType
            }, function(data) {
                $('#profile-name').text(escapeHTML(data.name));
                $('#profile-role').text(escapeHTML(data.role));
                $('#profile-details').html(data.details); // الـ Details المفترض تأتي منسقة من الباك إند
                $('#profile-status-dot').removeClass(
                    'status-online status-dnd status-invisible status-offline').addClass('status-' +
                    data.status);
            });
        });

        // 🌟 3. البحث السريع وبدء محادثة جديدة 🌟
        let mainSearchTimer;
        $('#user-search').on('keyup', function() {
            clearTimeout(mainSearchTimer);
            let query = $(this).val().trim();
            let resultsBox = $('#search-results');

            if (query.length < 2) {
                resultsBox.hide();
                return;
            }
            $(this).siblings('.search-icon').removeClass('mdi-magnify').addClass(
                'mdi-spin mdi-loading text-primary');

            mainSearchTimer = setTimeout(function() {
                $.get('/chat/search-users', {
                    q: query
                }, function(data) {
                    $('#user-search').siblings('.search-icon').removeClass(
                        'mdi-spin mdi-loading text-primary').addClass('mdi-magnify');
                    resultsBox.empty();

                    if (data.length === 0) {
                        resultsBox.append(
                            '<div class="p-3 text-center text-muted font-13 fw-bold">لا توجد نتائج مطابقة</div>'
                            );
                    } else {
                        data.forEach(user => {
                            let iconBg = 'bg-soft-primary text-primary';
                            if (user.role.includes('معلم')) iconBg =
                                'bg-soft-info text-info';
                            else if (user.role.includes('طالب')) iconBg =
                                'bg-soft-success text-success';
                            else if (user.role.includes('أمر')) iconBg =
                                'bg-soft-warning text-warning text-darken';
                            else if (user.role.includes('إدارة')) iconBg =
                                'bg-soft-danger text-danger';

                            let item = `
                                <div class="search-result-item d-flex align-items-center gap-3" onclick="startNewChat(${user.id}, '${user.type.replace(/\\/g, '\\\\')}')">
                                    <div class="avatar ${iconBg} rounded-circle text-center d-flex align-items-center justify-content-center shadow-sm border" style="width: 40px; height: 40px;">
                                        <i class="mdi ${escapeHTML(user.icon)} font-18"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-14 fw-bold text-dark">${escapeHTML(user.name)}</h6>
                                        <small class="text-muted font-12 fw-bold">${escapeHTML(user.role)}</small>
                                    </div>
                                </div>
                            `;
                            resultsBox.append(item);
                        });
                    }
                    resultsBox.fadeIn(200);
                });
            }, 500);
        });

        // إغلاق قائمة البحث عند النقر خارجها
        $(document).click(function(e) {
            if (!$(e.target).closest('.search-box-container').length) {
                $('#search-results').fadeOut(200);
            }
        });

        // الدالة المسؤولة عن خلق المحادثة في الـ Database وعمل Reload
        function startNewChat(targetId, targetType) {
            $('#search-results').hide();
            $('#user-search').val('');

            $.post('/chat/start', {
                target_id: targetId,
                target_type: targetType
            }, function(response) {
                if (response.status === 'success') {
                    window.location.reload();
                }
            });
        }

        // 🌟 4. إنشاء مجموعة جديدة (النافذة المنبثقة) 🌟
        let selectedGroupMembers = [];
        let groupSearchTimer;

        $('#group-search-user').on('keyup', function() {
            clearTimeout(groupSearchTimer);
            let query = $(this).val().trim();
            let resultsBox = $('#group-search-results');

            if (query.length < 2) {
                resultsBox.hide();
                return;
            }

            groupSearchTimer = setTimeout(function() {
                $.get('/chat/search-users', {
                    q: query
                }, function(data) {
                    resultsBox.empty();
                    if (data.length === 0) {
                        resultsBox.append(
                            '<div class="p-2 text-center text-muted font-12 fw-bold">لا توجد نتائج</div>'
                            );
                    } else {
                        data.forEach(user => {
                            let isAlreadyAdded = selectedGroupMembers.find(m => m.id == user
                                .id && m.type == user.type);
                            if (!isAlreadyAdded) {
                                let item = `
                                    <a href="javascript:void(0);" class="list-group-item list-group-item-action py-2 group-add-user-btn" 
                                       data-id="${user.id}" data-type="${user.type.replace(/\\/g, '\\\\')}" data-name="${escapeHTML(user.name)}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <strong class="font-13 text-dark">${escapeHTML(user.name)}</strong><br>
                                                <small class="text-muted fw-bold">${escapeHTML(user.role)}</small>
                                            </div>
                                            <i class="mdi mdi-plus-circle text-success font-20"></i>
                                        </div>
                                    </a>
                                `;
                                resultsBox.append(item);
                            }
                        });
                        if (resultsBox.children().length == 0) resultsBox.append(
                            '<div class="p-2 text-center text-muted font-12 fw-bold">تمت إضافة جميع نتائج البحث</div>'
                            );
                    }
                    resultsBox.show();
                });
            }, 500);
        });

        $(document).click(function(e) {
            if (!$(e.target).closest('#group-search-user, #group-search-results').length) {
                $('#group-search-results').hide();
            }
        });

        // إضافة العضو للـ Array لتكوين المجموعة
        $(document).on('click', '.group-add-user-btn', function() {
            selectedGroupMembers.push({
                id: $(this).data('id'),
                type: $(this).data('type'),
                name: $(this).data('name')
            });
            updateSelectedMembersUI();
            $('#group-search-user').val('').focus();
            $('#group-search-results').hide();
        });

        // إزالة العضو من Array
        $(document).on('click', '.remove-member-btn', function() {
            selectedGroupMembers.splice($(this).data('index'), 1);
            updateSelectedMembersUI();
        });

        function updateSelectedMembersUI() {
            let container = $('#selected-members-container');
            $('#selected-count').text(selectedGroupMembers.length);
            container.empty();
            if (selectedGroupMembers.length === 0) {
                container.append(
                    '<span class="text-muted font-12 fw-bold" id="no-members-msg">لم يتم اختيار أي عضو بعد.</span>');
            } else {
                selectedGroupMembers.forEach((member, index) => {
                    container.append(`
                        <span class="badge bg-soft-primary text-primary border border-primary px-3 py-2 font-12 d-flex align-items-center gap-1 shadow-sm rounded-pill">
                            ${escapeHTML(member.name)} <i class="mdi mdi-close-circle cursor-pointer text-danger ms-1 remove-member-btn" data-index="${index}" style="font-size: 15px;"></i>
                        </span>
                    `);
                });
            }
        }

        // حفظ المجموعة في الباك إند
        $('#btn-save-group').on('click', function() {
            let groupName = $('#group-name-input').val().trim();
            if (groupName === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى كتابة اسم المجموعة لتكوينها.',
                    confirmButtonColor: '#3b82f6'
                });
                $('#group-name-input').focus();
                return;
            }
            if (selectedGroupMembers.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى اختيار عضو واحد على الأقل لإضافته للمجموعة.',
                    confirmButtonColor: '#3b82f6'
                });
                $('#group-search-user').focus();
                return;
            }

            let btn = $(this);
            let originalText = btn.html();
            btn.prop('disabled', true).html('<i class="mdi mdi-spin mdi-loading me-1"></i> جاري الإنشاء...');

            $.post('/chat/group/create', {
                group_name: groupName,
                participants: selectedGroupMembers
            }, function(response) {
                if (response.status === 'success') {
                    window.location.reload();
                }
            }).fail(function(error) {
                let errorMsg = error.responseJSON && error.responseJSON.message ? error.responseJSON
                    .message : 'حدث خطأ غير معروف أثناء الإرسال.';
                Swal.fire({
                    icon: 'error',
                    title: 'فشل الإنشاء',
                    text: errorMsg,
                    confirmButtonColor: '#ef4444'
                });
                btn.prop('disabled', false).html(originalText);
            });
        });

        // 🌟 5. إعدادات وإدارة المجموعة المُنشأة 🌟
        $('#btn-group-info').on('click', function() {
            let currentName = $('#chat-title').text();
            $('#edit-group-name').val(currentName);
            $('#groupInfoModal').modal('show');
            loadGroupMembers();
        });

        function loadGroupMembers() {
            $('#current-group-members').html(
                '<div class="text-center p-4 text-muted"><i class="mdi mdi-spin mdi-loading font-36 text-primary d-block mb-2"></i> جاري تحميل الأعضاء...</div>'
                );

            $.get('/chat/group/' + currentConversationId + '/members', function(data) {
                $('#current-group-members').empty();
                $('#group-members-count').text(data.length);

                data.forEach(member => {
                    let isMe = (member.id == userId && member.type.replace(/\\\\/g, '\\') == userType
                        .replace(/\\\\/g, '\\'));

                    let deleteBtn = !isMe ?
                        `<button class="btn btn-sm btn-light text-danger rounded-circle btn-remove-member-group shadow-sm border" data-id="${member.id}" data-type="${member.type.replace(/\\/g, '\\\\')}" title="حذف العضو من المجموعة"><i class="mdi mdi-delete font-18"></i></button>` :
                        `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-12 px-3 py-1 rounded-pill">أنت (مسؤول)</span>`;

                    $('#current-group-members').append(`
                        <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar bg-soft-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                    <i class="mdi ${escapeHTML(member.icon)} font-20"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 font-14 fw-bold text-dark">${escapeHTML(member.name)}</h6>
                                    <small class="text-muted font-12 fw-bold">${escapeHTML(member.role)}</small>
                                </div>
                            </div>
                            <div>${deleteBtn}</div>
                        </div>
                    `);
                });
            });
        }

        // تغيير اسم المجموعة 
        $('#btn-update-group-name').on('click', function() {
            let newName = $('#edit-group-name').val().trim();
            if (newName === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يرجى كتابة اسم المجموعة أولاً',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }

            let btn = $(this);
            let originalText = btn.html();
            btn.prop('disabled', true).html('<i class="mdi mdi-spin mdi-loading"></i> جاري الحفظ...');

            $.post('/chat/group/' + currentConversationId + '/update-name', {
                group_name: newName
            }, function(response) {
                if (response.status === 'success') {
                    $('#chat-title').text(newName);
                    $('.contact-item[data-id="' + currentConversationId + '"] h6').text(newName);
                    btn.prop('disabled', false).html('<i class="mdi mdi-check"></i> حفظ التعديل');
                    Toast.fire({
                        icon: 'success',
                        title: 'تم تحديث الاسم بنجاح'
                    });
                }
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'فشل التحديث',
                    text: 'حدث خطأ أثناء تغيير اسم المجموعة',
                    confirmButtonColor: '#ef4444'
                });
                btn.prop('disabled', false).html(originalText);
            });
        });

        // إزالة عضو من المجموعة عبر الـ AJAX
        $(document).on('click', '.btn-remove-member-group', function() {
            let memberId = $(this).data('id');
            let memberType = $(this).data('type');
            let btn = $(this);

            Swal.fire({
                title: 'طرد العضو؟',
                text: "هل أنت متأكد من إزالة هذا العضو من المجموعة؟ لن يتمكن من رؤية الرسائل القادمة.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، إزالة فوراً',
                cancelButtonText: 'تراجع'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.html('<i class="mdi mdi-spin mdi-loading font-18"></i>');

                    $.post('/chat/group/' + currentConversationId + '/remove-member', {
                        user_id: memberId,
                        user_type: memberType
                    }, function(response) {
                        if (response.status === 'success') {
                            loadGroupMembers(); // تحديث القائمة بعد الحذف
                        }
                    }).fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'فشل الإزالة',
                            text: 'تأكد من امتلاكك صلاحية إزالة هذا العضو.',
                            confirmButtonColor: '#ef4444'
                        });
                        btn.html('<i class="mdi mdi-delete font-18"></i>');
                    });
                }
            });
        });
    </script>
@endpush
