@extends('layouts.app')

@section('title', 'Đăng Ký')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="text-center">Đăng Ký</h2>
        
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Tên</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Quyền</label>
                <select name="role" class="form-control">
                    <option value="user" selected>User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Đăng Ký</button>
        </form>
        
        <p class="mt-3 text-center">
            Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
        </p>
    </div>
</div>
@endsection
