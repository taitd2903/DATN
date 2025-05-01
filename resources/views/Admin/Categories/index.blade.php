@extends('layouts.layout')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center ">Danh Mục Sản Phẩm</h2>

        {{-- Thông báo --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-3 mb-4 justify-content-center">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm danh mục theo tên..."
                    value="{{ request('keyword') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary w-100">Tìm kiếm</button>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary w-100">Đặt lại</a>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary w-100">Thêm danh mục</a>
            </div>
        </form>
        


        <div class="card shadow-sm">
            <div class="card-body">
                @if ($categories->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach ($categories as $category)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary me-2">Danh mục cha</span>
                                        <strong>{{ $category->name }}</strong>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="btn btn-warning btn-sm">Sửa</a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Children --}}
                                @if ($category->children->count() > 0)
                                    <ul class="list-group mt-2 ms-4">
                                        @foreach ($category->children as $child)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge bg-secondary me-2">Danh mục con</span>
                                                    {{ $child->name }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.categories.edit', $child->id) }}"
                                                        class="btn btn-warning btn-sm">Sửa</a>
                                                    <form action="{{ route('admin.categories.destroy', $child->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Không tìm thấy danh mục nào phù hợp.</p>
                @endif
            </div>
        </div>
    </div>
@endsection