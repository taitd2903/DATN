@extends('layouts.app')

@section('content')
    <h2>Chào mừng bạn đến với trang người dùng</h2>

   
    

   

    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-danger">Đăng Xuất</button>
    </form>
@endsection
