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

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with(['product', 'variant'])->get();

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

        return view('Users.Checkout.index', compact('cartItems', 'totalPrice', 'finalPrice', 'discount', 'user'));
    }

    public function applyCoupon(Request $request)
    {
        $couponCode = $request->input('coupon_code');
        $user = Auth::user();
        $totalPrice = $request->session()->get('total_price', 0); // Lấy totalPrice từ session

        // Kiểm tra mã có được nhập không
        if (empty($couponCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa nhập mã giảm giá'
            ]);
        }

        // Kiểm tra mã tồn tại
        $coupon = Coupon::where('code', $couponCode)->first();
        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại'
            ]);
        }

        // Kiểm tra trạng thái
        if ($coupon->status != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không hoạt động'
            ]);
        }

        // Kiểm tra ngày hiệu lực
        $currentDate = now();
        if ($currentDate < $coupon->start_date || $currentDate > $coupon->end_date) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết hạn hoặc chưa có hiệu lực'
            ]);
        }

        // Kiểm tra điều kiện áp dụng cho người dùng
        if ($coupon->user_voucher_limit == 2) { // Chỉ áp dụng cho người dùng cụ thể
            if (!$coupon->users->contains($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không áp dụng cho bạn'
                ]);
            }
        } elseif ($coupon->user_voucher_limit == 3) { // Theo giới tính
            if ($user->gender != $coupon->gender) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không áp dụng cho bạn'
                ]);
            }
        }

        // Kiểm tra số lần sử dụng tổng cộng
        if ($coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết lượt sử dụng'
            ]);
        }

        // Kiểm tra số lần sử dụng của người dùng
        $userUsageCount = CouponUsage::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->count();
        if ($userUsageCount >= $coupon->usage_per_user) {
            return response()->json([
                'success' => false,
                'message' => 'Đã hết lượt sử dụng mã'
            ]);
        }

        // Tính số tiền giảm
        $discountAmount = 0;
        if ($coupon->discount_type == 1) { // Phần trăm
            $discountAmount = ($totalPrice * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else { // Giá trị cố định
            $discountAmount = $coupon->discount_value;
        }

        // Đảm bảo số tiền giảm không vượt quá tổng tiền
        if ($discountAmount > $totalPrice) {
            $discountAmount = $totalPrice;
        }

        // Tính tổng tiền sau giảm
        $finalPrice = $totalPrice - $discountAmount;

        // Lưu mã giảm giá và discount vào session
        $request->session()->put('applied_coupon', $coupon->code);
        $request->session()->put('discount', $discountAmount);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice
        ]);
    }

    public function placeOrder(Request $request)
    {
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

        $cartItems = CartItem::with(['product', 'variant'])->where('user_id', auth()->id())->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }

        $totalPrice = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $appliedCouponCode = $request->session()->get('applied_coupon');
        $discount = 0;
        $finalPrice = $totalPrice;

        // Nếu có mã giảm giá, kiểm tra lại trước khi đặt hàng
        if ($appliedCouponCode) {
            $coupon = Coupon::where('code', $appliedCouponCode)->first();
            if (!$coupon || !$this->isCouponValid($coupon, auth()->user())) {
                return redirect()->back()->with('error', 'Mã giảm giá không còn hợp lệ. Vui lòng bỏ mã hoặc nhập mã mới.');
            }

            // Tính lại giá trị giảm để đảm bảo chính xác
            $discount = $this->calculateDiscount($coupon, $totalPrice);
            $finalPrice = $totalPrice - $discount;
        }

        if ($finalPrice <= 0) {
            return redirect()->route('cart.index')->with('error', 'Tổng giá trị đơn hàng không hợp lệ.');
        }

        $note = "Địa chỉ: {$validated['address']}, {$request->ward_name}, {$request->district_name}, {$request->province_name}";

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
            'coupon_code' => $appliedCouponCode ?? null
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

        // Nếu có mã giảm giá, tăng used_count và thêm vào coupon_usages
        if ($appliedCouponCode && $coupon) {
            $coupon->used_count += 1;
            $coupon->save();

            CouponUsage::create([
                'user_id' => auth()->id(),
                'coupon_id' => $coupon->id,
                'order_id' => $order->id,
                'used_at' => now(),
            ]);
        }

        // Xóa giỏ hàng và dữ liệu trong session sau khi đặt hàng
        CartItem::where('user_id', auth()->id())->delete();
        session()->forget(['discount', 'applied_coupon', 'total_price']);

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

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Chờ xác nhận,Đang giao,Hoàn thành,Hủy',
        ]);

        $order->update(['status' => $request->status]);

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

        DB::transaction(function () use ($order) {
            // Khôi phục tồn kho
            foreach ($order->orderItems as $item) {
                $variant = $item->variant;
                if ($variant) {
                    $variant->stock_quantity += $item->quantity;
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

            // Xóa bản ghi trong coupon_usages và giảm used_count nếu có mã giảm giá
            if ($order->coupon_code) {
                $coupon = Coupon::where('code', $order->coupon_code)->first();
                if ($coupon) {
                    $couponUsage = CouponUsage::where('order_id', $order->id)
                        ->where('user_id', Auth::id())
                        ->where('coupon_id', $coupon->id)
                        ->first();
                    if ($couponUsage) {
                        $couponUsage->delete();
                        $coupon->used_count -= 1;
                        $coupon->save();
                    }
                }
            }

            $order->status = 'Hủy';
            $order->save();
        });

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    // Helper: Kiểm tra mã giảm giá có hợp lệ không
    private function isCouponValid(Coupon $coupon, $user)
    {
        $currentDate = now();

        // Kiểm tra trạng thái
        if ($coupon->status != 1) {
            return false;
        }

        // Kiểm tra ngày hiệu lực
        if ($currentDate < $coupon->start_date || $currentDate > $coupon->end_date) {
            return false;
        }

        // Kiểm tra điều kiện áp dụng cho người dùng
        if ($coupon->user_voucher_limit == 2) {
            if (!$coupon->users->contains($user->id)) {
                return false;
            }
        } elseif ($coupon->user_voucher_limit == 3) {
            if ($user->gender != $coupon->gender) {
                return false;
            }
        }

        // Kiểm tra số lần sử dụng tổng cộng
        if ($coupon->used_count >= $coupon->usage_limit) {
            return false;
        }

        // Kiểm tra số lần sử dụng của người dùng
        $userUsageCount = CouponUsage::where('user_id', $user->id)
            ->where('coupon_id', $coupon->id)
            ->count();
        if ($userUsageCount >= $coupon->usage_per_user) {
            return false;
        }

        return true;
    }

    // Helper: Tính số tiền giảm giá
    private function calculateDiscount(Coupon $coupon, $totalPrice)
    {
        $discountAmount = 0;
        if ($coupon->discount_type == 1) { // Phần trăm
            $discountAmount = ($totalPrice * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else { // Giá trị cố định
            $discountAmount = $coupon->discount_value;
        }

        return min($discountAmount, $totalPrice);
    }
}