<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    @include('backend.layouts.head')
</head>

<body>
    <div id="wrapper">
        @include('backend.layouts.topbar')
        @include('backend.layouts.sidebar')

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('backend.layouts.footer')
        </div>
    </div>

    @include('backend.layouts.rightbar')
    <div class="rightbar-overlay"></div>
    @include('backend.layouts.scripts')
    {{-- @stack('scripts') --}}
</body>

</html>
