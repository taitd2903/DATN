<?php
    namespace App\Http\Controllers\Admin\Returns;

    use App\Http\Controllers\Controller;
    use App\Models\OrderReturn;
    use App\Models\Order;
    use Illuminate\Http\Request;
    
    class AdminReturnController extends Controller
    {
        // public function index()
        // {
        //     // Hiển thị tất cả các yêu cầu hoàn hàng
        //     $returns = OrderReturn::all();
            
        //     return view('admin.returns.index', compact('returns'));
        // }
        public function index()
        {
            $returns = OrderReturn::with('order.user')->get();  // eager load user thông qua order
    
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
        // Nếu không tìm thấy đơn hàng
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
        // Nếu không có items trong đơn hàng
        return redirect()->route('admin.returns.index')->with('error', 'Đơn hàng không có sản phẩm.');
    }

    return redirect()->route('admin.returns.index')->with('success', 'Yêu cầu hoàn hàng đã được duyệt.');
}

    
        
        public function reject(Request $request, $id)
{
    $return = OrderReturn::findOrFail($id);

    // Kiểm tra nếu có lý do từ chối thì lưu vào cột rejection_reason
    $return->status = 'rejected'; // Cập nhật trạng thái là "rejected"
    $return->rejection_reason = $request->input('rejection_reason'); // Lưu lý do từ chối
    $return->save();

    // Thông báo và chuyển hướng lại
    return redirect()->route('admin.returns.index')->with('success', 'Yêu cầu hoàn hàng đã bị từ chối.');
}

        public function updateReturnProcess(Request $request, $id)
    {
        
        $return = OrderReturn::findOrFail($id);

        // Kiểm tra nếu đơn hoàn chưa được duyệt thì không cho cập nhật trạng thái hoàn hàng
        if ($return->status !== 'approved') {
            return redirect()->back()->with('error', 'Chỉ có thể cập nhật trạng thái khi đơn hoàn đã được duyệt.');
        }

        // Cập nhật trạng thái quá trình hoàn hàng
        $return->return_process_status = $request->return_process_status;
        $return->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái hoàn hàng thành công.');
    }

  

    }
    