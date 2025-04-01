<!-- TRANG CHU -->
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<head>
    <title>Ocean Sports</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}">
</head>
@vite(['resources/js/app.js'])
<!-- Favicons -->
<link rel="icon" href="{{ asset('assets/img/favicon.png') }}">
<link rel="apple-touch-icon" href="{{ asset('assets/img/apple-touch-icon.png') }}">

<!-- Bootstrap 5 CSS (nếu cần) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>


<!-- Custom CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/chat.css') }}">


</head>
<body>

    <!-- Nội dung trang -->

    <!-- jQuery (cần thiết nếu dùng plugin Bootstrap nào đó) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Bootstrap 5 Bundle (Gồm PopperJS + Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

<!-- Thêm vào trước </body> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

 <!-- Start Main Top -->
 <div class="main-top">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="text-slid-box">
                        <div id="offer-box" class="carouselTicker">
                            <ul class="offer-box">
                                <li>
                                    <i class="fab fa-opencart"></i> {{ __('messages.sale_10') }}
                                </li>
                                <li>
                                    <i class="fab fa-opencart"></i> {{ __('messages.sale_50_80') }}
                                </li>
                                <li>
                                    <i class="fab fa-opencart"></i>{{ __('messages.sale_20') }}
                                </li>
                                <li>
                                    <i class="fab fa-opencart"></i> {{ __('messages.sale_50') }}
                                </li>
                                <li>
                                    <i class="fab fa-opencart"></i> Giảm 10%! Thời trang nam
                                </li>
                                <li>
                                    <i class="fab fa-opencart"></i> 50% - 80% của thời trang
                                </li>
                                <li>
                                    <i class="fab fa-opencart"></i> 20% Vợt cầu lông
                                </li>
                                <li>
                                    <i class="fab fa-opencart"></i> Giảm 50% đồ thể thao
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="custom-select-box">
                    <select style="color: white"  id="language-switcher" class="form-control" onchange="changeLanguage(this.value)">
    <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>🇺🇸 English</option>
    <option value="vi" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>🇻🇳 Tiếng Việt</option>
</select>

<script>
    function changeLanguage(locale) {
        window.location.href = "/lang/" + locale;
    }
</script>

<script>
    function changeLanguage(locale) {
        window.location.href = "/lang/" + locale;
    }
</script>
                   
         </div>
                    <div class="right-phone-box">
                        <p>Gọi tôi :- <a href="#"> +11 900 800 100</a></p>
                    </div>
                    <!-- <div class="our-link">
                        <ul>
                            <li><a href="#">Tài khoản của tôi</a></li>
                            <li><a href="#">Địa chỉ của chúng tôi</a></li>
                            <li><a href="#">Liên hệ với chúng tôi</a></li>
                        </ul>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Top -->

 <!-- Start Main Top -->
 <header class="main-header" style="margin-top: -5px">
        <!-- Start Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-default bootsnav">
            <div class="container">
                <!-- Start Header Navigation -->

                <div class="navbar-header" style="margin-right:150px">
                <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto me-xl-0">
                     <h1 class="sitename">OceanSport</h1>
                      <span>.</span>
                 </a>
                </div>
         <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu" aria-controls="navbars-rs-food" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>

                </div>
                <!-- End Header Navigation -->

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="navbar-menu" >
                    <ul class="nav navbar-nav ml-auto" data-in="fadeInDown" data-out="fadeOutUp">
                        <li class="nav-item active"><a class="nav-link" href="index.html">{{ __('messages.home') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.html">{{ __('messages.about') }}</a></li>

                        <li class="dropdown megamenu-fw">
                            <a href="#" class="nav-link dropdown-toggle arrow" data-toggle="dropdown">{{ __('messages.products') }}</a>
                            <ul class="dropdown-menu megamenu-content" role="menu">
                                <li>
                                   

                                        <div class="row">
                                         <div class="col-menu col-md-3">
                                             <h6 class="title">Top</h6>
                                        <ul class="menu-col">
                                             <li><a href="shop.html">Áo</a></li>
                                              <li><a href="shop.html">Sơ mi</a></li>
                                         <li><a href="shop.html">thể thao</a></li>
                                            <li><a href="shop.html">bóng chuyền</a></li>
                                        </ul>
                                        </div>
                                        <!-- end col-3 -->
                                        <div class="col-menu col-md-3">
                                <h6 class="title">Bóng đá</h6>
                                <ul class="menu-col">
                                    <li><a href="shop.html">Bộ thể thap</a></li>
                                    <li><a href="shop.html">Áo đá bóng</a></li>
                                    <li><a href="shop.html">Quần đá bóng</a></li>
                                    <li><a href="shop.html">Phụ kiện</a></li>
                                </ul>
                            </div>
                            <div class="col-menu col-md-3">
                                <h6 class="title">Giảm giá</h6>
                                <ul class="menu-col">
                                    <li><a href="shop.html">Quần áo</a></li>
                                    <li><a href="shop.html">Giày dép</a></li>
                                    <li><a href="shop.html">Thể thao</a></li>
                                    <li><a href="shop.html">Đá bóng</a></li>
                                </ul>
                            </div>
                            <div class="col-menu col-md-3">
                                <h6 class="title">Giảm</h6>
                                <ul class="menu-col">
                                    <li><a href="shop.html">Túi</a></li>
                                    <li><a href="shop.html">Vợt</a></li>
                                    <li><a href="shop.html">Đá bóng</a></li>
                                    <li><a href="shop.html">Đá bóng</a></li>
                                </ul>
                            </div>
                                        <!-- end col-3 -->
                                    </div>
                                    <!-- end row -->
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href={{ url('categories') }}>{{ __('messages.categories') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact-us.html">{{ __('messages.contact') }}</a></li>
                      
                    </ul>
                </div>
                <!-- /.navbar-collapse -->


 <!-- Nút Giỏ Hàng -->
<!-- <li class="cart-icon">
    <a href="{{ route('cart.index') }}">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count"></span>
    </a>
</li> -->

<!-- Nút Sản Phẩm Yêu Thích -->
<!-- <li class="wishlist-icon">
    <a href="#">
        <i class="fas fa-heart"></i>
        <span class="wishlist-count"></span>
    </a>
</li> -->

<!-- Đăng nhập  -->
<li class="user-dropdown" style="margin-right: 45px;">

    <a href="{{ route('cart.index') }}">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count"></span>
        </a>
        
    <a href="#">
        <i class="fas fa-heart"></i>
        <span class="wishlist-count"></span>
    </a>
    @if(Auth::check())
        <div class="user-info">
            @if (!empty(Auth::user()->image) && file_exists(public_path('storage/' . Auth::user()->image)))
            <img src="{{ asset('storage/' . Auth::user()->image) }}" alt="Avatar" class="user-avatar">
                  
            <span class="user-name">{{ Auth::user()->name }}</span>
            <i class="fas fa-chevron-down"></i>
        @else
               
        <span class="user-name">{{ Auth::user()->name }}</span>
        <i class="fas fa-chevron-down"></i>
        @endif
        
        


        </div>

        <ul class="dropdown-menu">
            @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-user-shield"></i> {{ __('messages.admin_dashboard') }}
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('users.profile.edit') }}">
                    <i class="fas fa-user-edit"></i> {{ __('messages.update_account') }}
                </a>
            </li>
            <li>
                <a href="{{ route('order.tracking') }}">
                    <i class="fas fa-user-edit"></i>{{ __('messages.orders') }}
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>{{ __('messages.logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    @else
        <a href="{{ route('login') }}" class="btn-login"><i class="fas fa-user"></i></a>
    @endif
</li>


            </div>
        </nav>
        <!-- End Navigation -->
          <!-- Start Top Search -->
    <div class="top-search">
        <div class="container">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Search">
                <span class="input-group-addon close-search"><i class="fa fa-times"></i></span>
            </div>
        </div>
    </div>
    <!-- End Top Search -->
   
    </header>
    <!-- End Main Top -->




    <main>
        @yield('content')

    </main>
    
     <!-- Start Footer  -->
     <footer>
    <div class="footer-main">
        <div class="container">
            <div class="row">
                <!-- Cột 1: Về Chúng Tôi -->
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="footer-widget">
                        <h4>{{ __('messages.about_us') }}</h4>
                        <p>{{ __('messages.new_products') }}</p>
                        <ul class="social-icons">
                            <li><a href="#"><i class="fab fa-facebook" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fab fa-twitter" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fab fa-linkedin" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fab fa-google-plus" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fa fa-rss" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fab fa-pinterest-p" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fab fa-whatsapp" aria-hidden="true"></i></a></li>
                        </ul>
                    </div>
                </div>

                <!-- Cột 2: Thông tin liên hệ -->
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="footer-link">
                        <h4>{{ __('messages.contact_info') }}</h4>
                        <ul>
                            <li><a href="#">{{ __('messages.about_us') }}</a></li>
                            <li><a href="#">{{ __('messages.services') }}</a></li>
                            <li><a href="#">{{ __('messages.address') }}</a></li>
                            <li><a href="#">{{ __('messages.additional_services') }}</a></li>
                            <li><a href="#">{{ __('messages.privacy_policy') }}</a></li>
                            <li><a href="#">{{ __('messages.information') }}</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Cột 3: Liên hệ với chúng tôi -->
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="footer-link-contact">
                        <h4>{{ __('messages.contact_us') }}</h4>
                        <ul>
                            <li>
                                <p><i class="fas fa-map-marker-alt"></i> {{ __('messages.address_details', ['address' => 'Số 2, 17 Kiều Mai, KS 67213']) }}</p>
                            </li>
                            <li>
                                <p><i class="fas fa-phone-square"></i> {{ __('messages.phone') }}: 
                                    <a href="tel:+1-888705770">+1-888 705 770</a>
                                </p>
                            </li>
                            <li>
                                <p><i class="fas fa-envelope"></i> {{ __('messages.email') }}: 
                                    <a href="mailto:contactinfo@gmail.com">contactinfo@gmail.com</a>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
    <!-- End Footer  -->
   <!-- Start copyright  -->
   <div class="footer-copyright">
        <p class="footer-company">Tất cả tạo nên thương hiệu &copy; 2018 <a href="#">OceanSport</a> 
    </div>
    <!-- End copyright  -->

    <a href="#" id="back-to-top" title="Back to top" style="display: none;">&uarr;</a>

  <!-- ALL JS FILES -->
 <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
 <script src="{{ asset('assets/js/jquery-3.2.1.min.js') }}"></script>
 <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

 <script src="{{ asset('assets/js/popper.min.js') }}"></script>
 <script src="{{ asset('assets/js/jquery.superslides.min.js') }}"></script>
 <script src="{{ asset('assets/js/bootstrap-select.js') }}"></script>
 <script src="{{ asset('assets/js/inewsticker.js') }}"></script>
 <script src="{{ asset('assets/js/bootsnav.js.') }}"></script>
 <script src="{{ asset('assets/js/images-loded.min.js') }}"></script>
 <script src="{{ asset('assets/js/isotope.min.js') }}"></script>
 <script src="{{ asset('assets/js/baguetteBox.min.js') }}"></script>
 <script src="{{ asset('assets/js/form-validator.min.js') }}"></script>
 <script src="{{ asset('assets/js/custom.js') }}"></script>

 <script>
$(document).ready(function(){
    $('.dropdown-toggle').click(function(e) {
        e.preventDefault();
        $(this).next('.dropdown-menu').toggleClass('show');
    });

    // Ẩn dropdown khi click ra ngoài
    $(document).click(function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
});
</script>

<!-- Hiển thị xem sp khi ấn vào icon mắt -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".view-product").forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault(); // Ngăn chặn load trang
                let imageUrl = this.getAttribute("data-image"); 
                document.getElementById("modalProductImage").setAttribute("src", imageUrl);
                $("#productImageModal").modal("show"); // Hiển thị modal
            });
        });

        // Đóng modal khi nhấn nút X
        document.getElementById("closeModalBtn").addEventListener("click", function () {
            $("#productImageModal").modal("hide");
        });
    });
</script>
</body>

</html>