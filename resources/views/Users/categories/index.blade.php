@extends('layouts.app')

@section('content')
    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h4>Danh mục</h4>
                        <div class="breadcrumb__links">
                            <a href="{{ url('/') }}">Trang chủ</a>
                            <span>Danh mục</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <form method="GET" action="{{ route('categories.show') }}">
                    <br>
                    <h4 class="mb-3"></h4>
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Tìm kiếm"
                            value="{{ request('name') }}">
                    </div>

                    <div class="mb-3">
                        <select name="category" class="form-control">
                            <option value="">{{ __('messages.select_category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @foreach ($category->children as $child)
                                    <option value="{{ $child->id }}"
                                        {{ request('category') == $child->id ? 'selected' : '' }}>
                                        └ {{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>


                    <div class="mb-3">
                        <select name="gender" class="form-control">
                            <option value="">{{ __('messages.select_gender') }}</option>
                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>
                                {{ __('messages.male') }}</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>
                                {{ __('messages.female') }}</option>
                            <option value="unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>
                                {{ __('messages.unisex') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <!-- <h3>tìm kiếm theo khoảng giá</h3> -->
                        <input type="number" name="min_price" class="form-control mb-2" placeholder="Giá thấp"
                            value="{{ request('min_price') }}">
                        <input type="number" name="max_price" class="form-control" placeholder="Giá cao"
                            value="{{ request('max_price') }}">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>

                    @if (request()->hasAny(['name', 'category', 'gender', 'min_price', 'max_price']))
                        <a href="{{ route('categories.show') }}"
                            class="btn btn-secondary w-100 mt-2">{{ __('Đặt lại bộ lọc') ?? 'Reset Filter' }}</a>
                    @endif
                </form>

            </div>

            <div class="col-md-9">
                <h2 class="mb-3">Sản phẩm</h2>

                @if ($products->isEmpty())
                    <div class="alert alert-warning text-center mt-3">
                        {{ __('messages.no_products_found') }}
                    </div>
                @else
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                        @foreach ($products as $product)
                            @if ($product->is_delete == '1')
                                <div class="col-lg-3 col-md-6 col-sm-6 mix new-arrivals">
                                    <div class="product__item">
                                        <div class="product__item__pic set-bg position-relative">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    class="card-img-top product-img" alt="{{ $product->name }}"
                                                    style="width: 100%; aspect-ratio: 1 / 1; object-fit: cover;">
                                            @else
                                                <div class="p-3 text-center text-muted">Chưa có hình ảnh</div>
                                            @endif

                                            <!-- Overlay mờ thông báo hết hàng -->
                                            <div class="overlay-out-of-stock">
                                                <span>Sản phẩm tạm dừng bán hàng</span>
                                            </div>
                                        </div>

                                        <div class="product__item__text">
                                            <h6>{{ $product->name }}</h6>
                                            <a href="{{ route('products.show', $product->id) }}" class="add-cart"
                                                style="text-decoration: none">Xem chi tiết</a>

                                            @php
                                                $minPrice = $product->variants->min('price') ?? 0;
                                                $maxPrice = $product->variants->max('price') ?? 0;
                                            @endphp
                                            <h5>{{ number_format($minPrice, 0, ',', '.') }} VND -
                                                {{ number_format($maxPrice, 0, ',', '.') }} VND</h5>

                                            <div id="color-options" class="product__color__select">
                                                @foreach ($product->variants->unique('color') as $variant)
                                                    <button type="button" class="color-btn"
                                                        data-color="{{ $variant->color }}"
                                                        style="background-color: {{ $variant->color }}; width: 20px; height: 20px; border: 1px solid #ccc; border-radius: 50%; display: inline-block;">
                                                    </button>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-lg-3 col-md-6 col-sm-6 mix new-arrivals">
                                    <div class="product__item">
                                        <div class="product__item__pic set-bg">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    class="card-img-top product-img" alt="{{ $product->name }}"
                                                    style="width: 100%; aspect-ratio: 1 / 1; object-fit: cover;">
                                            @else
                                                <div class="p-3 text-center text-muted">Chưa có hình ảnh</div>
                                            @endif

                                        </div>

                                        <div class="product__item__text">
                                        <h6>{{ Str::limit($product->name, 30) }}</h6>

                                            <a href="{{ route('products.show', $product->id) }}" class="add-cart"
                                                style="text-decoration: none">Xem chi tiết</a>
                                            @php
                                                $minPrice = $product->variants->min('price') ?? 0;
                                                $maxPrice = $product->variants->max('price') ?? 0;
                                            @endphp

                                            <h5>{{ number_format($minPrice, 0, ',', '.') }} VND -
                                                {{ number_format($maxPrice, 0, ',', '.') }} VND</h5>

                                            <div id="color-options" class="product__color__select">
                                                @foreach ($product->variants->unique('color') as $variant)
                                                    <button type="button" class="color-btn"
                                                        data-color="{{ $variant->color }}"
                                                        style="background-color: {{ $variant->color }}; width: 20px; height: 20px; border: 1px solid #ccc; border-radius: 50%; display: inline-block;">
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="pagination justify-content-center">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

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
