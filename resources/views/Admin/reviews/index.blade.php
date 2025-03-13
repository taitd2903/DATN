@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Danh sách đánh giá chờ duyệt</h2>
    <a href="{{ route('admin.reviews.approved') }}" class="btn btn-secondary mb-3">Xem đánh giá đã duyệt</a>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Sản phẩm</th>
                <th>Người đánh giá</th>
                <th>Số sao</th>
                <th>Bình luận</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reviews as $review)
                <tr>
                    <td>{{ $review->id }}</td>
                    <td>{{ $review->product->name }}</td>
                    <td>{{ $review->user->name }}</td>
                    <td>{{ $review->rating }} ⭐</td>
                    <td>{{ $review->comment }}</td>
                    <td>
                        <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Duyệt</button>
                        </form>
                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa đánh giá này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
