<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Tạo mới đơn hàng</title>
    <link href="{{ asset('assets/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/jumbotron-narrow.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/jquery-1.11.3.min.js') }}"></script>
</head>

<body>
    <div class="container">
        <div class="header clearfix">
            <h3 class="text-muted">VNPAY DEMO</h3>
        </div>
        <div class="form-group">
            <button onclick="pay()">Giao dịch thanh toán</button><br>
        </div>
        <div class="form-group">
            <button onclick="querydr()">API truy vấn kết quả thanh toán</button><br>
        </div>
        <div class="form-group">
            <button onclick="refund()">API hoàn tiền giao dịch</button><br>
        </div>
        <p>&nbsp;</p>
        <footer class="footer">
            <p>&copy; VNPAY {{ date('Y') }}</p>
        </footer>
    </div>
    <script>
        function pay() {
            window.location.href = "{{ route('vnpay.pay') }}";
        }

        function querydr() {
            window.location.href = "{{ route('vnpay.query') }}";
        }

        function refund() {
            window.location.href = "{{ route('vnpay.refund') }}";
        }
    </script>
</body>
</html>
