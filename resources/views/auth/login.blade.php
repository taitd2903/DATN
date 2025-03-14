@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">

<div class="container">
    <div class="login-wrapper">
        <!-- Phần Đăng Nhập -->
        <div class="login-box">
            <h2>Sign in</h2>
          
            @if(session('success'))
                <p class="success-message">{{ session('success') }}</p>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
            <br>    @error('email') <span class="error">{{ $message }}</span> @enderror

<<<<<<< HEAD
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
=======
                <input type="password" name="password" placeholder="Password">
              <br>  @error('password') <span class="error">{{ $message }}</span> @enderror
<br>
                <a href="{{ route('password.request') }}" class="forgot-password">Forgot your password?</a>
  <br>              <button type="submit" class="btn">SIGN IN</button>
            </form>
        </div>

        <!-- Phần Đăng Ký -->
        <div class="signup-box">
            <h2>Hello, Friend!</h2>
            <p>Enter your personal details and start your journey with us</p>
            <a href="{{ route('register') }}" class="btn-outline">SIGN UP</a>
        </div>
>>>>>>> 1b5c3d64a15e0bdf1a2b8325e6a5b654e9aadbcb
    </div>
</div>

@endsection