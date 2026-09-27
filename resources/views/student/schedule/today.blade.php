@extends('backend.layouts.master')

@section('title', 'جدول اليوم')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="page-title font-bold mb-1"><i class="mdi mdi-calendar-text text-primary me-1"></i> حصص اليوم</h4>
            <p class="text-muted font-14">تتبع جدولك الدراسي اليومي وانضم لحصصك الافتراضية بسهولة.</p>
        </div>
    </div>

    @if ($schedules->isEmpty())
        <div class="alert alert-info shadow-sm rounded-4 font-bold text-center py-4">
            <i class="mdi mdi-party-popper font-24 d-block mb-2"></i> لا يوجد لديك حصص مجدولة لهذا اليوم!
        </div>
    @else
        <div class="row">
            @foreach ($schedules as $schedule)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 rounded-4 h-100"
                        style="border-right: 5px solid {{ $schedule->is_virtual ? '#198754' : '#6c757d' }};">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-light text-dark border px-3 py-1 rounded-pill">الحصة
                                    {{ $schedule->period_number }}</span>
                                @if ($schedule->is_virtual)
                                    <span
                                        class="badge bg-soft-success text-success px-2 py-1 rounded-pill animate__animated animate__pulse animate__infinite">
                                        <i class="mdi mdi-video-wireless"></i> بث مباشر
                                    </span>
                                @endif
                            </div>

                            <h5 class="font-bold text-dark mb-1">{{ $schedule->subject->name_ar ?? 'مادة غير محددة' }}</h5>
                            <p class="text-muted font-13 mb-3"><i class="mdi mdi-account-tie text-primary me-1"></i> أ.
                                {{ $schedule->teacher->name_ar ?? 'غير محدد' }}</p>

                            <div class="bg-light p-2 rounded-3 border mb-4 text-center font-bold text-dark font-14"
                                dir="ltr">
                                <i class="mdi mdi-clock-outline text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                            </div>

                            @if ($schedule->is_virtual)
                                <a href="{{ route('student.virtual_room', $schedule->id) }}"
                                    class="btn btn-success w-100 rounded-pill font-bold shadow-sm">
                                    <i class="mdi mdi-login me-1"></i> انضمام للحصة
                                </a>
                            @else
                                <button class="btn btn-light w-100 rounded-pill text-muted border font-bold" disabled>
                                    <i class="mdi mdi-school me-1"></i> حصة بالمدرسة
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
