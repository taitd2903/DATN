@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<div class="container">
    <div class="profile-wrapper">
        <div class="profile-box">
            <h2>Chỉnh sửa tài khoản</h2>

            @if(session('success'))
                <div class="alert success-message">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('user.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="input-box">
                    <label for="name"><i class="fa-solid fa-user"></i> Tên</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="input-box">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="input-box">
                    <label for="password"><i class="fa-solid fa-lock"></i> Mật khẩu mới (để trống nếu không đổi)</label>
                    <input type="password" id="password" name="password">
                </div>

                <div class="input-box">
                    <label for="password_confirmation"><i class="fa-solid fa-lock"></i> Xác nhận mật khẩu</label>
                    <input type="password" id="password_confirmation" name="password_confirmation">
                </div>

                <button type="submit" class="btn">Cập nhật</button>
            </form>

            @if ($errors->any())
                <div class="error-box">
                    @foreach ($errors->all() as $error)
                        <p class="error">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection