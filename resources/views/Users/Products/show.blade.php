{{-- @extends('layouts.app')

@section('content') --}}
<h1>{{ $product->name }}</h1>
<p>{{ $product->description }}</p>
<p>Danh mục: {{ $product->category->name }}</p>

<form id="cartForm" action="{{ route('cart.add') }}" method="POST">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="variant_id" id="variant_id">

    {{-- Chọn Size --}}
    <h4>Chọn Size:</h4>
    <div class="option-group size-group">
        @foreach ($product->variants->pluck('size')->unique() as $size)
            <button type="button" class="selection-box size-btn" onclick="selectSize('{{ $size }}', this)">
                {{ $size }}
            </button>
        @endforeach
    </div>

    {{-- Chọn Màu sắc --}}
    <h4>Chọn Màu sắc:</h4>
    <div class="option-group color-group">
        @foreach ($product->variants->pluck('color')->unique() as $color)
            <button type="button" class="selection-box color-btn" onclick="selectColor('{{ $color }}', this)">
                @if ($product->variants->where('color', $color)->first()->image)
                    <img src="{{ $product->variants->where('color', $color)->first()->image }}" width="24">
                @endif
                {{ $color }}
            </button>
        @endforeach
    </div>

    {{-- Nhập số lượng --}}
    <h4>Chọn số lượng:</h4>
    <input type="number" name="quantity" value="1" min="1" required>
    {{-- @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif --}}
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
</style>

<script>
    let selectedSize = null;
    let selectedColor = null;
    let productVariants = @json($product->variants);

    function selectSize(size, button) {
        selectedSize = size;

        // Xóa class 'selected' khỏi tất cả nút trong nhóm size
        document.querySelectorAll('.size-group .selection-box').forEach(btn => btn.classList.remove('selected'));

        // Thêm class 'selected' vào nút vừa chọn
        button.classList.add('selected');
    }

    function selectColor(color, button) {
        selectedColor = color;

        // Xóa class 'selected' khỏi tất cả nút trong nhóm màu
        document.querySelectorAll('.color-group .selection-box').forEach(btn => btn.classList.remove('selected'));

        // Thêm class 'selected' vào nút vừa chọn
        button.classList.add('selected');
    }

    function updateVariantId(event) {
        if (!selectedSize || !selectedColor) {
            alert('Vui lòng chọn size và màu sắc trước khi thêm vào giỏ hàng.');
            event.preventDefault();
            return;
        }

        let selectedVariant = productVariants.find(variant =>
            variant.size == selectedSize && variant.color == selectedColor
        );

        if (!selectedVariant) {
            alert('Không tìm thấy biến thể phù hợp.');
            event.preventDefault();
            return;
        }

        document.getElementById('variant_id').value = selectedVariant.id;
    }
</script>
{{-- @endsection --}}
