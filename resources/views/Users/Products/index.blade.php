{{-- @extends('layouts.app')

@section('content') --}}
    <h1>Danh sách sản phẩm</h1>
    
    @foreach($products as $product)
        <div class="product">
            <h2>{{ $product->name }}</h2>
            <p>{{ $product->description }}</p>
            <p>Danh mục: {{ $product->category->name }}</p>
            <p>Tổng số lượng: {{ $product->total_quantity }}</p>

            <h4>Biến thể:</h4>
            <ul>
                @foreach($product->variants as $variant)
                    <li>
                        <strong>Size:</strong> {{ $variant->size }} 
                        <strong>Màu sắc:</strong> {{ $variant->color }} 
                        <strong>Số lượng:</strong> {{ $variant->quantity }}
                    </li>
                @endforeach
            </ul>

            <a href="{{ route('products.show', $product->id) }}">Xem chi tiết</a>
        </div>
    @endforeach
{{-- @endsection --}}
