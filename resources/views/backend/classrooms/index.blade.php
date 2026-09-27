@extends('backend.layouts.master')

@section('title', 'الصفوف الدراسية (القاموس العام)')

@push('css')
    <style>
        .hover-effect {
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .hover-effect:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
            border-color: #e2e8f0;
        }

        .grade-icon-box {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            margin: 0 auto;
        }

        body[data-layout-color="dark"] .hover-effect {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="page-title font-bold mb-0"><i class="mdi mdi-bookshelf text-primary me-1"></i> الصفوف الدراسية
                    (المراحل)</h4>
                <p class="text-muted small mb-0 mt-1">إعداد القاموس العام للصفوف المتاحة لجميع فروع المدارس.</p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <div class="input-group input-group-sm shadow-sm" style="width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                    <input type="text" id="searchGrade" class="form-control border-start-0 ps-0"
                        placeholder="ابحث عن صف...">
                </div>

                {{-- 🔥 حماية زرار الإضافة بالصلاحية المعتمدة في الـ Seeder 🔥 --}}
                @can('manage_academics')
                    <button type="button" class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal"
                        data-bs-target="#addClassroomModal">
                        <i class="mdi mdi-plus me-1"></i> إضافة صف دراسي
                    </button>
                @endcan
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold shadow-sm" role="alert">
            <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm alert-dismissible fade show">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row" id="gradesCardsContainer">
        @forelse($classrooms as $classroom)
            <div class="col-xl-3 col-md-4 col-sm-6 grade-card-wrapper mb-4">
                <div class="card text-center shadow-sm hover-effect rounded-4 h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="grade-icon-box mb-3">
                            <i class="mdi mdi-google-classroom font-28"></i>
                        </div>
                        <h4 class="text-dark font-bold mt-2 mb-1 grade-name">{{ $classroom->name_ar }}</h4>
                        <p class="text-muted small mb-3">{{ $classroom->name_en ?? '---' }}</p>

                        <div class="bg-light p-3 rounded-3 mb-4 d-flex justify-content-around mt-auto">
                            <div class="text-center">
                                <span class="d-block text-muted font-12 fw-bold mb-1">الشعب</span>
                                <span class="font-18 fw-bold text-primary">{{ $classroom->sections_count ?? 0 }}</span>
                            </div>
                            <div class="border-start"></div>
                            <div class="text-center">
                                <span class="d-block text-muted font-12 fw-bold mb-1">الطلاب</span>
                                <span class="font-18 fw-bold text-success">{{ $classroom->students_count ?? 0 }}</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            {{-- زر العرض متاح للجميع --}}
                            <a href="{{ route('admin.classrooms.show', $classroom->id) }}"
                                class="btn btn-sm btn-soft-info px-3 rounded-pill" title="عرض التفاصيل والشعب">
                                <i class="mdi mdi-eye-outline font-18"></i>
                            </a>

                            {{-- 🔥 حماية أزرار التعديل والحذف بالصلاحية 🔥 --}}
                            @can('manage_academics')
                                <button type="button" class="btn btn-sm btn-soft-primary px-3 rounded-pill"
                                    data-bs-toggle="modal" data-bs-target="#editClassroomModal{{ $classroom->id }}"
                                    title="تعديل">
                                    <i class="mdi mdi-square-edit-outline font-18"></i>
                                </button>

                                <form action="{{ route('admin.classrooms.destroy', $classroom->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-soft-danger px-3 rounded-pill"
                                        onclick="return confirm('هل أنت متأكد من حذف هذا الصف؟ لن يتم الحذف إذا كان مرتبطاً بشعب.')"
                                        title="حذف">
                                        <i class="mdi mdi-trash-can-outline font-18"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 mt-4">
                <div
                    class="avatar-lg bg-soft-secondary rounded-circle mx-auto mb-3 d-flex justify-content-center align-items-center">
                    <i class="mdi mdi-google-classroom text-muted fs-1"></i>
                </div>
                <h5 class="text-muted fw-bold">لم يتم إضافة أي صفوف دراسية للقاموس العام حتى الآن.</h5>
                @can('manage_academics')
                    <button type="button" class="btn btn-primary rounded-pill px-4 mt-3 shadow-sm fw-bold"
                        data-bs-toggle="modal" data-bs-target="#addClassroomModal">
                        <i class="mdi mdi-plus me-1"></i> إضافة صف جديد
                    </button>
                @endcan
            </div>
        @endforelse
    </div>

    {{-- ========================================== --}}
    {{-- 🔥 قسم المودالات (مستخرج خارج الـ Grid للحفاظ على البوتستراب) 🔥 --}}
    {{-- ========================================== --}}

    @can('manage_academics')
        {{-- مودال الإضافة الديناميكي --}}
        <div class="modal fade" id="addClassroomModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom-0 bg-primary text-white rounded-top-4">
                        <h5 class="modal-title font-bold text-white"><i class="mdi mdi-plus-circle me-1"></i> إضافة صفوف جديدة
                            للقاموس</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.classrooms.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="alert alert-info border-0 shadow-sm mb-4 fw-bold">
                                <i class="mdi mdi-information-outline me-1"></i> هذه الشاشة لإعداد "المُسميات العامة" (مثال:
                                الصف الأول، الثاني). التسكين الفعلي للطلاب يتم لاحقاً داخل (الشُعب).
                            </div>

                            <div id="dynamic-grades-container">
                                <div class="row grade-row align-items-center mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label text-muted font-12 fw-bold">الاسم بالإنجليزية</label>
                                        <input type="text" class="form-control text-start" name="grades[0][name_en]"
                                            placeholder="مثال: Grade 1" dir="ltr">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label text-muted font-12 fw-bold">الاسم بالعربية <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control text-start" name="grades[0][name_ar]"
                                            placeholder="مثال: الصف الأول الابتدائي" required dir="rtl">
                                    </div>
                                    <div class="col-md-2 text-center mt-3 mt-md-4">
                                        <button type="button" class="btn btn-light text-muted border-0 shadow-sm" disabled
                                            title="الصف الأول لا يمكن حذفه"><i class="mdi mdi-trash-can"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="text-start mt-4 border-top pt-3">
                                <button type="button" id="add-row-btn"
                                    class="btn btn-soft-info rounded-pill px-3 fw-bold shadow-sm">
                                    <i class="mdi mdi-plus"></i> إضافة حقل لصف آخر
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm"><i
                                    class="mdi mdi-content-save"></i> حفظ الصفوف</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- توليد مودالات التعديل الخاصة بكل صف --}}
        @foreach ($classrooms as $classroom)
            <div class="modal fade" id="editClassroomModal{{ $classroom->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-bottom-0 bg-light rounded-top-4">
                            <h5 class="modal-title font-bold text-primary"><i class="mdi mdi-pencil me-1"></i> تعديل بيانات
                                الصف</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.classrooms.update', $classroom->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body p-4 text-start">
                                <div class="mb-3">
                                    <label class="form-label text-muted fw-bold">اسم الصف (عربي) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-start fw-bold" name="name_ar"
                                        value="{{ $classroom->name_ar }}" required dir="rtl">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted fw-bold">اسم الصف (إنجليزي)</label>
                                    <input type="text" class="form-control text-start fw-bold" name="name_en"
                                        value="{{ $classroom->name_en }}" dir="ltr">
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 bg-light rounded-bottom-4">
                                <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold"
                                    data-bs-dismiss="modal">إغلاق</button>
                                <button type="submit" class="btn btn-success px-4 rounded-pill fw-bold shadow-sm"><i
                                        class="mdi mdi-check"></i> حفظ التعديلات</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endcan

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // كود الإضافة المتعددة
            let rowIndex = 1;
            $('#add-row-btn').click(function() {
                let newRow = `
                <div class="row grade-row align-items-center mb-3">
                    <div class="col-md-5">
                        <input type="text" class="form-control text-start" name="grades[${rowIndex}][name_en]" placeholder="الاسم إنجليزي" dir="ltr">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control text-start fw-bold" name="grades[${rowIndex}][name_ar]" placeholder="الاسم عربي (مطلوب)" required dir="rtl">
                    </div>
                    <div class="col-md-2 text-center">
                        <button type="button" class="btn btn-danger text-white border-0 shadow-sm remove-row" title="حذف هذا الحقل"><i class="mdi mdi-trash-can"></i></button>
                    </div>
                </div>`;
                $('#dynamic-grades-container').append(newRow);
                rowIndex++;
            });

            // حذف الحقل الديناميكي المضاف حديثاً
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.grade-row').remove();
            });

            // كود البحث السريع المباشر في الـ Cards
            $("#searchGrade").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#gradesCardsContainer .grade-card-wrapper").filter(function() {
                    $(this).toggle($(this).find('.grade-name').text().toLowerCase().indexOf(value) >
                        -1)
                });
            });
        });
    </script>
@endpush
