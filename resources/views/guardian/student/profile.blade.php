@extends('backend.layouts.master')
@section('title', 'ملف الطالب الشامل')
@section('title_page', 'السجل الأكاديمي والمالي')

@push('css')
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .student-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            border-radius: 16px;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .student-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* تصميم التابات (Tabs) */
        .nav-pills .nav-link {
            color: #64748b;
            font-weight: bold;
            border-radius: 10px;
            padding: 12px 20px;
            transition: 0.3s;
            border: 1px solid transparent;
        }

        .nav-pills .nav-link:hover {
            background-color: #f1f5f9;
        }

        .nav-pills .nav-link.active {
            background-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }

        .info-card {
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .eval-card {
            border-left: 4px solid #8b5cf6;
        }

        .progress-bar-custom {
            height: 10px;
            border-radius: 50px;
            background-color: #e2e8f0;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background-color: #10b981;
            border-radius: 50px;
        }

        .stat-box {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
    </style>
@endpush

@section('content')
    <div class="row pb-5">
        <div class="col-12">

            {{-- ================= الهيدر ================= --}}
            <div class="student-header shadow-sm mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    <img src="{{ $student->student_photo ? asset('storage/' . $student->student_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->first_name) . '&background=e0e7ff&color=3730a3' }}"
                        alt="Student" class="student-avatar">
                    <div>
                        <h3 class="fw-bolder mb-1">{{ $student->first_name }} {{ $student->second_name }}
                            {{ $student->tribe }}</h3>
                        <p class="mb-2 opacity-75"><i class="bx bx-store-alt"></i> {{ $student->branch->name_ar ?? '-' }} |
                            {{ $student->grade->name_ar ?? '-' }} - {{ $student->section->name_ar ?? 'جاري التسكين' }}</p>
                        <span class="badge bg-white text-dark rounded-pill px-3 py-2 fw-bold"><i class="bx bx-barcode"></i>
                            كود الطالب: {{ $student->id }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    {{-- زرار تقديم طلب استئذان --}}
                    <button type="button" class="btn btn-warning text-dark fw-bold rounded-pill shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#leaveRequestModal">
                        <i class="bx bx-plus-medical"></i> طلب استئذان
                    </button>
                    {{-- زرار طباعة الشهادة --}}
                    <a href="{{ route('parent.certificate.print', ['student_id' => $student->id, 'term_id' => 1]) }}"
                        target="_blank" class="btn btn-light text-dark fw-bold rounded-pill shadow-sm">
                        <i class="bx bx-printer text-primary"></i> طباعة الشهادة
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success fw-bold shadow-sm rounded-3 border-0">
                    <i class="bx bx-check-circle fs-5 align-middle me-1"></i> {{ session('success') }}
                </div>
            @endif

            {{-- ================= التابات (Tabs) ================= --}}
            <ul class="nav nav-pills mb-4 gap-2" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#academic" type="button"
                        role="tab"><i class="bx bx-line-chart"></i> التقييمات والدرجات</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#attendance" type="button"
                        role="tab"><i class="bx bx-calendar-x"></i> سجل الغياب</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#finance" type="button"
                        role="tab"><i class="bx bx-wallet"></i> الكشف المالي والأقساط</button>
                </li>
            </ul>

            {{-- ================= محتوى التابات ================= --}}
            <div class="tab-content" id="pills-tabContent">

                {{-- 🔥 1. تاب التقييمات والدرجات 🔥 --}}
                <div class="tab-pane fade show active" id="academic" role="tabpanel">

                    {{-- جدول الدرجات الذكي --}}
                    <div class="info-card p-4 mb-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="bx bx-bar-chart-alt-2 text-primary"></i> درجات
                            التقييمات المستمرة</h5>
                        @if (isset($marks) && $marks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle text-center mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="text-start ps-3">المادة</th>
                                            <th>تفاصيل التقويم (الواجبات/الاختبارات)</th>
                                            <th>المجموع الحاصل عليه</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($marks as $subject_id => $subjectMarks)
                                            @php
                                                $subjectName =
                                                    $subjectMarks->first()->subject->name_ar ?? 'مادة غير معروفة';
                                                $totalObtained = 0;
                                                $totalMax = 0;
                                            @endphp
                                            <tr>
                                                <td class="text-start ps-3 fw-bold text-dark font-15">{{ $subjectName }}
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                                        @foreach ($subjectMarks as $mark)
                                                            @php
                                                                $catMax = floatval($mark->category->max_mark ?? 0);
                                                                $catObtained = floatval($mark->mark_obtained ?? 0);
                                                                $totalMax += $catMax;
                                                                $totalObtained += $catObtained;
                                                                $badgeColor =
                                                                    $catObtained < $catMax / 2
                                                                        ? 'bg-danger bg-opacity-10 text-danger'
                                                                        : 'bg-success bg-opacity-10 text-success';
                                                            @endphp
                                                            <span
                                                                class="badge border border-light {{ $badgeColor }} p-2">
                                                                {{ $mark->category->name ?? 'تقييم' }}:
                                                                <strong>{{ $catObtained }}</strong> / {{ $catMax }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="font-16">
                                                    @php $finalColor = ($totalMax > 0 && ($totalObtained / $totalMax) < 0.5) ? 'text-danger' : 'text-success'; @endphp
                                                    <strong class="{{ $finalColor }}">{{ $totalObtained }}</strong>
                                                    <span class="text-muted font-12">/ {{ $totalMax }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">لم يتم رصد درجات حتى الآن.</p>
                            </div>
                        @endif
                    </div>

                    {{-- كروت ملاحظات المعلمين --}}
                    <h5 class="fw-bold text-dark mb-3"><i class="bx bx-message-rounded-dots text-primary"></i> تقارير
                        وملاحظات المعلمين</h5>
                    <div class="row g-3">
                        @forelse($evaluations ?? [] as $eval)
                            <div class="col-md-6">
                                <div class="card info-card eval-card h-100 p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-dark mb-0"><i class="bx bx-user-circle text-muted"></i> أ.
                                            {{ $eval->teacher->name_ar ?? 'إدارة المدرسة' }}</h6>
                                        <small
                                            class="text-muted font-monospace">{{ $eval->created_at->format('Y-m-d') }}</small>
                                    </div>
                                    <div class="mb-3">
                                        <span class="text-muted small fw-bold">التقييم العام:</span>
                                        @if ($eval->overall_level == 'ممتاز')
                                            <span class="badge bg-success fs-6 px-3 rounded-pill">ممتاز 🌟</span>
                                        @elseif($eval->overall_level == 'يحتاج دعم')
                                            <span class="badge bg-danger fs-6 px-3 rounded-pill">يحتاج دعم ⚠️</span>
                                        @else
                                            <span
                                                class="badge bg-primary fs-6 px-3 rounded-pill">{{ $eval->overall_level }}</span>
                                        @endif
                                    </div>
                                    @if ($eval->weak_subjects)
                                        <div class="mb-2">
                                            <span class="text-muted small fw-bold">نقاط تحتاج تحسين:</span>
                                            <p class="text-danger fw-bold mb-0">{{ $eval->weak_subjects }}</p>
                                        </div>
                                    @endif
                                    @if ($eval->teacher_notes)
                                        <div class="bg-light p-3 rounded-3 mt-2 border">
                                            <small class="text-muted fw-bold"><i class="bx bx-message-rounded-dots"></i>
                                                ملاحظات المعلم:</small>
                                            <p class="text-dark mb-0 mt-1 fst-italic">"{{ $eval->teacher_notes }}"</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 info-card">
                                <i class="bx bx-star text-muted opacity-25" style="font-size: 60px;"></i>
                                <h5 class="fw-bold mt-3 text-muted">لم يتم رصد أي تقييمات للطالب حتى الآن.</h5>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- 🔥 2. تاب سجل الغياب 🔥 --}}
                <div class="tab-pane fade" id="attendance" role="tabpanel">
                    <div class="info-card p-4 mb-4">
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="stat-box">
                                    <h3 class="text-success fw-bold mb-1">{{ $attendanceStats['present'] ?? 0 }}</h3>
                                    <span class="text-muted fw-bold small">أيام الحضور</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-box">
                                    <h3 class="text-danger fw-bold mb-1">{{ $attendanceStats['absent'] ?? 0 }}</h3>
                                    <span class="text-muted fw-bold small">أيام الغياب</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-box">
                                    <h3 class="text-warning fw-bold mb-1">{{ $attendanceStats['late'] ?? 0 }}</h3>
                                    <span class="text-muted fw-bold small">التأخيرات</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-box">
                                    <h3 class="text-info fw-bold mb-1">{{ $attendanceStats['excused'] ?? 0 }}</h3>
                                    <span class="text-muted fw-bold small">استئذان مسبق</span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الحالة</th>
                                        <th>طريقة الحضور</th>
                                        <th>ملاحظات المدرسة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attendances ?? [] as $att)
                                        <tr>
                                            <td class="fw-bold font-monospace text-dark">
                                                {{ \Carbon\Carbon::parse($att->attendance_date)->format('Y-m-d') }}
                                            </td>
                                            <td>
                                                @if ($att->status == 'absent')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2"><i
                                                            class="bx bx-x"></i> غياب</span>
                                                @elseif($att->status == 'late')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2"><i
                                                            class="bx bx-time"></i> تأخير</span>
                                                @elseif($att->status == 'excused')
                                                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2"><i
                                                            class="bx bx-envelope"></i> مستأذن</span>
                                                @else
                                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2"><i
                                                            class="bx bx-check"></i> حاضر</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($att->attendance_mode == 'online')
                                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2"><i
                                                            class="bx bx-laptop"></i> أونلاين</span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2"><i
                                                            class="bx bx-building"></i> حضوري</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">{{ $att->notes ?? 'لا يوجد' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-success fw-bold">
                                                <i class="bx bx-check-shield fs-1 d-block mb-2"></i> الطالب ملتزم بالحضور
                                                ولم يتم تسجيل أي بيانات بعد.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 🔥 3. تاب الكشف المالي 🔥 --}}
                <div class="tab-pane fade" id="finance" role="tabpanel">
                    @if ($contract)
                        @php
                            $paidPercentage =
                                $contract->grand_total > 0
                                    ? ($contract->paid_amount / $contract->grand_total) * 100
                                    : 0;
                        @endphp
                        {{-- ملخص العقد --}}
                        <div class="card info-card mb-4 p-4 border-primary border-opacity-25 bg-primary bg-opacity-10">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                <div>
                                    <h5 class="fw-bold text-primary mb-1">عقد العام الدراسي الحالي</h5>
                                    <small class="text-muted">رقم العقد: <span
                                            class="font-monospace fw-bold">{{ $contract->contract_number }}</span></small>
                                </div>
                                <div class="text-end">
                                    <h4 class="fw-bolder text-dark mb-0">{{ number_format($contract->grand_total, 3) }}
                                        OMR</h4>
                                    <small class="text-muted fw-bold">الإجمالي الشامل</small>
                                </div>
                            </div>
                            <div class="progress-bar-custom mb-2 shadow-sm">
                                <div class="progress-bar-fill" style="width: {{ $paidPercentage }}%;"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted small fw-bold mt-2">
                                <span>تم سداد: <strong
                                        class="text-success">{{ number_format($contract->paid_amount, 3) }}</strong>
                                    OMR</span>
                                <span>المتبقي للدفع: <strong
                                        class="text-danger">{{ number_format($contract->total_remaining, 3) }}</strong>
                                    OMR</span>
                            </div>
                        </div>

                        {{-- جدول الأقساط --}}
                        <h6 class="fw-bold text-dark mb-3"><i class="bx bx-list-ul text-primary"></i> تفاصيل الأقساط
                            والمستحقات</h6>
                        <div class="card info-card border-0">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 text-center">
                                        <thead class="bg-light text-muted">
                                            <tr>
                                                <th>تاريخ الاستحقاق</th>
                                                <th>قيمة القسط الأصلي</th>
                                                <th>المدفوع</th>
                                                <th>المتبقي من القسط</th>
                                                <th>الحالة</th>
                                                <th>إجراء</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($installments as $inst)
                                                <tr>
                                                    <td class="fw-bold text-dark font-monospace">
                                                        {{ \Carbon\Carbon::parse($inst->due_date)->format('Y-m-d') }}</td>
                                                    <td class="fw-bold text-primary">{{ number_format($inst->amount, 3) }}
                                                        OMR</td>
                                                    <td class="text-success fw-bold">
                                                        {{ number_format($inst->paid_amount, 3) }}</td>
                                                    <td class="text-danger fw-bold">
                                                        {{ number_format($inst->remaining_amount, 3) }}</td>
                                                    <td>
                                                        @if ($inst->status == 'paid')
                                                            <span class="badge bg-success px-3 py-1 rounded-pill"><i
                                                                    class="bx bx-check"></i> مسدد</span>
                                                        @elseif($inst->status == 'partial')
                                                            <span
                                                                class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i
                                                                    class="bx bx-adjust"></i> سداد جزئي</span>
                                                        @else
                                                            <span class="badge bg-danger px-3 py-1 rounded-pill"><i
                                                                    class="bx bx-time"></i> غير مسدد</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($inst->status != 'paid')
                                                            <a href="#"
                                                                class="btn btn-sm btn-dark fw-bold rounded-pill shadow-sm px-3">دفع
                                                                إلكتروني</a>
                                                        @else
                                                            <button
                                                                class="btn btn-sm btn-light border text-muted fw-bold rounded-pill px-3"
                                                                disabled><i class="bx bx-receipt"></i> الإيصال</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-12 text-center py-5 info-card">
                            <i class="bx bx-wallet text-muted opacity-25" style="font-size: 60px;"></i>
                            <h5 class="fw-bold mt-3 text-muted">لا توجد عقود مالية مفعلة أو أقساط مسجلة للطالب.</h5>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- 🔥 نافذة (Modal) تقديم العذر الطبي / الاستئذان 🔥 --}}
    <div class="modal fade" id="leaveRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content info-card border-0">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bx bx-envelope text-warning me-2"></i>تقديم طلب
                        استئذان / غياب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('parent.student.leave_request', $student->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold font-13">من تاريخ <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="from_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold font-13">إلى تاريخ <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="to_date" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold font-13">سبب الغياب / الاستئذان <span
                                        class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3"
                                    placeholder="مثال: وعكة صحية، ظروف عائلية طارئة، موعد مستشفى..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold font-13">مرفق اختياري (تقرير طبي أو إثبات)</label>
                                <input type="file" name="attachment" class="form-control"
                                    accept=".jpg,.jpeg,.png,.pdf">
                                <small class="text-muted mt-1 d-block">الصيغ المسموحة: JPG, PNG, PDF (الحد الأقصى: 2
                                    ميجابايت)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning text-dark rounded-pill fw-bold px-4 shadow-sm"><i
                                class="bx bx-send me-1"></i> إرسال الطلب للإدارة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
