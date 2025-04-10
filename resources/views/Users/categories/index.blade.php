@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <form method="GET" action="{{ route('categories.show') }}">
                <h4 class="mb-3">{{ __('messages.filter') }}</h4>
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="{{ __('messages.search_by_name') }}" value="{{ request('name') }}">
                </div>
                <div class="mb-3">
                    <select name="category" class="form-control">
                        <option value="">{{ __('messages.select_category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @foreach($category->children as $child)
                                <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                    └ {{ $child->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

             
                <div class="mb-3">
                    <select name="gender" class="form-control">
                        <option value="">{{ __('messages.select_gender') }}</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                        <option value="unisex" {{ request('gender') == 'unisex' ? 'selected' : '' }}>{{ __('messages.unisex') }}</option>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="number" name="min_price" class="form-control mb-2" placeholder="Min Price" value="{{ request('min_price') }}">
                    <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="{{ request('max_price') }}">
                </div>

                <button type="submit" class="btn btn-primary w-100">{{ __('messages.filter') }}</button>

                @if(request()->hasAny(['name', 'category', 'gender', 'min_price', 'max_price']))
                    <a href="{{ route('categories.show') }}" class="btn btn-secondary w-100 mt-2">{{ __('Đặt lại bộ lọc') ?? 'Reset Filter' }}</a>
                @endif
            </form>
           
        </div>

        <div class="col-md-9">
            <h2 class="mb-3">{{ __('messages.products') }}</h2>

            @if($products->isEmpty())
                <div class="alert alert-warning text-center mt-3">
                    {{ __('messages.no_products_found') }}
                </div>
            @else
            <div class="row">
                    @foreach($products as $product)
                    @if($product->is_delete=="1")
                        <div class="col-md-4 mb-4">
                            <div class="card product-card shadow-sm">
                                {{-- <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('default-image.jpg') }}" 
                                class="card-img-top product-img" alt="{{ $product->name }}"> --}}
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="text-muted">{{ __('messages.gender') }}: {{ ucfirst($product->gender) }}</p>
                                    
                                    @php
                                        $minPrice = $product->variants->min('price') ?? 0;
                                        $maxPrice = $product->variants->max('price') ?? 0;
                                    @endphp
                    
                                    <p class="text-danger fw-bold">
                                        {{ number_format($minPrice, 0, ',', '.') }} VND - {{ number_format($maxPrice, 0, ',', '.') }} VND
                                    </p>
                    
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-success">{{ __('messages.view_details') }}</a>
                                </div>
                            </div>
                        </div>
                    @else
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
                                    {{ number_format($minPrice, 0, ',', '.') }} VND - {{ number_format($maxPrice, 0, ',', '.') }} VND
                                </p>
                
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-success">{{ __('messages.view_details') }}</a>
                            </div>
                        </div>
                        </div>
                    @endif
                    @endforeach
                </div>
                <!-- <div class="row">
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
                                        {{ number_format($minPrice, 0, ',', '.') }} VND - {{ number_format($maxPrice, 0, ',', '.') }} VND
                                    </p>
                    
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-success">{{ __('messages.view_details') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div> -->

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
