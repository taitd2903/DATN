@extends('layouts.layout')

@section('content')
<h1>Thêm biến thể sản phẩm</h1>

@if($errors->has('duplicate'))
    <div class="alert alert-danger">
        {{ $errors->first('duplicate') }}
    </div>
@endif

<form action="{{ route('admin.variants.store', $product->id) }}" method="POST" enctype="multipart/form-data" class="container mt-4 p-4 border rounded shadow">
    @csrf

    {{-- Nếu có biến thể có size thì hiển thị trường size --}}
    <div class="row">
    @if($hasSizeVariants)
        <div class="mb-3 col-md-6">
            <label class="form-label">Size:</label>
            <input type="text" name="size" class="form-control" value="{{ old('size') }}">
            @error('size')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    @endif

    <div class="mb-3 col-md-6">
        <label class="form-label">Màu:</label>
        <input 
            type="color" 
            name="color" 
            class="form-control form-control-color"  
            value="{{ old('color', '#000000') }}" 
            required
        >
        @error('color')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>


    <div class="mb-3">
        <label class="form-label">Giá gốc:</label>
        <input type="number" step="0.01" name="original_price" class="form-control" value="{{ old('original_price') }}" required>
        @error('original_price')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Giá bán:</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
        @error('price')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Tồn kho:</label>
        <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity') }}" required min="0">
        @error('stock_quantity')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Ảnh biến thể:</label>
        <input type="file" name="image" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Thêm biến thể</button>
    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-secondary mt-3">Quay lại</a>
</form>
@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const imageInput = document.querySelector('input[name="image"]');
        const previewContainer = document.createElement("div");
        previewContainer.classList.add("mt-3");
        
        const previewImage = document.createElement("img");
        previewImage.style.maxWidth = "200px";
        previewImage.style.display = "none"; // Ẩn ảnh ban đầu
        previewContainer.appendChild(previewImage);
        
        imageInput.parentNode.appendChild(previewContainer);

        imageInput.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = "block";
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
