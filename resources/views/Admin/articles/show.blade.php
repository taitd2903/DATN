@extends('layouts.layout')
@section('content')
<div class="container">
    <h2>{{ $article->name }}</h2>
    <p><strong>Slug:</strong> {{ $article->slug }}</p>

    @if ($article->image)
        <img src="{{ asset('storage/' . $article->image) }}" class="mb-3" width="300">
    @endif

    <p><strong>Mô tả:</strong> {{ $article->description }}</p>
    <p><strong>Nội dung:</strong> {!! nl2br(e($article->content)) !!}</p>
    <p><strong>SEO Title:</strong> {{ $article->seo_title }}</p>
    <p><strong>SEO Description:</strong> {{ $article->seo_description }}</p>
    <p><strong>SEO Keywords:</strong> {{ $article->seo_keywords }}</p>
    <p><strong>Lượt xem:</strong> {{ $article->views }}</p>
    <p><strong>Trạng thái:</strong> {{ $article->is_active ? 'Hiển thị' : 'Ẩn' }}</p>

    <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
</div>
@endsection
