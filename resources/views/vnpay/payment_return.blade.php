<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả thanh toán</title>
</head>
<body>
    <h2>Kết quả thanh toán</h2>
    <p>Trạng thái: {{ $status }}</p>
    <h3>Chi tiết giao dịch:</h3>
    <ul>
        @foreach($data as $key => $value)
            <li>{{ $key }}: {{ $value }}</li>
        @endforeach
    </ul>
    <a href="/">Quay lại trang chủ</a>
</body>
</html>
