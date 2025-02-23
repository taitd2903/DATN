@extends('layouts.layout')

@section('content')
<h1>Sửa sản phẩm</h1>

<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="container mt-4 p-4 border rounded shadow">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Tên sản phẩm:</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Mô tả:</label>
        <textarea name="description" class="form-control" required>{{ old('description', $product->description) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Ảnh:</label>
        <input type="file" name="image" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Danh mục:</label>
        <select name="category_id" class="form-select">
            <option value="">Chọn danh mục</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Giới tính:</label>
        <select name="gender" class="form-select" required>
            <option value="male" {{ old('gender', $product->gender) == 'male' ? 'selected' : '' }}>Nam</option>
            <option value="female" {{ old('gender', $product->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
            <option value="unisex" {{ old('gender', $product->gender) == 'unisex' ? 'selected' : '' }}>Unisex</option>
        </select>
    </div>

    <h3 class="mt-4">Biến thể sản phẩm:</h3>
    <div id="variant-container">
        @foreach($product->variants as $index => $variant)
            <div class="variant border p-3 mb-3 rounded">
                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Size:</label>
                        <input type="text" name="variants[{{ $index }}][size]" class="form-control" value="{{ old('variants.' . $index . '.size', $variant->size) }}" >
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Màu:</label>
                        <input type="text" name="variants[{{ $index }}][color]" class="form-control" value="{{ old('variants.' . $index . '.color', $variant->color) }}" required oninput="validateSizeFields()">

                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Giá gốc:</label>
                        <input type="number" step="0.01" name="variants[{{ $index }}][original_price]" class="form-control" value="{{ old('variants.' . $index . '.original_price', $variant->original_price) }}" required>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Giá bán:</label>
                        <input type="number" step="0.01" name="variants[{{ $index }}][price]" class="form-control" value="{{ old('variants.' . $index . '.price', $variant->price) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tồn kho:</label>
                        <input type="number" name="variants[{{ $index }}][stock_quantity]" class="form-control" value="{{ old('variants.' . $index . '.stock_quantity', $variant->stock_quantity) }}" required min="0">
                    </div>
                 

                    <div class="col-md-2" style="display: none;">
                        <label>Số lượng đã bán:</label>
                        <input type="hidden" name="variants[{{ $index }}][sold_quantity]" value="{{ $variant->sold_quantity ?? 0 }}">
                    </div>


                </div>

                <div class="mt-3">
                    <label class="form-label">Ảnh biến thể:</label>
                    <input type="file" name="variants[{{ $index }}][image]" class="form-control">
                </div>
                @if(count($product->variants) > 1)
                <button type="button" onclick="deleteVariant(this, {{ $variant->id }})" class="btn btn-danger mt-2">Xóa</button>
            @endif
            </div>
        @endforeach
    </div>

    <a href="{{ route('admin.variants.create', $product->id) }}" class="btn btn-secondary mt-3">Thêm biến thể</a>

    <button type="submit" class="btn btn-primary mt-3">Cập nhật sản phẩm</button>
</form>
<button id="add-size-button" class="btn btn-primary mt-2" onclick="showSizeFields()" style="display: none;">
    Thêm Size
</button>

<form id="delete-variant-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>let variantIndex = {{ count($product->variants) }};

    document.addEventListener("DOMContentLoaded", function () {
        validateSizeFields();
    });
    
    document.addEventListener("input", function (event) {
        if (event.target.matches("input[name^='variants'][name$='[size]']")) {
            validateSizeFields();
        }
    });
    
    function validateSizeFields() {
    let sizeInputs = document.querySelectorAll("input[name^='variants'][name$='[size]']");
    let colorInputs = document.querySelectorAll("input[name^='variants'][name$='[color]']");
    let addSizeButton = document.getElementById("add-size-button");
    let hasSize = Array.from(sizeInputs).some(input => input.value.trim());

    if (hasSize) {
        sizeInputs.forEach(input => {
            input.required = true;
            input.removeAttribute("disabled");
        });
        addSizeButton.style.display = "none"; // Ẩn nút nếu có size
    } else {
        sizeInputs.forEach(input => {
            input.required = false;
            input.value = "";
            input.setAttribute("disabled", "disabled");
        });
        addSizeButton.style.display = "block"; // Hiện nút nếu không có size
    }

    // Thêm kiểm tra trùng size + màu
    let variantData = new Map();

    sizeInputs.forEach((sizeInput, index) => {
        let size = sizeInput.value.trim();
        let color = colorInputs[index].value.trim();
        let key = `${size}-${color}`;

        if (color !== "") { 
            if (variantData.has(key)) {
                colorInputs[index].setCustomValidity("❌ Size trùng + Màu trùng không hợp lệ!");
            } else {
                colorInputs[index].setCustomValidity("");
                variantData.set(key, true);
            }
        }
    });
}
function validateSizeFields() {
    let sizeInputs = document.querySelectorAll("input[name^='variants'][name$='[size]']");
    let colorInputs = document.querySelectorAll("input[name^='variants'][name$='[color]']");
    let addSizeButton = document.getElementById("add-size-button");
    let hasSize = Array.from(sizeInputs).some(input => input.value.trim());

    if (hasSize) {
        sizeInputs.forEach(input => {
            input.required = true;
            input.removeAttribute("disabled");
        });
        addSizeButton.style.display = "none"; // Ẩn nút nếu có size
    } else {
        sizeInputs.forEach(input => {
            input.required = false;
            input.value = "";
            input.setAttribute("disabled", "disabled");
        });
        addSizeButton.style.display = "block"; // Hiện nút nếu không có size
    }

    // Thêm kiểm tra trùng size + màu
    let variantData = new Map();

    sizeInputs.forEach((sizeInput, index) => {
        let size = sizeInput.value.trim();
        let color = colorInputs[index].value.trim();
        let key = `${size}-${color}`;

        if (color !== "") { 
            if (variantData.has(key)) {
                colorInputs[index].setCustomValidity("❌ Size trùng + Màu trùng không hợp lệ!");
            } else {
                colorInputs[index].setCustomValidity("");
                variantData.set(key, true);
            }
        }
    });
}

    function showSizeFields() {
        let sizeInputs = document.querySelectorAll("input[name^='variants'][name$='[size]']");
        let addSizeButton = document.getElementById("add-size-button");
    
        sizeInputs.forEach(input => {
            input.removeAttribute("disabled");
        });
        addSizeButton.style.display = "none"; // Ẩn nút sau khi kích hoạt size
    }
    
    function addVariant() {
        let container = document.getElementById('variant-container');
    
        container.insertAdjacentHTML('beforeend', html);
        variantIndex++;
        validateSizeFields();
    }
    
    function deleteVariant(button, variantId) {
        if (confirm('Bạn có chắc muốn xóa biến thể này?')) {
            let variantElement = button.parentElement;
            if (variantId) {
                let form = document.getElementById('delete-variant-form');
                form.action = `/admin/products/{{ $product->id }}/variants/${variantId}`;
                form.submit();
            } else {
                variantElement.remove();
                validateSizeFields();
            }
        }
    }
    </script>
@endsection
{{-- ok --}}