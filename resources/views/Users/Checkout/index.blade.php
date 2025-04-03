@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/thanhtoan.css') }}">


    <div class="checkout-container">
      
        <div class="billing-info">
            <h3>THÔNG TIN THANH TOÁN</h3>

            <form id="checkoutForm" action="{{ route('checkout.placeOrder') }}" method="POST">
                @csrf

                <div class="form-group">
                    <!-- <label for="name">Họ và tên *</label> -->
                    <input type="text" id="name" name="name" value="{{ Auth::user()->name }}"
                        placeholder="Họ và tên *">
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </div>

                <div class="form-group">
                    <!-- <label for="phone">Số điện thoại *</label> -->
                    <input type="text" id="phone" name="phone" value="{{ Auth::user()->phone }}"
                        placeholder="Số điện thoại *">
                        @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                </div>

                <div class="form-group">
                    <!-- <label for="email">Địa chỉ Email</label> -->
                    <input type="email" id="email" name="email" value="{{ Auth::user()->email }}"
                        placeholder="Email *">
                        @error('Email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                </div>

                <div class="form-group">
                    <label for="province">Tỉnh/Thành phố</label>
                    <select id="province" name="city" class="form-control" >
                        <option value="" disabled selected>Chọn tỉnh/thành phố</option>
                    </select>
                </div>
            
                <div class="form-group">
                    <label for="district">Quận/Huyện</label>
                    <select id="district" name="district" class="form-control" >
                        <option value="" disabled selected>Chọn quận/huyện</option>
                    </select>
                </div>
            
                <div class="form-group">
                    <label for="ward">Xã/Phường</label>
                    <select id="ward" name="ward" class="form-control" >
                        <option value="" disabled selected>Chọn xã/phường</option>
                    </select>
                </div>
            
                <div class="form-group">
                    <label for="address">Địa chỉ chi tiết</label>
                    <input type="text" id="address" name="address" class="form-control" value="{{ $user->address ?? '' }}" >
                </div>
              
                <div class="commitment">
                    <p><strong>🔒 Cam kết bảo mật:</strong> Mọi thông tin của bạn sẽ được bảo vệ tuyệt đối và chỉ sử dụng để xử lý đơn hàng.</p>
                    <p>✅ Giao hàng nhanh chóng – Thanh toán an toàn – Hỗ trợ tận tình.</p>
                </div>
                
               
                

        </div>


        <div class="order-summary">
            <h3>ĐƠN HÀNG CỦA BẠN</h3>
            @foreach ($cartItems as $item)
                <div class="order-item">
                    <div class="order-item__image">
                        <img src="{{ $item->product->image_url }}" style="width: 120px; height: auto;">
                    </div>
                    <div class="order-item__details">
                        <p> Sản phẩm: <b> {{ $item->product->name }} </b></p>
                        <p> Giá: <b> {{ number_format($item->price, 0, ',', '.') }} VND </b></p>
                        <p> Số lượng: {{ $item->quantity }} </p>
                        <p> Size: {{ $item->variant->size ?? 'Không có' }} </p>
                        <p> Màu sắc: {{ $item->variant->color ?? 'Không có' }} </p>
                    </div>
                </div>
                
            @endforeach

            <div class="order-total">
                <p>Tổng cộng: {{ number_format($totalPrice, 0, ',', '.') }} VND</p>
            </div>



            <!-- Nhập mã giảm giá -->
            <h4>Nhập mã giảm giá</h4>
            <input type="text" id="coupon_code" name="coupon_code" class="form-control" placeholder="Mã giảm giá">
            <button type="button" id="apply_coupon_btn" class="btn btn-primary mt-2">Áp dụng</button>
            <div id="applied_coupons" class="mt-2">
                {{-- List mã giảm giá --}}
            </div> {{-- Đừng Xoá nữa nha sửa 2 lần cái thẻ div này rùi đó mấy ní --}}
            <div id="coupon_message" class="mt-2"></div>

            <div class="price-summary mt-4">
                <p>Tổng tiền trước giảm: <span id="total_price">{{ number_format($totalPrice, 0, ',', '.') }} VNĐ</span>
                </p>
                <p>Phí vận chuyển: <span id="shipping_fee">{{ number_format(30000, 0, ',', '.') }} VNĐ</span></p>
                <p>Số tiền giảm: <span id="discount_amount">0 VNĐ</span></p>
                <h4>Thành tiền: <span id="final_price">{{ number_format($totalPrice + 30000, 0, ',', '.') }} VNĐ</span>
                </h4>
            </div>


            <!-- Hidden input để lưu finalPrice cho form thanh toán -->
            <div class="payment-methods">
                <input type="hidden" name="final_price" id="hidden_final_price" value="{{ $totalPrice }}">

                <h4>Chọn phương thức thanh toán:</h4>

                <input type="radio" name="payment_method" value="cod" checked> Thanh toán khi nhận hàng (COD)
                <br>
                <input type="radio" name="payment_method" value="vnpay"> Thanh toán qua VNPAY

            </div>
            <input type="hidden" name="items" id="selectitem" value="{{ $items }}">

            <button id="paymentButton" type="submit" class="btn btn-success">Thanh toán</button>

            </form>
        </div>

    </div>

    {{-- chọn nút thanh toán --}}
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
                        note: form.querySelector('textarea').value,
                        coupon_code: formData.get('coupon_code'),
                        items: formData.get('items')
                    };

                    console.log('Data sent to VNPAY:', data); // Ghi log để kiểm tra dữ liệu gửi đi

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
                            console.log('Response from server:', data); // Ghi log để kiểm tra phản hồi
                            if (data.success && data.payment_url) {
                                window.location.href = data.payment_url;
                            } else {
                                alert('Có lỗi xảy ra khi tạo thanh toán VNPAY: ' + (data.message ||
                                    'Không rõ lỗi'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Đã có lỗi xảy ra. Vui lòng thử lại.');
                        });
                }
            });

            // Giữ nguyên logic áp dụng mã giảm giá
        });
    </script>
    {{-- Đừng xoá style này nha mấy ní --}}
    <style>
        #applied_coupons {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            /* Cho phép wrap nếu danh sách dài */
            gap: 10px;
            /* Khoảng cách giữa các mã */
        }

        .applied-coupon {
            display: flex;
            align-items: center;
        }

        .applied-coupon .badge {
            font-size: 14px;
        }

        .remove-coupon {
            cursor: pointer;
        }
    </style>
    {{-- Đừng xoá Script này nha mấy ní --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                        couponDiv.classList.add('applied-coupon', 'd-flex', 'align-items-center', 'mb-1');
                        const targetText = coupon.discount_target === 'shipping_fee' ? ' (Phí vận chuyển)' :
                            ' (Tổng đơn)';
                        couponDiv.innerHTML = `
                <span id="dcc" class="badge">${coupon.code}</span>
                <button id="ngg" type="button" class="btn btn-danger btn-sm remove-coupon" data-code="${coupon.code}">X</button>
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
                            const discountOrder = data.discount_order || 0;
                            const discountShipping = data.discount_shipping || 0;
                            const totalDiscount = discountOrder + discountShipping;
                            discountAmountElement.textContent = `${totalDiscount.toLocaleString('vi-VN')} VNĐ`;
                            finalPriceElement.textContent =
                                `${(totalPrice + shippingFee - totalDiscount).toLocaleString('vi-VN')} VNĐ`;
                            hiddenFinalPrice.value = totalPrice + shippingFee - totalDiscount;
                            displayAppliedCoupons(data.applied_coupons || []);
                        } else {
                            discountAmountElement.textContent = '0 VNĐ';
                            finalPriceElement.textContent =
                                `${(totalPrice + 30000).toLocaleString('vi-VN')} VNĐ`;
                            hiddenFinalPrice.value = totalPrice + 30000;
                            displayAppliedCoupons([]);
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi lấy danh sách mã giảm giá:', error);
                        discountAmountElement.textContent = '0 VNĐ';
                        finalPriceElement.textContent = `${(totalPrice + 30000).toLocaleString('vi-VN')} VNĐ`;
                        hiddenFinalPrice.value = totalPrice + 30000;
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
                        body: JSON.stringify({
                            coupon_code: couponCode
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        couponMessage.innerHTML =
                            `<div class="alert alert-${data.success ? 'success' : 'danger'}">${data.message}</div>`;
                        if (data.success) {
                            discountAmountElement.textContent =
                                `${(data.discount_order + data.discount_shipping).toLocaleString('vi-VN')} VNĐ`;
                            finalPriceElement.textContent =
                                `${data.final_price.toLocaleString('vi-VN')} VNĐ`;
                            hiddenFinalPrice.value = data.final_price;
                            displayAppliedCoupons(data.applied_coupons);
                        }
                    })
            });

            function removeCoupon(couponCode) {
                fetch('{{ route('checkout.removeCoupon') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            coupon_code: couponCode
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        couponMessage.innerHTML =
                            `<div class="alert alert-${data.success ? 'success' : 'danger'}">${data.message}</div>`;
                        if (data.success) {
                            discountAmountElement.textContent =
                                `${(data.discount_order + data.discount_shipping).toLocaleString('vi-VN')} VNĐ`;
                            finalPriceElement.textContent = `${data.final_price.toLocaleString('vi-VN')} VNĐ`;
                            hiddenFinalPrice.value = data.final_price;
                            displayAppliedCoupons(data.applied_coupons);
                        }
                    })
            }
        });
    </script>

    {{-- Đổ dữ liệu API về tỉnh ,thành phố, xã --}}
    <script>
        // Hàm hiển thị loading indicator (tùy chọn)
        function showLoading(selectElement, isLoading) {
            if (isLoading) {
                selectElement.disabled = true;
                selectElement.innerHTML = '<option value="" disabled selected>Đang tải...</option>';
            } else {
                selectElement.disabled = false;
            }
        }
    
        // Load tỉnh/thành phố
        document.addEventListener('DOMContentLoaded', () => {
            const provinceSelect = document.getElementById("province");
            const districtSelect = document.getElementById("district");
            const wardSelect = document.getElementById("ward");
    
            // Hiển thị loading
            showLoading(provinceSelect, true);
    
            fetch("https://provinces.open-api.vn/api/p/")
                .then(response => {
                    if (!response.ok) throw new Error('Không thể tải danh sách tỉnh/thành phố');
                    return response.json();
                })
                .then(data => {
                    provinceSelect.innerHTML = '<option value="" disabled selected>Chọn tỉnh/thành phố</option>';
                    data.forEach(province => {
                        let option = new Option(province.name, province.code);
                        provinceSelect.add(option);
                        if (province.code == "{{ $user->city ?? '' }}") {
                            option.selected = true;
                            loadDistricts(province.code);
                        }
                    });
                })
                .catch(error => {
                    console.error(error);
                    provinceSelect.innerHTML = '<option value="" disabled selected>Lỗi tải dữ liệu</option>';
                })
                .finally(() => {
                    showLoading(provinceSelect, false);
                });
    
            // Thêm sự kiện change ngay từ đầu
            provinceSelect.addEventListener('change', (e) => {
                districtSelect.innerHTML = '<option value="" disabled selected>Chọn quận/huyện</option>';
                wardSelect.innerHTML = '<option value="" disabled selected>Chọn xã/phường</option>';
                if (e.target.value) {
                    loadDistricts(e.target.value);
                }
            });
    
            districtSelect.addEventListener('change', (e) => {
                wardSelect.innerHTML = '<option value="" disabled selected>Chọn xã/phường</option>';
                if (e.target.value) {
                    loadWards(e.target.value);
                }
            });
        });
    
        // Load quận/huyện
        function loadDistricts(cityCode) {
            const districtSelect = document.getElementById("district");
            showLoading(districtSelect, true);
    
            fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`)
                .then(response => {
                    if (!response.ok) throw new Error('Không thể tải danh sách quận/huyện');
                    return response.json();
                })
                .then(data => {
                    districtSelect.innerHTML = '<option value="" disabled selected>Chọn quận/huyện</option>';
                    data.districts.forEach(district => {
                        let option = new Option(district.name, district.code);
                        districtSelect.add(option);
                        if (district.code == "{{ $user->district ?? '' }}") {
                            option.selected = true;
                            loadWards(district.code);
                        }
                    });
                })
                .catch(error => {
                    console.error(error);
                    districtSelect.innerHTML = '<option value="" disabled selected>Lỗi tải dữ liệu</option>';
                })
                .finally(() => {
                    showLoading(districtSelect, false);
                });
        }
    
        // Load xã/phường
        function loadWards(districtCode) {
            const wardSelect = document.getElementById("ward");
            showLoading(wardSelect, true);
    
            fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                .then(response => {
                    if (!response.ok) throw new Error('Không thể tải danh sách xã/phường');
                    return response.json();
                })
                .then(data => {
                    wardSelect.innerHTML = '<option value="" disabled selected>Chọn xã/phường</option>';
                    data.wards.forEach(ward => {
                        let option = new Option(ward.name, ward.code);
                        wardSelect.add(option);
                        if (ward.code == "{{ $user->ward ?? '' }}") {
                            option.selected = true;
                        }
                    });
                })
                .catch(error => {
                    console.error(error);
                    wardSelect.innerHTML = '<option value="" disabled selected>Lỗi tải dữ liệu</option>';
                })
                .finally(() => {
                    showLoading(wardSelect, false);
                });
        }
    
        // Bật/tắt chỉnh sửa địa chỉ
        
    </script>
    </div>
    @include('Users.chat')
@endsection
