{{-- @section('content') --}}
<div>
    <h1>Sửa mã giảm giá</h1>
    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="code">Mã giảm giá:</label>
        <input type="text" name="code" id="code" value="{{ $coupon->code }}" required>
    
        <label for="discount">Giảm giá:</label>
        <input type="number" name="discount" id="discount" value="{{ $coupon->discount }}" required>
    
        <label for="expires_at">Ngày hết hạn:</label>
        {{-- <input type="date" name="expires_at" id="expires_at" value="{{ $coupon->expires_at->format('Y-m-d') }}" required> --}}
        <input type="date" name="expires_at" id="expires_at" value="{{ \Carbon\Carbon::parse($coupon->expires_at)->format('Y-m-d') }}" required>

        <button type="submit">Cập nhật</button>
    </form>
</div>
{{-- @endsection --}}