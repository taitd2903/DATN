<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function create($id)
    {
        $product = Product::findOrFail($id);
        $reviews = Review::where('product_id', $id)->where('approved', true)->with('user')->get();
        return view('users.review.create', compact('product', 'reviews'));
    }

    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'video' => 'nullable|mimetypes:video/mp4|max:5120',
        ]);

        $userId = auth()->id();

        // Tìm đơn hàng hoàn thành chứa sản phẩm chưa được đánh giá
        $order = Order::where('user_id', $userId)
            ->where('status', 'Hoàn thành')
            ->whereHas('orderItems', function ($query) use ($productId) {
                $query->where('product_id', $productId)
                      ->where('has_reviewed', 0);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$order) {
            Log::info('No valid order found', [
                'user_id' => $userId,
                'product_id' => $productId,
                'status' => 'Hoàn thành',
            ]);
            return redirect()->back()->with('error', 'Bạn chưa mua sản phẩm này, đơn hàng chưa hoàn thành, hoặc đã đánh giá.');
        }

        // Xử lý ảnh
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews/images', 'public');
                $imagePaths[] = $path;
            }
        }

        // Xử lý video
        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('reviews/videos', 'public');
        }

        // Lưu đánh giá
        Review::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'approved' => false,
            'images' => !empty($imagePaths) ? json_encode($imagePaths) : null,
            'video' => $videoPath,
        ]);

        // Đánh dấu đã đánh giá (set has_reviewed = 1)
        OrderItem::where('order_id', $order->id)
            ->where('product_id', $productId)
            ->update(['has_reviewed' => true]);

        return redirect()->back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // Kiểm tra quyền xóa
        if (auth()->id() !== $review->user_id) {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa đánh giá này.');
        }

        $review->delete(); // Hard delete

        return redirect()->back()->with('success', 'Đánh giá đã được xóa.');
    }
}