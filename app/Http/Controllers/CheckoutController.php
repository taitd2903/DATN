<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
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
    //XỬ LÝ USER
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

        $checkoutPrices = [];
        foreach ($cartItems as $item) {
            $checkoutPrices[$item->id] = $item->price;
        }
        session(['checkout_prices' => $checkoutPrices]);

        //============================24/03==========================
        $currentCartHash = md5(serialize($cartItems->pluck('id')->toArray()));
        $previousCartHash = session('cart_hash');
        if ($previousCartHash && $previousCartHash !== $currentCartHash) {
            session()->forget('applied_coupons');
            session()->forget('discount_order');
            session()->forget('discount_shipping');
        }
        session(['cart_hash' => $currentCartHash]);
        //============================24/03==========================
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
        // Validate dữ liệu
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

        // Lấy sản phẩm trong giỏ hàng
        $cartItems = CartItem::with(['product', 'variant'])
            ->where('user_id', auth()->id())
            ->whereIn('id', $selectedItems)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }
        //=============================Quang Đạt đã để lại dấu răng ở đây (START)===========================//
        $totalPrice = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $appliedCoupons = $request->session()->get('applied_coupons', []);
        if ($appliedCoupons) {
            foreach ($appliedCoupons as $couponData) {
                $coupon = Coupon::where('code', $couponData['code'])->first();
                if (!$coupon || !$this->isCouponValid($coupon, auth()->user())) {
                    return redirect()->route('checkout', ['items' => $request->items])
                        ->with('error', "Mã giảm giá {$couponData['code']} không còn hiệu lực hoặc đã có sự thay đổi. Vui lòng kiểm tra lại.");
                }
                $baseAmount = ($coupon->discount_target == 'shipping_fee') ? 30000 : $totalPrice;
                $currentDiscount = $this->calculateDiscount($coupon, $baseAmount);
                if ($currentDiscount != $couponData['discount_amount']) {
                    return redirect()->route('checkout', ['items' => $request->items])
                        ->with('error', "Mã giảm giá {$couponData['code']} đã có sự thay đổi. Vui lòng áp dụng lại mã.");
                }
                if ($coupon->minimum_order_value && $totalPrice < $coupon->minimum_order_value) {
                    return redirect()->route('checkout', ['items' => $request->items])
                        ->with('error', "Mã giảm giá {$couponData['code']} yêu cầu đơn hàng tối thiểu " . number_format($coupon->minimum_order_value, 0, ',', '.') . " VNĐ. Tổng đơn hàng hiện tại không đủ điều kiện.");
                }
            }
        }
        //=============================Quang Đạt đã để lại dấu răng ở đây (END)===========================//
        // Kiểm tra giá thay đổi
        $checkoutPrices = session('checkout_prices', []);
        $priceChanged = false;
        $changedItems = [];
        foreach ($cartItems as $item) {
            if (isset($checkoutPrices[$item->id]) && $checkoutPrices[$item->id] != $item->price) {
                $priceChanged = true;
                $changedItems[] = $item->product->name;
                break;
            }
        }

        if ($priceChanged) {
            $message = 'Giá của sản phẩm "' . implode(', ', $changedItems) . '" đã thay đổi. Vui lòng kiểm tra lại giỏ hàng.';
            return redirect()->route('cart.index')->with('error', $message);
        }

        session()->forget('checkout_prices');

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
                if (!$coupon || $coupon->is_delete || !$this->isCouponValid($coupon, auth()->user())) {
                    return redirect()->route('checkout', ['items' => $request->items])
                        ->with('error', "Mã giảm giá {$couponData['code']} không còn hiệu lực.");
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
                $totalDiscount = $totalPrice + $shippingFee;
                $discountShipping = min($discountShipping, $shippingFee);
                $discountOrder = $totalDiscount - $discountShipping;
            }

            $request->session()->put('applied_coupons', $validCoupons);
        } else {
            $totalDiscount = 0;
            $finalPrice = $totalPrice + $shippingFee;
        }

        // Tính tổng số tiền giảm giá
        $discountAmount = $discountOrder + $discountShipping;

        $note = " {$validated['address']}, {$request->ward_name}, {$request->district_name}, {$request->province_name}";
        $couponCodes = array_column($appliedCoupons, 'code');
        $couponCodeString = !empty($couponCodes) ? implode(',', $couponCodes) : null;

        // Tạo đơn hàng với discount_amount
        foreach ($cartItems as $item) {
            $variant = ProductVariant::find($item->variant_id);
            if ($variant->stock_quantity < $item->quantity) {
                return redirect()->route('cart.index')->with('error', 'Sản phẩm "' . $item->product->name . '"mua thất bại hãy kiểm tra lại.'); 
            } 
        }
        $order = Order::create([

            'user_id' => auth()->id(),
            'note' => $note,
            'total_price' => $finalPrice,
            'discount_amount' => $discountAmount,
            'payment_method' => strtolower($validated['payment_method']),
            'status' => 'Chờ xác nhận',
            'customer_name' => $request->name,
            'customer_phone' => $request->phone,
            'customer_address' => $request->address,
            'payment_status' => "Chưa thanh toán",
            'coupon_code' => $couponCodeString,
            'city' => $request->city,
            'district' => $request->district,
            'ward' => $request->ward,
        ]);
        // Lưu sản phẩm vào order_items và cập nhật kho hàng
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
                'original_price' => $variant->original_price ?? null,
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

        // Cập nhật mã giảm giá
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
                        'applied_discount' => $couponData['discount_amount'],
                    ]);
                }
            }
        }

        // Xóa các mục trong giỏ hàng và session
        CartItem::where('user_id', auth()->id())->whereIn('id', $selectedItems)->delete();
        session()->forget(['discount', 'applied_coupons', 'total_price', 'discount_order', 'discount_shipping', 'checkout_prices']);

        return redirect()->route('checkout.invoice', ['id' => $order->id])
            ->with('success', 'Đơn hàng đã được đặt thành công!');
    }

    // Hiển thị sang trang invoice
    public function invoice($id)
    {
        $order = Order::findOrFail($id);
    
        // Nếu trạng thái là 'Đã giao hàng thành công'
        if ($order->status === 'Đã giao hàng thành công') {
            if ($order->complete_ship && Carbon::parse($order->complete_ship)->addDays(7)->lte(now())) {
                $order->status = 'Hoàn thành';
                $order->completed_at = now();
                $order->completed_by = $order->completed_by ?? Auth::id();
                $order->save();

                session()->flash('success', 'Đơn hàng đã tự động được xác nhận sau 7 ngày.');
            }
            //ngon
        }
        if (
            $order->payment_method === 'vnpay'
            && $order->payment_status === 'Chưa thanh toán'
            && $order->status !== 'Hủy' // ✅ Tránh xử lý lại
            && Carbon::parse($order->created_at)->addMinutes(30)->lte(now())
        ) {
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
        
            $order->status = 'Hủy'; // ✅ Đảm bảo 'Hủy' nằm trong ENUM
        
            $order->save();
        
            session()->flash('info', 'Đơn hàng vnpay đã bị hủy do quá thời gian thanh toán.');
        }
        
        // Lấy lại order đầy đủ với quan hệ
        $order = Order::with([
            'orderItems.product',
            'orderItems.variant',
            'user',
            'couponUsages.coupon'
        ])->findOrFail($id);

        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Đơn hàng', 'url' => null],
            ['name' => 'Đơn hàng ' . $order->id, 'url' => null],
        ];

        return view('Users.Checkout.invoice', compact('breadcrumbs', 'order'));
    }

    public function orderTracking()
    {
        $userId = Auth::id();
        $orders = Order::where('user_id', $userId)->get();

        foreach ($orders as $order) {
            if (
                $order->status === 'Đã giao hàng thành công' &&
                $order->complete_ship &&
                Carbon::parse($order->complete_ship)->addDays(7)->lte(now())
            ) {
                $order->status = 'Hoàn thành';
                $order->completed_at = now();
                $order->completed_by = $order->completed_by ?? $userId;
                $order->save();
            }

            if (
                $order->payment_method === 'vnpay'
                && $order->payment_status === 'Chưa thanh toán'
                && $order->status !== 'Hủy' 
                && Carbon::parse($order->created_at)->addMinutes(30)->lte(now())
            ) {
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
            
                $order->status = 'Hủy'; // ✅ Đảm bảo 'Hủy' nằm trong ENUM
            
                $order->save();
            
                session()->flash('info', 'Đơn hàng vnpay đã bị hủy do quá thời gian thanh toán.');
            }


        }


        $orders = Order::where('user_id', $userId)
            ->with('orderItems.product')
            ->orderBy('created_at', 'desc')
            ->get();

        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Đơn hàng', 'url' => null],
            ['name' => 'Đơn hàng của tôi', 'url' => null],
        ];

        return view('users.tracking.order_tracking', compact('breadcrumbs', 'orders'));
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

    //XỬ LÝ ADMIN
    // Hiển thị danh sách đơn hàng
    public function orderList(Request $request)
    {

        $ordersToCheck = Order::where('status', 'Đã giao hàng thành công')->get();

        foreach ($ordersToCheck as $order) {
            if (
                $order->complete_ship &&
                Carbon::parse($order->complete_ship)->addDays(7)->lte(now())
            ) {
                $order->status = 'Hoàn thành';
                $order->completed_at = now();
                $order->completed_by = $order->completed_by ?? $order->user_id; // ✅ Ghi theo người đặt hàng
                $order->save();
            }

          
        }

        $query = Order::orderBy('created_at', 'desc');

        // Lọc theo tên khách hàng
        if ($request->filled('name')) {
            $query->where('customer_name', 'like', '%' . $request->name . '%');
        }

        // Lọc theo số điện thoại
        if ($request->filled('phone')) {
            $query->where('customer_phone', 'like', '%' . $request->phone . '%');
        }

        // Lọc theo ngày đặt hàng
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Lọc theo trạng thái đơn hàng
        if ($request->filled('order_status')) {
            $query->where('status', $request->order_status);
        }
        $orders = $query->paginate(10);
        $statusOptions = ['Chờ xác nhận', 'Đang giao','Đã giao hàng thành công', 'Hoàn thành', 'Hủy'];
        $ordersvnpay= Order::get();
        foreach ($ordersvnpay as $order) {
        if (
            $order->payment_method === 'vnpay'
            && $order->payment_status === 'Chưa thanh toán'
            && $order->status !== 'Hủy' 
            && Carbon::parse($order->created_at)->addMinutes(30)->lte(now())
        ) {
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
        
            $order->status = 'Hủy'; // ✅ Đảm bảo 'Hủy' nằm trong ENUM
        
            $order->save();
        
            session()->flash('info', 'Đơn hàng vnpay đã bị hủy do quá thời gian thanh toán.');
        }
    }


        return view('admin.orders.index', compact('orders', 'statusOptions'));
    }


    // Cập nhật trạng thái qly đơn hàng trong admin
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Chờ xác nhận,Đang giao,Đã giao hàng thành công,Hoàn thành,Hủy',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Không cho phép cập nhật nếu đơn hàng đã bị huỷ trước đó
        if ($oldStatus === 'Hủy') {
            return redirect()->route('admin.orders.index')->with('error', 'Đơn hàng đã bị huỷ và không thể cập nhật trạng thái!');
        }
        if ($newStatus === 'Đã giao hàng thành công') {
            $order->complete_ship = now();
            $order->save();
        }
        // Nếu chuyển trạng thái sang "Hủy", hoàn lại hàng vào kho
        if ($newStatus === 'Hủy') {
            foreach ($order->orderItems as $item) {
                if ($item->variant) {
                    $variant = $item->variant;
                    $variant->stock_quantity += $item->quantity;
                    $variant->sold_quantity = max(0, $variant->sold_quantity - $item->quantity);
                    $variant->save();
                } else {
                    $product = $item->product;
                    $product->stock_quantity += $item->quantity;
                    $product->sold_quantity = max(0, $product->sold_quantity - $item->quantity);
                    $product->save();
                }
            }

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
        }

        // Nếu đơn hàng là ship COD và Đã giao hàng thành công, cập nhật trạng thái thanh toán
        if ($order->payment_method === 'cod' && $newStatus === 'Đã giao hàng thành công') {
            $order->payment_status = 'Đã thanh toán';
            $order->complete_ship = now();
        }

        // Cập nhật trạng thái mới
        $order->status = $newStatus;
        $order->status_updated_at = now();
        $order->status_updated_by = Auth::id();

        // Nếu chuyển sang "Đang giao" và chưa có thời gian giao hàng
        if ($newStatus === 'Đang giao' && !$order->delivering_at) {
            $order->delivering_at = now();
            $order->delivering_by = Auth::id();
        }

        // Nếu chuyển sang "Hoàn thành" và chưa có thời gian hoàn thành
        if ($newStatus === 'Hoàn thành' && !$order->completed_at) {
            $order->completed_at = now();
            $order->completed_by = Auth::id();
        }

        $order->save();

        return redirect()
            ->route('admin.orders.index', ['page' => $request->input('page')])
            ->with('success', 'Cập nhật trạng thái thành công!');
    }



    public function show(Order $order)
    {
        if (in_array($order->status ,[ 'Đã giao hàng thành công','Từ chối hoàn hàng'])) {
            if (Carbon::parse($order->complete_ship)->addDays(7)->lte(now())) {
                $order->status = 'Hoàn thành';
                $order->completed_at = now();
                $order->completed_by = $order->completed_by ?? Auth::id();
                $order->save();
    
                return redirect()->back()->with('success', 'Đơn hàng đã tự động được xác nhận sau 7 ngày.');
            }
            if (
                $order->payment_method === 'vnpay'
                && $order->payment_status === 'Chưa thanh toán'
                && $order->status !== 'Hủy' // ✅ Tránh xử lý lại
                && Carbon::parse($order->created_at)->addMinutes(30)->lte(now())
            ) {
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
            
                $order->status = 'Hủy'; // ✅ Đảm bảo 'Hủy' nằm trong ENUM
            
                $order->save();
            
                session()->flash('info', 'Đơn hàng vnpay đã bị hủy do quá thời gian thanh toán.');
            }
            
    
      
    }        return view('admin.orders.show', compact('order'));

}


    // ===================== Function này của Đạt, cấm động ====================== //
    public function applyCoupon(Request $request)
    {
        $couponCode = $request->input('coupon_code');
        $user = Auth::user();
        $totalPrice = $request->session()->get('total_price', 0);
        $shippingFee = 30000;

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

        if ($coupon->is_delete) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không còn hiệu lực'
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
                'message' => 'Mã giảm giá chưa có hiệu lực'
            ]);
        }

        if ($coupon->minimum_order_value && $totalPrice < $coupon->minimum_order_value) {
            return response()->json([
                'success' => false,
                'message' => "Đơn hàng cần tối thiểu " . number_format($coupon->minimum_order_value, 0, ',', '.') . " VNĐ để áp dụng mã này."
            ]);
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

        if ($coupon->user_voucher_limit == 2 && !$coupon->users->contains($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không áp dụng cho bạn'
            ]);
        } elseif ($coupon->user_voucher_limit == 3 && $user->gender != $coupon->gender) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không áp dụng cho bạn'
            ]);
        }

        $appliedCoupons = $request->session()->get('applied_coupons', []);

        if (in_array($couponCode, array_column($appliedCoupons, 'code'))) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá này đã được áp dụng'
            ]);
        }

        $orderTotalCount = count(array_filter($appliedCoupons, fn($c) => $c['discount_target'] == 'order_total'));
        $shippingFeeCount = count(array_filter($appliedCoupons, fn($c) => $c['discount_target'] == 'shipping_fee'));
        if ($coupon->discount_target == 'order_total' && $orderTotalCount >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể áp dụng tối đa 1 mã giảm giá trị đơn hàng.'
            ]);
        }
        if ($coupon->discount_target == 'shipping_fee' && $shippingFeeCount >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể áp dụng tối đa 1 mã giảm phí vận chuyển.'
            ]);
        }

        $totalDiscountOrder = array_sum(array_filter(
            array_column($appliedCoupons, 'discount_amount'),
            fn($item) => $appliedCoupons[array_search($item, array_column($appliedCoupons, 'discount_amount'))]['discount_target'] == 'order_total'
        ));
        $totalDiscountShipping = array_sum(array_filter(
            array_column($appliedCoupons, 'discount_amount'),
            fn($item) => $appliedCoupons[array_search($item, array_column($appliedCoupons, 'discount_amount'))]['discount_target'] == 'shipping_fee'
        ));

        if ($coupon->discount_target == 'order_total' && $totalDiscountOrder >= $totalPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng đã được giảm tối đa!'
            ]);
        }
        if ($coupon->discount_target == 'shipping_fee' && $totalDiscountShipping >= $shippingFee) {
            return response()->json([
                'success' => false,
                'message' => 'Phí vận chuyển đã được giảm tối đa!'
            ]);
        }

        $baseAmount = ($coupon->discount_target == 'shipping_fee') ? $shippingFee : $totalPrice;
        $discountAmount = 0;
        if ($coupon->discount_type == 1) {
            $discountAmount = ($baseAmount * $coupon->discount_value) / 100;
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else {
            $discountAmount = $coupon->discount_value;
        }
        $discountAmount = min($discountAmount, $baseAmount);

        // Thêm mã mới vào danh sách áp dụng
        $appliedCoupons[] = [
            'code' => $couponCode,
            'discount_amount' => $discountAmount,
            'discount_target' => $coupon->discount_target,
        ];

        // Cập nhật lại tổng giảm giá sau khi thêm mã mới
        $totalDiscountOrder = array_sum(array_filter(
            array_column($appliedCoupons, 'discount_amount'),
            fn($item) => $appliedCoupons[array_search($item, array_column($appliedCoupons, 'discount_amount'))]['discount_target'] == 'order_total'
        ));
        $totalDiscountShipping = array_sum(array_filter(
            array_column($appliedCoupons, 'discount_amount'),
            fn($item) => $appliedCoupons[array_search($item, array_column($appliedCoupons, 'discount_amount'))]['discount_target'] == 'shipping_fee'
        ));

        $totalDiscountOrder = min($totalDiscountOrder, $totalPrice);
        $totalDiscountShipping = min($totalDiscountShipping, $shippingFee);

        $totalDiscount = $totalDiscountOrder + $totalDiscountShipping;
        $maxDiscount = $totalPrice + $shippingFee;
        $isMaxDiscountReached = false;
        if ($totalDiscount > $maxDiscount) {
            $totalDiscount = $maxDiscount;
            $isMaxDiscountReached = true;
            if ($totalDiscountOrder > $totalPrice) {
                $totalDiscountOrder = $totalPrice;
                $totalDiscountShipping = $totalDiscount - $totalDiscountOrder;
            } else {
                $totalDiscountShipping = $totalDiscount - $totalDiscountOrder;
            }
        }

        $finalPrice = $totalPrice + $shippingFee - $totalDiscount;
        if ($finalPrice < 0) {
            $finalPrice = 0;
            $totalDiscount = $totalPrice + $shippingFee;
            $totalDiscountShipping = min($totalDiscountShipping, $shippingFee);
            $totalDiscountOrder = $totalDiscount - $totalDiscountShipping;
            $isMaxDiscountReached = true;
        }

        $request->session()->put('applied_coupons', $appliedCoupons);
        $request->session()->put('discount_order', $totalDiscountOrder);
        $request->session()->put('discount_shipping', $totalDiscountShipping);

        $response = [
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount_order' => $totalDiscountOrder,
            'discount_shipping' => $totalDiscountShipping,
            'final_price' => $finalPrice,
            'applied_coupons' => $appliedCoupons,
        ];

        if ($isMaxDiscountReached) {
            $response['message'] = 'Đã đạt mức giảm tối đa cho đơn hàng!';
        }

        return response()->json($response);
    }
    // ===================== Function này của Đạt, cấm động ====================== //
    public function removeCoupon(Request $request)
    {
        $couponCode = $request->input('coupon_code');
        $totalPrice = $request->session()->get('total_price', 0);
        $shippingFee = 30000;

        $appliedCoupons = $request->session()->get('applied_coupons', []);
        $appliedCoupons = array_filter($appliedCoupons, fn($coupon) => $coupon['code'] !== $couponCode);
        $appliedCoupons = array_values($appliedCoupons);

        $totalDiscountOrder = array_sum(array_filter(
            array_column($appliedCoupons, 'discount_amount'),
            fn($item) => $appliedCoupons[array_search($item, array_column($appliedCoupons, 'discount_amount'))]['discount_target'] == 'order_total'
        ));
        $totalDiscountShipping = array_sum(array_filter(
            array_column($appliedCoupons, 'discount_amount'),
            fn($item) => $appliedCoupons[array_search($item, array_column($appliedCoupons, 'discount_amount'))]['discount_target'] == 'shipping_fee'
        ));

        $totalDiscountOrder = min($totalDiscountOrder, $totalPrice);
        $totalDiscountShipping = min($totalDiscountShipping, $shippingFee);

        $totalDiscount = $totalDiscountOrder + $totalDiscountShipping;
        $maxDiscount = $totalPrice + $shippingFee;
        if ($totalDiscount > $maxDiscount) {
            $totalDiscount = $maxDiscount;
            if ($totalDiscountOrder > $totalPrice) {
                $totalDiscountOrder = $totalPrice;
                $totalDiscountShipping = $totalDiscount - $totalDiscountOrder;
            } else {
                $totalDiscountShipping = $totalDiscount - $totalDiscountOrder;
            }
        }

        $finalPrice = $totalPrice + $shippingFee - $totalDiscount;
        if ($finalPrice < 0) {
            $finalPrice = 0;
            $totalDiscount = $totalPrice + $shippingFee;
            $totalDiscountShipping = min($totalDiscountShipping, $shippingFee);
            $totalDiscountOrder = $totalDiscount - $totalDiscountShipping;
        }

        $request->session()->put('applied_coupons', $appliedCoupons);
        $request->session()->put('discount_order', $totalDiscountOrder);
        $request->session()->put('discount_shipping', $totalDiscountShipping);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa mã giảm giá',
            'discount_order' => $totalDiscountOrder,
            'discount_shipping' => $totalDiscountShipping,
            'final_price' => $finalPrice,
            'applied_coupons' => $appliedCoupons,
        ]);
    }

    // ===================== Function này của Đạt, cấm động ====================== //
    public function isCouponValid(Coupon $coupon, $user)
    {
        $currentDate = now();

        if ($coupon->is_delete) {
            return false;
        }

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
        $discountOrder = $request->session()->get('discount_order', 0);
        $discountShipping = $request->session()->get('discount_shipping', 0);
        $shippingFee = 30000;
        $finalPrice = $totalPrice + $shippingFee - $discountOrder - $discountShipping;

        return response()->json([
            'success' => true,
            'applied_coupons' => $appliedCoupons,
            'discount_order' => $discountOrder,
            'discount_shipping' => $discountShipping,
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
    public function getAvailableCoupons(Request $request)
{
    $user = Auth::user();
    $totalPrice = $request->session()->get('total_price', 0);
    $shippingFee = 30000;

    $coupons = Coupon::where('status', 1)
        ->where('is_delete', 0)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->where('used_count', '<', DB::raw('usage_limit'))
        ->get();

    $availableCoupons = [];
    foreach ($coupons as $coupon) {
        if (!$this->isCouponValid($coupon, $user)) {
            continue;
        }
        if ($coupon->minimum_order_value && $totalPrice < $coupon->minimum_order_value) {
            continue;
        }

        $baseAmount = ($coupon->discount_target == 'shipping_fee') ? $shippingFee : $totalPrice;
        $discountAmount = $this->calculateDiscount($coupon, $baseAmount);

        $discountText = '';
        if ($coupon->discount_target == 'shipping_fee') {
            $discountText = number_format($discountAmount, 0, ',', '.') . ' VNĐ phí vận chuyển';
        } else {
            if ($coupon->discount_type == 1) {
                $discountText = $coupon->discount_value . '% giá trị đơn hàng';
                if ($coupon->max_discount_amount) {
                    $discountText .= ' (tối đa ' . number_format($coupon->max_discount_amount, 0, ',', '.') . ' VNĐ)';
                }
            } else {
                $discountText = number_format($discountAmount, 0, ',', '.') . ' VNĐ giá trị đơn hàng';
            }
        }

        $availableCoupons[] = [
            'code' => $coupon->code,
            'discount_text' => $discountText,
        ];
    }

    return response()->json([
        'success' => true,
        'coupons' => $availableCoupons,
    ]);
}


public function confirmReceived($id) 
{
    $order = Order::findOrFail($id);
    if (in_array($order->status ,[ 'Đã giao hàng thành công','Từ chối hoàn hàng'])) {
        if (Carbon::parse($order->complete_ship)->addDays(7)->lte(now())) {
            $order->status = 'Hoàn thành';
            $order->completed_at = now();
            $order->completed_by = $order->completed_by ?? Auth::id();
            $order->save();

            return redirect()->back()->with('success', 'Đơn hàng đã tự động được xác nhận sau 7 ngày.');
        }
        if (
            $order->payment_method === 'vnpay'
            && $order->payment_status === 'Chưa thanh toán'
            && $order->status !== 'Hủy' // ✅ Tránh xử lý lại
            && Carbon::parse($order->created_at)->addMinutes(30)->lte(now())
        ) {
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
        
            $order->status = 'Hủy'; // ✅ Đảm bảo 'Hủy' nằm trong ENUM
        
            $order->save();
        
            session()->flash('info', 'Đơn hàng vnpay đã bị hủy do quá thời gian thanh toán.');
        }
        
        $order->status = 'Hoàn thành';
        $order->completed_at = now();
        $order->completed_by = Auth::id();
        $order->save();

        return redirect()->back()->with('success', 'Bạn đã xác nhận đã nhận hàng. Cảm ơn bạn!');
    }

    return redirect()->back()->with('error', 'Không thể xác nhận đơn hàng này.');
}

}
