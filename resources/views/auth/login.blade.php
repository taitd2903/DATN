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
    </div>
</div>

@endsection