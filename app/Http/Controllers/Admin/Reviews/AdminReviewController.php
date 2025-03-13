<?php
namespace App\Http\Controllers\Admin\Reviews;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::where('approved', false)->with('product', 'user')->get();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->approved = true;
        $review->save();

        return redirect()->route('admin.reviews.index')->with('success', 'Đánh giá đã được duyệt!');
    }

    public function approved()
{
    $reviews = Review::where('approved', true)->with('product', 'user')->get();
    return view('admin.reviews.approved', compact('reviews'));
}

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Đánh giá đã bị xóa!');
    }
}

?>