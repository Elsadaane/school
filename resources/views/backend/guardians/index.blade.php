@extends('backend.layouts.master')
@section('title', 'قائمة أولياء الأمور')

@push('css')
    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fc;
            transform: scale(1.002);
            transition: all 0.2s ease;
        }

        .filter-bar {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            margin-bottom: 20px;
        }

        .bg-soft-primary {
            background-color: rgba(73, 93, 124, 0.1);
            color: #0d6efd;
        }

        .bg-soft-info {
            background-color: rgba(13, 202, 240, 0.1);
            color: #0dcaf0;
        }

        .btn-soft-primary {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            border: none;
        }

        .btn-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: none;
        }

        .btn-soft-primary:hover {
            background-color: #0d6efd;
            color: white;
        }

        .btn-soft-danger:hover {
            background-color: #dc3545;
            color: white;
        }

        .guardian-avatar {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    {{-- 🌟 Page Header 🌟 --}}
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold mb-1"><i class="mdi mdi-human-male-female text-primary me-1"></i> إدارة أولياء
                    الأمور</h4>
                <p class="text-muted mb-0 font-13">إدارة بيانات أولياء الأمور، وسائل التواصل، وإضافة ملفات جديدة.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-light shadow-sm rounded-3" onclick="location.reload();" data-bs-toggle="tooltip"
                    title="تحديث البيانات">
                    <i class="mdi mdi-refresh font-18 text-secondary"></i>
                </button>

                @can('create_guardians')
                    <button type="button" class="btn btn-primary shadow-sm rounded-3 px-4 fw-bold" data-bs-toggle="modal"
                        data-bs-target="#addGuardianModal">
                        <i class="mdi mdi-plus-circle me-1"></i> إضافة ولي أمر
                    </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- 🌟 Smart Filter Bar 🌟 --}}
    <div class="filter-bar">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-merge shadow-sm rounded-3">
                    <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                    <input type="text" id="quickSearch" class="form-control border-start-0"
                        placeholder="بحث سريع بالاسم، الهوية، الهاتف، أو الإيميل...">
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span class="badge bg-soft-primary px-3 py-2 font-14 rounded-pill shadow-sm">إجمالي المسجلين:
                    {{ $guardians->count() }}</span>
            </div>
        </div>
    </div>

    {{-- 🌟 Alerts 🌟 --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 fw-bold">
            <i class="mdi mdi-alert-circle me-1 font-18"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 🌟 Data Table 🌟 --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 mb-5">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap mb-0 align-middle text-center" id="guardiansTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th class="text-start ps-4">بيانات ولي الأمر</th>
                                    <th>الفرع (النطاق)</th>
                                    <th>معلومات التواصل</th>
                                    <th>الجنسية / الهوية</th>
                                    <th>تاريخ التسجيل</th>
                                    @canany(['show_guardians', 'edit_guardians', 'delete_guardians'])
                                        <th style="width: 15%;">إجراءات</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($guardians as $index => $guardian)
                                    <tr class="guardian-row">
                                        <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                        <td class="text-start ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="guardian-avatar bg-soft-primary font-18">
                                                        {{ mb_substr($guardian->name_ar, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="m-0 fw-bold text-dark guardian-name">{{ $guardian->name_ar }}
                                                    </h6>
                                                    @if ($guardian->name_en)
                                                        <small class="text-muted d-block"
                                                            dir="ltr">{{ $guardian->name_en }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-light text-dark border">{{ $guardian->branch->name_ar ?? 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <span class="badge bg-light text-dark border guardian-phone" dir="ltr">
                                                    <i class="mdi mdi-phone text-success me-1"></i> {{ $guardian->phone }}
                                                </span>
                                                @if ($guardian->email)
                                                    <span class="badge bg-light text-dark border guardian-email">
                                                        <i class="mdi mdi-email-outline text-warning me-1"></i>
                                                        {{ $guardian->email }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold d-block">{{ $guardian->nationality_ar ?? 'غير محدد' }}</span>
                                            <small class="text-muted guardian-id"><i
                                                    class="mdi mdi-card-account-details-outline text-info"></i>
                                                {{ $guardian->national_id }}</small>
                                        </td>
                                        <td dir="ltr" class="font-13 text-muted">
                                            {{ $guardian->created_at->format('Y-m-d') }}</td>

                                        @canany(['show_guardians', 'edit_guardians', 'delete_guardians'])
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    @can('show_guardians')
                                                        <a href="{{ route('admin.guardians.show', $guardian->id) }}"
                                                            class="btn btn-sm btn-soft-info rounded-circle shadow-sm"
                                                            title="عرض الملف والأبناء">
                                                            <i class="mdi mdi-eye-outline font-18"></i>
                                                        </a>
                                                    @endcan
                                                    @can('edit_guardians')
                                                        <button type="button"
                                                            class="btn btn-sm btn-soft-primary rounded-circle shadow-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editGuardianModal{{ $guardian->id }}"
                                                            title="تعديل البيانات">
                                                            <i class="mdi mdi-square-edit-outline font-18"></i>
                                                        </button>
                                                    @endcan
                                                    @can('delete_guardians')
                                                        <form action="{{ route('admin.guardians.destroy', $guardian->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('تأكيد الحذف نهائياً؟');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-soft-danger rounded-circle shadow-sm"
                                                                title="حذف">
                                                                <i class="mdi mdi-trash-can-outline font-18"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>

                                    {{-- 🔥 مودال التعديل يولد لكل ولي أمر (محمي بالصلاحية) 🔥 --}}
                                    @can('edit_guardians')
                                        <div class="modal fade" id="editGuardianModal{{ $guardian->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0 shadow-lg rounded-4">
                                                    <div class="modal-header border-bottom-0 bg-light rounded-top-4">
                                                        <h5 class="modal-title font-bold text-primary"><i
                                                                class="mdi mdi-square-edit-outline me-1"></i> تعديل بيانات:
                                                            {{ $guardian->name_ar }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('admin.guardians.update', $guardian->id) }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf @method('PUT')
                                                        <div class="modal-body p-4 text-start">
                                                            <h6 class="text-primary mb-3 fw-bold border-bottom pb-2">البيانات
                                                                الشخصية</h6>
                                                            <div class="row g-3 mb-4">
                                                                <div class="col-md-12">
                                                                    <label class="form-label text-muted font-13 fw-bold">الفرع
                                                                        التابع له <span class="text-danger">*</span></label>
                                                                    <select name="branch_id"
                                                                        class="form-select bg-light border-0 shadow-sm"
                                                                        required>
                                                                        @foreach ($branches as $branch)
                                                                            <option value="{{ $branch->id }}"
                                                                                {{ $guardian->branch_id == $branch->id ? 'selected' : '' }}>
                                                                                {{ $branch->name_ar }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label text-muted font-13 fw-bold">اسم
                                                                        ولي الأمر (عربي) <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="name_ar" value="{{ $guardian->name_ar }}"
                                                                        required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label text-muted font-13 fw-bold">اسم
                                                                        ولي الأمر (إنجليزي)</label>
                                                                    <input type="text"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="name_en" value="{{ $guardian->name_en }}"
                                                                        dir="ltr">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label text-muted font-13 fw-bold">رقم
                                                                        الهوية / الجواز <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="national_id"
                                                                        value="{{ $guardian->national_id }}" required>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label text-muted font-13 fw-bold">تاريخ
                                                                        انتهاء الهوية</label>
                                                                    <input type="date"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="national_id_expiry"
                                                                        value="{{ $guardian->national_id_expiry }}">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label
                                                                        class="form-label text-muted font-13 fw-bold">الجنسية
                                                                        (عربي)</label>
                                                                    <input type="text"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="nationality_ar"
                                                                        value="{{ $guardian->nationality_ar }}">
                                                                </div>
                                                            </div>

                                                            <h6 class="text-primary mb-3 fw-bold border-bottom pb-2">بيانات
                                                                التواصل والعمل</h6>
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label class="form-label text-muted font-13 fw-bold">الهاتف
                                                                        <span class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="phone" value="{{ $guardian->phone }}"
                                                                        dir="ltr" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label
                                                                        class="form-label text-muted font-13 fw-bold">الإيميل
                                                                        <span class="text-danger">*</span></label>
                                                                    <input type="email"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="email" value="{{ $guardian->email }}"
                                                                        dir="ltr" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label text-muted font-13 fw-bold">اسم
                                                                        الوظيفة (عربي)</label>
                                                                    <input type="text"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="job_title_ar"
                                                                        value="{{ $guardian->job_title_ar }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label text-muted font-13 fw-bold">اسم
                                                                        الوظيفة (إنجليزي)</label>
                                                                    <input type="text"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="job_title_en"
                                                                        value="{{ $guardian->job_title_en }}" dir="ltr">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label text-muted font-13 fw-bold">جهة
                                                                        العمل (عربي)</label>
                                                                    <input type="text"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="work_place_ar"
                                                                        value="{{ $guardian->work_place_ar }}">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="form-label text-muted font-13 fw-bold">تحديث
                                                                        المرفق (اختياري)</label>
                                                                    <input type="file"
                                                                        class="form-control bg-light border-0 shadow-sm"
                                                                        name="id_card_image" accept=".jpg,.jpeg,.png,.pdf">
                                                                    @if ($guardian->id_card_image)
                                                                        <small class="text-success mt-1 d-block"><i
                                                                                class="mdi mdi-check-circle"></i> يوجد مرفق
                                                                            حالي</small>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label class="form-label text-danger font-13 fw-bold">كلمة
                                                                        المرور الجديدة (اتركه فارغاً إذا لم ترد التغيير)</label>
                                                                    <input type="password" name="password"
                                                                        class="form-control border-danger border-opacity-25"
                                                                        placeholder="أدخل كلمة المرور لتغييرها...">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                                                            <button type="button"
                                                                class="btn btn-secondary px-4 rounded-pill shadow-sm"
                                                                data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit"
                                                                class="btn btn-success px-4 rounded-pill shadow-sm fw-bold"><i
                                                                    class="mdi mdi-content-save me-1"></i> حفظ
                                                                التعديلات</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-5 text-muted">
                                            <i class="mdi mdi-account-off-outline font-36 d-block mb-2"></i> لا يوجد أولياء
                                            أمور مسجلين حالياً.
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

    {{-- 🔥 مودال الإضافة (المنفصل) محمي بالصلاحية 🔥 --}}
    @can('create_guardians')
        <div class="modal fade" id="addGuardianModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom-0 bg-light rounded-top-4">
                        <h5 class="modal-title font-bold text-primary"><i class="mdi mdi-account-plus-outline me-1"></i> إضافة
                            ولي أمر جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.guardians.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <h6 class="text-primary mb-3 fw-bold border-bottom pb-2">البيانات الشخصية</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label text-muted font-13 fw-bold">الفرع التابع له (مكان الإقامة/التسجيل)
                                        <span class="text-danger">*</span></label>
                                    <select name="branch_id" class="form-select bg-light border-0 shadow-sm" required>
                                        <option value="" selected disabled>اختر الفرع...</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">اسم ولي الأمر (عربي) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light border-0 shadow-sm" name="name_ar"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">اسم ولي الأمر (إنجليزي)</label>
                                    <input type="text" class="form-control bg-light border-0 shadow-sm" name="name_en"
                                        dir="ltr">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">رقم الهوية / الجواز <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light border-0 shadow-sm" name="national_id"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">تاريخ انتهاء الهوية</label>
                                    <input type="date" class="form-control bg-light border-0 shadow-sm"
                                        name="national_id_expiry">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted font-13 fw-bold">الجنسية (عربي)</label>
                                    <input type="text" class="form-control bg-light border-0 shadow-sm"
                                        name="nationality_ar">
                                </div>
                            </div>

                            <h6 class="text-primary mb-3 fw-bold border-bottom pb-2">بيانات التواصل والعمل</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">الهاتف <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-light border-0 shadow-sm" name="phone"
                                        dir="ltr" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">الإيميل <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control bg-light border-0 shadow-sm" name="email"
                                        dir="ltr" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">اسم الوظيفة (عربي)</label>
                                    <input type="text" class="form-control bg-light border-0 shadow-sm"
                                        name="job_title_ar">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">اسم الوظيفة (إنجليزي)</label>
                                    <input type="text" class="form-control bg-light border-0 shadow-sm"
                                        name="job_title_en" dir="ltr">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">جهة العمل (عربي)</label>
                                    <input type="text" class="form-control bg-light border-0 shadow-sm"
                                        name="work_place_ar">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted font-13 fw-bold">مرفق الهوية (اختياري)</label>
                                    <input type="file" class="form-control bg-light border-0 shadow-sm"
                                        name="id_card_image" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-muted font-13 fw-bold">كلمة المرور الابتدائية لدخول
                                        النظام</label>
                                    <input type="password" name="password"
                                        class="form-control border-primary border-opacity-25"
                                        placeholder="أدخل كلمة المرور المبدئية للولي الأمر...">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0 rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm fw-bold"><i
                                    class="mdi mdi-account-plus me-1"></i> تسجيل ولي الأمر</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // تفعيل الـ Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // 🔍 البحث السريع الذكي (مع إزالة الفراغات الإضافية لتجنب الأخطاء)
            $("#quickSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase().trim();
                $(".guardian-row").filter(function() {
                    var text = $(this).find('.guardian-name').text().toLowerCase() + " " +
                        $(this).find('.guardian-phone').text().toLowerCase() + " " +
                        $(this).find('.guardian-email').text().toLowerCase() + " " +
                        $(this).find('.guardian-id').text().toLowerCase();
                    $(this).toggle(text.indexOf(value) > -1);
                });
            });
        });
    </script>
@endpush
