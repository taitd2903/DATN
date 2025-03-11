<!-- TRANG CHU -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocean Sports</title>
    <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    
    <!-- Link CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/new.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sale.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

  
</head>

<body>
<header id="header" class="header d-flex align-items-center sticky-top">
<div class="container position-relative d-flex align-items-center justify-content-between">

<a href="" class="logo d-flex align-items-center me-auto me-xl-0">
  <!-- Uncomment the line below if you also wish to use an image logo -->
  <!-- <img src="assets/img/logo.png" alt=""> -->
  <h1 class="sitename">OceanSport</h1>
  <span>.</span>
</a>

<nav id="navmenu" class="navmenu">
  <ul>
    <li><a href="#hero" class="active">Home<br></a></li>
    <li><a href="#menu">Sản phẩm</a></li>
    <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
      <ul>
        <li><a href="#">Dropdown 1</a></li>
        <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <ul>
            <li><a href="#">Deep Dropdown 1</a></li>
            <li><a href="#">Deep Dropdown 2</a></li>
            <li><a href="#">Deep Dropdown 3</a></li>
            <li><a href="#">Deep Dropdown 4</a></li>
            <li><a href="#">Deep Dropdown 5</a></li>
          </ul>
        </li>
        <li><a href="#">Dropdown 2</a></li>
        <li><a href="#">Dropdown 3</a></li>
        <li><a href="#">Dropdown 4</a></li>
      </ul>
    </li>
    <li><a href="#account">Tài khoản</a></li>
  </ul>
  <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
</nav>

</div>
        <!-- Icon bên phải -->
            
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
            <a href="{{ route('login') }}" class="btn-custom btn-login"><i class="bi bi-people-fill" style="margin-right: 20px;"></i></a> 
       
        @endif

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
