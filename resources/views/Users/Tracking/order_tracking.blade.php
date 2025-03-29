@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 py-5" style="max-width: 100%;">
        <h2 class="mb-5 text-primary text-center fw-bold" style="font-size: 2.5rem; letter-spacing: 1px;">
            Lịch Sử Đơn Hàng
        </h2>

        @if (session('success'))
            <div class="alert alert-success text-center rounded-3 py-3 mx-auto" style="max-width: 800px;">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger text-center rounded-3 py-3 mx-auto" style="max-width: 800px;">{{ session('error') }}</div>
        @endif

        <div class="timeline position-relative mx-auto" style="max-width: 1200px;">
            @forelse ($orders as $order)
                <div class="timeline-item mb-5 animate__animated animate__fadeIn">
                    <div class="timeline-dot bg-{{ $order->status == 'Hoàn thành' ? 'success' : ($order->status == 'Đang giao' ? 'info' : ($order->status == 'Đã hủy' ? 'danger' : 'primary')) }}"></div>
                    <div class="timeline-content p-4 border rounded-3 shadow-sm w-100 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-primary fw-bold mb-0">Đơn hàng #{{ $order->id }}</h5>
                            <span class="badge bg-{{ $order->status == 'Hoàn thành' ? 'success' : ($order->status == 'Đang giao' ? 'info' : ($order->status == 'Đã hủy' ? 'danger' : 'primary')) }} fs-6 px-3 py-2">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div class="row g-3 text-muted">
                            <div class="col-md-6">
                                <p class="mb-2"><i class="fas fa-calendar-alt me-2"></i> Ngày đặt: <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong></p>
                                <p class="mb-2"><i class="fas fa-wallet me-2"></i> Thanh toán: <span class="badge bg-info px-2 py-1">{{ $order->payment_method }}</span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><i class="fas fa-money-check-alt me-2"></i> Trạng thái: 
                                    <span class="badge {{ $order->payment_status == 'Đã thanh toán' ? 'bg-success' : 'bg-warning' }} px-2 py-1">
                                        {{ $order->payment_status }}
                                    </span>
                                </p>
                                <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Địa chỉ: {{ $order->note }}</p>
                            </div>
                        </div>

                        <h6 class="mt-4 text-secondary fw-semibold">Sản phẩm trong đơn hàng:</h6>
                        <div class="order-items mt-3">
                            @foreach ($order->orderItems as $item)
                                <div class="card mb-2 border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center p-3">
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="rounded me-3" style="width: 70px; height: 70px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <strong>{{ $item->product->name }}</strong> (x{{ $item->quantity }})<br>
                                            <span class="badge bg-secondary mt-1 px-2 py-1">{{ $item->variant->size }} - {{ $item->variant->color }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="{{ route('checkout.invoice', $order->id) }}" class="btn btn-primary btn-sm px-4 py-2">
                                <i class="fas fa-file-invoice me-2"></i> Xem chi tiết
                            </a>
                            @if ($order->status == 'Chờ xác nhận' && $order->payment_status != 'Đã thanh toán')
                                @if ($order->payment_method == 'cod')
                                    <form action="{{ route('order.cancel', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm px-4 py-2">
                                            <i class="fas fa-times me-2"></i> Hủy đơn
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">Không thể hủy</span>
                                @endif
                            @else
                                <span class="text-muted small">Không thể hủy</span>
                            @endif
                            @if ($order->payment_status == 'Chưa thanh toán')
                                <a href="{{ route('checkout.continue', $order->id) }}" class="btn btn-success btn-sm px-4 py-2">
                                    <i class="fas fa-credit-card me-2"></i> Tiếp tục thanh toán
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                    <p class="text-muted fs-4">Bạn chưa có đơn hàng nào!</p>
                    <a href="{{ route('home') }}" class="btn btn-primary px-5 py-3">Mua sắm ngay</a>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        body {
            background-color: #f5f6fa;
        }
        .container-fluid {
            width: 100%;
            padding-left: 15px;
            padding-right: 15px;
        }
        .timeline {
            position: relative;
            padding-left: 60px;
            border-left: 5px solid #007bff;
        }
        .timeline-item {
            position: relative;
        }
        .timeline-dot {
            position: absolute;
            left: -16px;
            top: 15px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 4px solid #fff;
            transition: transform 0.3s ease;
        }
        .timeline-item:hover .timeline-dot {
            transform: scale(1.2);
        }
        .timeline-content {
            background: #fff;
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        .timeline-content:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .order-items .card {
            transition: all 0.2s ease;
        }
        .order-items .card:hover {
            background: #f8f9fa;
        }
        .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>

    <!-- Thêm Animate.css từ CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
@endsection