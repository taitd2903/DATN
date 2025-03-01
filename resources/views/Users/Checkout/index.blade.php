@extends('layouts.app')

@section('content')
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2>THÔNG TIN THANH TOÁN</h2>

        <form action="{{ route('checkout.placeOrder') }}" method="POST">
            @csrf

            <div>
                <label for="name">Họ và tên *</label>
                <input type="text" id="name" name="name" value="{{ Auth::user()->name }}">
            </div>

            <div>
                <label for="phone">Số điện thoại *</label>
                <input type="text" id="phone" name="phone" value="{{ Auth::user()->phone }}">
            </div>

            <div>
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" value="{{ Auth::user()->email }}">
            </div>



            <div>
                <label for="province">Tỉnh/Thành phố:</label>
                <select id="province" name="city" onchange="loadDistricts()">
                    <option value="">Chọn tỉnh/thành phố</option>
                </select>
                <input type="hidden" name="province_name" id="province_name">
            </div>

            <div>
                <label for="district">Quận/Huyện:</label>
                <select id="district" name="district" onchange="loadWards()">
                    <option value="">Chọn quận/huyện</option>
                </select>
                <input type="hidden" name="district_name" id="district_name">
            </div>

            <div>
                <label for="ward">Xã/Phường:</label>
                <select id="ward" name="ward">
                    <option value="">Chọn xã/phường</option>
                </select>
                <input type="hidden" name="ward_name" id="ward_name">
            </div>


            <div>
                <label for="address">Địa chỉ cụ thể *</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}">
            </div>

            <h4>Sản phẩm trong giỏ hàng</h4>
            <ul>
                @foreach ($cartItems as $item)
                    <li>
                        <strong>{{ $item->product->name }}</strong> <br>
                        <strong>Số lượng:</strong> {{ $item->quantity }} <br>
                        <strong>Size:</strong> {{ $item->variant->size ?? 'Không có' }} <br>
                        <strong>Màu sắc:</strong> {{ $item->variant->color ?? 'Không có' }} <br>
                        <strong>Giá:</strong> {{ number_format($item->price, 0, ',', '.') }} VND
                    </li>
                @endforeach
            </ul>

            <h4><strong>Tổng cộng: {{ number_format($totalPrice, 0, ',', '.') }} VND</strong></h4>

            <!-- Nhập mã giảm giá -->
            <h4>Nhập mã giảm giá</h4>
            <input type="text" id="coupon_code" name="coupon_code" class="form-control" placeholder="Mã giảm giá">
        <button type="button" id="apply_coupon_btn" class="btn btn-primary mt-2">Áp dụng</button>
        <div id="coupon_message" class="mt-2"></div>

        <div class="price-summary mt-4">
            <p>Tổng tiền trước giảm: <span id="total_price">{{ number_format($totalPrice, 0, ',', '.') }} VNĐ</span></p>
            <p>Số tiền giảm: <span id="discount_amount">0 VNĐ</span></p>
            <h4>Thành tiền: <span id="final_price">{{ number_format($totalPrice, 0, ',', '.') }} VNĐ</span></h4>
        </div>
        <!-- Hidden input để lưu finalPrice cho form thanh toán -->
        <input type="hidden" name="final_price" id="hidden_final_price" value="{{ $totalPrice }}">

            <h4>Chọn phương thức thanh toán:</h4>
            <input type="radio" name="payment_method" value="cod" checked> Thanh toán khi nhận hàng (COD)
            <input type="radio" name="payment_method" value="vnpay"> Thanh toán qua VNPAY

            <button type="submit" class="btn btn-success">Thanh toán</button>

        </form>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const applyCouponBtn = document.getElementById('apply_coupon_btn');
        const couponCodeInput = document.getElementById('coupon_code');
        const couponMessage = document.getElementById('coupon_message');
        const totalPriceElement = document.getElementById('total_price');
        const discountAmountElement = document.getElementById('discount_amount');
        const finalPriceElement = document.getElementById('final_price');
        const hiddenFinalPrice = document.getElementById('hidden_final_price');

        let totalPrice = {{ $totalPrice }}; // Tổng tiền trước giảm từ server

        applyCouponBtn.addEventListener('click', function () {
            const couponCode = couponCodeInput.value.trim();

            // Gửi yêu cầu Ajax
            fetch('{{ route("checkout.applyCoupon") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ coupon_code: couponCode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật giao diện khi áp dụng mã thành công
                    couponMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    discountAmountElement.textContent = `${data.discount_amount.toLocaleString('vi-VN')} VNĐ`;
                    finalPriceElement.textContent = `${data.final_price.toLocaleString('vi-VN')} VNĐ`;
                    hiddenFinalPrice.value = data.final_price; // Cập nhật giá trị để gửi form
                } else {
                    // Hiển thị thông báo lỗi
                    couponMessage.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    discountAmountElement.textContent = '0 VNĐ';
                    finalPriceElement.textContent = `${totalPrice.toLocaleString('vi-VN')} VNĐ`;
                    hiddenFinalPrice.value = totalPrice;
                }
            })
            .catch(error => {
                couponMessage.innerHTML = `<div class="alert alert-danger">Đã có lỗi xảy ra. Vui lòng thử lại.</div>`;
            });
        });
    });
</script>

        {{-- Đổ dữ liệu API về tỉnh ,thành phố, xã --}}
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                fetch("https://provinces.open-api.vn/api/p/")
                    .then(response => response.json())
                    .then(data => {
                        let provinceSelect = document.getElementById("province");
                        data.forEach(province => {
                            let option = new Option(province.name, province.code);
                            provinceSelect.add(option);
                        });
                    })
                    .catch(error => console.error("Lỗi tải dữ liệu tỉnh:", error));
            });

            function loadDistricts() {
                let provinceSelect = document.getElementById("province");
                let provinceCode = provinceSelect.value;
                let provinceName = provinceSelect.options[provinceSelect.selectedIndex].text;
                document.getElementById("province_name").value = provinceName; // Gán tên tỉnh vào input ẩn

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
                    })
                    .catch(error => console.error("Lỗi tải dữ liệu quận:", error));
            }

            function loadWards() {
                let districtSelect = document.getElementById("district");
                let districtCode = districtSelect.value;
                let districtName = districtSelect.options[districtSelect.selectedIndex].text;
                document.getElementById("district_name").value = districtName; // Gán tên quận vào input ẩn

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
                    })
                    .catch(error => console.error("Lỗi tải dữ liệu phường:", error));
            }

            document.getElementById("ward").addEventListener("change", function() {
                let wardName = this.options[this.selectedIndex].text;
                document.getElementById("ward_name").value = wardName; // Gán tên xã vào input ẩn
            });
        </script>
    </div>
@endsection
