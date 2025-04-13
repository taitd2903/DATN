@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5" style="max-width: 100%;">
    <h2 class="mb-5 text-primary text-center fw-bold" style="font-size: 2.5rem; letter-spacing: 1px;">
        Lịch Sử Đơn Hàng
    </h2>

    <!-- Form lọc -->
    <div class="filter-section mb-5 bg-light p-4 rounded-3 shadow-sm" style="max-width: 1200px; margin: auto;">
        <form id="filterForm" class="row g-3">
            <!-- Trạng thái đơn hàng -->
            <div class="col-md-3">
                <label for="status" class="form-label fw-bold">Trạng thái đơn hàng</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="Chờ xác nhận">Chờ xác nhận</option>
                    <option value="Đang giao">Đang giao</option>
                    <option value="Hoàn thành">Hoàn thành</option>
                    <option value="Hủy">Hủy</option>
                </select>
            </div>

            <!-- Khoảng thời gian -->
            <div class="col-md-3">
                <label for="time_range" class="form-label fw-bold">Khoảng thời gian</label>
                <select name="time_range" id="time_range" class="form-select" onchange="toggleCustomDate(this)">
                    <option value="">Tất cả</option>
                    <option value="7_days">7 ngày qua</option>
                    <option value="this_month">Tháng này</option>
                    <option value="3_months">3 tháng gần nhất</option>
                    <option value="custom">Khoảng ngày tùy chọn</option>
                </select>
            </div>

            <!-- Khoảng ngày tùy chọn -->
            <div class="col-md-3 custom-date d-none">
                <label for="start_date" class="form-label fw-bold">Từ ngày</label>
                <input type="date" name="start_date" id="start_date" class="form-control">
            </div>
            <div class="col-md-3 custom-date d-none">
                <label for="end_date" class="form-label fw-bold">Đến ngày</label>
                <input type="date" name="end_date" id="end_date" class="form-control">
            </div>

            <!-- Tên sản phẩm -->
            <div class="col-md-3">
                <label for="product_name" class="form-label fw-bold">Tên sản phẩm</label>
                <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Nhập tên sản phẩm">
            </div>

            <!-- Nút lọc -->
            <div class="col-md-12 text-end">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-filter me-2"></i> Lọc
                </button>
                <button type="button" onclick="resetFilters()" class="btn btn-secondary px-4 py-2">
                    <i class="fas fa-sync-alt me-2"></i> Đặt lại
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách đơn hàng -->
    <div class="timeline position-relative mx-auto" id="orderTimeline" style="max-width: 1200px;">
        @forelse ($orders as $order)
            <div class="timeline-item mb-5 animate__animated animate__fadeIn" data-order='@json($order)'>
                <div class="timeline-dot bg-{{ $order->status == 'Hoàn thành' ? 'success' : ($order->status == 'Đang giao' ? 'info' : ($order->status == 'Hủy' ? 'danger' : 'primary')) }}"></div>
                <div class="timeline-content p-4 border rounded-3 shadow-sm w-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-primary fw-bold mb-0">Đơn hàng #{{ $order->id }}</h5>
                        <span class="badge bg-{{ $order->status == 'Hoàn thành' ? 'success' : ($order->status == 'Đang giao' ? 'info' : ($order->status == 'Hủy' ? 'danger' : 'primary')) }} fs-6 px-3 py-2">
                            {{ $order->status }}
                        </span>
                    </div>
                    <div class="row g-3 text-muted">
                        <div class="col-md-6">
                            <p class="mb-2"><i class="fas fa-calendar-alt me-2"></i> Ngày đặt hàng:
                                <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                            </p>
                            <p class="mb-2"><i class="fas fa-shipping-fast me-2"></i> Giao hàng: <span class="badge bg-info px-2 py-1">{{ $order->delivering_at }}</span></p>
                            <p class="mb-2"><i class="fas fa-check-circle me-2"></i> Hoàn thành: <span class="badge bg-info px-2 py-1">{{ $order->completed_at }}</span></p>
                            <p class="mb-2"><i class="fas fa-wallet me-2"></i> Thanh toán: <span class="badge bg-info px-2 py-1">{{ $order->payment_method }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item py-2 d-flex align-items-center">
                                    <strong><i class="fas fa-money-check-alt me-2"></i> Trạng thái:</strong>
                                    <span class="badge {{ $order->payment_status == 'Đã thanh toán' ? 'bg-success' : 'bg-warning' }} px-2 py-1">
                                        {{ $order->payment_status }}
                                    </span>
                                </li>
                                <li class="list-group-item py-2 d-flex align-items-center">
                                    <strong>Mã giảm giá:</strong>
                                    <span>{{ $order->coupon_code ?? 'Không có mã nào áp dụng' }}</span>
                                </li>
                                @if ($order->coupon_code && $order->discount_amount > 0)
                                    <li class="list-group-item py-2 d-flex align-items-center">
                                        <strong>Số tiền giảm:</strong>
                                        <span>{{ number_format($order->discount_amount, 0, ',', '.') }} VNĐ</span>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <h6 class="mt-4 text-secondary fw-semibold">Sản phẩm trong đơn hàng:</h6>
                        <div class="order-items mt-3">
                            @foreach ($order->orderItems as $item)
                                <div class="card mb-2 border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center p-3">
                                        <img src="{{ asset('storage/' . $item->variant->image) }}" alt="{{ $item->product->name }}" class="rounded me-3" style="width: 70px; height: 70px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <strong>{{ $item->product->name }}</strong> (x{{ $item->quantity }})<br>
                                            <span class="badge bg-secondary mt-1 px-2 py-1">{{ $item->size }} - {{ $item->color }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="{{ route('checkout.invoice', $order->id) }}" class="btn btn-primary btn-sm px-4 py-2">
                                <i class="fas fa-file-invoice me-2"></i> Xem chi tiết
                            </a>
                            @if ($order->status == 'Hoàn thành' && now()->diffInDays($order->completed_at) <= 7)
                                @if ($order->return_request_status == '')
                                    <a href="{{ route('returns.create', $order->id) }}" class="btn btn-warning btn-sm px-4 py-2">
                                        <i class="fas fa-undo-alt me-2"></i> Yêu cầu hoàn hàng
                                    </a>
                                @else
                                    <span class="text-muted">Bạn đã yêu cầu hoàn hàng cho đơn này.</span>
                                @endif
                            @endif
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
                            @if ($order->payment_status == 'Chưa thanh toán' && $order->payment_method != 'cod')
                                <a href="{{ route('checkout.continue', $order->id) }}" class="btn btn-success btn-sm px-4 py-2">
                                    <i class="fas fa-credit-card me-2"></i> Tiếp tục thanh toán
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5" id="noOrders">
                <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                <p class="text-muted fs-4">Bạn chưa có đơn hàng nào!</p>
                <a href="{{ route('home') }}" class="btn btn-primary px-5 py-3">Mua sắm ngay</a>
            </div>
        @endforelse
    </div>
</div>

<!-- JavaScript xử lý lọc -->
<script>
    // Lấy dữ liệu đơn hàng từ Blade
    const orders = @json($orders);

    // Hiển thị/ẩn khoảng ngày tùy chọn
    function toggleCustomDate(select) {
        const customDateFields = document.querySelectorAll('.custom-date');
        customDateFields.forEach(field => {
            field.classList.toggle('d-none', select.value !== 'custom');
        });
    }

    // Hàm định dạng ngày
    function formatDate(date) {
        return date ? new Date(date).toLocaleString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }) : '';
    }

    // Hàm lọc đơn hàng
    function filterOrders() {
        const status = document.getElementById('status').value;
        const timeRange = document.getElementById('time_range').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const productName = document.getElementById('product_name').value.toLowerCase();

        const now = new Date();
        let filteredOrders = orders;

        // Lọc theo trạng thái
        if (status) {
            filteredOrders = filteredOrders.filter(order => order.status === status);
        }

        // Lọc theo khoảng thời gian
        if (timeRange) {
            filteredOrders = filteredOrders.filter(order => {
                const orderDate = new Date(order.created_at);
                switch (timeRange) {
                    case '7_days':
                        return orderDate >= new Date(now.setDate(now.getDate() - 7));
                    case 'this_month':
                        return orderDate.getMonth() === now.getMonth() && orderDate.getFullYear() === now.getFullYear();
                    case '3_months':
                        return orderDate >= new Date(now.setMonth(now.getMonth() - 3));
                    case 'custom':
                        if (startDate && endDate) {
                            const start = new Date(startDate);
                            const end = new Date(endDate);
                            return orderDate >= start && orderDate <= end;
                        }
                        return true;
                    default:
                        return true;
                }
            });
        }

        // Lọc theo tên sản phẩm
        if (productName) {
            filteredOrders = filteredOrders.filter(order =>
                order.order_items.some(item => item.product.name.toLowerCase().includes(productName))
            );
        }

        // Cập nhật giao diện
        renderOrders(filteredOrders);
    }

    // Hàm hiển thị danh sách đơn hàng
    function renderOrders(orders) {
        const timeline = document.getElementById('orderTimeline');
        const noOrders = document.getElementById('noOrders');
        timeline.innerHTML = '';

        if (orders.length === 0) {
            if (!noOrders) {
                timeline.innerHTML = `
                    <div class="text-center py-5" id="noOrders">
                        <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                        <p class="text-muted fs-4">Không tìm thấy đơn hàng nào!</p>
                        <a href="${window.location.origin}/" class="btn btn-primary px-5 py-3">Mua sắm ngay</a>
                    </div>
                `;
            }
            return;
        }

        orders.forEach(order => {
            const statusClass = {
                'Hoàn thành': 'success',
                'Đang giao': 'info',
                'Hủy': 'danger',
                'Chờ xác nhận': 'primary'
            }[order.status] || 'primary';

            const paymentStatusClass = order.payment_status === 'Đã thanh toán' ? 'bg-success' : 'bg-warning';

            const itemsHtml = order.order_items.map(item => `
                <div class="card mb-2 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <img src="${window.location.origin}/storage/${item.variant.image}" alt="${item.product.name}" class="rounded me-3" style="width: 70px; height: 70px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <strong>${item.product.name}</strong> (x${item.quantity})<br>
                            <span class="badge bg-secondary mt-1 px-2 py-1">${item.size} - ${item.color}</span>
                        </div>
                    </div>
                </div>
            `).join('');

            const discountHtml = order.coupon_code && order.discount_amount > 0 ? `
                <li class="list-group-item py-2 d-flex align-items-center">
                    <strong>Số tiền giảm:</strong>
                    <span>${new Intl.NumberFormat('vi-VN').format(order.discount_amount)} VNĐ</span>
                </li>
            ` : '';

            const buttonsHtml = `
                <a href="${window.location.origin}/checkout/invoice/${order.id}" class="btn btn-primary btn-sm px-4 py-2">
                    <i class="fas fa-file-invoice me-2"></i> Xem chi tiết
                </a>
                ${order.status === 'Hoàn thành' && (new Date() - new Date(order.completed_at)) / (1000 * 60 * 60 * 24) <= 7 ?
                    (order.return_request_status === '' ? `
                        <a href="${window.location.origin}/returns/create/${order.id}" class="btn btn-warning btn-sm px-4 py-2">
                            <i class="fas fa-undo-alt me-2"></i> Yêu cầu hoàn hàng
                        </a>
                    ` : `<span class="text-muted">Bạn đã yêu cầu hoàn hàng cho đơn này.</span>`) : ''
                }
                ${order.status === 'Chờ xác nhận' && order.payment_status !== 'Đã thanh toán' ?
                    (order.payment_method === 'cod' ? `
                        <form action="${window.location.origin}/order/cancel/${order.id}" method="POST" class="d-inline">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                            <button type="submit" class="btn btn-danger btn-sm px-4 py-2">
                                <i class="fas fa-times me-2"></i> Hủy đơn
                            </button>
                        </form>
                    ` : `<span class="text-muted small">Không thể hủy</span>`) : `<span class="text-muted small">Không thể hủy</span>`
                }
                ${order.payment_status === 'Chưa thanh toán' && order.payment_method !== 'cod' ? `
                    <a href="${window.location.origin}/checkout/continue/${order.id}" class="btn btn-success btn-sm px-4 py-2">
                        <i class="fas fa-credit-card me-2"></i> Tiếp tục thanh toán
                    </a>
                ` : ''}
            `;

            timeline.innerHTML += `
                <div class="timeline-item mb-5 animate__animated animate__fadeIn">
                    <div class="timeline-dot bg-${statusClass}"></div>
                    <div class="timeline-content p-4 border rounded-3 shadow-sm w-100 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-primary fw-bold mb-0">Đơn hàng #${order.id}</h5>
                            <span class="badge bg-${statusClass} fs-6 px-3 py-2">${order.status}</span>
                        </div>
                        <div class="row g-3 text-muted">
                            <div class="col-md-6">
                                <p class="mb-2"><i class="fas fa-calendar-alt me-2"></i> Ngày đặt hàng: <strong>${formatDate(order.created_at)}</strong></p>
                                <p class="mb-2"><i class="fas fa-shipping-fast me-2"></i> Giao hàng: <span class="badge bg-info px-2 py-1">${order.delivering_at || ''}</span></p>
                                <p class="mb-2"><i class="fas fa-check-circle me-2"></i> Hoàn thành: <span class="badge bg-info px-2 py-1">${order.completed_at || ''}</span></p>
                                <p class="mb-2"><i class="fas fa-wallet me-2"></i> Thanh toán: <span class="badge bg-info px-2 py-1">${order.payment_method}</span></p>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item py-2 d-flex align-items-center">
                                        <strong><i class="fas fa-money-check-alt me-2"></i> Trạng thái:</strong>
                                        <span class="badge ${paymentStatusClass} px-2 py-1">${order.payment_status}</span>
                                    </li>
                                    <li class="list-group-item py-2 d-flex align-items-center">
                                        <strong>Mã giảm giá:</strong>
                                        <span>${order.coupon_code || 'Không có mã nào áp dụng'}</span>
                                    </li>
                                    ${discountHtml}
                                </ul>
                            </div>
                            <h6 class="mt-4 text-secondary fw-semibold">Sản phẩm trong đơn hàng:</h6>
                            <div class="order-items mt-3">${itemsHtml}</div>
                            <div class="d-flex flex-wrap gap-3 mt-4">${buttonsHtml}</div>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    // Hàm đặt lại bộ lọc
    function resetFilters() {
        document.getElementById('filterForm').reset();
        toggleCustomDate(document.getElementById('time_range'));
        renderOrders(orders);
    }

    // Sự kiện submit form
    document.getElementById('filterForm').addEventListener('submit', event => {
        event.preventDefault();
        filterOrders();
    });

    // Khởi tạo hiển thị ban đầu
    renderOrders(orders);
</script>

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


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cityCode = "{{ $orders->first()->city ?? '' }}";
        const districtCode = "{{ $orders->first()->district ?? '' }}";
        const wardCode = "{{ $orders->first()->ward ?? '' }}";

        if (cityCode && districtCode && wardCode) {
            // Fetch tỉnh/thành phố
            fetch(`https://provinces.open-api.vn/api/p/`)
                .then(response => response.json())
                .then(data => {
                    let cityName = "";
                    data.forEach(province => {
                        if (province.code == cityCode) {
                            cityName = province.name;
                        }
                    });
                    document.getElementById("city-name").innerText = cityName;
                })
                .catch(error => console.error("Lỗi tải dữ liệu tỉnh/thành phố:", error));

            // Fetch quận/huyện
            fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`)
                .then(response => response.json())
                .then(data => {
                    let districtName = "";
                    data.districts.forEach(district => {
                        if (district.code == districtCode) {
                            districtName = district.name;
                        }
                    });
                    document.getElementById("district-name").innerText = districtName;
                })
                .catch(error => console.error("Lỗi tải dữ liệu quận/huyện:", error));

            // Fetch xã/phường
            fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                .then(response => response.json())
                .then(data => {
                    let wardName = "";
                    data.wards.forEach(ward => {
                        if (ward.code == wardCode) {
                            wardName = ward.name;
                        }
                    });
                    document.getElementById("ward-name").innerText = wardName;
                })
                .catch(error => console.error("Lỗi tải dữ liệu xã/phường:", error));
        }
    });
</script>
