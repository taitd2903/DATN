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
            setTimeout(function () {
                document.getElementById('success-message').style.display = 'none';
            }, 3000);
        </script>
    @endif

    @if ($cartItems->count() > 0)
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Images</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Remove</th>
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
                        <td><img src="{{ asset('storage/' . $item->variant->image) }}" class="cart-image"></td>
                        <td>{{ $item->product->name }}</td>
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
                                <button type="submit" class="btn btn-danger">×</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Order Summary</h3>
            <p>Sub Total: <span>{{ number_format($total, 0, ',', '.') }} đ</span></p>
            <p>Shipping Cost: <span>Free</span></p>
            <p class="grand-total">Grand Total: <span>{{ number_format($total - 40 - 10 + 2, 0, ',', '.') }} đ</span></p>
            <div class="cart-actions">
            <a href="{{ route('checkout') }}" class="btn btn-checkout">Checkout</a>
        </div>
        </div>

        <div class="cart-actions">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Tiếp tục mua hàng</a>
        </div>
        
    @else
        <p>Giỏ hàng của bạn đang trống.</p>
    @endif
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".increment, .decrement").forEach(button => {
            button.addEventListener("click", function() {
                let id = this.getAttribute("data-id");
                let input = document.querySelector(`.quantity-input[data-id='${id}']`);
                let max = parseInt(input.getAttribute("max"));
                let min = parseInt(input.getAttribute("min"));
                let currentValue = parseInt(input.value);

                if (this.classList.contains("increment") && currentValue < max) {
                    input.value = currentValue + 1;
                } else if (this.classList.contains("decrement") && currentValue > min) {
                    input.value = currentValue - 1;
                }

                input.closest(".update-form").submit();
            });
        });
    });
</script>

@endsection