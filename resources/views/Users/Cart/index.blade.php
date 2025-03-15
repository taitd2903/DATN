@extends('layouts.app')

@section('content')
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <div class="cart-container">
        <h2 style="text-align:center">GIỎ HÀNG CỦA BẠN</h2>
        <br>

        @if (session('success'))
            <div class="alert alert-success" id="success-message">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                }, 3000);
            </script>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" id="error-message">
                {{ session('error') }}
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('error-message').style.display = 'none';
                }, 5000);
            </script>
        @endif

        @if ($cartItems->count() > 0)
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach ($cartItems as $item)
                        @php
                            $subtotal = $item->quantity * $item->price;
                            $total += $subtotal;
                        @endphp
                        <tr data-cart-id="{{ $item->id }}">
                            <td><img src="{{ asset('storage/' . $item->variant->image) }}" class="cart-image"></td>
                            <td>
                                {{ $item->product->name }} ({{ $item->variant->size }} / {{ $item->variant->color }})
                                <span class="stock-warning" style="color: red; display: none;"></span>
                            </td>
                            <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="update-form">
                                    @csrf
                                    @method('PATCH')
                                    <div class="quantity-container">
                                        <button type="button" class="btn decrement"
                                            data-id="{{ $item->id }}">-</button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                            max="{{ $item->variant->stock_quantity }}" class="quantity-input"
                                            data-id="{{ $item->id }}" data-price="{{ $item->price }}">
                                        <button type="button" class="btn increment"
                                            data-id="{{ $item->id }}">+</button>
                                    </div>
                                    <div class="error-message" style="color: red; font-size: 0.9em; margin-top: 5px;"></div>
                                </form>
                            </td>
                            <td class="subtotal">{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">×</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="cart-summary">
                <h3>Tóm tắt đơn hàng</h3>
                <p>Tạm tính: <span class="total-amount">{{ number_format($total, 0, ',', '.') }} đ</span></p>
                <div class="cart-actions">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Tiếp tục mua hàng</a>
                    <a href="{{ route('checkout') }}" class="btn btn-checkout" id="checkout-btn">Thanh toán</a>
                </div>
            </div>
        @else
            <p>Giỏ hàng của bạn đang trống.</p>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Hàm cập nhật tổng phụ (subtotal) và tổng tiền (total) trên giao diện
            function updateTotals() {
                let total = 0;
                // Lặp qua từng dòng sản phẩm trong bảng
                document.querySelectorAll('tr[data-cart-id]').forEach(row => {
                    const input = row.querySelector('.quantity-input');
                    const price = parseFloat(input.getAttribute('data-price')); // Lấy giá từ data-price
                    const quantity = parseInt(input.value); // Lấy số lượng từ input
                    const subtotal = price * quantity;
                    // Cập nhật tổng phụ trên giao diện
                    row.querySelector('.subtotal').textContent = new Intl.NumberFormat('vi-VN').format(
                        subtotal) + ' đ';
                    total += subtotal;
                });
                // Cập nhật tổng tiền trên giao diện
                document.querySelector('.total-amount').textContent = new Intl.NumberFormat('vi-VN').format(total) +
                    ' đ';
            }

            // Hàm kiểm tra tồn kho bằng cách gửi request lên server
            function checkStock() {
                fetch("{{ route('cart.checkStock') }}", {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json()) // Chuyển response thành JSON
                    .then(data => { // Lặp qua dữ liệu trả về từ server
                        Object.keys(data).forEach(cartId => {
                            const row = document.querySelector(`tr[data-cart-id='${cartId}']`);
                            const input = row.querySelector('.quantity-input');
                            const stock = data[cartId].stock_quantity; // Số lượng tồn kho từ server
                            const quantity = data[cartId].quantity; // Số lượng hiện tại trong giỏ
                            const price = parseFloat(input.getAttribute('data-price'));
                            const stockWarning = row.querySelector('.stock-warning');

                            input.setAttribute('max', stock); // Cập nhật giá trị max của input
                            // Nếu số lượng trong giỏ vượt quá tồn kho
                            if (quantity > stock) {
                                input.value = stock; // Điều chỉnh số lượng về mức tối đa
                                stockWarning.style.display = 'inline';
                                stockWarning.textContent = `(Chỉ còn ${stock} trong kho)`;
                                // Gửi request cập nhật số lượng về server
                                updateCartItem(cartId, stock);
                            } else {
                                stockWarning.style.display = 'none';
                            }

                            updateTotals(); // Cập nhật lại tổng tiền
                        });
                    })
                    .catch(error => console.error('Error checking stock:', error)); // Log lỗi nếu request thất bại
            }

            // Hàm gửi request cập nhật số lượng lên server
            function updateCartItem(cartId, quantity) {
                const form = document.querySelector(`tr[data-cart-id='${cartId}'] .update-form`);
                const formData = new FormData(form);
                formData.set('quantity', quantity); // Cập nhật số lượng mới trong form data

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Cart updated:', data);
                    })
                    .catch(error => console.error('Error updating cart:', error));
            }

            // Gọi kiểm tra tồn kho ngay khi tải trang
            checkStock();

            // Kiểm tra định kỳ mỗi 10 giây (có thể điều chỉnh)
            // setInterval(checkStock, 10000);

            // Kiểm tra trước khi thanh toán
            document.getElementById('checkout-btn').addEventListener('click', function(e) {
                e.preventDefault(); // Ngăn chuyển hướng ngay lập tức
                fetch("{{ route('cart.checkStock') }}", {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        let canCheckout = true; // Biến kiểm tra xem có thể thanh toán không

                        // Reset tất cả thông báo stock-warning trước khi kiểm tra
                        document.querySelectorAll('.stock-warning').forEach(span => {
                            span.style.display = 'none';
                            span.textContent = '';
                        });

                        // Duyệt qua dữ liệu để kiểm tra tồn kho
                        Object.keys(data).forEach(cartId => {
                            const stock = data[cartId].stock_quantity;
                            const quantity = data[cartId].quantity;
                            const row = document.querySelector(`tr[data-cart-id='${cartId}']`);
                            const stockWarning = row.querySelector('.stock-warning');

                            if (quantity > stock) {
                                canCheckout = false;
                                // Hiển thị thông báo trong stock-warning
                                stockWarning.style.display = 'inline';
                                stockWarning.textContent = `(Chỉ còn ${stock} trong kho)`;
                                // Tự động điều chỉnh số lượng về mức tối đa trong kho
                                const input = row.querySelector('.quantity-input');
                                input.value = stock;
                                updateCartItem(cartId, stock); // Gửi request cập nhật số lượng
                            }
                        });

                        // Nếu không có vấn đề gì với tồn kho, chuyển hướng đến trang thanh toán
                        if (canCheckout) {
                            window.location.href = "{{ route('checkout') }}";
                        }

                        // Cập nhật lại tổng tiền sau khi điều chỉnh
                        updateTotals();
                    })
            });

            // Xử lý tăng/giảm số lượng
            document.querySelectorAll(".increment, .decrement").forEach(button => {
                button.addEventListener("click", function() {
                    let id = this.getAttribute("data-id");
                    let input = document.querySelector(`.quantity-input[data-id='${id}']`);
                    let max = parseInt(input.getAttribute("max"));
                    let min = parseInt(input.getAttribute("min"));
                    let currentValue = parseInt(input.value);
                    let errorDiv = input.closest('.quantity-container').nextElementSibling;

                    errorDiv.textContent = '';

                    if (this.classList.contains("increment")) {
                        if (currentValue >= max) {
                            errorDiv.textContent = 'Đã đạt số lượng tối đa trong kho!';
                            return;
                        }
                        input.value = currentValue + 1;
                    } else if (this.classList.contains("decrement")) {
                        if (currentValue <= min) {
                            errorDiv.textContent = 'Số lượng không thể nhỏ hơn 1!';
                            return;
                        }
                        input.value = currentValue - 1;
                    }

                    updateTotals();
                    input.closest(".update-form").submit();
                });
            });

            // Xử lý khi nhập tay số lượng
            document.querySelectorAll(".quantity-input").forEach(input => {
                input.addEventListener("change", function() {
                    let id = this.getAttribute("data-id");
                    let max = parseInt(this.getAttribute("max"));
                    let min = parseInt(this.getAttribute("min"));
                    let value = parseInt(this.value);
                    let errorDiv = this.closest('.quantity-container').nextElementSibling;

                    errorDiv.textContent = '';

                    if (value < min) {
                        this.value = min;
                        errorDiv.textContent = 'Số lượng không thể nhỏ hơn 1!';
                    } else if (value > max) {
                        this.value = max;
                        errorDiv.textContent = 'Số lượng vượt quá tồn kho!';
                        updateCartItem(id, max); // Cập nhật số lượng mới lên server
                    } else {
                        this.closest(".update-form").submit();
                    }

                    updateTotals();
                });
            });
        });
    </script>

@endsection
