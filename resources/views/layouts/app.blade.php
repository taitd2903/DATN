<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocean Sports</title>
    <link rel="stylesheet" href="{{ asset('/app.css') }}">
    {{-- thêm link css --}}
    {{-- người dùng --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/light-bootstrap-dashboard.css?v=2.0.0') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/view-user/layout.css') }}" rel="stylesheet" />

</head>
<body>
    <header style="background: #333; color: white; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
        <h2>Trang sản phẩm</h2>
        <div>
            @if(Auth::check())
                <span>Chào, <strong>{{ Auth::user()->name }}</strong>!</span>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="btn btn-warning">Chuyển sang Admin</a>
            @endif
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   style="color: yellow; margin-left: 10px;">
                    Đăng xuất
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}" style="color: yellow;">Đăng nhập</a>
            @endif
        </div>
    </header>

    <main style="padding: 20px;">
        @yield('content')
    </main>
    <footer></footer>
</body>
</html>
