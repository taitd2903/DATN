@extends('layouts.layout')

@section('content')
<div class="container py-4 px-3 px-md-4">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light p-2 rounded shadow-sm mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
            </li>
        </ol>
    </nav>

    <!-- Order Details Card -->
    <div class="card shadow-sm p-3 p-md-4 mb-4 rounded-lg border-0">
        <h2 class="mb-4 text-primary fw-bold text-center text-md-start">Chi Tiết Đơn Hàng #{{ $order->id }}</h2>
        
        <div class="row g-4">
            <!-- Customer Information -->
            <div class="col-12 col-md-6">
                <div class="card h-100 p-3 shadow-sm rounded">
                    <h4 class="text-dark mb-3">Thông tin khách hàng</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item py-2"><strong>Tên:</strong> {{ $order->user->name }}</li>
                        <li class="list-group-item py-2"><strong>Email:</strong> {{ $order->user->email }}</li>
                        <li class="list-group-item py-2"><strong>Số điện thoại:</strong> {{ $order->customer_phone ?? 'Chưa cập nhật' }}</li>
                       
                        <li class="list-group-item py-2"><strong>Địa chỉ:</strong>{{ $order->customer_address}}, <span id="ward-name"></span> ,<span id="district-name"></span>, <span id="city-name"></span></li>

                    </ul>
                </div>
            </div>
            
            <!-- Order Information -->
            <div class="col-12 col-md-6">
                <div class="card h-100 p-3 shadow-sm rounded">
                    <h4 class="text-dark mb-3"><i class="fas fa-info-circle me-2"></i>Thông tin đơn hàng</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item py-2 d-flex justify-content-between align-items-center">
                            <strong>Trạng thái:</strong>
                            <span class="badge bg-{{ $order->status == 'Thành công' ? 'success' : 'info' }} py-1 px-2">{{ $order->status }}</span>
                        </li>
                        <li class="list-group-item py-2 d-flex justify-content-between"><strong>Phương thức thanh toán:</strong> <span>{{ ($order->payment_method) }}</span></li>
                        <li class="list-group-item py-2 d-flex justify-content-between"><strong>Trạng thái thanh toán:</strong> <span>{{ ($order->payment_status) }}</span></li>
                        <li class="list-group-item py-2 d-flex justify-content-between"><strong>Tổng tiền:</strong> <span class="text-danger fw-bold">{{ number_format($order->total_price, 0, ',', '.') }} VND</span></li>
                        <li class="list-group-item py-2 d-flex justify-content-between"><strong>Ngày đặt hàng:</strong> <span>{{ $order->created_at->format('d/m/Y H:i') }}</span></li>
                        <li class="list-group-item py-2 d-flex justify-content-between"><strong>Ngày giao hàng:</strong> <span>{{ $order->delivering_at ?? 'Chưa cập nhật' }}</span></li>
                        <li class="list-group-item py-2 d-flex justify-content-between"><strong>Ngày hoàn thành:</strong> <span>{{ $order->completed_at ?? 'Chưa hoàn thành' }}</span></li>
                        <li class="list-group-item py-2 d-flex justify-content-between"><strong>Người cập nhật "Đang giao":</strong> <span>{{ $order->deliveringBy->name ?? 'Không xác định' }}</span></li>
                        <li class="list-group-item py-2 d-flex justify-content-between"><strong>Người cập nhật "Hoàn thành":</strong> <span>{{ $order->completedBy->name ?? 'Không xác định' }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Order Items Table -->
        <h4 class="mt-4 mb-3 text-dark">Sản phẩm trong đơn hàng</h4>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered shadow-sm rounded align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 80px;">Ảnh</th>
                        <th>Sản phẩm</th>
                        <th class="text-center" style="width: 100px;">Size</th>
                        <th class="text-center" style="width: 100px;">Màu</th>
                        <th class="text-center" style="width: 100px;">Số lượng</th>
                        <th class="text-end" style="width: 120px;">Giá</th>
                        <th class="text-end" >Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderItems as $item)
                        <tr>
                            <td class="text-center">
                                <img src="{{ $item->product->image_url }}" class="rounded img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>{{ $item->product->name }}</td>
                            <td class="text-center">{{ $item->variant ? $item->variant->size : 'Không có' }}</td>
                            <td class="text-center">{{ $item->variant ? $item->variant->color : 'Không có' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->price, 0, ',', '.') }} VND</td>
                            <td class="text-end fw-bold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VND</td>
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
        .container {
            max-width: 1400px;
        }
        .card {
            transition: box-shadow 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
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
            transform: scale(1.5);
        }
        .list-group-item {
            border: none;
        }
        @media (max-width: 768px) {
            .table {
                font-size: 0.9rem;
            }
            .card {
                margin-bottom: 1rem;
            }
            h2 {
                font-size: 1.5rem;
            }
            h4 {
                font-size: 1.25rem;
            }
        }
    </style>
@endsection
@endsection

<script>
    document.addEventListener('DOMContentLoaded', () => {
    // Lấy mã từ các phần tử HTML (hoặc lấy từ PHP như sau)
    const cityCode = "{{ $order->city }}";
    const districtCode = "{{ $order->district }}";
    const wardCode = "{{ $order->ward }}";

    // Hàm lấy tên tỉnh/thành phố từ mã cityCode
    fetch(`https://provinces.open-api.vn/api/p/`)
        .then(response => response.json())
        .then(data => {
            let cityName = "";
            data.forEach(province => {
                if (province.code == cityCode) {
                    cityName = province.name;
                }
            });
            // Cập nhật tên tỉnh/thành phố
            document.getElementById("city-name").innerText = cityName;
        })
        .catch(error => console.error("Lỗi tải dữ liệu tỉnh/thành phố:", error));

    // Hàm lấy tên quận/huyện từ mã districtCode
    fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`)
        .then(response => response.json())
        .then(data => {
            let districtName = "";
            data.districts.forEach(district => {
                if (district.code == districtCode) {
                    districtName = district.name;
                }
            });
            // Cập nhật tên quận/huyện
            document.getElementById("district-name").innerText = districtName;
        })
        .catch(error => console.error("Lỗi tải dữ liệu quận/huyện:", error));

    // Hàm lấy tên xã/phường từ mã wardCode
    fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
        .then(response => response.json())
        .then(data => {
            let wardName = "";
            data.wards.forEach(ward => {
                if (ward.code == wardCode) {
                    wardName = ward.name;
                }
            });
            // Cập nhật tên xã/phường
            document.getElementById("ward-name").innerText = wardName;
        })
        .catch(error => console.error("Lỗi tải dữ liệu xã/phường:", error));
});

</script>