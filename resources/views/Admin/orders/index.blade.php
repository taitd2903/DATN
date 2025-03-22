@extends('layouts.layout')

@section('content')
    <h1>Quản lý đơn hàng</h1>
    
    <div class="input-group my-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Nhập khách hàng, số điện thoại hoặc đia chỉ...">
        <button class="btn btn-primary" id="searchButton">Tìm kiếm</button>
    </div>

    <table class="table table-bordered mt-3" id="orderTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Phương thức thanh toán</th>
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
                <td class="customer_name">{{ $order->customer_name }}</td>
                <td class="customer_phone">{{ $order->customer_phone }}</td>
                <td class="customer_address">{{ $order->note }}</td> <!-- Đổi class này thành 'customer_address' nếu chứa địa chỉ -->
                <td>
                    @if ($order->payment_method == 'cod')
                        COD
                    @elseif ($order->payment_method == 'vnpay')
                        VNPAY
                    @else
                        Khác
                    @endif
                </td>
                <td>{{ $order->payment_status }}</td>
                <td>{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                <td class="order_status">
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

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Gán sự kiện click cho nút tìm kiếm
            document.getElementById("searchButton").addEventListener("click", filterOrders);
            
            // Cho phép nhấn Enter để tìm kiếm nhanh hơn
            document.getElementById("searchInput").addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    filterOrders();
                }
            });
        });
    
        function filterOrders() {
            let input = document.getElementById("searchInput").value.toLowerCase().trim();
            let rows = document.querySelectorAll("#orderTable tbody tr");
    
            rows.forEach(row => {
                let name = row.querySelector(".customer_name")?.innerText.toLowerCase().trim() || "";
                let phone = row.querySelector(".customer_phone")?.innerText.toLowerCase().trim() || "";
                let address = row.querySelector(".customer_address")?.innerText.toLowerCase().trim() || "";
    
                // Kiểm tra nếu input rỗng, hiển thị tất cả
                if (input === "") {
                    row.style.display = "table-row"; 
                    return;
                }
    
                // Nếu bất kỳ trường nào chứa từ khóa nhập vào, hiển thị hàng đó
                if (name.includes(input) || phone.includes(input) || address.includes(input)) {
                    row.style.display = "table-row"; 
                } else {
                    row.style.display = "none"; 
                }
            });
        }
    </script> --}}
     <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("searchButton").addEventListener("click", filterOrders);
            document.getElementById("searchInput").addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    filterOrders();
                }
            });
        });
    
        function filterOrders() {
            let input = document.getElementById("searchInput").value.toLowerCase().trim();
            let rows = document.querySelectorAll("#orderTable tbody tr");
    
            rows.forEach(row => {
                let name = row.querySelector(".customer_name")?.innerText.toLowerCase().trim() || "";
                let phone = row.querySelector(".customer_phone")?.innerText.toLowerCase().trim() || "";
                let address = row.querySelector(".customer_address")?.innerText.toLowerCase().trim() || "";
    
                let inputParts = input.split(" ").filter(part => part.length > 0); // Tách các từ trong input
    
                // Nếu input rỗng, hiển thị tất cả
                if (inputParts.length === 0) {
                    row.style.display = "table-row";
                    return;
                }
    
                let matches = inputParts.every(part => 
                    name.includes(part) || phone.includes(part) || address.includes(part)
                );
    
                if (matches) {
                    row.style.display = "table-row"; // Hiển thị nếu thỏa mãn điều kiện
                } else {
                    row.style.display = "none"; // Ẩn nếu không khớp
                }
            });
        }
    </script> 
    
@endsection
