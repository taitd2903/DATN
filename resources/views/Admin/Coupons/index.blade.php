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
    <div class="mb-3">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="status" class="col-form-label">Lọc theo trạng thái:</label>
            </div>
            <div class="col-auto">
                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Dừng hoạt động</option>
                </select>
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
            </tbody>
        </table>
        <div class="d-flex justify-content-center mt-4">
            {{ $coupons->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
