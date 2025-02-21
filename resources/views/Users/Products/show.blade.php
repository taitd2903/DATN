@extends('layouts.app')

@section('content')
    <h1>{{ $product->name }}</h1>
    <p>{{ $product->description }}</p>
    <p>Danh mục: {{ $product->category->name }}</p>

    <div>
        <img id="product-image" src="{{ $product->image }}" alt="{{ $product->name }}"
            style="max-width: 300px; display: block;">
    </div>

    <!-- Hiển thị tồn kho tổng -->
    <p><strong>Tồn kho: </strong> <span id="stock-info">{{ $product->variants->sum('stock_quantity') }}</span></p>

    <!-- Hiển thị giá sản phẩm -->
    <p><strong>Giá: </strong>
        <span id="base-price">{{ number_format($product->base_price, 0, ',', '.') }} VNĐ</span>
        <span id="variant-price" style="font-weight: bold; margin-left: 10px; color: red;"></span>
    </p>

    <!-- Form thêm vào giỏ hàng -->
    <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="variant_id" id="variant_id" value="">

        <!-- Chọn size -->
        <div>
            <label>Chọn Size:</label>
            <div id="size-options">
                @foreach ($product->variants->groupBy('size') as $size => $variants)
                    <button type="button" class="size-btn" data-size="{{ $size }}">{{ $size }}</button>
                @endforeach
            </div>
        </div>

        <!-- Chọn màu sắc -->
        <div>
            <label>Chọn Màu:</label>
            <div id="color-options">
                @foreach ($product->variants->unique('color') as $variant)
                    <button type="button" class="color-btn"
                        data-color="{{ $variant->color }}">{{ $variant->color }}</button>
                @endforeach
            </div>
        </div>

        <!-- Nhập số lượng -->
        <label for="quantity">Số lượng:</label>
        <input type="number" name="quantity" min="1" value="1" required>

        <!-- Nút thêm vào giỏ hàng -->
        <button type="submit" id="addToCartButton" disabled>Thêm vào giỏ hàng</button>
    </form>

    <!-- CSS -->
    <style>
        .size-btn,
        .color-btn {
            padding: 8px 16px;
            margin: 5px;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: all 0.3s;
        }

        .size-btn:hover,
        .color-btn:hover {
            background-color: #f0f0f0;
        }

        .size-btn.active,
        .color-btn.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .color-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let variants = @json($product->variants);
            let selectedSize = null;
            let selectedColor = null;
            let selectedVariant = null;

            // Khi chọn size
            document.querySelectorAll(".size-btn").forEach(button => {
                button.addEventListener("click", function() {
                    selectedSize = this.getAttribute("data-size");

                    // Đánh dấu nút size được chọn
                    document.querySelectorAll(".size-btn").forEach(btn => btn.classList.remove(
                        "active"));
                    this.classList.add("active");

                    // Reset màu sắc
                    selectedColor = null;
                    selectedVariant = null;
                    document.querySelectorAll(".color-btn").forEach(btn => {
                        btn.classList.remove("active");
                        btn.disabled = true;
                    });

                    // Lọc danh sách màu theo size đã chọn
                    let availableColors = variants.filter(v => v.size === selectedSize).map(v => v
                        .color);
                    document.querySelectorAll(".color-btn").forEach(btn => {
                        if (availableColors.includes(btn.getAttribute("data-color"))) {
                            btn.disabled = false;
                        }
                    });

                    // Cập nhật giao diện
                    document.getElementById("variant_id").value = "";
                    document.getElementById("stock-info").textContent = "";
                    document.getElementById("variant-price").textContent = "";
                    document.getElementById("addToCartButton").disabled = true;
                });
            });

            // Khi chọn màu sắc
            document.querySelectorAll(".color-btn").forEach(button => {
                button.addEventListener("click", function() {
                    if (this.disabled) return;

                    selectedColor = this.getAttribute("data-color");

                    document.querySelectorAll(".color-btn").forEach(btn => btn.classList.remove(
                        "active"));
                    this.classList.add("active");

                    // Tìm biến thể phù hợp
                    selectedVariant = variants.find(v => v.size === selectedSize && v.color ===
                        selectedColor);

                    if (selectedVariant) {
                        document.getElementById("variant_id").value = selectedVariant.id;
                        document.getElementById("stock-info").textContent = selectedVariant
                            .stock_quantity + " sản phẩm có sẵn";
                        document.getElementById("variant-price").textContent =
                            new Intl.NumberFormat('vi-VN').format(selectedVariant.price) + " VNĐ";

                        document.getElementById("addToCartButton").disabled = false;
                    }
                });
            });
        });
    </script>

    <a href="{{ route('products.index') }}">Quay lại danh sách sản phẩm</a>
@endsection
