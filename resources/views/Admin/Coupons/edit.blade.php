@extends('layouts.layout')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Sửa mã giảm giá</h1>

        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" class="p-4 border rounded bg-light">
            @csrf
            @method('PUT')

            {{-- <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề Coupon:</label>
            <input type="text" class="form-control" name="title" id="title" value="{{ old('title', $coupon->title) }}" required>
        </div> --}}
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="code" class="form-label">Mã giảm giá:</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" name="code"
                        id="code" value="{{ old('code', $coupon->code) }}">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Mô tả:</label>
                    <textarea class="form-control" name="description" id="description">{{ old('description', $coupon->description) }}</textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="discount_type" class="form-label">Loại giảm giá:</label>
                    <select class="form-control" name="discount_type" id="discount_type" required>
                        <option value="1" {{ old('discount_type', $coupon->discount_type) == 1 ? 'selected' : '' }}>
                            Phần trăm</option>
                        <option value="2" {{ old('discount_type', $coupon->discount_type) == 2 ? 'selected' : '' }}>Giá
                            trị cố định</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="discount_value" class="form-label">Giá trị giảm giá:</label>
                    <input type="number" class="form-control @error('discount_value') is-invalid @enderror"
                        name="discount_value" id="discount_value"
                        value="{{ old('discount_value', $coupon->discount_value) }}">
                    @error('discount_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Ngày bắt đầu:</label>
                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" name="start_date"
                        id="start_date" value="{{ old('start_date', optional($coupon->start_date)->format('Y-m-d')) }}">
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">Ngày hết hạn:</label>
                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" name="end_date"
                        id="end_date" value="{{ old('end_date', optional($coupon->end_date)->format('Y-m-d')) }}">
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="usage_limit" class="form-label">Số lượng mã:</label>
                    <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" name="usage_limit"
                        id="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}">
                    @error('usage_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="usage_per_user" class="form-label">Số lần sử dụng mỗi người:</label>
                    <input type="number" class="form-control @error('usage_per_user') is-invalid @enderror"
                        name="usage_per_user" id="usage_per_user"
                        value="{{ old('usage_per_user', $coupon->usage_per_user) }}">
                    @error('usage_per_user')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="user_voucher_limit" class="form-label">Giới hạn người dùng:</label>
                    <select class="form-control" name="user_voucher_limit" id="user_voucher_limit"
                        onchange="toggleUserSelection()">
                        <option value="1"
                            {{ old('user_voucher_limit', $coupon->user_voucher_limit) == 1 ? 'selected' : '' }}>Tất cả
                        </option>
                        <option value="2"
                            {{ old('user_voucher_limit', $coupon->user_voucher_limit) == 2 ? 'selected' : '' }}>Người cụ
                            thể</option>
                        <option value="3"
                            {{ old('user_voucher_limit', $coupon->user_voucher_limit) == 3 ? 'selected' : '' }}>Giới tính
                        </option>
                    </select>
                </div>

                <div id="userSelection" class="col-md-6 mb-3" style="display: none;">
                    <label for="selected_users" class="form-label">Chọn người dùng:</label>
                    <select class="form-control" name="selected_users[]" id="selected_users" multiple>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ in_array($user->id, old('selected_users', $coupon->users->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="genderSelection" class="col-md-6 mb-3" style="display: none;">
                    <label for="gender" class="form-label">Giới tính áp dụng:</label>
                    <select class="form-control" name="gender" id="gender">
                        <option value="male" {{ old('gender', $coupon->gender) == 'male' ? 'selected' : '' }}>Nam
                        </option>
                        <option value="female" {{ old('gender', $coupon->gender) == 'female' ? 'selected' : '' }}>Nữ
                        </option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Trạng thái:</label>
                    <select class="form-control" name="status" id="status">
                        <option value="1" {{ old('status', $coupon->status) == 1 ? 'selected' : '' }}>Hoạt động
                        </option>
                        <option value="2" {{ old('status', $coupon->status) == 2 ? 'selected' : '' }}>Dừng hoạt động
                        </option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>

        {{-- @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}
    </div>

    <script>
        function toggleUserSelection() {
            let userLimit = document.getElementById("user_voucher_limit").value;
            document.getElementById("userSelection").style.display = (userLimit == 2) ? "block" : "none";
            document.getElementById("genderSelection").style.display = (userLimit == 3) ? "block" : "none";
        }

        window.onload = toggleUserSelection;
    </script>
@endsection
