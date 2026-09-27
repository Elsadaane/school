@extends('backend.layouts.master') {{-- تأكد من مسار اللي أوت المناسب لولي الأمر --}}
@section('title', 'بوابة ولي الأمر')
@section('title_page', 'لوحة التحكم الرئيسية')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            color: white;
            border-radius: 16px;
            border: none;
            overflow: hidden;
            position: relative;
        }

        .welcome-card::after {
            content: '\eb46';
            font-family: 'boxicons';
            position: absolute;
            left: -20px;
            bottom: -40px;
            font-size: 150px;
            opacity: 0.05;
            z-index: 1;
        }

        .stat-card {
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .icon-box {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 24px;
            font-weight: bold;
        }

        .child-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            text-align: center;
            padding: 20px;
            transition: 0.3s;
            position: relative;
        }

        .child-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.1);
        }

        .child-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e2e8f0;
            padding: 2px;
            margin-bottom: 15px;
        }

        .attendance-badge {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: bold;
        }

        .absent-counter {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #fee2e2;
            color: #ef4444;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #fecaca;
        }
    </style>
@endpush

@section('content')
    <div class="row g-4 pb-5">
        <div class="col-12">

            {{-- 🌟 كارت الترحيب 🌟 --}}
            <div class="card welcome-card shadow-sm mb-4">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div style="z-index: 2;">
                        <h3 class="fw-bolder mb-2">أهلاً بك، {{ $guardian->name_ar }} 👋</h3>
                        <p class="mb-0 opacity-75 fs-6">تابع مستوى أبنائك، سجل الحضور، وحالتك المالية من مكان واحد.</p>
                    </div>
                    <div style="z-index: 2;">
                        <div
                            class="bg-white bg-opacity-25 px-4 py-2 rounded-pill fw-bold border border-light border-opacity-25">
                            <i class="bx bx-calendar"></i>
                            {{ \Carbon\Carbon::now()->locale('ar')->translatedFormat('l، d F Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🌟 الملخص المالي 🌟 --}}
            <h5 class="fw-bold text-dark mb-3"><i class="bx bx-wallet text-primary"></i> الملخص المالي للأقساط</h5>
            <div class="row g-3 mb-5">
                <div class="col-md-4">
                    <div class="card stat-card h-100 bg-white">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="icon-box bg-danger bg-opacity-10 text-danger"><i class="bx bx-trending-up"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-bold d-block mb-1">المتبقي للدفع (المديونية)</small>
                                <h4 class="fw-bolder text-danger mb-0">{{ number_format($totalDue, 3) }} <span
                                        class="fs-6 text-muted">OMR</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card h-100 bg-white">
                        <div class="card-body p-4 d-flex align-items-center gap-3">
                            <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bx bx-check-shield"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-bold d-block mb-1">إجمالي المدفوعات السابقة</small>
                                <h4 class="fw-bolder text-success mb-0">{{ number_format($totalPaid, 3) }} <span
                                        class="fs-6 text-muted">OMR</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card h-100 bg-white border-warning">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted fw-bold d-block mb-1">القسط القادم</small>
                                @if ($nextInstallment)
                                    <h5 class="fw-bolder text-dark mb-1">
                                        {{ number_format($nextInstallment->remaining_amount, 3) }} OMR
                                    </h5>
                                    <small class="text-warning fw-bold d-block"><i class="bx bx-time"></i> يستحق في:
                                        <span
                                            dir="ltr">{{ \Carbon\Carbon::parse($nextInstallment->due_date)->format('Y-m-d') }}</span>
                                    </small>
                                @else
                                    <h6 class="text-success fw-bold mt-2"><i class="bx bx-check-circle"></i> لا توجد أقساط
                                        مستحقة قريباً</h6>
                                @endif
                            </div>
                            @if ($nextInstallment)
                                <a href="#" class="btn btn-warning text-white fw-bold rounded-circle p-2 shadow-sm"
                                    title="دفع إلكتروني (قريباً)"><i class="bx bx-credit-card fs-4"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🌟 ملفات الأبناء 🌟 --}}
            <h5 class="fw-bold text-dark mb-3"><i class="bx bx-group text-primary"></i> الأبناء المسجلين
                ({{ $children->count() }})</h5>
            <div class="row g-4 mb-5">
                @forelse($children as $child)
                    <div class="col-md-6 col-xl-4">
                        <div class="child-card shadow-sm">

                            {{-- إحصائية الغياب الإجمالية (Badge) --}}
                            <div class="absent-counter" title="إجمالي أيام الغياب هذا العام">
                                <i class="bx bx-calendar-x"></i> غياب: {{ $absencesCount[$child->id] ?? 0 }} أيام
                            </div>

                            <img src="{{ $child->student_photo ? asset('storage/' . $child->student_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($child->first_name) . '&background=e0e7ff&color=3730a3' }}"
                                alt="صورة الطالب" class="child-avatar shadow-sm">

                            <h5 class="fw-bold text-dark">{{ $child->first_name }} {{ $child->second_name }}</h5>
                            <p class="text-muted mb-3">{{ $child->grade->name_ar ?? '-' }} |
                                {{ $child->section->name_ar ?? 'جاري التسكين' }}</p>

                            {{-- حالة الحضور اليوم --}}
                            <div
                                class="mb-4 bg-light p-2 rounded-3 border d-flex justify-content-between align-items-center px-3">
                                <span class="fw-bold text-muted small">حالة اليوم:</span>
                                @php $att = $todayAttendance[$child->id] ?? null; @endphp
                                @if ($att && $att->status == 'present')
                                    <span class="attendance-badge bg-success bg-opacity-10 text-success"><i
                                            class="bx bx-check"></i> حاضر</span>
                                @elseif($att && $att->status == 'absent')
                                    <span class="attendance-badge bg-danger bg-opacity-10 text-danger"><i
                                            class="bx bx-x"></i> غائب</span>
                                @elseif($att && $att->status == 'late')
                                    <span class="attendance-badge bg-warning bg-opacity-10 text-warning"><i
                                            class="bx bx-time"></i> متأخر</span>
                                @elseif($att && $att->status == 'excused')
                                    <span class="attendance-badge bg-info bg-opacity-10 text-info"><i
                                            class="bx bx-envelope"></i> مستأذن</span>
                                @else
                                    <span class="attendance-badge bg-secondary bg-opacity-10 text-secondary"><i
                                            class="bx bx-minus"></i> لم يُسجل بعد</span>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('parent.student.profile', $child->id) }}"
                                    class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm"><i
                                        class="bx bx-user-pin"></i> الملف الأكاديمي</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 bg-white rounded-4 border shadow-sm">
                        <i class="bx bx-user-x text-muted opacity-25" style="font-size: 60px;"></i>
                        <p class="text-muted fw-bold mt-2 mb-0">لا يوجد أبناء مسجلين بحسابك حالياً.</p>
                    </div>
                @endforelse
            </div>

            {{-- 🌟 آخر التقييمات الأكاديمية 🌟 --}}
            <h5 class="fw-bold text-dark mb-3"><i class="bx bx-star text-warning"></i> أحدث التقييمات الأكاديمية للأبناء
            </h5>
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="text-start ps-4">الطالب</th>
                                    <th>المعلم</th>
                                    <th>التقييم العام</th>
                                    <th>نقاط تحتاج تحسين</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestEvaluations as $eval)
                                    <tr>
                                        <td class="text-start ps-4 fw-bold text-dark">
                                            {{ $eval->student->first_name }}
                                        </td>
                                        <td class="text-muted">أ. {{ $eval->teacher->name_ar ?? 'غير محدد' }}</td>
                                        <td>
                                            @if ($eval->overall_level == 'ممتاز')
                                                <span class="badge bg-success px-3 py-1 rounded-pill">🌟 ممتاز</span>
                                            @elseif($eval->overall_level == 'يحتاج دعم')
                                                <span class="badge bg-danger px-3 py-1 rounded-pill">⚠️ يحتاج دعم</span>
                                            @else
                                                <span
                                                    class="badge bg-primary px-3 py-1 rounded-pill">{{ $eval->overall_level }}</span>
                                            @endif
                                        </td>
                                        <td class="text-danger small fw-bold">{{ $eval->weak_subjects ?? '---' }}</td>
                                        <td class="text-muted small font-monospace" dir="ltr">
                                            {{ $eval->created_at->format('Y-m-d') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted fw-bold">
                                            <i class="bx bx-calendar-star fs-1 opacity-25 d-block mb-2"></i>
                                            لا توجد تقييمات مسجلة مؤخراً لأبنائك.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
