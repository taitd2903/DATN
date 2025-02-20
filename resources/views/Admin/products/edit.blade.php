{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
<h1>Sửa sản phẩm</h1>

<form action="{{ route('admin.products.update', $product->id) }}" method="POST">
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

    <!-- Thêm chọn Giới tính -->
    <label>Giới tính:</label>
    <select name="gender" required>
        <option value="male" {{ old('gender', $product->gender) == 'male' ? 'selected' : '' }}>Nam</option>
        <option value="female" {{ old('gender', $product->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
        <option value="unisex" {{ old('gender', $product->gender) == 'unisex' ? 'selected' : '' }}>Unisex</option>
    </select>

    <h3>Biến thể sản phẩm:</h3>
    <div id="variant-container">
        @foreach($product->variants as $index => $variant)
            <div class="variant" id="variant-{{ $variant->id }}">
                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

                <label>Size:</label>
                <input type="text" name="variants[{{ $index }}][size]" value="{{ old('variants.' . $index . '.size', $variant->size) }}">

                <label>Màu:</label>
                <input type="text" name="variants[{{ $index }}][color]" value="{{ old('variants.' . $index . '.color', $variant->color) }}" required>

                <label>Giá:</label>
                <input type="number" step="0.01" name="variants[{{ $index }}][price]" value="{{ old('variants.' . $index . '.price', $variant->price) }}" required>

                <label>Số lượng tồn kho:</label>
                <input type="number" name="variants[{{ $index }}][stock_quantity]" value="{{ old('variants.' . $index . '.stock_quantity', $variant->stock_quantity) }}" required min="0">

                <label>Số lượng đã bán:</label>
                <input type="number" name="variants[{{ $index }}][sold_quantity]" value="{{ old('variants.' . $index . '.sold_quantity', $variant->sold_quantity) }}" required min="0">

                <button type="button" onclick="deleteVariant(this, {{ $variant->id }})" class="delete-btn">Xóa</button>
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
    let variantIndex = {{ count($product->variants) }}; // Bắt đầu từ số lượng biến thể hiện có

    function addVariant() {
        let container = document.getElementById('variant-container');
        let html = `
            <div class="variant" id="variant-new-${variantIndex}">
                <label>Size:</label>
                <input type="text" name="variants[${variantIndex}][size]">
                <label>Màu:</label>
                <input type="text" name="variants[${variantIndex}][color]" required>
                <label>Giá:</label>
                <input type="number" step="0.01" name="variants[${variantIndex}][price]" required>
                <label>Số lượng tồn kho:</label>
                <input type="number" name="variants[${variantIndex}][stock_quantity]" required min="0">
                <label>Số lượng đã bán:</label>
                <input type="number" name="variants[${variantIndex}][sold_quantity]" required min="0">
                <button type="button" onclick="deleteVariant(this, null)" class="delete-btn">Xóa</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        variantIndex++;
    }

    function deleteVariant(button, variantId) {
        if (confirm('Bạn có chắc muốn xóa biến thể này?')) {
            let variantElement = button.parentElement;

            if (variantId) {
                // Nếu là biến thể đã lưu trong DB, gửi request xóa
                let form = document.getElementById('delete-variant-form');
                form.action = `/admin/products/{{ $product->id }}/variants/${variantId}`;
                form.submit();
            } else {
                // Nếu là biến thể mới thêm, chỉ cần xóa khỏi DOM
                variantElement.remove();
            }
        }
    }
</script>
{{-- @endsection --}}
