<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Phương thức này sẽ hiển thị danh sách các sản phẩm trong giỏ hàng của người dùng
    public function index()
    {
        // Lấy tất cả các sản phẩm trong giỏ hàng của người dùng hiện tại
        // 'user_id' được lấy từ ID của người dùng đang đăng nhập (Auth::id()).
        // Với mỗi CartItem, ta sẽ load các quan hệ 'product' và 'variant' để dễ dàng truy cập thông tin chi tiết về sản phẩm và biến thể của nó.
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with(['product', 'variant']) // Load quan hệ product và variant
            ->get();

        // Trả về view 'Users.Cart.index' và truyền dữ liệu $cartItems vào view để hiển thị giỏ hàng
        return view('Users.Cart.index', compact('cartItems'));
    }

    // Phương thức này dùng để thêm sản phẩm vào giỏ hàng
    public function add(Request $request)
    {
        // Kiểm tra tính hợp lệ của dữ liệu được gửi từ form
        // - 'product_id' phải có giá trị và tồn tại trong bảng 'products'
        // - 'variant_id' phải có giá trị và tồn tại trong bảng 'product_variants'
        // - 'quantity' phải là số nguyên và lớn hơn hoặc bằng 1
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Tìm kiếm biến thể sản phẩm (variant) theo ID
        $variant = ProductVariant::find($request->variant_id);

        // Nếu không tìm thấy biến thể, trả về thông báo lỗi
        if (!$variant) {
            return redirect()->back()->with('error', 'Biến thể sản phẩm không tồn tại.');
        }

        // Kiểm tra nếu số lượng sản phẩm trong kho không đủ để thêm vào giỏ hàng
        if ($variant->stock_quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Số lượng sản phẩm không đủ trong kho.');
        }

        // Kiểm tra nếu sản phẩm đã tồn tại trong giỏ hàng của người dùng
        $cartItem = CartItem::where([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'variant_id' => $request->variant_id
        ])->first();

        // Nếu sản phẩm đã có trong giỏ hàng, ta sẽ tăng số lượng lên thay vì thêm mới
        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            // Kiểm tra nếu số lượng mới vượt quá số lượng có sẵn trong kho
            if ($variant->stock_quantity < $newQuantity) {
                return redirect()->back()->with('error', 'Không đủ số lượng sản phẩm trong kho.');
            }
            // Tăng số lượng sản phẩm trong giỏ hàng
            $cartItem->increment('quantity', $request->quantity);
        } else {
            // Nếu sản phẩm chưa có trong giỏ, ta sẽ tạo mới một CartItem
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'price' => $variant->price, // Lưu giá của biến thể sản phẩm vào giỏ hàng
            ]);
        }

        // Sau khi thêm thành công, chuyển hướng người dùng về trang giỏ hàng và hiển thị thông báo thành công
        return redirect()->route('cart.index')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng.');
    }

    // Phương thức này dùng để xoá sản phẩm khỏi giỏ hàng
    public function remove($id)
    {
        // Tìm CartItem trong giỏ hàng của người dùng với id được cung cấp
        $cartItem = CartItem::where('user_id', Auth::id())->find($id);

        // Nếu tìm thấy CartItem, tiến hành xoá nó
        if ($cartItem) {
            $cartItem->delete();
            return redirect()->route('cart.index')->with('success', 'Sản phẩm đã bị xoá khỏi giỏ hàng.');
        }

        // Nếu không tìm thấy CartItem, trả về thông báo lỗi
        return redirect()->back()->with('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
    }

    // Phương thức này dùng để cập nhật số lượng sản phẩm trong giỏ hàng
    public function update(Request $request, $id)
    {
        // Kiểm tra tính hợp lệ của dữ liệu gửi lên (chỉ kiểm tra số lượng)
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Tìm CartItem trong giỏ hàng của người dùng theo id
        $cartItem = CartItem::where('user_id', Auth::id())->find($id);

        // Nếu không tìm thấy CartItem, trả về thông báo lỗi
        if (!$cartItem) {
            return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng.');
        }

        // Lấy thông tin biến thể của sản phẩm trong giỏ hàng
        $variant = $cartItem->variant;

        // Kiểm tra nếu biến thể không tồn tại hoặc số lượng trong kho không đủ
        if (!$variant || $variant->stock_quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Số lượng sản phẩm không đủ.');
        }

        // Cập nhật số lượng sản phẩm trong giỏ hàng
        $cartItem->update(['quantity' => $request->quantity]);

        // Sau khi cập nhật thành công, chuyển hướng người dùng về giỏ hàng với thông báo thành công
        return redirect()->route('cart.index')->with('success', 'Cập nhật giỏ hàng thành công.');
    }
}
