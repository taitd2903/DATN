<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
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

        // Lấy thông tin người dùng nếu đã đăng nhập
        $user = auth()->user();

        // Lấy danh sách tỉnh/thành, quận/huyện, xã/phường


        return view('Users.Checkout.index', compact('cartItems', 'totalPrice', 'finalPrice', 'discount', 'user'));
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
        $discount = min(session('discount', 0), $totalPrice);
        $finalPrice = $totalPrice - $discount;

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
            'payment_status' => "Chưa thanh toán"
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

                // Nếu hết hàng, chỉ đặt stock_quantity = 0 (không dùng is_available)
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

                // Nếu hết hàng, chỉ đặt stock_quantity = 0 (không dùng is_available)
                if ($product->stock_quantity <= 0) {
                    $product->stock_quantity = 0;
                }

                $product->save();
            }
        }

        // Xóa giỏ hàng sau khi đặt hàng
        CartItem::where('user_id', auth()->id())->delete();
        session()->forget('discount');

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

            $order->status = 'Hủy';
            $order->save();
        });

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }







    //xoá ở quản lý đơn  hàng admin
    // public function destroy($id)
    // {
    //     $order = Order::findOrFail($id);
    //     $order->delete();

    //     return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được xóa thành công!');
    // }





}
