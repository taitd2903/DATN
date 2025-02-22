@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2>Danh sách sản phẩm</h2>
            <p class="text-muted">Danh sách tất cả các sản phẩm và biến thể</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Thêm Danh Mục</a>
    </div>

    <!-- Bộ lọc -->
    <div class="mb-3 d-flex gap-2">
        <input type="text" id="filter-name" class="form-control w-20" placeholder="Lọc theo tên sản phẩm...">
        <select id="filter-category" class="form-control w-20">
            <option value="">Tất cả danh mục</option>
            @foreach($categories as $category)
                <option value="{{ $category->name }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <select id="filter-gender" class="form-control w-20">
            <option value="">Tất cả giới tính</option>
            <option value="male">Nam</option>
            <option value="female">Nữ</option>
            <option value="unisex">Unisex</option>
        </select>
        <select id="filter-sold" class="form-control w-20">
            <option value="">Sắp xếp theo số lượng bán</option>
            <option value="desc">Bán chạy nhất</option>
            <option value="asc">Bán ít nhất</option>
        </select>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="product-table">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên sản phẩm</th>
                    <th>Mô tả</th>
                
                    <th>Danh mục</th>
                    <th>Giới tính</th>
                    <th>Tổng số lượng</th>
                    <th data-sort="sold">Số biến thể</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr class="product-row" data-name="{{ $product->name }}" data-category="{{ $product->category?->name ?? 'Không có danh mục' }}" data-gender="{{ $product->gender }}" data-price="{{ $product->base_price }}" data-sold="{{ $product->variants->sum('sold_quantity') }}">

                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->category?->name ?? 'Không có danh mục' }}</td>
                    <td>
                        @if($product->gender == 'male') Nam
                        @elseif($product->gender == 'female') Nữ
                        @else Unisex
                        @endif
                    </td>
                    <td>{{ $product->variants->sum('stock_quantity') }}</td>
                    <td>
                        <a href="#" class="toggle-variants" data-id="{{ $product->id }}">{{ $product->variants->count() }}</a>
                    </td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
                <tr class="variant-table" id="variants-{{ $product->id }}" style="display: none;">
                    <td colspan="9">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
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
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    function closeAllVariantTables() {
        document.querySelectorAll(".variant-table").forEach(table => {
            table.style.display = "none";
        });
    }

    function showVariantsForVisibleProducts() {
        document.querySelectorAll(".product-row").forEach(row => {
            let productId = row.querySelector(".toggle-variants")?.getAttribute("data-id");
            let variantTable = document.getElementById("variants-" + productId);
            if (variantTable && row.style.display !== "none") {
                variantTable.style.display = "table-row";
            } else if (variantTable) {
                variantTable.style.display = "none";
            }
        });
    }

    document.querySelectorAll(".toggle-variants").forEach(function (link) {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            var productId = this.getAttribute("data-id");
            var variantTable = document.getElementById("variants-" + productId);
            variantTable.style.display = variantTable.style.display === "none" ? "table-row" : "none";
        });
    });

    function applyFilters() {
        var nameFilter = document.getElementById("filter-name").value.toLowerCase();
        var categoryFilter = document.getElementById("filter-category").value;
        var genderFilter = document.getElementById("filter-gender").value;
        var soldFilter = document.getElementById("filter-sold").value;
        
        let rows = Array.from(document.querySelectorAll(".product-row"));
        rows.forEach(row => {
            let matches = true;
            if (nameFilter && !row.getAttribute("data-name").toLowerCase().includes(nameFilter)) {
                matches = false;
            }
            if (categoryFilter && row.getAttribute("data-category") !== categoryFilter) {
                matches = false;
            }
            if (genderFilter && row.getAttribute("data-gender") !== genderFilter) {
                matches = false;
            }
            row.style.display = matches ? "" : "none";
        });

        if (soldFilter) {
            rows.sort((a, b) => {
                let aValue = parseInt(a.getAttribute("data-sold"));
                let bValue = parseInt(b.getAttribute("data-sold"));
                return soldFilter === "asc" ? aValue - bValue : bValue - aValue;
            });
            rows.forEach(row => document.querySelector("#product-table tbody").appendChild(row));
        }
        showVariantsForVisibleProducts();
    }

    document.getElementById("filter-name").addEventListener("input", applyFilters);
    document.getElementById("filter-category").addEventListener("change", applyFilters);
    document.getElementById("filter-gender").addEventListener("change", applyFilters);
    document.getElementById("filter-sold").addEventListener("change", applyFilters);
});



</script>
@endsection