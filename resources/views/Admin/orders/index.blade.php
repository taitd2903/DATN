@extends('layouts.layout')

@section('content')
    <h1>Quản lý đơn hàng</h1>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Phương thức thanh toán</th>
                <th>Trạng thái thanh toán</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $index => $order)
            <tr>
                <td>{{ $orders->firstItem() + $index }}</td>  <!-- STT bắt đầu từ 1 -->
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->customer_phone }}</td>
                <td>{{ $order->note }}</td>
                <td>
                    @if ($order->payment_method == 'cod')
                        COD
                    @elseif ($order->payment_method == 'vnpay')
                        VNPAY
                    @else
                        Khác
                    @endif
                </td> <!-- Hiển thị phương thức thanh toán -->
                <td>{{ $order->payment_status }}</td>
                <td>{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                <td>
                    <span class="badge
                        @if($order->status == 'Chờ xác nhận') bg-warning
                        @elseif($order->status == 'Đang giao') bg-primary
                        @elseif($order->status == 'Hoàn thành') bg-success
                        @elseif($order->status == 'Hủy') bg-danger
                        @endif">
                        {{ $order->status }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info">Xem Đơn Hàng</a>


                    @if ($order->status !== 'Hủy')
                        <a href="{{ route('admin.orders.editStatus', $order->id) }}" class="btn btn-info btn-sm">Cập nhật trạng thái</a>
                    @else
                        <button class="btn btn-secondary btn-sm" disabled>Đã huỷ</button>
                    @endif
                    
                    
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $orders->links() }}
@endsection
