<!-- Chi tiet sp -->
@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/chitietsp.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <hr>

    <div class="container">


        @if ($product->image)
            <div class="row">
                <div class="col-md-6 mt-5">
                    <div class="d-flex align-items-start">
                        <!-- Cột trái: Ảnh nhỏ -->
                        <div class="me-3">
                            <div class="d-flex flex-column gap-2 align-items-center">
                                @foreach ($product->variants->take(3) as $variant)
                                    <img src="{{ asset('storage/' . $variant->image) }}"
                                        alt="{{ $variant->color }} - {{ $variant->size }}"
                                        class="img-thumbnail variant-thumbnail" width="150" height="150"
                                        data-variant-id="{{ $variant->id }}" data-size="{{ $variant->size }}"
                                        data-color="{{ $variant->color }}">
                                @endforeach
                            </div>
                        </div>

                        <!-- Cột phải: Ảnh lớn -->
                        <div class="zoom-box">
                            <img id="variant-image" src="{{ asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}" class="img-fluid zoom-image">
                            @php
                                $outOfStock =
                                    $product->variants && $product->variants->count() > 0
                                        ? $product->variants->sum('stock_quantity') == 0
                                        : true;
                            @endphp

                            @if ($outOfStock)
                                <div class="overlay-out-of-stock">
                                    <span>Sản phẩm tạm hết hàng</span>
                                </div>
                            @endif
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
                <p class="sku">
                    
                    <span class="rating">
                        @php
                            $averageRating = \App\Models\Review::where('product_id', $product->id)->avg('rating') ?? 0;
                            $ratingCount = \App\Models\Review::where('product_id', $product->id)->count();
                            $averageRating = round($averageRating, 1);
                        @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $averageRating ? 'text-warning' : 'text-muted' }}">★</span>
                        @endfor
                        ({{ $averageRating }}) <span class="reviews">{{ $ratingCount }}
                            {{ __('messages.reviews') }}</span>
                    </span>
                </p>

                <p class="price">
                    @if ($product->base_price !== null && $product->base_price !== '')
                        <span id="base-price">{{ number_format($product->base_price, 0, ',', '.') }} VNĐ</span>
                    @endif
                    <span id="variant-price" style="font-weight: bold; e color: red;">
                        {{ number_format($minPrice, 0, ',', '.') }} - {{ number_format($maxPrice, 0, ',', '.') }} VNĐ
                    </span>
                </p>
                @php
                $totalStock = $product->variants->sum('stock_quantity');
            @endphp
            
            <div id="overall-stock-status">
                <strong>
                    @if ($totalStock == 0)
                        {{ __('messages.out_of_stock') }}
                    @elseif ($totalStock < 20)
                        Sắp hết hàng
                    @else
                        Còn hàng
                    @endif
                </strong>
            </div>
                           
                <p id="stock-info"></p>
                <p style="display: none"><strong style="display: block">{{ __('messages.stock') }} </strong>
                    <span style="display: block" id="stock-info">{{ $product->variants->sum('stock_quantity') }}</span>
                </p>
                <hr>

                <!--
                                        <div>
                                            <img id="product-image" src="{{ $product->image }}" alt="{{ $product->name }}"
                                                style="max-width: 300px; display: block;">
                                        </div> -->


                <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="variant_id" value="">


                    @if ($product->variants->whereNotNull('size')->count() > 0)
                        <div>

                            <div id="size-options">
                                <p class="mt-2"><strong>{{ __('messages.size') }}</strong></p>
                                @foreach ($product->variants->groupBy('size') as $size => $variants)
                                    <button type="button" class="size-btn"
                                        data-size="{{ $size }}">{{ $size }}</button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <br>
                    <div>


                        <div id="color-options">
                            <strong class="mt-2">{{ __('messages.color') }}</strong>
                            @foreach ($product->variants->unique('color') as $variant)
                                <button type="button" class="color-btn" data-color="{{ $variant->color }}"
                                    style="background-color: {{ $variant->color }}; width: 32px; height: 32px; border: 1px solid #ccc; border-radius: 50%;">
                                </button>
                            @endforeach

                        </div>
                        <br>
                        <!-- </div><p><strong>{{ __('messages.selected_variant') }} </strong> <span id="selected-variant-info">{{ __('messages.not_selected') }}</span></p> -->



                        <!-- <label for="quantity" class="quantity-label">{{ __('messages.quantity') }}</label> -->

                        <!-- <div class="quantity-controls">
                                            <button type="button" class="quantity-btn" id="decrease">−</button>
                                            <input type="text" id="quantity" name="quantity" value="1" min="1" class="quantity-input" readonly>
                                            <button type="button" class="quantity-btn" id="increase">+</button>
                                        </div> -->

                        <div class="quantity-wrapper">
                            <label for="quantity" class="quantity-label">Số lượng:</label>
                            <div class="quantity-controls">
                                <button type="button" class="quantity-btn" id="decrease" disabled>−</button>
                                <input type="number" id="quantity" name="quantity" value="1" min="1"
                                    class="quantity-input" disabled>
                                <button type="button" class="quantity-btn" id="increase" disabled>+</button>
                            </div>
                        </div>

                        {{-- ========================== Phần này của Đạt thông báo lỗi ============================ --}}
                        @if (session('error'))
                            <small class="text-danger" id="error-message">{{ session('error') }}</small>
                            <script>
                                setTimeout(function() {
                                    document.getElementById('error-message').style.display = 'none';
                                }, 5000);
                            </script>
                        @endif
                        @if (session('success'))
                            <small class="text-success" id="success-message">{{ session('success') }}</small>
                            <script>
                                setTimeout(function() {
                                    document.getElementById('success-message').style.display = 'none';
                                }, 5000);
                            </script>
                        @endif
                        {{-- ========================= Hết phần của Đạt ============================ --}}



                        <div class="mt-3 d-flex align-items-center">
                            @if (!$product->is_delete == '1')
                                <p class="stock-info" id="stock-info"></p>
                                <button type="submit" id="addToCartButton" disabled
                                    class="btn btn-outline-dark  custom-add-to-cart">
                                    {{ __('messages.add_to_cart') }}
                                </button>
                            @else
                                <div class="alert alert-warning text-center fw-bold" role="alert">
                                    <h4 class="mb-0 text-danger">Sản phẩm đang tạm dừng bán</h4>
                                </div>
                            @endif
                        </div>


                        <br>
                        <a href="#" class="text-primary"
                            onclick="openSizeGuide()">{{ __('messages.size_guide') }}</a>


                        <!-- Modal hiển thị bảng size -->
                        <div id="sizeGuideModal" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="closeSizeGuide()">&times;</span>
                                <img src="../assets/img/bangsize.jpg" alt="Bảng size áo nữ">
                            </div>
                        </div>
                        <hr>
                        <span>
                            <strong>Mô tả:</strong> {{ $product->description }}
                        </span>
                        <br>
                        <span id="long-description" style="display: none;">
                            <strong>Mô tả dài:</strong> {{ $product->long_description }}
                        </span>
                        <br>
                        <button type="button" id="toggle-description" class="btn btn-link p-0" style="color: black; margin-bottom:10px;    margin-left: 43%; cursor: pointer;text-aligh:center">
                            Xem thêm ▼
                        </button>
                        
                        
                        <br>
                        <div class="info-banner">
                            <div class="info-box">
                                <img src="https://img.icons8.com/ios-filled/40/000000/phone.png" alt="Call icon">
                                <div>
                                    <p>Mua hàng: <span class="highlight">0912.743.443</span> từ 8h00 -<br>21h30 mỗi ngày
                                    </p>
                                </div>
                            </div>
                            <div class="info-box">
                                <img src="https://img.icons8.com/ios-filled/40/000000/delivery.png"
                                    alt="Free Shipping icon">
                                <div>
                                    <p>Giao hàng nhanh<br><span class="highlight">3-5 ngày</span></p>
                                </div>
                            </div>
                            <div class="info-box">
                                <img src="https://img.icons8.com/ios-filled/40/000000/return-purchase.png"
                                    alt="Return icon">
                                <div>
                                    <p>Tiết kiệm lên tới<br><span class="highlight">20%</span></p>
                                </div>
                            </div>
                        </div>



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

    <?php
    // Kiểm tra xem người dùng có thể đánh giá hay không
    $userCanReview = false;
    $debugOrders = []; // Debug
    if (auth()->check()) {
        $userId = auth()->id();
        // Lấy tất cả đơn hàng hoàn thành chứa sản phẩm chưa được đánh giá
        $orders = \App\Models\Order::where('user_id', $userId)
            ->where('status', 'Hoàn thành')
            ->whereHas('orderItems', function ($query) use ($product) {
                $query->where('product_id', $product->id)->where('has_reviewed', 0);
            })
            ->pluck('id')
            ->toArray();
    
        $debugOrders = $orders; // Debug
        $userCanReview = count($orders) > 0;
    }
    ?>



    <div class="review-container d-flex">
        <!-- Danh sách đánh giá (Bên trái) -->
        <div class="review-list flex-fill me-3">
            <div id="review-list">
                @foreach ($reviews->take(2) as $review)
                    <div class="review-item mb-3 p-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $review->user->name }}</strong>
                                <span class="text-muted">({{ $review->created_at->format('d/m/Y H:i') }})</span>
                            </div>
                        </div>
                        <div class="star-rating mt-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}">★</span>
                            @endfor
                        </div>
                        <p class="mt-2">{{ $review->comment }}</p>

                        @if ($review->images)
                            @php
                                $images = json_decode($review->images, true);
                            @endphp
                            @if (is_array($images))
                                <div class="review-images mt-2 d-flex flex-wrap">
                                    @foreach ($images as $image)
                                        <img src="{{ asset('storage/' . $image) }}" alt="Ảnh đánh giá"
                                            class="img-thumbnail me-2 mb-2" style="width: 100px;">
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        @if ($review->video)
                            <div class="review-video mt-2">
                                <video width="200" controls class="rounded">
                                    <source src="{{ asset('storage/' . $review->video) }}" type="video/mp4">
                                    Trình duyệt của bạn không hỗ trợ video.
                                </video>
                            </div>
                        @endif

                        @if (auth()->check() && auth()->id() == $review->user_id)
                            <form action="{{ route('reviews.destroy', ['id' => $review->id]) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này không?');" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        @endif
                    </div>
                    <hr>
                @endforeach

                <!-- Đánh giá ẩn (Ban đầu bị thu gọn) -->
                @if ($reviews->count() > 2)
                    <div id="hidden-reviews" style="display: none;">
                        @foreach ($reviews->slice(2) as $review)
                            <div class="review-item mb-3 p-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $review->user->name }}</strong>
                                        <span class="text-muted">({{ $review->created_at->format('d/m/Y H:i') }})</span>
                                    </div>
                                </div>
                                <div class="star-rating mt-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}">★</span>
                                    @endfor
                                </div>
                                <p class="mt-2">{{ $review->comment }}</p>

                                @if ($review->images)
                                    @php
                                        $images = json_decode($review->images, true);
                                    @endphp
                                    @if (is_array($images))
                                        <div class="review-images mt-2 d-flex flex-wrap">
                                            @foreach ($images as $image)
                                                <img src="{{ asset('storage/' . $image) }}" alt="Ảnh đánh giá"
                                                    class="img-thumbnail me-2 mb-2" style="width: 100px;">
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                @if ($review->video)
                                    <div class="review-video mt-2">
                                        <video width="200" controls class="rounded">
                                            <source src="{{ asset('storage/' . $review->video) }}" type="video/mp4">
                                            Trình duyệt của bạn không hỗ trợ video.
                                        </video>
                                    </div>
                                @endif

                                @if (auth()->check() && auth()->id() == $review->user_id)
                                    <form action="{{ route('reviews.destroy', ['id' => $review->id]) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này không?');"
                                        class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                @endif
                            </div>
                            <hr>
                        @endforeach
                    </div>
                    <button id="toggle-reviews" class="btn btn-link mb-3">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                @endif
            </div>
        </div>

        <!-- Form đánh giá (Bên phải) -->
        <div class="review-form" style="min-width: 300px;">
            @if ($userCanReview)
                <form action="{{ route('product.review.store', ['id' => $product->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <label>Đánh giá của bạn</label>
                    <select name="rating" required class="form-select mb-2">
                        <option value="5">⭐⭐⭐⭐⭐ 5 sao</option>
                        <option value="4">⭐⭐⭐⭐ 4 sao</option>
                        <option value="3">⭐⭐⭐ 3 sao</option>
                        <option value="2">⭐⭐ 2 sao</option>
                        <option value="1">⭐ 1 sao</option>
                    </select>
                    <textarea name="comment" class="form-control mb-2" placeholder="Viết đánh giá của bạn" required></textarea>

                    <label class="mt-2">Tải ảnh lên:</label>
                    <input type="file" name="images[]" class="form-control mb-2" multiple accept="image/*"
                        onchange="previewImages(event)">
                    <div id="image-preview" class="mt-2 d-flex flex-wrap"></div>

                    <label class="mt-2">Tải video lên:</label>
                    <input type="file" name="video" class="form-control mb-2" accept="video/mp4"
                        onchange="previewVideo(event)">
                    <div id="video-preview" class="mt-2"></div>

                    <button type="submit" class="btn btn-primary mt-2">Gửi đánh giá</button>
                </form>
            @else
                <p><i>Chỉ những người đã mua hàng mới có thể đánh giá</i></p>
            @endif
        </div>
    </div>
{{-- Js nút trỏ xuống xem thêm của mô tả dài --}}
    <script>
        document.getElementById('toggle-description').addEventListener('click', function() {
            const longDescription = document.getElementById('long-description');
            const isHidden = longDescription.style.display === 'none';
            
            longDescription.style.display = isHidden ? 'block' : 'none';
            this.textContent = isHidden ? 'Thu gọn ▲' : 'Xem thêm ▼';
        });
        </script>

    <!-- JavaScript cho xem trước ảnh và video -->
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
                    img.classList.add("img-thumbnail", "me-2", "mb-2");
                    img.style.width = "100px";
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
                    video.style.width = "200px";
                    video.controls = true;
                    preview.appendChild(video);
                };
                read
                er.readAsDataURL(file);
            }
        }

        // JavaScript để thu gọn/mở rộng đánh giá
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggle-reviews');
            const hiddenReviews = document.getElementById('hidden-reviews');

            if (toggleButton && hiddenReviews) {
                const icon = toggleButton.querySelector('i');
                toggleButton.addEventListener('click', function() {
                    if (hiddenReviews.style.display === 'none') {
                        hiddenReviews.style.display = 'block';
                        icon.classList.remove('bi-chevron-down');
                        icon.classList.add('bi-chevron-up');
                    } else {
                        hiddenReviews.style.display = 'none';
                        icon.classList.remove('bi-chevron-up');
                        icon.classList.add('bi-chevron-down');
                    }
                });
            }
        });
    </script>

    <!-- CSS cập nhật -->
    <style>
        .review-container {
            display: flex;
            gap: 20px;
        }

        .review-list {
            flex: 2;
        }

        .review-form {
            flex: 1;
            position: sticky;
            top: 20px;
        }

        .review-item {
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .star-rating {
            display: block;
        }

        #toggle-reviews {
            text-decoration: none;
            font-size: 1.2rem;
            /* Kích thước icon */
        }

        @media (max-width: 768px) {
            .review-container {
                flex-direction: column;
            }

            .review-form {
                position: static;
            }
        }
    </style>







    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function updateColorButtons(size) {
                let availableColors = variants
                    .filter(variant => variant.size === size)
                    .map(variant => variant.color);

                document.querySelectorAll('.color-btn').forEach(button => {
                    let color = button.getAttribute('data-color');
                    if (availableColors.includes(color)) {
                        button.disabled = false;
                        button.style.opacity = "1";
                        button.style.pointerEvents = "auto";
                    } else {
                        button.disabled = true;
                        button.style.opacity = "0.3";
                        button.style.pointerEvents = "none";
                    }
                });
            }

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
                    const stockInfoElement = document.getElementById("stock-info");
                    document.getElementById("overall-stock-status").style.display = "none";
                    const quantity = variant.stock_quantity;
if (variant.stock_quantity === 0) {
    stockInfoElement.textContent = `Sản phẩm hết hàng (0 sản phẩm)`;
    stockInfoElement.style.color = "gray";
} else if (variant.stock_quantity < 20) {
    stockInfoElement.textContent = `Sắp hết hàng (${quantity} sản phẩm)`;
    stockInfoElement.style.color = "orange";
} else {
    stockInfoElement.textContent = `Còn hàng (${quantity} sản phẩm)`;
    stockInfoElement.style.color = "green";
}

                    document.getElementById("variant-price").textContent =
                        new Intl.NumberFormat('vi-VN').format(variant.price) + " VNĐ";
                    document.getElementById("addToCartButton").disabled = false;
                    document.getElementById("quantity").disabled = false;
                    document.getElementById("increase").disabled = false;
                    document.getElementById("decrease").disabled = false;

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
                    document.getElementById("overall-stock-status").style.display = "block";
                    document.getElementById("variant_id").value = "";
                    document.getElementById("stock-info").textContent = "";
                    document.getElementById("variant-price").textContent = "";
                    document.getElementById("addToCartButton").disabled = true;
                    document.getElementById("quantity").disabled = true;
                    document.getElementById("increase").disabled = true;
                    document.getElementById("decrease").disabled = true;
                    quantityInput.style.opacity = "0.5";
                    increaseBtn.style.opacity = "0.5";
                    decreaseBtn.style.opacity = "0.5";
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
                        updateColorButtons(selectedSize);
                        selectedColor = null;
                        selectedVariant = null;
                        document.querySelectorAll(".color-btn").forEach(btn => {
                            btn.classList.remove("active");
                            btn.disabled = true;
                        });

                        updateVariant(null);
                    } else {
                        selectedSize = this.getAttribute("data-size");
                        document.querySelectorAll(".size-btn").forEach(btn => btn.classList.remove(
                            "active"));
                        this.classList.add("active");
                        updateColorButtons(selectedSize);

                        selectedColor = null;
                        selectedVariant = null;
                        document.querySelectorAll(".color-btn").forEach(btn => {
                            btn.classList.remove("active");
                            btn.disabled = true;
                        });

                        let availableColors = variants.filter(v => v.size === selectedSize).map(v =>
                            v.color);
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
                        document.querySelectorAll(".color-btn").forEach(btn => btn.classList.remove(
                            "active"));
                        this.classList.add("active");

                        selectedVariant = variants.find(v => v.size === selectedSize && v.color ===
                            selectedColor);
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
        document.addEventListener("DOMContentLoaded", function() {
            const image = document.getElementById("variant-image");

            image.addEventListener("mousemove", function(e) {
                let rect = image.getBoundingClientRect();
                let x = (e.clientX - rect.left) / rect.width * 100;
                let y = (e.clientY - rect.top) / rect.height * 100;

                image.style.transformOrigin = `${x}% ${y}%`;
            });

            image.addEventListener("mouseleave", function() {
                image.style.transformOrigin = "center center";
            });
        });

        // so luong

        document.addEventListener("DOMContentLoaded", function() {
            const decreaseBtn = document.getElementById("decrease");
            const increaseBtn = document.getElementById("increase");
            const quantityInput = document.getElementById("quantity");

            decreaseBtn.addEventListener("click", function() {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });

            increaseBtn.addEventListener("click", function() {
                let currentValue = parseInt(quantityInput.value);
                quantityInput.value = currentValue + 1;
            });
        });
    </script>

    <script>
        document.querySelectorAll('.color-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Bỏ class selected khỏi tất cả button
                document.querySelectorAll('.color-btn').forEach(btn => btn.classList.remove('selected'));
                // Thêm class selected vào button được chọn
                button.classList.add('selected');
            });
        });
    </script>




    @include('Users.chat')
@endsection
<style>
    .color-btn:disabled {
        cursor: not-allowed;
        border: 1px dashed #999;
    }


    .color-btn.selected {
        border: 2px solid #1890ff;
        /* Xanh dương nổi bật */
        box-shadow: 0 0 0px #000000;
        transform: scale(1.3);
        /* Phóng to nhẹ cho nổi bật */
    }

    .custom-add-to-cart {

        padding: 10px 20px;

        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        width: 60%;

    }

    .custom-add-to-cart:hover {
        background: #ff0000;
        /* Red background on hover */
        color: #fff;
        /* White text on hover */
        border-color: #ff0000;
        /* Match border to background */
    }

    .custom-add-to-cart::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg,
                transparent,
                rgba(255, 255, 255, 0.4),
                transparent);
        transition: 0.6s ease;
    }

    .custom-add-to-cart:hover::before {
        left: 100%;
        /* Chasing animation on hover */
    }
</style>
