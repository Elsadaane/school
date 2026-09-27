@extends('backend.layouts.master')

@section('title', 'الملف الشخصي لولي الأمر')

@push('css')
    <style>
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

        .student-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .student-card:hover {
            transform: translateX(-5px);
            border-left-color: #0d6efd;
            background-color: #f8f9fa;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
        }

        .id-card-preview {
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            cursor: zoom-in;
            border: 2px dashed #ced4da;
            transition: all 0.3s;
        }

        .id-card-preview:hover {
            border-color: #0d6efd;
            opacity: 0.9;
        }
    </style>
@endpush

@section('content')
    {{-- 🌟 Page Header 🌟 --}}
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold mb-1"><i class="mdi mdi-card-account-details-outline text-primary me-1"></i>
                    ملف ولي الأمر</h4>
                <p class="text-muted mb-0 font-13">عرض التفاصيل الشاملة لولي الأمر والطلاب التابعين له.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                {{-- 🔥 حماية التعديل 🔥 --}}
                @can('edit_guardians')
                    <a href="{{ route('admin.guardians.index') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                        <i class="mdi mdi-pencil me-1"></i> تعديل البيانات
                    </a>
                @endcan
                <a href="{{ route('admin.guardians.index') }}"
                    class="btn btn-light border rounded-pill px-4 shadow-sm fw-bold">
                    <i class="mdi mdi-arrow-right me-1"></i> العودة للقائمة
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- ================= العمود الأول (بيانات ولي الأمر) ================= --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4 text-center border-bottom">
                    <div class="avatar-xl mx-auto mb-3">
                        <div class="avatar-title bg-soft-primary text-primary rounded-circle font-36 fw-bold shadow-sm"
                            style="width: 90px; height: 90px;">
                            {{ mb_substr($guardian->name_ar, 0, 1) }}
                        </div>
                    </div>
                    <h4 class="mb-1 fw-bold text-dark">{{ $guardian->name_ar }}</h4>
                    <p class="text-muted mb-2" dir="ltr">{{ $guardian->name_en }}</p>
                    <span
                        class="badge bg-soft-success px-3 py-2 font-13 rounded-pill shadow-sm border border-success border-opacity-25">
                        <i class="mdi mdi-account-child me-1"></i> مسؤول عن ({{ $guardian->students->count() ?? 0 }}) طلاب
                    </span>
                </div>

                <div class="card-body p-4 text-start">
                    <h6 class="text-uppercase text-primary fw-bold mb-3"><i class="mdi mdi-phone-in-talk me-1"></i> معلومات
                        التواصل</h6>
                    <p class="mb-2"><i class="mdi mdi-phone text-success font-18 me-2 align-middle"></i> <span
                            class="fw-bold" dir="ltr"><a href="tel:{{ $guardian->phone }}"
                                class="text-dark text-decoration-none">{{ $guardian->phone }}</a></span></p>
                    <p class="mb-4"><i class="mdi mdi-email-outline text-warning font-18 me-2 align-middle"></i> <span
                            class="fw-bold"><a href="mailto:{{ $guardian->email }}"
                                class="text-dark text-decoration-none">{{ $guardian->email }}</a></span></p>

                    <h6 class="text-uppercase text-primary fw-bold mb-3 border-top pt-3"><i
                            class="mdi mdi-card-account-details me-1"></i> البيانات الشخصية والعمل</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">رقم الهوية:</span>
                        <span class="fw-bold text-dark font-monospace">{{ $guardian->national_id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">تاريخ الانتهاء:</span>
                        <span class="fw-bold text-dark">{{ $guardian->national_id_expiry ?? 'غير مسجل' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">الجنسية:</span>
                        <span class="fw-bold text-dark">{{ $guardian->nationality_ar ?? 'غير مسجل' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">الوظيفة:</span>
                        <span class="fw-bold text-dark">{{ $guardian->job_title_ar ?? 'غير مسجل' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">جهة العمل:</span>
                        <span class="fw-bold text-dark">{{ $guardian->work_place_ar ?? 'غير مسجل' }}</span>
                    </div>

                    {{-- عرض صورة الهوية لو موجودة --}}
                    <h6 class="text-uppercase text-primary fw-bold mb-3 border-top pt-3"><i
                            class="mdi mdi-attachment me-1"></i> المرفقات الرسمية</h6>
                    @if ($guardian->id_card_image)
                        <div class="text-center">
                            {{-- 🔥 حماية رابط الصورة بـ rel 🔥 --}}
                            <a href="{{ asset('storage/' . $guardian->id_card_image) }}" target="_blank"
                                rel="noopener noreferrer" data-bs-toggle="tooltip" title="اضغط لعرض الصورة بحجمها الأصلي">
                                <img src="{{ asset('storage/' . $guardian->id_card_image) }}"
                                    class="img-fluid id-card-preview shadow-sm w-100" alt="بطاقة الهوية">
                            </a>
                            <small class="text-muted mt-2 d-block"><i class="mdi mdi-magnify-plus-outline"></i> اضغط
                                للتكبير</small>
                        </div>
                    @else
                        <div class="alert alert-light text-center border-dashed mb-0 text-muted">
                            <i class="mdi mdi-file-hidden font-24 d-block mb-1"></i> لا توجد مرفقات لبطاقة الهوية
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= العمود الثاني (الأبناء/الطلاب) ================= --}}
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div
                    class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="text-primary fw-bold mb-0"><i class="mdi mdi-human-male-boy me-1"></i> الأبناء المسجلين
                        بالمدرسة</h5>

                    {{-- 🔥 حماية زرار الإضافة بصلاحية الطلاب 🔥 --}}
                    @can('create_students')
                        <a href="{{ route('admin.students.create', ['guardian_id' => $guardian->id]) }}"
                            class="btn btn-sm btn-outline-primary rounded-pill shadow-sm fw-bold">
                            <i class="mdi mdi-plus"></i> إضافة طالب تابع
                        </a>
                    @endcan
                </div>
                <div class="card-body p-4">

                    @if ($guardian->students && $guardian->students->count() > 0)
                        <div class="row g-3">
                            @foreach ($guardian->students as $student)
                                <div class="col-md-6">
                                    <div
                                        class="card border border-light shadow-sm student-card rounded-3 h-100 p-3 bg-white">
                                        <div class="d-flex align-items-start">
                                            <div class="avatar-sm me-3 flex-shrink-0">
                                                <span
                                                    class="avatar-title bg-soft-info text-info rounded-circle font-16 fw-bold">
                                                    {{ mb_substr($student->first_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="m-0 fw-bold text-dark">{{ $student->first_name }}
                                                    {{ $student->second_name }}</h6>
                                                <small class="text-muted d-block mt-1 font-monospace"><i
                                                        class="mdi mdi-identifier text-primary"></i>
                                                    {{ $student->student_id ?? 'بدون رقم' }}</small>

                                                <div class="mt-2 d-flex gap-2 flex-wrap">
                                                    <span class="badge bg-light text-dark border"><i
                                                            class="mdi mdi-layers-outline text-warning"></i>
                                                        {{ $student->grade->name_ar ?? 'غير مسكن' }}</span>
                                                    <span class="badge bg-light text-dark border"><i
                                                            class="mdi mdi-google-classroom text-success"></i>
                                                        {{ $student->section->name_ar ?? '-' }}</span>
                                                </div>
                                            </div>

                                            {{-- 🔥 تصحيح الراوت وتأمينه بالصلاحية 🔥 --}}
                                            @can('show_students')
                                                <div class="ms-2 flex-shrink-0">
                                                    <a href="{{ route('admin.students.show', $student->id) }}"
                                                        class="btn btn-sm btn-light border rounded-circle shadow-sm"
                                                        data-bs-toggle="tooltip" title="عرض ملف الطالب">
                                                        <i class="mdi mdi-eye-outline text-info font-16"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div
                                class="avatar-lg mx-auto mb-3 bg-soft-warning rounded-circle d-flex justify-content-center align-items-center">
                                <i class="mdi mdi-emoticon-sad-outline font-36 text-warning"></i>
                            </div>
                            <h5 class="fw-bold text-dark">لا يوجد أبناء مسجلين</h5>
                            <p class="text-muted">ولي الأمر هذا ليس له أي طلاب مرتبطين به في النظام حالياً.</p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- ================= السجل المالي والعقود ================= --}}
            <div class="card shadow-sm border-0 rounded-4 mt-4 border-top border-warning border-4">
                <div
                    class="card-header bg-white border-bottom-0 pt-4 pb-3 d-flex justify-content-between align-items-center">
                    <h5 class="text-dark fw-bold mb-0">
                        <i class="mdi mdi-file-document-edit-outline text-warning me-1"></i> العقود المالية للأبناء
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-custom text-center align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-muted fw-bold">رقم العقد</th>
                                    <th class="text-muted fw-bold">الطالب</th>
                                    <th class="text-muted fw-bold">العام الدراسي</th>
                                    <th class="text-muted fw-bold">الإجمالي</th>
                                    <th class="text-muted fw-bold">المتبقي</th>
                                    <th class="text-muted fw-bold">حالة العقد</th>
                                    <th class="text-muted fw-bold">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // تجميع كل عقود أبناء ولي الأمر في كولكشن واحد وترتيبها بالأحدث
                                    $allContracts = $guardian->students->flatMap->contracts->sortByDesc('created_at');
                                @endphp

                                @forelse ($allContracts as $contract)
                                    <tr>
                                        <td class="fw-bold text-primary font-monospace">#{{ $contract->contract_number }}
                                        </td>
                                        <td class="fw-bold">{{ $contract->student->first_name }}</td>
                                        <td><span
                                                class="badge bg-light text-dark border">{{ $contract->academicYear->name ?? '-' }}</span>
                                        </td>
                                        <td class="text-success fw-bold">{{ number_format($contract->grand_total, 3) }}
                                            ر.ع</td>
                                        <td class="text-danger fw-bold">{{ number_format($contract->total_remaining, 3) }}
                                            ر.ع</td>
                                        <td>
                                            @if ($contract->contract_status == 'draft')
                                                <span class="badge bg-soft-warning text-warning px-2 py-1 rounded-pill"><i
                                                        class="mdi mdi-pencil-circle-outline"></i> مسودة</span>
                                            @elseif ($contract->contract_status == 'active')
                                                <span class="badge bg-soft-success text-success px-2 py-1 rounded-pill"><i
                                                        class="mdi mdi-check-circle-outline"></i> معتمد</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger px-2 py-1 rounded-pill"><i
                                                        class="mdi mdi-close-circle-outline"></i> ملغي</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                {{-- زر التفاصيل والتعديل --}}
                                                <a href="{{ route('admin.student-contracts.show', $contract->id) }}"
                                                    class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="tooltip"
                                                    title="عرض وإدارة تفاصيل العقد">
                                                    <i class="mdi mdi-file-document-edit-outline"></i> تفاصيل
                                                </a>

                                                {{-- زر الطباعة (يظهر فقط لو العقد معتمد) --}}
                                                @if ($contract->contract_status == 'active')
                                                    <a href="{{ route('admin.student-contracts.print', $contract->id) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-light border shadow-sm text-secondary"
                                                        data-bs-toggle="tooltip" title="طباعة العقد">
                                                        <i class="mdi mdi-printer"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted fw-bold">
                                            <i class="mdi mdi-file-hidden font-24 d-block mb-1 opacity-50"></i> لا توجد
                                            عقود مالية مسجلة حتى الآن.
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

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endpush
