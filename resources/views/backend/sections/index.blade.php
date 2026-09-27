@extends('backend.layouts.master')

@section('title', 'إدارة الشعب والفصول')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <style>
        /* 🌟 Premium Card Effects 🌟 */
        .section-card {
            border: 1px solid #f0f2f8;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            background: #ffffff;
            height: 100%;
        }

        .section-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            border-color: #d1d5db;
        }

        /* 🌟 Custom Avatar 🌟 */
        .avatar-title-custom {
            background: linear-gradient(135deg, #e0e8f9 0%, #f4f6fa 100%);
            color: #3b82f6;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto;
            box-shadow: inset 0 0 15px rgba(59, 130, 246, 0.1);
        }

        /* 🌟 Clean Badges 🌟 */
        .info-badge {
            background: #f8f9fa;
            border: 1px solid #e2e6ea;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
        }

        /* 🌟 Select2 Modern Tweaks 🌟 */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 45px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .select2-container--open {
            z-index: 9999999 !important;
        }

        /* 🌟 Filter Bar 🌟 */
        .filter-bar {
            background: #fff;
            padding: 18px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            margin-bottom: 25px;
            border: 1px solid #f0f2f8;
            border-top: 4px solid #3b82f6;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .section-card,
        body[data-layout-color="dark"] .filter-bar {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .info-badge {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #ced4da;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
            color: #fff;
        }

        body[data-layout-color="dark"] .text-dark {
            color: #f8f9fa !important;
        }

        body[data-layout-color="dark"] .avatar-title-custom {
            background: #3b4652;
            color: #60a5fa;
            box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.3);
        }

        body[data-layout-color="dark"] .select2-container--bootstrap-5 .select2-selection,
        body[data-layout-color="dark"] .select2-dropdown {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .modal-content {
            background-color: #323a46;
            border-color: #424e5a;
        }
    </style>
@endpush

@section('content')
    {{-- 🌟 Page Header 🌟 --}}
    <div class="row mb-3 mt-2">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold mb-1 text-dark"><i
                        class="mdi mdi-google-classroom text-primary me-1 fs-4 align-middle"></i> إدارة الشعب والفصول
                    الدراسية</h4>
                <p class="text-muted mb-0 font-14">التحكم المركزي في الفصول وتوزيع المعلمين والطاقة الاستيعابية.</p>
            </div>

            <div class="d-flex gap-2 align-items-center flex-wrap">
                <div class="btn-group shadow-sm rounded-pill bg-white border">
                    <button class="btn btn-light border-0 rounded-pill-start" type="button" data-bs-toggle="tooltip"
                        title="تحديث البيانات" onclick="location.reload();">
                        <i class="mdi mdi-refresh font-18 text-secondary"></i>
                    </button>
                    <button class="btn btn-light border-0" type="button" data-bs-toggle="tooltip" title="طباعة القائمة"
                        onclick="window.print();">
                        <i class="mdi mdi-printer font-18 text-info"></i>
                    </button>
                </div>

                {{-- 🔥 حماية زر إنشاء الشعب بصلاحية الإنشاء 🔥 --}}
                @can('create_sections')
                    <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal"
                        data-bs-target="#addSectionModal">
                        <i class="mdi mdi-plus-circle me-1"></i> فتح شعب جديدة
                    </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- 🌟 Smart Filter Bar 🌟 --}}
    <div class="filter-bar">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-merge shadow-sm rounded-3">
                    <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-primary"></i></span>
                    <input type="text" id="quickSearch" class="form-control border-start-0 ps-0 fw-bold"
                        placeholder="بحث سريع باسم الشعبة...">
                </div>
            </div>
            <div class="col-md-8">
                <select id="globalSearch" class="form-control select2-init shadow-sm" style="width: 100%;">
                    <option value="">تصفية متقدمة (اختر الفرع، أو السنة، أو الصف)...</option>
                    <optgroup label="الفروع">
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->name_ar }}">{{ $branch->name_ar }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="السنوات الدراسية">
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->name }}">{{ $year->name }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="الصفوف الدراسية">
                        @foreach ($grades as $grade)
                            <option value="{{ $grade->name_ar }}">{{ $grade->name_ar }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
        </div>
    </div>

    {{-- 🌟 Alerts 🌟 --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show auto-hide-alert shadow-sm rounded-4 fw-bold border-0"
            role="alert">
            <i class="mdi mdi-check-circle me-1 fs-5 align-middle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 fw-bold border-0" role="alert">
            <i class="mdi mdi-alert-circle me-1 fs-5 align-middle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 fw-bold border-0" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 🌟 Sections Grid 🌟 --}}
    <div class="row" id="sectionsCardsContainer">
        @forelse($sections as $section)
            <div class="col-xl-3 col-lg-4 col-md-6 section-card-wrapper mb-4">
                <div class="card section-card">
                    <div class="card-body p-4 d-flex flex-column h-100">
                        {{-- Card Body Header --}}
                        <div class="text-center mb-4">
                            <div class="avatar-title-custom mb-3">
                                <i class="mdi mdi-google-classroom"></i>
                            </div>
                            <h4 class="text-dark font-bold mb-1">{{ $section->name_ar }}</h4>
                            <span
                                class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill border border-primary border-opacity-25">{{ $section->grade->name_ar ?? 'غير محدد' }}</span>
                        </div>

                        {{-- Details Badges --}}
                        <div class="d-flex flex-column gap-2 section-data mb-4">
                            <div class="info-badge shadow-sm"><i class="mdi mdi-domain text-info font-18"></i> <span
                                    class="text-truncate">{{ $section->branch->name_ar ?? 'بدون فرع' }}</span></div>
                            <div class="info-badge shadow-sm"><i
                                    class="mdi mdi-calendar-clock text-warning text-dark font-18"></i> <span
                                    class="text-truncate">{{ $section->academicYear->name ?? 'غير محدد' }}</span></div>
                        </div>

                        {{-- Card Footer Stats --}}
                        <div
                            class="bg-light rounded-4 p-3 d-flex justify-content-around text-center mt-auto mb-4 border shadow-sm">
                            <div>
                                <p class="text-muted mb-1 font-12 fw-bold">الطلاب / السعة</p>
                                <h4 class="text-dark fw-bold mb-0 font-monospace">
                                    {{ $section->students->count() ?? 0 }} <span class="text-muted font-16">/
                                        {{ $section->max_capacity }}</span>
                                </h4>
                            </div>
                            <div class="border-end border-2 border-white"></div>
                            <div>
                                <p class="text-muted mb-1 font-12 fw-bold">المعلمين</p>
                                <h4 class="text-primary fw-bold mb-0 font-monospace">{{ $section->teachers->count() }}
                                </h4>
                            </div>
                        </div>

                        {{-- 🔥 أزرار الأكشن السريعة 🔥 --}}
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('admin.sections.attendance', $section->id) }}"
                                class="btn btn-sm btn-soft-warning rounded-circle p-2 shadow-sm border border-warning border-opacity-25"
                                data-bs-toggle="tooltip" title="سجل الحضور اليومي">
                                <i class="mdi mdi-calendar-check font-20 text-dark"></i>
                            </a>
                            <a href="{{ route('admin.sections.students', $section->id) }}"
                                class="btn btn-sm btn-soft-info rounded-circle p-2 shadow-sm border border-info border-opacity-25"
                                data-bs-toggle="tooltip" title="سجل وملفات الطلاب">
                                <i class="mdi mdi-account-group-outline font-20 text-dark"></i>
                            </a>

                            {{-- 🔥 حماية زر التعديل بصلاحية التعديل 🔥 --}}
                            @can('edit_sections')
                                <button type="button"
                                    class="btn btn-sm btn-soft-primary rounded-circle p-2 shadow-sm border border-primary border-opacity-25"
                                    data-bs-toggle="modal" data-bs-target="#editSectionModal{{ $section->id }}"
                                    data-bs-toggle="tooltip" title="تعديل البيانات">
                                    <i class="mdi mdi-square-edit-outline font-20"></i>
                                </button>
                            @endcan

                            {{-- 🔥 حماية زر الحذف بصلاحية الحذف 🔥 --}}
                            @can('delete_sections')
                                <form action="{{ route('admin.sections.destroy', $section->id) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                        class="btn btn-sm btn-soft-danger rounded-circle p-2 shadow-sm border border-danger border-opacity-25 btn-delete"
                                        data-bs-toggle="tooltip" title="حذف الشعبة">
                                        <i class="mdi mdi-trash-can-outline font-20"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔥 مودال تعديل الشعبة محمي بالصلاحية 🔥 --}}
            @can('edit_sections')
                <div class="modal fade" id="editSectionModal{{ $section->id }}" tabindex="-1" aria-hidden="true"
                    data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4 border-top border-primary border-4">
                            <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                                <h5 class="modal-title font-bold text-primary"><i
                                        class="mdi mdi-pencil-circle-outline me-1"></i> تعديل الشعبة</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.sections.update', $section->id) }}" method="POST"
                                class="prevent-double-submit">
                                @csrf @method('PUT')
                                <div class="modal-body p-4 text-start">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted font-13 fw-bold">الفرع <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select bg-light shadow-sm fw-bold border-primary"
                                                name="branch_id" required>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}"
                                                        {{ $section->branch_id == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name_ar }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted font-13 fw-bold">السنة الدراسية <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select bg-light shadow-sm fw-bold border-primary"
                                                name="academic_year_id" required>
                                                @foreach ($academicYears as $year)
                                                    <option value="{{ $year->id }}"
                                                        {{ $section->academic_year_id == $year->id ? 'selected' : '' }}>
                                                        {{ $year->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label text-muted font-13 fw-bold">الصف الدراسي <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select bg-light shadow-sm fw-bold border-primary"
                                                name="grade_id" required>
                                                @foreach ($grades as $grade)
                                                    <option value="{{ $grade->id }}"
                                                        {{ $section->grade_id == $grade->id ? 'selected' : '' }}>
                                                        {{ $grade->name_ar }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label text-muted font-13 fw-bold">اسم الشعبة <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control shadow-sm fw-bold border-primary"
                                                name="name_ar" value="{{ $section->name_ar }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label text-muted font-13 fw-bold">السعة <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control shadow-sm fw-bold font-monospace border-primary text-center"
                                                name="max_capacity" value="{{ $section->max_capacity }}" min="1"
                                                required>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label text-muted font-13 fw-bold">تعيين المعلمين (اختياري)</label>
                                        <select class="form-select select2-modal shadow-sm" name="teachers[]"
                                            multiple="multiple" style="width: 100%;">
                                            @foreach ($teachers as $teacher)
                                                <option value="{{ $teacher->id }}"
                                                    {{ $section->teachers->contains($teacher->id) ? 'selected' : '' }}>
                                                    {{ $teacher->name_ar }} ({{ $teacher->specialization ?? 'عام' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0 bg-light rounded-bottom-4 justify-content-between">
                                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold"
                                        data-bs-dismiss="modal">إلغاء</button>
                                    <button type="submit"
                                        class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold btn-submit"><i
                                            class="mdi mdi-check-decagram-outline me-1"></i> حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan

        @empty
            <div class="col-12 text-center py-5 mt-4">
                <div class="avatar-xl mx-auto mb-4 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle"
                    style="width: 110px; height: 110px;">
                    <i class="mdi mdi-door-closed text-primary" style="font-size: 55px;"></i>
                </div>
                <h4 class="text-dark font-bold mb-2">لا توجد شعب دراسية حالياً</h4>
                <p class="text-muted mb-4 font-14">ابدأ بفتح أول شعبة وربطها بالفرع والصف المناسب لتتمكن من تسجيل الطلاب
                    والمعلمين.</p>

                @can('create_sections')
                    <button class="btn btn-primary rounded-pill px-5 py-2 shadow-sm fw-bold" data-bs-toggle="modal"
                        data-bs-target="#addSectionModal">
                        <i class="mdi mdi-plus-circle-outline me-1 fs-5 align-middle"></i> إضافة شعبة جديدة
                    </button>
                @endcan
            </div>
        @endforelse
    </div>

    {{-- 🔥 مودال إضافة شعبة محمية بالصلاحية 🔥 --}}
    @can('create_sections')
        <div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 border-top border-primary border-4">
                    <div class="modal-header bg-light border-bottom-0 rounded-top-4 pt-4 px-4">
                        <h5 class="modal-title font-bold text-primary"><i
                                class="mdi mdi-plus-circle-outline me-1 fs-4 align-middle"></i> فتح شعب دراسية جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.sections.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 bg-white">

                            {{-- Master Selectors --}}
                            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
                                <div class="card-body">
                                    <h6 class="font-bold text-primary mb-3 border-bottom pb-2"><i
                                            class="mdi mdi-cogs me-1"></i> 1. الإعدادات الأساسية للشعب</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4 text-start">
                                            <label class="form-label text-muted font-13 fw-bold">الفرع <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select border-primary shadow-sm fw-bold" name="branch_id"
                                                id="branch_id_add" required>
                                                <option value="" selected disabled>-- أولاً: اختر الفرع --</option>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 text-start">
                                            <label class="form-label text-muted font-13 fw-bold">السنة الدراسية <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select shadow-sm fw-bold" name="academic_year_id"
                                                id="academic_year_id_add" required disabled>
                                                <option value="" selected disabled>اختر الفرع أولاً</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 text-start">
                                            <label class="form-label text-muted font-13 fw-bold">الصف الدراسي <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select shadow-sm fw-bold border-primary" name="grade_id"
                                                id="grade_id_add" required>
                                                <option value="" selected disabled>-- اختر الصف --</option>
                                                @foreach ($grades as $grade)
                                                    <option value="{{ $grade->id }}">{{ $grade->name_ar }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Dynamic Rows Container --}}
                            <div class="card border-0 shadow-sm rounded-4 mb-2 bg-light">
                                <div class="card-body pb-2">
                                    <h6 class="font-bold text-primary mb-3 border-bottom pb-2"><i
                                            class="mdi mdi-table-row-plus-before me-1"></i> 2. بيانات الشعب المراد فتحها</h6>
                                    <div id="dynamic-sections-container">
                                        <div
                                            class="row section-row align-items-center mb-3 p-3 bg-white border border-primary border-opacity-25 rounded-3 position-relative shadow-sm">
                                            <div class="col-md-3 text-start">
                                                <label class="form-label text-muted font-12 fw-bold">اسم الشعبة <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control bg-light border-0 fw-bold shadow-sm"
                                                    name="sections[0][name_ar]" placeholder="مثال: شعبة أ" required>
                                            </div>
                                            <div class="col-md-2 text-start">
                                                <label class="form-label text-muted font-12 fw-bold">السعة <span
                                                        class="text-danger">*</span></label>
                                                <input type="number"
                                                    class="form-control bg-light border-0 fw-bold font-monospace text-center shadow-sm"
                                                    name="sections[0][max_capacity]" value="25" min="1" required>
                                            </div>
                                            <div class="col-md-6 text-start">
                                                <label class="form-label text-muted font-12 fw-bold">إسناد معلمين
                                                    (اختياري)</label>
                                                <select class="form-select select2-modal shadow-sm"
                                                    name="sections[0][teachers][]" multiple="multiple" style="width: 100%;">
                                                    @foreach ($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}">{{ $teacher->name_ar }}
                                                            ({{ $teacher->specialization ?? 'عام' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-1 text-center mt-4">
                                                <button type="button"
                                                    class="btn btn-light text-muted border-0 p-2 rounded-circle shadow-sm"
                                                    disabled><i class="mdi mdi-trash-can font-18"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-start mt-3 mb-2">
                                        <button type="button" id="add-row-btn"
                                            class="btn btn-soft-primary rounded-pill px-4 fw-bold shadow-sm">
                                            <i class="mdi mdi-plus-circle-outline me-1 fs-5 align-middle"></i> إضافة شعبة أخرى
                                            لنفس الصف
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4 justify-content-between">
                            <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold btn-submit"><i
                                    class="mdi mdi-check-decagram-outline me-1 fs-5 align-middle"></i> حفظ واعتماد
                                الشعب</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            // 1. Tooltips & Alerts
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            setTimeout(function() {
                $('.auto-hide-alert').fadeOut('slow');
            }, 4000);

            // 2. Select2 Initialization with Bootstrap 5 Theme
            $('.select2-init').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? "rtl" : "ltr",
                allowClear: true
            });

            // تهيئة Select2 داخل الـ Modals عند فتحها (لحل مشاكل الـ Focus)
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.select2-modal').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? "rtl" : "ltr",
                    placeholder: "ابحث واختر المعلمين...",
                    allowClear: true,
                    dropdownParent: $(this)
                });
            });

            // تهيئة Select2 للقوائم الرئيسية في الإضافة
            $('#branch_id_add, #academic_year_id_add, #grade_id_add').select2({
                theme: 'bootstrap-5',
                dir: isRtl ? "rtl" : "ltr",
                dropdownParent: $('#addSectionModal')
            });

            // 3. Smart Search Logic
            $("#quickSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#sectionsCardsContainer .section-card-wrapper").filter(function() {
                    $(this).toggle($(this).find('h4').text().toLowerCase().indexOf(value) > -1)
                });
            });

            $('#globalSearch').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue) {
                    var searchWord = selectedValue.toLowerCase();
                    $("#sectionsCardsContainer .section-card-wrapper").filter(function() {
                        var cardText = $(this).find('.section-data').text();
                        $(this).toggle(cardText.toLowerCase().indexOf(searchWord) > -1);
                    });
                } else {
                    $("#sectionsCardsContainer .section-card-wrapper").show();
                }
            });

            // 4. AJAX Dependency Logic (Branch -> Year) 🔥
            $('#branch_id_add').on('change', function() {
                let branchId = $(this).val();
                let yearSelect = $('#academic_year_id_add');

                yearSelect.empty().append('<option value="" selected disabled>⏳ جاري التحميل...</option>')
                    .prop('disabled', true).trigger('change');

                let localeUrl = "{{ url(app()->getLocale()) }}";

                if (branchId) {
                    $.get(`${localeUrl}/admin/get-branch-academic-years/${branchId}`, function(data) {
                        let options =
                            '<option value="" selected disabled>-- اختر السنة الدراسية --</option>';
                        $.each(data, function(key, value) {
                            options += `<option value="${value.id}">${value.name}</option>`;
                        });
                        yearSelect.empty().html(options).prop('disabled', false).removeClass(
                            'border-danger').addClass('border-primary').trigger('change');
                    }).fail(function() {
                        yearSelect.empty().append(
                            '<option value="" selected disabled>حدث خطأ في الاتصال</option>');
                    });
                }
            });

            // 5. Dynamic Rows Addition (إضافة شعب متعددة)
            let rowIndex = 1;
            $('#add-row-btn').click(function() {
                let teachersOptions =
                    `@foreach ($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name_ar }} ({{ $teacher->specialization ?? 'عام' }})</option>@endforeach`;

                let newRow = `
                <div class="row section-row align-items-center mb-3 p-3 bg-white border border-primary border-opacity-25 rounded-3 position-relative mt-2 shadow-sm" style="display:none;">
                    <div class="col-md-3 text-start">
                        <label class="form-label text-muted font-12 fw-bold">اسم الشعبة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light border-0 fw-bold shadow-sm" name="sections[${rowIndex}][name_ar]" placeholder="اسم الشعبة" required>
                    </div>
                    <div class="col-md-2 text-start">
                        <label class="form-label text-muted font-12 fw-bold">السعة <span class="text-danger">*</span></label>
                        <input type="number" class="form-control bg-light border-0 fw-bold font-monospace text-center shadow-sm" name="sections[${rowIndex}][max_capacity]" value="25" min="1" required>
                    </div>
                    <div class="col-md-6 text-start">
                        <label class="form-label text-muted font-12 fw-bold">إسناد معلمين (اختياري)</label>
                        <select class="form-select select2-new shadow-sm" name="sections[${rowIndex}][teachers][]" multiple="multiple" style="width: 100%;">
                            ${teachersOptions}
                        </select>
                    </div>
                    <div class="col-md-1 text-center mt-4">
                        <button type="button" class="btn btn-soft-danger border-0 p-2 rounded-circle shadow-sm remove-row" data-bs-toggle="tooltip" title="حذف السطر"><i class="mdi mdi-trash-can font-18"></i></button>
                    </div>
                </div>`;

                let $newRow = $(newRow);
                $('#dynamic-sections-container').append($newRow);
                $newRow.fadeIn(300); // انيميشن ناعم للإضافة

                $('.select2-new').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? "rtl" : "ltr",
                    placeholder: "ابحث واختر المعلمين...",
                    dropdownParent: $('#addSectionModal')
                }).removeClass('select2-new').addClass('select2-modal');

                // إعادة تفعيل التولتيب للزر الجديد
                var tooltipTriggerList = [].slice.call(document.querySelectorAll(
                    '.remove-row[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });

                rowIndex++;
            });

            $(document).on('click', '.remove-row', function() {
                var tooltip = bootstrap.Tooltip.getInstance(this);
                if (tooltip) tooltip.dispose();

                $(this).closest('.section-row').fadeOut(300, function() {
                    $(this).remove();
                });
            });

            // 🔥 تأمين الفورم ضد الإرسال المزدوج 🔥
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1 fs-4 align-middle"></i> جاري الحفظ...');
            });

            // SweetAlert لتأكيد الحذف
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد الحذف النهائي؟',
                    text: "سيتم حذف هذه الشعبة ولن تتمكن من التراجع! يرجى التأكد من عدم وجود طلاب مسجلين بها.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف الشعبة',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
