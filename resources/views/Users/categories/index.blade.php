{{-- @extends('layouts.app')

@section('content') --}}
<div class="container">
    <h2>Danh Mục Sản Phẩm</h2>
    
    <ul class="list-group">
        @foreach ($categories as $category)
            <li class="list-group-item">
                <strong>{{ $category->name }}</strong>

                @if ($category->children->count() > 0)
                    <ul class="list-group mt-2">
                        @foreach ($category->children as $child)
                            <li class="list-group-item">{{ $child->name }}</li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>
{{-- @endsection --}}
 