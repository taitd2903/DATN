@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <!-- Kiểm tra quyền: chỉ admin được xem thống kê -->
    @if (Auth::user()->role === 'admin')
        <h1 class="h3 mb-4 text-gray-800">Thống kê tổng quan</h1>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="statsTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab">Tổng quan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="revenue-tab" data-toggle="tab" href="#revenue" role="tab">Doanh thu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="category-revenue-tab" data-toggle="tab" href="#category-revenue" role="tab">Doanh thu theo danh mục</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="product-revenue-tab" data-toggle="tab" href="#product-revenue" role="tab">Doanh thu theo sản phẩm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="top-selling-tab" data-toggle="tab" href="#top-selling" role="tab">Sản phẩm bán chạy</a>
            </li>
        </ul>

        <div class="tab-content mt-3" id="statsTabsContent">
            <!-- Tổng quan -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng sản phẩm</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng danh mục</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCategory }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tổng số lượng trong kho</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStock }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tổng đã bán</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSoldFiltered }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doanh thu -->
            <div class="tab-pane fade" id="revenue" role="tabpanel">
                <h2 class="h4 mt-4">Doanh thu</h2>
                <div class="mb-3">
                    <label for="filterDate" class="form-label">Lọc theo ngày:</label>
                    <input type="date" id="filterDate" class="form-control" onchange="filterRevenue()">
                </div>
                <canvas id="revenueChart"></canvas>
            </div>

            <!-- Doanh thu theo danh mục -->
            <div class="tab-pane fade" id="category-revenue" role="tabpanel">
                <h2 class="h4 mt-4">Doanh thu theo danh mục</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Danh mục</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByCategory as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ number_format($category->total_revenue, 0, ',', '.') }} VND</td>
                            </tr>
                            @foreach($category->children as $child)
                                <tr>
                                    <td>-- {{ $child->name }}</td>
                                    <td>{{ number_format($child->products->sum(fn($product) => $product->variants->sum('revenue')), 0, ',', '.') }} VND</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Doanh thu theo sản phẩm -->
            <div class="tab-pane fade" id="product-revenue" role="tabpanel">
                <h2 class="h4 mt-4">Doanh thu theo sản phẩm</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByProduct as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ number_format($product->variants->sum('revenue'), 0, ',', '.') }} VND</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Sản phẩm bán chạy -->
            <div class="tab-pane fade" id="top-selling" role="tabpanel">
                <h2 class="h4 mt-4">Sản phẩm bán chạy</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng đã bán</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topSellingProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->total_sold }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Thông báo cho staff khi không có quyền -->
        <div class="alert alert-danger" role="alert">
            Bạn không có quyền truy cập phần thống kê. Vui lòng liên hệ Admin để biết thêm chi tiết.
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Quay lại Dashboard</a>
    @endif
</div>

<!-- Script chỉ hiển thị cho admin -->
@if (Auth::user()->role === 'admin')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function filterRevenue() {
            let date = document.getElementById('filterDate').value;
            window.location.href = '?date=' + date;
        }

        var ctx = document.getElementById('revenueChart').getContext('2d');
        var revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Hôm nay', 'Tháng này', 'Năm nay'],
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: [{{ $revenueByDay }}, {{ $revenueByMonth }}, {{ $revenueByYear }}],
                    backgroundColor: ['#4e73df', '#1cc88a', '#e74a3b'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endif
@endsection