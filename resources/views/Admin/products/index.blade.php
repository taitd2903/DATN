@extends('layouts.app')

@section('content')
    <h1>Danh sách sản phẩm</h1>

    <a href="{{ route('admin.products.create') }}">Thêm sản phẩm</a>

    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <ul>
        @foreach($products as $product)
            <li>
                <strong>{{ $product->name }}</strong> - {{ $product->description }} - Giá gốc: {{ $product->base_price }} VND
                <p>Danh mục: {{ $product->category->name }}</p>
                <p><strong>Giới tính:</strong> 
                    @if($product->gender == 'male')
                        Nam
                    @elseif($product->gender == 'female')
                        Nữ
                    @else
                        Unisex
                    @endif
                </p>
                <p><strong>Tổng số lượng sản phẩm:</strong> {{ $product->total_quantity }}</p>
                <p><strong>Số lượng tồn kho:</strong> {{ $product->total_stock }}</p> <!-- Hiển thị số lượng tồn kho -->
                <p><strong>Đã bán:</strong> {{ $product->total_sold }}</p> <!-- Hiển thị số lượng đã bán -->

                <h4>Biến thể sản phẩm:</h4>
                <ul>
                    @foreach($product->variants as $variant)
                        <li>
                            <strong>Size:</strong> {{ $variant->size }} - 
                            <strong>Màu sắc:</strong> {{ $variant->color }} - 
                            <strong>Giá:</strong> {{ $variant->price }} VND - 
                            <strong>Tồn kho:</strong> {{ $variant->stock_quantity }} - 
                            <strong>Đã bán:</strong> {{ $variant->sold_quantity }}
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
