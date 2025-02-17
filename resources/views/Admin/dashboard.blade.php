@extends('layouts.app')

@section('content')
    <h2>Trang quản trị</h2>
    <p>Xin chào Admin, {{ Auth::user()->name }}!</p>
     <a href="">trang user</a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Đăng xuất</button>
    </form>
@endsection
