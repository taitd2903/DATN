{{-- @section('content') --}}
<div>
    <h1>Coupons</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.coupons.create') }}">Tạo mã giảm giá mới</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Discount</th>
                <th>Created At</th>
                <th>Expires At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($coupons as $coupon)
                <tr>
                    <td>{{ $coupon->id }}</td>
                    <td>{{ $coupon->code }}</td>
                    <td>{{ $coupon->discount }}</td>
                    <td>{{ $coupon->created_at }}</td>
                    <td>{{ $coupon->expires_at }}</td>
                    <td>
                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}">Sửa</a>
                        <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Xoá</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{-- @endsection --}}