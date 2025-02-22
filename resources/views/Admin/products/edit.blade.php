@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h2>Chỉnh sửa sản phẩm</h2>
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Tên sản phẩm -->
        <div class="mb-3">
            <label for="name" class="form-label">Tên sản phẩm</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
        </div>

        <!-- Ảnh sản phẩm -->
        <div class="mb-3">
            <label for="image" class="form-label">Ảnh sản phẩm</label>
            <input type="file" class="form-control" id="image" name="image">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="Ảnh sản phẩm" class="img-thumbnail mt-2" width="150">
            @endif
        </div>

        <!-- Danh mục -->
        <div class="mb-3">
            <label for="category_id" class="form-label">Danh mục</label>
            <select class="form-control" id="category_id" name="category_id">
                <option value="">Chọn danh mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Giới tính -->
        <div class="mb-3">
            <label class="form-label">Giới tính</label>
            <select class="form-control" name="gender" required>
                <option value="male" {{ $product->gender == 'male' ? 'selected' : '' }}>Nam</option>
                <option value="female" {{ $product->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                <option value="unisex" {{ $product->gender == 'unisex' ? 'selected' : '' }}>Unisex</option>
            </select>
        </div>

        <!-- Biến thể sản phẩm -->
        <div class="mb-3">
            <label class="form-label">Biến thể</label>
            <div id="variants-container">
                @foreach($product->variants as $index => $variant)
                    <div class="variant-item border p-3 mb-3">
                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                        
                        <label>Màu sắc</label>
                        <input type="text" class="form-control mb-2" name="variants[{{ $index }}][color]" value="{{ $variant->color }}" required>

                        <label>Giá gốc</label>
                        <input type="number" class="form-control mb-2" name="variants[{{ $index }}][original_price]" value="{{ $variant->original_price }}" required>

                        <label>Giá bán</label>
                        <input type="number" class="form-control mb-2" name="variants[{{ $index }}][price]" value="{{ $variant->price }}" required>

                        <label>Số lượng trong kho</label>
                        <input type="number" class="form-control mb-2" name="variants[{{ $index }}][stock_quantity]" value="{{ $variant->stock_quantity }}" required>

                        <label>Số lượng đã bán</label>
                        <input type="number" class="form-control mb-2" name="variants[{{ $index }}][sold_quantity]" value="{{ $variant->sold_quantity }}" required>

                        <label>Ảnh biến thể</label>
                        <input type="file" class="form-control mb-2" name="variants[{{ $index }}][image]">
                        @if($variant->image)
                            <img src="{{ asset('storage/' . $variant->image) }}" class="img-thumbnail" width="100">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection