@extends('layouts.layout')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Thêm sản phẩm</h1>

        <!-- Form tạo sản phẩm -->
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm:</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ảnh:</label>
                <input type="file" class="form-control" name="image" id="image" accept="image/*">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả:</label>
                <textarea class="form-control" name="description" id="description" placeholder="Nhập mô tả sản phẩm"></textarea>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Danh mục:</label>
                <select class="form-select" name="category_id" id="category_id" >
                    <option value="">Không chọn danh mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @if($category->children->count())
                            @foreach($category->children as $child)
                                <option value="{{ $child->id }}">-- {{ $child->name }}</option>
                            @endforeach
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label">Giới tính:</label>
                <select class="form-select" name="gender" id="gender" required>
                    <option value="male">Nam</option>
                    <option value="female">Nữ</option>
                    <option value="unisex">Unisex</option>
                </select>
            </div>

            <h3>Thêm biến thể:</h3>
            <div id="variant-container"></div>

            <button type="button" class="btn btn-primary mb-3" onclick="addVariant()">Thêm biến thể</button>

            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-success">Lưu</button>
            </div>
        </form>
    </div>

    <script>
        let variantIndex = 0;

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
                        <label>Giá gốc:</label>
                        <input type="number" class="form-control" step="0.01" name="variants[${variantIndex}][original_price]" required>
                    </div>

                    <div class="col-md-2">
                        <label>Giá bán:</label>
                        <input type="number" class="form-control" step="0.01" name="variants[${variantIndex}][price]" required>
                    </div>

                    <div class="col-md-2">
                        <label>Số lượng tồn kho:</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][stock_quantity]" required min="0">
                    </div>

                    <div class="col-md-2">
                        <label>Số lượng đã bán:</label>
                        <input type="number" class="form-control" name="variants[${variantIndex}][sold_quantity]" required min="0">
                    </div>

                    <div class="col-md-2">
                        <label>Ảnh biến thể:</label>
                        <input type="file" class="form-control" name="variants[${variantIndex}][image]" accept="image/*">
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
            if (document.querySelectorAll('.variant').length === 0) {
                variantIndex = 0;
            }
        }

        // Thêm biến thể mặc định khi trang tải
        addVariant();
    </script>
@endsection
