<!-- Chi tiet sp -->
@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/chitietsp.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}"> -->
<hr>
    
<div class="container">
  

    @if($product->image)
    <div class="row">
        <div class="col-md-6">
            <!-- Ảnh chính ban đầu là ảnh của sản phẩm -->
            <div class="mt-3 d-flex " id="variant-thumbnails" style="margin-left: 120px;">
                <img id="variant-image" src="{{ asset('storage/' . $product->image) }}" 
                     alt="{{ $product->name }}" class="img-fluid" width="600px" height="60%">
            </div>

            <!-- Ảnh nhỏ: Gồm ảnh sản phẩm chung + ảnh của các biến thể -->
            <div class="mt-3 d-flex" style="margin-left: 120px;">
             

                    <div class="mt-3 d-flex" id="variant-thumbnails">
                        @foreach ($product->variants as $variant)
                            <img src="{{ asset('storage/' . $variant->image) }}" 
                                 alt="{{ $variant->color }} - {{ $variant->size }}" 
                                 class="img-thumbnail me-2 variant-thumbnail"
                                 width="20%" height="20%"
                                 data-variant-id="{{ $variant->id }}"
                                 data-size="{{ $variant->size }}"
                                 data-color="{{ $variant->color }}">
                        @endforeach
                    </div>
            </div>
        </div>
@else
    <p>Chưa có hình ảnh</p>
@endif


 
<div class="col-md-6">
    <br>
    <h1>
    <b> Sản phẩm{{ $product->name }}</b> </h1>
    <p>Danh mục: {{ $product->category ? $product->category->name : 'Chưa có danh mục' }}</p>
    <!-- <p> Mo ta: {{ $product->description }}</p> -->
<!-- 
    <div>
        <img id="product-image" src="{{ $product->image }}" alt="{{ $product->name }}"
            style="max-width: 300px; display: block;">
    </div> -->

    <p><strong>Giá: </strong>
        <span id="base-price">{{ number_format($product->base_price,0, ',', '.') }} VNĐ</span>
        <span id="variant-price" style="font-weight: bold; margin-left: 10px; color: red;"></span>
    </p>
 
    <p><strong>Tồn kho: </strong> <span id="stock-info">{{ $product->variants->sum('stock_quantity') }}</span></p>


    <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="variant_id" id="variant_id" value="">

    
        @if($product->variants->whereNotNull('size')->count() > 0) 
        <div>
        <p class="mt-3"><strong>SIZE</strong></p>
            <div id="size-options">
                @foreach ($product->variants->groupBy('size') as $size => $variants)
                    <button type="button" class="size-btn" data-size="{{ $size }}">{{ $size }}</button>
                @endforeach
            </div>
        </div>
        @endif

      <br>
        <div>
        <p><strong>MÀU SẮC</strong></p>
        <div class="d-flex">
            <div id="color-options">
                @foreach ($product->variants->unique('color') as $variant)
                    <button type="button" class="color-btn"
                        data-color="{{ $variant->color }}">{{ $variant->color }}</button>
                @endforeach
                </div>
            </div>
        </div><p><strong>Đang chọn: </strong> <span id="selected-variant-info">Chưa chọn</span></p>
        <div class="mt-3">
                    <a href="#" class="text-primary">Hướng dẫn chọn size</a> | 
                    <a href="#" class="text-primary">Thông số sản phẩm</a>
                </div>
     
        <label for="quantity">Số lượng:</label>
        <input type="number" name="quantity" class="form-control w-25 me-2" min="1" value="1" required>

    <br>
    <div class="mt-3 d-flex">
                    <button type="submit" id="addToCartButton" disabled class="btn btn-danger">Thêm vào giỏ hàng</button>
                </div>
                <br>
                <p> Mo ta: {{ $product->description }}</p>
</div>




</div>

    </form>
    </div>

    <a href="{{ route('products.index') }}">Quay lại danh sách sản phẩm</a>

      <!-- Đánh giá sản phẩm -->
      <h4 class="mt-4">Đánh giá sản phẩm</h4>
        <div class="rating-box">
            <div >

               <div><span>5⭐ ---------------------- 0% | 0 đánh giá</span></div> 
               <div><span>4⭐ 0% | 0 đánh giá</span></div> 
               <div><span>3⭐ 0% | 0 đánh giá</span></div> 
               <div><span>2⭐ 0% | 0 đánh giá</span></div> 
               <div><span>1⭐ 0% | 0 đánh giá</span></div> 
               
                <button class="btn btn-dark btn-sm" >Đánh giá ngay</button>
            </div>
        </div>

        <hr>

        <!-- Bình luận -->
        <h4 class="mt-4">Bình luận</h4>
        <textarea class="form-control" placeholder="Bình luận ngay..." rows="3"></textarea>
        <div class="mt-2 d-flex align-items-center">
            <input type="radio" name="gender" id="male" checked> <label for="male" class="ms-2 me-3">Anh</label>
            <input type="radio" name="gender" id="female"> <label for="female" class="ms-2">Chị</label>
        </div>
        <div class="mt-2 d-flex">
            <input type="text" class="form-control me-2" placeholder="Họ và tên">
            <input type="email" class="form-control me-2" placeholder="Email">
            <button class="btn btn-danger">Gửi</button>
        </div>
    </div>
<br>
<hr>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let variants = @json($product->variants);
            let selectedSize = null;
            let selectedColor = null;
            let selectedVariant = null;
        
            let defaultImage = document.getElementById("variant-image").getAttribute("src");
            let variantImageElement = document.getElementById("variant-image");
            let selectedVariantInfo = document.getElementById("selected-variant-info");
        
            function updateVariant(variant) {
                if (variant) {
                    document.getElementById("variant_id").value = variant.id;
                    document.getElementById("stock-info").textContent = variant.stock_quantity + " sản phẩm có sẵn";
                    document.getElementById("variant-price").textContent =
                        new Intl.NumberFormat('vi-VN').format(variant.price) + " VNĐ";
                    document.getElementById("addToCartButton").disabled = false;
        
                    // Cập nhật ảnh biến thể
                    variantImageElement.src = "/storage/" + variant.image;
        
                    // Cập nhật thông tin "Đang chọn"
                    selectedVariantInfo.textContent = `Size ${variant.size} - Màu ${variant.color}`;
        
                    // Tự động chọn size
                    selectedSize = variant.size;
                    document.querySelectorAll(".size-btn").forEach(btn => {
                        btn.classList.toggle("active", btn.getAttribute("data-size") === selectedSize);
                    });
        
                    // Tự động chọn màu
                    selectedColor = variant.color;
                    document.querySelectorAll(".color-btn").forEach(btn => {
                        btn.classList.toggle("active", btn.getAttribute("data-color") === selectedColor);
                    });
                } else {
                    // Reset nếu không có biến thể
                    document.getElementById("variant_id").value = "";
                    document.getElementById("stock-info").textContent = "";
                    document.getElementById("variant-price").textContent = "";
                    document.getElementById("addToCartButton").disabled = true;
                    variantImageElement.src = defaultImage;
                    selectedVariantInfo.textContent = "Chưa chọn";
                }
            }
        
            // Xử lý chọn ảnh biến thể
            document.querySelectorAll(".variant-thumbnail").forEach(img => {
                img.addEventListener("click", function() {
                    let variantId = this.getAttribute("data-variant-id");
                    let variant = variants.find(v => v.id == variantId);
                    updateVariant(variant);
                });
            });
        
            // Khi chọn size
            document.querySelectorAll(".size-btn").forEach(button => {
                button.addEventListener("click", function() {
                    if (this.classList.contains("active")) {
                        selectedSize = null;
                        this.classList.remove("active");
        
                        selectedColor = null;
                        selectedVariant = null;
                        document.querySelectorAll(".color-btn").forEach(btn => {
                            btn.classList.remove("active");
                            btn.disabled = true;
                        });
        
                        updateVariant(null);
                    } else {
                        selectedSize = this.getAttribute("data-size");
                        document.querySelectorAll(".size-btn").forEach(btn => btn.classList.remove("active"));
                        this.classList.add("active");
        
                        selectedColor = null;
                        selectedVariant = null;
                        document.querySelectorAll(".color-btn").forEach(btn => {
                            btn.classList.remove("active");
                            btn.disabled = true;
                        });
        
                        let availableColors = variants.filter(v => v.size === selectedSize).map(v => v.color);
                        document.querySelectorAll(".color-btn").forEach(btn => {
                            if (availableColors.includes(btn.getAttribute("data-color"))) {
                                btn.disabled = false;
                            }
                        });
        
                        updateVariant(null);
                    }
                });
            });
        
            // Khi chọn màu sắc
            document.querySelectorAll(".color-btn").forEach(button => {
                button.addEventListener("click", function() {
                    if (this.disabled) return;
        
                    if (this.classList.contains("active")) {
                        selectedColor = null;
                        this.classList.remove("active");
                        updateVariant(null);
                    } else {
                        selectedColor = this.getAttribute("data-color");
                        document.querySelectorAll(".color-btn").forEach(btn => btn.classList.remove("active"));
                        this.classList.add("active");
        
                        selectedVariant = variants.find(v => v.size === selectedSize && v.color === selectedColor);
                        updateVariant(selectedVariant);
                    }
                });
            });
        });
        </script>
        


   
@endsection
