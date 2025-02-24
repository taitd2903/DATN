<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Discount;
use App\Models\City;
use App\Models\District;
use App\Models\Ward;

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
        $cities = City::all();
        $districts = District::where('city_id', $user->city ?? null)->get();
        $wards = Ward::where('district_id', $user->district ?? null)->get();

        return view('Users.Checkout.index', compact('cartItems', 'totalPrice', 'finalPrice', 'discount', 'user', 'cities', 'districts', 'wards'));
    }

    public function applyDiscount(Request $request)
    {
        $discountCode = $request->input('discount_code');
        $discount = Discount::where('code', $discountCode)->first();

        if (!$discount) {
            return back()->with('error', 'Mã giảm giá không hợp lệ.');
        }

        session(['discount' => $discount->value]);
        return back()->with('success', 'Mã giảm giá đã được áp dụng.');
    }

    public function placeOrder(Request $request)
    {
        $cartItems = CartItem::with(['product', 'variant'])->get();
        if ($cartItems->isEmpty()) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống.');
        }

        $totalPrice = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $discount = session('discount', 0);
        $finalPrice = $totalPrice - $discount;

        $order = Order::create([
            'user_id' => auth()->id(),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'district' => $request->input('district'),
            'ward' => $request->input('ward'),
            'total_price' => $finalPrice,
            'payment_method' => $request->input('payment_method'),
            'status' => 'pending',
        ]);

        // Xóa giỏ hàng sau khi đặt hàng
        CartItem::where('user_id', auth()->id())->delete();
        session()->forget('discount');

        return redirect()->route('order.success')->with('success', 'Đơn hàng đã được đặt thành công!');
    }
}
