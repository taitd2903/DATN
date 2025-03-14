

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">

<div class="login-container">
        <h2 class="title">ĐĂNG NHẬP</h2>

        @if(session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
   
    <p>
       
    </p>
    

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <!-- Tài khoản -->
            <div class="input-box">
                <i class="fa fa-user"></i>
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" >
                @error('email')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
            </div>

            <!-- Mật khẩu -->
            <div class="input-box">
                 <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Mật khẩu" >
                @error('password')
                <span class="text-red-500">{{ $message }}</span>
            @enderror
            </div>

            <!-- Ghi nhớ & Quên mật khẩu -->
            <div class="options">
                <label><input type="checkbox" name="remember"> Ghi Nhớ</label>
                <a href="{{ route('password.request') }}">Quên Mật Khẩu?</a>
            </div>

            <!-- Nút Đăng nhập -->
            <button type="submit" class="btn">Đăng nhập</button>

            <!-- Đăng ký -->
            <p>Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a></p>
        </form>
    </div>
</div>

@endsection

{{-- kjhsdjshjksdho --}}