@extends('backend.layouts.master')
@section('title', 'إدارة العُهَد')
@section('title_page', 'الموارد البشرية - سجل العُهَد والأصول')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        /* 🌟 Premium UI 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
            overflow: hidden;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 24px;
        }

        .table-erp {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-erp thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
            text-align: center;
        }

        .table-erp tbody td {
            vertical-align: middle;
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            text-align: center;
            font-size: 14px;
            color: #334155;
        }

        .table-erp tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* الأزرار الموحدة */
        .action-btn {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            font-size: 18px;
            margin: 0 3px;
            background: #fff;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .action-btn.edit {
            background: #e0f2fe;
            color: #0284c7;
            border-color: #bae6fd;
        }

        .action-btn.edit:hover {
            background: #0284c7;
            color: #fff;
            border-color: #0284c7;
        }

        .action-btn.delete {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fecaca;
        }

        .action-btn.delete:hover {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }

        .action-btn.return {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }

        .action-btn.return:hover {
            background: #10b981;
            color: #fff;
            border-color: #10b981;
        }

        /* تحسينات الـ Select2 */
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #cbd5e1;
            min-height: 42px;
            border-radius: 8px;
            background-color: #f8fafc;
            font-weight: 600;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp thead th {
            background-color: #3b4652;
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody td {
            color: #adb5bd;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .table-erp tbody tr:hover td {
            background-color: #38414e;
        }

        body[data-layout-color="dark"] .action-btn {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #ced4da;
        }

        body[data-layout-color="dark"] .bg-light,
        body[data-layout-color="dark"] .modal-header {
            background-color: #3b4652 !important;
            border-color: #424e5a !important;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🔥 الإحصائيات العلوية 🔥 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-premium border-bottom border-primary border-4 shadow-sm h-100">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold mb-2">إجمالي العُهد</h6>
                            <h3 class="fw-bolder text-primary mb-0">{{ $totalItems }}</h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class='bx bx-laptop'></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-premium border-bottom border-warning border-4 shadow-sm h-100">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold mb-2">قيد الاستخدام</h6>
                            <h3 class="fw-bolder text-warning mb-0">{{ $withEmployee ?? ($inUse ?? 0) }}</h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class='bx bx-time-five'></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-premium border-bottom border-success border-4 shadow-sm h-100">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold mb-2">تم إرجاعها</h6>
                            <h3 class="fw-bolder text-success mb-0">{{ $returned }}</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success"><i class='bx bx-check-shield'></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-premium border-bottom border-danger border-4 shadow-sm h-100">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold mb-2">تالف أو مفقود</h6>
                            <h3 class="fw-bolder text-danger mb-0">{{ $damaged ?? ($damagedOrLost ?? 0) }}</h3>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class='bx bx-error-circle'></i></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- رأس الجدول وزر الإضافة --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 class="fw-bolder text-dark mb-0">
                <i class='bx bx-briefcase-alt-2 text-primary me-2 fs-3 align-middle'></i>
                سجل تسليم وإرجاع العُهَد
            </h4>

            {{-- 🔥 زر الإضافة محمي بالصلاحية 🔥 --}}
            @can('manage_custody')
                <button class="btn btn-primary fw-bold shadow-sm px-4 py-2 rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#addCustodyModal">
                    <i class='bx bx-plus-circle me-1 align-middle fs-5'></i> تسليم عُهدة جديدة
                </button>
            @endcan
        </div>

        {{-- جدول العهد --}}
        <div class="card card-premium mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-erp table-hover w-100 mb-0">
                        <thead class="sticky-top" style="z-index: 10;">
                            <tr>
                                <th class="text-start ps-4">اسم الموظف</th>
                                <th>تفاصيل العُهدة</th>
                                <th>تاريخ الاستلام</th>
                                <th>تاريخ الإرجاع</th>
                                <th>الحالة</th>
                                @can('manage_custody')
                                    <th style="width: 150px;">الإجراءات</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($custodies as $custody)
                                <tr>
                                    <td class="text-start ps-4 text-primary fw-bolder">
                                        {{ app()->getLocale() == 'ar' ? $custody->employee->full_name_ar ?? 'محذوف' : $custody->employee->full_name_en ?? ($custody->employee->full_name_ar ?? 'Deleted') }}
                                        <div class="text-muted fw-bold font-12 mt-1">
                                            <i
                                                class="bx bx-buildings align-middle me-1"></i>{{ app()->getLocale() == 'ar' ? $custody->employee->department->name_ar ?? '' : $custody->employee->department->name_en ?? '' }}
                                        </div>
                                    </td>

                                    {{-- عرض الأصل الثابت أو العهدة العادية بذكاء --}}
                                    <td>
                                        @if ($custody->asset_id)
                                            <span
                                                class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 mb-1 px-2 py-1 rounded-2">
                                                <i class='bx bx-buildings me-1 align-middle'></i>أصل ثابت
                                            </span><br>
                                            <span class="fw-bold text-dark">
                                                {{ $custody->asset?->category?->name_ar ?? (\Illuminate\Support\Str::limit($custody->asset?->transaction_details, 40) ?? 'أصل') }}
                                                <span
                                                    class="text-primary font-monospace">({{ $custody->asset?->asset_code ?? 'بدون كود' }})</span>
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 mb-1 px-2 py-1 rounded-2">
                                                <i class='bx bx-package me-1 align-middle'></i>عُهدة عادية
                                            </span><br>
                                            <span class="fw-bold text-dark">{{ $custody->item_name }}</span>
                                        @endif
                                    </td>

                                    <td class="text-dark fw-bold font-monospace">
                                        {{ \Carbon\Carbon::parse($custody->receive_date)->format('Y-m-d') }}
                                    </td>
                                    <td class="text-muted font-monospace fw-bold">
                                        {{ $custody->return_date ? \Carbon\Carbon::parse($custody->return_date)->format('Y-m-d') : '---' }}
                                    </td>
                                    <td>
                                        @if ($custody->status == 'in_use' || $custody->status == 'with_employee')
                                            <span
                                                class="badge bg-warning bg-opacity-10 border border-warning border-opacity-50 text-dark px-3 py-2 rounded-pill font-13">
                                                <i class='bx bx-time-five me-1 align-middle'></i>قيد الاستخدام
                                            </span>
                                        @elseif($custody->status == 'returned')
                                            <span
                                                class="badge bg-success bg-opacity-10 border border-success border-opacity-50 text-success px-3 py-2 rounded-pill font-13">
                                                <i class='bx bx-check-double me-1 align-middle'></i>تم الإرجاع
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-danger bg-opacity-10 border border-danger border-opacity-50 text-danger px-3 py-2 rounded-pill font-13">
                                                <i class='bx bx-x-circle me-1 align-middle'></i>تالف / مفقود
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 🔥 أزرار الإجراءات محمية بالصلاحية 🔥 --}}
                                    @can('manage_custody')
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                @if ($custody->status == 'in_use' || $custody->status == 'with_employee')
                                                    <button type="button"
                                                        class="action-btn return btn-return-custody shadow-sm"
                                                        data-id="{{ $custody->id }}" data-bs-toggle="modal"
                                                        data-bs-target="#returnCustodyModal" title="إرجاع وإخلاء طرف">
                                                        <i class='bx bx-undo'></i>
                                                    </button>
                                                @endif

                                                <button type="button" class="action-btn edit btn-edit-custody shadow-sm"
                                                    data-id="{{ $custody->id }}" data-employee="{{ $custody->employee_id }}"
                                                    data-asset="{{ $custody->asset_id }}"
                                                    data-item="{{ $custody->item_name }}"
                                                    data-date="{{ \Carbon\Carbon::parse($custody->receive_date)->format('Y-m-d') }}"
                                                    data-notes="{{ $custody->notes }}" data-bs-toggle="modal"
                                                    data-bs-target="#editCustodyModal" title="تعديل">
                                                    <i class='bx bx-edit'></i>
                                                </button>

                                                <form action="{{ route('admin.hr.custodies.destroy', $custody->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="action-btn delete btn-delete shadow-sm"
                                                        title="حذف العهدة">
                                                        <i class='bx bx-trash'></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->can('manage_custody') ? 6 : 5 }}"
                                        class="p-5 text-muted text-center fw-bold">
                                        <i class='bx bx-box fs-1 d-block mb-3 opacity-25' style="font-size: 50px;"></i>
                                        لا توجد عُهد مسجلة حالياً
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 إضافة الترقيم (Pagination) 🔥 --}}
            @if (method_exists($custodies, 'hasPages') && $custodies->hasPages())
                <div class="card-footer bg-white border-top-0 pt-3 pb-3 rounded-bottom-4">
                    {{ $custodies->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- 🔥 مودال الإضافة (محمي) 🔥 --}}
    @can('manage_custody')
        <div class="modal fade" id="addCustodyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg border-top border-primary border-4">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='bx bx-laptop me-1 fs-4 align-middle'></i> تسليم
                            عُهدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.hr.custodies.store') }}" method="POST" class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">الموظف المستلم <span
                                        class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select select2-init" required style="width: 100%;">
                                    <option value="" disabled selected>-- اختر الموظف --</option>
                                    @foreach ($employees ?? [] as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">ربط العهدة بأصل ثابت (اختياري)</label>
                                <select name="asset_id" id="add_asset_id" class="form-select select2-init"
                                    onchange="toggleItemName('add')" style="width: 100%;">
                                    <option value="">بدون ربط (عُهدة عادية)</option>
                                    @foreach ($assets ?? [] as $asset)
                                        <option value="{{ $asset->id }}">
                                            {{ $asset->category?->name_ar ?? (\Illuminate\Support\Str::limit($asset->transaction_details, 40) ?? 'أصل') }}
                                            - ({{ $asset->asset_code ?? 'بدون كود' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3" id="add_item_name_div">
                                <label class="form-label fw-bold text-muted">اسم وتفاصيل العُهدة <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="item_name" id="add_item_name"
                                    class="form-control fw-bold bg-light border-0 shadow-sm"
                                    placeholder="مثال: لابتوب ديل، مفاتيح مكتب...">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">تاريخ الاستلام <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="receive_date"
                                    class="form-control text-center fw-bold bg-light border-0 shadow-sm font-monospace"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold text-muted">ملاحظات وحالة العُهدة</label>
                                <textarea name="notes" class="form-control bg-light border-0 shadow-sm" rows="2"
                                    placeholder="حالة اللابتوب جيدة، يوجد خدش في الشاشة..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4 px-4 pb-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm rounded-pill btn-submit">
                                <i class="bx bx-check-shield me-1 align-middle fs-5"></i> حفظ وتأكيد التسليم
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔥 مودال التعديل (محمي) 🔥 --}}
        <div class="modal fade" id="editCustodyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg border-top border-primary border-4">
                    <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-primary"><i class='bx bx-edit me-1 fs-4 align-middle'></i> تعديل
                            بيانات العُهدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="editCustodyForm" class="prevent-double-submit">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" id="edit_employee_id" class="form-select select2-edit" required
                                    style="width: 100%;">
                                    @foreach ($employees ?? [] as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">ربط العهدة بأصل ثابت (اختياري)</label>
                                <select name="asset_id" id="edit_asset_id" class="form-select select2-edit"
                                    onchange="toggleItemName('edit')" style="width: 100%;">
                                    <option value="">بدون ربط (عُهدة عادية)</option>
                                    @foreach ($assets ?? [] as $asset)
                                        <option value="{{ $asset->id }}">
                                            {{ $asset->category?->name_ar ?? (\Illuminate\Support\Str::limit($asset->transaction_details, 40) ?? 'أصل') }}
                                            - ({{ $asset->asset_code ?? 'بدون كود' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3" id="edit_item_name_div">
                                <label class="form-label fw-bold text-muted">اسم العهدة <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="item_name" id="edit_item_name"
                                    class="form-control fw-bold bg-light border-0 shadow-sm">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">تاريخ الاستلام <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="receive_date" id="edit_receive_date"
                                    class="form-control text-center fw-bold bg-light border-0 shadow-sm font-monospace"
                                    required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold text-muted">ملاحظات</label>
                                <textarea name="notes" id="edit_notes" class="form-control bg-light border-0 shadow-sm" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4 px-4 pb-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm rounded-pill btn-submit">
                                <i class="bx bx-check me-1 align-middle fs-5"></i> حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔥 مودال استرجاع العهدة (محمي) 🔥 --}}
        <div class="modal fade" id="returnCustodyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg border-top border-success border-4">
                    <div class="modal-header border-bottom-0 pb-0 bg-success bg-opacity-10 rounded-top-4 pt-4 px-4">
                        <h5 class="modal-title fw-bold text-success"><i class='bx bx-shield-check me-1 fs-4 align-middle'></i>
                            استرجاع عهدة (إخلاء طرف)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" method="POST" id="returnCustodyForm" enctype="multipart/form-data"
                        class="prevent-double-submit">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">حالة العهدة عند الاستلام <span
                                        class="text-danger">*</span></label>
                                <select name="status" class="form-select border-success fw-bold text-success shadow-sm"
                                    required>
                                    <option value="returned">سليمة وبحالة جيدة (إخلاء طرف كامل)</option>
                                    <option value="damaged">تالفة / بها مشاكل / مفقودة (تحتاج مراجعة مالية)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">ملاحظات الاستلام (إن وجدت)</label>
                                <textarea name="return_notes" class="form-control bg-light border-0 shadow-sm" rows="2"
                                    placeholder="اكتب أي ملاحظات عن حالة العهدة..."></textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold text-muted">صورة التوقيع / مستند الاستلام</label>
                                <input type="file" name="signature"
                                    class="form-control border-dashed p-2 bg-light shadow-sm"
                                    accept="image/png, image/jpeg, image/jpg">
                                <small class="text-muted d-block mt-2"><i class="bx bx-info-circle align-middle"></i> اختياري،
                                    يمكنك إرفاق مستند المخالصة الموقّع</small>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4 px-4 pb-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold shadow-sm"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm rounded-pill btn-submit">
                                <i class="bx bx-check-shield me-1 align-middle fs-5"></i> تأكيد الاستلام وإخلاء الطرف
                            </button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleItemName(mode) {
            let assetSelect = document.getElementById(mode + '_asset_id').value;
            let itemNameDiv = document.getElementById(mode + '_item_name_div');
            let itemNameInput = document.getElementById(mode + '_item_name');

            if (assetSelect) {
                $(itemNameDiv).slideUp();
                itemNameInput.removeAttribute('required');
                itemNameInput.value = '';
            } else {
                $(itemNameDiv).slideDown();
                itemNameInput.setAttribute('required', 'required');
            }
        }

        $(document).ready(function() {
            let isRtl = "{{ app()->getLocale() }}" === 'ar';

            $('#addCustodyModal').on('shown.bs.modal', function() {
                $('.select2-init').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#addCustodyModal')
                });
            });

            $('#editCustodyModal').on('shown.bs.modal', function() {
                $('.select2-edit').select2({
                    theme: 'bootstrap-5',
                    dir: isRtl ? 'rtl' : 'ltr',
                    dropdownParent: $('#editCustodyModal')
                });
            });

            $('.prevent-double-submit').on('submit', function() {
                $(this).find('.btn-submit').prop('disabled', true).html(
                    '<i class="bx bx-loader-alt bx-spin me-1 align-middle fs-5"></i> جاري الحفظ...');
            });

            $('.btn-edit-custody').click(function() {
                let id = $(this).data('id');
                $('#edit_employee_id').val($(this).data('employee')).trigger('change');
                $('#edit_asset_id').val($(this).data('asset')).trigger('change');
                $('#edit_item_name').val($(this).data('item'));
                $('#edit_receive_date').val($(this).data('date'));
                $('#edit_notes').val($(this).data('notes'));
                let url = "{{ route('admin.hr.custodies.update', ':id') }}".replace(':id', id);
                $('#editCustodyForm').attr('action', url);
                toggleItemName('edit');
            });

            $('.btn-return-custody').click(function() {
                let id = $(this).data('id');
                let url = "{{ route('admin.hr.custodies.status', ':id') }}".replace(':id', id);
                $('#returnCustodyForm').attr('action', url);
            });

            $('.btn-delete').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'هل أنت متأكد من الحذف؟',
                    text: 'سيتم مسح العهدة من سجل الموظف نهائياً.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'نعم، احذف نهائياً',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'عملية ناجحة',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات!',
                    html: `<ul class="text-start mb-0 fw-bold">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                           </ul>`
                });
            @endif
        });
    </script>
@endpush
