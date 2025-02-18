@extends('layouts.app')

@section('content')
    <h2>Chào mừng bạn đến với trang người dùng</h2>
    <p>Xin chào, {{ Auth::user()->name }}!</p>

    @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="btn btn-warning">Chuyển sang Admin</a>
    @endif

    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-danger">Đăng Xuất</button>
    </form>
@endsection
