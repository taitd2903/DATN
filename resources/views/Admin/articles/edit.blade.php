@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>Cập nhật bài viết</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $article->name) }}" required>
        </div>

        <div class="mb-3">
            <label>Slug</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug', $article->slug) }}">
        </div>

        <div class="mb-3">
            <label>Ảnh đại diện</label>
            <input type="file" name="image" class="form-control">
            @if ($article->image)
                <img src="{{ asset('storage/' . $article->image) }}" width="150" class="mt-2">
            @endif
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control">{{ old('description', $article->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Nội dung</label>
            <textarea name="content" class="form-control" rows="5">{{ old('content', $article->content) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Trạng thái</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ old('is_active', $article->is_active) == 1 ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ old('is_active', $article->is_active) == 0 ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        <div class="mb-3">
            <label>SEO Title</label>
            <input type="text" name="seo_title" class="form-control" value="{{ old('seo_title', $article->seo_title) }}">
        </div>

        <div class="mb-3">
            <label>SEO Description</label>
            <input type="text" name="seo_description" class="form-control" value="{{ old('seo_description', $article->seo_description) }}">
        </div>

        <div class="mb-3">
            <label>SEO Keywords</label>
            <input type="text" name="seo_keywords" class="form-control" value="{{ old('seo_keywords', $article->seo_keywords) }}">
        </div>

        <button class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
