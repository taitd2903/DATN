
@extends('layouts.layout')


@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Danh sách Banner</h2>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">Thêm mới</a>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Hình ảnh</th>
            <th>Tiêu đề</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($banners as $banner)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td><img src="{{ asset('storage/' . $banner->image) }}" width="100"></td>
            <td>{{ $banner->title }}</td>
            <td>{{ $banner->is_active ? 'Hiện' : 'Ẩn' }}</td>
            <td>
                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa banner này?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
