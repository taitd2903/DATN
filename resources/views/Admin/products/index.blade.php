@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2>Danh sách sản phẩm</h2>
            <p class="text-muted">Danh sách tất cả các sản phẩm và biến thể</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Thêm sản phẩm</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3 d-flex gap-2">
        <input type="text" id="filter-name" class="form-control w-20" placeholder="Lọc theo tên sản phẩm...">

        {{-- DANH MỤC PHÂN CẤP --}}
        @php
            function renderCategoryOptions($categories, $parentId = null, $prefix = '') {
                foreach ($categories->where('parent_id', $parentId) as $cat) {
                    echo '<option value="' . $cat->id . '">' . $prefix . $cat->name . '</option>';
                    renderCategoryOptions($categories, $cat->id, $prefix . '-- ');
                }
            }
        @endphp

        <select id="filter-category" class="form-control w-20">
            <option value="">Tất cả danh mục</option>
            @php renderCategoryOptions($categories); @endphp
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
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Mô tả</th>
                    <th>Danh mục</th>
                    <th>Giới tính</th>
                    <th>Số lượng tồn kho</th>
                    <th>Số biến thể</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr class="product-row"
                    data-name="{{ $product->name }}"
                    data-category="{{ $product->category_id }}"
                    data-gender="{{ $product->gender }}"
                    data-price="{{ $product->base_price }}"
                    data-sold="{{ $product->variants->sum('sold_quantity') }}">

                    <td>{{ $product->id }}</td>
                    <td>
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" width="100">
                        @else
                            <img src="{{ asset('images/default.png') }}" alt="Không có ảnh" class="img-thumbnail" width="100">
                        @endif
                    </td>
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
                        <a href="{{ route('admin.products.show', $product->id) }}">
                            Chi tiết sản phẩm
                        </a>
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
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    function applyFilters() {
        const nameFilter = document.getElementById("filter-name").value.toLowerCase();
        const categoryFilter = document.getElementById("filter-category").value;
        const genderFilter = document.getElementById("filter-gender").value;
        const soldFilter = document.getElementById("filter-sold").value;

        const rows = Array.from(document.querySelectorAll(".product-row"));

        rows.forEach(row => {
            let matches = true;

            const name = row.getAttribute("data-name").toLowerCase();
            const category = row.getAttribute("data-category");
            const gender = row.getAttribute("data-gender");

            if (nameFilter && !name.includes(nameFilter)) matches = false;
            if (categoryFilter && category !== categoryFilter) matches = false;
            if (genderFilter && gender !== genderFilter) matches = false;

            row.style.display = matches ? "" : "none";
        });

        if (soldFilter) {
            rows.sort((a, b) => {
                let aSold = parseInt(a.getAttribute("data-sold"));
                let bSold = parseInt(b.getAttribute("data-sold"));
                return soldFilter === "asc" ? aSold - bSold : bSold - aSold;
            });

            const tbody = document.querySelector("#product-table tbody");
            rows.forEach(row => tbody.appendChild(row));
        }
    }

    document.getElementById("filter-name").addEventListener("input", applyFilters);
    document.getElementById("filter-category").addEventListener("change", applyFilters);
    document.getElementById("filter-gender").addEventListener("change", applyFilters);
    document.getElementById("filter-sold").addEventListener("change", applyFilters);
});
</script>
@endsection
