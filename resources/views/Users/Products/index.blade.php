
<!-- SP -->
@extends('layouts.app')
@section('content')

    <!-- Bộ lọc sản phẩm -->
    <!-- <form method="GET" action="{{ route('products.index') }}" class="mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Tìm kiếm theo tên" value="{{ request('name') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-control">
                    <option value="">Chọn danh mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="gender" class="form-control">
                    <option value="">Chọn giới tính</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                    <option value="unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Lọc</button>
            </div>
        </div>
    </form> -->

 <!-- Start Slider -->
 <!-- <div id="slides-shop" class="cover-slides">
        <ul class="slides-container">
            <li class="text-left">
                <img src="../assets/img/banner-01.jpg" alt="">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h1 class="m-b-20"><strong>Welcome To <br> Thewayshop</strong></h1>
                            <p class="m-b-40">See how your users experience your website in realtime or view <br> trends to see any changes in performance over time.</p>
                            <p><a class="btn hvr-hover" href="#">Shop New</a></p>
                        </div>
                    </div>
                </div>
            </li>
            <li class="text-center">
                <img src="../assets/img/banner-02.jpg" alt="">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h1 class="m-b-20"><strong>Welcome To <br> Thewayshop</strong></h1>
                            <p class="m-b-40">See how your users experience your website in realtime or view <br> trends to see any changes in performance over time.</p>
                            <p><a class="btn hvr-hover" href="#">Shop New</a></p>
                        </div>
                    </div>
                </div>
            </li>
            <li class="text-right">
                <img src="../assets/img/banner-03.jpg" alt="">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h1 class="m-b-20"><strong>Welcome To <br> Thewayshop</strong></h1>
                            <p class="m-b-40">See how your users experience your website in realtime or view <br> trends to see any changes in performance over time.</p>
                            <p><a class="btn hvr-hover" href="#">Shop New</a></p>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        
    </div> -->
    <!-- End Slider -->
    <div id="slides-shop" class="cover-slides">
        <ul class="slides-container">
            @foreach ($banners as $key => $banner)
                @if ($banner->is_active)
                    <li class="{{ $key == 0 ? 'text-left' : ($key == 1 ? 'text-center' : 'text-right') }}">
                        <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ __('messages.banner') }} {{ $key + 1 }}">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
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
    <!-- @foreach ($banners as $banner)
    @if ($banner->is_active)
        <img src="{{ asset('storage/' . $banner->image) }}" width="120" height="60" style="object-fit: cover;">
    @endif
@endforeach -->

    <!-- SAN PHAM -->
    <div class="products-box">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="title-all text-center">
                        <h1>{{ __('messages.our_products') }}</h1>
                    </div>
                </div>
            </div>
            <div class="row special-list">
                @foreach($products as $product)
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 special-grid best-seller" style="margin-right: 30px">
                        <div class="products-single fix">
                            <div class="box-img-hover">
                                <div class="type-lb">
                                    {{-- <p class="sale">{{ __('messages.sale') }}</p> --}}
                                </div>
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" alt="{{ $product->name }}">
                                @else
                                    <div class="p-3 text-center text-muted">{{ __('messages.no_image') }}</div>
                                @endif
                                                       <!-- Modal hiển thị ảnh -->
                                                       <div class="modal fade" id="productImageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.product_image') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalBtn">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalProductImage" src="" class="img-fluid" alt="Product Image">
            </div>
        </div>
    </div>
</div>
                                <div class="mask-icon">
                                    <ul>
                                        <li>
                                            <a href="#" class="view-product" data-image="{{ asset('storage/' . $product->image) }}" data-toggle="tooltip" data-placement="right" title="{{ __('messages.view_product') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </li>
                                        <li><a href="#" data-toggle="tooltip" data-placement="right" title="{{ __('messages.add_to_favorite') }}"><i class="far fa-heart"></i></a></li>
                                    </ul>
                                    <a href="{{ route('products.show', $product->id) }}" class="cart">{{ __('messages.view_details') }}</a>
                                </div>
                            </div>
                            <div class="why-text">
                                <h4>{{ $product->name }}</h4>
                                @php
                                    $minPrice = $product->variants->min('price') ?? 0;
                                    $maxPrice = $product->variants->max('price') ?? 0;
                                @endphp
                                <h5 style="font-size: 12px;">
                                    {{ number_format($minPrice, 0, ',', '.') }} VND - {{ number_format($maxPrice, 0, ',', '.') }} VND
                                </h5>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


<!-- BST -->
 <div class="latest-blog">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="title-all text-center">
                        <h1>{{ __('messages.best_selling_products') }}</h1>
                        <p>{{ __('messages.top_selling_products_by_category') }}</p>
                    </div>
                </div>
            </div>

            @foreach($representativeProductsByParentCategory as $parentCategoryId => $representativeProduct)
                @if($representativeProduct)
                    <div class="category-section">
                        <h2 class="text-center">{{ $categories->find($parentCategoryId)->name }}</h2>
                        <div class="row">
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <div class="blog-box">
                                    <div class="blog-img">
                                        <img src="{{ asset('storage/' . $representativeProduct->image) }}" class="img-fluid" alt="{{ $representativeProduct->name }}">
                                    </div>
                                    <div class="blog-content">
                                        <div class="title-blog">
                                            <h3>{{ $representativeProduct->name }}</h3>
                                            <p>{{ __('messages.price') }}: {{ number_format($representativeProduct->min_price, 0, ',', '.') }} - {{ number_format($representativeProduct->max_price, 0, ',', '.') }} VNĐ</p>
                                            <p>{{ __('messages.avg_price') }}: {{ number_format($representativeProduct->avg_price, 0, ',', '.') }} VNĐ</p>
                                            <p>{{ __('messages.sold') }}: {{ $representativeProduct->variants->sum('sold_quantity') }} {{ __('messages.products') }}</p>
                                        </div>
                                        <ul class="option-blog">
                                            <li><a href="#" data-toggle="tooltip" data-placement="right" title="{{ __('messages.likes') }}"><i class="far fa-heart"></i></a></li>
                                            <li><a href="#" data-toggle="tooltip" data-placement="right" title="{{ __('messages.views') }}"><i class="fas fa-eye"></i></a></li>
                                            <li><a href="#" data-toggle="tooltip" data-placement="right" title="{{ __('messages.comments') }}"><i class="far fa-comments"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>


    <!-- End Blog  -->

     <!-- about section start -->
     <section class="about">
        <div class="about-container">
            <div class="about-text">
                <h2>{{ __('messages.fashion_style') }}</h2>
                <p>{{ __('messages.fashion_trend_2025') }}</p>
                <p>{{ __('messages.fashion_lifetime') }}</p>
                <a href="#" class="btn">{{ __('messages.view_more_fashion') }}</a>
            </div>
            <div class="about-image">
                <img src="../assets/img/hi.jpg" alt="{{ __('messages.fashion_image') }}">
            </div>
        </div>
    </section>


 <!-- about section end -->
 @include('Users.chat')
 @endsection