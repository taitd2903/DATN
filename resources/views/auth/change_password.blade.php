@extends('layouts.app')

@section('content')

<br>
<h2 class="doimatkhau">Đổi mật khẩu</h2>

@if (session('success'))
    <div style="color: green">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div style="color: red">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <ul style="color: red">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('user.change-password') }}" class="doimk">
     @csrf
 
     <label for="current_password">Mật khẩu hiện tại:</label>
     <input type="password" name="current_password" required>
 
     <label for="new_password">Mật khẩu mới:</label>
     <input type="password" name="new_password" required>
 
     <label for="new_password_confirmation">Xác nhận mật khẩu mới:</label>
     <input type="password" name="new_password_confirmation" required>
 
     <button type="submit">Đổi mật khẩu</button>
 </form>
 
@endsection