<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Thanh toán VNPAY">
    <meta name="author" content="">
    <title>Tạo mới đơn hàng</title>
    <link href="{{ asset('assets/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/jumbotron-narrow.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/jquery-1.11.3.min.js') }}"></script>
</head>

<body>
    <div class="container">
        <h3 class="text-center">Tạo mới đơn hàng</h3>
        <div class="table-responsive">
            <form action="{{ route('vnpay.pay') }}" id="frmCreateOrder" method="post">
                @csrf
                <div class="form-group">
                    <label for="amount">Số tiền</label>
                    <input class="form-control" id="amount" name="amount" type="number" min="1000" max="100000000" value="10000" required />
                </div>
                <h4>Chọn phương thức thanh toán</h4>
                <div class="form-group">
                    <h5>Cách 1: Chuyển hướng sang Cổng VNPAY</h5>
                    <input type="radio" id="bankCode1" name="bankCode" value="" checked>
                    <label for="bankCode1">Cổng thanh toán VNPAYQR</label><br>
                    
                    <h5>Cách 2: Chọn phương thức tại trang đơn vị</h5>
                    <input type="radio" id="bankCode2" name="bankCode" value="VNPAYQR">
                    <label for="bankCode2">Thanh toán qua VNPAYQR</label><br>

                    <input type="radio" id="bankCode3" name="bankCode" value="VNBANK">
                    <label for="bankCode3">Thanh toán ATM/Tài khoản nội địa</label><br>

                    <input type="radio" id="bankCode4" name="bankCode" value="INTCARD">
                    <label for="bankCode4">Thanh toán qua thẻ quốc tế</label><br>
                </div>
                <div class="form-group">
                    <h5>Chọn ngôn ngữ thanh toán:</h5>
                    <input type="radio" id="language1" name="language" value="vn" checked>
                    <label for="language1">Tiếng Việt</label><br>
                    <input type="radio" id="language2" name="language" value="en">
                    <label for="language2">English</label><br>
                </div>
                <button type="submit" class="btn btn-primary">Thanh toán</button>
            </form>
        </div>
        <p>&nbsp;</p>
        <footer class="footer text-center">
            <p>&copy; VNPAY {{ date('Y') }}</p>
        </footer>
    </div>
</body>
</html>
