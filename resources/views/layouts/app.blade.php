<!-- TRANG CHU -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocean Sports</title>
    
    <!-- Link CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/new.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sale.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}"> -->

  
</head>

<body>
<header>
    <nav class="navbar">
        <a href="/" class="logo">
            <img src="../assets/img/logo.png" alt="Logo">
        </a>

          <!-- Menu + Search -->
          <!-- <div class="collapse navbar-collapse" id="navbarNav"> -->
            <!-- Menu trái -->
       
              <nav class="navbar">
        <!-- Menu -->
        <ul class="menu">
                    <li class="dropdown">
                        <a href="#">Nam▾</a>
                        <div class="dropdown-menu">
                            <a href="#">Quần</a><br>
                            <a href="#">Áo</a><br>
                            <a href="#">Giày</a><br>
                            <a href="#">Phụ kiện</a><br>
                        </div>
                    </li>
                    
                    <li class="dropdown">
                        <a href="#">Nữ▾</a>
                        <div class="dropdown-menu">
                        <a href="#">Quần</a><br>
                            <a href="#">Áo</a><br>
                            <a href="#">Giày</a><br>
                            <a href="#">Phụ kiện</a><br>
                        </div>
                    </li>
    
                    <li class="dropdown">
                        <a href="#">Giày & Phụ kiện▾</a>
                        <div class="dropdown-menu">
                        <a href="#">Quần</a><br>
                            <a href="#">Áo</a><br>
                            <a href="#">Giày</a><br>
                            <a href="#">Phụ kiện</a><br>
                        </div>
                    </li>
    
                    <li class="dropdown">
                        <a href="#">COLLECTION▾</a>
                        <div class="dropdown-menu">
                        <a href="#">Quần</a><br>
                            <a href="#">Áo</a><br>
                            <a href="#">Giày</a><br>
                            <a href="#">Phụ kiện</a><br>
                        </div>
                    </li>
    
                    <li class="dropdown">
                        <a href="#" style="color: brown;">SALE▾</a>
                        <div class="dropdown-menu">
                        <a href="#">Quần</a><br>
                            <a href="#">Áo</a><br>
                            <a href="#">Giày</a><br>
                            <a href="#">Phụ kiện</a><br>
                        </div>
                    </li>
                </ul>
</nav>

        <!-- Tìm kiếm -->
         
        <form class="d-flex" role="search" style="padding: 0 0 0 100px">
        <div class="search-box">
                <input type="text" name="name" class="form-control" placeholder="Tìm kiếm theo tên" value="{{ request('name') }}">
          
                <!-- Nút submit có icon kính lúp -->
                <button class="btn btn-outline-primary" type="submit">
                  <i class="bi bi-search" ></i>
                </button>
              </div>
           
        </form>

      

        <!-- Icon bên phải -->

        <div class="icons">
        <i class="bi bi-bag-plus"></i>
        <i class="bi bi-bookmark-heart-fill"></i>
           
            <!-- <i class="bi bi-people-fill"></i> -->

        <!-- <i class="bi bi-bag-plus" style="color: black; margin-left: -500px; font-size: 24px; margin-bottom: 10px;"></i>
            <i class="bi bi-bookmark-heart-fill" style="color: black; font-size: 24px; margin-bottom: 10px;"></i> -->
            
            
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
            <a href="{{ route('login') }}" class="btn-custom btn-login"><i class="bi bi-people-fill"></i></a> 
       
        @endif

       
            
        </div>
    </nav>
</header>

    

    <main>
        @yield('content')
    </main>
    
    <footer class="footer-info container py-5">
    <div class="row text-center text-md-start">
      <!-- Cột 1: Tư vấn & Liên hệ -->
      <div class="col-md-4 mb-4">
        <h5 class="mb-3 fw-bold">TƯ VẤN & LIÊN HỆ (8:30 - 17:00)</h5>
        <p class="mb-1">1900 0000</p>
        <p class="mb-0">Thứ 7 & Chủ Nhật nghỉ</p>
      </div>

      <!-- Cột 2: Đăng ký nhận thông tin -->
      <div class="col-md-4 mb-4">
        <h5 class="mb-3 fw-bold">ĐĂNG KÝ NHẬN THÔNG TIN TỪ OCEAN SPORTS</h5>
        <form class="d-flex flex-column flex-sm-row">
          <input
            type="email"
            class="form-control mb-2 mb-sm-0"
            placeholder="Email của bạn"
            aria-label="Email"
          />
          <button class="btn btn-dark ms-sm-2">Gửi</button>
        </form>
        <small class="text-muted d-block mt-2">
          Đừng bỏ lỡ các chương trình khuyến mãi, hậu mãi hấp dẫn
        </small>
      </div>

      <!-- Cột 3: Ocean Sports & Mạng Xã Hội -->
      <div class="col-md-4 mb-4">
        <h5 class="mb-3 fw-bold">Ocean Sports thuộc Mạng Xã Hội</h5>
        <p class="mb-2">Theo dõi chúng tôi trên:</p>
        <div>
          <!-- Thay thế icon mạng xã hội bằng hình ảnh hoặc icon FontAwesome -->
          <a href="#" class="me-3 text-decoration-none">Facebook</a>
          <a href="#" class="me-3 text-decoration-none">Instagram</a>
          <a href="#" class="text-decoration-none">Youtube</a>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>
