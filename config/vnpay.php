<?php

return [
    'vnp_TmnCode' => env('VNP_TMN_CODE', 'FZVRHBJ3'),
    'vnp_HashSecret' => env('VNP_HASH_SECRET', 'PC2XQFFD9629KAR9KR9XUK41W6CKU8LE'),
    'vnp_Url' => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'vnp_ReturnUrl' => env('VNP_RETURN_URL', 'http://localhost/vnpay_php/vnpay_return.php'),
    'vnp_ApiUrl' => env('VNP_API_URL', 'http://sandbox.vnpayment.vn/merchant_webapi/merchant.html'),
    'vnp_TransactionApiUrl' => env('VNP_TRANSACTION_API_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'),
];
