@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 py-5" style="max-width: 100%;">
        <h2 class="mb-5 text-primary text-center fw-bold" style="font-size: 2.5rem; letter-spacing: 1px;">
            Lịch Sử Đơn Hàng
        </h2>

        <div class="filter-section mb-4 p-4 border rounded-3 bg-white shadow-sm" style="max-width: 1200px; margin: auto;">
            <h5 class="mb-3 fw-bold">Lọc đơn hàng</h5>
            <form id="orderFilterForm" class="row g-3">
                <!-- Status Filter -->
                <div class="col-md-4">
                    <label for="statusFilter" class="form-label">Trạng thái đơn hàng</label>
                    <select id="statusFilter" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="Chờ xác nhận">Chờ xác nhận</option>
                        <option value="Đang giao">Đang giao</option>
                        <option value="Đã giao hàng thành công">Đã giao hàng thành công</option>
                        <option value="Hoàn thành">Hoàn thành</option>
                        <option value="Hủy">Hủy</option>
                    </select>
                </div>
                <!-- Date Range Filter -->
                <div class="col-md-4">
                    <label for="startDate" class="form-label">Từ ngày</label>
                    <input type="date" id="startDate" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="endDate" class="form-label">Đến ngày</label>
                    <input type="date" id="endDate" class="form-control">
                </div>
                <!-- Filter Button -->
                <div class="col-md-4">
                    <label for="paymentStatusFilter" class="form-label">Trạng thái thanh toán</label>
                    <select id="paymentStatusFilter" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="Chưa thanh toán">Chưa thanh toán</option>
                        <option value="Đã thanh toán">Đã thanh toán</option>
                        <option value="Thất bại">Thất bại</option>
                    </select>
                </div>
                <!-- Product Name Filter -->
                <div class="col-md-4">
                    <label for="productName" class="form-label">Tên sản phẩm</label>
                    <input type="text" id="productName" class="form-control" placeholder="Nhập tên sản phẩm">
                </div>

                <div class="col-12">
                    <button type="button" id="applyFilter" class="btn btn-primary mt-2">Áp dụng bộ lọc</button>
                    <button type="button" id="resetFilter" class="btn btn-secondary mt-2">Xóa bộ lọc</button>
                </div>
            </form>
        </div>
        <ul class="nav nav-tabs justify-content-center" id="tableTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="table1-tab" data-bs-toggle="tab" data-bs-target="#table1" type="button" role="tab">
                    Đơn hàng
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="table2-tab" data-bs-toggle="tab" data-bs-target="#table2" type="button" role="tab">
                    Đơn hoàn
                </button>
            </li>
        </ul>
        



          <div class="tab-content mt-3" id="tableTabsContent">
            <div class="tab-pane fade show active" id="table1" role="tabpanel">
                    <!-- Danh sách đơn hàng -->
        <div class="timeline position-relative mx-auto" id="orderTimeline" style="max-width: 1200px;">
            @php
    $excludedStatuses = [
        'Đã hoàn hàng',
        'Đang chờ hoàn hàng',
        'Đang trên đường hoàn',
        'Đã nhận được đơn hoàn',
        'Đã hoàn tiền',
    ];
@endphp
@forelse ($orders->whereNotIn('status', $excludedStatuses) as $order)
                <div class="timeline-item mb-5 animate__animated animate__fadeIn" data-order='@json($order)'>
                    <div
                        class="timeline-dot bg-{{ $order->status == 'Hoàn thành' ? 'success' : ($order->status == 'Đang giao' ? 'info' : ($order->status == 'Hủy' ? 'danger' : 'primary')) }}">
                    </div>
                    <div class="timeline-content p-4 border rounded-3 shadow-sm w-100 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-primary fw-bold mb-0">Đơn hàng #{{ $order->id }}</h5>
                            <span
                                class="badge bg-{{ $order->status == 'Hoàn thành' ? 'success' : ($order->status == 'Đang giao' ? 'info' : ($order->status=='Từ chối hoàn hàng' ? 'danger': ($order->status == 'Hủy' ? 'danger' : 'primary'))) }} fs-6 px-3 py-2">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div class="row g-3">
                            <!-- Left Column: Order Information -->
                            <div class="col-md-6 text-muted">
                                <h6 class="text-secondary fw-semibold mb-3">Thông tin đơn hàng</h6>
                                <p class="mb-2"><i class="fas fa-calendar-alt me-2"></i> Ngày đặt hàng:
                                    <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                                </p>
                                <p class="mb-2"><i class="fas fa-shipping-fast me-2"></i> Giao hàng: 
                                    <span class="badge bg-info px-2 py-1">{{ $order->delivering_at ?? 'Chưa có' }}</span>
                                </p>
                                <p class="mb-2"><i class="fas fa-check-circle me-2"></i> Hoàn thành: 
                                    <span class="badge bg-info px-2 py-1">{{ $order->completed_at ?? 'Chưa hoàn thành' }}</span>
                                </p>
                                <p class="mb-2"><i class="fas fa-wallet me-2"></i> Thanh toán: 
                                    <span class="badge bg-info px-2 py-1">{{ $order->payment_method }}</span>
                                </p>
                                <p class="mb-2"><i class="fas fa-clock me-2"></i> Cập nhật: 
                                    <span class="badge bg-info px-2 py-1">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                                </p>
                                <p class="mb-2"><i class="fas fa-check-circle me-2"></i> Trạng thái : 
                                    <span class="badge {{ $order->payment_status == 'Đã thanh toán' ? 'bg-success' : 'bg-warning' }} px-2 py-1">
                                        {{ $order->payment_status }}</span>
                                </p>
                                <p class="mb-2"><i class="fas fa-ticket-alt me-2"></i> Mã giảm giá: 
                                    <span class="badge bg-info px-2 py-1">
                                        <span>{{ $order->coupon_code ?? 'Không có mã nào áp dụng' }}</span>
                                    </span>
                                    
                                    @if ($order->coupon_code && $order->discount_amount > 0)
                                        <li class="list-group-item py-2 d-flex align-items-center">
                                            <strong>Số tiền giảm:</strong>
                                            <span>{{ number_format($order->discount_amount, 0, ',', '.') }} VNĐ</span>
                                        </li>
                                    @endif
                                </p>
                            </div>
                            <!-- Right Column: Product Details -->
                            <div class="col-md-6">
                                <h6 class="text-secondary fw-semibold mb-3">Sản phẩm trong đơn hàng</h6>
                                <div class="order-items">
                                    @foreach ($order->orderItems as $item)
                                        <div class="card mb-2 border-0 shadow-sm">
                                            <div class="card-body d-flex align-items-center p-3">
                                                <img src="{{ asset('storage/' . $item->variant->image) }}"
                                                alt="{{ $item->product->name }}" class="rounded me-3"
                                                style="width: 150px; height: 100px; object-fit: cover;">
                                           
                                                <div class="flex-grow-1">
                                                    <strong>Tên sản phẩm: {{ $item->product->name }}</strong> (x{{ $item->quantity }})<br>
                                                    <span class="badge bg-secondary mt-1 px-2 py-1">{{ $item->size }} - {{ $item->color }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="{{ route('checkout.invoice', $order->id) }}"
                                class="btn btn-primary btn-sm px-4 py-2">
                                <i class="fas fa-file-invoice me-2"></i> Xem chi tiết
                            </a>
                            @if ($order->status == 'Đã giao hàng thành công' && now()->diffInDays($order->completed_at) <= 7)
                                @if ($order->return_request_status == '')
                                    <a href="{{ route('returns.create', $order->id) }}"
                                        class="btn btn-warning btn-sm px-4 py-2">
                                        <i class="fas fa-undo-alt me-2"></i> Yêu cầu hoàn hàng
                                    </a>
                                @else
                                    <span class="text-muted">Bạn đã yêu cầu hoàn hàng cho đơn này.</span>
                                @endif
                            @endif
                            
                            @if (in_array($order->status, ['Đã giao hàng thành công', 'Từ chối hoàn hàng']))
                            <a href="{{ route('checkout.done', $order->id) }}"
                                class="btn btn-success btn-sm px-4 py-2">
                                <i class="fas fa-credit-card me-2"></i> Xác nhận đã nhận hàng
                            </a>
                        @endif
                            @if ($order->status == 'Chờ xác nhận' && $order->payment_status != 'Đã thanh toán')
                                @if ($order->payment_method == 'cod')
                                    <form action="{{ route('order.cancel', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm px-4 py-2"   onclick="return confirm('Bạn chắc chắn hủy đơn không?')">
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
                                <a href="{{ route('checkout.continue', $order->id) }}"
                                    class="btn btn-success btn-sm px-4 py-2">
                                    <i class="fas fa-credit-card me-2"></i> Tiếp tục thanh toán
                                </a>
                            @endif
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

            </div>



            <div class="tab-pane fade" id="table2" role="tabpanel">
                      <!-- Danh sách đơn hàng -->
        <div class="timeline position-relative mx-auto" id="orderTimeline" style="max-width: 1200px;">
            @php
    $excludedStatuses = [
        'Đã hoàn hàng',
        'Đang chờ hoàn hàng',
        'Đang trên đường hoàn',
        'Đã nhận được đơn hoàn',
        'Đã hoàn tiền',
    ];
@endphp
@forelse ($orders->whereIn('status', $excludedStatuses) as $order)
                <div class="timeline-item mb-5 animate__animated animate__fadeIn" data-order='@json($order)'>
                    <div
                        class="timeline-dot bg-{{ $order->status == 'Hoàn thành' ? 'success' : ($order->status == 'Đang giao' ? 'info' : ($order->status == 'Hủy' ? 'danger' : 'primary')) }}">
                    </div>
                    <div class="timeline-content p-4 border rounded-3 shadow-sm w-100 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-primary fw-bold mb-0">Đơn hàng #{{ $order->id }}</h5>
                            <span
                                class="badge bg-{{ $order->status == 'Hoàn thành' ? 'success' : ($order->status == 'Đang giao' ? 'info' : ($order->status=='Từ chối hoàn hàng' ? 'danger': ($order->status == 'Hủy' ? 'danger' : 'primary'))) }} fs-6 px-3 py-2">
                                {{ $order->status }}
                            </span>
                        </div>
                        <div class="row g-3">
                            <!-- Left Column: Order Information -->
                            <div class="col-md-6 text-muted">
                                <h6 class="text-secondary fw-semibold mb-3">Thông tin đơn hàng</h6>
                                <p class="mb-2"><i class="fas fa-calendar-alt me-2"></i> Ngày đặt hàng:
                                    <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                                </p>
                                <p class="mb-2"><i class="fas fa-shipping-fast me-2"></i> Giao hàng: 
                                    <span class="badge bg-info px-2 py-1">{{ $order->delivering_at ?? 'Chưa có' }}</span>
                                </p>
                                <p class="mb-2"><i class="fas fa-check-circle me-2"></i> Hoàn thành: 
                                    <span class="badge bg-info px-2 py-1">{{ $order->completed_at ?? 'Chưa hoàn thành' }}</span>
                                </p>
                                <p class="mb-2"><i class="fas fa-wallet me-2"></i> Thanh toán: 
                                    <span class="badge bg-info px-2 py-1">{{ $order->payment_method }}</span>
                                </p>
                                <p class="mb-2"><i class="fas fa-clock me-2"></i> Cập nhật: 
                                    <span class="badge bg-info px-2 py-1">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                                </p>
                                <p class="mb-2"><i class="fas fa-check-circle me-2"></i> Trạng thái : 
                                    <span class="badge {{ $order->payment_status == 'Đã thanh toán' ? 'bg-success' : 'bg-warning' }} px-2 py-1">
                                        {{ $order->payment_status }}</span>
                                </p>
                                <p class="mb-2"><i class="fas fa-ticket-alt me-2"></i> Mã giảm giá: 
                                    <span class="badge bg-info px-2 py-1">
                                        <span>{{ $order->coupon_code ?? 'Không có mã nào áp dụng' }}</span>
                                    </span>
                                    
                                    @if ($order->coupon_code && $order->discount_amount > 0)
                                        <li class="list-group-item py-2 d-flex align-items-center">
                                            <strong>Số tiền giảm:</strong>
                                            <span>{{ number_format($order->discount_amount, 0, ',', '.') }} VNĐ</span>
                                        </li>
                                    @endif
                                </p>
                            </div>
                            <!-- Right Column: Product Details -->
                            <div class="col-md-6">
                                <h6 class="text-secondary fw-semibold mb-3">Sản phẩm trong đơn hàng</h6>
                                <div class="order-items">
                                    @foreach ($order->orderItems as $item)
                                        <div class="card mb-2 border-0 shadow-sm">
                                            <div class="card-body d-flex align-items-center p-3">
                                                <img src="{{ asset('storage/' . $item->variant->image) }}"
                                                alt="{{ $item->product->name }}" class="rounded me-3"
                                                style="width: 150px; height: 100px; object-fit: cover;">
                                           
                                                <div class="flex-grow-1">
                                                    <strong>Tên sản phẩm: {{ $item->product->name }}</strong> (x{{ $item->quantity }})<br>
                                                    <span class="badge bg-secondary mt-1 px-2 py-1">{{ $item->size }} - {{ $item->color }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="{{ route('checkout.invoice', $order->id) }}"
                                class="btn btn-primary btn-sm px-4 py-2">
                                <i class="fas fa-file-invoice me-2"></i> Xem chi tiết
                            </a>
                            @if ($order->status == 'Đã giao hàng thành công' && now()->diffInDays($order->completed_at) <= 7)
                                @if ($order->return_request_status == '')
                                    <a href="{{ route('returns.create', $order->id) }}"
                                        class="btn btn-warning btn-sm px-4 py-2">
                                        <i class="fas fa-undo-alt me-2"></i> Yêu cầu hoàn hàng
                                    </a>
                                @else
                                    <span class="text-muted">Bạn đã yêu cầu hoàn hàng cho đơn này.</span>
                                @endif
                            @endif
                            
                            @if (in_array($order->status, ['Đã giao hàng thành công', 'Từ chối hoàn hàng']))
                            <a href="{{ route('checkout.done', $order->id) }}"
                                class="btn btn-success btn-sm px-4 py-2">
                                <i class="fas fa-credit-card me-2"></i> Xác nhận đã nhận hàng
                            </a>
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
                                <a href="{{ route('checkout.continue', $order->id) }}"
                                    class="btn btn-success btn-sm px-4 py-2">
                                    <i class="fas fa-credit-card me-2"></i> Tiếp tục thanh toán
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5" id="noOrders">
                    <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                    <p class="text-muted fs-4">Bạn chưa có đơn hoàn nào!</p>
                    <a href="{{ route('home') }}" class="btn btn-primary px-5 py-3">Mua sắm ngay</a>
                </div>
            @endforelse
        </div>
    </div>

            </div>
          </div>

    <!-- JavaScript xử lý lọc -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            const productName = document.getElementById('productName');
            const paymentStatusFilter = document.getElementById(
            'paymentStatusFilter'); // Thêm biến cho trạng thái thanh toán
            const applyFilter = document.getElementById('applyFilter');
            const resetFilter = document.getElementById('resetFilter');
            const timelineItems = document.querySelectorAll('.timeline-item');
            const noOrders = document.getElementById('noOrders');

            // Function to format date as dd/mm/yyyy for comparison
            function formatDateToDMY(dateStr) {
                if (!dateStr) return '';
                const date = new Date(dateStr);
                return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear()}`;
            }

            // Function to check if order matches filters
            function filterOrders() {
                let hasVisibleOrders = false;
                const selectedStatus = statusFilter.value;
                const start = startDate.value ? new Date(startDate.value) : null;
                const end = endDate.value ? new Date(endDate.value) : null;
                const productQuery = productName.value.trim().toLowerCase();
                const selectedPaymentStatus = paymentStatusFilter.value; // Lấy giá trị trạng thái thanh toán

                timelineItems.forEach(item => {
                    const order = JSON.parse(item.dataset.order);
                    const orderDateStr = order.created_at.split(' ')[
                    0]; // Assuming created_at is like "2025-04-14 12:00:00"
                    const orderDate = new Date(orderDateStr);
                    const orderStatus = order.status;
                    const paymentStatus = order.payment_status; // Giả sử đơn hàng có trường payment_status
                    let matchesStatus = !selectedStatus || orderStatus === selectedStatus;
                    let matchesDate = true;
                    let matchesProduct = !productQuery;
                    let matchesPaymentStatus = !selectedPaymentStatus || paymentStatus ===
                        selectedPaymentStatus; // Kiểm tra trạng thái thanh toán

                    // Date range filter
                    if (start) {
                        matchesDate = matchesDate && orderDate >= start;
                    }
                    if (end) {
                        // Include the entire end date
                        end.setHours(23, 59, 59, 999);
                        matchesDate = matchesDate && orderDate <= end;
                    }

                    // Product name filter
                    if (productQuery) {
                        order.order_items.forEach(item => {
                            if (item.product.name.toLowerCase().includes(productQuery)) {
                                matchesProduct = true;
                            }
                        });
                    }

                    // Show or hide item
                    if (matchesStatus && matchesDate && matchesProduct && matchesPaymentStatus) {
                        item.style.display = 'block';
                        hasVisibleOrders = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Show/hide "no orders" message
                if (noOrders) {
                    noOrders.style.display = hasVisibleOrders ? 'none' : 'block';
                }
            }

            // Apply filter on button click
            applyFilter.addEventListener('click', filterOrders);

            // Reset filters
            resetFilter.addEventListener('click', () => {
                statusFilter.value = '';
                startDate.value = '';
                endDate.value = '';
                productName.value = '';
                paymentStatusFilter.value = ''; // Reset trạng thái thanh toán
                timelineItems.forEach(item => item.style.display = 'block');
                if (noOrders) {
                    noOrders.style.display = timelineItems.length === 0 ? 'block' : 'none';
                }
            });

            // Optional: Apply filter on Enter key in product name input
            productName.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    filterOrders();
                }
            });
        });
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
