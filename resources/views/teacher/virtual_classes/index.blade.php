@extends('backend.layouts.master')

@section('title', 'جدول حصص اليوم')

@push('css')
    <style>
        /* 🌟 تأثيرات بصرية احترافية للكروت 🌟 */
        .schedule-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-right: 5px solid #6c757d;
            display: flex;
            flex-direction: column;
        }

        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        /* ألوان تمييز نوع الحصة */
        .border-virtual {
            border-right-color: #0d6efd !important;
        }

        .border-physical {
            border-right-color: #198754 !important;
        }

        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .bg-soft-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .bg-soft-warning {
            background-color: rgba(255, 193, 7, 0.15);
            color: #d39e00;
        }

        .bg-soft-info {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        /* 🔥 أنيميشن حالة الطوارئ 🔥 */
        .emergency-alert {
            border-right: 5px solid #dc3545;
            background-color: #fff5f5;
        }

        .pulse-icon {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
@endpush

@section('content')
    {{-- 🌟 ترويسة الصفحة والتاريخ 🌟 --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold mb-1">
                    <i class="mdi mdi-calendar-clock text-primary me-1"></i> جدول حصص اليوم
                </h4>
                <p class="text-muted mb-0 font-13">مرحباً بك، يعرض هذا الجدول مهامك الأكاديمية المجدولة لهذا اليوم فقط.</p>
            </div>
            <div class="text-end bg-white px-4 py-2 rounded-pill shadow-sm border">
                <i class="mdi mdi-calendar-today text-primary me-1"></i>
                {{-- استخدام Carbon لعرض تاريخ اليوم باللغة العربية --}}
                <span
                    class="font-bold text-dark font-15">{{ \Carbon\Carbon::now()->locale('ar')->translatedFormat('l، j F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- 🔥 تنبيه الطوارئ (Emergency Online Day) 🔥 --}}
    @if (isset($isEmergencyOnline) && $isEmergencyOnline)
        <div class="alert emergency-alert shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center">
            <i class="mdi mdi-alert-decagram text-danger fs-1 me-3 pulse-icon"></i>
            <div>
                <h5 class="fw-bold text-danger mb-1">حالة طوارئ نشطة!</h5>
                <p class="mb-0 text-dark font-14">
                    بناءً على تعليمات الإدارة، تم تحويل جميع الحصص الحضورية لهذا اليوم إلى <strong class="text-primary">حصص
                        أونلاين (بث مباشر)</strong>. يرجى التكرم بالالتزام بجدول البث.
                </p>
            </div>
        </div>
    @endif

    {{-- 🌟 حالة: لا يوجد حصص 🌟 --}}
    @if ($schedules->isEmpty())
        <div class="card shadow-sm border-0 rounded-4 text-center py-5">
            <div class="card-body py-5">
                <i class="mdi mdi-coffee-outline text-muted opacity-50" style="font-size: 80px;"></i>
                <h4 class="mt-3 font-bold text-dark">يوم دراسي خفيف!</h4>
                <p class="text-muted font-15">ليس لديك أي حصص مسندة في جدول اليوم. استمتع بوقتك!</p>
            </div>
        </div>
    @else
        {{-- 🌟 شبكة الحصص (Grid) 🌟 --}}
        <div class="row">
            @foreach ($schedules as $schedule)
                <div class="col-md-6 col-xl-4 mb-4 d-flex">
                    <div
                        class="card shadow-sm border-0 rounded-4 w-100 schedule-card {{ $schedule->is_virtual ? 'border-virtual' : 'border-physical' }}">
                        <div class="card-body p-4 d-flex flex-column">

                            {{-- نوع الحصة ورقمها --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span
                                    class="badge {{ $schedule->is_virtual ? 'bg-primary' : 'bg-success' }} rounded-pill px-3 py-2 font-12 shadow-sm">
                                    <i
                                        class="mdi {{ $schedule->is_virtual ? 'mdi-video-wireless' : 'mdi-google-classroom' }} me-1"></i>
                                    {{ $schedule->is_virtual ? 'حصة أونلاين' : 'حصة حضورية' }}
                                </span>
                                <h5 class="text-dark font-bold mb-0 bg-light px-3 py-1 rounded-pill border">الحصة
                                    {{ $schedule->period_number }}</h5>
                            </div>

                            {{-- بيانات المادة والشعبة --}}
                            <h4 class="font-bold text-dark mb-1">{{ $schedule->subject->name_ar ?? 'مادة غير محددة' }}</h4>
                            <p class="text-muted font-14 mb-3">
                                <i class="mdi mdi-account-group text-info me-1"></i>
                                {{ $schedule->section->name_ar ?? 'شعبة غير محددة' }} - <span
                                    class="font-12">{{ $schedule->section->grade->name_ar ?? '' }}</span>
                            </p>

                            {{-- وقت الحصة --}}
                            <div class="d-flex align-items-center mb-4 bg-light p-2 rounded-3 border">
                                <i class="mdi mdi-clock-outline text-secondary font-24 me-2 ms-1"></i>
                                <div>
                                    <small class="text-muted d-block font-11 fw-bold">توقيت الحصة المجدول</small>
                                    <span class="text-dark font-14 font-bold" dir="ltr">
                                        {{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') : 'غير محدد' }}
                                        -
                                        {{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') : 'غير محدد' }}
                                    </span>
                                </div>
                            </div>

                            {{-- 🔥 الإجراءات والأزرار الأكاديمية (يتم دفعها لأسفل الكارت) 🔥 --}}
                            <div class="mt-auto">
                                {{-- زرار البث أو التوجه للفصل --}}
                                @if ($schedule->is_virtual)
                                    <a href="{{ route('teacher.virtual_classes.enterRoom', $schedule->id) }}"
                                        class="btn btn-primary w-100 rounded-pill font-bold shadow-sm py-2 mb-2"
                                        style="background-color: #0d6efd; border:none;">
                                        <i class="mdi mdi-play-circle me-1 font-16 align-middle"></i> بدء البث المباشر
                                    </a>
                                @else
                                    <button class="btn btn-light w-100 rounded-pill font-bold text-muted border py-2 mb-2"
                                        style="cursor: default;" disabled>
                                        <i class="mdi mdi-map-marker-outline me-1 font-16 align-middle"></i> التوجه للقاعة
                                        الدراسية
                                    </button>
                                @endif

                                {{-- أزرار الربط مع وحدات الغياب، التقييم، ورصد الدرجات --}}
                                <div class="d-flex justify-content-between gap-2 mt-2">
                                    <a href="{{ route('teacher.attendance.create', $schedule->id) }}"
                                        class="btn btn-soft-warning flex-grow-1 rounded-pill font-bold font-12 py-1 shadow-sm"
                                        title="تحضير الطلاب">
                                        <i class="mdi mdi-account-check-outline font-16 d-block"></i> الغياب
                                    </a>
                                    <a href="{{ route('teacher.evaluations.create', $schedule->id) }}"
                                        class="btn btn-soft-info flex-grow-1 rounded-pill font-bold font-12 py-1 shadow-sm"
                                        title="رصد التقييمات">
                                        <i class="mdi mdi-star-outline font-16 d-block"></i> التقييم
                                    </a>
                                    <a href="{{ route('teacher.marks.index', ['section_id' => $schedule->section_id, 'subject_id' => $schedule->subject_id]) }}"
                                        class="btn btn-soft-success flex-grow-1 rounded-pill font-bold font-12 py-1 shadow-sm"
                                        title="رصد الدرجات">
                                        <i class="mdi mdi-format-list-checks font-16 d-block"></i> الدرجات
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
