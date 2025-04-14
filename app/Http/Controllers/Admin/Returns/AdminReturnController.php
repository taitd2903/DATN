<?php

namespace App\Http\Controllers\Admin\Returns;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminReturnController extends Controller
{
    public function index()
    {
        $returns = OrderReturn::with('order.user')->get();
        return view('admin.returns.index', compact('returns'));
    }

    public function approve($id)
    {
        $return = OrderReturn::findOrFail($id);

        $return->status = 'approved';
        $return->save();

        // Cập nhật trạng thái đơn hàng
        $order = Order::find($return->order_id);

        if (!$order) {
            return redirect()->route('admin.returns.index')->with('error', 'Đơn hàng không tồn tại.');
        }

        $order->status = 'Hoàn thành';
        $order->save();

        // Cập nhật kho hàng nếu có items
        if ($order->items && $order->items->count() > 0) {
            foreach ($order->items as $item) {
                $product = $item->product;
                $product->increment('stock', $item->quantity);
                $product->decrement('sold', $item->quantity);
            }
        } else {
            return redirect()->route('admin.returns.index')->with('error', 'Đơn hàng không có sản phẩm.');
        }

        return redirect()->route('admin.returns.index')->with('success', 'Yêu cầu hoàn hàng đã được duyệt.');
    }

    public function reject(Request $request, $id)
    {
        $return = OrderReturn::findOrFail($id);

        $return->status = 'rejected';
        $return->rejection_reason = $request->input('rejection_reason');
        $return->save();

        return redirect()->route('admin.returns.index')->with('success', 'Yêu cầu hoàn hàng đã bị từ chối.');
    }

    public function updateReturnProcess(Request $request, $id)
    {
        $return = OrderReturn::findOrFail($id);

        if ($return->status !== 'approved') {
            return redirect()->back()->with('error', 'Chỉ có thể cập nhật trạng thái khi đơn hoàn đã được duyệt.');
        }

        $return->return_process_status = $request->return_process_status;
        $return->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái hoàn hàng thành công.');
    }

    public function refunded(Request $request, $id)
    {
        $return = OrderReturn::findOrFail($id);

        // Kiểm tra điều kiện để hoàn tiền
        if ($return->status !== 'approved' || $return->return_process_status !== 'return_completed') {
            return redirect()->route('admin.returns.index')->with('error', 'Không thể hoàn tiền cho yêu cầu này.');
        }

        if ($return->refunded_at) {
            return redirect()->route('admin.returns.index')->with('error', 'Yêu cầu này đã được hoàn tiền.');
        }

        // Validate input
        $request->validate([
            'refund_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'refund_note' => 'nullable|string|max:500',
        ]);

        // Lưu ảnh minh chứng
        $refundImagePath = $request->file('refund_image')->store('refund_images', 'public');

        // Cập nhật thông tin hoàn tiền
        $return->update([
            'refund_image' => $refundImagePath,
            'refund_note' => $request->refund_note,
            'refunded_at' => now(),
        ]);

        // Cập nhật trạng thái đơn hàng (tùy chọn, nếu cần)
        $order = Order::find($return->order_id);
        if ($order) {
            $order->status = 'Hoàn thành';
            $order->save();
        }

        return redirect()->route('admin.returns.index')->with('success', 'Hoàn tiền thành công.');
    }
}