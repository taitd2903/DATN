{{-- @extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h2 class="text-center text-primary fw-bold">Yêu Cầu Hoàn Hàng</h2>
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order_id }}">

                    <div class="mb-3">
                        <label for="reason" class="form-label fw-semibold">Lý do hoàn hàng:</label>
                        <textarea name="reason" id="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">Ảnh minh chứng (tùy chọn):</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        @error('image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Gửi Yêu Cầu</button>
                </form>
            </div>
        </div>
    </div>
@endsection --}}


@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h2 class="text-center text-primary fw-bold">Yêu Cầu Hoàn Hàng</h2>
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order_id }}">

                    <div class="mb-3">
                        <label for="reason" class="form-label fw-semibold">Lý do hoàn hàng:</label>
                        <textarea name="reason" id="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">Ảnh minh chứng (tùy chọn):</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        @error('image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Thêm các trường mới -->
                    <div class="mb-3">
                        <label for="bank_account" class="form-label fw-semibold">Số tài khoản:</label>
                        <input type="text" name="bank_account" id="bank_account" class="form-control" value="{{ old('bank_account') }}" required>
                        @error('bank_account')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="account_holder" class="form-label fw-semibold">Tên người dùng:</label>
                        <input type="text" name="account_holder" id="account_holder" class="form-control" value="{{ old('account_holder') }}" required>
                        @error('account_holder')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bank_name" class="form-label fw-semibold">Tên ngân hàng:</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ old('bank_name') }}" required>
                        @error('bank_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Gửi Yêu Cầu</button>
                </form>
            </div>
        </div>
    </div>
@endsection