@extends('layouts.app')

@section('content')
    <h2>Đăng ký</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="text" name="name" placeholder="Tên" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>
        <button type="submit">Đăng ký</button>
    </form>

    <p>Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
@endsection
