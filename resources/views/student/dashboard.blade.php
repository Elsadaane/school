@extends('backend.layouts.master')
@section('title', 'الرئيسية | لوحة الطالب')

@push('css')
    <style>
        .widget-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            transition: 0.3s;
            height: 100%;
            background: #fff;
        }

        .widget-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        .schedule-item {
            border-right: 4px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .schedule-live {
            border-right-color: #dc3545;
            background: #fff5f5;
        }

        .schedule-virtual {
            border-right-color: #0d6efd;
            background: #eff6ff;
        }

        .schedule-physical {
            border-right-color: #10b981;
            background: #ecfdf5;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 24px;
        }

        .eval-badge {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
        }

        /* تأثير النبض לחصص اللايف */
        .animate-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }

            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            {{-- 🌟 كارت الترحيب مع أزرار الوصول السريع 🌟 --}}
            <div class="card widget-card bg-primary bg-opacity-10 border-primary border-opacity-25">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-lg">
                            <span class="avatar-title bg-primary text-white rounded-circle fs-2 shadow-sm">
                                {{ mb_substr($student->first_name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h4 class="fw-bold text-primary mb-1">مرحباً بك يا بطل، {{ $student->first_name }}
                                {{ $student->tribe }}! 🚀</h4>
                            <p class="text-dark fw-bold mb-0">
                                {{ $student->grade->name_ar ?? '' }} - {{ $student->section->name_ar ?? '' }}
                                <span class="text-muted ms-2">({{ $student->branch->name_ar ?? '' }})</span>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-white text-primary fw-bold shadow-sm border"><i
                                class="mdi mdi-file-document-edit-outline me-1"></i> واجباتي</a>
                        <a href="#" class="btn btn-sm btn-white text-success fw-bold shadow-sm border"><i
                                class="mdi mdi-chart-box-outline me-1"></i> شهاداتي</a>
                        <a href="{{ route('chat.index') }}"
                            class="btn btn-sm btn-white text-info fw-bold shadow-sm border"><i
                                class="mdi mdi-forum me-1"></i> رسائلي</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3 fw-bold">
            <i class="mdi mdi-alert-circle"></i> {{ $errors->first() }}
        </div>
    @endif

    {{-- 🔥 تنبيه الطوارئ 🔥 --}}
    @if (isset($isEmergencyOnline) && $isEmergencyOnline)
        <div
            class="alert alert-danger shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center border-0 border-start border-danger border-4">
            <i class="mdi mdi-weather-lightning-rain text-danger fs-1 me-3 animate-pulse"></i>
            <div>
                <h5 class="fw-bold text-danger mb-1">حالة طوارئ المناخ!</h5>
                <p class="mb-0 text-dark font-14">تم تحويل جميع الحصص اليوم إلى <strong>نظام التعلم عن بعد
                        (أونلاين)</strong>. يرجى الدخول للحصص عبر زر البث المباشر.</p>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- 🌟 1. جدول الحصص والبث المباشر 🌟 --}}
        <div class="col-xl-7 col-lg-12">

            {{-- 🔥 حصص البث المباشر (الإضافية/المراجعات) 🔥 --}}
            @if (isset($liveClasses) && $liveClasses->count() > 0)
                <div class="card widget-card mb-4 border border-danger border-opacity-25">
                    <div
                        class="card-header bg-danger bg-opacity-10 pt-3 pb-2 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-danger mb-0">
                            <i class="mdi mdi-video-wireless me-1"></i> بث مباشر اليوم
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach ($liveClasses as $liveClass)
                            @php
                                $startTime = \Carbon\Carbon::parse($liveClass->start_at);
                                $endTime = $startTime->copy()->addMinutes($liveClass->duration);
                                $isLiveNow = \Carbon\Carbon::now()->between($startTime, $endTime);
                            @endphp
                            <div class="schedule-item schedule-live">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="text-center border-end border-danger border-opacity-25 pe-3 text-danger">
                                        <i class="mdi mdi-youtube-tv fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">
                                            {{ $liveClass->topic }}
                                            <span
                                                class="badge bg-light text-dark font-11 ms-1 border">{{ $liveClass->subject->name_ar }}</span>
                                        </h6>
                                        <p class="mb-0 font-12 text-muted">
                                            <i class="mdi mdi-account-tie"></i> أ.
                                            {{ $liveClass->teacher->name_ar ?? 'غير محدد' }}
                                            <span class="ms-3 text-danger fw-bold"><i class="mdi mdi-clock-outline"></i>
                                                {{ $startTime->format('h:i A') }} ({{ $liveClass->duration }}
                                                دقيقة)</span>
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('student.live_classes.join', $liveClass->id) ?? '#' }}"
                                        class="btn btn-sm {{ $isLiveNow ? 'btn-danger animate-pulse' : 'btn-outline-danger' }} rounded-pill fw-bold px-3">
                                        <i class="mdi mdi-play-circle me-1"></i>
                                        {{ $isLiveNow ? 'انضم للغرفة الآن' : 'بانتظار البدء' }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 📆 الجدول المدرسي المعتاد 📆 --}}
            <div class="card widget-card">
                <div
                    class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="mdi mdi-calendar-clock text-primary me-1"></i> الجدول
                        المدرسي</h5>
                    <span
                        class="text-muted font-12">{{ \Carbon\Carbon::now()->locale('ar')->translatedFormat('l') }}</span>
                </div>
                <div class="card-body">
                    @forelse ($schedules as $schedule)
                        <div class="schedule-item {{ $schedule->is_virtual ? 'schedule-virtual' : 'schedule-physical' }}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-center border-end pe-3">
                                    <span class="d-block font-12 text-muted fw-bold">الحصة</span>
                                    <span class="d-block font-18 fw-bolder text-dark">{{ $schedule->period_number }}</span>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $schedule->subject->name_ar ?? 'غير محدد' }}</h6>
                                    <p class="mb-0 font-12 text-muted">
                                        <i class="mdi mdi-account-tie"></i> أ.
                                        {{ $schedule->teacher->name_ar ?? 'غير محدد' }}
                                        <span class="ms-2" dir="ltr"><i class="mdi mdi-clock-outline"></i>
                                            {{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') : '--:--' }}</span>
                                    </p>
                                </div>
                            </div>
                            <div>
                                @if ($schedule->is_virtual)
                                    <a href="{{ route('student.virtual_classes.join', $schedule->id) }}"
                                        class="btn btn-sm btn-primary rounded-pill fw-bold shadow-sm px-3">
                                        <i class="mdi mdi-play-circle me-1"></i> دخول الحصة
                                    </a>
                                @else
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill font-12">
                                        <i class="mdi mdi-map-marker-outline"></i> حضورية
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="mdi mdi-calendar-blank text-muted opacity-25" style="font-size: 60px;"></i>
                            <h6 class="mt-2 text-muted fw-bold">لا يوجد حصص مجدولة لك اليوم!</h6>
                            <p class="font-13 text-muted">استمتع بيومك وراجع دروسك السابقة.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-12">
            <div class="row g-4">
                {{-- 🌟 2. الموقف المالي (Light Theme Gradient) 🌟 --}}
                <div class="col-12">
                    <div class="card widget-card gradient-primary">
                        <div class="card-body p-4 relative overflow-hidden">
                            <i class="mdi mdi-wallet text-white opacity-10 position-absolute"
                                style="font-size: 100px; left: -10px; bottom: -20px; transform: rotate(-15deg);"></i>
                            <h6 class="text-white-50 fw-bold mb-3">الموقف المالي للعقد الحالي</h6>
                            @if ($activeContract)
                                <h3 class="fw-bolder text-white mb-1" dir="ltr">
                                    {{ number_format($activeContract->total_remaining, 3) }} OMR</h3>
                                <p class="font-13 text-white-50">إجمالي المتبقي من الرسوم</p>
                                <div class="mt-3">
                                    <span
                                        class="badge {{ $activeContract->financial_status == 'fully_paid' ? 'bg-white text-success' : 'bg-warning text-dark' }} px-3 py-2 rounded-pill shadow-sm">
                                        {{ $activeContract->financial_status == 'fully_paid' ? 'مسدد بالكامل ✔️' : 'يوجد أقساط متبقية ⏳' }}
                                    </span>
                                </div>
                            @else
                                <p class="mb-0 text-white-50">لا يوجد عقد مالي مفعل لك حالياً.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 🌟 3. ملخص الحضور والغياب 🌟 --}}
                <div class="col-12">
                    <div class="card widget-card">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <h6 class="fw-bold text-dark mb-0"><i class="mdi mdi-calendar-check text-success"></i> إحصائية
                                الحضور (الشهر الحالي)</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $present = $attendanceStats['present'] ?? 0;
                                $absent = $attendanceStats['absent'] ?? 0;
                                $total = $present + $absent;
                                $presentPerc = $total > 0 ? round(($present / $total) * 100) : 0;
                            @endphp

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-13 fw-bold text-dark">نسبة المواظبة</span>
                                <span class="font-13 fw-bold text-success">{{ $presentPerc }}%</span>
                            </div>
                            <div class="progress mb-4" style="height: 10px; border-radius: 10px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $presentPerc }}%" aria-valuenow="{{ $presentPerc }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                            <div class="row text-center">
                                <div class="col-6 border-end">
                                    <h4 class="fw-bold text-success mb-0">{{ $present }}</h4>
                                    <span class="font-12 text-muted fw-bold">أيام الحضور</span>
                                </div>
                                <div class="col-6">
                                    <h4 class="fw-bold text-danger mb-0">{{ $absent }}</h4>
                                    <span class="font-12 text-muted fw-bold">أيام الغياب</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🌟 4. آخر التقييمات 🌟 --}}
                <div class="col-12">
                    <div class="card widget-card">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <h6 class="fw-bold text-dark mb-0"><i class="mdi mdi-star-circle-outline text-warning"></i>
                                السلوك والتقييم الأكاديمي</h6>
                        </div>
                        <div class="card-body">
                            @forelse ($latestEvaluations as $eval)
                                <div
                                    class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3 last-child-noborder">
                                    <div>
                                        <span class="d-block font-13 fw-bold text-dark">
                                            <i class="mdi mdi-account-tie text-muted"></i>
                                            {{ $eval->teacher->name_ar ?? '---' }}
                                        </span>
                                        <span class="text-muted font-11"><i class="mdi mdi-clock-outline"></i>
                                            {{ $eval->created_at->diffForHumans() }}</span>
                                    </div>
                                    <span
                                        class="eval-badge shadow-sm 
                                        {{ $eval->overall_level == 'ممتاز'
                                            ? 'bg-success text-white'
                                            : ($eval->overall_level == 'يحتاج دعم'
                                                ? 'bg-danger text-white'
                                                : 'bg-info text-white') }}">
                                        {{ $eval->overall_level }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-3">
                                    <i class="mdi mdi-star-outline text-warning opacity-50" style="font-size: 40px;"></i>
                                    <p class="text-muted font-13 fw-bold mb-0 mt-2">لم يتم رصد تقييمات لك بعد.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
