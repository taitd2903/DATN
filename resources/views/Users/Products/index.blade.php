@extends('layouts.app')
@section('content')
    <div id="slides-shop" class="cover-slides">
        <ul class="slides-container">
            @foreach ($banners as $key => $banner)
                @if ($banner->is_active)
                    <li class="{{ $key == 0 ? 'text-left' : ($key == 1 ? 'text-center' : 'text-right') }}">
                        <img src="{{ asset('storage/' . $banner->image) }}" alt="Banner {{ $key + 1 }}">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <h1 class="m-b-20"><strong>{{ $banner->title }}</strong></h1>
                                    <p class="m-b-40">{{ $banner->description }}</p>
                                    @if ($banner->link)
                                        <p><a class="btn hvr-hover" href="{{ $banner->link }}">Mua ngay</a></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

    <div class="container my-5">
        <!-- Sản phẩm mới -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4 animate__animated animate__fadeIn">
                <h1 class="display-6 section-title">Sản phẩm mới <i class="fas fa-box-open text-primary ms-2"></i></h1>
            </div>
            <div class="col-12">
                <div class="carousel slide" id="newProductsCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($products->chunk(4) as $chunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row justify-content-center">
                                    @foreach($chunk as $product)
                                        <div class="col-md-2 col-6 mb-4 animate__animated animate__fadeInUp">
                                            <div class="card h-100 border-0 product-card">
                                                <a href="{{ route('products.show', $product->id) }}">
                                                    @php
                                                        $defaultImage = $product->image ? asset('storage/' . $product->image) : asset('path/to/default-image.jpg');
                                                        $firstVariantImage = $product->variants->whereNotNull('image')->first();
                                                        $defaultImage = $firstVariantImage ? asset('storage/' . $firstVariantImage->image) : $defaultImage;
                                                    @endphp
                                                    <img src="{{ $defaultImage }}" class="card-img-top product-image" data-default-image="{{ $defaultImage }}" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                                                </a>
                                                <div class="card-body">
                                                    <p class="card-title" style="font-size: 14px; font-weight: 500;">{{ Str::limit($product->name, 30) }}</p>
                                                    @php
                                                        $minPrice = $product->variants->min('price') ?? 0;
                                                        $maxPrice = $product->variants->max('price') ?? 0;
                                                    @endphp
                                                    <p class="card-text text-danger text-left">{{ number_format($minPrice, 0, ',', '.') }}đ</p>
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            @foreach($product->variants as $variant)
                                                                @if($variant->color)
                                                                    <span class="color-dot variant-color" data-image="{{ $variant->image ? asset('storage/' . $variant->image) : $defaultImage }}" style="background-color: {{ $variant->color }}; width: 12px; height: 12px; border-radius: 50%; margin: 0 3px; display: inline-block; cursor: pointer;"></span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <span class="heart-icon" style="color: #ff4d4f; font-size: 12px;">❤️</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="rating" style="font-size: 10px;">
                                                            ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                                        </span>
                                                        <span class="ms-2" style="font-size: 12px; color: #777;">({{ $product->variants->sum('sold_quantity') }} đã bán)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#newProductsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#newProductsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="product-list">
                    <div class="row justify-content-center">
                        @foreach($products->take(8) as $product)
                            <div class="col-md-2 col-6 mb-4 animate__animated animate__fadeInUp">
                                <div class="card h-100 border-0 product-card">
                                    <a href="{{ route('products.show', $product->id) }}">
                                        @php
                                            $defaultImage = $product->image ? asset('storage/' . $product->image) : asset('path/to/default-image.jpg');
                                            $firstVariantImage = $product->variants->whereNotNull('image')->first();
                                            $defaultImage = $firstVariantImage ? asset('storage/' . $firstVariantImage->image) : $defaultImage;
                                        @endphp
                                        <img src="{{ $defaultImage }}" class="card-img-top product-image" data-default-image="{{ $defaultImage }}" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                                    </a>
                                    <div class="card-body">
                                        <p class="card-title" style="font-size: 14px; font-weight: 500;">{{ Str::limit($product->name, 30) }}</p>
                                        @php
                                            $minPrice = $product->variants->min('price') ?? 0;
                                            $maxPrice = $product->variants->max('price') ?? 0;
                                        @endphp
                                        <p class="card-text text-danger text-left">{{ number_format($minPrice, 0, ',', '.') }}đ</p>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                @foreach($product->variants as $variant)
                                                    @if($variant->color)
                                                        <span class="color-dot variant-color" data-image="{{ $variant->image ? asset('storage/' . $variant->image) : $defaultImage }}" style="background-color: {{ $variant->color }}; width: 12px; height: 12px; border-radius: 50%; margin: 0 3px; display: inline-block; cursor: pointer;"></span>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <span class="heart-icon" style="color: #ff4d4f; font-size: 12px;">❤️</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="rating" style="font-size: 10px;">
                                                ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                            </span>
                                            <span class="ms-2" style="font-size: 12px; color: #777;">({{ $product->variants->sum('sold_quantity') }} đã bán)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Banner quảng cáo giữa các phần -->
        <div class="row my-5">
            <div class="col-12">
                <div class="promo-banner animate__animated animate__fadeIn">
                    <img src="https://file.hstatic.net/1000398692/collection/thai-hien-sport-banner-dung-cu-tap-the-thao_726300d647d34b3297269d9609f1209d.jpg" alt="Promo Banner" class="img-fluid rounded">
                    <div class="banner-content">
                        <h3 class="text-white">Ưu Đãi Đặc Biệt!</h3>
                        <p class="text-white">Giảm giá lên đến 50% cho các sản phẩm mới!</p>
                        <a href="#" class="btn btn-primary rounded-pill">Khám phá ngay</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sản phẩm nổi bật -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4 animate__animated animate__fadeIn">
                <h1 class="display-6 section-title">Sản phẩm nổi bật <i class="fas fa-star text-warning ms-2"></i></h1>
            </div>
            <div class="col-12">
                <div class="carousel slide" id="featuredProductsCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($products->chunk(4) as $chunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row justify-content-center">
                                    @foreach($chunk as $product)
                                        <div class="col-md-2 col-6 mb-4 animate__animated animate__fadeInUp">
                                            <div class="card h-100 border-0 product-card">
                                                <a href="{{ route('products.show', $product->id) }}">
                                                    @php
                                                        $defaultImage = $product->image ? asset('storage/' . $product->image) : asset('path/to/default-image.jpg');
                                                        $firstVariantImage = $product->variants->whereNotNull('image')->first();
                                                        $defaultImage = $firstVariantImage ? asset('storage/' . $firstVariantImage->image) : $defaultImage;
                                                    @endphp
                                                    <img src="{{ $defaultImage }}" class="card-img-top product-image" data-default-image="{{ $defaultImage }}" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                                                </a>
                                                <div class="card-body">
                                                    <p class="card-title" style="font-size: 14px; font-weight: 500;">{{ Str::limit($product->name, 30) }}</p>
                                                    @php
                                                        $minPrice = $product->variants->min('price') ?? 0;
                                                        $maxPrice = $product->variants->max('price') ?? 0;
                                                    @endphp
                                                    <p class="card-text text-danger text-left">{{ number_format($minPrice, 0, ',', '.') }}đ</p>
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            @foreach($product->variants as $variant)
                                                                @if($variant->color)
                                                                    <span class="color-dot variant-color" data-image="{{ $variant->image ? asset('storage/' . $variant->image) : $defaultImage }}" style="background-color: {{ $variant->color }}; width: 12px; height: 12px; border-radius: 50%; margin: 0 3px; display: inline-block; cursor: pointer;"></span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <span class="heart-icon" style="color: #ff4d4f; font-size: 12px;">❤️</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="rating" style="font-size: 10px;">
                                                            ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                                        </span>
                                                        <span class="ms-2" style="font-size: 12px; color: #777;">({{ $product->variants->sum('sold_quantity') }} đã bán)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#featuredProductsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#featuredProductsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="product-list">
                    <div class="row justify-content-center">
                        @foreach($products->take(8) as $product)
                            <div class="col-md-2 col-6 mb-4 animate__animated animate__fadeInUp">
                                <div class="card h-100 border-0 product-card">
                                    <a href="{{ route('products.show', $product->id) }}">
                                        @php
                                            $defaultImage = $product->image ? asset('storage/' . $product->image) : asset('path/to/default-image.jpg');
                                            $firstVariantImage = $product->variants->whereNotNull('image')->first();
                                            $defaultImage = $firstVariantImage ? asset('storage/' . $firstVariantImage->image) : $defaultImage;
                                        @endphp
                                        <img src="{{ $defaultImage }}" class="card-img-top product-image" data-default-image="{{ $defaultImage }}" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                                    </a>
                                    <div class="card-body">
                                        <p class="card-title" style="font-size: 14px; font-weight: 500;">{{ Str::limit($product->name, 30) }}</p>
                                        @php
                                            $minPrice = $product->variants->min('price') ?? 0;
                                            $maxPrice = $product->variants->max('price') ?? 0;
                                        @endphp
                                        <p class="card-text text-danger text-left">{{ number_format($minPrice, 0, ',', '.') }}đ</p>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                @foreach($product->variants as $variant)
                                                    @if($variant->color)
                                                        <span class="color-dot variant-color" data-image="{{ $variant->image ? asset('storage/' . $variant->image) : $defaultImage }}" style="background-color: {{ $variant->color }}; width: 12px; height: 12px; border-radius: 50%; margin: 0 3px; display: inline-block; cursor: pointer;"></span>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <span class="heart-icon" style="color: #ff4d4f; font-size: 12px;">❤️</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="rating" style="font-size: 10px;">
                                                ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                            </span>
                                            <span class="ms-2" style="font-size: 12px; color: #777;">({{ $product->variants->sum('sold_quantity') }} đã bán)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Banner quảng cáo thứ hai -->
        <div class="row my-5">
            <div class="col-12">
                <div class="promo-banner animate__animated animate__fadeIn">
                    <img src="https://file.hstatic.net/1000398692/collection/banner_giay_pan_ps_7686edaa13ec4b06a1f244428a72d164.jpg" alt="Promo Banner 2" class="img-fluid rounded">
                    <div class="banner-content">
                        <h3 class="text-white">Khuyến Mãi Lớn!</h3>
                        <p class="text-white">Mua 1 tặng 1 cho tất cả các sản phẩm nổi bật!</p>
                        <a href="#" class="btn btn-warning rounded-pill">Xem ngay</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sản phẩm nhiều người chú ý -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4 animate__animated animate__fadeIn">
                <h1 class="display-6 section-title">Sản phẩm nhiều người chú ý <i class="fas fa-eye text-success ms-2"></i></h1>
            </div>
            <div class="col-12">
                <div class="carousel slide" id="notableProductsCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($products->chunk(4) as $chunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row justify-content-center">
                                    @foreach($chunk as $product)
                                        <div class="col-md-2 col-6 mb-4 animate__animated animate__fadeInUp">
                                            <div class="card h-100 border-0 product-card">
                                                <a href="{{ route('products.show', $product->id) }}">
                                                    @php
                                                        $defaultImage = $product->image ? asset('storage/' . $product->image) : asset('path/to/default-image.jpg');
                                                        $firstVariantImage = $product->variants->whereNotNull('image')->first();
                                                        $defaultImage = $firstVariantImage ? asset('storage/' . $firstVariantImage->image) : $defaultImage;
                                                    @endphp
                                                    <img src="{{ $defaultImage }}" class="card-img-top product-image" data-default-image="{{ $defaultImage }}" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                                                </a>
                                                <div class="card-body">
                                                    <p class="card-title" style="font-size: 14px; font-weight: 500;">{{ Str::limit($product->name, 30) }}</p>
                                                    @php
                                                        $minPrice = $product->variants->min('price') ?? 0;
                                                        $maxPrice = $product->variants->max('price') ?? 0;
                                                    @endphp
                                                    <p class="card-text text-danger text-left">{{ number_format($minPrice, 0, ',', '.') }}đ</p>
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            @foreach($product->variants as $variant)
                                                                @if($variant->color)
                                                                    <span class="color-dot variant-color" data-image="{{ $variant->image ? asset('storage/' . $variant->image) : $defaultImage }}" style="background-color: {{ $variant->color }}; width: 12px; height: 12px; border-radius: 50%; margin: 0 3px; display: inline-block; cursor: pointer;"></span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <span class="heart-icon" style="color: #ff4d4f; font-size: 12px;">❤️</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="rating" style="font-size: 10px;">
                                                            ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                                        </span>
                                                        <span class="ms-2" style="font-size: 12px; color: #777;">({{ $product->variants->sum('sold_quantity') }} đã bán)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#notableProductsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#notableProductsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="product-list">
                    <div class="row justify-content-center">
                        @foreach($products->take(8) as $product)
                            <div class="col-md-2 col-6 mb-4 animate__animated animate__fadeInUp">
                                <div class="card h-100 border-0 product-card">
                                    <a href="{{ route('products.show', $product->id) }}">
                                        @php
                                            $defaultImage = $product->image ? asset('storage/' . $product->image) : asset('path/to/default-image.jpg');
                                            $firstVariantImage = $product->variants->whereNotNull('image')->first();
                                            $defaultImage = $firstVariantImage ? asset('storage/' . $firstVariantImage->image) : $defaultImage;
                                        @endphp
                                        <img src="{{ $defaultImage }}" class="card-img-top product-image" data-default-image="{{ $defaultImage }}" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                                    </a>
                                    <div class="card-body">
                                        <p class="card-title" style="font-size: 14px; font-weight: 500;">{{ Str::limit($product->name, 30) }}</p>
                                        @php
                                            $minPrice = $product->variants->min('price') ?? 0;
                                            $maxPrice = $product->variants->max('price') ?? 0;
                                        @endphp
                                        <p class="card-text text-danger text-left">{{ number_format($minPrice, 0, ',', '.') }}đ</p>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                @foreach($product->variants as $variant)
                                                    @if($variant->color)
                                                        <span class="color-dot variant-color" data-image="{{ $variant->image ? asset('storage/' . $variant->image) : $defaultImage }}" style="background-color: {{ $variant->color }}; width: 12px; height: 12px; border-radius: 50%; margin: 0 3px; display: inline-block; cursor: pointer;"></span>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <span class="heart-icon" style="color: #ff4d4f; font-size: 12px;">❤️</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="rating" style="font-size: 10px;">
                                                ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                            </span>
                                            <span class="ms-2" style="font-size: 12px; color: #777;">({{ $product->variants->sum('sold_quantity') }} đã bán)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Banner quảng cáo thứ ba -->
        <div class="row my-5">
            <div class="col-12">
                <div class="promo-banner animate__animated animate__fadeIn">
                    <img src="https://www.shutterstock.com/image-vector/sports-fitness-products-banner-design-260nw-1919898449.jpg" alt="Promo Banner 3" class="img-fluid rounded">
                    <div class="banner-content">
                        <h3 class="text-white">Flash Sale!</h3>
                        <p class="text-white">Giảm giá cực sốc trong 24 giờ!</p>
                        <a href="#" class="btn btn-danger rounded-pill">Mua ngay</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Giảm giá sốc -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4 animate__animated animate__fadeIn">
                <h1 class="display-6 section-title">Giảm giá sốc <i class="fas fa-tags text-danger ms-2"></i></h1>
            </div>
            <div class="col-12">
                <div class="carousel slide" id="discountProductsCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($products->chunk(4) as $chunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row justify-content-center">
                                    @foreach($chunk as $product)
                                        <div class="col-md-2 col-6 mb-4 animate__animated animate__fadeInUp">
                                            <div class="card h-100 border-0 product-card">
                                                <a href="{{ route('products.show', $product->id) }}">
                                                    @php
                                                        $defaultImage = $product->image ? asset('storage/' . $product->image) : asset('path/to/default-image.jpg');
                                                        $firstVariantImage = $product->variants->whereNotNull('image')->first();
                                                        $defaultImage = $firstVariantImage ? asset('storage/' . $firstVariantImage->image) : $defaultImage;
                                                    @endphp
                                                    <img src="{{ $defaultImage }}" class="card-img-top product-image" data-default-image="{{ $defaultImage }}" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                                                </a>
                                                <div class="card-body">
                                                    <p class="card-title" style="font-size: 14px; font-weight: 500;">{{ Str::limit($product->name, 30) }}</p>
                                                    @php
                                                        $minPrice = $product->variants->min('price') ?? 0;
                                                        $maxPrice = $product->variants->max('price') ?? 0;
                                                    @endphp
                                                    <p class="card-text text-danger text-left">{{ number_format($minPrice, 0, ',', '.') }}đ</p>
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            @foreach($product->variants as $variant)
                                                                @if($variant->color)
                                                                    <span class="color-dot variant-color" data-image="{{ $variant->image ? asset('storage/' . $variant->image) : $defaultImage }}" style="background-color: {{ $variant->color }}; width: 12px; height: 12px; border-radius: 50%; margin: 0 3px; display: inline-block; cursor: pointer;"></span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <span class="heart-icon" style="color: #ff4d4f; font-size: 12px;">❤️</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="rating" style="font-size: 10px;">
                                                            ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                                        </span>
                                                        <span class="ms-2" style="font-size: 12px; color: #777;">({{ $product->variants->sum('sold_quantity') }} đã bán)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#discountProductsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#discountProductsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>

                <div class="product-list">
                    <div class="row justify-content-center">
                        @foreach($products->take(8) as $product)
                            <div class="col-md-2 col-6 mb-4 animate__animated animate__fadeInUp">
                                <div class="card h-100 border-0 product-card">
                                    <a href="{{ route('products.show', $product->id) }}">
                                        @php
                                            $defaultImage = $product->image ? asset('storage/' . $product->image) : asset('path/to/default-image.jpg');
                                            $firstVariantImage = $product->variants->whereNotNull('image')->first();
                                            $defaultImage = $firstVariantImage ? asset('storage/' . $firstVariantImage->image) : $defaultImage;
                                        @endphp
                                        <img src="{{ $defaultImage }}" class="card-img-top product-image" data-default-image="{{ $defaultImage }}" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                                    </a>
                                    <div class="card-body">
                                        <p class="card-title" style="font-size: 14px; font-weight: 500;">{{ Str::limit($product->name, 30) }}</p>
                                        @php
                                            $minPrice = $product->variants->min('price') ?? 0;
                                            $maxPrice = $product->variants->max('price') ?? 0;
                                        @endphp
                                        <p class="card-text text-danger text-left">{{ number_format($minPrice, 0, ',', '.') }}đ</p>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                @foreach($product->variants as $variant)
                                                    @if($variant->color)
                                                        <span class="color-dot variant-color" data-image="{{ $variant->image ? asset('storage/' . $variant->image) : $defaultImage }}" style="background-color: {{ $variant->color }}; width: 12px; height: 12px; border-radius: 50%; margin: 0 3px; display: inline-block; cursor: pointer;"></span>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <span class="heart-icon" style="color: #ff4d4f; font-size: 12px;">❤️</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="rating" style="font-size: 10px;">
                                                ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                            </span>
                                            <span class="ms-2" style="font-size: 12px; color: #777;">({{ $product->variants->sum('sold_quantity') }} đã bán)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sản phẩm bán chạy -->
    <div class="container my-5">
        <div class="row">
            <div class="col-12 text-center mb-4 animate__animated animate__fadeIn">
                <h1 class="display-6 section-title">Sản phẩm bán chạy <i class="fas fa-fire text-danger ms-2"></i></h1>
            </div>
        </div>
        <div class="row justify-content-center">
            @foreach($products as $product)
                <div class="col-md-2 col-6 mb-4 animate__animated animate__fadeInUp">
                    <div class="card h-100 border-0 product-card">
                        <a href="{{ route('products.show', $product->id) }}">
                            @php
                                $defaultImage = $product->image ? asset('storage/' . $product->image) : asset('path/to/default-image.jpg');
                                $firstVariantImage = $product->variants->whereNotNull('image')->first();
                                $defaultImage = $firstVariantImage ? asset('storage/' . $firstVariantImage->image) : $defaultImage;
                            @endphp
                            <img src="{{ $defaultImage }}" class="card-img-top product-image" data-default-image="{{ $defaultImage }}" style="height: 250px; object-fit: cover;" alt="{{ $product->name }}">
                        </a>
                        <div class="card-body">
                            <p class="card-title" style="font-size: 14px; font-weight: 500;">{{ Str::limit($product->name, 30) }}</p>
                            @php
                                $minPrice = $product->variants->min('price') ?? 0;
                                $maxPrice = $product->variants->max('price') ?? 0;
                            @endphp
                            <p class="card-text text-danger text-left">{{ number_format($minPrice, 0, ',', '.') }}đ</p>
                            <div class="d-flex justify-content-between">
                                <div>
                                    @foreach($product->variants as $variant)
                                        @if($variant->color)
                                            <span class="color-dot variant-color" data-image="{{ $variant->image ? asset('storage/' . $variant->image) : $defaultImage }}" style="background-color: {{ $variant->color }}; width: 12px; height: 12px; border-radius: 50%; margin: 0 3px; display: inline-block; cursor: pointer;"></span>
                                        @endif
                                    @endforeach
                                </div>
                                <span class="heart-icon" style="color: #ff4d4f; font-size: 12px;">❤️</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="rating" style="font-size: 10px;">
                                    ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                </span>
                                <span class="ms-2" style="font-size: 12px; color: #777;">({{ $product->variants->sum('sold_quantity') }} đã bán)</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Phần giới thiệu -->
    <section class="about py-5 bg-light">
        <div class="container">
            <div class="row align-items-center animate__animated animate__fadeIn">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h2 class="display-6 section-title">Phong cách thời trang</h2>
                    <p class="text-muted">Xu hướng thời trang năm 2025</p>
                    <p class="text-muted">Thời trang bền vững suốt đời</p>
                    <a href="#" class="btn btn-primary rounded-pill px-4">Xem thêm thời trang</a>
                </div>
                <div class="col-md-6">
                    <img src="../assets/img/hi.jpg" class="img-fluid rounded shadow" alt="Hình ảnh thời trang">
                </div>
            </div>
        </div>
    </section>

    @include('Users.chat')
@endsection

<style>
    /* Cải thiện màu sắc và hiệu ứng cho tiêu đề */
    .section-title {
        background: linear-gradient(90deg, #ff4d4f, #ff7878);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
        position: relative;
    }
    .section-title::after {
        content: '';
        position: absolute;
        width: 50%;
        height: 3px;
        background: linear-gradient(90deg, #ff4d4f, #ff7878);
        bottom: -5px;
        left: 25%;
    }

    /* Cải thiện giao diện sản phẩm */
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    .card-img-top {
        transition: transform 0.3s ease;
    }
    .card-img-top:hover {
        transform: scale(1.05);
    }
    .color-dot {
        border: 1px solid #ddd;
        transition: transform 0.2s ease;
    }
    .color-dot:hover {
        transform: scale(1.2);
    }
    .heart-icon:hover {
        cursor: pointer;
        color: #ff7878;
        transform: scale(1.2);
        transition: transform 0.2s ease;
    }

    /* Giao diện banner quảng cáo */
    .promo-banner {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
    }
    .promo-banner img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        filter: brightness(70%);
        transition: transform 0.5s ease;
    }
    .promo-banner:hover img {
        transform: scale(1.05);
    }
    .banner-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }
    .banner-content h3 {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    .banner-content p {
        font-size: 1.2rem;
        margin-bottom: 20px;
    }
    .banner-content .btn {
        padding: 10px 20px;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }
    .banner-content .btn:hover {
        background-color: #ff7878;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.card');

        cards.forEach(card => {
            const image = card.querySelector('.product-image');
            const colorDots = card.querySelectorAll('.variant-color');

            colorDots.forEach(dot => {
                dot.addEventListener('mouseover', function () {
                    const newImage = this.getAttribute('data-image');
                    image.setAttribute('src', newImage);
                });

                dot.addEventListener('mouseout', function () {
                    const defaultImage = image.getAttribute('data-default-image');
                    image.setAttribute('src', defaultImage);
                });
            });
        });
    });
</script>
