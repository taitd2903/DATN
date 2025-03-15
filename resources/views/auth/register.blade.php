@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/register.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">

<div class="auth-container">
    <div class="auth-box">
        <!-- Phần đăng nhập (bên trái) -->
        <div class="auth-left">
            <h2>Chào mừng bạn yêu!</h2>
            <p>Nếu bạn đã có tài khoản,hãy tiếp tục đăng nhập và tận hưởng không gian mua sắm với OceanSport</p>
            <a href="{{ route('login') }}" class="btn-outline">ĐĂNG NHẬP</a>
        </div>

        <!-- Phần đăng ký (bên phải) -->
        <div class="auth-right">
          <h1>Đăng ký tài khoản</h1>
            

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="text" name="name" placeholder="Name" value="{{ old('name') }}">
          <br>      @error('name') <span class="error">{{ $message }}</span> @enderror

                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
           <br>     @error('email') <span class="error">{{ $message }}</span> @enderror

                <input type="password" name="password" placeholder="Password">
             <br>   @error('password') <span class="error">{{ $message }}</span> @enderror

                <input type="password" name="password_confirmation" placeholder="Confirm Password">

                <label for="gender"><i class="fa-solid fa-venus-mars"></i> Giới tính</label>
            <select name="gender" id="gender" class="form-control" 1>
                <option value="">-- Chọn giới tính --</option>
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
            </select>

            <br>    <button type="submit" class="btn">ĐĂNG KÝ</button>
            </form>
        </div>
    </div>
</div>
@endsection