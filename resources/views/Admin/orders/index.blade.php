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
                <td class="customer_address">{{ $order->customer_address}}, <span id="ward-name"></span> ,<span id="district-name"></span>, <span id="city-name"></span></td> <!-- Đổi class này thành 'customer_address' nếu chứa địa chỉ -->
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
<script>
    document.addEventListener('DOMContentLoaded', () => {
    // Lấy mã từ các phần tử HTML (hoặc lấy từ PHP như sau)
    const cityCode = "{{ $order->city }}";
    const districtCode = "{{ $order->district }}";
    const wardCode = "{{ $order->ward }}";

    // Hàm lấy tên tỉnh/thành phố từ mã cityCode
    fetch(`https://provinces.open-api.vn/api/p/`)
        .then(response => response.json())
        .then(data => {
            let cityName = "";
            data.forEach(province => {
                if (province.code == cityCode) {
                    cityName = province.name;
                }
            });
            // Cập nhật tên tỉnh/thành phố
            document.getElementById("city-name").innerText = cityName;
        })
        .catch(error => console.error("Lỗi tải dữ liệu tỉnh/thành phố:", error));

    // Hàm lấy tên quận/huyện từ mã districtCode
    fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`)
        .then(response => response.json())
        .then(data => {
            let districtName = "";
            data.districts.forEach(district => {
                if (district.code == districtCode) {
                    districtName = district.name;
                }
            });
            // Cập nhật tên quận/huyện
            document.getElementById("district-name").innerText = districtName;
        })
        .catch(error => console.error("Lỗi tải dữ liệu quận/huyện:", error));

    // Hàm lấy tên xã/phường từ mã wardCode
    fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
        .then(response => response.json())
        .then(data => {
            let wardName = "";
            data.wards.forEach(ward => {
                if (ward.code == wardCode) {
                    wardName = ward.name;
                }
            });
            // Cập nhật tên xã/phường
            document.getElementById("ward-name").innerText = wardName;
        })
        .catch(error => console.error("Lỗi tải dữ liệu xã/phường:", error));
});

</script>