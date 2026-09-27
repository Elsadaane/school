@extends('backend.layouts.master')
@section('title', 'لوحة تحكم المعلم')
@section('title_page', 'مرحباً بك في بوابتك الأكاديمية')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <style>
        /* التبويبات */
        .nav-pills .nav-link.active {
            background-color: #673ab7;
            box-shadow: 0 4px 10px rgba(103, 58, 183, 0.3);
            color: white !important;
        }

        .nav-pills .nav-link {
            color: #673ab7;
            font-weight: bold;
            border: 1px solid #673ab7;
            margin-left: 10px;
            border-radius: 50px;
            padding: 10px 25px;
            transition: all 0.3s;
        }

        /* كارت الترحيب */
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 10%, transparent 40%);
            transform: rotate(20deg);
            pointer-events: none;
        }

        /* تنسيقات السلايدر والكروت */
        .swiper-container {
            width: 100%;
            padding-top: 20px;
            padding-bottom: 40px;
        }

        .class-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #eef2f5;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(103, 58, 183, 0.15);
            border-color: #673ab7;
        }

        .class-card.pending-task {
            border: 1px solid #ff4d4f;
        }

        .class-card.completed-task {
            border: 1px solid #10b981;
        }

        .class-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #0dcaf0, #673ab7);
        }

        .class-card.pending-task::before {
            background: linear-gradient(90deg, #ff8f00, #ff4d4f);
        }

        .class-card.completed-task::before {
            background: linear-gradient(90deg, #34d399, #10b981);
        }

        .period-badge {
            background-color: #f3e8ff;
            color: #673ab7;
            font-weight: 900;
            font-size: 14px;
            padding: 8px 15px;
            border-radius: 50px;
            display: inline-block;
        }

        .class-info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            color: #475569;
            font-weight: 600;
        }

        .class-info-item i {
            font-size: 20px;
            color: #94a3b8;
            background: #f1f5f9;
            padding: 8px;
            border-radius: 50%;
        }

        /* أزرار السلايدر */
        .swiper-button-next,
        .swiper-button-prev {
            background: #fff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            color: #673ab7;
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 18px;
            font-weight: bold;
        }

        .swiper-pagination-bullet-active {
            background: #673ab7;
        }

        @keyframes pulse-danger {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 77, 79, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 77, 79, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 77, 79, 0);
            }
        }

        .pulse-danger {
            animation: pulse-danger 2s infinite;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">

            {{-- 🌟 كارت الترحيب والإحصائيات 🌟 --}}
            <div class="card welcome-card mb-4 shadow">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="fw-bolder mb-2">مرحباً بك، أ. {{ auth('teacher')->user()->name_ar }} 👋</h3>
                            <p class="mb-3 opacity-75 fs-6">إليك نظرة شاملة على جدول حصصك ومهامك الأكاديمية.</p>

                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-white text-dark px-3 py-2 rounded-pill font-13 shadow-sm">
                                    <i class="bx bx-book-reader text-primary align-middle font-16 me-1"></i> إجمالي حصص
                                    اليوم: {{ $todayClasses->count() }}
                                </span>

                                @if (isset($pendingAttendanceClasses) && $pendingAttendanceClasses->count() > 0)
                                    <span class="badge bg-danger px-3 py-2 rounded-pill font-13 shadow-sm pulse-danger">
                                        <i class="bx bx-error-circle align-middle font-16 me-1"></i> مهام معلقة: غياب
                                        {{ $pendingAttendanceClasses->count() }} حصص
                                    </span>
                                @else
                                    <span class="badge bg-success px-3 py-2 rounded-pill font-13 shadow-sm">
                                        <i class="bx bx-check-double align-middle font-16 me-1"></i> جميع المهام مكتملة
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div
                                class="d-inline-block bg-white bg-opacity-25 px-4 py-3 rounded-4 fw-bold text-center border border-light border-opacity-25 shadow-sm">
                                <i class="bx bx-calendar fs-2 d-block mb-1"></i>
                                {{ \Carbon\Carbon::now()->locale('ar')->translatedFormat('l، d F Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔥 تنبيه الطوارئ (لو مفعل) 🔥 --}}
            @if (isset($emergencyToday) && $emergencyToday)
                <div
                    class="alert alert-danger bg-danger text-white border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center">
                    <i class="bx bx-broadcast fs-1 me-3 pulse-danger"></i>
                    <div>
                        <h5 class="text-white fw-bold mb-1">يوم طوارئ أونلاين!</h5>
                        <p class="mb-0 font-14">جميع الحصص المدرجة اليوم ستعقد عبر الفصول الافتراضية، يرجى الدخول من تبويب
                            "الفصول الافتراضية".</p>
                    </div>
                </div>
            @endif

            {{-- تبويبات التحكم (اليوم - غداً - الأسبوع) --}}
            <ul class="nav nav-pills mb-4 justify-content-center" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active shadow-sm" id="pills-today-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-today">
                        <i class="bx bx-calendar-star me-1"></i> حصص اليوم
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link shadow-sm" id="pills-tomorrow-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-tomorrow">
                        <i class="bx bx-calendar-edit me-1"></i> تحضير غداً
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link shadow-sm" id="pills-week-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-week">
                        <i class="bx bx-calendar-event me-1"></i> جدول الأسبوع
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">

                {{-- مصفوفة ذكية لطباعة سلايدر اليوم وغداً بكود واحد --}}
                @php
                    $sliderTabs = [
                        'today' => ['title' => 'جدول حصصك لليوم', 'data' => $todayClasses, 'active' => true],
                        'tomorrow' => ['title' => 'حصصك المجدولة لغدٍ', 'data' => $tomorrowClasses, 'active' => false],
                    ];
                @endphp

                @foreach ($sliderTabs as $tabId => $tabInfo)
                    <div class="tab-pane fade {{ $tabInfo['active'] ? 'show active' : '' }}" id="pills-{{ $tabId }}">

                        <div class="d-flex justify-content-between align-items-end mb-3 px-2">
                            <h5 class="fw-bold text-dark mb-0"><i
                                    class="bx bx-time-five text-primary fs-4 align-middle"></i> {{ $tabInfo['title'] }}</h5>
                            <span class="badge bg-primary rounded-pill px-3 fs-6">{{ $tabInfo['data']->count() }} حصص</span>
                        </div>

                        @if ($tabInfo['data']->count() > 0)
                            <div class="swiper-container mySwiper">
                                <div class="swiper-wrapper pb-2">
                                    @foreach ($tabInfo['data'] as $class)
                                        @php
                                            // التحقق هل الغياب معلق لهذه الحصة (اليوم فقط)
                                            $isAttendancePending =
                                                $tabId == 'today' &&
                                                isset($pendingAttendanceClasses) &&
                                                $pendingAttendanceClasses->contains('id', $class->id);
                                            $isAttendanceCompleted = $tabId == 'today' && !$isAttendancePending;
                                        @endphp
                                        <div class="swiper-slide">
                                            <div
                                                class="card class-card {{ $isAttendancePending ? 'pending-task' : '' }} {{ $isAttendanceCompleted ? 'completed-task' : '' }}">
                                                <div class="card-body p-4">
                                                    <div class="text-center mb-4">
                                                        <span class="period-badge shadow-sm">الحصة رقم
                                                            {{ $class->period_number }}</span>
                                                    </div>

                                                    <div class="class-info-item">
                                                        <i class="bx bx-book-bookmark text-info bg-info bg-opacity-10"></i>
                                                        <div>
                                                            <small class="d-block text-muted"
                                                                style="font-size: 11px;">المادة</small>
                                                            <span
                                                                class="fs-5 text-dark fw-bold">{{ $class->subject->name_ar ?? 'غير محدد' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="class-info-item">
                                                        <i class="bx bx-group text-success bg-success bg-opacity-10"></i>
                                                        <div>
                                                            <small class="d-block text-muted" style="font-size: 11px;">الصف
                                                                والشعبة</small>
                                                            <span
                                                                class="text-dark fw-bold">{{ $class->grade->name_ar ?? '-' }}
                                                                - {{ $class->section->name_ar ?? '-' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="class-info-item mb-0">
                                                        <i
                                                            class="bx bx-building-house text-warning bg-warning bg-opacity-10"></i>
                                                        <div>
                                                            <small class="d-block text-muted"
                                                                style="font-size: 11px;">الفرع</small>
                                                            <span
                                                                class="text-dark fw-bold">{{ $class->branch->name_ar ?? '-' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-light border-0 text-center py-3">
                                                    {{-- زرار الغياب الذكي --}}
                                                    @if ($isAttendanceCompleted)
                                                        <button class="btn btn-sm btn-success fw-bold rounded-pill px-4"
                                                            disabled>
                                                            <i class="bx bx-check-double"></i> تم التحضير
                                                        </button>
                                                    @else
                                                        <a href="{{ route('teacher.attendance.create', $class->id) }}"
                                                            class="btn btn-sm {{ $isAttendancePending ? 'btn-danger pulse-danger' : 'btn-outline-primary' }} fw-bold rounded-pill px-3">
                                                            <i class="bx bx-list-check"></i> تحضير الطلاب
                                                        </a>
                                                    @endif

                                                    {{-- زرار رصد الدرجات --}}
                                                    <a href="{{ route('teacher.evaluations.create', $class->id) }}"
                                                        class="btn btn-sm btn-outline-info fw-bold rounded-pill px-3 ms-1">
                                                        <i class="bx bx-bar-chart-alt-2"></i> تقييم
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        @else
                            <div class="card border-0 shadow-sm rounded-4 text-center py-5 mt-3">
                                <div class="card-body">
                                    <i class="bx bx-coffee text-muted opacity-25" style="font-size: 80px;"></i>
                                    <h4 class="fw-bold text-dark mt-3">يوم خفيف!</h4>
                                    <p class="text-muted fs-5 mb-0">ليس لديك أي حصص مسجلة في هذا اليوم.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

                {{-- 3. تبويب الأسبوع (جدول تقليدي مجمع) --}}
                <div class="tab-pane fade" id="pills-week">
                    <div class="card border-0 shadow rounded-4 mt-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="fw-bold text-dark mb-0"><i class="bx bx-calendar text-primary me-2"></i> الجدول
                                الأسبوعي العام</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-center align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="bg-primary text-white" style="width: 120px;">اليوم / الحصة</th>
                                            @for ($i = 1; $i <= 7; $i++)
                                                <th class="text-muted fw-bold">الحصة {{ $i }}</th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'] as $dayKey)
                                            <tr>
                                                <td class="fw-bold bg-light">{{ $daysAr[$dayKey] }}</td>
                                                @for ($p = 1; $p <= 7; $p++)
                                                    @php
                                                        $cell = isset($weeklySchedules[$dayKey])
                                                            ? $weeklySchedules[$dayKey]
                                                                ->where('period_number', $p)
                                                                ->first()
                                                            : null;
                                                    @endphp
                                                    <td>
                                                        @if ($cell)
                                                            <div
                                                                class="p-2 bg-success bg-opacity-10 rounded border border-success border-opacity-25">
                                                                <span
                                                                    class="text-success fw-bold d-block">{{ $cell->subject->name_ar }}</span>
                                                                <small
                                                                    class="text-muted fw-bold">{{ $cell->grade->name_ar }}
                                                                    - {{ $cell->section->name_ar }}</small>
                                                            </div>
                                                        @else
                                                            <span class="text-muted opacity-25">-</span>
                                                        @endif
                                                    </td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            function initSwipers() {
                var swiper = new Swiper(".mySwiper", {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    grabCursor: true,
                    observer: true,
                    observeParents: true,
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                        dynamicBullets: true
                    },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev"
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 25
                        },
                        1400: {
                            slidesPerView: 4,
                            spaceBetween: 30
                        }
                    },
                });
            }

            initSwipers();

            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function() {
                initSwipers();
            });
        });
    </script>
@endpush
