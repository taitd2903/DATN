@extends('layouts.layout')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Thêm sản phẩm</h1>




        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form tạo sản phẩm -->
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm:</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ảnh:</label>
                <input type="file" class="form-control" name="image" id="image" accept="image/*" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả:</label>
                <textarea class="form-control" name="description" id="description" placeholder="Nhập mô tả sản phẩm" required></textarea>
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
            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
                <button type="submit" class="btn btn-success">Lưu</button>
            </div>
            
            
        </form>
    </div>

    <script>
  let variantIndex = 0;

function addVariant() {
    let container = document.getElementById('variant-container');
    let variantId = `variant-${variantIndex}`;
    let sizeRequiredAttr = variantIndex === 0 ? "" : "disabled"; // Ô đầu tiên không bị ràng buộc, ô sau vô hiệu hóa ban đầu

    let html = `
        <div class="variant mb-3 row" id="${variantId}">
            <div class="col-md-2">
                <label>Size:</label>
                <input type="text" class="form-control size-input" name="variants[${variantIndex}][size]" ${sizeRequiredAttr} oninput="handleSizeInput()">
            </div>

            <div class="col-md-2">
                <label>Màu:</label>
                <input type="color" class="form-control" name="variants[${variantIndex}][color]" required oninput="handleSizeInput()">

            </div>

            <div class="col-md-2">
                <label>Giá gốc:</label>
                <input type="number" class="form-control" step="0.01" name="variants[${variantIndex}][original_price]" required min="0" max="100000000000">
            </div>

            <div class="col-md-2">
                <label>Giá bán:</label>
                <input type="number" class="form-control" step="0.01" name="variants[${variantIndex}][price]" required min="0" max="100000000000">
            </div>

            <div class="col-md-2">
                <label>Số lượng tồn kho:</label>
                <input type="number" class="form-control" name="variants[${variantIndex}][stock_quantity]" required min="0" max="1000000000">
            </div>
<div class="col-md-2" style="display: none;">
    <label>Số lượng đã bán:</label>
    <input type="hidden" name="variants[${variantIndex}][sold_quantity]" value="0"max="1000000000">
</div>
            <div class="col-md-2">
                <label>Ảnh biến thể:</label>
                <input type="file" class="form-control" name="variants[${variantIndex}][image]" accept="image/*" required>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-remove" onclick="removeVariant('${variantId}')">Xóa</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    variantIndex++;

    updateRemoveButtons();
    handleSizeInput(); // Kiểm tra lại trạng thái size khi thêm biến thể mới
}

function removeVariant(variantId) {
    document.getElementById(variantId).remove();
    updateRemoveButtons();
    handleSizeInput(); // Kiểm tra lại khi xóa biến thể
}

// Kiểm tra logic nhập vào ô "Size"
function handleSizeInput() {
    let allSizeInputs = document.querySelectorAll('.size-input');

    if (allSizeInputs.length === 0) return;

    let firstSizeValue = allSizeInputs[0]?.value.trim(); 

    allSizeInputs.forEach((input, index) => {
        if (index === 0) {
            input.required = false; 
            input.disabled = false;
        } else {
            let prevSizeValue = allSizeInputs[index - 1]?.value.trim(); 

            if (prevSizeValue !== "") {
                input.required = true; 
                input.disabled = false;
            } else {
                input.required = false; 
                input.disabled = true;
                input.value = ""; 
            }
        }
    });
    let allVariants = document.querySelectorAll('.variant');
    let variantData = new Map(); 

    allVariants.forEach(variant => {
        let sizeInput = variant.querySelector('.size-input');
        let colorInput = variant.querySelector('input[name^="variants"][name$="[color]"]');
        
        let size = sizeInput ? sizeInput.value.trim() : "";
        let color = colorInput ? colorInput.value.trim() : "";

        if (color !== "") { 
            let key = `${size}-${color}`;

            if (variantData.has(key)) {
                colorInput.setCustomValidity("❌ Size trùng + Màu trùng không hợp lệ!");
            } else {
                colorInput.setCustomValidity("");
                variantData.set(key, true);
            }
        }
    });
}


function updateRemoveButtons() {
    let variants = document.querySelectorAll('.variant');
    let removeButtons = document.querySelectorAll('.btn-remove');

    if (variants.length === 1) {
        removeButtons.forEach(button => button.style.display = 'none');
    } else {
        removeButtons.forEach(button => button.style.display = 'block');
    }
}

addVariant();


    </script>
@endsection
{{-- ok --}}