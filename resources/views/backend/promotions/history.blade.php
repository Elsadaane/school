@extends('backend.layouts.master')
@section('title', 'سجل ترقيات الطلاب')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .page-wrapper {
            padding: 25px;
        }

        /* 🌟 كروت الإحصائيات 🌟 */
        .stat-card {
            border-radius: 16px;
            padding: 25px;
            color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card .stat-icon {
            position: absolute;
            right: -10px;
            bottom: -20px;
            font-size: 120px;
            opacity: 0.15;
            transform: rotate(15deg);
        }

        .stat-card .stat-label {
            font-size: 13px;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 900;
            font-family: 'Courier New', monospace;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        /* 🌟 كارت الجدول 🌟 */
        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background: #fff;
            overflow: hidden;
            border-top: 5px solid #ef4444;
        }

        /* 🌟 الجدول 🌟 */
        .table-custom {
            white-space: nowrap;
            margin-bottom: 0;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody tr {
            transition: all 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tbody td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
            padding: 14px 16px;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        /* 🌟 معلومات الطالب 🌟 */
        .student-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 14px;
        }

        /* 🌟 شارات السنوات والصفوف 🌟 */
        .year-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 5px;
        }

        .year-badge.from {
            background: #e2e8f0;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .year-badge.to {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .grade-name {
            font-size: 12px;
            font-weight: 700;
            display: block;
            margin-top: 3px;
        }

        .grade-name.from {
            color: #64748b;
        }

        .grade-name.to {
            color: #1e40af;
        }

        /* 🌟 شارات الحالة 🌟 */
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 2px solid;
        }

        .status-badge.success {
            background: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }

        .status-badge.danger {
            background: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }

        /* 🌟 أزرار العمليات 🌟 */
        .btn-rollback {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 13px;
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-rollback:hover {
            background: #ef4444;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-back {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 12px 24px;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        /* 🌟 Print 🌟 */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        {{-- 🌟 العنوان 🌟 --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                    <i class='bx bx-history text-danger fs-2'></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark">سجل عمليات الترحيل</h4>
                    <p class="text-muted mb-0 font-13">سجل بجميع عمليات النجاح والرسوب مع إمكانية التراجع</p>
                </div>
            </div>

            <a href="{{ route('admin.promotions.index') }}" class="btn-back">
                <i class='bx bx-arrow-back'></i>
                <span>العودة للترحيل</span>
            </a>
        </div>

        {{-- 🌟 كروت الإحصائيات 🌟 --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-gradient-primary">
                    <div class="position-relative z-1">
                        <div class="stat-label">إجمالي الترقيات</div>
                        <div class="stat-value">{{ $promotions->total() }}</div>
                    </div>
                    <i class='bx bx-transfer stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-success">
                    <div class="position-relative z-1">
                        <div class="stat-label">الناجحون</div>
                        <div class="stat-value">{{ $promotions->where('status', 'promoted')->count() }}</div>
                    </div>
                    <i class='bx bx-check-circle stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-danger">
                    <div class="position-relative z-1">
                        <div class="stat-label">الراسبون</div>
                        <div class="stat-value">{{ $promotions->where('status', 'retained')->count() }}</div>
                    </div>
                    <i class='bx bx-x-circle stat-icon'></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-gradient-warning">
                    <div class="position-relative z-1">
                        <div class="stat-label">هذا الشهر</div>
                        <div class="stat-value">{{ $promotions->where('created_at', '>=', now()->startOfMonth())->count() }}
                        </div>
                    </div>
                    <i class='bx bx-calendar stat-icon'></i>
                </div>
            </div>
        </div>

        {{-- رسائل النجاح والخطأ --}}
        @if (session('success'))
            <div class="alert alert-success fw-bold shadow-sm rounded-3 border-0">
                <i class='bx bx-check-circle me-2 fs-5 align-middle'></i> {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger fw-bold shadow-sm rounded-3 border-0">
                <i class='bx bx-error-circle me-2 fs-5 align-middle'></i> {{ $errors->first() }}
            </div>
        @endif

        {{-- 🌟 جدول السجل 🌟 --}}
        <div class="card card-premium">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-10 p-2 rounded-circle">
                        <i class='bx bx-list-ul text-danger fs-4'></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">سجل الترقيات</h5>
                        <small class="text-muted">عرض {{ $promotions->count() }} من {{ $promotions->total() }} عملية</small>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom text-center align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%" class="text-start ps-4">اسم الطالب</th>
                            <th width="20%">من (العام - الصف)</th>
                            <th width="20%">إلى (العام - الصف)</th>
                            <th width="15%">الحالة</th>
                            <th width="20%">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($promotions as $index => $promotion)
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration + $promotions->firstItem() - 1 }}</td>
                                <td class="text-start ps-4">
                                    <div class="student-name">
                                        <i class='bx bx-user-circle text-primary me-1'></i>
                                        {{ $promotion->student->first_name ?? '---' }}
                                        {{ $promotion->student->tribe ?? '' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="year-badge from">
                                        <i
                                            class='bx bx-calendar me-1'></i>{{ $promotion->fromAcademicYear->name ?? '---' }}
                                    </span>
                                    <span class="grade-name from">
                                        <i class='bx bx-book-open me-1'></i>{{ $promotion->fromGrade->name_ar ?? '---' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="year-badge to">
                                        <i
                                            class='bx bx-calendar-check me-1'></i>{{ $promotion->toAcademicYear->name ?? '---' }}
                                    </span>
                                    <span class="grade-name to">
                                        <i class='bx bx-book-reader me-1'></i>{{ $promotion->toGrade->name_ar ?? '---' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($promotion->status == 'promoted')
                                        <span class="status-badge success">
                                            <i class='bx bx-check-circle'></i> ناجح
                                        </span>
                                    @else
                                        <span class="status-badge danger">
                                            <i class='bx bx-x-circle'></i> راسب
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.promotions.rollback', $promotion->id) }}" method="POST"
                                        class="d-inline rollback-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-rollback btn-rollback-action">
                                            <i class='bx bx-undo'></i> تراجع
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-4">
                                        <i class='bx bx-file text-muted opacity-25' style="font-size: 80px;"></i>
                                        <h5 class="fw-bold text-muted mt-3">لا توجد أي عمليات ترحيل سابقة</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- الترقيم --}}
            @if ($promotions->hasPages())
                <div class="card-footer bg-white border-top-0 py-3 d-flex justify-content-center">
                    {{ $promotions->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.btn-rollback-action').click(function(e) {
                e.preventDefault();
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'تأكيد التراجع؟',
                    html: "سيتم إرجاع الطالب لصفه السابق وحذف مسودة عقده الجديد (إذا لم تُسدد بعد).<br><br><small class='text-danger'>هذا الإجراء لا يمكن التراجع عنه.</small>",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bx bx-undo"></i> نعم، نفذ التراجع',
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
