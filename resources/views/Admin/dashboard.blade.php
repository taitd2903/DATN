@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Chào mừng, ' . Auth::user()->name)

@section('content')
<h1>đây là giao diện Admin</h1>
    <p>Email: {{ Auth::user()->email }}</p>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Đăng Xuất</button>
    </form>
@endsection
