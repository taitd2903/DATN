@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/forgot.css') }}">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<div class="login-container">

    <h2>Quên mật khẩu</h2>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="input-box">
        <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
        <input type="email" name="email" placeholder="Nhập email" required>
        </div>

        <button type="submit" class="btn">Gửi yêu cầu</button>
    </form>

    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <p>Đã nhớ mật khẩu?<a href="{{ route('login') }}">Đăng nhập</a></p>

@endsection
