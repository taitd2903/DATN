<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách ID sản phẩm được chọn từ query string (ví dụ: ?items=1,2,3)
        $selectedItems = $request->query('items') ? explode(',', $request->query('items')) : [];
        $items = $request->query('items');
        // Lấy sản phẩm trong giỏ hàng chỉ của user hiện tại, lọc theo danh sách được chọn
        $cartItems = CartItem::with(['product', 'variant'])
            ->where('user_id', auth()->id())
            ->whereIn('id', $selectedItems) // Lọc theo ID sản phẩm được chọn
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect('/cart')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $totalPrice = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $discount = session('discount', 0);
        $finalPrice = $totalPrice - $discount;

        // Lưu totalPrice vào session để sử dụng trong applyCoupon
        session(['total_price' => $totalPrice]);

        // Lấy thông tin người dùng nếu đã đăng nhập
        $user = auth()->user();

        return view('Users.Checkout.index', compact('cartItems', 'totalPrice', 'finalPrice', 'discount', 'user', 'items'));
    }


    public function placeOrder(Request $request)
    {
        //dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'payment_method' => 'required|in:cod,vnpay',
        ]);

        $selectedItems = $request->items ? explode(',', $request->items) : [];

        // Lấy sản phẩm trong giỏ hàng chỉ của user hiện tại, lọc theo danh sách được chọn
        $cartItems = CartItem::with(['product', 'variant'])
            ->where('user_id', auth()->id())
            ->whereIn('id', $selectedItems) // Lọc theo ID sản phẩm được chọn
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }

        $totalPrice = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $appliedCoupons = $request->session()->get('applied_coupons', []);
        $discount = 0;
        $finalPrice = $totalPrice;

        if ($appliedCoupons) {
            $validCoupons = [];
            foreach ($appliedCoupons as $couponData) {
                $coupon = Coupon::where('code', $couponData['code'])->first();
                if ($coupon && $this->isCouponValid($coupon, auth()->user())) {
                    $discountAmount = $this->calculateDiscount($coupon, $totalPrice);
                    $validCoupons[] = [
                        'code' => $coupon->code,
                        'discount_amount' => $discountAmount,
                    ];
                }
            }

            $discount = array_sum(array_column($validCoupons, 'discount_amount'));
            $finalPrice = $totalPrice - $discount;
            $request->session()->put('applied_coupons', $validCoupons);
            $appliedCoupons = $validCoupons;
        }

        if ($finalPrice <= 0) {
            return redirect()->route('cart.index')->with('error', 'Tổng giá trị đơn hàng không hợp lệ.');
        }

        $note = " {$validated['address']}, {$request->ward_name}, {$request->district_name}, {$request->province_name}";

        $couponCodes = array_column($appliedCoupons, 'code');
        $couponCodeString = !empty($couponCodes) ? implode(',', $couponCodes) : null;
        $order = Order::create([
            'user_id' => auth()->id(),
            'note' => $note,
            'total_price' => $finalPrice,
            'payment_method' => strtolower($validated['payment_method']),
            'status' => 'Chờ xác nhận',
            'customer_name' => $request->name,
            'customer_phone' => $request->phone,
            'customer_address' => $request->address,
            'payment_status' => "Chưa thanh toán",
            'coupon_code' => $couponCodeString,
        ]);

        // Lưu sản phẩm vào order_items và cập nhật kho hàng
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

        if ($appliedCoupons) {
            foreach ($appliedCoupons as $couponData) {
                $coupon = Coupon::where('code', $couponData['code'])->first();
                if ($coupon) {
                    $coupon->used_count += 1;
                    $coupon->save();

                    CouponUsage::create([
                        'user_id' => auth()->id(),
                        'coupon_id' => $coupon->id,
                        'order_id' => $order->id,
                        'used_at' => now(),
                    ]);
                }
            }
        }

        CartItem::where('user_id', auth()->id())->whereIn('id', $selectedItems)->delete();
        session()->forget(['discount', 'applied_coupons', 'total_price']);

        return redirect()->route('checkout.invoice', ['id' => $order->id])
            ->with('success', 'Đơn hàng đã được đặt thành công!');
    }

    // Hiển thị sang trang invoice
    public function invoice($id)
    {

        $order = Order::with(['orderItems.product', 'orderItems.variant', 'user'])->findOrFail($id);

        return view('Users.Checkout.invoice', compact('order'));
    }

    // Hiển thị danh sách đơn hàng
    public function orderList()
    {
        $orders = Order::oldest()->paginate(10); // Sắp xếp theo thời gian tạo đơn (cũ nhất trước)
        return view('admin.orders.index', compact('orders'));
    }

    // Trang chỉnh sửa trạng thái đơn hàng
    public function editStatus(Order $order)
    {
        $statusOptions = ['Chờ xác nhận', 'Đang giao', 'Hoàn thành', 'Hủy'];
        return view('admin.orders.edit-status', compact('order', 'statusOptions'));
    }

    // Cập nhật trạng thái qly đơn hàng trong admin
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Chờ xác nhận,Đang giao,Hoàn thành,Hủy',
        ]);

        // Nếu đơn hàng đã bị huỷ trước đó, không cho phép cập nhật nữa
        if ($order->status === 'Hủy') {
            return redirect()->route('admin.orders.index')->with('error', 'Đơn hàng đã bị huỷ và không thể cập nhật trạng thái!');
        }

        // Nếu chuyển trạng thái sang "Hủy", hoàn lại hàng vào kho
        if ($request->status === 'Hủy') {
            foreach ($order->orderItems as $item) {
                if ($item->variant) {
                    $variant = $item->variant;

                    // Hoàn kho: cộng lại stock_quantity, trừ sold_quantity
                    $variant->stock_quantity += $item->quantity;
                    $variant->sold_quantity -= $item->quantity;

                    // Đảm bảo sold_quantity không âm
                    if ($variant->sold_quantity < 0) {
                        $variant->sold_quantity = 0;
                    }

                    $variant->save();
                } else {
                    $product = $item->product;

                    // Hoàn kho: cộng lại stock_quantity, trừ sold_quantity
                    $product->stock_quantity += $item->quantity;
                    $product->sold_quantity -= $item->quantity;

                    // Đảm bảo sold_quantity không âm
                    if ($product->sold_quantity < 0) {
                        $product->sold_quantity = 0;
                    }

                    $product->save();
                }
            }
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
        }

        // Cập nhật trạng thái đơn hàng ship COD nếu mà hoàn thành thì chuyển sang thanh toán thành công
        
        if ($order->payment_method === 'cod' && $request->status === 'Hoàn thành') {
            $order->update([
                'status' => $request->status,
                'payment_status' => 'Đã thanh toán', 
            ]);
        } else {
            // Cập nhật trạng thái đơn hàng bình thường
            $order->update(['status' => $request->status]);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật trạng thái thành công!');
    }


    public function orderTracking()
    {
        $orders = Order::where('user_id', Auth::id())->with('orderItems.product')->get();

        return view('users.tracking.order_tracking', compact('orders'));
    }

    public function cancelOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền hủy đơn này!');
        }

        if ($order->status !== 'Chờ xác nhận') {
            return redirect()->back()->with('error', 'Đơn hàng đang vận chuyển, không thể hủy!');
        }
        Log::info('Coupon Code in cancelOrder:', [$order->coupon_code]);
        DB::transaction(function () use ($order) {
            // Khôi phục tồn kho
            foreach ($order->orderItems as $item) {
                $variant = $item->variant;
                if ($variant) {
                    $variant->stock_quantity += $item->quantity;
                    $variant->sold_quantity -= $item->quantity;
                    $variant->save();
                } else {
                    $product = $item->product;
                    if ($product) {
                        $product->stock_quantity += $item->quantity;
                        $product->sold_quantity -= $item->quantity;
                        $product->save();
                    }
                }
            }

            // Phần này của tao nhé Thắng
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
            $order->status = 'Hủy';
            $order->save();
        });

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    // ===================== Function này của Đạt, cấm động ====================== //
    public function applyCoupon(Request $request)
    {
        $couponCode = $request->input('coupon_code');
        $user = Auth::user();
        $totalPrice = $request->session()->get('total_price', 0);

        if (empty($couponCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa nhập mã giảm giá'
            ]);
        }

        $coupon = Coupon::where('code', $couponCode)->first();
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại'
            ]);
        }

        if ($coupon->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không hoạt động'
            ]);
        }

        $currentDate = now();
        if ($currentDate < $coupon->start_date || $currentDate > $coupon->end_date) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết hạn hoặc chưa có hiệu lực'
            ]);
        }

        if ($coupon->minimum_order_value && $totalPrice < $coupon->minimum_order_value) {
            return response()->json([
                'success' => false,
                'message' => "Đơn hàng cần tối thiểu " . number_format($coupon->minimum_order_value, 0, ',', '.') . " VNĐ để áp dụng mã này."
            ]);
        }

        if ($coupon->user_voucher_limit == 2) {
            if (!$coupon->users->contains($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không áp dụng cho bạn'
                ]);
            }
        } elseif ($coupon->user_voucher_limit == 3) {
            if ($user->gender != $coupon->gender) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không áp dụng cho bạn'
                ]);
            }
        }

        if ($coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết lượt sử dụng'
            ]);
        }

        $userUsageCount = CouponUsage::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->count();
        if ($userUsageCount >= $coupon->usage_per_user) {
            return response()->json([
                'success' => false,
                'message' => 'Đã hết lượt sử dụng mã'
            ]);
        }

        $appliedCoupons = $request->session()->get('applied_coupons', []);

        if (in_array($couponCode, array_column($appliedCoupons, 'code'))) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá này đã được áp dụng'
            ]);
        }

        $discountAmount = 0;
        if ($coupon->discount_type == 1) {
            $discountAmount = ($totalPrice * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else {
            $discountAmount = $coupon->discount_value;
        }


        if ($discountAmount > $totalPrice) {
            $discountAmount = $totalPrice;
        }

        $appliedCoupons[] = [
            'code' => $couponCode,
            'discount_amount' => $discountAmount,
        ];

        $totalDiscount = array_sum(array_column($appliedCoupons, 'discount_amount'));
        if ($totalDiscount > $totalPrice) {
            $totalDiscount = $totalPrice;
        }

        $request->session()->put('applied_coupons', $appliedCoupons);
        $request->session()->put('discount', $totalDiscount);

        $finalPrice = $totalPrice - $totalDiscount;

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount_amount' => $totalDiscount,
            'final_price' => $finalPrice,
            'applied_coupons' => $appliedCoupons,
        ]);
    }
    // ===================== Function này của Đạt, cấm động ====================== //
    public function removeCoupon(Request $request)
    {
        $couponCode = $request->input('coupon_code');
        $totalPrice = $request->session()->get('total_price', 0);

        $appliedCoupons = $request->session()->get('applied_coupons', []);

        $appliedCoupons = array_filter($appliedCoupons, function ($coupon) use ($couponCode) {
            return $coupon['code'] !== $couponCode;
        });


        $appliedCoupons = array_values($appliedCoupons);
        $request->session()->put('applied_coupons', $appliedCoupons);

        $totalDiscount = array_sum(array_column($appliedCoupons, 'discount_amount'));

        $finalPrice = $totalPrice - $totalDiscount;

        $request->session()->put('discount', $totalDiscount);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa mã giảm giá',
            'discount_amount' => $totalDiscount,
            'final_price' => $finalPrice,
            'applied_coupons' => $appliedCoupons,
        ]);
    }

    // ===================== Function này của Đạt, cấm động ====================== //
    private function isCouponValid(Coupon $coupon, $user)
    {
        $currentDate = now();

        if ($coupon->status != 1) {
            return false;
        }

        if ($currentDate < $coupon->start_date || $currentDate > $coupon->end_date) {
            return false;
        }

        if ($coupon->user_voucher_limit == 2) {
            if (!$coupon->users->contains($user->id)) {
                return false;
            }
        } elseif ($coupon->user_voucher_limit == 3) {
            if ($user->gender != $coupon->gender) {
                return false;
            }
        }

        if ($coupon->used_count >= $coupon->usage_limit) {
            return false;
        }

        $userUsageCount = CouponUsage::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->count();
        if ($userUsageCount >= $coupon->usage_per_user) {
            return false;
        }

        return true;
    }

    // ===================== Function này của Đạt, cấm động ====================== //
    public function getAppliedCoupons(Request $request)
    {
        $totalPrice = $request->session()->get('total_price', 0);
        $appliedCoupons = $request->session()->get('applied_coupons', []);
        $totalDiscount = $request->session()->get('discount', 0);
        $finalPrice = $totalPrice - $totalDiscount;

        return response()->json([
            'success' => true,
            'applied_coupons' => $appliedCoupons,
            'discount_amount' => $totalDiscount,
            'final_price' => $finalPrice,
            'total_price' => $totalPrice,
        ]);
    }

    // ===================== Function này của Đạt, cấm động ====================== //
    private function calculateDiscount(Coupon $coupon, $totalPrice)
    {
        $discountAmount = 0;
        if ($coupon->discount_type == 1) {
            $discountAmount = ($totalPrice * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else {
            $discountAmount = $coupon->discount_value;
        }

        return min($discountAmount, $totalPrice);
    }
}
