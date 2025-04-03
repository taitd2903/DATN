@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h2 class="text-center text-primary fw-bold">Yêu Cầu Hoàn Hàng</h2>
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <form action="{{ route('returns.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order_id }}">

                    <div class="mb-3">
                        <label for="reason" class="form-label fw-semibold">Lý do hoàn hàng:</label>
                        <textarea name="reason" id="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">Ảnh minh chứng (tùy chọn):</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        @error('image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Gửi Yêu Cầu</button>
                </form>
            </div>
        </div>
    </div>
@endsection
