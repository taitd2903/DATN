@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Chi Tiết Đơn Hàng #{{ $order->id }}</h2>

        <div class="card p-3 mb-4">
            <h2>Thông tin khách hàng</h2>
            <p>Tên: {{ $order->user->name }}</p>
            <p>Email: {{ $order->user->email }}</p>
            <p>Số điện thoại: {{ $order->customer_phone ?? 'Chưa cập nhật' }}</p>
            <p>Địa chỉ: {{ $order->customer_address ?? 'Chưa có địa chỉ' }}</p>

            <p><strong>Trạng thái:</strong> <span class="badge bg-info">{{ $order->status }}</span></p>
            <p><strong>Phương thức thanh toán:</strong> {{ strtoupper($order->payment_method) }}</p>
            <p><strong>Trạng thái thanh toán:</strong> {{ strtoupper($order->payment_status) }}</p>
            <p><strong>Tổng tiền:</strong> <span class="text-danger">{{ number_format($order->total_price, 0, ',', '.') }}
                    VND</span></p>
            <p><strong>Ghi chú:</strong> {{ $order->note }}</p>
            <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <h4 class="mb-3">Sản phẩm trong đơn hàng</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Size</th>
                    <th>Màu</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->variant ? $item->variant->size : 'Không có' }}</td>
                        <td>{{ $item->variant ? $item->variant->color : 'Không có' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }} VND</td>
                        <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VND</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Quay lại</a>
        @if (($order->payment_method ?? '') == 'vnpay' && ($status ?? '') == 'Thất bại')
        <div class="text-center mt-4">
            <a href="{{ route('cart.index') }}" class="btn btn-primary">Tiếp tục mua hàng</a>
        </div>
    @endif
    



    </div>
    @include('Users.chat')
@endsection
