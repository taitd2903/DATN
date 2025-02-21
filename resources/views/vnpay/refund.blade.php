<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hoàn tiền giao dịch</title>
    <link href="{{ asset('assets/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/jquery-1.11.3.min.js') }}"></script>
</head>
<body>
    <div class="container">
        <div class="header clearfix">
            <h3 class="text-muted">VNPAY DEMO</h3>
        </div>

        <h3 class="mt-3">Hoàn tiền giao dịch</h3>

        <form action="{{ url('/vnpay/refund') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="TxnRef">Mã GD thanh toán cần hoàn (vnp_TxnRef):</label>
                <input id="TxnRef" class="form-control" name="TxnRef" type="text" required />
            </div>

            <div class="form-group">
                <label for="TransactionType">Kiểu hoàn tiền (vnp_TransactionType):</label>
                <select id="TransactionType" name="TransactionType" class="form-control">
                    <option value="02">Hoàn tiền toàn phần</option>
                    <option value="03">Hoàn tiền một phần</option>
                </select>
            </div>

            <div class="form-group">
                <label for="Amount">Số tiền hoàn:</label>
                <input id="Amount" class="form-control" name="Amount" type="number" min="1" required />
            </div>

            <div class="form-group">
                <label for="TransactionDate">Thời gian khởi tạo GD thanh toán (vnp_TransactionDate):</label>
                <input id="TransactionDate" class="form-control" name="TransactionDate" type="datetime-local" required />
            </div>

            <div class="form-group">
                <label for="CreateBy">User khởi tạo hoàn (vnp_CreateBy):</label>
                <input id="CreateBy" class="form-control" name="CreateBy" type="text" required />
            </div>

            <input type="submit" class="btn btn-primary" value="Hoàn tiền" />
        </form>

        @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @isset($response)
        <div class="alert alert-info mt-3">
            <h4>Phản hồi từ API:</h4>
            <pre>{{ json_encode($response, JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endisset
    </div>
</body>
</html>
