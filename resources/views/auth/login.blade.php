@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">

<div class="container">
    <div class="login-wrapper">
        <!-- Phần Đăng Nhập -->
        <div class="login-box">
            <h2>Đăng nhập</h2>
          
            @if(session('success'))
                <p class="success-message">{{ session('success') }}</p>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
            <br>    @error('email') <span class="error">{{ $message }}</span> @enderror

                <input type="password" name="password" placeholder="Password">
              <br>  @error('password') <span class="error">{{ $message }}</span> @enderror
<br>
<div class="options">
    <label><input type="checkbox" name="remember"> Ghi Nhớ</label>
    <a href="{{ route('password.request') }}">Quên Mật Khẩu?</a>
</div>
  <br>              <button type="submit" class="btn">ĐĂNG NHẬP</button>
            </form>
        </div>

        <!-- Phần Đăng Ký -->
        <div class="signup-box">
            <h2>Chào mừng bạn yêu!</h2>
            <p>Nếu chưa có tài khoản, hãy đăng ký để tận hưởng không gian mua sắm với OceanSport!</p>
            <a href="{{ route('register') }}" class="btn-outline">ĐĂNG KÝ</a>
        </div>
    </div>
</div>

@endsection