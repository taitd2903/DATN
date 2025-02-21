@extends('layouts.layout')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card strpied-tabled-with-hover">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách sản phẩm</h4>
                        <p class="card-category">Danh sách tất cả các sản phẩm và biến thể</p>
                    </div>
                    
                    <div class="card-body table-full-width table-responsive">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Thêm Danh Mục</a>

                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Mô tả</th>
                                    <th>Giá gốc (VND)</th>
                                    <th>Danh mục</th>
                                    <th>Giới tính</th>
                                    <th>Tổng số lượng</th>
                                    <th>Số biến thể</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->description }}</td>
                                    <td>{{ number_format($product->base_price) }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>
                                        @if($product->gender == 'male') Nam
                                        @elseif($product->gender == 'female') Nữ
                                        @else Unisex
                                        @endif
                                    </td>
                                    <td>{{ $product->variants->sum('stock_quantity') }}</td> <!-- Tổng số lượng biến thể -->
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="collapse" data-bs-target="#variants-{{ $product->id }}">
                                            {{ $product->variants->count() }}
                                        </button>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Bảng hiển thị biến thể -->
                                <tr id="variants-{{ $product->id }}" class="collapse">
                                    <td colspan="9">
                                        <div class="card card-body p-2"> <!-- Thêm card để tạo khoảng cách -->
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Size</th>
                                                        <th>Màu sắc</th>
                                                        <th>Giá (VND)</th>
                                                        <th>Tồn kho</th>
                                                        <th>Đã bán</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($product->variants as $variant)
                                                    <tr>
                                                        <td>{{ $variant->size }}</td>
                                                        <td>{{ $variant->color }}</td>
                                                        <td>{{ number_format($variant->price) }}</td>
                                                        <td>{{ $variant->stock_quantity }}</td>
                                                        <td>{{ $variant->sold_quantity }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
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
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
