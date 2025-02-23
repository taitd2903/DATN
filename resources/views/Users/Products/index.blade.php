@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Danh sách sản phẩm</h1>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($products as $product)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                    @else
                        <div class="p-3 text-center">Chưa có hình ảnh</div>
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ $product->description }}</p>
                        <p><strong>Danh mục:</strong> {{ $product->category ? $product->category->name : 'Chưa có danh mục' }}</p>
                        <p><strong>Tổng số lượng đã bán:</strong> {{ $product->total_sold_quantity }}</p>

                        <h6>Biến thể:</h6>
                        <ul class="list-unstyled">
                            @foreach($product->variants as $variant)
                                <li>
                                    @if(!empty($variant->size))
                                        <span class="badge bg-primary">Size: {{ $variant->size }}</span>
                                    @endif
                                    <span class="badge bg-secondary">Màu: {{ $variant->color }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary mt-2 w-100">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
