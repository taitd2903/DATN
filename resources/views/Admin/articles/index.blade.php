@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>Danh sách bài viết</h2>

    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary mb-3">Thêm bài viết</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Slug</th>
                <th>Trạng thái</th>
                <th>Lượt xem</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr>
                <td>{{ $article->name }}</td>
                <td>{{ $article->slug }}</td>
                <td>{{ $article->is_active ? 'Hiển thị' : 'Ẩn' }}</td>
                <td>{{ $article->views }}</td>
                <td>
                    <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-info btn-sm">Xem</a>
                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa bài viết?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $articles->links() }}
</div>
@endsection
