@extends('layouts.app')

@section('content')
<h1>{{ $product->name }}</h1>
<p>{{ $product->description }}</p>
<p>Danh mục: {{ $product->category->name }}</p>

{{-- Hiển thị số lượng tồn kho --}}
<p><strong>Tồn kho: </strong> 
    <span id="stock-info">{{ $product->variants->sum('stock_quantity') }}</span>
</p>

{{-- Hiển thị giá sản phẩm --}}
<p><strong>Giá: </strong> 
    <span id="base-price">
        {{ number_format($product->base_price, 0, ',', '.') }} VNĐ
    </span>
    <span id="variant-price" style="font-weight: bold; margin-left: 10px;"></span>
</p>

<form id="cartForm" action="{{ route('cart.add') }}" method="POST">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="variant_id" id="variant_id">

    {{-- Chọn Size (chỉ hiển thị nếu có size) --}}
    @if ($product->variants->pluck('size')->filter()->count() > 0)
        <h4>Chọn Size:</h4>
        <div class="option-group size-group">
            @foreach ($product->variants->pluck('size')->unique()->filter() as $size)
                <button type="button" class="selection-box size-btn" onclick="toggleSize('{{ $size }}', this)">
                    {{ $size }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- Chọn Màu sắc --}}
    <h4>Chọn Màu sắc:</h4>
    <div class="option-group color-group">
        @foreach ($product->variants->pluck('color')->unique() as $color)
            <button type="button" class="selection-box color-btn" onclick="toggleColor('{{ $color }}', this)" data-color="{{ $color }}">
                @if ($product->variants->where('color', $color)->first()->image)
                    <img src="{{ $product->variants->where('color', $color)->first()->image }}" width="24">
                @endif
                {{ $color }}
            </button>
        @endforeach
    </div>

    {{-- Nhập số lượng --}}
    <h4>Chọn số lượng:</h4>
    <input type="number" id="quantity" name="quantity" value="1" min="1" required>

    {{-- Nút Thêm vào Giỏ hàng --}}
    <button type="submit" class="add-to-cart-btn" onclick="updateVariantId(event)">
        🛒 Thêm vào giỏ hàng
    </button>
</form>

<a href="{{ route('products.index') }}">Quay lại danh sách sản phẩm</a>

<style>
    .selection-box {
        padding: 8px 12px;
        border: 1px solid #ccc;
        background: white;
        cursor: pointer;
        margin: 5px;
        border-radius: 5px;
    }

    .selection-box.selected {
        border: 2px solid red;
        background: #ffebeb;
    }

    .selection-box.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        text-decoration: line-through;
    }
</style>

<script>
    let selectedSize = null;
    let selectedColor = null;
    let selectedStock = null;
    let productVariants = @json($product->variants);

    // Hàm toggle Size (Chọn và Bỏ chọn)
    function toggleSize(size, button) {
        if (selectedSize === size) {
            // Nếu đã chọn, bấm lại để bỏ chọn
            selectedSize = null;
            button.classList.remove('selected');
        } else {
            // Nếu chưa chọn hoặc chọn mới
            selectedSize = size;
            resetSelection('.size-group .selection-box', button);
        }
        updateAvailableColors();
        updatePriceAndStock();
    }

    // Hàm toggle Color (Chọn và Bỏ chọn)
    function toggleColor(color, button) {
        if (button.classList.contains('disabled')) return;

        if (selectedColor === color) {
            // Nếu đã chọn, bấm lại để bỏ chọn
            selectedColor = null;
            button.classList.remove('selected');
        } else {
            // Nếu chưa chọn hoặc chọn mới
            selectedColor = color;
            resetSelection('.color-group .selection-box', button);
        }

        // Nếu sản phẩm không có size, bỏ chọn size khi chọn màu
        if (document.querySelector('.size-group') === null) {
            selectedSize = null;
        }

        updatePriceAndStock();
    }

    // Reset trạng thái chọn
    function resetSelection(selector, button) {
        document.querySelectorAll(selector).forEach(btn => btn.classList.remove('selected'));
        button.classList.add('selected');
    }

    // Cập nhật màu khả dụng dựa trên size
    function updateAvailableColors() {
        document.querySelectorAll('.color-group .selection-box').forEach(btn => {
            let color = btn.getAttribute('data-color');
            let exists = productVariants.some(v => v.size == selectedSize && v.color == color);
            btn.classList.toggle('disabled', !exists);
        });
    }

    // Cập nhật giá và tồn kho dựa trên biến thể
    function updatePriceAndStock() {
        let basePrice = document.getElementById('base-price');
        let variantPrice = document.getElementById('variant-price');
        let stockInfo = document.getElementById('stock-info');
        let quantityInput = document.getElementById('quantity');

        if (!selectedColor) {
            basePrice.style.textDecoration = "none";
            variantPrice.innerHTML = "";
            stockInfo.innerHTML = {{ $product->variants->sum('stock_quantity') }};
            quantityInput.removeAttribute("max");
            return;
        }

        let selectedVariant = productVariants.find(variant =>
            (!selectedSize || variant.size == selectedSize) && variant.color == selectedColor
        );

        if (selectedVariant) {
            basePrice.style.textDecoration = "line-through";
            variantPrice.innerHTML = `${selectedVariant.price.toLocaleString()} VNĐ`;
            
            // Hiển thị số lượng tồn kho theo biến thể
            selectedStock = selectedVariant.stock_quantity;
            stockInfo.innerHTML = selectedStock;

            // Giới hạn số lượng theo tồn kho
            quantityInput.max = selectedStock;
            if (quantityInput.value > selectedStock) {
                quantityInput.value = selectedStock;
            }
        } else {
            basePrice.style.textDecoration = "none";
            variantPrice.innerHTML = "";
            stockInfo.innerHTML = {{ $product->variants->sum('stock_quantity') }};
            quantityInput.removeAttribute("max");
        }
    }

    // Xử lý cập nhật biến thể khi gửi form
    function updateVariantId(event) {
        if (!selectedColor) {
            alert('Vui lòng chọn màu sắc trước khi thêm vào giỏ hàng.');
            event.preventDefault();
            return;
        }

        let selectedVariant = productVariants.find(variant =>
            (!selectedSize || variant.size == selectedSize) && variant.color == selectedColor
        );

        if (!selectedVariant) {
            alert('Không tìm thấy biến thể phù hợp.');
            event.preventDefault();
            return;
        }

        document.getElementById('variant_id').value = selectedVariant.id;
    }
</script>
@endsection
