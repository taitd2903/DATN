@extends('layouts.layout')

@section('content')
    <div class="container py-4">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light p-2 rounded">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H3.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L3.707 7.5H14.5a.5.5 0 0 1 .5.5"/>
                        </svg>
                        Quay lại
                    </a>
                </li>
            </ol>
        </nav>

        <!-- Order Details Card -->
        <div class="card shadow-sm p-4 mb-4" style="border-radius: 10px;">
            <h2 class="mb-4 text-primary">Chi Tiết Đơn Hàng #{{ $order->id }}</h2>

            <!-- Customer Information -->
            <div class="row">
                <div class="col-md-6">
                    <h4 class="text-dark mb-3">Thông tin khách hàng</h4>
                    <ul class="list-unstyled">
                        <li><strong>Tên:</strong> {{ $order->user->name }}</li>
                        <li><strong>Email:</strong> {{ $order->user->email }}</li>
                        <li><strong>Số điện thoại:</strong> {{ $order->customer_phone ?? 'Chưa cập nhật' }}</li>
                        <li><strong>Địa chỉ:</strong> {{ $order->note ?? 'Chưa có địa chỉ' }}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h4 class="text-dark mb-3">Thông tin đơn hàng</h4>
                    <ul class="list-unstyled">
                        <li><strong>Trạng thái:</strong> 
                            <span class="badge bg-{{ $order->status == 'Thành công' ? 'success' : 'info' }}">{{ $order->status }}</span>
                        </li>
                        <li><strong>Phương thức thanh toán:</strong> {{ strtoupper($order->payment_method) }}</li>
                        <li><strong>Trạng thái thanh toán:</strong> {{ strtoupper($order->payment_status) }}</li>
                        <li><strong>Tổng tiền:</strong> <span class="text-danger fw-bold">{{ number_format($order->total_price, 0, ',', '.') }} VND</span></li>    
                        <li><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</li>
                        <li><strong>Ngày giao hàng:</strong> {{ $order->delivering_at }}</li>
                        <li><strong>Ngày hoàn thành giao hàng:</strong> {{ $order->completed_at }}</li>
                        <li><strong>Thời gian cập nhập trạng thái:</strong> {{ $order->status_updated_at }}</li>
                        <li><strong>Tên người cập nhập đơn hàng:</strong> {{ $order->updatedBy->name ?? 'Không xác định' }}</li>
                        

                    </ul>
                </div>
            </div>

            <!-- Order Items Table -->
            <h4 class="mt-4 mb-3 text-dark">Sản phẩm trong đơn hàng</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Ảnh</th>
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
                                <td>
                                    <img src="{{ $item->product->image_url }}" 
                                         
                                         class="rounded" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->variant ? $item->variant->size : 'Không có' }}</td>
                                <td>{{ $item->variant ? $item->variant->color : 'Không có' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }} VND</td>
                                <td class="fw-bold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VND</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    @section('styles')
        <style>
            .card {
                transition: box-shadow 0.3s ease;
            }
            .card:hover {
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }
            .btn-outline-secondary {
                transition: all 0.3s ease;
            }
            .btn-outline-secondary:hover {
                background-color: #f1f1f1;
            }
            .breadcrumb {
                background-color: #f8f9fa;
            }
            .table img {
                transition: transform 0.2s ease;
            }
            .table img:hover {
                transform: scale(1.1);
            }
        </style>
    @endsection
@endsection