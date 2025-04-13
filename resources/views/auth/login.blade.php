@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <div class="container">
        <div class="login-wrapper">
            <!-- Phần Đăng Nhập -->
            <div class="login-box">
                <h2>{{ __('messages.login') }}</h2>

                @if(session('success'))
                    <p class="success-message">{{ session('success') }}</p>
                @endif
                
                 
                 @if (session('ban_reason'))
                    <script>
                        alert("Tài khoản của bạn đã bị khóa!\nLý do: {{ session('ban_reason') }}");
                    </script>
                @endif

            

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
                    <br> @error('email') <span class="error">{{ $message }}</span> @enderror

                    <input type="password" name="password" placeholder="Password">
                    <br> @error('password') <span class="error">{{ $message }}</span> @enderror
                    <br>
                    <div class="options">
                        {{-- <label><input type="checkbox" name="remember"> {{ __('messages.remember_me') }}</label> --}}
                        <a href="{{ route('password.request') }}" style="margin-right: 30px">{{ __('messages.forgot_password') }}</a>
                    </div>
                    <br>
                    <button type="submit" class="btn">{{ __('messages.login') }}</button>
                </form>
            </div>

            
            <div class="signup-box">
                <h2>{{ __('messages.welcome_message') }}</h2>
                <p>{{ __('messages.signup_prompt') }}</p>
                <a href="{{ route('register') }}" class="btn-outline">{{ __('messages.register') }}</a>
            </div>
        </div>
    </div>

@endsection