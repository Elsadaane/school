@extends('backend.layouts.master')
@section('title', 'قائمة الموظفين')
@section('title_page', 'الموارد البشرية - سجل الموظفين')

@push('css')
    <link href="{{ asset('ltr/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <style>
        .page-wrapper {
            background-color: #f8f9fa;
            padding: 25px;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .card-header-custom {
            background-color: #fff;
            border-bottom: 2px solid #f1f4f8;
            padding: 20px 25px;
        }

        .table-custom thead th {
            background-color: #f8fafd;
            color: #333;
            font-weight: 700;
            padding: 15px;
            border: none;
            text-align: center;
            white-space: nowrap;
        }

        .table-custom tbody td {
            vertical-align: middle;
            padding: 15px;
            border-bottom: 1px dashed #eee;
            font-weight: 600;
            color: #555;
            text-align: center;
            white-space: nowrap;
        }

        .table-custom tbody tr:hover td {
            background-color: #f8faff;
        }

        .avatar-wrapper {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
            margin-inline-end: 10px;
            border: 2px solid #eee;
            background-color: #fff;
        }

        .avatar-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 6px 12px;
            margin-bottom: 10px;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 4px 8px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid page-wrapper">

        <div class="card card-custom">
            <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class='mdi mdi-account-group text-primary me-2 fs-4 align-middle'></i> سجل بيانات الموظفين
                </h5>

                {{-- 🔥 زر تعيين موظف جديد محمي بالصلاحية 🔥 --}}
                @can('create_employees')
                    <a href="{{ route('admin.hr.employees.create') }}"
                        class="btn btn-primary fw-bold px-4 py-2 rounded-3 shadow-sm">
                        <i class='mdi mdi-account-plus-outline me-1'></i> تعيين موظف جديد
                    </a>
                @endcan
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="employeesTable" class="table table-custom w-100 mb-0">
                        <thead>
                            <tr>
                                <th>الكود</th>
                                <th class="text-start ps-4 no-sort">اسم الموظف</th>
                                <th>القسم / الإدارة</th>
                                <th>المسمى الوظيفي</th>
                                <th>الوردية</th>
                                <th>الراتب</th>
                                <th>الحالة</th>

                                {{-- 🔥 عمود الإجراءات يظهر فقط لمن يملك الصلاحية 🔥 --}}
                                @canany(['view_employees', 'edit_employees'])
                                    <th class="no-sort action-col">الإجراءات</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $emp)
                                <tr>
                                    <td class="text-primary fw-bolder font-monospace">#{{ $emp->employee_code }}</td>
                                    <td class="text-start text-dark fw-bold ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-wrapper shadow-sm">
                                                <img src="{{ $emp->profile_image ? asset('storage/' . $emp->profile_image) : asset('ltr/assets/images/avatars/avatar-1.png') }}"
                                                    alt="Avatar"
                                                    onerror="this.src='{{ asset('ltr/assets/images/avatars/avatar-1.png') }}'">
                                            </div>
                                            <div>
                                                <div>{{ $emp->full_name_ar }}</div>
                                                <small class="text-muted fw-normal font-monospace"
                                                    dir="ltr">{{ $emp->phone }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1 shadow-sm">
                                            {{ $emp->department->name_ar ?? 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">{{ $emp->job_title }}</td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1">
                                            {{ $emp->shift->name_ar ?? 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td class="text-success fw-bolder font-monospace" dir="ltr">
                                        {{ number_format($emp->basic_salary, 3) }} <small
                                            class="text-muted fw-normal font-sans-serif">ر.ع</small>
                                    </td>
                                    <td>
                                        @if ($emp->status == 'active')
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">
                                                <i class='mdi mdi-check-circle-outline me-1'></i> نشط
                                            </span>
                                        @elseif($emp->status == 'on_leave')
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1">
                                                <i class='mdi mdi-clock-outline me-1'></i> في إجازة
                                            </span>
                                        @elseif($emp->status == 'suspended')
                                            <span
                                                class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1">
                                                <i class='mdi mdi-minus-circle-outline me-1'></i> موقوف
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1">
                                                <i class='mdi mdi-close-circle-outline me-1'></i> منتهي
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 🔥 أزرار الإجراءات محمية بالصلاحية 🔥 --}}
                                    @canany(['view_employees', 'edit_employees'])
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                @can('view_employees')
                                                    <a href="{{ route('admin.hr.employees.show', $emp->id) }}"
                                                        class="btn btn-sm btn-outline-primary rounded-3 shadow-sm"
                                                        title="ملف الموظف">
                                                        <i class='mdi mdi-account-details-outline font-16'></i>
                                                    </a>
                                                @endcan

                                                @can('edit_employees')
                                                    <a href="{{ route('admin.hr.employees.edit', $emp->id) }}"
                                                        class="btn btn-sm btn-outline-secondary rounded-3 shadow-sm"
                                                        title="تعديل البيانات">
                                                        <i class='mdi mdi-pencil-outline font-16'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    @endcanany
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->canany(['view_employees', 'edit_employees']) ? 8 : 7 }}"
                                        class="p-5 text-muted fw-bold">
                                        <i
                                            class='mdi mdi-account-off-outline fs-1 d-block mb-2 text-secondary opacity-50'></i>
                                        لا يوجد موظفين مسجلين حالياً في النظام
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('ltr/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('ltr/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // تفعيل الـ DataTables بأمان وتجاوب
            let table = $('#employeesTable').DataTable({
                scrollX: true,
                language: {
                    search: "",
                    searchPlaceholder: "بحث...",
                    lengthMenu: "عرض _MENU_ سجل",
                    info: "عرض _START_ إلى _END_ من أصل _TOTAL_ موظف",
                    paginate: {
                        first: "الأول",
                        last: "الأخير",
                        next: "التالي",
                        previous: "السابق"
                    },
                    zeroRecords: "لم يتم العثور على أي موظف مطابق للبحث"
                },
                ordering: true,
                order: [
                    [0, 'desc'] // ترتيب حسب الأحدث كوداً
                ],
                columnDefs: [{
                    orderable: false,
                    targets: 'no-sort'
                }]
            });

            // ضبط أبعاد الـ ScrollX 
            setTimeout(() => {
                table.columns.adjust();
            }, 200);

            // إشعارات SweetAlert2
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'عملية ناجحة',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@endpush
