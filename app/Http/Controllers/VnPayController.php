<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
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
        $vnp_Amount = $request->input('amount') * 100; // Amount gửi cho VNPay
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

        // Lấy danh sách sản phẩm từ giỏ hàng
        $selectedItems = $request->items ? explode(',', $request->items) : [];
        $cartItems = CartItem::with(['product', 'variant'])
            ->where('user_id', auth()->id())
            ->whereIn('id', $selectedItems)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng trống'], 400);
        }
    
//=============================Quang Đạt đã để lại dấu răng ở đây (START)===========================//
        $totalPrice = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $appliedCoupons = $request->session()->get('applied_coupons', []);
        if ($appliedCoupons) {
            foreach ($appliedCoupons as $couponData) {
                $coupon = Coupon::where('code', $couponData['code'])->first();
                if (!$coupon) {
                    return response()->json([
                        'success' => false,
                        'message' => "Mã giảm giá {$couponData['code']} không còn tồn tại."
                    ], 400);
                }
                $checkoutController = new CheckoutController();
                if (!$checkoutController->isCouponValid($coupon, auth()->user())) {
                    return response()->json([
                        'success' => false,
                        'message' => "Mã giảm giá {$couponData['code']} không hiệu lực hoặc đã có sự thay đổi."
                    ], 400);
                }
                $baseAmount = ($coupon->discount_target == 'shipping_fee') ? 30000 : $totalPrice;
                $currentDiscount = $this->calculateDiscount($coupon, $baseAmount);
                if ($currentDiscount != $couponData['discount_amount']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Mã giảm giá {$couponData['code']} đã có sự thay đổi. Vui lòng áp dụng lại mã."
                    ], 400);
                }
                if ($coupon->minimum_order_value && $totalPrice < $coupon->minimum_order_value) {
                    return response()->json([
                        'success' => false,
                        'message' => "Mã giảm giá {$couponData['code']} yêu cầu đơn hàng tối thiểu " . number_format($coupon->minimum_order_value, 0, ',', '.') . " VNĐ. Tổng đơn hàng hiện tại không đủ điều kiện."
                    ], 400);
                }
            }
        }
    //=============================Quang Đạt đã để lại dấu răng ở đây (END)===========================//
        // Tính toán giá trị
        $totalPrice = $cartItems->sum(fn($item) => $item->price * $item->quantity); // Tổng tiền gốc
        $shippingFee = 30000;
        $appliedCoupons = $request->session()->get('applied_coupons', []);

        $discountOrder = 0;
        $discountShipping = 0;

        if ($appliedCoupons) {
            $validCoupons = [];
            foreach ($appliedCoupons as $couponData) {
                $coupon = Coupon::where('code', $couponData['code'])->first();
                if (!$coupon) {
                    continue; // Bỏ qua nếu mã không tồn tại
                }
                $baseAmount = ($coupon->discount_target == 'shipping_fee') ? $shippingFee : $totalPrice;
                $discountAmount = $this->calculateDiscount($coupon, $baseAmount);

                if ($coupon->minimum_order_value && $totalPrice < $coupon->minimum_order_value) {
                    continue;
                }

                if ($coupon->discount_target == 'shipping_fee') {
                    $discountShipping += $discountAmount;
                } else {
                    $discountOrder += $discountAmount;
                }

                $validCoupons[] = [
                    'code' => $coupon->code,
                    'discount_amount' => $discountAmount,
                    'discount_target' => $coupon->discount_target,
                ];
            }

            $discountOrder = min($discountOrder, $totalPrice);
            $discountShipping = min($discountShipping, $shippingFee);

            $totalDiscount = $discountOrder + $discountShipping;
            $maxDiscount = $totalPrice + $shippingFee;
            if ($totalDiscount > $maxDiscount) {
                $totalDiscount = $maxDiscount;
                if ($discountOrder > $totalPrice) {
                    $discountOrder = $totalPrice;
                    $discountShipping = $totalDiscount - $discountOrder;
                } else {
                    $discountShipping = $totalDiscount - $discountOrder;
                }
            }

            $finalPrice = $totalPrice + $shippingFee - $totalDiscount;
            if ($finalPrice < 0) {
                $finalPrice = 0;
            }

            $request->session()->put('applied_coupons', $validCoupons);
        } else {
            $totalDiscount = 0;
            $finalPrice = $totalPrice + $shippingFee;
        }

        // Tính discount_amount
        $discountAmount = $discountOrder + $discountShipping;

        $couponCodes = array_column($appliedCoupons, 'code');
        $couponCodeString = !empty($couponCodes) ? implode(',', $couponCodes) : null;

        // Lưu đơn hàng
        $order = Order::create([
            'customer_name' => $request->input('name'),
            'customer_phone' => $request->input('phone'),
            'customer_address' => $request->input('address'),
            'user_id' => Auth::id(),
            'note' => $note,
            'total_price' => $finalPrice,
            'discount_amount' => $discountAmount, 
            
            'coupon_code' => $couponCodeString,
            'payment_method' => 'vnpay',
            'status' => 'Chờ xác nhận',
            'payment_status' => 'Chưa thanh toán',
            'vnp_txn_ref' => $vnp_TxnRef,
            'city' => $request->input('city'),
            'district' => $request->input('district'),
            'ward' => $request->input('ward'),
        ]);

        // Lưu OrderItems
        foreach ($cartItems as $item) {
            $variant = ProductVariant::find($item->variant_id);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'size' => $variant->size ?? null,
                'color' => $variant->color ?? null,
                'original_price' => $item->original_price ?? null,
            ]);
        }

        $request->session()->put('pending_order_id', $order->id);
        $request->session()->put('applied_coupons_for_vnpay', $appliedCoupons);

        if (!$order) {
            Log::error('Failed to create order', $request->all());
            return response()->json(['success' => false, 'message' => 'Không thể tạo đơn hàng'], 500);
        }

        // Tạo URL thanh toán VNPay
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

        return response()->json([
            'success' => true,
            'payment_url' => $paymentUrl
        ]);
    }
    private function calculateDiscount(Coupon $coupon, $totalPrice)
    {
        $discountAmount = 0;
        if ($coupon->discount_type == 1) { // Giảm theo phần trăm
            $discountAmount = ($totalPrice * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else { // Giảm cố định
            $discountAmount = $coupon->discount_value;
        }

        return min($discountAmount, $totalPrice);
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
        $order = Order::where('vnp_txn_ref', $txnRef)
            ->with('orderItems.product', 'orderItems.variant')
            ->first();

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        DB::transaction(function () use ($request, $order) {
            if ($request->query('vnp_ResponseCode') == '00') {
                // Thanh toán thành công, cập nhật trạng thái đơn hàng
                $order->update(['payment_status' => 'Đã thanh toán', 'status' => 'Chờ xác nhận']);

                // Xóa tất cả các đơn hàng "Chưa thanh toán" khác của user này
                Order::where('user_id', $order->user_id)
                    ->where('payment_status', 'Chưa thanh toán')
                    ->where('id', '<>', $order->id) // Không xóa đơn hàng hiện tại
                    ->delete();

                // Xóa giỏ hàng của user sau khi thanh toán thành công
                CartItem::where('user_id', $order->user_id)->delete();

                // Trừ kho hàng
                foreach ($order->orderItems as $item) {
                    if ($item->variant) {
                        $item->variant->decrement('stock_quantity', $item->quantity);
                        $item->variant->increment('sold_quantity', $item->quantity);
                        $item->variant->save();
                    } else {
                        $item->product->decrement('stock_quantity', $item->quantity);
                        $item->product->increment('sold_quantity', $item->quantity);
                        $item->product->save();
                    }
                }

                $appliedCoupons = $request->session()->get('applied_coupons_for_vnpay', []);
            if ($order->coupon_code && $appliedCoupons) {
                $couponCodes = explode(',', $order->coupon_code);
                foreach ($couponCodes as $index => $code) {
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
                                'applied_discount' => $appliedCoupons[$index]['discount_amount'] ?? 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
            } else {
                // Nếu thanh toán thất bại, cập nhật trạng thái đơn hàng
                $order->update(['payment_status' => 'Thất bại', 'status' => 'Hủy']);


                // Hoàn lại mã giảm giá nếu có
                if ($order->coupon_code) {
                    $couponCodes = explode(',', $order->coupon_code);
                    foreach ($couponCodes as $code) {
                        $code = trim($code);
                        if (!empty($code)) {
                            $coupon = Coupon::where('code', $code)->first();
                            if ($coupon) {
                                $couponUsage = CouponUsage::where('order_id', $order->id)
                                    ->where('user_id', $order->user_id)
                                    ->where('coupon_id', $coupon->id)
                                    ->first();
                                if ($couponUsage) {
                                    $couponUsage->delete();
                                    $coupon->used_count = max(0, $coupon->used_count - 1);
                                    $coupon->save();
                                }
                            }
                        }
                    }
                }

                // Trả sản phẩm về giỏ hàng nếu thanh toán thất bại
                foreach ($order->orderItems as $item) {
                    $existingCartItem = CartItem::where('user_id', $order->user_id)
                        ->where('product_id', $item->product_id)
                        ->where('variant_id', $item->variant_id)
                        ->first();

                    if ($existingCartItem) {
                        $existingCartItem->increment('quantity', $item->quantity);
                    } else {
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
        });
        // Xóa session mã giảm giá
        $request->session()->forget(['applied_coupons', 'applied_coupons_for_vnpay', 'discount', 'total_price']);
        $appliedCoupons = $request->session()->get('applied_coupons_for_vnpay', []);
        return view('Users.Checkout.invoice', [
            'order' => $order,
            'status' => $request->query('vnp_ResponseCode') == '00' ? 'Thành công' : 'Thất bại',
            'appliedCoupons' => $appliedCoupons,
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
        if (session()->has('continue_payment_' . $order->id)) {
            return redirect()->route('checkout')->with('error', 'Bạn đã tiếp tục thanh toán đơn hàng này.');
        }

        session()->put('continue_payment_' . $order->id, true);

        if ($order->payment_status == 'Chưa thanh toán') {
            foreach ($order->orderItems as $item) {
                $existingCartItem = CartItem::where('user_id', $order->user_id)
                    ->where('product_id', $item->product_id)
                    ->where('variant_id', $item->variant_id)
                    ->first();

                if (!$existingCartItem) {
                    CartItem::create([
                        'user_id' => $order->user_id,
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ]);
                }
            }
            return redirect()->route('checkout');
        }
        return redirect()->route('home')->with('error', 'Đơn hàng này đã được xử lý.');
    }
}
