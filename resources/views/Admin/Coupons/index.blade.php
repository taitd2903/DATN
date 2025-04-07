@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center">Danh sách Mã Giảm Giá</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tạo mã giảm giá mới
        </a>
    </div>
                <!-- Lọc các kiểu -->
    <div class="mb-3">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-3 align-items-center">
            <div class="col-lg-2">
                <label for="code" class="col-form-label">Mã giảm giá:</label>
            </div>
            <div class="col-lg-2">
                <input type="text" name="code" id="code" class="form-control" value="{{ request('code') }}" placeholder="Nhập mã...">
            </div>
            <div class="col-lg-2">
                <label for="status" class="col-form-label">Trạng thái:</label>
            </div>
            <div class="col-lg-2">
                <select name="status" id="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Dừng hoạt động</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label for="discount_type" class="col-form-label">Loại giảm giá:</label>
            </div>
            <div class="col-lg-2">
                <select name="discount_type" id="discount_type" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('discount_type') == '1' ? 'selected' : '' }}>Phần trăm</option>
                    <option value="2" {{ request('discount_type') == '2' ? 'selected' : '' }}>Giá trị cố định</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label for="start_date" class="col-form-label">Từ ngày:</label>
            </div>
            <div class="col-lg-2">
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-lg-2">
                <label for="end_date" class="col-form-label">Đến ngày:</label>
            </div>
            <div class="col-lg-2">
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-lg-2">
                <label for="usage_status" class="col-form-label">Giới hạn sử dụng:</label>
            </div>
            <div class="col-lg-2">
                <select name="usage_status" id="usage_status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="available" {{ request('usage_status') == 'available' ? 'selected' : '' }}>Còn lượt</option>
                    <option value="exhausted" {{ request('usage_status') == 'exhausted' ? 'selected' : '' }}>Hết lượt</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label for="user_voucher_limit" class="col-form-label">Loại giới hạn:</label>
            </div>
            <div class="col-lg-2">
                <select name="user_voucher_limit" id="user_voucher_limit" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('user_voucher_limit') == '1' ? 'selected' : '' }}>Tất cả người dùng</option>
                    <option value="2" {{ request('user_voucher_limit') == '2' ? 'selected' : '' }}>Người dùng cụ thể</option>
                    <option value="3" {{ request('user_voucher_limit') == '3' ? 'selected' : '' }}>Giới tính</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label for="discount_target" class="col-form-label">Áp dụng giảm giá cho:</label>
            </div>
            <div class="col-lg-2">
                <select name="discount_target" id="discount_target" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="order_total" {{ request('discount_target') == 'order_total' ? 'selected' : '' }}>Tổng đơn hàng</option>
                    <option value="shipping_fee" {{ request('discount_target') == 'shipping_fee' ? 'selected' : '' }}>Phí vận chuyển</option>
                </select>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">Lọc</button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Xóa bộ lọc</a>
                </div>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Mã Giảm Giá</th>
                    <th>Mô Tả</th>
                    <th>Loại Giảm Giá</th>
                    <th>Giá Trị Giảm</th>
                    <th>Ngày Bắt Đầu</th>
                    <th>Ngày Kết Thúc</th>
                    <th>Giới Hạn Sử Dụng</th>
                    <th>Đã Sử Dụng</th>
                    <th>Trạng Thái</th>
                    <th>Loại Giới Hạn</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @if($coupons->isEmpty())
                <tr><td colspan="12">Không tìm thấy mã giảm giá nào phù hợp.</td></tr>
                @else
                    @foreach ($coupons as $coupon)
                        <tr>
                            <td>{{ $coupon->id }}</td>
                            <td class="fw-bold">{{ $coupon->code }}</td>
                            <td>{{ $coupon->description ?? 'Không có mô tả' }}</td>
                            <td>{{ $coupon->discount_type == 1 ? 'Phần trăm' : 'Giá trị cố định' }}</td>
                            <td>{{ number_format($coupon->discount_value) }}</td>
                            <td>{{ $coupon->start_date }}</td>
                            <td>{{ $coupon->end_date }}</td>
                            <td>{{ $coupon->usage_limit }}</td>
                            <td>{{ $coupon->used_count }}</td>
                            <td>
                                <span class="badge {{ $coupon->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $coupon->status == 1 ? 'Hoạt động' : 'Dừng hoạt động' }}
                                </span>
                            </td>

                            <td>
                                @if ($coupon->user_voucher_limit == 1)
                                    <span>Tất cả</span>
                                @elseif ($coupon->user_voucher_limit == 2)
                                    <span>Người dùng cụ thể:</span><br>
                                    @foreach ($coupon->users as $user)
                                        <small>{{ $user->name }} ({{ $user->email }})</small><br>
                                    @endforeach
                                @elseif ($coupon->user_voucher_limit == 3)
                                    <span>Giới tính: {{ $coupon->gender === 'male' ? 'Nam' : ($coupon->gender === 'female' ? 'Nữ' : 'Unisex') }}</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                        <i class="bi bi-trash"></i> Xoá
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $coupons->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
