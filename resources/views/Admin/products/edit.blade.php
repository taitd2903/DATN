@extends('layouts.layout')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Chỉnh sửa sản phẩm</h1>

        <!-- Form chỉnh sửa sản phẩm -->
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm:</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ $product->name }}" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ảnh:</label>
                <input type="text" class="form-control" name="image" id="image" value="{{ $product->image }}" placeholder="URL ảnh sản phẩm">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả:</label>
                <textarea class="form-control" name="description" id="description" placeholder="Nhập mô tả sản phẩm">{{ $product->description }}</textarea>
            </div>

            <div class="mb-3">
    <label for="category_id" class="form-label">Danh mục:</label>
    <select class="form-select" name="category_id" id="category_id">
        <option value="">Không chọn danh mục</option> <!-- Tùy chọn không chọn danh mục -->
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @if($category->children->count())
                @foreach($category->children as $child)
                    <option value="{{ $child->id }}" {{ $product->category_id == $child->id ? 'selected' : '' }}>-- {{ $child->name }}</option>
                @endforeach
            @endif
        @endforeach
    </select>
</div>


            <div class="mb-3">
                <label for="gender" class="form-label">Giới tính:</label>
                <select class="form-select" name="gender" id="gender" required>
                    <option value="male" {{ $product->gender == 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ $product->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                    <option value="unisex" {{ $product->gender == 'unisex' ? 'selected' : '' }}>Unisex</option>
                </select>
            </div>

            <h3>Chỉnh sửa biến thể:</h3>
            <div id="variant-container">
                @foreach($product->variants as $index => $variant)
                    <div class="variant mb-3 row" id="variant-{{ $index }}">
                        <div class="col-md-2">
                            <label>Size:</label>
                            <input type="text" class="form-control" name="variants[{{ $index }}][size]" value="{{ $variant->size }}">
                        </div>
                        
                        <div class="col-md-2">
                            <label>Màu:</label>
                            <input type="text" class="form-control" name="variants[{{ $index }}][color]" value="{{ $variant->color }}" required>
                        </div>
                        
                        <div class="col-md-2">
                            <label>Giá gốc:</label>
                            <input type="number" class="form-control" step="0.01" name="variants[{{ $index }}][original_price]" value="{{ $variant->original_price }}" required>
                        </div>
                        
                        <div class="col-md-2">
                            <label>Giá bán:</label>
                            <input type="number" class="form-control" step="0.01" name="variants[{{ $index }}][price]" value="{{ $variant->price }}" required>
                        </div>
                        
                        <div class="col-md-2">
                            <label>Số lượng tồn kho:</label>
                            <input type="number" class="form-control" name="variants[{{ $index }}][stock_quantity]" value="{{ $variant->stock_quantity }}" required min="0">
                        </div>
                        
                        <div class="col-md-2">
                            <label>Số lượng đã bán:</label>
                            <input type="number" class="form-control" name="variants[{{ $index }}][sold_quantity]" value="{{ $variant->sold_quantity }}" required min="0">
                        </div>
                        
                        <div class="col-md-2">
                            <label>Ảnh biến thể:</label>
                            <input type="text" class="form-control" name="variants[{{ $index }}][image]" value="{{ $variant->image }}" oninput="previewVariantImage({{ $index }})" placeholder="URL ảnh biến thể">
                            <img id="preview-{{ $index }}" src="{{ $variant->image }}" alt="Ảnh biến thể" class="img-thumbnail mt-2" style="max-width: 100px;">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger" onclick="removeVariant('variant-{{ $index }}')">Xóa</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-primary mb-3" onclick="addVariant()">Thêm biến thể</button>

            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-success">Cập nhật</button>
            </div>
        </form>
    </div>

    <script>
        let variantIndex = {{ $product->variants->count() }};

        function addVariant() {
            let container = document.getElementById('variant-container');
            let variantId = `variant-${variantIndex}`;
            let html = `
                <div class="variant mb-3 row" id="${variantId}">
                    <div class="col-md-2">
                        <label>Size:</label>
                        <input type="text" class="form-control" name="variants[${variantIndex}][size]">
                    </div>
                    <div class="col-md-2">
                        <label>Màu:</label>
                        <input type="text" class="form-control" name="variants[${variantIndex}][color]" required>
                    </div>
                    <div class="col-md-2">
                        <label>Giá bán:</label>
                        <input type="number" class="form-control" step="0.01" name="variants[${variantIndex}][price]" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger" onclick="removeVariant('${variantId}')">Xóa</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            variantIndex++;
        }

        function removeVariant(variantId) {
            document.getElementById(variantId).remove();
        }
    </script>
@endsection