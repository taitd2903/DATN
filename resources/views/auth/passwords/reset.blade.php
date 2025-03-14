@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<div class="container">
    <div class="reset-wrapper">
        <div class="reset-box">
            <h2>Reset Password</h2>
            <p>Enter your new password to reset your account</p>

            @if (session('status'))
                <p class="success-message">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-box">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="input-box">
                    <label for="password"><i class="fa-solid fa-lock"></i> New Password</label>
                    <input type="password" name="password" placeholder="Enter new password" required>
                </div>

                <div class="input-box">
                    <label for="password_confirmation"><i class="fa-solid fa-lock"></i> Confirm Password</label>
                    <input type="password" name="password_confirmation" placeholder="Confirm password" required>
                </div>

                <button type="submit" class="btn">Reset Password</button>
            </form>

            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <p class="error">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <p>Remembered your password? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </div>
</div>

@endsection