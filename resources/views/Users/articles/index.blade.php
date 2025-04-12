@extends('layouts.app')

@section('content')
<section class="breadcrumb-blog" style="background-image: url('{{ asset('assets/img/banner-01.jpg') }}'); background-size: cover; background-position: center;"> 
           <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Bài viết của chúng tôi</h2>
                </div>
            </div>
        </div>
    </section>
    <section class="blog spad">
    <div class="container">
        <div class="section-title text-center mb-5">
            <span>Tin tức mới nhất</span>
            <h2>Bài viết nổi bật</h2>
        </div>

        <div class="row">
            @foreach($articles as $article)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4 d-flex">
                    <div class="blog__item shadow rounded w-100">
                        <!-- Hình ảnh -->
                        <div class="blog__item__pic" 
                             style="background-image: url('{{ asset('storage/' . $article->image) }}');
                                    background-size: cover;
                                    background-position: center;
                                    height: 220px;
                                    border-top-left-radius: 5px;
                                    border-top-right-radius: 5px;">
                        </div>

                        <!-- Nội dung bài viết -->
                        <div class="blog__item__text p-3">
                            <span class="d-block text-muted mb-2" style="font-size: 14px;">
                                <img src="{{ asset('img/icon/calendar.png') }}" alt="" style="width: 16px; margin-right: 5px;">
                                {{ $article->created_at->format('d M Y') }}
                            </span>
                            <h5 class="mb-2" style="font-weight: 600;">{{ $article->name }}</h5>
                            <p class="article-description">
    {{ \Illuminate\Support\Str::limit($article->slug, 20) }}
</p>    
                           <p>Lượt xem:{{ $article->views }}</p>
                            <a href="{{ route('articles.showUser', $article->id) }}" style="text-decoration: none">Xem thêm</a>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <!-- Phân trang -->
        <div class="d-flex justify-content-center mt-4">
            {{ $articles->links() }}
        </div>
    </div>
</section>

@endsection
