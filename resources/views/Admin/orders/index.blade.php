@extends('layouts.layout')

@section('content')
    <h1>Quản lý đơn hàng</h1>
    
    <!-- Thêm các input lọc -->
    <div class="filter-container mb-3 w-75 mx-auto">
        <!-- Hàng 1 -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="nameFilter" class="form-control" placeholder="Tên khách hàng">
            </div>
            <div class="col-md-6">
                <input type="text" id="phoneFilter" class="form-control" placeholder="Số điện thoại">
            </div>
        </div>
        <!-- Hàng 2 -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="date" id="dateFilter" class="form-control">
            </div>
            <div class="col-md-6">
                <select id="paymentStatusFilter" class="form-control">
                    <option value="">Tất cả trạng thái thanh toán</option>
                    <option value="Chưa thanh toán">Chưa thanh toán</option>
                    <option value="Đã thanh toán">Đã thanh toán</option>
                    <option value="Thất bại">Thất bại</option>
                </select>
            </div>
        </div>
        <!-- Hàng 3 -->
        <div class="row mb-2">
            <div class="col-md-6">
                <select id="orderStatusFilter" class="form-control">
                    <option value="">Tất cả trạng thái đơn hàng</option>
                    <option value="Chờ xác nhận">Chờ xác nhận</option>
                    <option value="Đang giao">Đang giao</option>
                    <option value="Hoàn thành">Hoàn thành</option>
                    <option value="Hủy">Hủy</option>
                </select>
            </div>
            <div class="row mt-2" id="backFilterRow" style="display: none;">
                <div class="col-md-12 text-end">
                    <button id="resetFilter" class="btn btn-secondary btn-sm">Trở lại</button>
                </div>
            </div>    
        </div>
    </div>

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
            @foreach($orders as $index => $order)
            <tr>
                <td>{{ $orders->firstItem() + $index }}</td>
                <td class="customer_info" data-name="{{ $order->customer_name }}" data-phone="{{ $order->customer_phone }}"> 
                    {{ $order->customer_name }} <br> SĐT:{{ $order->customer_phone }}
                </td> 
                <td class="order_date" data-date="{{ $order->created_at->format('Y-m-d') }}">{{ $order->created_at->format('d/m/Y') }}</td>
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
                    <span class="badge
                        @if($order->status == 'Chờ xác nhận') bg-warning
                        @elseif($order->status == 'Đang giao') bg-primary
                        @elseif($order->status == 'Hoàn thành') bg-success
                        @elseif($order->status == 'Hủy') bg-danger
                        @endif">
                        {{ $order->status }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem</a>
                    @if ($order->status !== 'Hủy')
                        <a href="{{ route('admin.orders.editStatus', $order->id) }}" class="btn btn-warning btn-sm">Cập nhật</a>
                    @else
                        <button class="btn btn-secondary btn-sm" disabled>Đã hủy</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $orders->links() }}

    <!-- CSS -->
    <style>
        .payment_method {
            width: 80px; 
            text-align: center; 
        }
        .customer_info {
            width: 150px; 
            word-wrap: break-word; 
        }
        .filter-container {
            margin-bottom: 20px;
        }
    </style>

    <!-- Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const filters = ["nameFilter", "phoneFilter", "dateFilter", "paymentStatusFilter", "orderStatusFilter"];
            filters.forEach(id => document.getElementById(id).addEventListener("input", filterOrders));
            filters.forEach(id => document.getElementById(id).addEventListener("change", filterOrders));
            document.getElementById("resetFilter").addEventListener("click", resetFilters);
        });

        function filterOrders() {
            let nameFilter = document.getElementById("nameFilter").value.toLowerCase().trim();
            let phoneFilter = document.getElementById("phoneFilter").value.toLowerCase().trim();
            let dateFilter = document.getElementById("dateFilter").value;
            let paymentStatusFilter = document.getElementById("paymentStatusFilter").value.toLowerCase().trim();
            let orderStatusFilter = document.getElementById("orderStatusFilter").value.toLowerCase().trim();
            
            let rows = document.querySelectorAll("#orderTable tbody tr");
            let isFiltering = false;

            rows.forEach(row => {
                let name = row.querySelector(".customer_info").getAttribute("data-name").toLowerCase();
                let phone = row.querySelector(".customer_info").getAttribute("data-phone").toLowerCase();
                let date = row.querySelector(".order_date").getAttribute("data-date");
                let paymentStatus = row.querySelector(".payment_status").innerText.toLowerCase();
                let orderStatus = row.querySelector(".order_status").getAttribute("data-status").toLowerCase();

                let nameMatch = !nameFilter || name.includes(nameFilter);
                let phoneMatch = !phoneFilter || phone.includes(phoneFilter);
                let dateMatch = !dateFilter || date === dateFilter;
                let paymentStatusMatch = !paymentStatusFilter || paymentStatus.includes(paymentStatusFilter);
                let orderStatusMatch = !orderStatusFilter || orderStatus === orderStatusFilter;

                let showRow = nameMatch && phoneMatch && dateMatch && paymentStatusMatch && orderStatusMatch;

                row.style.display = showRow ? "table-row" : "none";
                if (!showRow) isFiltering = true;
            });

            // Hiển thị nút "Trở lại" nếu có ít nhất một bộ lọc được áp dụng
            document.getElementById("backFilterRow").style.display = (
                nameFilter || phoneFilter || dateFilter || paymentStatusFilter || orderStatusFilter
            ) ? "block" : "none";
        }

        function resetFilters() {
            document.getElementById("nameFilter").value = "";
            document.getElementById("phoneFilter").value = "";
            document.getElementById("dateFilter").value = "";
            document.getElementById("paymentStatusFilter").value = "";
            document.getElementById("orderStatusFilter").value = "";

            document.querySelectorAll("#orderTable tbody tr").forEach(row => {
                row.style.display = "table-row";
            });

            document.getElementById("backFilterRow").style.display = "none";
        }
    </script>
@endsection
