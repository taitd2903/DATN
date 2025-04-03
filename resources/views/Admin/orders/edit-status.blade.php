@extends('layouts.layout')

@section('content')
    <h1>Chỉnh sửa trạng thái đơn hàng</h1>

    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" id="statusForm">
        @csrf
        @method('PUT')
    
        <div class="form-group">
            <label for="status">Trạng thái:</label>
            <select name="status" id="status" class="form-control">
                @foreach ($statusOptions as $status)
                    @if (!($order->payment_method == 'vnpay' && $status == 'Hủy')) 
                        <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('statusForm');
            const statusSelect = document.getElementById('status');
            const currentStatus = "{{ $order->status }}"; // Trạng thái hiện tại từ server

            // Định nghĩa thứ tự trạng thái
            const statusOrder = {
                'Chờ xác nhận': 1,
                'Đang giao': 2,
                'Hoàn thành': 3,
                'Hủy': 0
            };

            // Lưu trạng thái ban đầu
            const originalStatus = statusSelect.value;

            form.addEventListener('submit', function(e) {
                const selectedStatus = statusSelect.value;
                
                // Kiểm tra nếu trạng thái không thay đổi
                if (selectedStatus === currentStatus) {
                    return; // Cho phép submit nếu không thay đổi
                }

                const currentOrder = statusOrder[currentStatus] || 0;
                const newOrder = statusOrder[selectedStatus] || 0;

                // Kiểm tra khi chọn "Hoàn thành"
                if (selectedStatus === 'Hoàn thành') {
                    if (!confirm('Đơn hàng này đã hoàn thành. Bạn có chắc chắn muốn cập nhật?')) {
                        e.preventDefault();
                        statusSelect.value = originalStatus;
                        return;
                    }
                }

                // Logic kiểm tra thứ tự
                if (currentStatus === 'Chờ xác nhận') {
                    if (selectedStatus !== 'Đang giao' && selectedStatus !== 'Hủy') {
                        e.preventDefault();
                        alert('Từ "Chờ xác nhận" chỉ có thể chuyển sang "Đang giao" hoặc "Hủy"');
                        statusSelect.value = originalStatus;
                    }
                } else if (newOrder <= currentOrder || newOrder > currentOrder + 1) {
                    e.preventDefault();
                    alert('Trạng thái chỉ có thể thay đổi theo thứ tự: Chờ xác nhận -> Đang giao -> Hoàn thành');
                    statusSelect.value = originalStatus;
                }
            });

            // Optional: Hiển thị thông báo khi người dùng thay đổi select
            statusSelect.addEventListener('change', function() {
                const selectedStatus = this.value;
                const currentOrder = statusOrder[currentStatus] || 0;
                const newOrder = statusOrder[selectedStatus] || 0;

                if (selectedStatus === 'Hoàn thành' && currentStatus !== 'Hoàn thành') {
                    if (!confirm('Đơn hàng này sẽ được đánh dấu là hoàn thành. Bạn có chắc chắn không?')) {
                        this.value = currentStatus;
                        return;
                    }
                }

                if (currentStatus === 'Chờ xác nhận') {
                    if (selectedStatus !== 'Đang giao' && selectedStatus !== 'Hủy') {
                        alert('Từ "Chờ xác nhận" chỉ có thể chuyển sang "Đang giao" hoặc "Hủy"');
                        this.value = currentStatus;
                    }
                } else if (newOrder <= currentOrder || newOrder > currentOrder + 1) {
                    alert('Đơn hàng đã hoàn thành vui lòng không thay đổi trạng thái');
                    this.value = currentStatus;
                }
            });
        });
    </script>
@endsection