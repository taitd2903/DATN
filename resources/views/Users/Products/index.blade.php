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

    <br>
    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="filter__controls">
                        <li class="active" data-filter="*">Sản phẩm hot</li>

                        <li data-filter=".hot-sales">Bán chạy</li>
                    </ul>
                </div>
            </div>

            <div class="row product__filter">

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                    @foreach ($products as $product)
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

                                    <span class="label">New</span>

                                    <ul class="product__hover">
                                        <li><a href="#"><i class="bi bi-eye"></i></a></li>
                                        <li><a href="#"><i class="bi bi-eye-fill"></i></a></li>
                                        <li><a href="#"><i class="bi bi-eye-slash"></i></a></li>
                                    </ul>
                                </div>


                                <div class="product__item__text">
                                    <h6>{{ $product->name }}</h6>
                                    <a href="{{ route('products.show', $product->id) }}" class="add-cart"
                                        style="text-decoration: none">Xem chi tiết</a>


                                    <div class="rating">
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                    </div>

                                    @php
                                        $firstVariant = $product->variants->first();
                                        $price = $firstVariant ? $firstVariant->price : '0.00';
                                    @endphp

                                    <h5>${{ number_format($price, 2) }}</h5>

                                    <div id="color-options" class="product__color__select">
                                        @foreach ($product->variants->unique('color') as $variant)
                                            <button type="button" class="color-btn" data-color="{{ $variant->color }}"
                                                style="background-color: {{ $variant->color }}; width: 20px; height: 20px; border: 1px solid #ccc; border-radius: 50%; display: inline-block;">
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>


            </div>

            <h2 class="text-center">Sản phẩm bán chạy</h2>
            <div class="row product__filter">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                    @foreach ($topSellingProducts as $product)
                        <div class="col-lg-3 col-md-6 col-sm-6 mix new-arrivals">
                            <div class="product__item">
                                <!-- Product Image -->
                                <div class="product__item__pic set-bg">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            class="card-img-top product-img" alt="{{ $product->name }}"
                                            style="width: 100%; aspect-ratio: 1 / 1; object-fit: cover;">
                                    @else
                                        <div class="p-3 text-center text-muted">Chưa có hình ảnh</div>
                                    @endif
                                    <span class="label">New</span>
                                    <ul class="product__hover">
                                        <li><a href="#"><i class="bi bi-eye"></i></a></li>
                                        <li><a href="#"><i class="bi bi-eye-fill"></i></a></li>
                                        <li><a href="#"><i class="bi bi-eye-slash"></i></a></li>
                                    </ul>
                                </div>

                                <!-- Product Details -->
                                <div class="product__item__text">
                                    <h6>{{ $product->name }}</h6>
                                    <a href="{{ route('products.show', $product->id) }}" class="add-cart"
                                        style="text-decoration: none">Xem chi tiết</a>
                                    <p>Đã bán: {{ $product->total_sold_quantity }}</p>

                                    <!-- Rating (Placeholder) -->
                                    <div class="rating">
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                        <i class="fa fa-star-o"></i>
                                    </div>

                                    <!-- Price Range -->
                                    <h5>{{ number_format($product->min_price) }} -
                                        {{ number_format($product->max_price) }} VNĐ</h5>

                                    <!-- Color Variants -->
                                    @if ($product->variants && $product->variants->count() > 0)
                                        <div id="color-options" class="product__color__select">
                                            @foreach ($product->variants->unique('color') as $variant)
                                                <button type="button" class="color-btn" data-color="{{ $variant->color }}"
                                                    style="background-color: {{ $variant->color }}; width: 20px; height: 20px; border: 1px solid #ccc; border-radius: 50%; display: inline-block;">
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
    </section>

    <!-- Phần giới thiệu -->
    <section class="about py-5 bg-light">
        <div class="container">
            <div class="row align-items-center animate__animated animate__fadeIn">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h2 class="display-6 section-title">Phong cách thời trang</h2>
                    <p class="text-muted">Xu hướng thời trang năm 2025 đang chứng kiến sự trỗi dậy mạnh mẽ của phong cách
                        tối giản, cá tính và hướng đến sự linh hoạt trong từng chuyển động. Những bộ outfit không chỉ đẹp
                        mắt mà còn giúp người mặc cảm thấy thoải mái, tự tin cả khi vận động hay trong đời sống thường ngày.
                    </p>
                    <p class="text-muted">Thời trang bền vững không còn là xu hướng nhất thời – đó là tuyên ngôn sống. Chúng
                        tôi cam kết mang đến những sản phẩm thân thiện với môi trường, sử dụng chất liệu tái chế và quy
                        trình sản xuất tiết kiệm năng lượng. Hãy cùng chúng tôi xây dựng một phong cách sống xanh và hiện
                        đại, nơi cái đẹp đồng hành cùng trách nhiệm.</p>

                    <a href={{ url('categories') }} class="btn btn-primary rounded-pill px-4">Xem thêm thời trang</a>
                </div>
                <div class="col-md-6">
                    <img src="../assets/img/hi.jpg" class="img-fluid rounded shadow" alt="Hình ảnh thời trang">
                </div>
            </div>
        </div>
    </section>

    <!-- tin tuc -->
    <section class="latest spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>BÀI VIẾT MỚI</span>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach ($articles as $article)
                    <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                        <div class="blog__item">
                            <div class="blog__item__pic set-bg"
                                style="background-image: url('{{ asset('storage/' . $article->image) }}');
                                   ">
                            </div>
                            <div class="blog__item__text">
                                <span><i class="bi bi-calendar3"></i> {{ $article->created_at->format('d M Y') }}</span>
                                <h5>{{ $article->name }}</h5>
                                <p>{{ \Illuminate\Support\Str::limit($article->description, 100) }}</p>
                                <a href="{{ route('articles.showUser', $article->id) }}"
                                    style="text-decoration: none">Xem
                                    thêm</a>
                            </div>
                        </div>
                    </div>
                @endforeach
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
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');

        cards.forEach(card => {
            const image = card.querySelector('.product-image');
            const colorDots = card.querySelectorAll('.variant-color');

            colorDots.forEach(dot => {
                dot.addEventListener('mouseover', function() {
                    const newImage = this.getAttribute('data-image');
                    image.setAttribute('src', newImage);
                });

                dot.addEventListener('mouseout', function() {
                    const defaultImage = image.getAttribute('data-default-image');
                    image.setAttribute('src', defaultImage);
                });
            });
        });
    });
</script>
