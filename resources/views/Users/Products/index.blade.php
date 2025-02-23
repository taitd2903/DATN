@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Danh sách sản phẩm</h1>

    <!-- Bộ lọc sản phẩm -->
    <form method="GET" action="{{ route('products.index') }}" class="mb-4">
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
    </form>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($products as $product)
            <div class="col d-flex">
                <div class="card h-100 shadow-sm border-0 rounded-lg w-100">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top product-img" alt="{{ $product->name }}">
                    @else
                        <div class="p-3 text-center text-muted">Chưa có hình ảnh</div>
                    @endif

                    <div class="card-body text-center">
                        <h5 class="card-title text-primary">{{ $product->name }}</h5>
                        <p class="card-text text-muted small">{{ Str::limit($product->description, 100) }}</p>
                        <p class="fw-bold">Danh mục: <span class="text-dark">{{ $product->category ? $product->category->name : 'Chưa có danh mục' }}</span></p>
                        <p class="text-success fw-bold">Tổng số lượng đã bán: {{ $product->total_sold_quantity }}</p>
                        <p class="fw-bold">Giới tính: <span class="text-dark">{{ ucfirst($product->gender) }}</span></p>

                        <h6 class="mt-3">Biến thể:</h6>
                        <ul class="list-unstyled d-flex justify-content-center gap-2 flex-wrap">
                            @foreach($product->variants as $variant)
                                <li>
                                    @if(!empty($variant->size))
                                        <span class="badge bg-primary">Size: {{ $variant->size }}</span>
                                    @endif
                                    <span class="badge bg-secondary">Màu: {{ $variant->color }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary mt-3 w-75">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

<style>
    .product-img {
        height: 200px;
        object-fit: cover;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    .card {
        transition: transform 0.3s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    .badge {
        font-size: 0.9rem;
        padding: 5px 10px;
    }
    .container {
        max-width: 100%;
    }
    .row {
        display: flex;
        flex-wrap: wrap;
    }
    .col {
        display: flex;
    }
</style>
