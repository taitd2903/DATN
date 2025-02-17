{{-- @extends('layouts.app')

@section('content') --}}
    <h1>{{ $product->name }}</h1>
    <p>{{ $product->description }}</p>
    <p>Danh mục: {{ $product->category->name }}</p>

    <h4>Biến thể:</h4>
    <ul>
        @foreach($product->variants as $variant)
            <li>
                <strong>Size:</strong> {{ $variant->size }} 
                <strong>Màu sắc:</strong> {{ $variant->color }} 
                <strong>Số lượng:</strong> {{ $variant->quantity }}
                <strong>Gía</strong> {{ $variant->price }}
            </li>
        @endforeach
    </ul>
    
    <a href="{{ route('products.index') }}">Quay lại danh sách sản phẩm</a>
{{-- @endsection --}}
