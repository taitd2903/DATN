<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with(['product', 'variant']) // Load quan hệ product và variant để tránh N+1
            ->get();

        return view('Users.Cart.index', compact('cartItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        // Kiểm tra số lượng tồn kho trước khi thêm vào giỏ hàng
        if ($variant->quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Số lượng sản phẩm không đủ.');
        }

        // Thêm hoặc cập nhật giỏ hàng
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            // Kiểm tra tồn kho trước khi tăng số lượng
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($variant->quantity < $newQuantity) {
                return redirect()->back()->with('error', 'Không đủ số lượng sản phẩm trong kho.');
            }

            // Nếu đã có, tăng số lượng
            $cartItem->increment('quantity', $request->quantity);
        } else {
            // Nếu chưa có, tạo mới
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'price' => $variant->price, // Lưu giá của biến thể
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng.');
    }

    public function remove($id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->where('id', $id)->first();
        if ($cartItem) {
            $cartItem->delete();
        }
        return redirect()->route('cart.index')->with('success', 'Sản phẩm đã bị xoá khỏi giỏ hàng.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('user_id', Auth::id())->where('id', $id)->first();

        if (!$cartItem) {
            return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng.');
        }

        if ($cartItem->variant->quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Số lượng sản phẩm không đủ.');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng thành công.');
    }
}
