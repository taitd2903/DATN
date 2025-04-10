<!-- Chi tiet sp -->
@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/chitietsp.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<hr>
    
<div class="container">
  

    @if($product->image)
    <div class="row">
    <div class="col-md-6">
    <div class="d-flex align-items-start">
        <!-- Cột trái: Ảnh nhỏ -->
        <div class="me-3">
            <div class="d-flex flex-column gap-2 align-items-center">
                @foreach ($product->variants->take(3) as $variant)
                    <img src="{{ asset('storage/' . $variant->image) }}" 
                         alt="{{ $variant->color }} - {{ $variant->size }}" 
                         class="img-thumbnail variant-thumbnail"
                         width="80"
                         height="80"
                         data-variant-id="{{ $variant->id }}"
                         data-size="{{ $variant->size }}"
                         data-color="{{ $variant->color }}">
                @endforeach
            </div>
        </div>

        <!-- Cột phải: Ảnh lớn -->
        <div class="zoom-box">
            <img id="variant-image" src="{{ asset('storage/' . $product->image) }}" 
                 alt="{{ $product->name }}" class="img-fluid zoom-image">
        </div>
    </div>
</div>
@else
    <p>{{ __('messages.no_image') }}</p>
@endif


 
<div class="col-md-6">
    <br>
    <h1>
    <h1 class="product-title">{{ $product->name }}</h1>

    <!-- <p class="sku">
    <strong>{{ __('messages.out_of_stock') }}</strong> {{ $product->variants->sum('stock_quantity') }} 

    <span class="rating">
        ⭐⭐⭐⭐⭐ (0) <span class="reviews">0 {{ __('messages.reviews') }}</span>
    </span>
    </p> -->

    <p class="price"> 
        @if($product->base_price !== null && $product->base_price !== "")
            <span id="base-price">{{ number_format($product->base_price, 0, ',', '.') }} VNĐ</span>
        @endif
        <span id="variant-price" style="font-weight: bold; margin-left: 10px; color: red;">
            {{ number_format($minPrice, 0, ',', '.') }} - {{ number_format($maxPrice, 0, ',', '.') }} VNĐ
        </span>
    </p>
    
    <p> Mô tả: {{ $product->description }}</p>
    <p><strong>Còn:</strong> <span id="stock-info">{{ $product->variants->sum('stock_quantity') }}</span></p>
    <hr>


<!-- 
    <div>
        <img id="product-image" src="{{ $product->image }}" alt="{{ $product->name }}"
            style="max-width: 300px; display: block;">
    </div> -->
    

    <!-- <button class="dashed-line-btn"></button> -->


    <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="variant_id" id="variant_id" value="">

    
        @if($product->variants->whereNotNull('size')->count() > 0) 
        <div>

            <div id="size-options">
            <p class="mt-2"><strong>{{ __('messages.size') }}</strong></p>
                @foreach ($product->variants->groupBy('size') as $size => $variants)
                    <button type="button" class="size-btn" data-size="{{ $size }}">{{ $size }}</button>
                @endforeach
            </div>
        </div>
        @endif

      <br>
        <div>
       
      
            <div id="color-options">
            <strong class="mt-2">{{ __('messages.color') }}</strong>
                @foreach ($product->variants->unique('color') as $variant)
                    <button type="button" class="color-btn"
                        data-color="{{ $variant->color }}">{{ $variant->color }}</button>
                @endforeach
                </div>
          <br>
        </div><p><strong>{{ __('messages.selected_variant') }} </strong> <span id="selected-variant-info">{{ __('messages.not_selected') }}</span></p>

     <br>
   
    <!-- <label for="quantity" class="quantity-label">{{ __('messages.quantity') }}</label> -->
    
    <!-- <div class="quantity-controls">
        <button type="button" class="quantity-btn" id="decrease">−</button>
        <input type="text" id="quantity" name="quantity" value="1" min="1" class="quantity-input" readonly>
        <button type="button" class="quantity-btn" id="increase">+</button>
    </div> -->

    <div class="quantity-wrapper">
    <label for="quantity" class="quantity-label">Số lượng:</label>
    <div class="quantity-controls">
        <button type="button" class="quantity-btn" id="decrease">−</button>
        <input type="number" id="quantity" name="quantity" value="1" min="1" class="quantity-input">
        <button type="button" class="quantity-btn" id="increase">+</button>
    </div>
</div>

        <div class="mt-3 d-flex align-items-center">

    <!-- Nút thêm vào giỏ hàng -->
    <button type="submit" id="addToCartButton" disabled class="btn btn-outline-dark ms-2">
    {{ __('messages.add_to_cart') }}
    </button>
</div>

<br>
<a href="#" class="text-primary" onclick="openSizeGuide()">{{ __('messages.size_guide') }}</a> | 
<a href="#" class="text-primary">{{ __('messages.product_info') }}</a>

<!-- Modal hiển thị bảng size -->
<div id="sizeGuideModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeSizeGuide()">&times;</span>
        <img src="../assets/img/bangsize.jpg" alt="Bảng size áo nữ">
    </div>
</div>
<br>
<!-- <div class="policy-support">
    <ul class="list-unstyled">
        <li><i class="fa fa-check-square"></i> Chính sách bảo mật - Bảo vệ thông tin khách hàng</li>
        <li><i class="fa fa-truck"></i> Chính sách giao hàng - Nhanh chóng, tiện lợi</li>
        <li><i class="fa fa-sync-alt"></i> Chính sách đổi trả - Đảm bảo quyền lợi khách hàng</li>
        <li><i class="fa fa-credit-card"></i> Chính sách thanh toán - Linh hoạt, an toàn</li>
        <li><i class="fa fa-headphones"></i> Hỗ trợ khách hàng - Tư vấn 24/7</li>
    </ul>
</div> -->

<div class="info-banner">
    <div class="info-box">
        <img src="https://img.icons8.com/ios-filled/40/000000/phone.png" alt="Call icon">
        <div>
            <p>Mua hàng: <span class="highlight">0912.743.443</span> từ 8h00 -<br>21h30 mỗi ngày</p>
        </div>
    </div>
    <div class="info-box">
        <img src="https://img.icons8.com/ios-filled/40/000000/delivery.png" alt="Free Shipping icon">
        <div>
            <p>Miễn phí vận chuyển<br><span class="highlight">Cho đơn hàng trên 499k</span></p>
        </div>
    </div>
    <div class="info-box">
        <img src="https://img.icons8.com/ios-filled/40/000000/return-purchase.png" alt="Return icon">
        <div>
            <p>Đổi sản phẩm trong vòng<br><span class="highlight">45 ngày</span></p>
        </div>
    </div>
</div>

                
{{--========================== Phần này của Đạt thông báo lỗi ============================--}}
                @if (session('error'))
            <small class="text-danger" id="error-message">{{ session('error') }}</small>
            <script>
                setTimeout(function () {
                    document.getElementById('error-message').style.display = 'none';
                }, 5000);
            </script>
        @endif
        @if (session('success'))
            <small class="text-success" id="success-message">{{ session('success') }}</small>
            <script>
                setTimeout(function () {
                    document.getElementById('success-message').style.display = 'none';
                }, 5000);
            </script>
        @endif
{{--========================= Hết phần của Đạt ============================--}}
                <br>
              
</div>




</div>

    </form>
    </div>
<br>
    <a class="back-btn" href="{{ route('products.index') }}">
    <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
</a>
     
     


{{-- đánh giá sản phầm của nam --}}



<h4 class="mt-4">{{ __('messages.product_review') }}</h4>

@if ($userCanReview)
    <form action="{{ route('product.review.store', ['id' => $product->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>{{ __('messages.your_review') }}</label>
        <select name="rating" required>
            <option value="5">⭐⭐⭐⭐⭐{{ __('messages.rating') }}</option>
            <option value="4">⭐⭐⭐⭐4 {{ __('messages.rating') }}</option>
            <option value="3">⭐⭐⭐3 {{ __('messages.rating') }}</option>
            <option value="2">⭐⭐2 {{ __('messages.rating') }}</option>
            <option value="1">⭐1 {{ __('messages.rating') }}</option>
        </select>
        <textarea name="comment" class="form-control" placeholder="{{ __('messages.write_review') }}" required></textarea>

        {{-- Upload ảnh --}}
        <label class="mt-2">{{ __('messages.upload_images') }}:</label>
        <input type="file" name="images[]" class="form-control" multiple accept="image/*" onchange="previewImages(event)">
        <div id="image-preview" class="mt-2"></div>

        {{-- Upload video --}}
        <label class="mt-2">{{ __('messages.upload_video') }}</label>
        <input type="file" name="video" class="form-control" accept="video/mp4" onchange="previewVideo(event)">
        <div id="video-preview" class="mt-2"></div>

        <button type="submit" class="btn btn-primary mt-2">{{ __('messages.submit_review') }}</button>
    </form>
@else
<p><i>{{ __('messages.only_purchased') }}</i></p>
@endif

{{-- Script để xem trước ảnh và video trước khi upload --}}
<script>
    function previewImages(event) {
        let files = event.target.files;
        let preview = document.getElementById('image-preview');
        preview.innerHTML = "";
        if (files.length > 5) {
            alert("Chỉ được chọn tối đa 5 ảnh!");
            event.target.value = "";
            return;
        }
        for (let i = 0; i < files.length; i++) {
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = document.createElement("img");
                img.src = e.target.result;
                img.classList.add("img-thumbnail", "mr-2");
                img.width = 100;
                preview.appendChild(img);
            };
            reader.readAsDataURL(files[i]);
        }
    }

    function previewVideo(event) {
        let file = event.target.files[0];
        let preview = document.getElementById('video-preview');
        preview.innerHTML = "";
        if (file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                let video = document.createElement("video");
                video.src = e.target.result;
                video.width = 200;
                video.controls = true;
                preview.appendChild(video);
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@foreach ($reviews as $review)
    <div class="review-item mb-3">
        <strong>{{ $review->user->name }}</strong> - <span>{{ $review->created_at->format('d/m/Y') }}</span>
        <br>
        <span>
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= $review->rating)
                    ⭐
                @else
                    ☆
                @endif
            @endfor
        </span>
        <p>{{ $review->comment }}</p>

        {{-- Hiển thị ảnh nếu có --}}
        @if ($review->images)
            @php
                $images = json_decode($review->images, true);
            @endphp
            <div class="review-images mt-2">
                @foreach ($images as $image)
                    <img src="{{ asset('storage/' . $image) }}" alt="Ảnh đánh giá" class="img-thumbnail" width="100">
                @endforeach
            </div>
        @endif

        {{-- Hiển thị video nếu có --}}
        @if ($review->video)
            <div class="review-video mt-2">
                <video width="200" controls>
                    <source src="{{ asset('storage/' . $review->video) }}" type="video/mp4">
                    Trình duyệt của bạn không hỗ trợ video.
                </video>
            </div>
        @endif

        {{-- Hiển thị nút xóa nếu đây là đánh giá của user hiện tại --}}
        @if (auth()->check() && auth()->id() == $review->user_id)
            <form action="{{ route('reviews.destroy', ['id' => $review->id]) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này không?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm mt-2">Xóa</button>
            </form>
        @endif
    </div>
    <hr>
@endforeach




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

        function openSizeGuide() {
    document.getElementById("sizeGuideModal").style.display = "flex";
}

function closeSizeGuide() {
    document.getElementById("sizeGuideModal").style.display = "none";
}

// Đóng modal khi nhấn ra ngoài
window.onclick = function(event) {
    let modal = document.getElementById("sizeGuideModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}


// zoom ảnh
document.addEventListener("DOMContentLoaded", function () {
    const image = document.getElementById("variant-image");

    image.addEventListener("mousemove", function (e) {
        let rect = image.getBoundingClientRect();
        let x = (e.clientX - rect.left) / rect.width * 100;
        let y = (e.clientY - rect.top) / rect.height * 100;
        
        image.style.transformOrigin = `${x}% ${y}%`;
    });

    image.addEventListener("mouseleave", function () {
        image.style.transformOrigin = "center center";
    });
});

//nut yeu thich
document.getElementById("favoriteButton").addEventListener("click", function() {
    this.classList.toggle("liked");
    let icon = this.querySelector("i");
    if (this.classList.contains("liked")) {
        icon.classList.remove("bi-heart");
        icon.classList.add("bi-heart-fill");
    } else {
        icon.classList.remove("bi-heart-fill");
        icon.classList.add("bi-heart");
    }
});

// so luong

document.addEventListener("DOMContentLoaded", function () {
    const decreaseBtn = document.getElementById("decrease");
    const increaseBtn = document.getElementById("increase");
    const quantityInput = document.getElementById("quantity");

    decreaseBtn.addEventListener("click", function () {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });

    increaseBtn.addEventListener("click", function () {
        let currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1;
    });
});
        </script>
        
        


        @include('Users.chat')
@endsection
