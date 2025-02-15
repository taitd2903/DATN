@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Chào mừng, ' . Auth::user()->name)

@section('content')
    <p>Email: {{ Auth::user()->email }}</p>
    <h1>đây là giao diện user</h1>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Đăng Xuất</button>
    </form>
@endsection
