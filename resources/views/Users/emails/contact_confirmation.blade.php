<!DOCTYPE html>
<html>
<head>
    <style>
        .container { font-family: Arial, sans-serif; padding: 20px; }
        .header { color: #333; font-size: 20px; }
        .footer { margin-top: 20px; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <p class="header">Xin chào {{ $contact->name ?? 'Quý khách' }},</p>
        <p>Cảm ơn bạn đã liên hệ với chúng tôi! Chúng tôi đã nhận được thông tin của bạn và sẽ xem xét trong thời gian sớm nhất. Nếu cần thêm thông tin hoặc hỗ trợ, chúng tôi sẽ liên hệ lại với bạn ngay.</p>
        <p class="footer">Trân trọng,<br>Ocean Sports</p>
    </div>
</body>
</html>