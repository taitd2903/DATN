@extends('layouts.layout')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Tạo mã giảm giá</h1>

        {{-- @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}

        <form action="{{ route('admin.coupons.store') }}" method="POST" class="p-4 border rounded bg-light">
            @csrf

            <div class="row">
                {{-- <div class="col-md-6 mb-3">
                <label for="title" class="form-label">Tiêu đề Coupon:</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control">
            </div> --}}

                <div class="col-md-12 mb-3">
                    <label for="code" class="form-label">Mã giảm giá:</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}"
                        class="form-control @error('code') is-invalid @enderror">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Mô tả:</label>
                    <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="discount_type" class="form-label">Loại giảm giá:</label>
                    <select name="discount_type" id="discount_type" class="form-control">
                        <option value="1" {{ old('discount_type') == 1 ? 'selected' : '' }}>Phần trăm</option>
                        <option value="2" {{ old('discount_type') == 2 ? 'selected' : '' }}>Giá trị cố định</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="discount_value" class="form-label">Giá trị giảm giá:</label>
                    <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value') }}"
                        class="form-control @error('discount_value') is-invalid @enderror">
                    @error('discount_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Ngày bắt đầu:</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                        class="form-control @error('start_date') is-invalid @enderror">
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">Ngày hết hạn:</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                        class="form-control @error('end_date') is-invalid @enderror">
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="usage_limit" class="form-label">Số lượng mã:</label>
                    <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}"
                        class="form-control @error('usage_limit') is-invalid @enderror">
                    @error('usage_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="usage_per_user" class="form-label">Số lần sử dụng mỗi người:</label>
                    <input type="number" name="usage_per_user" id="usage_per_user" value="{{ old('usage_per_user') }}"
                        class="form-control @error('usage_per_user') is-invalid @enderror">
                    @error('usage_per_user')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="user_voucher_limit" class="form-label">Giới hạn người dùng:</label>
                    <select name="user_voucher_limit" id="user_voucher_limit" class="form-control"
                        onchange="toggleUserSelection()">
                        <option value="1" {{ old('user_voucher_limit') == 1 ? 'selected' : '' }}>Tất cả</option>
                        <option value="2" {{ old('user_voucher_limit') == 2 ? 'selected' : '' }}>Người cụ thể</option>
                        <option value="3" {{ old('user_voucher_limit') == 3 ? 'selected' : '' }}>Giới tính</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3" id="userSelection" style="display: none;">
                    <label for="selected_users" class="form-label">Chọn người dùng:</label>
                    <select name="selected_users[]" id="selected_users" class="form-control" multiple>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3" id="genderSelection" style="display: none;">
                    <label for="gender" class="form-label">Giới tính áp dụng:</label>
                    <select name="gender" id="gender" class="form-control">
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="max_discount_amount" class="form-label">Mức giảm tối đa (nếu có):</label>
                    <input type="number" name="max_discount_amount" id="max_discount_amount"
                        value="{{ old('max_discount_amount') }}"
                        class="form-control @error('max_discount_amount') is-invalid @enderror">
                    @error('max_discount_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Trạng thái:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Hoạt động</option>
                        <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>Dừng hoạt động</option>
                    </select>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Hủy
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleUserSelection() {
            let userLimit = document.getElementById("user_voucher_limit").value;
            document.getElementById("userSelection").style.display = (userLimit == 2) ? "block" : "none";
            document.getElementById("genderSelection").style.display = (userLimit == 3) ? "block" : "none";
        }
    </script>
@endsection
