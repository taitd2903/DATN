@extends('layouts.app')

@section('content')
    <div class="cart-container">
        <h2>Giỏ hàng của bạn</h2>

        @if (session('success'))
            <div class="alert alert-success" id="success-message">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(function () {
                    document.getElementById('success-message').style.display = 'none';
                }, 3000);
            </script>
        @endif

        @if ($cartItems->count() > 0)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Hình ảnh</th>
                        <th>Biến thể</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach ($cartItems as $item)
                        @php
                            $subtotal = $item->quantity * $item->price;
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            
                            <td><img src="{{ asset('storage/' . $item->variant->image) }}" style="width: 80px; height: 80px;" ></td>
                            <td>{{ $item->variant->size }} / {{ $item->variant->color }}</td>
                            <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="update-form">
                                    @csrf
                                    @method('PATCH')
                                    <div class="quantity-container">
                                        <button type="button" class="btn decrement" data-id="{{ $item->id }}">-</button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                            max="{{ $item->variant->stock_quantity }}" class="quantity-input"
                                            data-id="{{ $item->id }}">
                                        <button type="button" class="btn increment" data-id="{{ $item->id }}">+</button>
                                    </div>
                                </form>
                            </td>
                            <td>{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h4 class="text-right">Tổng tiền: <span id="total-price">{{ number_format($total, 0, ',', '.') }}</span> đ</h4>
            <div class="cart-actions">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Tiếp tục mua hàng</a>
                <a href="{{ route('checkout') }}" class="btn btn-success">Thanh toán</a>
            </div>
        @else
            <p>Giỏ hàng của bạn đang trống.</p>
        @endif
    </div>

     <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Đăng ký sự kiện click cho các nút tăng và giảm số lượng
            document.querySelectorAll(".increment, .decrement").forEach(button => {
                button.addEventListener("click", function() {
                    let id = this.getAttribute("data-id"); // Lấy id của sản phẩm
                    let input = document.querySelector(`.quantity-input[data-id='${id}']`); // Tìm input số lượng tương ứng
                    let max = parseInt(input.getAttribute("max")); // Số lượng tối đa
                    let min = parseInt(input.getAttribute("min")); // Số lượng tối thiểu
                    let currentValue = parseInt(input.value); // Lấy giá trị hiện tại của input số lượng

                    // Kiểm tra xem có phải là nút tăng số lượng hay không và tăng nếu chưa đạt số lượng tối đa
                    if (this.classList.contains("increment") && currentValue < max) {
                        input.value = currentValue + 1;
                    } 
                    // Kiểm tra xem có phải là nút giảm số lượng hay không và giảm nếu chưa đạt số lượng tối thiểu
                    else if (this.classList.contains("decrement") && currentValue > min) {
                        input.value = currentValue - 1;
                    }

                    // Tự động gửi form để cập nhật số lượng giỏ hàng
                    input.closest(".update-form").submit();
                });
            });
        });
    </script>


@endsection
