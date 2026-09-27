@forelse ($academicYears as $year)
    <div class="accordion-item border-0 mb-4 shadow-sm rounded-4 overflow-hidden bg-white">
        <h2 class="accordion-header" id="heading_year_{{ $year->id }}">
            <button class="accordion-button bg-light fw-bold text-dark px-4 py-3" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse_year_{{ $year->id }}" aria-expanded="true"
                aria-controls="collapse_year_{{ $year->id }}">
                <i class="bx bx-calendar-check me-2 text-primary fs-4 align-middle"></i>
                العام الدراسي: {{ $year->name }}
            </button>
        </h2>
        <div id="collapse_year_{{ $year->id }}" class="accordion-collapse collapse show"
            aria-labelledby="heading_year_{{ $year->id }}">
            <div class="accordion-body bg-white p-4">

                {{-- 🔥 تجميع الشعب بناءً على الصف الدراسي (Grade) بـ Null-safe Operator 🔥 --}}
                @php
                    $groupedSections = $year->sections->groupBy(function ($section) {
                        return $section->grade?->name_ar ?? 'شعب غير مصنفة أو بدون صف';
                    });
                @endphp

                @forelse ($groupedSections as $gradeName => $sections)
                    @php $gradeSlug = Str::slug($gradeName) ?: crc32($gradeName); @endphp

                    <div class="mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                        <div
                            class="d-flex justify-content-between align-items-center mb-3 bg-primary bg-opacity-10 p-3 rounded-3 border border-primary border-opacity-25 shadow-sm">
                            <h6 class="fw-bold text-primary mb-0 ms-2">
                                <i class="bx bx-layer me-2 fs-5 align-middle"></i>{{ $gradeName }}
                            </h6>

                            {{-- 🔥 ميزة UX: تحديد كل شعب هذا الصف بضغطة واحدة 🔥 --}}
                            <div class="form-check form-switch mb-0 me-2 d-flex align-items-center gap-2">
                                <label class="form-check-label font-13 fw-bold text-dark cursor-pointer mb-0"
                                    for="selectAll_{{ $year->id }}_{{ $gradeSlug }}">تحديد كل الشُعب</label>
                                <input class="form-check-input select-all-grade shadow-sm cursor-pointer m-0"
                                    type="checkbox" data-target=".grade-{{ $year->id }}-{{ $gradeSlug }}"
                                    id="selectAll_{{ $year->id }}_{{ $gradeSlug }}"
                                    style="width: 2.5em; height: 1.25em;">
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 px-2">
                            @foreach ($sections as $section)
                                <div
                                    class="form-check form-check-inline m-0 bg-light px-3 py-2 rounded-3 border border-secondary border-opacity-25 shadow-sm hover-elevate transition-all">
                                    {{-- تأمين الـ ID لعدم التضارب في الـ DOM --}}
                                    <input
                                        class="form-check-input grade-{{ $year->id }}-{{ $gradeSlug }} cursor-pointer border-secondary"
                                        type="checkbox" name="sections[]" value="{{ $section->id }}"
                                        id="sec_{{ $year->id }}_{{ $section->id }}"
                                        {{ in_array($section->id, $teacherSectionIds ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label cursor-pointer fw-bold ms-2 text-dark font-13"
                                        for="sec_{{ $year->id }}_{{ $section->id }}">
                                        {{ $section->name_ar }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div
                        class="text-center py-4 bg-light rounded-3 border border-dashed border-secondary border-opacity-50">
                        <i class="bx bx-info-circle fs-1 text-muted opacity-50 d-block mb-2"></i>
                        <span class="text-muted fw-bold font-14">لا توجد شعب أكاديمية مسندة لهذه السنة في هذا
                            الفرع.</span>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
@empty
    <div
        class="alert alert-warning text-center shadow-sm border-warning border-start border-4 rounded-3 fw-bold py-4 bg-warning bg-opacity-10">
        <i class="bx bx-error-circle fs-3 align-middle me-2 text-warning"></i>
        <span class="text-dark">لا توجد سنوات دراسية مسجلة ومفعلة لهذا الفرع حالياً. يرجى تهيئة العام الدراسي
            أولاً.</span>
    </div>
@endforelse

{{-- 
    💡 يرجى إضافة هذا السكربت البسيط في الصفحة الرئيسية (create/edit) 
    لضمان تشغيل ميزة "تحديد الكل" بشكل صحيح بعد حقن الـ AJAX
--}}
<script>
    // استخدام Event Delegation لضمان عملها على العناصر المحقونة بـ AJAX
    $(document).off('change', '.select-all-grade').on('change', '.select-all-grade', function() {
        let targetClass = $(this).data('target');
        $(targetClass).prop('checked', $(this).is(':checked'));
    });
</script>

<style>
    .hover-elevate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        border-color: #3b82f6 !important;
    }
</style>
