@extends('backend.layouts.master')
@section('title', 'رادار الإنذارات المبكرة')

@push('css')
    <style>
        .radar-header {
            background: linear-gradient(135deg, #111827 0%, #dc3545 100%);
            color: white;
            border-radius: 15px;
            padding: 25px 30px;
            position: relative;
            overflow: hidden;
        }

        .radar-bg-pulse {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: radarPulse 3s infinite;
            pointer-events: none;
        }

        @keyframes radarPulse {
            0% {
                transform: scale(0.5);
                opacity: 0.8;
            }

            100% {
                transform: scale(3);
                opacity: 0;
            }
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        .alert-card {
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.04);
            height: 100%;
            transition: transform 0.2s;
        }

        .alert-card:hover {
            transform: translateY(-3px);
        }

        .icon-radar {
            width: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 24px;
        }

        .table-custom {
            white-space: nowrap;
        }

        .text-darken {
            color: #d97706 !important;
        }

        /* Dark Mode Adjustments */
        body[data-layout-color="dark"] .alert-card {
            background-color: #323a46 !important;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }

        body[data-layout-color="dark"] th {
            color: #ced4da;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] td {
            color: #adb5bd;
            border-color: #3b4652;
        }

        body[data-layout-color="dark"] .text-dark {
            color: #f8f9fa !important;
        }
    </style>
@endpush

@section('content')
    @php
        // ✅ حساب التنبيهات مرة واحدة (شاملة لكل الأقسام)
        $overdueCount = isset($dueInstallments) ? $dueInstallments->count() : 0;
        $absentCount = isset($absentStudents) ? $absentStudents->count() : 0;
        $lowStockCount = isset($lowStock) ? $lowStock->count() : 0;
        $docsCount = isset($expiringDocs) ? $expiringDocs->count() : 0;
        $invoicesCount = isset($dueInvoices) ? $dueInvoices->count() : 0;
        $contractsCount = isset($expiringContracts) ? $expiringContracts->count() : 0;

        $totalAlerts = $overdueCount + $absentCount + $lowStockCount + $docsCount + $invoicesCount + $contractsCount;
    @endphp

    <div class="row mb-4 mt-2">
        <div class="col-12">
            <div class="radar-header shadow-lg d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="radar-bg-pulse"></div>
                <div class="z-index-1">
                    <h3 class="fw-bold mb-1 text-white">
                        <i class="mdi mdi-radar me-2"
                            style="animation: spin 4s linear infinite; display: inline-block;"></i> رادار الإنذارات المبكرة
                        الشامل
                    </h3>
                    <p class="mb-0 text-white-50">نظام إنذار مبكر لكشف التعثر المالي، الغياب، نواقص المخزون، وتنبيهات
                        الموارد البشرية.</p>
                </div>
                <div class="z-index-1">
                    <span class="badge bg-white text-danger px-4 py-2 rounded-pill font-14 fw-bold shadow-sm">
                        <i class="mdi mdi-bell-ring-outline me-1"></i> إجمالي التنبيهات النشطة: {{ $totalAlerts }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- 🌟 كروت الملخص السريع (للاستفادة من بيانات الـ Controller الأخرى) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card alert-card bg-white p-3 text-center border-start border-warning border-4">
                <i class="mdi mdi-package-variant-closed text-warning fs-2"></i>
                <h3 class="fw-bold mb-0 mt-2 text-dark">{{ $lowStockCount }}</h3>
                <small class="text-muted fw-bold">نواقص المخزون</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card alert-card bg-white p-3 text-center border-start border-info border-4">
                <i class="mdi mdi-file-document-edit-outline text-info fs-2"></i>
                <h3 class="fw-bold mb-0 mt-2 text-dark">{{ $docsCount }}</h3>
                <small class="text-muted fw-bold">وثائق موظفين تنتهي قريباً</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card alert-card bg-white p-3 text-center border-start border-danger border-4">
                <i class="mdi mdi-receipt-text-outline text-danger fs-2"></i>
                <h3 class="fw-bold mb-0 mt-2 text-dark">{{ $invoicesCount }}</h3>
                <small class="text-muted fw-bold">فواتير موردين غير مسددة</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card alert-card bg-white p-3 text-center border-start border-secondary border-4">
                <i class="mdi mdi-file-account-outline text-secondary fs-2"></i>
                <h3 class="fw-bold mb-0 mt-2 text-dark">{{ $contractsCount }}</h3>
                <small class="text-muted fw-bold">عقود موظفين تنتهي قريباً</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- 🔴 1. رادار الأقساط المتأخرة --}}
        <div class="col-xl-6">
            <div class="card alert-card bg-white">
                <div
                    class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-danger mb-0 d-flex align-items-center">
                        <div class="icon-radar bg-danger bg-opacity-10 text-danger me-2 shadow-sm">
                            <i class="mdi mdi-cash-remove"></i>
                        </div>
                        رادار المتأخرات المالية
                    </h5>
                    <span class="badge bg-danger rounded-pill shadow-sm">{{ $overdueCount }} أقساط متأخرة</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-custom table-hover align-middle text-center mb-0">
                            <thead class="bg-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th class="text-start ps-4">الطالب</th>
                                    <th>تاريخ الاستحقاق</th>
                                    <th>المبلغ المتبقي</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- ✅ تم تغيير $overdueInstallments إلى $dueInstallments لتطابق الـ Controller --}}
                                @forelse($dueInstallments ?? [] as $inst)
                                    @php
                                        // ✅ تم تصحيح العلاقة من contract إلى studentContract
                                        $student = $inst->studentContract->student ?? null;
                                        $remaining = $inst->amount - $inst->paid_amount;
                                        $daysLate = \Carbon\Carbon::parse($inst->due_date)->diffInDays(now());
                                    @endphp
                                    @if ($student)
                                        <tr>
                                            <td class="text-start ps-4">
                                                <h6 class="mb-1 fw-bold text-dark"><i
                                                        class="mdi mdi-account-alert-outline text-danger me-1"></i>{{ $student->first_name }}
                                                    {{ $student->tribe }}</h6>
                                                <small
                                                    class="text-muted fw-bold">{{ $student->grade->name_ar ?? 'بدون صف' }}</small>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-danger fw-bold font-monospace d-block">{{ \Carbon\Carbon::parse($inst->due_date)->format('Y-m-d') }}</span>
                                                <small
                                                    class="badge bg-danger bg-opacity-10 text-danger mt-1 border border-danger border-opacity-25">متأخر
                                                    {{ $daysLate }} يوم</small>
                                            </td>
                                            <td>
                                                <strong
                                                    class="text-dark font-monospace fs-6">{{ number_format($remaining, 3) }}
                                                    OMR</strong>
                                            </td>
                                            <td>
                                                @if (isset($student->parent->phone))
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->parent->phone) }}"
                                                        target="_blank" rel="noopener noreferrer"
                                                        class="btn btn-sm btn-success rounded-pill px-3 shadow-sm"
                                                        title="تواصل عبر واتساب">
                                                        <i class="mdi mdi-whatsapp me-1"></i> تذكير
                                                    </a>
                                                @else
                                                    <span class="text-muted font-12 fw-bold">لا يوجد رقم</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-success fw-bold fs-5">
                                            <i class="mdi mdi-check-decagram fs-1 d-block mb-2 opacity-75"></i>لا توجد أي
                                            أقساط متأخرة حالياً!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🟡 2. رادار الغياب المتكرر --}}
        <div class="col-xl-6">
            <div class="card alert-card bg-white">
                <div
                    class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-warning text-darken mb-0 d-flex align-items-center">
                        <div class="icon-radar bg-warning bg-opacity-10 text-warning me-2 shadow-sm">
                            <i class="mdi mdi-calendar-remove text-darken"></i>
                        </div>
                        رادار الغياب (الخطر الأكاديمي)
                    </h5>
                    <span class="badge bg-warning text-dark rounded-pill shadow-sm">{{ $absentCount }} طلاب</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-custom table-hover align-middle text-center mb-0">
                            <thead class="bg-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th class="text-start ps-4">الطالب</th>
                                    <th>الصف والشعبة</th>
                                    <th>أيام الغياب</th>
                                    <th>ولي الأمر</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absentStudents ?? [] as $student)
                                    <tr>
                                        <td class="text-start ps-4">
                                            <h6 class="mb-0 fw-bold text-dark"><i
                                                    class="mdi mdi-account-cancel-outline text-warning text-darken me-1"></i>{{ $student->first_name }}
                                                {{ $student->tribe }}</h6>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold text-dark">{{ $student->grade->name_ar ?? '-' }}</span><br>
                                            <small class="text-muted">{{ $student->section->name_ar ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-danger rounded-pill px-3 py-2 fs-6 shadow-sm font-monospace">{{ $student->absent_count }}
                                                أيام</span>
                                        </td>
                                        <td>
                                            @if (isset($student->parent->phone))
                                                <a href="tel:{{ preg_replace('/[^0-9\+]/', '', $student->parent->phone) }}"
                                                    class="btn btn-sm btn-outline-dark rounded-pill px-3 shadow-sm">
                                                    <i class="mdi mdi-phone me-1"></i> اتصال
                                                </a>
                                            @else
                                                <span class="text-muted font-12 fw-bold">غير مسجل</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-success fw-bold fs-5">
                                            <i class="mdi mdi-account-check fs-1 d-block mb-2 opacity-75"></i>معدلات الحضور
                                            ممتازة جداً!
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
