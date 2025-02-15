@extends('layouts.app')

@section('title', 'Đăng Nhập')
@section('heading', 'Đăng Nhập')

@section('content')
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng Nhập</button>
    </form>
    <a href="{{ route('register') }}">Chưa có tài khoản? Đăng ký</a>
@endsection
