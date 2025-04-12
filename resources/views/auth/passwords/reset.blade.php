@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<div class="container">
    <div class="reset-wrapper">
        <div class="reset-box">
            <h2>Đặt lại mật khẩu</h2>
            <p>Nhập mật khẩu mới để đặt lại tài khoản của bạn</p>

            @if (session('status'))
                <p class="success-message">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-box">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="Email của bạn" required>
                </div>

                <div class="input-box">
                    <label for="password"><i class="fa-solid fa-lock"></i>Mật khầu mới</label>
                    <input type="password" name="password" placeholder="Nhập mật khẩu mới" required>
                </div>

                <div class="input-box">
                    <label for="password_confirmation"><i class="fa-solid fa-lock"></i> Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu" required>
                </div>

                <button type="submit" class="btn">Đặt lại mật khẩu</button>
            </form>

            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <p class="error">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <p>Bạn nhớ mật khẩu?<a href="{{ route('login') }}">Đăng nhập</a></p>
        </div>
    </div>
</div>

@endsection