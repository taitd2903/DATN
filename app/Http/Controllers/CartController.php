<?php

namespace App\Http\Controllers;

use App\Events\PriceUpdated;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with(['product', 'variant'])
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

        $variant = ProductVariant::find($request->variant_id);
        if (!$variant) {
            return redirect()->back()->with('error', 'Biến thể sản phẩm không tồn tại.');
        }

        $cartItem = CartItem::where([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id
        ])->first();

        $currentQuantity = $cartItem ? $cartItem->quantity : 0;
        $newQuantity = $currentQuantity + $request->quantity;

        if ($variant->stock_quantity < $newQuantity) {
            return redirect()->back()->with('error', "Vui lòng kiểm tra lại số lượng tồn kho!");
        }

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'price' => $variant->price,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng.');
    }

    public function remove($id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->find($id);

        if ($cartItem) {
            $cartItem->delete();
            return response()->json([
                'success' => 'Sản phẩm đã bị xoá khỏi giỏ hàng.'
            ]);
        }

        return response()->json([
            'error' => 'Sản phẩm không tồn tại trong giỏ hàng.'
        ], 404);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('user_id', Auth::id())->find($id);

        if (!$cartItem) {
            return response()->json([
                'error' => 'Không tìm thấy sản phẩm trong giỏ hàng.'
            ], 404);
        }

        $variant = $cartItem->variant;

        if (!$variant || $variant->stock_quantity < $request->quantity) {
            return response()->json([
                'error' => 'Số lượng sản phẩm không đủ.'
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => 'Cập nhật giỏ hàng thành công.',
            'cartItem' => $cartItem
        ]);
    }
    public function checkStock()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with(['product', 'variant'])
            ->get();

        $stockData = [];
        foreach ($cartItems as $item) {
            $stockData[$item->id] = [
                'stock_quantity' => $item->variant->stock_quantity,
                'quantity' => $item->quantity,
                'product_name' => $item->product->name,
                'variant_size' => $item->variant->size,
                'variant_color' => $item->variant->color,
            ];

            if ($item->variant->stock_quantity > 0 && $item->quantity == 0) {
                $item->quantity = 1;
                $item->save();
                $stockData[$item->id]['quantity'] = 1;
            } elseif ($item->variant->stock_quantity < $item->quantity) {
                $item->quantity = $item->variant->stock_quantity;
                $item->save();
                $stockData[$item->id]['quantity'] = $item->variant->stock_quantity;
            }
        }

        return response()->json($stockData);
    }
    public function updatePrice($variantId, $newPrice)
    {
        $variant = ProductVariant::find($variantId);
        if (!$variant) {
            return redirect()->back()->with('error', 'Biến thể sản phẩm không tồn tại.');
        }
        $variant->price = $newPrice;
        $variant->save();

        // Cập nhật giá trong giỏ hàng và phát Event
        $cartItems = CartItem::where('variant_id', $variantId)->get();
        foreach ($cartItems as $cartItem) {
            $cartItem->price = $newPrice;
            $cartItem->save();
            event(new PriceUpdated($cartItem));
        }

        return redirect()->back()->with('success', 'Giá sản phẩm đã được cập nhật.');
    }
}
