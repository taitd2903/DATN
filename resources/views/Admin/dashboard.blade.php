@extends('layouts.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                {{-- Header có màu gradient giống sidebar --}}
                <div class="card-header text-white" style="background: linear-gradient(to right, #487ACB, #3BA3F5); text-align: center; padding-bottom: 15px;">
                    <h4 class="mb-0">Trang Quản Trị</h4>
                </div>

                <div class="card-body">
                    @if (Auth::user()->role === 'admin')
                        <p class="fs-5">Xin chào <span class="badge bg-primary">Admin</span>, <strong>{{ Auth::user()->name }}</strong>!</p>
                    @elseif (Auth::user()->role === 'staff')
                        <p class="fs-5">Xin chào <span class="badge bg-warning text-dark">Staff</span>, <strong>{{ Auth::user()->name }}</strong>!</p>
                    @endif

                    <div class="d-flex justify-content-between align-items-center my-3">
                        <a href="{{ route('home') }}" class="btn btn-outline-info">
                            <i class="bi bi-box-arrow-right"></i> Chuyển sang giao diện người dùng
                        </a>

                        {{-- Nút Đăng xuất sang phải --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-box-arrow-left"></i> Đăng Xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
