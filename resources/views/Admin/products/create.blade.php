{{-- @extends('layouts.app')

@section('content') --}}
    <h1>Thêm sản phẩm</h1>
    <form action="{{ route('admin.products.store') }}" method="POST">
        @csrf
        <label>Tên sản phẩm:</label>
        <input type="text" name="name" required>

        <label>Ảnh:</label>
        <input type="text" name="image">

        <label>Mô tả:</label>
        <textarea name="description"></textarea>

        <label>Giá gốc:</label>
        <input type="number" step="0.01" name="base_price">

        <label>Danh mục:</label>
        <select name="category_id" required>
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

        <h3>Thêm biến thể:</h3>
        <div id="variant-container">
            <div>
                <label>Size:</label>
                <input type="text" name="variants[0][size]">
                <label>Màu:</label>
                <input type="text" name="variants[0][color]">
                <label>Giá:</label>
                <input type="number" step="0.01" name="variants[0][price]">
                <label>Số lượng:</label>
                <input type="number" name="variants[0][quantity]">
            </div>
        </div>

        <button type="button" onclick="addVariant()">Thêm biến thể</button>
        <button type="submit">Lưu</button>
    </form>

    <script>
        let variantIndex = 1;

        function addVariant() {
            let container = document.getElementById('variant-container');
            let html = `
                <div>
                    <label>Size:</label>
                    <input type="text" name="variants[${variantIndex}][size]">
                    <label>Màu:</label>
                    <input type="text" name="variants[${variantIndex}][color]">
                    <label>Giá:</label>
                    <input type="number" step="0.01" name="variants[${variantIndex}][price]">
                    <label>Số lượng:</label>
                    <input type="number" name="variants[${variantIndex}][quantity]">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            variantIndex++;
        }
    </script>
{{-- @endsection --}}
