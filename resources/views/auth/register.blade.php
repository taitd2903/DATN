@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/register.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">

<div class="auth-container">
    <div class="auth-box">
        <!-- Phần đăng nhập (bên trái) -->
        <div class="auth-left">
            <h2>{{ __('messages.welcome_message') }}</h2>
            <p>{{ __('messages.login_prompt') }}</p>
            <a href="{{ route('login') }}" class="btn-outline">{{ __('messages.login') }}</a>
        </div>

        <!-- Phần đăng ký (bên phải) -->
        <div class="auth-right">
            <h1>{{ __('messages.register_account') }}</h1>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="text" name="name" placeholder="{{ __('messages.name') }}" value="{{ old('name') }}">
                <br> @error('name') <span class="error">{{ $message }}</span> @enderror

                <input type="email" name="email" placeholder="{{ __('messages.email') }}" value="{{ old('email') }}">
                <br> @error('email') <span class="error">{{ $message }}</span> @enderror

                <input type="password" name="password" placeholder="{{ __('messages.password') }}">
                <br> @error('password') <span class="error">{{ $message }}</span> @enderror

                <input type="password" name="password_confirmation" placeholder="{{ __('messages.confirm_password') }}">

                <label for="gender"><i class="fa-solid fa-venus-mars"></i> {{ __('messages.gender') }}</label>
                <select name="gender" id="gender" class="form-control">
                    <option value="">{{ __('messages.select_gender') }}</option>
                    <option value="male">{{ __('messages.male') }}</option>
                    <option value="female">{{ __('messages.female') }}</option>
                </select>
                <br> @error('gender') <span class="error">{{ $message }}</span> @enderror

                <br> <button type="submit" class="btn">{{ __('messages.register') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection