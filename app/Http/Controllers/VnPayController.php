<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VnPayController extends Controller
{
    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'language' => 'nullable|string',
            'bankCode' => 'nullable|string',
        ]);

        $vnp_TxnRef = time();
        $vnp_Amount = $request->input('amount') * 100;
        $vnp_Locale = $request->input('language', 'vn');
        $vnp_BankCode = $request->input('bankCode');
        $vnp_IpAddr = $request->ip();

        $vnp_TmnCode = config('vnpay.vnp_TmnCode');
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $vnp_Url = config('vnpay.vnp_Url');
        $vnp_ReturnUrl = config('vnpay.vnp_ReturnUrl');

        $vnp_CreateDate = now()->format('YmdHis');
        $vnp_ExpireDate = now()->addMinutes(30)->format('YmdHis');

        // Tạo địa chỉ đầy đủ
        $note = $request->input('address') . ', ' . $request->input('ward') . ', ' . 
                           $request->input('district') . ', ' . $request->input('city');

        // Lưu đơn hàng vào bảng orders
        
        $order = Order::create([
            'customer_name' => $request->input('name'),
            'customer_phone' => $request->input('phone'),
            'customer_address' => $request->input('address'),
            'user_id' => Auth::id(),
            'note' => $note,
            'total_price' => $request->input('amount'),
            'coupon_code' => $request->input('coupon_code'),
            'payment_method' => 'vnpay',
            'status' => 'Chờ xác nhận',
            'payment_status' => 'Đã thanh toán',
            'vnp_txn_ref' => $vnp_TxnRef,
        ]);

        $cartItems = CartItem::with(['product', 'variant'])->where('user_id', auth()->id())->get();
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id, // Nếu có biến thể
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);

            // Cập nhật tồn kho
            if ($item->variant) {
                $variant = $item->variant;

                if ($variant->stock_quantity < $item->quantity) {
                    return redirect()->route('cart.index')->with('error', 'Biến thể "' . $variant->name . '" không đủ số lượng trong kho.');
                }

                $variant->stock_quantity -= $item->quantity;
                $variant->sold_quantity += $item->quantity;

                if ($variant->stock_quantity <= 0) {
                    $variant->stock_quantity = 0;
                }

                $variant->save();
            } else {
                $product = $item->product;

                if ($product->stock_quantity < $item->quantity) {
                    return redirect()->route('cart.index')->with('error', 'Sản phẩm "' . $product->name . '" không đủ số lượng trong kho.');
                }

                $product->stock_quantity -= $item->quantity;
                $product->sold_quantity += $item->quantity;

                if ($product->stock_quantity <= 0) {
                    $product->stock_quantity = 0;
                }

                $product->save();
            }
        }

        // Kiểm tra xem đơn hàng đã được lưu chưa
        if (!$order) {
            Log::error('Failed to create order', $request->all());
            return response()->json(['success' => false, 'message' => 'Không thể tạo đơn hàng'], 500);
        }

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => "Thanh toan GD: $vnp_TxnRef",
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate,
        ];

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);

        $hashdata = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $paymentUrl = $vnp_Url . '?' . http_build_query($inputData) . '&vnp_SecureHash=' . $vnp_SecureHash;

        // Trả về JSON thay vì redirect
        return response()->json([
            'success' => true,
            'payment_url' => $paymentUrl
        ]);
    }

    public function response(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $inputData = $request->except(['vnp_SecureHash']);
        ksort($inputData);

        // Tạo hashdata từ response để kiểm tra chữ ký
        $hashdata = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        // Kiểm tra chữ ký
        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $isValidSignature = ($secureHash === $request->query('vnp_SecureHash'));



        return view('vnpay.response', [
            'data' => $request->all(),
            'isValidSignature' => $isValidSignature
        ]);
    }
    public function paymentReturn(Request $request)
    {
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $inputData = $request->except(['vnp_SecureHash']);
        ksort($inputData);


        $hashdata = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        // Kiểm tra chữ ký
        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $isValidSignature = ($secureHash === $request->query('vnp_SecureHash'));

        if ($isValidSignature) {
            $status = $request->query('vnp_ResponseCode') == '00' ? 'Thành công' : 'Thất bại';
            return view('vnpay.payment_return', [
                'status' => $status,
                'data' => $request->all(),
            ]);
        } else {
            return view('vnpay.payment_return', [
                'status' => 'Lỗi xác thực chữ ký',
                'data' => $request->all(),
            ]);
        }
    }
}
