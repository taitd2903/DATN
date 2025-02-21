@extends('layouts.layout')

@section('content')
    <h2>Trang quản trị</h2>
    <p>Xin chào Admin, {{ Auth::user()->name }}!</p>
    <a href="{{ route('users.dashboard') }}" >Chuyển sang User</a>

    <form method="POST" action="{{ route('logout') }}" >
        @csrf
        <button type="submit" >Đăng Xuất</button>
    </form>
@endsection
