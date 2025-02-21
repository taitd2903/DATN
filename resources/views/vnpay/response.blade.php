<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VNPAY RESPONSE</title>
    <link href="{{ asset('assets/bootstrap.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/jquery-1.11.3.min.js') }}"></script>
</head>
<body>
    <div class="container">
        <div class="header clearfix">
            <h3 class="text-muted">VNPAY RESPONSE</h3>
        </div>

        <div class="table-responsive">
            <div class="form-group">
                <label>Mã đơn hàng:</label>
                <label>{{ $data['vnp_TxnRef'] ?? 'Không có dữ liệu' }}</label>
            </div>

            <div class="form-group">
                <label>Số tiền:</label>
                <label>{{ number_format(($data['vnp_Amount'] ?? 0) / 100, 2, ',', '.') }} VND</label>
            </div>

            <div class="form-group">
                <label>Nội dung thanh toán:</label>
                <label>{{ $data['vnp_OrderInfo'] ?? 'Không có dữ liệu' }}</label>
            </div>

            <div class="form-group">
                <label>Mã phản hồi (vnp_ResponseCode):</label>
                <label>{{ $data['vnp_ResponseCode'] ?? 'Không có dữ liệu' }}</label>
            </div>

            <div class="form-group">
                <label>Mã GD tại VNPAY:</label>
                <label>{{ $data['vnp_TransactionNo'] ?? 'Không có dữ liệu' }}</label>
            </div>

            <div class="form-group">
                <label>Mã Ngân hàng:</label>
                <label>{{ $data['vnp_BankCode'] ?? 'Không có dữ liệu' }}</label>
            </div>

            <div class="form-group">
                <label>Thời gian thanh toán:</label>
                <label>{{ $data['vnp_PayDate'] ?? 'Không có dữ liệu' }}</label>
            </div>

            <div class="form-group">
                <label>Kết quả:</label>
                @if ($isValidSignature)
                    @if ($data['vnp_ResponseCode'] == '00')
                        <div class="alert alert-success">💰 Giao dịch thành công</div>
                    @else
                        <div class="alert alert-warning">⚠️ Giao dịch không thành công</div>
                    @endif
                @else
                    <div class="alert alert-danger">🚨 Chữ ký không hợp lệ</div>
                @endif
            </div>
        </div>

        <footer class="footer">
            <p>&copy; VNPAY {{ date('Y') }}</p>
        </footer>
    </div>
</body>
</html>
