<?php

namespace App\Http\Controllers;

use App\Models\OrderReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderReturnController extends Controller
{
    // Hiển thị danh sách yêu cầu hoàn hàng của người dùng
    public function index()
    {
        $returns = OrderReturn::where('user_id', Auth::id())->latest()->get();
        return view('users.returns.index', compact('returns'));
    }

    // Hiển thị form yêu cầu hoàn hàng
    public function create($order_id)
    {
        // Kiểm tra xem đơn hàng có tồn tại và thuộc về người dùng hiện tại không
        $order = Order::where('id', $order_id)->where('user_id', Auth::id())->where('status', 'Đã giao hàng thành công')->first();

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Bạn không thể yêu cầu hoàn hàng cho đơn hàng này.');
        }

        return view('users.returns.create', compact('order_id'));
    }

    // Xử lý yêu cầu hoàn hàng
   

    public function store(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'reason' => 'required|string|max:500',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Kiểm tra xem đơn hàng có thuộc về người dùng và đã hoàn thành chưa
    $order = Order::where('id', $request->order_id)
                  ->where('user_id', Auth::id())
                  ->where('status', 'Đã giao hàng thành công')
                  ->first();

    if (!$order) {
        return redirect()->route('orders.index')->with('error', 'Bạn không thể yêu cầu hoàn hàng cho đơn hàng này.');
    }

    // Kiểm tra xem người dùng đã yêu cầu hoàn hàng cho đơn hàng này với trạng thái 'pending' hoặc 'completed' chưa
    $existingReturnRequest = OrderReturn::where('order_id', $request->order_id)
                                        ->where('user_id', Auth::id())
                                        ->whereIn('status', ['pending', 'completed'])
                                        ->first();

    if ($existingReturnRequest) {
        return redirect()->route('returns.index')->with('error', 'Bạn đã yêu cầu hoàn hàng cho đơn hàng này rồi.');
    }

    // Lưu ảnh nếu có
    $imagePath = $request->file('image') ? $request->file('image')->store('returns', 'public') : null;

    // Tạo yêu cầu hoàn hàng mới với trạng thái 'pending'
    $orderReturn = OrderReturn::create([
        'order_id' => $request->order_id,
        'user_id' => Auth::id(),
        'reason' => $request->reason,
        'image' => $imagePath,
        'status' => 'pending',  // Trạng thái 'pending' khi yêu cầu hoàn hàng được tạo
    ]);

    // Cập nhật trạng thái của đơn hàng trong bảng 'orders'
    $order->update([
        'return_request_status' => 'completed',  // Đổi trạng thái thành 'completed'
    ]);

    return redirect()->route('returns.index')->with('success', 'Yêu cầu hoàn hàng đã được gửi.');
}



    public function show($id)
    {
        // Lấy yêu cầu hoàn hàng theo ID
        $return = OrderReturn::findOrFail($id);

        // Kiểm tra xem người dùng hiện tại có phải là chủ đơn hàng không
        if ($return->user_id !== Auth::id()) {
            return redirect()->route('returns.index')->with('error', 'Bạn không thể xem yêu cầu hoàn hàng này.');
        }

        // Trả về view hiển thị chi tiết yêu cầu hoàn hàng
        return view('users.returns.show', compact('return'));
    }
}
