{{-- @section('content') --}}
<div>
    <h1>Tạo mã giảm giá</h1>
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        <label for="code">Mã giảm giá:</label>
        <input type="text" name="code" id="code" required>
    
        <label for="discount">Giảm giá:</label>
        <input type="number" name="discount" id="discount" required>
    
        <label for="expires_at">Ngày hết hạn:</label>
        <input type="date" name="expires_at" id="expires_at" required>
        <button type="submit">Lưu</button>
    </form>
</div>
{{-- @endsection --}}