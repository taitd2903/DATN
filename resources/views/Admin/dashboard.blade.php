@extends('layouts.layout')

@section('content')
    <h2>Trang quản trị</h2>
    @if (Auth::user()->role === 'admin')
    <p>Xin chào <span class="text-primary">Admin</span>, {{ Auth::user()->name }}!</p>
@elseif (Auth::user()->role === 'staff')
    <p>Xin chào <span class="text-primary">Staff</span>, {{ Auth::user()->name }}!</p>
@endif
    <a href="{{ route('users.dashboard') }}" >Chuyển sang User</a>

    <form method="POST" action="{{ route('logout') }}" >
        @csrf
        <button type="submit" >Đăng Xuất</button>
    </form>
@endsection
