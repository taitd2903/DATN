@extends('layouts.app')

@section('content')
<div class="container">
    <h2>THÔNG TIN THANH TOÁN</h2>

    <form action="{{ route('checkout.placeOrder') }}" method="POST">
        @csrf

        <div>
            <label for="name">Họ và tên *</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div>
            <label for="phone">Số điện thoại *</label>
            <input type="text" id="phone" name="phone" required>
        </div>

        <div>
            <label for="email">Địa chỉ Email</label>
            <input type="email" id="email" name="email">
        </div>

        <div>
            <label for="city">Tỉnh/ thành phố *</label>
            {{-- <select id="city" name="city" required>
                <option value="">Chọn tỉnh/thành phố</option>
            </select> --}}
            <input type="text" id="city" name="city" required>
        </div>

        <div>
            <label for="district">Quận/ huyện *</label>
            {{-- <select id="district" name="district" required>
                <option value="">Chọn quận/huyện</option>
            </select> --}}
            <input type="text" id="district" name="district" required>
       
        </div>

        <div>
            <label for="ward">Xã, phường *</label>
            {{-- <select id="ward" name="ward" required>
                <option value="">Chọn xã/phường</option>
            </select> --}}
            <input type="text" id="ward" name="ward" required>
        </div>

        <div>
            <label for="address">Địa chỉ cụ thể *</label>
            <input type="text" id="address" name="address" required>
        </div>

        <h4>Sản phẩm trong giỏ hàng</h4>
        <ul>
            @foreach($cartItems as $item)
                <li>
                    <strong>{{ $item->product->name }}</strong> <br>
                    <strong>Số lượng:</strong> {{ $item->quantity }} <br>
                    <strong>Size:</strong> {{ $item->variant->size ?? 'Không có' }} <br>
                    <strong>Màu sắc:</strong> {{ $item->variant->color ?? 'Không có' }} <br>
                    <strong>Thành tiền:</strong> {{ number_format($item->price, 0, ',', '.') }} VND 
                </li>
            @endforeach
        </ul>

        <h4><strong>Tổng cộng: {{ number_format($totalPrice, 0, ',', '.') }} VND</strong></h4>

        <!-- Nhập mã giảm giá -->
        <h4>Nhập mã giảm giá</h4>
        <input type="text" name="discount_code" placeholder="Nhập mã giảm giá">
        <button type="submit" formaction="{{ route('checkout.applyDiscount') }}">Áp dụng</button>

        @if(session('discount'))
            <p style="color: green;">Mã giảm giá đã được áp dụng: -{{ number_format(session('discount'), 0, ',', '.') }} VND</p>
<h4><strong>Tổng sau khi giảm: {{ number_format($totalPrice - session('discount'), 0, ',', '.') }} VND</strong></h4>
        @endif

        <h4><strong>Tổng giá trị: {{ number_format($totalPrice, 0, ',', '.') }} VND</strong></h4>
        
        <h4>Chọn phương thức thanh toán:</h4>
        <label><input type="radio" name="payment_method" value="COD" required> Thanh toán khi nhận hàng</label><br>
        <label><input type="radio" name="payment_method" value="Online"> Thanh toán Online </label><br>

        <button type="submit" class="btn btn-success">Thanh toán</button>

    </form>
</div>
@endsection