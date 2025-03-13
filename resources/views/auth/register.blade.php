@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
<meta name="viewport" content="width=device-width, initial-scale=1">

<div class="login-container">

    <h2>Đăng ký</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="input-box">
        <i class="fa fa-user"></i>
        <input type="text" name="name" placeholder="Tên" value="{{ old('name') }}">

        @if ($errors->has('name'))
        @foreach ($errors->get('name') as $error)
            <span class="text-danger">{{ $error }}</span><br>
        @endforeach
    @endif
        </div>


          <div class="input-box">
         <i class="fa fa-envelope"></i>
        <input type="email" name="email" placeholder="Email" value="{{ old('email')}}">
        @if ($errors->has('email'))
        @foreach ($errors->get('email') as $error)
            <span class="text-danger">{{ $error }}</span><br>
        @endforeach
    @endif
        </div>

        <div class="input-box">
        <i class="fa fa-envelope"></i>
        <input type="password" name="password" placeholder="Mật khẩu" {{ old('password')}}>
        @if ($errors->has('password'))
         @foreach ($errors->get('password') as $error)
        <span class="text-danger">{{ $error }}</span><br>
          @endforeach
        @endif
        </div>

            <div class="input-box">
                <i class="fa fa-envelope"></i>
        <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" {{ old('password_confirmation')}}>
            </div>

        <div class="input-box">
                <label for="gender"><i class="fa-solid fa-venus-mars"></i> Gender</label>
            <select name="gender" id="gender" class="form-control" 1>
                <option value="">-- Chọn giới tính --</option>
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
            </select>
        </div>

        @error('gender')
        <p style="color: red;">{{ $message }}</p>
        @enderror
        
        <button type="submit" class="btn">Đăng ký</button>
    </form>

    <p>Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
@endsection
