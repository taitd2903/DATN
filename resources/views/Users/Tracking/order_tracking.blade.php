@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Đơn hàng của bạn</h2>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th>Ngày đặt</th>
                    <th>Phương thức thanh toán</th>
                    <th>Địa chỉ giao hàng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>
                            <ul>
                                @foreach ($order->orderItems as $item)
                                    
                                    <li>{{ $item->product->name }} (x{{ $item->quantity }})</li>
                                    <li>{{ $item->variant->size }} - {{ $item->variant->color }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $order->created_at }}</td>
                        <td class="text-center">{{ $order->payment_method }}</td> 
                        <td>{{ $order->note }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'Chờ xác nhận' => 'primary', // Xanh dương đậm
                                    'Đang giao' => 'info', // Xanh dương nhạt
                                    'Thành công' => 'success', // Xanh lá
                                    'Đã hủy' => 'danger', // Đỏ
                                    
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>
                            @if ($order->status == 'Chờ xác nhận' && $order->payment_status != 'Đã thanh toán')
                                <form action="{{ route('order.cancel', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Hủy</button>
                                </form>
                            @else
                                <span class="text-muted">Không thể hủy</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
