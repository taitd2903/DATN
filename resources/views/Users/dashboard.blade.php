@extends('layouts.app')

@section('content')
    <h2>Chào mừng bạn đến với trang người dùng</h2>
    <p>Xin chào, {{ Auth::user()->name }}!</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Đăng xuất</button>
    </form>
@endsection
