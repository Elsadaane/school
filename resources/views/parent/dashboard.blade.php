@extends('backend.layouts.master')
@section('title', __('app.student_login'))
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">مرحباً بك يا والدين: {{ Auth::guard('student')->user()->first_name }}</h4>
                    <p class="text-muted font-13">هنا ستجد دروسك ودرجاتك وتقييماتك.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
