{{-- @extends('layouts.app') --}}

@section('content')
    <h1>Danh sách sản phẩm</h1>
    
    @foreach($products as $product)
        <div class="product">
            <h2>{{ $product->name }}</h2>
            <p>{{ $product->description }}</p>
            <p>Danh mục: {{ $product->category->name }}</p>
            <p><strong>Tổng số lượng đã bán:</strong> {{ $product->total_sold_quantity }}</p>

            <h4>Biến thể:</h4>
            <ul>
                @foreach($product->variants as $variant)
                    <li>
                        <strong>Size:</strong> {{ $variant->size }} 
                        <strong>Màu sắc:</strong> {{ $variant->color }} 
                    </li>
                @endforeach
            </ul>

            <a href="{{ route('products.show', $product->id) }}">Xem chi tiết</a>
        </div>
    @endforeach
{{-- @endsection --}}
