<!--
=========================================================
 Light Bootstrap Dashboard - v2.0.1
=========================================================

 Product Page: https://www.creative-tim.com/product/light-bootstrap-dashboard
 Copyright 2019 Creative Tim (https://www.creative-tim.com)
 Licensed under MIT (https://github.com/creativetimofficial/light-bootstrap-dashboard/blob/master/LICENSE)

 Coded by Creative Tim

=========================================================

 The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/Logo.svg') }}">

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Quản trị Ocean Sports</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <!-- CSS Files -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/light-bootstrap-dashboard.css?v=2.0.0') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/adminchat.css') }}">
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="{{ asset('assets/css/demo.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

</head>
@stack('scripts')
<body>
    <div class="wrapper">
        <div class="sidebar" data-image="../assets/img/sidebar-.jpg" data-color="blue">
            <!--
         Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"
 
         Tip 2: you can also add an image using data-image tag
     -->
            <div class="sidebar-wrapper">
                <div class="logo">
                    <a href="{{ route('admin.statistics.profit') }}" class="simple-text">
                        {{-- <img src="../assets/img/logo.png" alt="Logo"> --}}
                        <h4>Ocean Sports</h4>

                    </a>
                </div>
                <ul class="nav">
                <li class="{{ request()->routeIs('admin.statistics.*') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.statistics.profit') }}">
                            <i class="nc-icon nc-chart-pie-35"></i>
                            <p>Thống kê</p>
                        </a>
                    </li>
                  
                    <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.users.index') }}">
        <i class="nc-icon nc-circle-09"></i>
        <p>Người dùng</p>
    </a>
</li>

                   
             

<li class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.categories.index') }}">
                            <i class="nc-icon nc-notes"></i>
                            <p>Danh mục</p>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.products.index') }}">
                            <i class="nc-icon nc-paper-2"></i>
                            <p>Sản phẩm</p>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.coupons.index') }}">
                            <i class="nc-icon nc-atom"></i>
                            <p>Mã giảm giá</p>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.banners.index') }}">
                            <i class="nc-icon nc-atom"></i>
                            <p>quản lý Banner</p>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.chat') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.chat') }}">
                            <i class="nc-icon nc-chat-round"></i>
                            <p>Tin nhắn</p>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.returns.index') }}">
                            <i class="nc-icon nc-atom"></i>
                            <p>quản lý đơn hoàn</p>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.reviews.index') }}">
                            <i class="nc-icon nc-atom"></i>
                            <p>quản lý đánh giá</p>
                        </a>
                    </li>
                    
               
                    <li class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.orders.index') }}">
                            <i class="nc-icon nc-cart-simple"></i> <!-- Đổi icon thành giỏ hàng -->
                            <p>Quản lý đơn hàng</p>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.articles.index') }}">
                            <i class="nc-icon nc-cart-simple"></i> <!-- Đổi icon thành giỏ hàng -->
                            <p>Quản lý bài viết </p>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.contacts.index') }}">
                            <i class="nc-icon nc-email-85"></i>
                            <p>Quản lý liên hệ</p>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('admin.trash.*') ? 'active' : '' }}">

                        <a class="nav-link" href="{{ route('admin.trash.index') }}">
                            <i class="nc-icon nc-pin-3"></i>
                            <p>Thùng rác</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg " color-on-scroll="500">
                <div class="container-fluid">

                    <!-- <button href="" class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                         <span class="navbar-toggler-bar burger-lines"></span>
                         <span class="navbar-toggler-bar burger-lines"></span>
                         <span class="navbar-toggler-bar burger-lines"></span>
                     </button> -->
                    <div class="collapse navbar-collapse justify-content-end" id="navigation">
                        <!-- /thông báo/ -->
                        <!-- <ul class="nav navbar-nav mr-auto">
                             <li class="nav-item">
                                 <a href="#" class="nav-link" data-toggle="dropdown">
                                     <i class="nc-icon nc-palette"></i>
                                     <span class="d-lg-none">Dashboard</span>
                                 </a>
                             </li>
                             <li class="dropdown nav-item">
                                 <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                                     <i class="nc-icon nc-planet"></i>
                                     <span class="notification">5</span>
                                     <span class="d-lg-none">Notification</span>
                                 </a>
                                 <ul class="dropdown-menu">
                                     <a class="dropdown-item" href="#">Notification 1</a>
                                     <a class="dropdown-item" href="#">Notification 2</a>
                                     <a class="dropdown-item" href="#">Notification 3</a>
                                     <a class="dropdown-item" href="#">Notification 4</a>
                                     <a class="dropdown-item" href="#">Another notification</a>
                                 </ul>
                             </li>
                             <li class="nav-item">
                                 <a href="#" class="nav-link">
                                     <i class="nc-icon nc-zoom-split"></i>
                                     <span class="d-lg-block">&nbsp;Search</span>
                                 </a>
                             </li>
                         </ul> -->
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="http://example.com"
                                    id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span class="no-icon">{{ Auth::user()->name }}</span>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <!-- <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Thông tin tài
                                        khoản</a> -->

<!-- 
                                    <div class="divider"></div> -->
                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <span class="no-icon">Log out</span>
                                    </a>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('products.index') }}">Trang Người dùng</a>

                            </li>



                            <li class="nav-item">
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>

                            </li>

                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
            @yield('content')
            <div id="messageNotification" class="notification" style="display: none;">
                <p>Cúc cu, khách tới!</span>!</p>
                <button onclick="window.location.href='/admin/chat'">Xem</button>
                <button onclick="closeNotification()">Đóng</button>
            </div>

        </div>
    </div>
    <!--   -->
    <!-- <div class="fixed-plugin">
     <div class="dropdown show-dropdown">
         <a href="#" data-toggle="dropdown">
             <i class="fa fa-cog fa-2x"> </i>
         </a>
 
         <ul class="dropdown-menu">
             <li class="header-title"> Sidebar Style</li>
             <li class="adjustments-line">
                 <a href="javascript:void(0)" class="switch-trigger">
                     <p>Background Image</p>
                     <label class="switch">
                         <input type="checkbox" data-toggle="switch" checked="" data-on-color="primary" data-off-color="primary"><span class="toggle"></span>
                     </label>
                     <div class="clearfix"></div>
                 </a>
             </li>
             <li class="adjustments-line">
                 <a href="javascript:void(0)" class="switch-trigger background-color">
                     <p>Filters</p>
                     <div class="pull-right">
                         <span class="badge filter badge-black" data-color="black"></span>
                         <span class="badge filter badge-azure" data-color="azure"></span>
                         <span class="badge filter badge-green" data-color="green"></span>
                         <span class="badge filter badge-orange" data-color="orange"></span>
                         <span class="badge filter badge-red" data-color="red"></span>
                         <span class="badge filter badge-purple active" data-color="purple"></span>
                     </div>
                     <div class="clearfix"></div>
                 </a>
             </li>
             <li class="header-title">Sidebar Images</li>
 
             <li class="active">
                 <a class="img-holder switch-trigger" href="javascript:void(0)">
                     <img src="../assets/img/sidebar-1.jpg" alt="" />
                 </a>
             </li>
             <li>
                 <a class="img-holder switch-trigger" href="javascript:void(0)">
                     <img src="../assets/img/sidebar-3.jpg" alt="" />
                 </a>
             </li>
             <li>
                 <a class="img-holder switch-trigger" href="javascript:void(0)">
                     <img src="..//assets/img/sidebar-4.jpg" alt="" />
                 </a>
             </li>
             <li>
                 <a class="img-holder switch-trigger" href="javascript:void(0)">
                     <img src="../assets/img/sidebar-5.jpg" alt="" />
                 </a>
             </li>
 
             <li class="button-container">
                 <div class="">
                     <a href="http://www.creative-tim.com/product/light-bootstrap-dashboard" target="_blank" class="btn btn-info btn-block btn-fill">Download, it's free!</a>
                 </div>
             </li>
 
             <li class="header-title pro-title text-center">Want more components?</li>
 
             <li class="button-container">
                 <div class="">
                     <a href="http://www.creative-tim.com/product/light-bootstrap-dashboard-pro" target="_blank" class="btn btn-warning btn-block btn-fill">Get The PRO Version!</a>
                 </div>
             </li>
 
             <li class="header-title" id="sharrreTitle">Thank you for sharing!</li>
 
             <li class="button-container">
                 <button id="twitter" class="btn btn-social btn-outline btn-twitter btn-round sharrre"><i class="fa fa-twitter"></i> · 256</button>
                 <button id="facebook" class="btn btn-social btn-outline btn-facebook btn-round sharrre"><i class="fa fa-facebook-square"></i> · 426</button>
             </li>
         </ul>
     </div>
 </div>
  -->
</body>
<!--   Core JS Files   -->
<script>
    function closeNotification() {
        $('#messageNotification').hide();
    }
    const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        encrypted: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }
    });
    const channel = pusher.subscribe('chat.user.admin');
    channel.bind('message.sent', function(data) {
        if (data.receiverId === 'admin') {
            $('#senderName').text(data.userName);
            $('#messageNotification').show();
            setTimeout(() => {
                $('#messageNotification').hide();
            }, 5000);
        }
    });
</script>
<script src="{{ asset('assets/js/core/jquery.3.2.1.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}" type="text/javascript"></script>
<!-- Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="{{ asset('assets/js/plugins/bootstrap-switch.js') }}"></script>
<!-- Google Maps Plugin -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!-- Chartist Plugin -->
<script src="{{ asset('assets/js/plugins/chartist.min.js') }}"></script>
<!-- Notifications Plugin -->
<script src="{{ asset('assets/js/plugins/bootstrap-notify.js') }}"></script>
<!-- Control Center for Light Bootstrap Dashboard: scripts for the example pages etc -->
<script src="{{ asset('assets/js/light-bootstrap-dashboard.js?v=2.0.0') }}" type="text/javascript"></script>
<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
<script src="{{ asset('assets/js/demo.js') }}"></script>


</html>
