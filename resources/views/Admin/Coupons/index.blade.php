{{-- @section('content') --}}
<div>
    <h1>Danh sách Mã Giảm Giá</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.coupons.create') }}">Tạo mã giảm giá mới</a>

    <table>
        <thead>
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
        <tbody>
            @foreach ($coupons as $coupon)
                <tr>
                    <td>{{ $coupon->id }}</td>
                    <td>{{ $coupon->code }}</td>
                    <td>{{ $coupon->description ?? 'Không có mô tả' }}</td>
                    <td>{{ $coupon->discount_type == 1 ? 'Phần trăm' : 'Giá trị cố định' }}</td>
                    <td>{{ number_format($coupon->discount_value) }}</td>
                    <td>{{ $coupon->start_date }}</td>
                    <td>{{ $coupon->end_date }}</td>
                    <td>{{ $coupon->usage_limit }}</td>
                    <td>{{ $coupon->used_count }}</td>
                    <td>{{ $coupon->status == 1 ? 'Hoạt động' : 'Dừng hoạt động' }}</td>

                    {{-- Hiển thị loại giới hạn người dùng --}}
                    <td>
                        @if ($coupon->user_voucher_limit == 1)
                            <span>Tất cả</span>
                        @elseif ($coupon->user_voucher_limit == 2)
                            <span>Người dùng cụ thể</span>
                            <br>
                            @foreach ($coupon->users as $user)
                                <small>{{ $user->name }} ({{ $user->email }})</small><br>
                            @endforeach
                        @elseif ($coupon->user_voucher_limit == 3)
                            <span>Giới tính: {{ $coupon->gender == 'male' ? 'Nam' : 'Nữ' }}</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}">Sửa</a>
                        <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">Xoá</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{-- @endsection --}}
