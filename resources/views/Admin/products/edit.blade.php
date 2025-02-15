{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
<h1>Sửa sản phẩm</h1>

<form action="{{ route('products.update', $product->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Tên sản phẩm:</label>
    <input type="text" name="name" value="{{ old('name', $product->name) }}" required>

    <label>Ảnh:</label>
    <input type="text" name="image" value="{{ old('image', $product->image) }}">

    <label>Mô tả:</label>
    <textarea name="description">{{ old('description', $product->description) }}</textarea>

    <label>Giá gốc:</label>
    <input type="number" step="1" name="base_price" value="{{ old('base_price', $product->base_price) }}" required>

    <label>Danh mục:</label>
    <select name="category_id" required>
        <option value="">Chọn danh mục</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <h3>Biến thể sản phẩm:</h3>
    <div id="variant-container">
        @foreach($product->variants as $index => $variant)
            <div id="variant-{{ $variant->id }}">
                <label>Size:</label>
                <input type="text" name="variants[{{ $index }}][size]" value="{{ old('variants.' . $index . '.size', $variant->size) }}" required>

                <label>Màu:</label>
                <input type="text" name="variants[{{ $index }}][color]" value="{{ old('variants.' . $index . '.color', $variant->color) }}" required>

                <label>Giá:</label>
                <input type="number" step="0.01" name="variants[{{ $index }}][price]" value="{{ old('variants.' . $index . '.price', $variant->price) }}" required>

                <label>Số lượng:</label>
                <input type="number" name="variants[{{ $index }}][quantity]" value="{{ old('variants.' . $index . '.quantity', $variant->quantity) }}" required>

                <!-- Nút Xóa biến thể (tách riêng form) -->
                <button type="button" onclick="deleteVariant({{ $variant->id }})" class="delete-btn">Xóa</button>
            </div>
        @endforeach
    </div>

    <button type="button" onclick="addVariant()">Thêm biến thể</button>
    <button type="submit">Cập nhật sản phẩm</button>
</form>

<!-- Form xóa (ẩn) -->
<form id="delete-variant-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    let variantIndex = {{ count($product->variants) }};

    function addVariant() {
        let container = document.getElementById('variant-container');
        let html = `
            <div>
                <label>Size:</label>
                <input type="text" name="variants[${variantIndex}][size]" required>
                <label>Màu:</label>
                <input type="text" name="variants[${variantIndex}][color]" required>
                <label>Giá:</label>
                <input type="number" step="0.01" name="variants[${variantIndex}][price]" required>
                <label>Số lượng:</label>
                <input type="number" name="variants[${variantIndex}][quantity]" required>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        variantIndex++;
    }

    function deleteVariant(variantId) {
        if (confirm('Bạn có chắc muốn xóa biến thể này?')) {
            let form = document.getElementById('delete-variant-form');
            form.action = `/admin/products/{{ $product->id }}/variants/${variantId}`;
            form.submit();
        }
    }
</script>
{{-- @endsection --}}
