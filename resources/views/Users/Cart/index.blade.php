@extends('layouts.app')

@section('content')
    <h1>Giỏ hàng của bạn</h1>

    {{-- Kiểm tra xem có thông báo thành công nào từ session không --}}
    @if (session('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }} {{-- Hiển thị thông báo thành công --}}
        </div>
        <script>
            // Tự động ẩn thông báo sau 3 giây
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 3000);
        </script>
    @endif

    {{-- Kiểm tra xem giỏ hàng có sản phẩm hay không --}}
    @if ($cartItems->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Hình Ảnh</th>
                    <th>Biến thể</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp {{-- Khởi tạo tổng tiền giỏ hàng --}}
                @foreach ($cartItems as $item)
                    @php
                        $subtotal = $item->quantity * $item->price; // Tính thành tiền của mỗi sản phẩm
                        $total += $subtotal; // Cộng dồn vào tổng tiền
                    @endphp
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>
                            {{-- <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" width="80" height="80" style="object-fit: cover; border-radius: 8px;"> --}}
                            <img id="product-image" src="{{ $item->product->image }}" alt="{{ $item->product->name }}" width="80" height="80">
                        </td>
                        <td>{{ $item->variant->size }} / {{ $item->variant->color }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                        <td>
                            {{-- Form cập nhật số lượng sản phẩm --}}
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="update-form">
                                @csrf
                                @method('PATCH')
                                <div class="quantity-container">
                                    {{-- Nút giảm số lượng --}}
                                    <button type="button" class="btn btn-sm btn-secondary decrement" data-id="{{ $item->id }}">-</button>
                                    {{-- Input số lượng --}}
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                        max="{{ $item->variant->stock_quantity }}" class="form-control quantity-input"
                                        data-id="{{ $item->id }}" style="width: 50px; text-align: center;">
                                    {{-- Nút tăng số lượng --}}
                                    <button type="button" class="btn btn-sm btn-secondary increment" data-id="{{ $item->id }}">+</button>
                                </div>
                            </form>
                        </td>
                        <td>{{ number_format($subtotal, 0, ',', '.') }} đ</td> {{-- Thành tiền của sản phẩm --}}
                        <td>
                            {{-- Form xoá sản phẩm khỏi giỏ hàng --}}
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <h4 class="text-right">Tổng tiền: <span id="total-price">{{ number_format($total, 0, ',', '.') }}</span> đ</h4>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Tiếp tục mua hàng</a>
        <div class="text-right">
            <a href="{{ route('checkout') }}" class="btn btn-success">Thanh toán</a>
        </div>
    @else
        <p>Giỏ hàng của bạn đang trống.</p>
    @endif

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
