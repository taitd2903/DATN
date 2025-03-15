<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create($id)
    {
        $product = Product::findOrFail($id);
        return view('users.review.create', compact('product'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);

        Review::create([
            'product_id' => $id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'approved' => false, // Mặc định là chưa duyệt
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
