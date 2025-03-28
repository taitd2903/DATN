<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $appliedCoupons = $request->session()->get('applied_coupons', []);
        $couponCodes = array_column($appliedCoupons, 'code');
        $couponCodeString = !empty($couponCodes) ? implode(',', $couponCodes) : null;
        // Lưu đơn hàng vào bảng orders

        $order = Order::create([
            'customer_name' => $request->input('name'),
            'customer_phone' => $request->input('phone'),
            'customer_address' => $request->input('address'),
            'user_id' => Auth::id(),
            'note' => $note,
            'total_price' => $request->input('amount'),
            'coupon_code' => $couponCodeString,
            'payment_method' => 'vnpay',
            'status' => 'Chờ xác nhận',
            'payment_status' => 'Chưa thanh toán',
            'vnp_txn_ref' => $vnp_TxnRef,
        ]);

        $request->session()->put('pending_order_id', $order->id);

        $selectedItems = $request->items ? explode(',', $request->items) : [];

        // Lấy sản phẩm trong giỏ hàng chỉ của user hiện tại, lọc theo danh sách được chọn
        $cartItems = CartItem::with(['product', 'variant'])
            ->where('user_id', auth()->id())
            ->whereIn('id', $selectedItems) // Lọc theo ID sản phẩm được chọn
            ->get();
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id, // Nếu có biến thể
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }
       

        // Không xóa giỏ hàng ngay lập tức, chỉ xóa khi thanh toán thành công
        $request->session()->put('pending_order_id', $order->id);
        $request->session()->put('applied_coupons_for_vnpay', $appliedCoupons);
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

        $hashdata = urldecode(http_build_query($inputData));
        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $isValidSignature = ($secureHash === $request->query('vnp_SecureHash'));

        $txnRef = $request->query('vnp_TxnRef');
        $order = Order::where('vnp_txn_ref', $txnRef)->with('orderItems.product', 'orderItems.variant')->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }
        DB::transaction(function () use ($request, $order) {
            if ($request->query('vnp_ResponseCode') == '00') {
                // Thanh toán thành công, cập nhật trạng thái đơn hàng
                $order->update(['payment_status' => 'Đã thanh toán', 'status' => 'Chờ xác nhận']);
            
                // Xóa giỏ hàng của user sau khi thanh toán thành công
                CartItem::where('user_id', $order->user_id)->delete();

                // Trừ kho hàng
                foreach ($order->orderItems as $item) {
                    if ($item->variant) {
                        $item->variant->stock_quantity -= $item->quantity;
                        $item->variant->sold_quantity += $item->quantity;
                        $item->variant->save();
                    } else {
                        $item->product->stock_quantity -= $item->quantity;
                        $item->product->sold_quantity += $item->quantity;
                        $item->product->save();
                    }
                }
                // Xử lý tăng giảm mã giảm giá của Đạt
                if ($order->coupon_code) {
                    $couponCodes = explode(',', $order->coupon_code);
                    foreach ($couponCodes as $code) {
                        $code = trim($code);
                        if (!empty($code)) {
                            $coupon = Coupon::where('code', $code)->first();
                            if ($coupon) {
                                $coupon->used_count += 1;
                                $coupon->save();

                                CouponUsage::create([
                                    'user_id' => $order->user_id,
                                    'coupon_id' => $coupon->id,
                                    'order_id' => $order->id,
                                    'used_at' => now(),
                                ]);
                            }
                        }
                    }
                }
            } else {
                // Nếu giao dịch thất bại, hủy đơn hàng và hoàn kho
                $order->update(['payment_status' => 'Thất bại', 'status' => 'Hủy']);

                // foreach ($order->orderItems as $item) {
                //     if ($item->variant) {
                //         $item->variant->increment('stock_quantity', $item->quantity);
                //         $item->variant->decrement('sold_quantity', min($item->quantity, $item->variant->sold_quantity));
                //     } else {
                //         $item->product->increment('stock_quantity', $item->quantity);
                //         $item->product->decrement('sold_quantity', min($item->quantity, $item->product->sold_quantity));
                //     }
                // }
                // Back mã giảm giá của Đạt 
                // if ($order->coupon_code) {
                //     $couponCodes = explode(',', $order->coupon_code);
                //     foreach ($couponCodes as $code) {
                //         $code = trim($code);
                //         if (!empty($code)) {
                //             $coupon = Coupon::where('code', $code)->first();
                //             if ($coupon) {
                //                 $couponUsage = CouponUsage::where('order_id', $order->id)
                //                     ->where('user_id', $order->user_id)
                //                     ->where('coupon_id', $coupon->id)
                //                     ->first();
                //                 if ($couponUsage) {
                //                     $couponUsage->delete();
                //                     $coupon->used_count = max(0, $coupon->used_count - 1);
                //                     $coupon->save();
                //                 }
                //             }
                //         }
                //     }
                // }
            }
        });
        $request->session()->forget(['applied_coupons', 'applied_coupons_for_vnpay', 'discount', 'total_price']);
        if ($request->query('vnp_ResponseCode') !== '00') {
            foreach ($order->orderItems as $item) {
                // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
                $existingCartItem = CartItem::where('user_id', $order->user_id)
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->first();

                if ($existingCartItem) {
                    // Nếu đã có, tăng số lượng
                    $existingCartItem->increment('quantity', $item->quantity);
                } else {
                    // Nếu chưa có, thêm mới
                    CartItem::create([
                        'user_id' => $order->user_id,
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ]);
                }
            }
        }

        return view('Users.Checkout.invoice', [
            'order' => $order,
            'status' => $request->query('vnp_ResponseCode') == '00' ? 'Thành công' : 'Thất bại'
        ]);
    }
    public function updateStatus(Request $request, Order $order)
    {
        // Nếu đơn hàng thanh toán bằng VNPay thì không cho phép cập nhật thành "Hủy"
        if ($order->payment_method == 'vnpay' && $request->status == 'Hủy') {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng đã thanh toán bằng VNPay.');
        }

        $order->update(['status' => $request->status]);

        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật trạng thái thành công.');
    }

    public function continuePayment(Order $order, Request $request)
    {
        // Kiểm tra nếu đơn hàng chưa thanh toán
        if ($order->payment_status == 'Chưa thanh toán') {
            // Đưa lại sản phẩm vào giỏ hàng nếu bị xóa
            foreach ($order->orderItems as $item) {
                // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
                $existingCartItem = CartItem::where('user_id', $order->user_id)
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->first();
            
                if (!$existingCartItem) {
                    // Nếu chưa có trong giỏ hàng thì thêm vào
                    CartItem::create([
                        'user_id' => $order->user_id,
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ]);
                }
            }
            

            // Xóa session pending order để tránh trùng lặp
            $request->session()->forget('pending_order_id');

            // Chuyển hướng về trang thanh toán bình thường
            return redirect()->route('checkout');
        }

        return redirect()->route('home')->with('error', 'Đơn hàng này đã được xử lý.');
    }
}
