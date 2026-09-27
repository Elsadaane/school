@extends('backend.layouts.master')
@section('title', 'تاريخ التحويلات المالية')

@push('css')
    <style>
        .table-hover tbody tr:hover td {
            background-color: #f8fafc;
        }

        .bg-light-blue {
            background-color: #f0f4f8;
        }
    </style>
@endpush

@section('content')
    {{-- 🔥 حماية محتوى الشاشة بالكامل بصلاحية الإدارة المالية 🔥 --}}
    @can('view_payments')
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="page-title font-bold text-dark mb-1">
                        <i class="mdi mdi-swap-horizontal-circle-outline text-primary me-1"></i> سجل تحويلات الخزينة
                    </h4>
                    <p class="text-muted font-13 mb-0">متابعة دقيقة لكل العمليات المالية المنقولة بين حسابات المدرسة.</p>
                </div>
            </div>
        </div>

        {{-- 🔥 صندوق الفلاتر الذكية 🔥 --}}
        <div class="card shadow-sm border-0 mb-4 bg-light">
            <div class="card-body p-3">
                <form action="{{ route('admin.account-transfers.history') }}" method="GET">
                    <div class="row g-2 align-items-end justify-content-end">
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label text-muted font-12 fw-bold mb-1"><i class="mdi mdi-calendar-start"></i> من
                                تاريخ</label>
                            <input type="date" class="form-control fw-bold text-dark shadow-sm border-0" name="start_date"
                                value="{{ request('start_date', date('Y-m-01')) }}">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label text-muted font-12 fw-bold mb-1"><i class="mdi mdi-calendar-end"></i> إلى
                                تاريخ</label>
                            <input type="date" class="form-control fw-bold text-dark shadow-sm border-0" name="end_date"
                                value="{{ request('end_date', date('Y-m-t')) }}">
                        </div>
                        <div class="col-md-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 font-bold shadow-sm" title="بحث وتصفية">
                                <i class="mdi mdi-magnify me-1"></i> تصفية
                            </button>
                            <a href="{{ route('admin.account-transfers.history') }}"
                                class="btn btn-warning text-dark px-3 shadow-sm font-bold" title="إعادة ضبط">
                                <i class="mdi mdi-refresh"></i>
                            </a>
                            <button type="button" class="btn btn-success px-4 font-bold shadow-sm"
                                title="تصدير Excel (Coming Soon)">
                                <i class="mdi mdi-file-excel me-1"></i> تصدير
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 🔥 جدول عرض البيانات 🔥 --}}
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
                <h6 class="mb-0 fw-bold text-primary"><i class="mdi mdi-history font-20 me-2 text-success align-middle"></i>
                    تفاصيل الحركات المالية المجراة</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0 text-center align-middle">
                        <thead class="bg-light-blue text-muted">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th><i class="mdi mdi-bank-transfer-out text-danger fs-5 align-middle me-1"></i> سُحب من حساب
                                </th>
                                <th><i class="mdi mdi-bank-transfer-in text-success fs-5 align-middle me-1"></i> أُودع في حساب
                                </th>
                                <th><i class="mdi mdi-cash-multiple fs-5 align-middle me-1"></i> المبلغ المُحول</th>
                                <th><i class="mdi mdi-calendar-clock fs-5 align-middle me-1"></i> تاريخ التحويل</th>
                                <th><i class="mdi mdi-note-text-outline fs-5 align-middle me-1"></i> البيان / الملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $index => $transfer)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $transfers->firstItem() + $index }}</td>

                                    {{-- الحساب المحول منه (المصدر) --}}
                                    <td>
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 font-13">
                                            {{ $transfer->financialAccount->name_ar ?? 'حساب محذوف / مجهول' }}
                                        </span>
                                    </td>

                                    {{-- الحساب المحول إليه (الوجهة - Polymorphic Relation) --}}
                                    <td>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 font-13">
                                            {{ $transfer->payable->name_ar ?? 'حساب محذوف / مجهول' }}
                                        </span>
                                    </td>

                                    {{-- المبلغ مع تنسيق دقيق --}}
                                    <td class="font-bold text-dark font-monospace fs-5" dir="ltr">
                                        {{ number_format(abs($transfer->amount), 3) }} <small
                                            class="text-muted font-12">OMR</small>
                                    </td>

                                    {{-- التاريخ والوقت --}}
                                    <td dir="ltr">
                                        <div class="font-13 text-dark fw-bold">{{ $transfer->payment_date->format('Y-m-d') }}
                                        </div>
                                        <div class="font-11 text-muted">{{ $transfer->payment_date->format('h:i A') }}</div>
                                    </td>

                                    <td class="text-start">
                                        @if ($transfer->notes)
                                            <span class="text-muted font-13"><i
                                                    class="mdi mdi-information-outline text-info me-1"></i>{{ $transfer->notes }}</span>
                                        @else
                                            <span class="text-muted font-12">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="mdi mdi-database-off text-muted opacity-25" style="font-size: 60px;"></i>
                                        </div>
                                        <h5 class="text-muted fw-bold">لا يوجد تاريخ تحويلات في هذه الفترة</h5>
                                        <p class="text-muted font-13 mb-0">قم بتغيير تواريخ البحث لرؤية نتائج أقدم.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔥 الـ Pagination الذكي للحفاظ على الفلاتر 🔥 --}}
            @if ($transfers->hasPages())
                <div class="card-footer bg-white border-0 pt-3 rounded-bottom-4">
                    {{ $transfers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    @else
        {{-- شاشة حجب الدخول لمن لا يملك الصلاحية --}}
        <div class="alert alert-danger text-center py-5 shadow-sm rounded-4 border-0 mt-5">
            <i class="mdi mdi-shield-lock-outline text-danger" style="font-size: 80px;"></i>
            <h3 class="mt-3 fw-bolder text-danger">عفواً، الدخول محظور!</h3>
            <p class="text-dark fw-bold font-15">أنت لا تملك الصلاحيات الإدارية لمشاهدة السجلات المالية.</p>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-dark mt-3 px-5 rounded-pill fw-bold shadow-sm">العودة
                للرئيسية</a>
        </div>
    @endcan

@endsection
