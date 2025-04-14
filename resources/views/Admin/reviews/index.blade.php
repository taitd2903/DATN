

@extends('layouts.layout')

@section('content')
<div class="container py-4">
    <h2 class="text-center text-primary fw-bold mb-4">Danh sách đánh giá chờ duyệt</h2>
    <a href="{{ route('admin.reviews.approved') }}" class="btn btn-secondary mb-3">Xem đánh giá đã duyệt</a>
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Bộ lọc -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}" id="filterForm">
                <div class="row g-3">
                    <!-- Lọc theo tên sản phẩm -->
                    <div class="col-md-4">
                        <label for="product_name" class="form-label">Tên Sản Phẩm</label>
                        <input type="text" name="product_name" id="product_name" class="form-control" 
                               value="{{ request('product_name') }}" placeholder="Nhập tên sản phẩm">
                    </div>
                    <!-- Lọc theo tên người đánh giá -->
                    <div class="col-md-4">
                        <label for="user_name" class="form-label">Người Đánh Giá</label>
                        <input type="text" name="user_name" id="user_name" class="form-control" 
                               value="{{ request('user_name') }}" placeholder="Nhập tên người đánh giá">
                    </div>
                    <!-- Lọc theo số sao -->
                    <div class="col-md-2">
                        <label for="rating" class="form-label">Số Sao</label>
                        <select name="rating" id="rating" class="form-select">
                            <option value="">Tất cả</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} ⭐</option>
                            @endfor
                        </select>
                    </div>
                    <!-- Nút tìm kiếm và reset -->
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Lọc</button>
                        <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách đánh giá -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
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
                    <?php
                        // Khởi tạo danh sách đánh giá đã lọc
                        $filteredReviews = collect($reviews ?? []);

                        // Lọc theo tên sản phẩm
                        if (request('product_name')) {
                            $productName = strtolower(trim(request('product_name')));
                            $filteredReviews = $filteredReviews->filter(function ($review) use ($productName) {
                                $name = isset($review->product->name) ? strtolower($review->product->name) : '';
                                return str_contains($name, $productName);
                            });
                        }

                        // Lọc theo tên người đánh giá
                        if (request('user_name')) {
                            $userName = strtolower(trim(request('user_name')));
                            $filteredReviews = $filteredReviews->filter(function ($review) use ($userName) {
                                $name = isset($review->user->name) ? strtolower($review->user->name) : '';
                                return str_contains($name, $userName);
                            });
                        }

                        // Lọc theo số sao
                        if (request('rating')) {
                            $rating = request('rating');
                            $filteredReviews = $filteredReviews->filter(function ($review) use ($rating) {
                                return isset($review->rating) && strval($review->rating) === strval($rating);
                            });
                        }
                    ?>

                    @if($filteredReviews->isNotEmpty())
                        @foreach ($filteredReviews as $review)
                            <tr>
                                <td>{{ $review->id ?? 'N/A' }}</td>
                                <td>{{ $review->product->name ?? 'Không có sản phẩm' }}</td>
                                <td>{{ $review->user->name ?? 'Không có người dùng' }}</td>
                                <td>{{ $review->rating ?? 0 }} ⭐</td>
                                <td>{{ $review->comment ?? 'Không có bình luận' }}</td>
                                <td>
                                    {{-- <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Duyệt</button>
                                    </form> --}}
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                Không có đánh giá nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Xử lý form lọc
        const filterForm = document.getElementById('filterForm');
        filterForm.addEventListener('submit', function (e) {
            // Xóa các input rỗng
            const inputs = filterForm.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (!input.value) {
                    input.removeAttribute('name');
                }
            });
        });
    });
</script>
@endsection