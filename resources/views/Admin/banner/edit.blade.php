
@extends('layouts.layout')


@section('content')
<h2>Chỉnh sửa Banner</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Tiêu đề</label>
        <input type="text" name="title" class="form-control" value="{{ $banner->title }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" class="form-control">{{ $banner->description }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Hình ảnh hiện tại</label><br>
        <img src="{{ asset('storage/' . $banner->image) }}" width="150">
    </div>
    <div class="mb-3">
        <label class="form-label">Hình ảnh mới (nếu có)</label>
        <input type="file" name="image" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Liên kết</label>
        <input type="url" name="link" class="form-control" value="{{ $banner->link }}">
    </div>
    <div class="mb-3">
    <label class="form-label">Trạng thái</label>
    <div class="btn-group" role="group">
        <button type="submit" name="is_active" value="1" class="btn btn-success" {{ $banner->is_active ? 'disabled' : '' }}>Hiển thị</button>
        <button type="submit" name="is_active" value="0" class="btn btn-danger" {{ !$banner->is_active ? 'disabled' : '' }}>Ẩn</button>
    </div>
</div>
    <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
</form>
@endsection
