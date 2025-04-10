@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Danh sách bài viết</h2>

  

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Ảnh </th>
                <th>Slug</th>
                <th>Trạng thái</th>
                <th>Lượt xem</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr>

                <td>{{ $article->name }}</td>
                <td> <img src="{{ asset('storage/' . $article->image) }}" class="mb-3" width="300"></td> 
                <td>{{ $article->slug }}</td>
                <td>{{ $article->is_active ? 'Hiển thị' : 'Ẩn' }}</td>
                <td>{{ $article->views }}</td>
                <td>
                    <a href="{{ route('articles.showUser', $article) }}" class="btn btn-info btn-sm">Xem</a>

                  
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $articles->links() }}
</div>
@endsection
