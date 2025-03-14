@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/register.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">

<div class="auth-container">
    <div class="auth-box">
        <!-- Phần đăng nhập (bên trái) -->
        <div class="auth-left">
            <h2>Welcome Back!</h2>
            <p>To keep connected with us, please login with your personal info</p>
            <a href="{{ route('login') }}" class="btn-outline">SIGN IN</a>
        </div>

        <!-- Phần đăng ký (bên phải) -->
        <div class="auth-right">
          <h1>Create Account</h1>
            

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="text" name="name" placeholder="Name" value="{{ old('name') }}">
          <br>      @error('name') <span class="error">{{ $message }}</span> @enderror

                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}">
           <br>     @error('email') <span class="error">{{ $message }}</span> @enderror

                <input type="password" name="password" placeholder="Password">
             <br>   @error('password') <span class="error">{{ $message }}</span> @enderror

                <input type="password" name="password_confirmation" placeholder="Confirm Password">

                <label for="gender"><i class="fa-solid fa-venus-mars"></i> Gender</label>
            <select name="gender" id="gender" class="form-control" 1>
                <option value="">-- Chọn giới tính --</option>
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
            </select>

            <br>    <button type="submit" class="btn">SIGN UP</button>
            </form>
        </div>
    </div>
</div>
@endsection