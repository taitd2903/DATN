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
 




<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh mục sản phẩm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="sanpham.css">
    <link rel="stylesheet" href="menu.css">
</head>
<body>
     <!-- Thanh điều hướng -->
     <nav class="navbar navbar-expand-lg bg-light" style="height: 100px;">
      <div class="container">
        <!-- Logo -->
        <a href="#">
          <img src="./img/logo.png" alt="" width="200" height="60">
        </a>
  
        <!-- Menu + Search -->
        <div class="collapse navbar-collapse" id="navbarNav">
          <!-- Menu trái -->
     
            <nav class="navbar">
              <ul class="menu">
                  <li class="dropdown">
                      <a href="#">Nam▾</a>
                      <div class="dropdown-menu">
                          <a href="#">Quần</a>
                          <a href="#">Áo</a>
                          <a href="#">Giày</a>
                          <a href="#">Phụ kiện</a>
                      </div>
                  </li>
                  
                  <li class="dropdown">
                      <a href="#">Nữ▾</a>
                      <div class="dropdown-menu">
                          <a href="#">Quần</a>
                          <a href="#">Áo</a>
                          <a href="#">Giày</a>
                          <a href="#">Phụ kiện</a>
                      </div>
                  </li>
  
                  <li class="dropdown">
                      <a href="#">Giày & Phụ kiện▾</a>
                      <div class="dropdown-menu">
                          <a href="#">Quần</a>
                          <a href="#">Áo</a>
                          <a href="#">Giày</a>
                          <a href="#">Phụ kiện</a>
                      </div>
                  </li>
  
                  <li class="dropdown">
                      <a href="#">COLLECTION▾</a>
                      <div class="dropdown-menu">
                          <a href="#">Quần</a>
                          <a href="#">Áo</a>
                          <a href="#">Giày</a>
                          <a href="#">Phụ kiện</a>
                      </div>
                  </li>
  
                  <li class="dropdown">
                      <a href="#" style="color: brown;">SALE▾</a>
                      <div class="dropdown-menu">
                          <a href="#">Quần</a>
                          <a href="#">Áo</a>
                          <a href="#">Giày</a>
                          <a href="#">Phụ kiện</a>
                      </div>
                  </li>
              </ul>
          </nav>
  
          <!-- Thanh tìm kiếm bên phải -->
          <form class="d-flex" role="search" style="padding: 0 0 0 100px;">
            <div class="input-group">
              <!-- Ô nhập -->
              <input 
                class="form-control" 
                type="search" 
                placeholder="Tìm kiếm..." 
                aria-label="Search"
              >
              <!-- Nút submit có icon kính lúp -->
              <button class="btn btn-outline-primary" type="submit">
                <i class="bi bi-search" ></i>
              </button>
            </div>
          </form>
  
          <i class="bi bi-bag-plus" style="color: black; padding: 10px 10px 0 30px; font-size: 24px; margin-bottom: 10px;"></i>
          <i class="bi bi-bookmark-heart-fill" style="color: black; padding: 10px 10px 0 20px; font-size: 24px; margin-bottom: 10px;"></i>
          <i class="bi bi-people-fill" style="color: black; padding: 10px 10px 0 20px; font-size: 24px; margin-bottom: 10px;"></i>
  
        </div>
      </div>
    </nav>
 <hr>
    <!-- Banner -->
    <section class="banner">
        <img src="./img/bia.jpg" alt="Banner" class="img-fluid" width="100%"4>
    </section>


    <!-- Thanh điều hướng (Breadcrumb) -->
    <nav class="breadcrumb">
        <a href="#">Trang chủ</a> > <a href="#">Danh mục</a> > <a href="#">Nam</a>
    </nav>

    <div class="container">
        <!-- Sidebar bộ lọc -->
        <aside class="sidebar">
            <h2>DANH MỤC</h2>
            <ul>
                <li><a href="#">Nam</a></li>
                <li><a href="#">Nữ</a></li>
                <li><a href="#">Phụ kiện</a></li>
                <li><a href="#">SALE</a></li>
                <li><a href="#">BST</a></li>
            </ul>
           
            
            <h2>Sản phẩm</h2>
            <ul>
              <li><a href="#">Bóng đá</a></li>
              <li><a href="#">Pickleball</a></li>
              <li><a href="#">Cầu lông</a></li>
              <li><a href="#">Chạy</a></li>
          </ul>

            <input type="text" placeholder="Bạn muốn tìm gì?">
            <ul>
                <li><input type="checkbox">giày</li>
                <li><input type="checkbox"> áo</li>
                <li><input type="checkbox"> váy</li>
                <li><input type="checkbox"> quần</li>
            </ul>
        </aside>

        <!-- Danh sách sản phẩm -->
        <main class="product-list">
            <div class="product">
                <img src="./img/anh3.jpg" alt="Áo thu đông">
                <p>Áo thu đông</p>
                <span class="price">299.000đ</span>
                <button class="btn btn-danger">Thêm vào giỏ hàng</button>
            </div>
            <div class="product">
                <img src="./img/anh3.jpg" alt="Áo thu đông">
                <p>Áo thu đông</p>
                <span class="price">126.000đ</span>
                <button class="btn btn-danger">Thêm vào giỏ hàng</button>
            </div>
            <div class="product">
                <img src="./img/anh3.jpg" alt="Áo thu đông">
                <p>Áo thu đông</p>
                <span class="price">232.000đ</span>
                <button class="btn btn-danger">Thêm vào giỏ hàng</button>
            </div>
            <div class="product">
                <img src="./img/anh3.jpg" alt="Áo thu đông">
                <p>Áo thu đông</p>
                <span class="price">271.000đ</span>
                <button class="btn btn-danger">Thêm vào giỏ hàng</button>
            </div>
            <div class="product">
                <img src="./img/anh3.jpg" alt="Áo thu đông">
                <p>Áo thu đông</p>
                <span class="price">321.000đ</span>
                <button class="btn btn-danger">Thêm vào giỏ hàng</button>
            </div>
        </main>
    </div>

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