
@extends('layouts.layout')


@section('content')
<h2>Thêm mới Banner</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label class="form-label">Tiêu đề</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Hình ảnh</label>
        <input type="file" name="image" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Liên kết</label>
        <input type="url" name="link" class="form-control">
    </div>
    <!-- <div class="form-check">
        <input type="checkbox" name="is_active" value="1" class="form-check-input">
        <label class="form-check-label">Hiển thị banner</label>
    </div> -->
    <button type="submit" class="btn btn-success mt-3">Thêm mới</button>
</form>
@endsection
