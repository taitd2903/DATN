@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/thanhtoan.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        #available_coupon_list {
            font-size: 14px;
            padding: 8px;
        }
        #couponDropdownMenu {
            width: 100%;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1000;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        #couponDropdownMenu[aria-expanded="true"] {
            display: block !important;
        }
        #available_coupon_list > div:last-child {
            border-bottom: none;
        }
        #loadingMessage {
            color: #6c757d;
            text-align: center;
        }
        #couponDropdown {
            background: #ffffff;
            border: 2px solid #007bff;
            color: #007bff;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        #couponDropdown:hover {
            background: #007bff;
            color: #ffffff;
            border-color: #0056b3;
        }
        #couponDropdown:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        #couponDropdown span i {
            font-size: 16px;
        }
        #available_coupon_list button[id^="copy-coupon-"] {
            padding: 4px 10px;
            font-size: 12px;
            background: #f8f9fa;
            border: 1px solid #6c757d;
            color: #6c757d;
            border-radius: 4px;
            transition: all 0.2s ease;
            max-width:50px;
        }
        #available_coupon_list button[id^="copy-coupon-"]:hover {
            background: #6c757d;
            color: #ffffff;
            border-color: #5a6268;
        }
        #available_coupon_list > div {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        #available_coupon_list > div > span {
            font-size: 14px;
            color: #343a40;
        }
        </style>
    <div class="container my-5">
        <div class="row g-4">
            <!-- Thông tin thanh toán -->
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title mb-4 fw-bold">Thông tin thanh toán</h3>
                        <form id="checkoutForm" action="{{ route('checkout.placeOrder') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}" placeholder="Nhập họ và tên" required>
                                    @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ Auth::user()->phone }}" placeholder="Nhập số điện thoại" required>
                                    @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" placeholder="Nhập email" required>
                                    @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="province" class="form-label">Tỉnh/Thành phố</label>
                                    <select id="province" name="city" class="form-select">
                                        <option value="" disabled selected>Chọn tỉnh/thành phố</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="district" class="form-label">Quận/Huyện</label>
                                    <select id="district" name="district" class="form-select">
                                        <option value="" disabled selected>Chọn quận/huyện</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="ward" class="form-label">Xã/Phường</label>
                                    <select id="ward" name="ward" class="form-select">
                                        <option value="" disabled selected>Chọn xã/phường</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">Địa chỉ chi tiết</label>
                                    <input type="text" class="form-control" id="address" name="address" value="{{ $user->address ?? '' }}" placeholder="Số nhà, tên đường...">
                                </div>
                            </div>

                            <div class="mt-4 text-muted small">
                                <p><strong>🔒 Cam kết bảo mật:</strong> Thông tin của bạn được bảo vệ tuyệt đối và chỉ dùng để xử lý đơn hàng.</p>
                                <p>✅ Giao hàng nhanh chóng – Thanh toán an toàn – Hỗ trợ tận tình.</p>
                            </div>
                           
                    </div>
                </div>
            </div>

            <!-- Đơn hàng của bạn -->
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title mb-4 fw-bold">Đơn hàng của bạn</h3>
                        @foreach ($cartItems as $item)
                            <div class="d-flex border-bottom py-3">
                            <img src="{{ asset('storage/' . $item->variant->image) }}" style="width: 120px; height: auto;">                                <div class="ms-3 flex-grow-1">
                                    <p class="fw-bold mb-1">Tên sản phẩm: {{ $item->product->name }}</p>
                                    <p class="mb-1">Giá: {{ number_format($item->price, 0, ',', '.') }} VND</p>
                                    <p class="mb-1">Số lượng: {{ $item->quantity }}</p>
                                    <p class="mb-0">
    Size: {{ $item->variant->size ?? 'Không có' }} |
    Màu:
    @if($item->variant->color)
        <span style="display: inline-flex; align-items: center;">
            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background-color: {{ $item->variant->color }}; border: 1px solid #ccc; margin-right: 4px;"></span>
           
        </span>
    @else
        Không có
    @endif
</p>
                                </div>
                            </div>
                        @endforeach

                        <!-- Mã giảm giá -->
                        <div class="mt-4">
                            <div class="mt-3">
                                <h5>Ưu đãi dành cho bạn</h5>
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary w-100 text-start d-flex justify-content-between align-items-center" type="button" id="couponDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Chọn mã giảm giá
                                        <span><i class="bi bi-chevron-down"></i></span>
                                    </button>
                                    <div class="dropdown-menu w-100" aria-labelledby="couponDropdown" style="max-height: 150px; overflow-y: auto;">
                                        <div id="available_coupon_list" class="p-2">
                                            <div class="text-muted text-center">Đang tải mã giảm giá...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <h5>Nhập mã giảm giá</h5>
                            <div class="input-group mb-3">
                                <input type="text" id="coupon_code" name="coupon_code" class="form-control" placeholder="Mã giảm giá">
                                <button type="button" id="apply_coupon_btn" class="btn btn-outline-primary mt-2">Áp dụng</button>
                            </div>
                            <div id="applied_coupons" class="d-flex flex-wrap gap-2"></div>
                            <div id="coupon_message" class="mt-2"></div>
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                        </div>

                        <!-- Tổng tiền -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tổng tiền trước giảm:</span>
                                <span id="total_price">{{ number_format($totalPrice, 0, ',', '.') }} VNĐ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển:</span>
                                <span id="shipping_fee">{{ number_format(30000, 0, ',', '.') }} VNĐ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Số tiền giảm:</span>
                                <span id="discount_amount">0 VNĐ</span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span style="color: red;">Thành tiền:</span>
                                <span id="final_price" style="color: red;">{{ number_format($totalPrice + 30000, 0, ',', '.') }} VNĐ</span>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="mt-4">
                            <h5>Phương thức thanh toán</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
                                <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="vnpay" id="vnpay">
                                <label class="form-check-label" for="vnpay">Thanh toán qua VNPAY</label>
                            </div>
                        </div>

                        <input type="hidden" name="final_price" id="hidden_final_price" value="{{ $totalPrice + 30000 }}">
                        <input type="hidden" name="items" id="selectitem" value="{{ $items }}">

                                                    
                            @if(Auth::check() && Auth::user()->role == 'user')
                            <button id="paymentButton" type="submit" class="btn btn-success w-100 mt-4">Thanh toán</button>
                            @else
                            
                            <button type="button" class="btn btn-secondary w-100 mt-4" onclick="alert('Chỉ tài khoản người dùng mới được phép thanh toán!')">
                                Không thể thanh toán
                            </button>
                            @endif
                    </div>
                </div>
            </div>
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
                        city: formData.get('city'),
                        district: formData.get('district'),
                        ward: formData.get('ward'),
                        address: formData.get('address'),
                        // note: form.querySelector('textarea').value,
                        coupon_code: formData.get('coupon_code'),
                        items: formData.get('items')
                    };

                    console.log('Data sent to VNPAY:', data); // Ghi log để kiểm tra dữ liệu gửi đi
                    //debugger;
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
    <script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            const couponCodeInput = document.getElementById('coupon_code');
            const couponMessage = document.getElementById('coupon_message');
            const availableCouponList = document.getElementById('available_coupon_list');

            if (!couponCodeInput || !couponMessage || !availableCouponList) {
                console.error('Missing DOM elements:', {
                    couponCodeInput: !!couponCodeInput,
                    couponMessage: !!couponMessage,
                    availableCouponList: !!availableCouponList
                });
                return;
            }

            function fetchAvailableCoupons() {
                fetch('{{ route('checkout.availableCoupons') }}', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    availableCouponList.innerHTML = '';
                    if (data.success && data.coupons && data.coupons.length > 0) {
                        data.coupons.forEach(coupon => {
                            const couponItem = document.createElement('div');
                            couponItem.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'p-2', 'border-bottom');
                            couponItem.innerHTML = `
                                <span>${coupon.code} (-${coupon.discount_text})</span>
                                <button type="button" id="copy-coupon-" class="btn btn-sm btn-outline-secondary copy-coupon-btn" data-code="${coupon.code}" data-bs-dismiss="dropdown">Copy</button>
                            `;
                            availableCouponList.appendChild(couponItem);
                        });
                        document.querySelectorAll('.copy-coupon-btn').forEach(button => {
                            button.addEventListener('click', function() {
                                const code = this.getAttribute('data-code');
                                navigator.clipboard.writeText(code)
                                    .then(() => {
                                        couponCodeInput.value = code;
                                        setTimeout(() => {
                                            couponMessage.innerHTML = '';
                                        }, 3000);
                                    })
                                    .catch(err => {
                                        console.error('Copy error:', err);
                                        couponMessage.innerHTML = `<div class="alert alert-danger">Lỗi khi sao chép mã</div>`;
                                    });
                            });
                        });
                    } else {
                        availableCouponList.innerHTML = '<div class="text-muted text-center">Không có mã giảm giá nào</div>';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    availableCouponList.innerHTML = '<div class="text-muted text-center">Lỗi tải dữ liệu</div>';
                });
            }
            couponDropdown.addEventListener('click', function() {
                fetchAvailableCoupons();
            });
            fetchAvailableCoupons();
        });
        document.getElementById('couponToggle').addEventListener('click', function() {
        const couponList = document.getElementById('couponList');
        couponList.style.display = couponList.style.display === 'none' ? 'block' : 'none';
    });
    document.addEventListener('click', function(event) {
        const couponList = document.getElementById('couponList');
        const couponToggle = document.getElementById('couponToggle');
        if (!couponList.contains(event.target) && !couponToggle.contains(event.target)) {
            couponList.style.display = 'none';
        }
    });
    })();
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const errorAlerts = document.querySelectorAll('.alert.alert-danger');
            errorAlerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 5000);
            });
        });
    </script>
    </div>
    @include('Users.chat')
@endsection
