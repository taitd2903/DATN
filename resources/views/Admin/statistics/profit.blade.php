@extends('layouts.layout')

@section('content')
    <div class="container mt-4">
        
        <h1 class="mb-4 text-center">📊 Thống kê lợi nhuận</h1>
        <div class="mb-3">
            <a href="{{ route('admin.statistics.index') }}" class="btn btn-secondary">
                 sang trang sơ đồ thống kê 
            </a>
        </div>
        <form method="GET" action="{{ route('admin.statistics.profit') }}" class="mb-4">
            <input type="hidden" name="tab" id="currentTab" value="{{ request('tab', 'product-profit') }}">
            <div class="row">
                <div class="col-md-3">
                    <label>Từ ngày:</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label>Đến ngày:</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3">
                    <label>Tên sản phẩm:</label>
                    <input type="text" name="product_name" class="form-control" placeholder="Nhập tên sản phẩm" value="{{ request('product_name') }}">
                </div>
                <div class="col-md-3">
                    <label>Danh mục:</label>
                    <select name="category_id" class="form-control">
                        <option value="">Tất cả danh mục</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                
                            @if ($category->children)
                                @foreach ($category->children as $child)
                                    <option value="{{ $child->id }}" {{ request('category_id') == $child->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;|__ {{ $child->name }}
                                    </option>
                
                                    @if ($child->children)
                                        @foreach ($child->children as $grandchild)
                                            <option value="{{ $grandchild->id }}" {{ request('category_id') == $grandchild->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|____ {{ $grandchild->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>
                
            </div>

            <div class="row mt-3">
                <div class="col-md-3">
                    <label>ID đơn hàng:</label>
                    <input type="text" name="order_id" class="form-control" placeholder="Nhập ID đơn hàng" value="{{ request('order_id') }}">
                </div>
                <div class="col-md-9 d-flex align-items-end justify-content-end">
                    <button type="submit" class="btn btn-primary">🔍 Lọc dữ liệu</button>
                </div>
            </div>
        </form>

        {{-- Tabs Bootstrap --}}
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ request('tab', 'product-profit') == 'product-profit' ? 'active' : '' }}" 
                   href="?tab=product-profit&from_date={{ request('from_date') }}&to_date={{ request('to_date') }}&product_name={{ request('product_name') }}&category_id={{ request('category_id') }}&order_id={{ request('order_id') }}">
                   Lợi nhuận theo sản phẩm
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'order-profit' ? 'active' : '' }}" 
                   href="?tab=order-profit&from_date={{ request('from_date') }}&to_date={{ request('to_date') }}&product_name={{ request('product_name') }}&category_id={{ request('category_id') }}&order_id={{ request('order_id') }}">
                   Lợi nhuận theo đơn hàng
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'coupon-stats' ? 'active' : '' }}"
                   href="?tab=coupon-stats&from_date={{ request('from_date') }}&to_date={{ request('to_date') }}&product_name={{ request('product_name') }}&category_id={{ request('category_id') }}&order_id={{ request('order_id') }}">
                   Thống kê mã giảm giá
                </a>
            </li>
        </ul>

        <div class="tab-content mt-3">
            {{-- Bảng lợi nhuận theo sản phẩm --}}
            <div class="tab-pane fade {{ request('tab', 'product-profit') == 'product-profit' ? 'show active' : '' }}" id="product-profit">
                <h2 class="text-primary">🔹 Lợi nhuận theo sản phẩm</h2>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Doanh thu</th>
                            <th>Giá vốn</th>
                            <th>Lợi nhuận</th>
                            <th>Số lượng đã bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productProfits as $product)
                            <tr>
                                <td>{{ $product['product_name'] }}</td>
                                <td>{{ number_format($product['total_revenue']) }} VND</td>
                                <td>{{ number_format($product['total_cost']) }} VND</td>
                                <td>{{ number_format($product['total_profit']) }} VND</td>
                                <td>{{ $product['total_sold'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">Không có dữ liệu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Bảng lợi nhuận theo đơn hàng --}}
            <div class="tab-pane fade {{ request('tab') == 'order-profit' ? 'show active' : '' }}" id="order-profit">
                <h2 class="text-success">🔸 Lợi nhuận theo đơn hàng</h2>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày tạo</th>
                            <th>Doanh thu</th>
                            <th>Giá vốn</th>
                            <th>Lợi nhuận</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orderProfits as $order)
                            <tr>
                                <td>{{ $order['order_code'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($order['created_at'])->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($order['total_revenue']) }} VND</td>
                                <td>{{ number_format($order['total_cost']) }} VND</td>
                                <td>{{ number_format($order['total_profit']) }} VND</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">Không có đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Thống kê mã giảm giá -->
            <div class="tab-pane fade {{ request('tab') == 'coupon-stats' ? 'show active' : '' }}" id="coupon-stats">
                <h2 class="text-center">Thống kê mã giảm giá</h2>
                <br>
                <div class="discountstatistics">
                    <h4 class="text-center">Tổng quan</h4>
                    <div class="row mb-4">
                        <div class="col">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6>Tổng số mã</h6>
                                    <p>{{ $totalCoupons }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6>Mã đang hoạt động</h6>
                                    <p>{{ $activeCoupons }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6>Mã hết hạn</h6>
                                    <p>{{ $expiredCoupons }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6>Mã giảm phí vận chuyển</h6>
                                    <p>{{ $shippingCoupons }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h6>Mã giảm giá trị đơn</h6>
                                    <p>{{ $orderCoupons }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="text-center">Hiệu quả sử dụng</h4>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Tổng lượt sử dụng</h5>
                                    <p>{{ $totalUsages }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Tổng tiền giảm</h5>
                                    <p>{{ number_format($totalDiscount, 0, ',', '.') }} VNĐ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Số người dùng</h5>
                                    <p>{{ $uniqueUsers }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Tỷ lệ đơn hàng dùng mã</h5>
                                    <p>{{ number_format($couponOrderRate, 2) }}%</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Tổng doanh thu sau giảm giá</h5>
                                    <p>{{ number_format($affectedRevenue, 0, ',', '.') }} VNĐ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5>Tổng doanh thu trước giảm</h5>
                                    <p>{{ number_format($originalRevenue, 0, ',', '.') }} VNĐ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="text-center">Top 5 mã sử dụng nhiều nhất</h4>
                    <table class="table table-bordered mb-4">
                        <thead class="table-light">
                            <tr>
                                <th>Mã</th>
                                <th>Loại</th>
                                <th>Lượt sử dụng</th>
                                <th>Tổng tiền giảm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topUsedCoupons as $coupon)
                                <tr>
                                    <td>{{ $coupon['code'] }}</td>
                                    <td>{{ $coupon['type'] }}</td>
                                    <td>{{ $coupon['usage_count'] }}</td>
                                    <td>{{ number_format($coupon['total_discount'], 0, ',', '.') }} VNĐ</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">Không có dữ liệu mã giảm giá.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <h4 class="text-center">Top 5 mã sử dụng ít nhất</h4>
                    <table class="table table-bordered mb-4">
                        <thead class="table-light">
                            <tr>
                                <th>Mã</th>
                                <th>Loại</th>
                                <th>Lượt sử dụng</th>
                                <th>Tổng tiền giảm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leastUsedCoupons as $coupon)
                                <tr>
                                    <td>{{ $coupon['code'] }}</td>
                                    <td>{{ $coupon['type'] }}</td>
                                    <td>{{ $coupon['usage_count'] }}</td>
                                    <td>{{ number_format($coupon['total_discount'], 0, ',', '.') }} VNĐ</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">Không có dữ liệu mã giảm giá.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <h4 class="text-center">Biểu đồ tỷ lệ sử dụng mã</h4>
                    <div class="row mb-4">
                        <div class="col-md-5">
                            <canvas id="pieChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="columnChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thêm Chart.js và script -->
    @if(request('tab') == 'coupon-stats')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            const pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Phí vận chuyển', 'Giá trị đơn'],
                    datasets: [{
                        data: [{{ $pieChartData['shipping_usage'] }}, {{ $pieChartData['order_usage'] }}],
                        backgroundColor: ['#36A2EB', '#FF6384'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Tỷ lệ sử dụng mã giảm giá' }
                    }
                }
            });
            const columnCtx = document.getElementById('columnChart').getContext('2d');
            const columnData = @json($columnChartData);
            const labels = Object.keys(columnData.data);
            const shippingData = labels.map(key => columnData.data[key].shipping);
            const orderData = labels.map(key => columnData.data[key].order);

            const columnChart = new Chart(columnCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Phí vận chuyển',
                            data: shippingData,
                            backgroundColor: '#36A2EB'
                        },
                        {
                            label: 'Giá trị đơn',
                            data: orderData,
                            backgroundColor: '#FF6384'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: columnData.group_by === 'day' ? 'Ngày' : 'Tháng'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Lượt sử dụng'
                            },
                            ticks: {
                                stepSize: 2,   // Mỗi bước giá trị tăng lên 2
                                max: 40        // Giá trị lớn nhất là 40
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Lượt sử dụng mã theo thời gian'
                        }
                    }
                }
            });
        </script>
    @endif
@endsection
