@extends('layouts.app')

@section('content')
    <meta name="viewport" content="width=device-width, initial-scale=1">

  <section class="shopping-cart spad">
    <div class="container">
        <h2 style="text-align:center">{{ __('messages.your_cart') }}</h2>
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
        <div class="row">
            <div class="col-lg-8">
                <div class="shopping__cart__table">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>{{ __('messages.image') }}</th>
                                <th>{{ __('messages.product') }}</th>
                                <th>{{ __('messages.price') }}</th>
                                <th>Số lượng</th>
                                <th>{{ __('messages.total') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach ($cartItems as $item)
                                @php
                                    $subtotal = $item->quantity * $item->price;
                                    $total += $subtotal;
                                    $isOutOfStock = $item->variant->stock_quantity == 0;
                                @endphp
                                <tr data-cart-id="{{ $item->id }}">
                                    <td><input type="checkbox" class="select-item" data-id="{{ $item->id }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}></td>
                                    <td class="product__cart__item__pic">
                                        <img src="{{ asset('storage/' . $item->variant->image) }}" alt="" class="cart-image" style="max-width: 80px;">
                                    </td>
                                    <td class="product__cart__item__text">
                                        <b>{{ $item->product->name }} <br> {{ $item->variant->size }} / 
                                            <span style="display: inline-flex; align-items: center;">
                                                <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background-color: {{ $item->variant->color }}; border: 1px solid #ccc; margin-right: 4px;"></span>
                                            </span>
                                        </b>
                                        <span class="stock-warning" style="color: red; display: none;"></span>
                                    </td>

                                    <td class="cart__price">{{ number_format($item->price, 0, ',', '.') }} đ</td>

                                    <td class="quantity__item">
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
                                    <td class="cart__price subtotal" style="font-weight: bold; color: red;">{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                                    <td class="cart__close">
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
                </div>

                <div class="select-all-container" style="margin-top: 10px;">
                    <!-- <input type="checkbox" id="select-all"> -->
                    <label for="select-all">Chọn tất cả</label>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-6">
                        <div class="continue__btn">
                            <a href="{{ route('products.index') }}"><i class="fas fa-arrow-left"></i> {{ __('messages.continue_shopping') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart__total">
                    <h6>{{ __('messages.order_summary') }}</h6>
                    <ul>
                        <li>{{ __('messages.subtotal') }} <span class="total-amount">{{ number_format($total, 0, ',', '.') }} đ</span></li>
                        <!-- <li>{{ __('messages.total') }} <span class="total-amount">{{ number_format($total, 0, ',', '.') }} đ</span></li> -->
                    </ul>
                    <div class="terms-checkbox" style="margin-bottom: 15px;">
                        <input type="checkbox" id="terms-agree">
                        <label for="terms-agree" style="font-size: 0.9em;display: inline;">
                            Tôi đồng ý với điều khoản dịch vụ: Chỉ được hoàn hàng khi sản phẩm bị lỗi hoặc hỏng do nhà sản xuất.
                        </label>
                    </div>
                    <a href="{{ route('checkout') }}" class="primary-btn" id="checkout-btn" disabled style="opacity: 0.6; cursor: not-allowed;">{{ __('messages.checkout') }}</a>
                    {{-- <a href="{{ route('checkout') }}" class="primary-btn" id="checkout-btn">{{ __('messages.checkout') }}</a> --}}
                </div>
            </div>
        </div>
        @else
            <p>{{ __('messages.cart_empty') }}</p>
        @endif
    </div>
</section>

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
                        showNotification('Giá sản phẩm trong giỏ hàng đã được cập nhật!');
                    } else {
                        console.log('Row not found for cart ID:', e.id);
                    }
                });
            function showNotification(message) {
                    let notif = document.createElement('div');
                    notif.textContent = message;
                    notif.style.position = 'fixed';
                    notif.style.top = '20px';
                    notif.style.right = '20px';
                    notif.style.background = '#38c172';
                    notif.style.color = '#fff';
                    notif.style.padding = '10px 20px';
                    notif.style.borderRadius = '8px';
                    notif.style.boxShadow = '0 0 10px rgba(0,0,0,0.15)';
                    notif.style.zIndex = '9999';
                    document.body.appendChild(notif);
                    setTimeout(() => notif.remove(), 5000);
                }

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

            // Handle terms checkbox
                const termsCheckbox = document.getElementById('terms-agree');
                const checkoutBtn = document.getElementById('checkout-btn');

                termsCheckbox.addEventListener('change', function() {
                    checkoutBtn.disabled = !this.checked;
                    if (this.checked) {
                        checkoutBtn.style.opacity = '1';
                        checkoutBtn.style.cursor = 'pointer';
                    } else {
                        checkoutBtn.style.opacity = '0.6';
                        checkoutBtn.style.cursor = 'not-allowed';
                    }
                });
            document.getElementById('checkout-btn').addEventListener('click', function(e) {
                e.preventDefault();
                if (!termsCheckbox.checked) {
                alert('Vui lòng đồng ý với điều khoản dịch vụ trước khi thanh toán!');
                return;
            }
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
