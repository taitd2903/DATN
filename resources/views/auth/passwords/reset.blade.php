@extends('layouts.app')

@section('content')
    <h2>Đặt lại mật khẩu</h2>

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="email" name="email" placeholder="Nhập email" required>
        <input type="password" name="password" placeholder="Nhập mật khẩu mới" required>
        <input type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu" required>
        <button type="submit">Đặt lại mật khẩu</button>
    </form>

    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
@endsection
