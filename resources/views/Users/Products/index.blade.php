@extends('layouts.app')
@section('content')
    <div id="slides-shop" class="cover-slides">
        <ul class="slides-container">
            @foreach ($banners as $key => $banner)
                @if ($banner->is_active)
                    <li class="{{ $key == 0 ? 'text-left' : ($key == 1 ? 'text-center' : 'text-right') }}">
                        <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ __('messages.banner') }} {{ $key + 1 }}">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <h1 class="m-b-20"><strong>{{ $banner->title }}</strong></h1>
                                    <p class="m-b-40">{{ $banner->description }}</p>
                                    @if ($banner->link)
                                        <p><a class="btn hvr-hover" href="{{ $banner->link }}">{{ __('messages.shop_now') }}</a></p>
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
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h1 class="display-6">{{ __('messages.new_products') }}</h1>
            </div>
            <div class="col-12">
                <div class="carousel slide" id="newProductsCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($products->chunk(4) as $chunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row justify-content-center">
                                    @foreach($chunk as $product)
                                        <div class="col-md-3 col-6 mb-4">
                                            <div class="card h-100">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 200px; object-fit: contain;" alt="{{ $product->name }}">
                                                @else
                                                    <div class="p-3 text-center text-muted">Chưa có ảnh</div>
                                                @endif
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">{{ Str::limit($product->name, 20) }}</h5>
                                                    @php
                                                        $minPrice = $product->variants->min('price') ?? 0;
                                                        $maxPrice = $product->variants->max('price') ?? 0;
                                                    @endphp
                                                    <p class="card-text text-danger fw-bold">{{ number_format($minPrice, 0, ',', '.') }} VND</p>
                                                    <span class="rating">
                                                ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                            </span>
                                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary w-100 btn-sm">{{ __('messages.view_details') }}</a>
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
                            <div class="col-6 mb-4">
                                <div class="card h-100">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="{{ $product->name }}">
                                    @else
                                        <div class="p-3 text-center text-muted">Chưa có ảnh</div>
                                    @endif
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ Str::limit($product->name, 20) }}</h5>
                                        @php
                                            $minPrice = $product->variants->min('price') ?? 0;
                                            $maxPrice = $product->variants->max('price') ?? 0;
                                        @endphp
                                        <p class="card-text text-danger fw-bold">{{ number_format($minPrice, 0, ',', '.') }} VND</p>
                                        <span class="rating">
                                            ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                        </span>
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary w-100 btn-sm">{{ __('messages.view_details') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h1 class="display-6">Sản phẩm nổi bật</h1>
            </div>
            <div class="col-12">
                <div class="carousel slide" id="featuredProductsCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($products->chunk(4) as $chunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row justify-content-center">
                                    @foreach($chunk as $product)
                                        <div class="col-md-3 col-6 mb-4">
                                            <div class="card h-100">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 200px; object-fit: contain;" alt="{{ $product->name }}">
                                                @else
                                                    <div class="p-3 text-center text-muted">Chưa có ảnh</div>
                                                @endif
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">{{ Str::limit($product->name, 20) }}</h5>
                                                    @php
                                                        $minPrice = $product->variants->min('price') ?? 0;
                                                        $maxPrice = $product->variants->max('price') ?? 0;
                                                    @endphp
                                                    <p class="card-text text-danger fw-bold">{{ number_format($minPrice, 0, ',', '.') }} VND</p>
                                                    <span class="rating">
                                                        ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                                    </span>
                                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary w-100 btn-sm">{{ __('messages.view_details') }}</a>
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
                            <div class="col-6 mb-4">
                                <div class="card h-100">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="{{ $product->name }}">
                                    @else
                                        <div class="p-3 text-center text-muted">Chưa có ảnh</div>
                                    @endif
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ Str::limit($product->name, 20) }}</h5>
                                        @php
                                            $minPrice = $product->variants->min('price') ?? 0;
                                            $maxPrice = $product->variants->max('price') ?? 0;
                                        @endphp
                                        <p class="card-text text-danger fw-bold">{{ number_format($minPrice, 0, ',', '.') }} VND</p>
                                        <span class="rating">
                                            ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                        </span>
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary w-100 btn-sm">{{ __('messages.view_details') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h1 class="display-6">Sản phẩm nhiều người chú ý</h1>
            </div>
            <div class="col-12">
                <div class="carousel slide" id="notableProductsCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($products->chunk(4) as $chunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row justify-content-center">
                                    @foreach($chunk as $product)
                                        <div class="col-md-3 col-6 mb-4">
                                            <div class="card h-100">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 200px; object-fit: contain;" alt="{{ $product->name }}">
                                                @else
                                                    <div class="p-3 text-center text-muted">Chưa có ảnh</div>
                                                @endif
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">{{ Str::limit($product->name, 20) }}</h5>
                                                    @php
                                                        $minPrice = $product->variants->min('price') ?? 0;
                                                        $maxPrice = $product->variants->max('price') ?? 0;
                                                    @endphp
                                                    <p class="card-text text-danger fw-bold">{{ number_format($minPrice, 0, ',', '.') }} VND</p>
                                                    <span class="rating">
                                                        ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                                    </span>
                                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary w-100 btn-sm">{{ __('messages.view_details') }}</a>
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
                            <div class="col-6 mb-4">
                                <div class="card h-100">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="{{ $product->name }}">
                                    @else
                                        <div class="p-3 text-center text-muted">Chưa có ảnh</div>
                                    @endif
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ Str::limit($product->name, 20) }}</h5>
                                        @php
                                            $minPrice = $product->variants->min('price') ?? 0;
                                            $maxPrice = $product->variants->max('price') ?? 0;
                                        @endphp
                                        <p class="card-text text-danger fw-bold">{{ number_format($minPrice, 0, ',', '.') }} VND</p>
                                        <span class="rating">
                                            ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                        </span>
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary w-100 btn-sm">{{ __('messages.view_details') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h1 class="display-6">Giảm giá sốc</h1>
            </div>
            <div class="col-12">
                <div class="carousel slide" id="discountProductsCarousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($products->chunk(4) as $chunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row justify-content-center">
                                    @foreach($chunk as $product)
                                        <div class="col-md-3 col-6 mb-4">
                                            <div class="card h-100">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 200px; object-fit: contain;" alt="{{ $product->name }}">
                                                @else
                                                    <div class="p-3 text-center text-muted">Chưa có ảnh</div>
                                                @endif
                                                <div class="card-body text-center">
                                                    <h5 class="card-title">{{ Str::limit($product->name, 20) }}</h5>
                                                    @php
                                                        $minPrice = $product->variants->min('price') ?? 0;
                                                        $maxPrice = $product->variants->max('price') ?? 0;
                                                    @endphp
                                                    <p class="card-text text-danger fw-bold">{{ number_format($minPrice, 0, ',', '.') }} VND</p>
                                                    <span class="rating">
                                                        ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                                    </span>
                                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary w-100 btn-sm">{{ __('messages.view_details') }}</a>
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
                            <div class="col-6 mb-4">
                                <div class="card h-100">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="{{ $product->name }}">
                                    @else
                                        <div class="p-3 text-center text-muted">Chưa có ảnh</div>
                                    @endif
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ Str::limit($product->name, 20) }}</h5>
                                        @php
                                            $minPrice = $product->variants->min('price') ?? 0;
                                            $maxPrice = $product->variants->max('price') ?? 0;
                                        @endphp
                                        <p class="card-text text-danger fw-bold">{{ number_format($minPrice, 0, ',', '.') }} VND</p>
                                        <span class="rating">
                                                ⭐⭐⭐⭐⭐ <span class="reviews">(0)</span>
                                            </span>
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary w-100 btn-sm">{{ __('messages.view_details') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h1 class="display-6">{{ __('messages.best_selling_products') }}</h1>
                <p class="text-muted">{{ __('messages.top_selling_products_by_category') }}</p>
            </div>
        </div>
        <div class="row justify-content-center">
            @foreach($representativeProductsByParentCategory as $parentCategoryId => $representativeProduct)
                @if($representativeProduct)
                    <div class="col-md-4 col-6 mb-4">
                        <div class="card">
                            <img src="{{ asset('storage/' . $representativeProduct->image) }}" class="card-img-top" style="height: 200px; object-fit: contain;" alt="{{ $representativeProduct->name }}">
                            <div class="card-body text-center">
                                <h3 class="card-title">{{ Str::limit($representativeProduct->name, 25) }}</h3>
                                <p class="card-text">
                                    <span class="text-danger fw-bold">{{ number_format($representativeProduct->min_price, 0, ',', '.') }} - {{ number_format($representativeProduct->max_price, 0, ',', '.') }} VNĐ</span><br>
                                    <small class="text-muted">{{ __('messages.avg_price') }}: {{ number_format($representativeProduct->avg_price, 0, ',', '.') }} VNĐ</small><br>
                                    <small class="text-muted">{{ __('messages.sold') }}: {{ $representativeProduct->variants->sum('sold_quantity') }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <section class="about py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h2 class="display-6">{{ __('messages.fashion_style') }}</h2>
                    <p class="text-muted">{{ __('messages.fashion_trend_2025') }}</p>
                    <p class="text-muted">{{ __('messages.fashion_lifetime') }}</p>
                    <a href="#" class="btn btn-primary rounded-pill px-4">{{ __('messages.view_more_fashion') }}</a>
                </div>
                <div class="col-md-6">
                    <img src="../assets/img/hi.jpg" class="img-fluid rounded shadow" alt="{{ __('messages.fashion_image') }}">
                </div>
            </div>
        </div>
    </section>

    @include('Users.chat')
@endsection
