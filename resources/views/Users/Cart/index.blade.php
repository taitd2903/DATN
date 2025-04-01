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
                        <th></th>
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
                            // $total += $subtotal;
                            $isOutOfStock = $item->variant->stock_quantity == 0;
                        @endphp
                        <tr data-cart-id="{{ $item->id }}">
                            <td><input type="checkbox" class="select-item" data-id="{{ $item->id }}"
                                    {{ $isOutOfStock ? 'disabled' : '' }}></td>
                            <td><img src="{{ asset('storage/' . $item->variant->image) }}" class="cart-image"></td>
                            <td>
                              <b>  {{ $item->product->name }} ({{ $item->variant->size }} / {{ $item->variant->color }}) </b>
                                <span class="stock-warning" style="color: red; display: none;"></span>
                            </td>
                            <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="update-form">
                                    @csrf
                                    @method('PATCH')
                                    <div class="quantity-container">
                                        <button type="button" class="btn decrement" data-id="{{ $item->id }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>-</button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                            max="{{ $item->variant->stock_quantity }}" class="quantity-input"
                                            data-id="{{ $item->id }}" data-price="{{ $item->price }}">
                                        <button type="button" class="btn increment" data-id="{{ $item->id }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>+</button>
                                    </div>
                                    <div class="error-message" style="color: red; font-size: 0.9em; margin-top: 5px;"></div>
                                </form>
                            </td>
                           <td class="subtotal" style="font-weight: bold; color: red;"> {{ number_format($subtotal, 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="remove-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">X</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
              
            </table>
         <div class="select-all-container">
                 <input type="checkbox" id="select-all">
                  <label for="select-all">Chọn tất cả</label>
                </div>
            <!-- <div
                style="font-family: Arial, sans-serif; padding: 10px; margin-top: 15px; background-color: #f4f4f4; border: 1px solid #ccc; border-radius: 5px; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="select-all">Chọn tất cả
            </div> -->
            <div class="cart-summary">
                <h3>Tóm tắt đơn hàng</h3>
                <b><p>Tạm tính: <span class="total-amount">{{ number_format($total, 0, ',', '.') }} đ</span></p></b>
                <div class="cart-actions">
                    <!-- <a href="{{ route('products.index') }}" class="btn btn-secondary">Tiếp tục mua hàng</a> -->
                
                    <a class="back-btn" href="{{ route('products.index') }}">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua hàng
                </a>
                <a class="back-btn" href="{{ route('checkout') }}" id="checkout-btn">
                    Thanh toán <i class="fas fa-arrow-right"></i>
                </a>
                    <!-- <a href="{{ route('checkout') }}" class="btn btn-checkout" id="checkout-btn">Thanh toán</a> -->
                </div>
            </div>
        @else
            <p>Giỏ hàng của bạn đang trống.</p>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Echo.private(`cart.{{ Auth::id() }}`)
                .listen('PriceUpdated', (e) => {
                    const row = document.querySelector(`tr[data-cart-id='${e.id}']`);
                    console.log('Found row:', row);
                    if (row) {
                        const priceCell = row.querySelector('td:nth-child(4)');
                        const subtotalCell = row.querySelector('.subtotal');
                        const input = row.querySelector('.quantity-input');

                        priceCell.textContent = new Intl.NumberFormat('vi-VN').format(e.price) + ' đ';
                        subtotalCell.textContent = new Intl.NumberFormat('vi-VN').format(e.subtotal) + ' đ';
                        input.setAttribute('data-price', e.price);

                        updateTotals();
                    } else {
                        console.log('Row not found for cart ID:', e.id);
                    }
                });


            function updateTotals() {
                let total = 0;
                document.querySelectorAll('tr[data-cart-id]').forEach(row => {
                    const checkbox = row.querySelector('.select-item');
                    const input = row.querySelector('.quantity-input');
                    const price = parseFloat(input.getAttribute('data-price'));
                    const quantity = parseInt(input.value);
                    const subtotal = price * quantity;

                    row.querySelector('.subtotal').textContent = new Intl.NumberFormat('vi-VN').format(
                        subtotal) + ' đ';

                    if (checkbox.checked && !checkbox.disabled) {
                        total += subtotal;
                    }
                });
                document.querySelector('.total-amount').textContent = new Intl.NumberFormat('vi-VN').format(total) +
                    ' đ';
            }

            function saveCheckboxState() {
                const selectedItems = Array.from(document.querySelectorAll('.select-item'))
                    .map(cb => ({
                        id: cb.getAttribute('data-id'),
                        checked: cb.checked
                    }));
                localStorage.setItem('cartSelections', JSON.stringify(selectedItems));
            }

            const selections = JSON.parse(localStorage.getItem('cartSelections')) || [];
            selections.forEach(selection => {
                const checkbox = document.querySelector(`.select-item[data-id='${selection.id}']`);
                if (checkbox) checkbox.checked = selection.checked;
            });

            document.getElementById('select-all').addEventListener('change', function() {
                const isChecked = this.checked;
                document.querySelectorAll('.select-item').forEach(checkbox => {
                    if (!checkbox.disabled) {
                        checkbox.checked = isChecked;
                    }
                });
                updateTotals();
                saveCheckboxState();
            });

            document.querySelectorAll('.select-item').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = document.querySelectorAll('.select-item').length ===
                        document.querySelectorAll('.select-item:checked').length;
                    document.getElementById('select-all').checked = allChecked;
                    updateTotals();
                    saveCheckboxState();
                });
            });

            function checkStock() {
                fetch("{{ route('cart.checkStock') }}", {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Object.keys(data).forEach(cartId => {
                            const row = document.querySelector(`tr[data-cart-id='${cartId}']`);
                            const input = row.querySelector('.quantity-input');
                            const stock = data[cartId].stock_quantity;
                            const quantity = data[cartId].quantity;
                            const stockWarning = row.querySelector('.stock-warning');
                            const checkbox = row.querySelector('.select-item');
                            const decrementBtn = row.querySelector('.decrement');
                            const incrementBtn = row.querySelector('.increment');

                            input.setAttribute('max', stock);
                            if (stock === 0) {
                                checkbox.disabled = true;
                                decrementBtn.disabled = true;
                                incrementBtn.disabled = true;
                                input.value = 0;
                                stockWarning.style.display = 'inline';
                                stockWarning.textContent = `(Hết hàng)`;
                                if (checkbox.checked) {
                                    checkbox.checked = false;
                                    saveCheckboxState();
                                }
                            } else if (quantity > stock) {
                                input.value = stock;
                                stockWarning.style.display = 'inline';
                                stockWarning.textContent = `(Chỉ còn ${stock} trong kho)`;
                                updateCartItem(cartId, stock);
                            } else {
                                stockWarning.style.display = 'none';
                                checkbox.disabled = false;
                                decrementBtn.disabled = false;
                                incrementBtn.disabled = false;
                                if (quantity < 1) {
                                    input.value = 1;
                                    updateCartItem(cartId, 1);
                                } else {
                                    input.value = quantity;
                                }
                            }
                            updateTotals();
                        });
                    })
                    .catch(error => console.error('Error checking stock:', error));
            }

            function updateCartItem(cartId, quantity) {
                const form = document.querySelector(`tr[data-cart-id='${cartId}'] .update-form`);
                const formData = new FormData(form);
                formData.set('quantity', quantity);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-HTTP-Method-Override': 'PATCH'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        console.log('Cart updated:', data);
                        updateTotals();
                    })
                    .catch(error => console.error('Error updating cart:', error));
            }
            checkStock();
            // setInterval(checkStock, 3000);
            document.getElementById('checkout-btn').addEventListener('click', function(e) {
                e.preventDefault();
                fetch("{{ route('cart.checkStock') }}", {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        let canCheckout = true;
                        document.querySelectorAll('.stock-warning').forEach(span => {
                            span.style.display = 'none';
                            span.textContent = '';
                        });

                        Object.keys(data).forEach(cartId => {
                            const stock = data[cartId].stock_quantity;
                            const quantity = data[cartId].quantity;
                            const row = document.querySelector(`tr[data-cart-id='${cartId}']`);
                            const stockWarning = row.querySelector('.stock-warning');
                            const checkbox = row.querySelector('.select-item');
                            const input = row.querySelector('.quantity-input');
                            const decrementBtn = row.querySelector('.decrement');
                            const incrementBtn = row.querySelector('.increment');

                            if (stock === 0) {
                                checkbox.disabled = true;
                                decrementBtn.disabled = true;
                                incrementBtn.disabled = true;
                                input.value = 0;
                                stockWarning.style.display = 'inline';
                                stockWarning.textContent = `(Hết hàng)`;
                                if (checkbox.checked) {
                                    checkbox.checked = false;
                                    canCheckout = false;
                                }
                            } else if (checkbox.checked && quantity > stock) {
                                canCheckout = false;
                                stockWarning.style.display = 'inline';
                                stockWarning.textContent = `(Chỉ còn ${stock} trong kho)`;
                                input.value = stock;
                                updateCartItem(cartId, stock);
                            }
                        });

                        if (canCheckout) {
                            const selectedItems = Array.from(document.querySelectorAll(
                                    '.select-item:checked'))
                                .map(checkbox => checkbox.getAttribute('data-id'));
                            if (selectedItems.length > 0) {
                                const url = "{{ route('checkout') }}?items=" + selectedItems.join(',');
                                window.location.href = url;
                            } else {
                                alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
                            }
                        }
                        updateTotals();
                    });
            });

            function removeCartItem(cartId) {
                const form = document.querySelector(`tr[data-cart-id='${cartId}'] .remove-form`);
                if (!form) {
                    console.error('Form not found for cart ID:', cartId);
                    return;
                }
                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-HTTP-Method-Override': 'DELETE'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.error || 'Có lỗi khi xóa sản phẩm');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Cart item removed:', data);
                        const row = document.querySelector(`tr[data-cart-id='${cartId}']`);
                        if (row) row.remove();
                        updateTotals();
                        const allChecked = document.querySelectorAll('.select-item').length ===
                            document.querySelectorAll('.select-item:checked').length;
                        document.getElementById('select-all').checked = allChecked;
                        const remainingItems = document.querySelectorAll('.select-item').length;
                        if (remainingItems === 0) localStorage.removeItem('cartSelections');
                        saveCheckboxState();
                    })
                    .catch(error => {
                        console.error('Error removing cart item:', error.message);
                        alert(error.message);
                    });
            }

            document.querySelectorAll('tr[data-cart-id] form button[type="submit"]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Delete button clicked');
                    const cartId = this.closest('tr[data-cart-id]').getAttribute('data-cart-id');
                    console.log('Removing cart ID:', cartId);
                    removeCartItem(cartId);
                });
            });
            document.querySelectorAll(".increment, .decrement").forEach(button => {
                button.addEventListener("click", function() {
                    let id = this.getAttribute("data-id");
                    let input = document.querySelector(`.quantity-input[data-id='${id}']`);
                    let max = parseInt(input.getAttribute("max"));
                    let min = parseInt(input.getAttribute("min"));
                    let currentValue = parseInt(input.value);
                    let errorDiv = input.closest('.quantity-container').nextElementSibling;

                    errorDiv.textContent = '';
                    if (max === 0) {
                        errorDiv.textContent = 'Sản phẩm đã hết hàng!';
                        return;
                    }
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

                    updateCartItem(id, input.value);
                });
            });

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
                        updateCartItem(id, max);
                    } else {
                        updateCartItem(id, value);
                    }
                });
            });
            updateTotals();
        });
    </script>
@include('Users.chat')
@endsection
