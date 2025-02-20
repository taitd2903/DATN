{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
    <h1>Thêm sản phẩm</h1>
    <form action="{{ route('admin.products.store') }}" method="POST">
        @csrf
        <label for="name">Tên sản phẩm:</label>
        <input type="text" name="name" id="name" required>

        <label for="image">Ảnh:</label>
        <input type="text" name="image" id="image" placeholder="URL ảnh sản phẩm">

        <label for="description">Mô tả:</label>
        <textarea name="description" id="description" placeholder="Nhập mô tả sản phẩm"></textarea>

        <label for="base_price">Giá gốc:</label>
        <input type="number" step="0.01" name="base_price" id="base_price" required>

        <label for="category_id">Danh mục:</label>
        <select name="category_id" id="category_id" required>
            <option value="">Chọn danh mục</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @if($category->children->count())
                    @foreach($category->children as $child)
                        <option value="{{ $child->id }}">-- {{ $child->name }}</option>
                    @endforeach
                @endif
            @endforeach
        </select>

        <label for="gender">Giới tính:</label>
<select name="gender" id="gender" required>
    <option value="male">Nam</option>
    <option value="female">Nữ</option>
    <option value="unisex">Unisex</option>
</select>

        <h3>Thêm biến thể:</h3>
        <div id="variant-container"></div>

        <button type="button" onclick="addVariant()">Thêm biến thể</button>
        <button type="submit">Lưu</button>
    </form>

    <script>
        let variantIndex = 0;

        function addVariant() {
            let container = document.getElementById('variant-container');
            let variantId = `variant-${variantIndex}`;
            let html = `
                <div class="variant" id="${variantId}">
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

                    <button type="button" onclick="removeVariant('${variantId}')">Xóa</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            variantIndex++;
        }

        function removeVariant(variantId) {
            document.getElementById(variantId).remove();
            if (document.querySelectorAll('.variant').length === 0) {
                variantIndex = 0;
            }
        }

        // Thêm biến thể mặc định khi trang tải
        addVariant();
    </script>
{{-- @endsection --}}
