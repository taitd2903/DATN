{{-- @section('content') --}}
<div>
    <h1>Tạo mã giảm giá</h1>
    
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf

        <label for="title">Tiêu đề Coupon:</label>
        <input type="text" name="title" id="title" value="{{ old('title') }}" required>
        
        <label for="code">Mã giảm giá:</label>
        <input type="text" name="code" id="code" value="{{ old('code') }}" required>
        
        <label for="description">Mô tả:</label>
        <textarea name="description" id="description">{{ old('description') }}</textarea>

        <label for="discount_type">Loại giảm giá:</label>
        <select name="discount_type" id="discount_type" required>
            <option value="1" {{ old('discount_type') == 1 ? 'selected' : '' }}>Phần trăm</option>
            <option value="2" {{ old('discount_type') == 2 ? 'selected' : '' }}>Giá trị cố định</option>
        </select>

        <label for="discount_value">Giá trị giảm giá:</label>
        <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value') }}" required>

        <label for="start_date">Ngày bắt đầu:</label>
        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required>

        <label for="end_date">Ngày hết hạn:</label>
        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required>

        <label for="usage_limit">Số lần sử dụng tối đa:</label>
        <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit') }}" required>

        <label for="usage_per_user">Số lần sử dụng mỗi người:</label>
        <input type="number" name="usage_per_user" id="usage_per_user" value="{{ old('usage_per_user') }}" required>

        <label for="user_voucher_limit">Loại giới hạn người dùng:</label>
        <select name="user_voucher_limit" id="user_voucher_limit" onchange="toggleUserSelection()">
            <option value="1" {{ old('user_voucher_limit') == 1 ? 'selected' : '' }}>Tất cả</option>
            <option value="2" {{ old('user_voucher_limit') == 2 ? 'selected' : '' }}>Người cụ thể</option>
            <option value="3" {{ old('user_voucher_limit') == 3 ? 'selected' : '' }}>Giới tính</option>
        </select>

        <div id="userSelection" style="display: none;">
            <label for="selected_users">Chọn người dùng:</label>
            <select name="selected_users[]" id="selected_users" multiple>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <div id="genderSelection" style="display: none;">
            <label for="gender">Giới tính áp dụng:</label>
            <select name="gender" id="gender">
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
            </select>
        </div>

        <label for="max_discount_amount">Mức giảm tối đa (nếu có):</label>
        <input type="number" name="max_discount_amount" id="max_discount_amount" value="{{ old('max_discount_amount') }}">

        <label for="status">Trạng thái:</label>
        <select name="status" id="status">
            <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Hoạt động</option>
            <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>Dừng hoạt động</option>
        </select>

        <button type="submit">Lưu</button>
    </form>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<script>
    function toggleUserSelection() {
        let userLimit = document.getElementById("user_voucher_limit").value;
        document.getElementById("userSelection").style.display = (userLimit == 2) ? "block" : "none";
        document.getElementById("genderSelection").style.display = (userLimit == 3) ? "block" : "none";
    }
</script>
{{-- @endsection --}}
