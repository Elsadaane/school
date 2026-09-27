@extends('backend.layouts.master')
@section('title', 'تفاصيل الصف: ' . $grade->name_ar)

@section('content')
    <div class="container-fluid">

        {{-- 🌟 رأس الصفحة (Header) 🌟 --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-primary text-white p-4 rounded-3">
                    {{-- 🔥 إضافة flex-wrap و gap-3 للتجاوب مع الموبايل 🔥 --}}
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md bg-white rounded-circle d-flex justify-content-center align-items-center shadow-sm flex-shrink-0"
                                style="width: 50px; height: 50px;">
                                <i class="mdi mdi-google-classroom text-primary fs-2"></i>
                            </div>
                            <div class="ms-3">
                                <h3 class="mb-1 fw-bold text-white">{{ $grade->name_ar }}</h3>
                                <p class="mb-0 opacity-75">{{ $grade->name_en ?? 'لا يوجد اسم إنجليزي' }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <span
                                class="badge bg-white text-primary px-3 py-2 fw-bold fs-6 mb-2 d-inline-block shadow-sm">إجمالي
                                الشعب:
                                {{ $grade->sections->count() }}</span>
                            <br>
                            <a href="{{ route('admin.classrooms.index') }}"
                                class="btn btn-outline-light btn-sm shadow-sm fw-bold">
                                <i class="mdi mdi-arrow-left"></i> عودة للقائمة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🌟 جدول توزيع الصف على الفروع 🌟 --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold"><i class="mdi mdi-table-eye text-primary me-1"></i> الشعب
                            الدراسية المفعلة لهذا الصف</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-centered table-nowrap mb-0 text-center align-middle">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>الفرع</th>
                                        <th>السنة الدراسية</th>
                                        <th>اسم الشعبة / الفصل</th>
                                        <th>السعة القصوى</th>
                                        {{-- 🔥 عمود الإجراءات 🔥 --}}
                                        @can('view_sections')
                                            <th>إجراء</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($grade->sections as $index => $section)
                                        <tr>
                                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                            <td>
                                                {{-- 🔥 استخدام كلاسات البوتستراب القياسية 🔥 --}}
                                                <span
                                                    class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 fs-6 px-3 py-1">
                                                    <i class="mdi mdi-domain me-1"></i>
                                                    {{ $section->branch->name_ar ?? 'غير محدد' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 fs-6 px-3 py-1">
                                                    <i class="mdi mdi-calendar me-1"></i>
                                                    {{ $section->academicYear->name ?? 'غير محدد' }}
                                                </span>
                                            </td>
                                            <td class="fw-bold fs-5 text-dark">{{ $section->name_ar }}</td>
                                            <td><span class="text-muted font-14">{{ $section->max_capacity }} طالب</span>
                                            </td>

                                            {{-- 🔥 زر الدخول لتفاصيل الشعبة 🔥 --}}
                                            @can('view_sections')
                                                <td>
                                                    <a href="{{ route('admin.sections.show', $section->id) }}"
                                                        class="btn btn-sm btn-soft-primary rounded-pill px-3 shadow-sm fw-bold">
                                                        <i class="mdi mdi-eye-outline me-1"></i> عرض الشعبة
                                                    </a>
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->can('view_sections') ? 6 : 5 }}"
                                                class="py-5 text-center">
                                                <i class="mdi mdi-inbox-remove-outline text-muted opacity-25"
                                                    style="font-size: 60px;"></i>
                                                <h5 class="text-muted mt-3 fw-bold">لا توجد شعب دراسية (فصول) مفعلة لهذا
                                                    الصف حتى الآن.</h5>
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

    </div>
@endsection
