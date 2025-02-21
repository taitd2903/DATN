
@extends('layouts.layout')


@section('content')
<div class="container">
    <h2>Danh Mục Sản Phẩm</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Thêm Danh Mục</a>

    <ul>
      @foreach ($categories as $category)
    <li>
        <strong>{{ $category->name }}</strong>
        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning btn-sm">Sửa</a>
        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</button>
        </form>
        
        @if ($category->children->count() > 0)
            <ul>
                @foreach ($category->children as $child)
                    <li>
                        {{ $child->name }}
                        <a href="{{ route('admin.categories.edit', $child->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.categories.destroy', $child->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
    </ul>
</div>
@endsection
