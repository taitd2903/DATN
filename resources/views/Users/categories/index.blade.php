@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        
         <!-- Bộ lọc sản phẩm -->
         <form method="GET" action="{{ route('categories.show') }}" class="mb-4">
    <div class="row g-3 align-items-center">
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="{{ __('messages.search_by_name') }}" value="{{ request('name') }}">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-control">
                <option value="">{{ __('messages.select_category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="gender" class="form-control">
                <option value="">{{ __('messages.select_gender') }}</option>
                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                <option value="unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>{{ __('messages.unisex') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">{{ __('messages.filter') }}</button>
        </div>
    </div>
</form>

@if($products->isEmpty())
    <div class="alert alert-warning text-center mt-3">
    {{ __('messages.no_products_found') }}
    </div>
@else
@endif

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
        <!-- Sidebar Danh Mục -->
        <div class="col-md-3">
            <h4 class="mb-3">Categories</h4>
            <ul class="list-group">
                @foreach($categories as $category)
                    <li class="list-group-item">
                        <strong>{{ $category->name }}</strong>
                        @if($category->children->count() > 0)
                            <ul class="list-unstyled ms-3">
                                @foreach($category->children as $subCategory)
                                    <li>
                                        <a href="{{ route('categories.show', ['category_id' => $subCategory->id]) }}" 
                                           class="text-decoration-none text-dark">
                                            {{ $subCategory->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>

            <!-- Bộ lọc giá -->
            <h4 class="mt-4">Price</h4>
            <form method="GET" action="{{ route('categories.show') }}">
                <input type="number" name="min_price" class="form-control mb-2" placeholder="Min Price" value="{{ request('min_price') }}">
                <input type="number" name="max_price" class="form-control mb-2" placeholder="Max Price" value="{{ request('max_price') }}">
                <button type="submit" class="btn btn-danger w-100">Filter</button>
            </form>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="col-md-9">
            <h2 class="mb-3">{{ __('messages.products') }}</h2>
            <div class="row">
                @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card product-card shadow-sm">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('default-image.jpg') }}" 
                        class="card-img-top product-img" alt="{{ $product->name }}">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="text-muted">{{ __('messages.gender') }}: {{ ucfirst($product->gender) }}</p>
                            
                            @php
                                $minPrice = $product->variants->min('price') ?? 0;
                                $maxPrice = $product->variants->max('price') ?? 0;
                            @endphp
            
                            <p class="text-danger fw-bold">
                                {{ number_format($minPrice, 0, ',', '.') }}VND - {{ number_format($maxPrice, 0, ',', '.') }} VND
                            </p>
            
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-success">{{ __('messages.view_details') }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
            
            </div>

            <!-- Phân trang -->
            <div class="pagination justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Thêm CSS tùy chỉnh -->
<style>
    .product-card {
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease-in-out;
    }
    .product-card:hover {
        transform: scale(1.05);
    }
    .product-img {
        height: 250px;
        object-fit: cover;
    }
</style>
@include('Users.chat')
@endsection
