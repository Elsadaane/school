@extends('backend.layouts.master')
@section('title', 'إضافة مستخدم جديد')
@section('title_page', 'إعدادات النظام - إدارة المستخدمين')

@push('css')
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
            background-color: #fff;
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 12px 15px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            background-color: #fff !important;
        }

        .input-group-text {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
            font-weight: bold;
        }

        .password-toggle {
            cursor: pointer;
            transition: 0.2s;
        }

        .password-toggle:hover {
            color: #3b82f6;
        }

        /* Error Styling */
        .input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }

        .error-msg {
            font-size: 12px;
            color: #ef4444;
            margin-top: 6px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Dark Mode Support */
        body[data-layout-color="dark"] .card-premium {
            background-color: #323a46;
            border-color: #424e5a;
        }

        body[data-layout-color="dark"] .form-label {
            color: #cbd5e1;
        }

        body[data-layout-color="dark"] .form-control,
        body[data-layout-color="dark"] .form-select {
            background-color: #3b4652;
            border-color: #424e5a;
            color: #fff;
        }

        body[data-layout-color="dark"] .form-control:focus,
        body[data-layout-color="dark"] .form-select:focus {
            border-color: #3b82f6;
        }

        body[data-layout-color="dark"] .input-group-text {
            background-color: #282f36;
            border-color: #424e5a;
            color: #adb5bd;
        }

        body[data-layout-color="dark"] .bg-light {
            background-color: #3b4652 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- 🌟 الهيدر --}}
        <div class="row mb-4 mt-2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div
                        class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary shadow-sm border border-primary border-opacity-25">
                        <i class="mdi mdi-account-plus-outline fs-3 align-middle"></i>
                    </div>
                    <div>
                        <h4 class="page-title font-bold text-dark mb-1">إضافة مستخدم إداري جديد</h4>
                        <p class="text-muted font-14 mb-0">إنشاء حساب جديد وتعيين الصلاحيات والدور الوظيفي المناسب له.</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}"
                        class="btn btn-light border rounded-pill px-4 fw-bold shadow-sm text-dark">
                        <i class="mdi mdi-arrow-left me-1 align-middle"></i> العودة للخلف
                    </a>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm rounded-3 mb-4 border-0 border-start border-danger border-4 fw-bold">
                <div class="d-flex align-items-center mb-2">
                    <i class="mdi mdi-alert-circle font-20 me-2 text-danger"></i>
                    <strong class="font-15 text-danger">عذراً، يرجى مراجعة الأخطاء التالية في الإدخالات:</strong>
                </div>
                <ul class="mb-0 ps-4 font-13">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-premium shadow-sm border-top border-primary border-4">
            <div class="card-body p-4 p-md-5">

                <form action="{{ route('admin.users.store') }}" method="POST" class="prevent-double-submit" novalidate>
                    @csrf

                    <div class="row g-4">
                        {{-- الاسم --}}
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل للموظف <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text border-end-0"><i
                                        class="mdi mdi-account-outline fs-5"></i></span>
                                <input type="text" name="name"
                                    class="form-control bg-light border-start-0 ps-0 @error('name') input-error @enderror"
                                    placeholder="مثال: أحمد عبدالله" value="{{ old('name') }}" required>
                            </div>
                            @error('name')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        {{-- البريد الإلكتروني --}}
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني للعمل <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text border-end-0"><i
                                        class="mdi mdi-email-outline fs-5 text-primary"></i></span>
                                <input type="email" name="email"
                                    class="form-control bg-light border-start-0 ps-0 font-monospace @error('email') input-error @enderror"
                                    placeholder="admin@domain.com" dir="ltr" value="{{ old('email') }}" required>
                            </div>
                            @error('email')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        {{-- كلمة المرور --}}
                        <div class="col-md-6">
                            <label class="form-label">كلمة المرور الافتراضية <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text border-end-0"><i class="mdi mdi-lock-outline fs-5"></i></span>
                                <input type="password" name="password" id="password_input"
                                    class="form-control bg-light border-start-0 border-end-0 ps-0 font-monospace @error('password') input-error @enderror"
                                    placeholder="••••••••" required>
                                <span class="input-group-text bg-light password-toggle border-start-0"
                                    onclick="togglePassword('password_input', this)">
                                    <i class="mdi mdi-eye-outline fs-5"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        {{-- تأكيد كلمة المرور --}}
                        <div class="col-md-6">
                            <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text border-end-0"><i
                                        class="mdi mdi-lock-check-outline fs-5"></i></span>
                                <input type="password" name="password_confirmation" id="password_conf_input"
                                    class="form-control bg-light border-start-0 border-end-0 ps-0 font-monospace"
                                    placeholder="••••••••" required>
                                <span class="input-group-text bg-light password-toggle border-start-0"
                                    onclick="togglePassword('password_conf_input', this)">
                                    <i class="mdi mdi-eye-outline fs-5"></i>
                                </span>
                            </div>
                        </div>

                        {{-- الصلاحية --}}
                        <div class="col-md-12">
                            <label class="form-label">الدور الوظيفي / الصلاحية <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-3">
                                <span class="input-group-text border-end-0"><i
                                        class="mdi mdi-shield-account-outline fs-5 text-warning"></i></span>
                                <select name="role"
                                    class="form-select bg-light border-start-0 ps-0 fw-bold @error('role') input-error @enderror"
                                    required>
                                    <option value="" disabled selected>-- حدد الدور الوظيفي للمستخدم --</option>
                                    @foreach ($roles as $role)
                                        {{-- حماية إضافية: لا تعرض صلاحية السوبر أدمن إلا لو اللي بيضيف حالياً سوبر أدمن --}}
                                        @if ($role->name === 'super_admin' && !auth()->user()->hasRole('super_admin'))
                                            @continue
                                        @endif
                                        <option value="{{ $role->name }}"
                                            {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('role')
                                <div class="error-msg"><i class="mdi mdi-close-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- زر الحفظ --}}
                    <div class="text-center border-top mt-5 pt-4">
                        <button type="submit"
                            class="btn btn-primary rounded-pill btn-lg px-5 fw-bold shadow-sm btn-submit">
                            <i class="mdi mdi-content-save-all fs-4 align-middle me-1"></i> حفظ وإضافة المستخدم
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // دالة إظهار وإخفاء الباسورد لتحسين ה-UX
        function togglePassword(inputId, iconElement) {
            let input = document.getElementById(inputId);
            let icon = iconElement.querySelector('i');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('mdi-eye-outline');
                icon.classList.add('mdi-eye-off-outline');
            } else {
                input.type = "password";
                icon.classList.remove('mdi-eye-off-outline');
                icon.classList.add('mdi-eye-outline');
            }
        }

        $(document).ready(function() {
            // منع الإرسال المزدوج
            $('.prevent-double-submit').on('submit', function() {
                let btn = $(this).find('.btn-submit');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin fs-4 align-middle me-2"></i> جاري إنشاء الحساب...'
                    );
            });
        });
    </script>
@endpush
