@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/forgot.css') }}">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<div class="container">
    <div class="forgot-wrapper">
        <div class="forgot-box">
            <h2>Forgot Password?</h2>
            <p>Enter your email to receive a password reset link</p>

            @if (session('status'))
                <p class="success-message">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="input-box">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>

                <button type="submit" class="btn">Send Request</button>
            </form>

            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <p class="error">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <p>Remember your password? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </div>
</div>

@endsection