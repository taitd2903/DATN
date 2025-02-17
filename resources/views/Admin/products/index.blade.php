@extends('layouts.app')

@section('content')
    <h1>Danh sách sản phẩm</h1>
    <p><strong>Tổng số sản phẩm hiện có:</strong> {{ $totalProducts }}</p> <!-- Hiển thị tổng số sản phẩm tính từ tất cả các biến thể -->

    <a href="{{ route('admin.products.create') }}">Thêm sản phẩm</a>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <ul>
        @foreach($products as $product)
            <li>
                <strong>{{ $product->name }}</strong> - {{ $product->description }} - Giá gốc: {{ $product->base_price }} VND
                <p>Danh mục: {{ $product->category->name }}</p>
                <p><strong>Tổng số lượng sản phẩm: </strong>{{ $product->total_quantity }} </p> <!-- Hiển thị tổng số lượng của tất cả biến thể của sản phẩm -->

                <!-- Hiển thị các biến thể (size, color) của sản phẩm -->
                <h4>Biến thể sản phẩm:</h4>
                <ul>
                    @foreach($product->variants as $variant)
                        <li>
                            <strong>Size:</strong> {{ $variant->size }} - <strong>Màu sắc:</strong> {{ $variant->color }} 
                            - <strong>Số lượng:</strong> {{ $variant->quantity }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('admin.products.edit', $product->id) }}">Sửa</a>

                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Xóa</button>
                </form>
            </li>
        @endforeach
    </ul>
@endsection
