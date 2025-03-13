@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Danh sách đánh giá đã duyệt</h2>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary mb-3">Xem đánh giá chờ duyệt</a>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Sản phẩm</th>
                    <th>Người dùng</th>
                    <th>Số sao</th>
                    <th>Nội dung</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reviews as $review)
                    <tr>
                        <td>{{ $review->id }}</td>
                        <td>{{ $review->product->name }}</td>
                        <td>{{ $review->user->name }}</td>
                        <td class="text-warning">{{ $review->rating }} ★</td>
                        <td>{{ $review->comment }}</td>
                        <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline">
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
@endsection
