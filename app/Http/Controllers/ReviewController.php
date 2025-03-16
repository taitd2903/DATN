<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function create($id)
    {
        $product = Product::findOrFail($id);
        return view('users.review.create', compact('product'));
    }

    // public function store(Request $request, $id)
    // {
    //     $request->validate([
    //         'rating' => 'required|integer|min:1|max:5',
    //         'comment' => 'required|string|max:500',
    //     ]);

    //     Review::create([
    //         'product_id' => $id,
    //         'user_id' => Auth::id(),
    //         'rating' => $request->rating,
    //         'comment' => $request->comment,
    //         'approved' => false, // Mặc định là chưa duyệt
    //     ]);

    //     return redirect()->back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt.');
    // }
    public function store(Request $request, $productId)
{
    $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|max:500',
    ]);

    $userId = auth()->id();

    // Lấy đơn hàng gần nhất mà user đã mua sản phẩm này
    $order = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.user_id', $userId)
        ->where('order_items.product_id', $productId)
        ->where('orders.status', 'Hoàn thành')
        ->orderBy('orders.created_at', 'desc') // Lấy đơn mới nhất
        ->select('orders.id')
        ->first();

    if (!$order) {
        return redirect()->back()->with('error', 'Bạn chưa mua sản phẩm này hoặc đơn hàng chưa hoàn thành.');
    }

    // Kiểm tra xem đã đánh giá đơn hàng này chưa
    $hasReviewed = Review::where('order_id', $order->id)
        ->where('user_id', $userId)
        ->where('product_id', $productId)
        ->exists();

    if ($hasReviewed) {
        return redirect()->back()->with('error', 'Bạn đã đánh giá sản phẩm này cho đơn hàng này rồi.');
    }

    // Debug: kiểm tra xem order_id có đúng không
    

    // Lưu đánh giá vào database
    Review::create([
        'product_id' => $productId,
        'user_id' => $userId,
        'order_id' => $order->id, // Lưu order_id vào review
        'rating' => $request->rating,
        'comment' => $request->comment,
        'approved' => false, // Mặc định chưa duyệt
    ]);

    return redirect()->back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt.');
}


    


    public function destroy($id)
{
    $review = Review::findOrFail($id);

    // Kiểm tra xem user có quyền xóa bình luận không
    if (auth()->id() !== $review->user_id) {
        return redirect()->back()->with('error', 'Bạn không có quyền xóa đánh giá này.');
    }

    $review->delete();

    return redirect()->back()->with('success', 'Đánh giá đã được xóa.');
}
}
