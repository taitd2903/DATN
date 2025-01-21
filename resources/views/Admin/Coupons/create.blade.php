{{-- @section('content') --}}
<div>
    <h1>Tạo mã giảm giá</h1>
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        <label for="code">Mã giảm giá:</label>
        <input type="text" name="code" id="code">

        <label for="discount">Giảm giá:</label>
        <input type="number" name="discount" id="discount">

        <label for="expires_at">Ngày hết hạn:</label>
        <input type="date" name="expires_at" id="expires_at">
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
{{-- @endsection --}}
