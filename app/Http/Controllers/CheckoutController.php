<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;

use App\Models\Discount;

use Order;

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

    // public function applyDiscount(Request $request)
    // {
    //     $discountCode = $request->input('discount_code');
    //     $discount = Discount::where('code', $discountCode)->first();

    //     if (!$discount) {
    //         return back()->with('error', 'Mã giảm giá không hợp lệ.');
    //     }

    //     session(['discount' => $discount->value]);
    //     return back()->with('success', 'Mã giảm giá đã được áp dụng.');
    // }
    public function showInvoice($orderId)
{
    $order = Order::findOrFail($orderId);
    return view('Users.Checkout.invoice', compact('order'));
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

    $note = "Tên: {$validated['name']}, SĐT: {$validated['phone']}, Email: " . ($validated['email'] ?? 'N/A') . ", "
          . "Địa chỉ: {$validated['address']}, {$validated['ward']}, {$validated['district']}, {$validated['city']}";

    $order = Order::create([
        'user_id' => auth()->id(),
        'note' => $note,
        'total_price' => $finalPrice,
        'payment_method' => strtolower($validated['payment_method']),
        'status' => 'pending',
    ]);

    CartItem::where('user_id', auth()->id())->delete();
    session()->forget('discount');

    return redirect()->route('checkout.invoice', ['order' => $order->id])
                     ->with('success', 'Đơn hàng đã được đặt thành công!');
}
}