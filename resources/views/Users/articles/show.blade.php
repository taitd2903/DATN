@extends('layouts.app')
@section('content')
 <!-- Blog Details Hero Begin -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
 <section class="blog-hero spad">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-9 text-center">
                    <div class="blog__hero__text">
                    <h2>{{ $article->name }}</h2>
                        <ul>
                            <li> Ngày {{ $article->created_at->format('d M Y') }}</li>
                            <li><strong>Lượt xem:</strong> {{ $article->views }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Blog Details Hero End -->
<div class="container">
<section class="blog-details spad">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-12">
                    <div class="blog__details__pic">
                        <img src="{{ asset('storage/' . $article->image) }}" alt="">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="blog__details__content">
                        <!-- <div class="blog__details__share">
                            <span>share</span>
                            <ul>
                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#" class="twitter"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#" class="youtube"><i class="fa fa-youtube-play"></i></a></li>
                                <li><a href="#" class="linkedin"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div> -->
                        <div class="blog__details__text">
                        <p><strong>Slug:</strong> {{ $article->slug }}</p>
                        <!-- <p><strong>Mô tả:</strong> {{ $article->description }}</p> -->
                        </div>
                        <div class="blog__details__quote">
                            <i class="fa fa-quote-left"></i>
                            <p><strong>Mô tả:</strong> {{ $article->description }}</p>
                        </div>
                        <div class="blog__details__text">
                            <!-- Nội dung -->
                        <p><strong></strong> {!! nl2br(e($article->content)) !!}</p>
                        </div>
                        <div class="blog__details__option">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="blog__details__author">
                                        <div class="blog__details__author__pic">
                                            <img src="img/blog/details/blog-author.jpg" alt="">
                                        </div>
                                        <div class="blog__details__author__text">
                                            <h5></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="blog__details__tags">
                                        <a href="#">{{ $article->seo_title }}</a>
                                        <a href="#"> {{ $article->seo_keywords }}</a>
                                        <a href="#">{{ $article->is_active ? 'Hiển thị' : 'Ẩn' }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>

    <div class="blog__details__reactions">
    <span>Bạn cảm thấy bài viết này thế nào?</span>
    <ul class="reaction-list d-flex gap-3">
        <li>
            <button class="reaction-btn" data-type="cute" title="Dễ thương">
                <i class="bi bi-emoji-smile"></i>
                <span class="count">0</span>
            </button>
        </li>
        <li>
            <button class="reaction-btn" data-type="funny" title="Hài hước">
                <i class="bi bi-emoji-laughing"></i>
                <span class="count">0</span>
            </button>
        </li>
        <li>
            <!-- <button class="reaction-btn" data-type="wow" title="Ngạc nhiên">
                <i class="bi bi-emoji-surprise"></i>
                <span class="count">0</span>
            </button>
        </li> -->
        <li>
            <button class="reaction-btn" data-type="sad" title="Buồn">
                <i class="bi bi-emoji-frown"></i>
                <span class="count">0</span>
            </button>
        </li>
        <li>
            <button class="reaction-btn" data-type="love" title="Tuyệt vời">
                <i class="bi bi-heart-fill text-danger"></i>
                <span class="count">0</span>
            </button>
        </li>
    </ul>
</div>

                        <div class="col-lg-12 text-center">
                        <a href="{{ route('article.index') }}" class="site-btn">Quay lại</a>
                                     
                        </div>
                        <!-- <div class="blog__details__btns">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <a href="" class="blog__details__btns__item">
                                        <p><span class="arrow_left"></span> Previous Pod</p>
                                        <h5>It S Classified How To Utilize Free Classified Ad Sites</h5>
                                    </a>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <a href="" class="blog__details__btns__item blog__details__btns__item--next">
                                        <p>Next Pod <span class="arrow_right"></span></p>
                                        <h5>Tips For Choosing The Perfect Gloss For Your Lips</h5>
                                    </a>
                                </div>
                            </div>
                        </div> -->
                        <!-- <div class="blog__details__comment">
                            <h4>Leave A Comment</h4>
                            <form action="#">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4">
                                        <input type="text" placeholder="Name">
                                    </div>
                                    <div class="col-lg-4 col-md-4">
                                        <input type="text" placeholder="Email">
                                    </div>
                                    <div class="col-lg-4 col-md-4">
                                        <input type="text" placeholder="Phone">
                                    </div>
                                    <div class="col-lg-12 text-center">
                                        <textarea placeholder="Comment"></textarea>
                                        <button type="submit" class="site-btn">Post Comment</button>
                                    </div>
                                </div>
                            </form>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>
<!-- <div class="container">
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

    <a href="{{ route('article.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
</div> -->
@endsection
