<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocean Sports</title>
    
    <!-- Link CSS -->
    <link rel="stylesheet" href="{{ asset('/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/light-bootstrap-dashboard.css?v=2.0.0') }}">
    <link rel="stylesheet" href="{{ asset('/assets/view-user/layout.css') }}">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background: white;
            color: black;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        header h2 {
            margin: 0;
            font-size: 24px;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .user-info span {
            margin-right: 15px;
            font-weight: bold;
        }
        .btn-custom {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .btn-admin {
            background: #ffc107;
        }
        .btn-logout {
            background: #dc3545;
        }
        .btn-login {
            background: #28a745;
        }
        .btn-custom:hover {
            opacity: 0.8;
        }
        main {
            padding: 30px;
            background: white;
            max-width: 1200px;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        footer {
            text-align: center;
            padding: 15px;
            background: #333;
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
       <a href="/"><img src="../assets/img/Logo.svg" alt="Logo"></a> 
        <div class="user-info">
            <a href="{{ route('cart.index') }}" class="btn-custom btn-cart" style="background: #007bff;">
                🛒 Giỏ hàng
            </a>
            
            @if(Auth::check())
                <span>Chào, <strong>{{ Auth::user()->name }}</strong>!</span>
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn-custom btn-admin">Chuyển sang Admin</a>
                @endif
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="btn-custom btn-logout">
                    Đăng xuất
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-custom btn-login">Đăng nhập</a>
            @endif
        </div>
    </header>

    <main>
        @yield('content')
    </main>
    
    <footer>
        &copy; 2025 Ocean Sports - All Rights Reserved
    </footer>
</body>
</html>
