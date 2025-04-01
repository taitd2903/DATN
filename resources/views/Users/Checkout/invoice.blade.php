@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <!-- Order Header -->
                <div class="card shadow-lg border-0 mb-5" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="fw-bold text-dark">
                                Đơn Hàng #{{ $order->id }}
                                <span class="badge bg-{{ $order->status == 'Thành công' ? 'success' : 'warning' }} ms-2">
                                    {{ $order->status }}
                                </span>
                            </h2>
                            <span class="text-muted">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        <!-- Customer & Order Info -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="bg-white p-4 rounded-3 shadow-sm">
                                    <h5 class="text-primary fw-semibold mb-3">Khách Hàng</h5>
                                    <div class="d-flex flex-column gap-2">
                                        <div><i class="fas fa-user me-2 text-muted"></i> {{ $order->user->name }}</div>
                                        <div><i class="fas fa-envelope me-2 text-muted"></i> {{ $order->user->email }}</div>
                                        <div><i class="fas fa-phone me-2 text-muted"></i> {{ $order->customer_phone ?? 'Chưa cập nhật' }}</div>
                                        <div><i class="fas fa-map-marker-alt me-2 text-muted"></i> {{ $order->note ?? 'Chưa có địa chỉ' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white p-4 rounded-3 shadow-sm">
                                    <h5 class="text-primary fw-semibold mb-3">Thông Tin Thanh Toán</h5>
                                    <div class="d-flex flex-column gap-2">
                                        <div><strong>Phương thức:</strong> {{ strtoupper($order->payment_method) }}</div>
                                        <div><strong>Trạng thái:</strong> {{ strtoupper($order->payment_status) }}</div>
                                        <div><strong>Tổng tiền:</strong> 
                                            <span class="text-success fw-bold">{{ number_format($order->total_price, 0, ',', '.') }} VND</span>
                                        </div>
                                        
                                        <div><strong>Thời gian đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
                                        <div><strong>Thời gian thanh toán:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card shadow-lg border-0" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-dark mb-4">Chi Tiết Sản Phẩm</h4>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th class="py-3 fw-semibold">Ảnh</th>
                                        <th class="py-3 fw-semibold">Sản phẩm</th>
                                        <th class="py-3 fw-semibold">Size</th>
                                        <th class="py-3 fw-semibold">Màu</th>
                                        <th class="py-3 fw-semibold">Số lượng</th>
                                        <th class="py-3 fw-semibold">Giá</th>
                                        <th class="py-3 fw-semibold">Tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderItems as $item)
                                        <tr class="border-bottom">
                                            <td class="py-3">
                                                <img src="{{ $item->product->image_url }}" 
                                                     
                                                     class="rounded me-3" 
                                                     style="width: 70px; height: 70px; object-fit: cover;">
                                            </td>
                                            <td class="py-3">{{ $item->product->name }}</td>
                                            <td class="py-3">{{ $item->variant ? $item->variant->size : 'Không có' }}</td>
                                            <td class="py-3">{{ $item->variant ? $item->variant->color : 'Không có' }}</td>
                                            <td class="py-3">{{ $item->quantity }}</td>
                                            <td class="py-3">{{ number_format($item->price, 0, ',', '.') }} VND</td>
                                            <td class="py-3 fw-semibold text-dark">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VND</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-5 d-flex justify-content-between">
                    <a href="{{ route('home') }}" class="btn btn-outline-dark btn-lg px-4">
                        <i class="fas fa-arrow-left me-2"></i> Quay lại
                    </a>
                    @if (($order->payment_method ?? '') == 'vnpay' && ($status ?? '') == 'Thất bại')
                        <a href="{{ route('cart.index') }}" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-shopping-cart me-2"></i> Tiếp tục mua hàng
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('Users.chat')
@endsection

@section('styles')
    <style>
        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .btn {
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .table img {
            transition: transform 0.2s ease;
        }
        .table img:hover {
            transform: scale(1.1);
        }
    </style>
@endsection