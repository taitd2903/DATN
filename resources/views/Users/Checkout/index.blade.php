@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/thanhtoan.css') }}">

    <div class="checkout-container py-5" >
        <div class="container">
            <div class="row g-4">
                <!-- Billing Info -->
                <div class="col-lg-6">
                    <div class="card shadow-lg border-0 p-4" style="border-radius: 20px;">
                        <h3 class="fw-bold text-primary mb-4">Thông Tin Thanh Toán</h3>
                        @if ($errors->any())
                            <div class="alert alert-danger animate__animated animate__fadeIn">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form id="checkoutForm" action="{{ route('checkout.placeOrder') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <input type="text" id="name" name="name" value="{{ Auth::user()->name }}"
                                    class="form-control shadow-sm" placeholder="Họ và tên *" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" id="phone" name="phone" value="{{ Auth::user()->phone }}"
                                    class="form-control shadow-sm" placeholder="Số điện thoại *" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="email" id="email" name="email" value="{{ Auth::user()->email }}"
                                    class="form-control shadow-sm" placeholder="Email *" required>
                            </div>
                            <div class="form-group mb-3">
                                <select id="province" name="city" onchange="loadDistricts()"
                                    class="form-control shadow-sm" required>
                                    <option value="">Chọn tỉnh/thành phố</option>
                                </select>
                                <input type="hidden" name="province_name" id="province_name">
                            </div>
                            <div class="form-group mb-3">
                                <select id="district" name="district" onchange="loadWards()"
                                    class="form-control shadow-sm" required>
                                    <option value="">Chọn quận/huyện</option>
                                </select>
                                <input type="hidden" name="district_name" id="district_name">
                            </div>
                            <div class="form-group mb-3">
                                <select id="ward" name="ward" class="form-control shadow-sm" required>
                                    <option value="">Chọn xã/phường</option>
                                </select>
                                <input type="hidden" name="ward_name" id="ward_name">
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" id="address" name="address" value="{{ old('address') }}"
                                    class="form-control shadow-sm" placeholder="Địa chỉ cụ thể *" required>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-6">
                    <div class="card shadow-lg border-0 p-4" style="border-radius: 20px;">
                        <h3 class="fw-bold text-primary mb-4">Đơn Hàng Của Bạn</h3>
                        <div class="order-items mb-4">
                            @foreach ($cartItems as $item)
                                <div class="order-item d-flex align-items-center mb-3 animate__animated animate__fadeIn">
                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                        class="rounded me-3" style="width: 70px; height: 70px; object-fit: cover; transition: transform 0.3s ease;">
                                    <div class="flex-grow-1">
                                        <p class="mb-1"><strong>{{ $item->product->name }}</strong></p>
                                        <p class="mb-1">Giá: {{ number_format($item->price, 0, ',', '.') }} VND</p>
                                        <p class="mb-1">Số lượng: {{ $item->quantity }}</p>
                                        <p class="mb-0">Size: {{ $item->variant->size ?? 'Không có' }} | Màu: {{ $item->variant->color ?? 'Không có' }}</p>
                                    </div>
                                </div>
                                <hr class="my-2">
                            @endforeach
                        </div>

                        <!-- Coupon Section -->
                        <div class="coupon-section mb-4">
                            <h4 class="fw-semibold">Mã Giảm Giá</h4>
                            <div class="input-group mb-2">
                                <input type="text" id="coupon_code" name="coupon_code" class="form-control shadow-sm"
                                    placeholder="Nhập mã giảm giá">
                                <button type="button" id="apply_coupon_btn" class="btn btn-primary">Áp dụng</button>
                            </div>
                            <div id="applied_coupons" class="mt-2"></div>
                            <div id="coupon_message" class="mt-2"></div>
                        </div>

                        <!-- Price Summary -->
                        <div class="price-summary mb-4">
                            <p>Tổng tiền trước giảm: <span id="total_price">{{ number_format($totalPrice, 0, ',', '.') }} VNĐ</span></p>
                            <p>Phí vận chuyển: <span id="shipping_fee">{{ number_format(30000, 0, ',', '.') }} VNĐ</span></p>
                            <p>Số tiền giảm: <span id="discount_amount">0 VNĐ</span></p>
                            <h4 class="fw-bold text-success">Thành tiền: <span id="final_price">{{ number_format($totalPrice + 30000, 0, ',', '.') }} VNĐ</span></h4>
                        </div>

                        <!-- Payment Methods -->
                        <div class="payment-methods">
                            <input type="hidden" name="final_price" id="hidden_final_price" value="{{ $totalPrice + 30000 }}">
                            <h4 class="fw-semibold mb-3">Phương Thức Thanh Toán</h4>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
                                <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" value="vnpay" id="vnpay">
                                <label class="form-check-label" for="vnpay">Thanh toán qua VNPAY</label>
                            </div>
                            <input type="hidden" name="items" id="selectitem" value="{{ $items }}">
                            <button id="paymentButton" type="submit" class="btn btn-success w-100 py-2">Thanh Toán Ngay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('checkoutForm');
            const paymentButton = document.getElementById('paymentButton');

            paymentButton.addEventListener('click', function(e) {
                e.preventDefault();
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

                if (paymentMethod === 'cod') {
                    form.submit();
                } else if (paymentMethod === 'vnpay') {
                    const formData = new FormData(form);
                    const data = {
                        amount: document.getElementById('hidden_final_price').value,
                        language: 'vn',
                        name: formData.get('name'),
                        phone: formData.get('phone'),
                        email: formData.get('email'),
                        city: formData.get('province_name'),
                        district: formData.get('district_name'),
                        ward: formData.get('ward_name'),
                        address: formData.get('address'),
                        note: form.querySelector('textarea')?.value || '',
                        coupon_code: formData.get('coupon_code'),
                        items: formData.get('items')
                    };

                    fetch('{{ route('vnpay.payment') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.payment_url) {
                            window.location.href = data.payment_url;
                        } else {
                            alert('Có lỗi xảy ra khi tạo thanh toán VNPAY: ' + (data.message || 'Không rõ lỗi'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Đã có lỗi xảy ra. Vui lòng thử lại.');
                    });
                }
            });

            // Coupon Logic
            const applyCouponBtn = document.getElementById('apply_coupon_btn');
            const couponCodeInput = document.getElementById('coupon_code');
            const couponMessage = document.getElementById('coupon_message');
            const appliedCouponsDiv = document.getElementById('applied_coupons');
            const totalPriceElement = document.getElementById('total_price');
            const discountAmountElement = document.getElementById('discount_amount');
            const finalPriceElement = document.getElementById('final_price');
            const hiddenFinalPrice = document.getElementById('hidden_final_price');
            let totalPrice = {{ $totalPrice }};

            function displayAppliedCoupons(coupons) {
                appliedCouponsDiv.innerHTML = '';
                if (coupons && coupons.length > 0) {
                    coupons.forEach(coupon => {
                        const couponDiv = document.createElement('div');
                        couponDiv.classList.add('applied-coupon', 'd-flex', 'align-items-center', 'mb-1', 'animate__animated', 'animate__fadeIn');
                        couponDiv.innerHTML = `
                            <span class="badge bg-success me-2">${coupon.code}</span>
                            <button type="button" class="btn btn-danger btn-sm remove-coupon" data-code="${coupon.code}">X</button>
                        `;
                        appliedCouponsDiv.appendChild(couponDiv);
                    });

                    document.querySelectorAll('.remove-coupon').forEach(button => {
                        button.addEventListener('click', function() {
                            const code = this.getAttribute('data-code');
                            removeCoupon(code);
                        });
                    });
                }
            }

            function loadAppliedCoupons() {
                fetch('{{ route('checkout.getAppliedCoupons') }}', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        totalPrice = data.total_price || 0;
                        const shippingFee = 30000;
                        const totalDiscount = (data.discount_order || 0) + (data.discount_shipping || 0);
                        discountAmountElement.textContent = `${totalDiscount.toLocaleString('vi-VN')} VNĐ`;
                        finalPriceElement.textContent = `${(totalPrice + shippingFee - totalDiscount).toLocaleString('vi-VN')} VNĐ`;
                        hiddenFinalPrice.value = totalPrice + shippingFee - totalDiscount;
                        displayAppliedCoupons(data.applied_coupons || []);
                    }
                });
            }

            loadAppliedCoupons();

            applyCouponBtn.addEventListener('click', function() {
                const couponCode = couponCodeInput.value.trim();
                fetch('{{ route('checkout.applyCoupon') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ coupon_code: couponCode })
                })
                .then(response => response.json())
                .then(data => {
                    couponMessage.innerHTML = `<div class="alert alert-${data.success ? 'success' : 'danger'} animate__animated animate__fadeIn">${data.message}</div>`;
                    if (data.success) {
                        discountAmountElement.textContent = `${(data.discount_order + data.discount_shipping).toLocaleString('vi-VN')} VNĐ`;
                        finalPriceElement.textContent = `${data.final_price.toLocaleString('vi-VN')} VNĐ`;
                        hiddenFinalPrice.value = data.final_price;
                        displayAppliedCoupons(data.applied_coupons);
                    }
                });
            });

            function removeCoupon(couponCode) {
                fetch('{{ route('checkout.removeCoupon') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ coupon_code: couponCode })
                })
                .then(response => response.json())
                .then(data => {
                    couponMessage.innerHTML = `<div class="alert alert-${data.success ? 'success' : 'danger'} animate__animated animate__fadeIn">${data.message}</div>`;
                    if (data.success) {
                        discountAmountElement.textContent = `${(data.discount_order + data.discount_shipping).toLocaleString('vi-VN')} VNĐ`;
                        finalPriceElement.textContent = `${data.final_price.toLocaleString('vi-VN')} VNĐ`;
                        hiddenFinalPrice.value = data.final_price;
                        displayAppliedCoupons(data.applied_coupons);
                    }
                });
            }

            // Province/District/Ward Logic
            fetch("https://provinces.open-api.vn/api/p/")
                .then(response => response.json())
                .then(data => {
                    let provinceSelect = document.getElementById("province");
                    data.forEach(province => {
                        let option = new Option(province.name, province.code);
                        provinceSelect.add(option);
                    });
                });

            window.loadDistricts = function() {
                let provinceSelect = document.getElementById("province");
                let provinceCode = provinceSelect.value;
                let provinceName = provinceSelect.options[provinceSelect.selectedIndex].text;
                document.getElementById("province_name").value = provinceName;

                let districtSelect = document.getElementById("district");
                districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';

                if (!provinceCode) return;

                fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.districts.forEach(district => {
                            let option = new Option(district.name, district.code);
                            districtSelect.add(option);
                        });
                    });
            };

            window.loadWards = function() {
                let districtSelect = document.getElementById("district");
                let districtCode = districtSelect.value;
                let districtName = districtSelect.options[districtSelect.selectedIndex].text;
                document.getElementById("district_name").value = districtName;

                let wardSelect = document.getElementById("ward");
                wardSelect.innerHTML = '<option value="">Chọn xã/phường</option>';

                if (!districtCode) return;

                fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.wards.forEach(ward => {
                            let option = new Option(ward.name, ward.code);
                            wardSelect.add(option);
                        });
                    });
            };

            document.getElementById("ward").addEventListener("change", function() {
                let wardName = this.options[this.selectedIndex].text;
                document.getElementById("ward_name").value = wardName;
            });
        });
    </script>

    <style>
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        }
        .form-control, .btn {
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .order-item img:hover {
            transform: scale(1.1);
        }
        .applied-coupon {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .badge {
            padding: 8px 12px;
            font-size: 14px;
        }
    </style>
    @include('Users.chat')
@endsection