<!-- TRANG CHU -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <head>
        <title>Ocean Sports</title>
        <link rel="icon" type="image/png" style="width: 100%;" href="{{ asset('assets/img/logoo.png') }}">
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

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
        </div>
    </div>
</div>
<!-- End Main Top -->

<!-- End Main Top -->
<header class="main-header" >
    <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-default bootsnav">
        <div class="container">

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-menu"
                    aria-controls="navbars-rs-food" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars"></i>
            </button>

            <div class="navbar-header">
                <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('assets/img/logoo.png') }}"
                                                                   class="logo" alt=""></a>
            </div>
            
            <div class="collapse navbar-collapse" id="navbar-menu">
                <ul class="nav navbar-nav ml-auto" data-in="fadeInDown" data-out="fadeOutUp">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">{{ __('messages.home') }}</a></li>
                   
                           <li class="nav-item  {{ request()->is('bai-viet') ? 'active' : '' }}"><a class="nav-link" href={{ url('bai-viet') }}>Bài viết</a>
                           </li>
                 
                           <li class="nav-item {{ request()->is('categories') ? 'active' : '' }}">
    <a class="nav-link" href="{{ url('categories') }}">{{ __('messages.categories') }}</a>
</li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contact.form')}}">{{ __('messages.contact') }}</a>
                    </li>

                </ul>
            </div>
            
            <div class="right__content--mobile">
                <ul class="d-flex justify-content-center align-items-center gap-4">
                    <li class="side-menu">
                        <a href="{{ route('cart.index') }}">
                            <i class="fa fa-shopping-bag"></i>
                        </a>
                    </li>


                    <!-- Đăng nhập  -->
                    <li class="user-dropdown">
                        @if(Auth::check())
                            <div class="d-flex align-items-center">
                                @if (!empty(Auth::user()->image) && file_exists(public_path('storage/' . Auth::user()->image)))
                                    <img src="{{ asset('storage/' . Auth::user()->image) }}" alt="Avatar"
                                         class="rounded-circle img-fluid me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                @else
                                    <i class="fas fa-user fa-lg me-2"></i>
                                @endif
                                <span class="d-md-block d-none">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down ms-1 d-none d-md-block"></i>
                            </div>

                            <ul class="dropdown-menu">
                                @if(in_array(auth()->user()->role, ['admin', 'staff']))
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-user-shield"></i> Chuyển sang Admin
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
                                    <a href="{{ route('returns.index') }}">
                                        <i class="fas fa-user-shield"></i> Đơn hoàn
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
                        <a href="{{ route('login') }}" class="user-icon">
    <i class="fas fa-user"></i>
</a>
                        @endif
                    </li>
                </ul>
            </div>

        </div>
    </nav>

</header>
<!-- End Main Top -->

<main>
    @if(isset($breadcrumbs))
        @include('Users.breadcrumb')
    @endif
    @yield('content')
</main>

<!-- Start Footer  -->
<footer class="footer">
    <div class="container">
        <div class="row">
         
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer__about">
                    <div class="footer__logo">
                        <a href="#"><img src="assets/img/logoo.png" alt=""></a>
                    </div>
                    <p>Khách hàng là trung tâm trong mô hình kinh doanh độc đáo của chúng tôi, bao gồm cả thiết kế.</p>
                    <a href="#"><img src="assets/img/payment.png" alt=""></a>
                </div>
            </div>

            <div class="col-lg-2 offset-lg-1 col-md-3 col-sm-6">
                <div class="footer__widget">
                    <h6>Mua sắm</h6>
                    <ul>
                        <li><a href="#">Cửa hàng thời trang</a></li>
                        <li><a href="#">Giày đang thịnh hành</a></li>
                        <li><a href="#">Phụ kiện</a></li>
                        <li><a href="#">Khuyến mãi</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-6">
                <div class="footer__widget">
                    <h6>Hỗ trợ</h6>
                    <ul>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="#">Phương thức thanh toán</a></li>
                        <li><a href="#">Giao hàng</a></li>
                        <li><a href="#">Đổi trả & hoàn tiền</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 offset-lg-1 col-md-6 col-sm-6">
                <div class="footer__widget">
                    <h6>Đăng ký nhận tin</h6>
                    <div class="footer__newslatter">
                        <p>Hãy là người đầu tiên biết về sản phẩm mới, lookbook, khuyến mãi & ưu đãi!</p>
                        <p>Liên hệ hotline: 099.999.999</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</footer>

<!-- End Footer  -->

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

<script src="{{ asset('assets/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.nicescroll.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.countdown.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.slicknav.js') }}"></script>
<script src="{{ asset('assets/js/mixitup.min.js') }}"></script>
<script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
 <script src="{{ asset('assets/js/main.js') }}"></script> 

<script>
    $(document).ready(function () {
        $('.dropdown-toggle').click(function (e) {
            e.preventDefault();
            $(this).next('.dropdown-menu').toggleClass('show');
        });

        // Ẩn dropdown khi click ra ngoài
        $(document).click(function (e) {
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('#navbar-menu');
        const rightContentMobile = document.querySelector('.right__content--mobile');

        navbarToggler.addEventListener('click', function () {
            if (navbarCollapse.classList.contains('show')) {
                rightContentMobile.style.display = 'block';
            } else {
                rightContentMobile.style.display = 'none';
            }
        });

        navbarCollapse.addEventListener('hidden.bs.collapse', function () {
            rightContentMobile.style.display = 'block';
        });

        navbarCollapse.addEventListener('shown.bs.collapse', function () {
            rightContentMobile.style.display = 'none';
        });
    });

     //cam xuc bai viet
     document.querySelectorAll('.reaction-btn').forEach(button => {
        button.addEventListener('click', () => {
            let countSpan = button.querySelector('.count');
            let currentCount = parseInt(countSpan.innerText);
            countSpan.innerText = currentCount + 1;

            button.classList.add('animate');
            setTimeout(() => {
                button.classList.remove('animate');
            }, 400);
        });
    });
</script>
</body>

</html>
