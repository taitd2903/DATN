@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Chỉnh Sửa Combo</h1>
    <form action="{{ route('combos.update', $combo->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Tên Combo</label>
            <input type="text" name="name" class="form-control" value="{{ $combo->name }}" required>
        </div>

        <div class="form-group">
            <label>Giá ưu đãi</label>
            <input type="number" name="discount_price" class="form-control" value="{{ $combo->discount_price }}" required>
        </div>

        <div class="form-group">
            <label>Ảnh Combo (Hiện tại)</label><br>
            <img src="{{ asset('storage/' . $combo->image) }}" alt="combo" width="150"><br>
            <input type="file" name="image" class="form-control mt-2">
        </div>

        <div class="form-group">
            <label>Chọn Sản Phẩm</label><br>
            @foreach($products as $product)
                <input type="checkbox" name="products[]" value="{{ $product->id }}"
                    {{ $combo->products->contains($product->id) ? 'checked' : '' }}>
                {{ $product->name }} ({{ $product->price }}đ)<br>
            @endforeach
        </div>

        <div class="form-group">
            <label>Flash Sale</label>
            <input type="checkbox" name="is_flash_sale" {{ $combo->is_flash_sale ? 'checked' : '' }}> Kích hoạt flash sale
        </div>

        <button type="submit" class="btn btn-warning">Cập Nhật</button>
    </form>
</div>
@endsection
