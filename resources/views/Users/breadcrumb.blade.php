<nav aria-label="breadcrumb" class="my-2 container">
    <ol class="breadcrumb d-flex align-items-center">
        @foreach($breadcrumbs as $breadcrumb)
            <li class="breadcrumb-item {{ !$loop->last ? '' : 'active' }}"
                @if($loop->last) aria-current="page" @endif>
                @if($breadcrumb['url'] && !$loop->last)
                    <a class="text-decoration-underline" href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
                @else
                    <span>{{ $breadcrumb['name'] }}</span>
                @endif
            </li>
            @if(!$loop->last)
                <span class="breadcrumb-separator mx-2">/</span>
            @endif
        @endforeach
    </ol>
</nav>
