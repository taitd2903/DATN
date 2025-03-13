@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
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
            <h2 class="mb-3">Products</h2>
            <div class="row">
                @foreach($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card product-card shadow-sm">
                          <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('default-image.jpg') }}" 
                          class="card-img-top product-img" alt="{{ $product->name }}">
                                                 <div class="card-body text-center">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="text-muted">Giới tính: {{ ucfirst($product->gender) }}</p>
                                <p class="text-danger fw-bold">
                                    {{ number_format($product->variants->first()->price ?? 0, 0, ',', '.') }} VND
                                </p>
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-success">Xem chi tiết</a>

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
@endsection
