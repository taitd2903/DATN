@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Đánh giá sản phẩm: {{ $product->name }}</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('product.review.store', $product->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="rating" class="form-label">Số sao:</label>
            <select name="rating" id="rating" class="form-control">
                <option value="5">⭐️⭐️⭐️⭐️⭐️ (5 Sao)</option>
                <option value="4">⭐️⭐️⭐️⭐️ (4 Sao)</option>
                <option value="3">⭐️⭐️⭐️ (3 Sao)</option>
                <option value="2">⭐️⭐️ (2 Sao)</option>
                <option value="1">⭐️ (1 Sao)</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">Nhận xét:</label>
            <textarea name="comment" id="comment" rows="3" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Gửi đánh giá</button>
    </form>
</div>
@endsection
