@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý thùng rác</h1>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="statsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="category-tab" data-toggle="tab" href="#category" role="tab">Danh mục</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="product-tab" data-toggle="tab" href="#product" role="tab">Sản phẩm</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="coupon-tab" data-toggle="tab" href="#coupon" role="tab">Mã giảm giá</a>
        </li>
    </ul>

    <div class="tab-content mt-3" id="statsTabsContent">
        <!-- Danh mục -->
        <div class="tab-pane fade show active" id="category" role="tabpanel">
            <h2 class="h4 mt-4">Danh mục</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên danh mục</th>
                        <th>Số lượng sản phẩm</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->products->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Sản phẩm -->
        <div class="tab-pane fade" id="product" role="tabpanel">
            <h2 class="h4 mt-4">Sản phẩm</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Ảnh</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category ? $product->category->name : 'Không có danh mục' }}</td>
                            <td><img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" width="100"></td>
                            <td>{{ number_format($product->price, 0, ',', '.') }} VND</td>
                            <td>{{ $product->is_delete ? 'Đã xóa' : 'Còn bán' }}</td>
                            <td>
    <form action="{{ route('admin.trash.restore', $product->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-success btn-sm">Khôi phục</button>
    </form>
</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Mã giảm giá -->
        <div class="tab-pane fade" id="coupon" role="tabpanel">
            <h2 class="h4 mt-4">Mã giảm giá</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Mã giảm giá</th>
                        <th>Mô tả</th>
                        <th>Giá trị giảm</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                        <tr>
                            <td>{{ $coupon->code }}</td>
                            <td>{{ $coupon->description ?? 'Không có mô tả' }}</td>
                            <td>{{ number_format($coupon->discount_value, 0, ',', '.') }} {{ $coupon->discount_type == 1 ? '%' : 'VND' }}</td>
                            <td>{{ $coupon->is_delete ? 'Đã xóa' : 'Còn hoạt động' }}</td>
                            <td>
                                <form action="{{ route('admin.trash.restore', $coupon->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">Khôi phục</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>
</div>
@endsection
