@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h2>Chi tiết sản phẩm: {{ $product->name }}</h2>
   <h4>Mô tả: <span class=""> {{ $product->description }}</span></h4>

    <!-- Hiển thị ảnh sản phẩm chính -->
    @if($product->image)
        <div class="mb-3">
            <img src="{{ asset('storage/' . $product->image) }}" alt="Ảnh sản phẩm" class="img-fluid" style="max-width: 300px;">
        </div>
    @endif

    <div class="mb-3">
        <strong>Danh mục:</strong> {{ $product->category?->name ?? 'Không có danh mục' }} <br>
        <strong>Giới tính:</strong> 
        @if($product->gender == 'male') Nam
        @elseif($product->gender == 'female') Nữ
        @else Unisex
        @endif
    </div>

    <h4>Danh sách biến thể</h4>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Ảnh</th>
                <th>Size</th>
                <th>Màu sắc</th>
                <th>Giá gốc (VND)</th>
                <th>Giá bán (VND)</th>
                <th>Tồn kho</th>
                <th>Đã bán</th>
            </tr>
        </thead>
        <tbody>
            @foreach($product->variants as $variant)
            <tr>
                <td>
                    @if($variant->image)
                        <img src="{{ asset('storage/' . $variant->image) }}" alt="Ảnh biến thể" class="img-thumbnail" style="max-width: 100px;">
                    @else
                        Không có ảnh
                    @endif
                </td>
                <td>{{ $variant->size }}</td>
                <td><div style="width: 30px; height: 30px; border-radius: 4px; background-color: {{ $variant->color }}; border: 1px solid #ccc;"></div></td>

                <td>{{ number_format($variant->original_price) }}</td>
                <td>{{ number_format($variant->price) }}</td>
                <td>{{ $variant->stock_quantity }}</td>
                <td>{{ $variant->sold_quantity }}</td>
               
            </tr>
            @endforeach
        </tbody>
    </table>


<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        Quay lại danh sách
    </a>

    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning fw-bold">
        ✏️ Sửa sản phẩm
    </a>
</div>



</div>
@endsection
