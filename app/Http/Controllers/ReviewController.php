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

    
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Mỗi ảnh tối đa 2MB
            'video' => 'nullable|mimetypes:video/mp4|max:5120', // Video tối đa 5MB
        ]);
    
        $userId = auth()->id();
    
        $order = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $userId)
            ->where('order_items.product_id', $productId)
            ->where('orders.status', 'Hoàn thành')
            ->orderBy('orders.created_at', 'desc')
            ->select('orders.id')
            ->first();
    
        if (!$order) {
            return redirect()->back()->with('error', 'Bạn chưa mua sản phẩm này hoặc đơn hàng chưa hoàn thành.');
        }
    
        $hasReviewed = Review::where('order_id', $order->id)
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    
        if ($hasReviewed) {
            return redirect()->back()->with('error', 'Bạn đã đánh giá sản phẩm này cho đơn hàng này rồi.');
        }
    
        // Xử lý ảnh
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews/images', 'public'); // Lưu vào storage/app/public/reviews/images
                $imagePaths[] = $path;
            }
        }
    
        // Xử lý video
        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('reviews/videos', 'public'); // Lưu vào storage/app/public/reviews/videos
        }
    
        // Lưu đánh giá vào database
        Review::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'approved' => false,
            'images' => json_encode($imagePaths), // Lưu danh sách ảnh dưới dạng JSON
            'video' => $videoPath, // Lưu đường dẫn video
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
