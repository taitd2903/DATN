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
                                        <div><i class="fas fa-phone me-2 text-muted"></i>
                                            {{ $order->customer_phone ?? 'Chưa cập nhật' }}</div>
                                        <div>
                                            <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                            <span id="full-address">
                                                {{ implode(', ', array_filter([$order->customer_address])) }}, <span
                                                    id="ward-name"></span> ,<span id="district-name"></span>, <span
                                                    id="city-name"></span>


                                            </span>
                                            {{-- <span id="full-address">
                                                {{ implode(', ', array_filter([$order->customer_address, $order->ward, $order->district, $order->city])) ?? 'Chưa có địa chỉ' }}
                                                <p>Thành phố: <span id="city-name"></span></p>
                                                <p>Quận/Huyện: <span id="district-name"></span></p>
                                                <p>Xã/Phường: <span id="ward-name"></span></p>
                                                
                                            </span> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-white p-4 rounded-3 shadow-sm">
                                    <h5 class="text-primary fw-semibold mb-4">Thông Tin Thanh Toán</h5>
                                    <div class="d-flex flex-column gap-3">

                                        <div><strong>🕒 Thời gian đặt hàng:</strong>
                                            {{ $order->created_at->format('d/m/Y H:i') }}</div>
                                        <div><strong>💳 Thời gian thanh toán:</strong>
                                            {{ $order->updated_at->format('d/m/Y H:i') }}</div>
                                        <div><strong>📦 Phương thức:</strong> {{ ucfirst($order->payment_method) }}</div>
                                        <div><strong>🕒 Thời gian nhận hàng:</strong> {{ $order->complete_ship }}</div>
                                        <div><strong>📌 Trạng thái:</strong> {{ $order->payment_status }}</div>

                                        <!-- Hiển thị mã giảm giá -->
                                        @if ($order->couponUsages->isNotEmpty())
                                            <div>
                                                <h6 class="fw-semibold text-dark mb-2">🎁 Mã giảm giá đã áp dụng:</h6>
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($order->couponUsages as $usage)
                                                        <li
                                                            class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                            <div>
                                                                <div class="text-muted">Mã:
                                                                    <strong>{{ $usage->coupon->code }}</strong></div>
                                                                <small class="text-secondary fst-italic">
                                                                    @if ($usage->coupon->discount_target === 'shipping_fee')
                                                                        ➤ Giảm phí vận chuyển
                                                                    @elseif ($usage->coupon->discount_target === 'order_total')
                                                                        ➤ Giảm trực tiếp vào tổng đơn hàng
                                                                    @else
                                                                        ➤ Loại giảm giá không xác định
                                                                    @endif
                                                                </small>
                                                            </div>
                                                            <div class="text-success">-
                                                                {{ number_format($usage->applied_discount, 0, ',', '.') }}
                                                                ₫</div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <div class="text-muted">Không có mã giảm giá nào được áp dụng.</div>
                                        @endif


                                        <div class="alert alert-success d-flex justify-content-between align-items-center mt-3 mb-0"
                                            role="alert">
                                            <strong class="me-2">💰 Tổng tiền:</strong>
                                            <span
                                                class="fw-bold fs-4 mb-0">{{ number_format($order->total_price, 0, ',', '.') }}
                                                VND</span>
                                        </div>
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
                                                <img src="{{ asset('storage/' . $item->variant->image) }}"
                                                    class="rounded me-3"
                                                    style="width: 70px; height: 70px; object-fit: cover;">
                                            </td>
                                            <td class="py-3">{{ $item->product->name }}</td>
                                            <td class="py-3">{{ $item->size ?? 'Không có' }}</td>
                                            <td class="py-3">
                                                @if ($item->color)
                                                    <span style="display: inline-flex; align-items: center;">
                                                        <span
                                                            style="display: inline-block; width: 22px; height: 22px; border-radius: 50%; background-color: {{ $item->color }}; border: 1px solid #ccc; margin-right: 4px;"></span>

                                                    </span>
                                                @else
                                                    Không có
                                                @endif
                                            </td>

                                            <td class="py-3">{{ $item->quantity }}</td>
                                            <td class="py-3">{{ number_format($item->price, 0, ',', '.') }} VND</td>
                                            <td class="py-3 fw-semibold text-dark">
                                                {{ number_format($item->price * $item->quantity, 0, ',', '.') }} VND</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->              @if ($order->status == 'Đã giao hàng thành công')
                                <a href="{{ route('checkout.done', $order->id) }}" class="btn btn-success btn-sm px-4 py-2">
                                    <i class="fas fa-credit-card me-2"></i> xác nhận đã nhận hàng
                                </a>
                            @endif
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
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
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
