@extends('layouts.layout')

@section('content')
    <h1 class="text-center">Quản lý đơn hàng</h1>

    <!-- Form lọc -->
    <form id="filterForm" action="{{ route('admin.orders.index') }}" method="GET" class="filter-container mb-2 w-100 mx-auto bg-white shadow-sm rounded sticky-top p-2">
        <!-- Row for input fields -->
        <div class="row align-items-end g-2">
            <!-- Tên khách hàng -->
            <div class="col-md-2 col-6">
                <label for="nameFilter" class="form-label small">Tên khách hàng</label>
                <input type="text" id="nameFilter" name="name" class="form-control form-control-sm" placeholder="Tên khách hàng" value="{{ request('name') }}">
            </div>
            <!-- Số điện thoại -->
            <div class="col-md-2 col-6">
                <label for="phoneFilter" class="form-label small">Số điện thoại</label>
                <input type="text" id="phoneFilter" name="phone" class="form-control form-control-sm" placeholder="Số điện thoại" value="{{ request('phone') }}">
            </div>
            <!-- Từ ngày -->
            <div class="col-md-2 col-6">
                <label for="startDateFilter" class="form-label small">Từ ngày</label>
                <input type="date" id="startDateFilter" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>
            <!-- Đến ngày -->
            <div class="col-md-2 col-6">
                <label for="endDateFilter" class="form-label small">Đến ngày</label>
                <input type="date" id="endDateFilter" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
            </div>
            <!-- Trạng thái thanh toán -->
            <div class="col-md-2 col-6">
                <label for="paymentStatusFilter" class="form-label small">Trạng thái thanh toán</label>
                <select id="paymentStatusFilter" name="payment_status" class="form-control form-control-sm">
                    <option value="">Tất cả</option>
                    <option value="Chưa thanh toán" {{ request('payment_status') === 'Chưa thanh toán' ? 'selected' : '' }}>Chưa thanh toán</option>
                    <option value="Đã thanh toán" {{ request('payment_status') === 'Đã thanh toán' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="Thất bại" {{ request('payment_status') === 'Thất bại' ? 'selected' : '' }}>Thất bại</option>
                </select>
            </div>
            <!-- Trạng thái đơn hàng -->
            <div class="col-md-2 col-6">
                <label for="orderStatusFilter" class="form-label small">Trạng thái đơn hàng</label>
                <select id="orderStatusFilter" name="order_status" class="form-control form-control-sm">
                    <option value="">Tất cả</option>
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status }}" {{ request('order_status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- Row for buttons -->
        <div class="row mt-2">
            <div class="col-12 d-flex justify-content-end">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3">Lọc</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm px-3">Trở lại</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Table đơn hàng -->
    <table class="table table-bordered mt-3" id="orderTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Thông tin người đặt</th>
                <th>Ngày đặt hàng</th>
                <th>Thanh toán</th>
                <th>Trạng thái thanh toán</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $index => $order)
                <tr>
                    <td>{{ $orders->firstItem() + $index }}</td>
                    <td class="customer_info" data-name="{{ $order->customer_name }}"
                        data-phone="{{ $order->customer_phone }}">
                        {{ $order->customer_name }} <br> SĐT: {{ $order->customer_phone }}
                    </td>
                    <td class="order_date" data-date="{{ $order->created_at->format('Y-m-d') }}">
                        {{ $order->created_at->format('d/m/Y') }}</td>
                    <td class="payment_method">
                        @if ($order->payment_method == 'cod')
                            COD
                        @elseif ($order->payment_method == 'vnpay')
                            VNPAY
                        @else
                            Khác
                        @endif
                    </td>
                    <td class="payment_status">{{ $order->payment_status }}</td>
                    <td>{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                    <td class="order_status" data-status="{{ $order->status }}">
                        {{ $order->status }}
                    </td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem</a>
                        @if ($order->status === 'Đã giao hàng thành công')
                            <button class="btn btn-success btn-sm" disabled>Đã giao hàng thành công</button>
                        @elseif ($order->status === 'Hủy')
                            <button class="btn btn-secondary btn-sm" disabled>Đã hủy</button>
                            @elseif($order->status === 'Đã hoàn hàng')
                            <button class="btn btn-success btn-sm" disabled>Đã hoàn hàng</button>
                            
                        @else
                        @if($order->status === 'Hoàn thành')
                        <button class="btn btn-success btn-sm" disabled>Đơn hàng đã hoàn thành</button>
                            @else

                          
                            <div class="update-container">
                                <button class="btn btn-warning btn-sm update-status-btn">Cập nhật</button>
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST"
                                    class="status-form" style="display: none;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="page" value="{{ request('page') }}">
                                    <select name="status" class="form-control status-select mt-2"
                                        onchange="this.form.submit()">
                                        @foreach ($statusOptions as $status)
                                            @if ($status !== 'Hoàn thành')
                                                <option value="{{ $status }}"
                                                    {{ $order->status === $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @endif
                        
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $orders->appends(request()->query())->links() }}
    <!-- Script xử lý cập nhật trạng thái -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const allowedTransitions = {
                'Chờ xác nhận': ['Đang giao', 'Hủy'],
                'Đang giao': ['Đã giao hàng thành công'],
            };

            // Hiển thị form cập nhật khi click "Cập nhật"
            document.querySelectorAll('.update-status-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const container = this.closest('.update-container');
                    const row = this.closest('tr');
                    const currentStatus = row.querySelector('.order_status').dataset.status;

                    if (currentStatus === 'Đã giao hàng thành công') return;
                    if (currentStatus === 'Hủy') {
                        alert('Không thể thay đổi trạng thái đơn hàng đã hủy.');
                        return;
                    }

                    this.style.display = 'none';
                    container.querySelector('.status-form').style.display = 'block';
                });
            });

            // Xử lý khi thay đổi select trạng thái
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function(e) {
                    const form = this.closest('form');
                    const row = this.closest('tr');
                    const currentStatus = row.querySelector('.order_status').dataset.status;
                    const selectedStatus = this.value;

                    // Nếu không có trong danh sách cho phép
                    if (allowedTransitions[currentStatus] && !allowedTransitions[currentStatus]
                        .includes(selectedStatus)) {
                        alert(
                            `Không thể chuyển trạng thái từ "${currentStatus}" sang "${selectedStatus}".`);
                        this.value = currentStatus;
                        e.preventDefault();
                        return;
                    }

                    // Xác nhận với những trạng thái nhạy cảm
                    if (selectedStatus === 'Đã giao hàng thành công') {
                        if (!confirm('Bạn có chắc muốn đánh dấu là "Đã giao hàng thành công"?')) {
                            this.value = currentStatus;
                            e.preventDefault();
                            return;
                        }
                    }

                    if (selectedStatus === 'Hủy') {
                        if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
                            this.value = currentStatus;
                            e.preventDefault();
                            return;
                        }
                    }

                    form.submit();
                });
            });
        });
    </script>



    <!-- Script xử lý -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusOrder = {
                'Chờ xác nhận': 1,
                'Đang giao': 2,
                'Đã giao hàng thành công': 3,
                'Hoàn thành': 4,
                'Hủy': 0
            };

            // Bắt sự kiện khi bấm nút Cập nhật
            document.querySelectorAll('.update-status-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const container = this.closest('.update-container');
                    const form = container.querySelector('.status-form');
                    const currentStatus = container.closest('tr').querySelector('.order_status')
                        .getAttribute('data-status');

                    // Nếu đã Đã giao hàng thành công thì bấm không làm gì cả
                    if (currentStatus === 'Đã giao hàng thành công') {
                        return; // không hiện form, không alert
                    }

                    // Nếu đã hủy thì cảnh báo
                    if (currentStatus === 'Hủy') {
                        alert('Không thể thay đổi trạng thái đơn hàng đã hủy.');
                        return;
                    }

                    // Nếu còn lại thì cho hiển thị form
                    this.style.display = 'none';
                    form.style.display = 'block';
                });
            });

            // Bắt sự kiện thay đổi trạng thái
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function(e) {
                    const form = this.closest('.status-form');
                    const selectedStatus = this.value;
                    const currentStatus = this.closest('tr').querySelector('.order_status')
                        .getAttribute('data-status');
                    const currentOrder = statusOrder[currentStatus];
                    const newOrder = statusOrder[selectedStatus];

                    if (currentStatus === 'Đã giao hàng thành công') {
                        alert('Đơn hàng đã Đã giao hàng thành công không thể thay đổi trạng thái.');
                        this.value = currentStatus;
                        e.preventDefault();
                        return;
                    }

                    if (currentStatus === 'Đang giao' && selectedStatus === 'Hủy') {
                        alert('Đơn đang giao không thể bị hủy.');
                        this.value = currentStatus;
                        e.preventDefault();
                        return;
                    }

                    if (newOrder < currentOrder && selectedStatus !== 'Hủy') {
                        alert(
                            `Không thể chuyển trạng thái từ "${currentStatus}" về "${selectedStatus}".`
                        );
                        this.value = currentStatus;
                        e.preventDefault();
                        return;
                    }

                    if (selectedStatus === 'Đã giao hàng thành công') {
                        if (!confirm(
                                'Bạn có chắc chắn muốn đánh dấu đơn hàng này là Đã giao hàng thành công?'
                                )) {
                            this.value = currentStatus;
                            e.preventDefault();
                            return;
                        }
                    }

                    if (selectedStatus === 'Hủy') {
                        if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')) {
                            this.value = currentStatus;
                            e.preventDefault();
                            return;
                        }
                    }

                    form.submit();
                });
            });
        });
    </script>
@endsection
