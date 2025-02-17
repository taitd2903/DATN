@extends('layouts.app')

@section('content')
    <h2>Đăng nhập</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <p style="color: red;">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
    </form>

    <p>Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a></p>
@endsection
