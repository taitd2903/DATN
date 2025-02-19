{{-- @extends('layouts.app')

@section('content') --}}
<h1>Giỏ hàng của bạn</h1>

@if ($cartItems->isEmpty())
    <p>Giỏ hàng trống.</p>
@else
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Thông tin sản phẩm</th>
                <th>Số lượng</th>
                <th>Thao tác</th>
                <th>Giá</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cartItems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>Size: {{ $item->variant->size }}, Màu: {{ $item->variant->color }}</td>
                    <td>
                        {{-- <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                            <span>{{ $item->quantity }}</span>
                            <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}">+</button>
                        </form> --}}
                        <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="quantity" value="{{ $item->quantity - 1 }}">
                            <button type="submit" {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                        </form>

                        <span>{{ $item->quantity }}</span>

                        <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                            <button type="submit">+</button>
                        </form>

                    </td>
                    <td>
                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                            @csrf
                            {{-- @method('DELETE') --}}
                            <button type="submit">❌ Xoá</button>
                        </form>
                    </td>
                    <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    @if(session('success'))
    <div class="alert alert-danger">
        {{ session('success') }}
    </div>
@endif
    <h3>Tổng tiền: {{ number_format($cartItems->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }} đ
    </h3>

    <button>🛒 Thanh toán</button>
@endif

<a href="{{ route('products.index') }}">Tiếp tục mua sắm</a>
{{-- @endsection --}}
