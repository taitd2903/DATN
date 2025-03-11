

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">

<div class="login-container">
    <div class="login-box">
        <h2 class="title">ĐĂNG NHẬP</h2>

        @if(session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif

        @if($errors->any())
            <p style="color: red;">{{ $errors->first() }}</p>
        @endif


   
    <p>
       
    </p>
    

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <!-- Tài khoản -->
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                <span class="icon"><i class="bi bi-person"></i></span>
            </div>

            <!-- Mật khẩu -->
            <div class="input-group">
                <input type="password" name="password" placeholder="Mật khẩu" required>
                <span class="icon"><i class="bi bi-lock"></i></span>
            </div>

            <!-- Ghi nhớ & Quên mật khẩu -->
            <div class="options">
                <label><input type="checkbox" name="remember"> Ghi Nhớ</label>
                <a href="{{ route('password.request') }}">Quên Mật Khẩu?</a>
            </div>

            <!-- Nút Đăng nhập -->
            <button type="submit" class="login-btn">Đăng nhập</button>

            <!-- Đăng ký -->
            <p class="register">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a></p>
        </form>
    </div>
</div>

@endsection

{{-- kjhsdjshjksdho --}}