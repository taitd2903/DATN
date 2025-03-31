{{-- @extends('layouts.layout')

@section('content')
    <div class="container">
        <h1>Quản lý Combo sản phẩm</h1>
        <a href="{{ route('admin.combos.create') }}" class="btn btn-primary mb-3">Thêm Combo mới</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tên Combo</th>
                    <th>Sản phẩm</th>
                    <th>Giá ưu đãi</th>
                    <th>Hình ảnh</th>
                    <th>Flash Sale</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($combos as $combo)
                <tr>
                    <td>{{ $combo->name }}</td>
                    <td>
                        @foreach($combo->products as $product)
                            - {{ $product->name }} <br>
                        @endforeach
                    </td>
                    <td>{{ number_format($combo->discount_price) }} đ</td>
                    <td><img src="{{ asset('storage/'.$combo->image) }}" width="50" alt="combo image"></td>
                    <td>{{ $combo->is_flash_sale ? 'Có' : 'Không' }}</td>
                    <td>
                        <a href="{{ route('combos.edit', $combo) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('combos.destroy', $combo) }}" method="POST" style="display: inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
@endsection --}}
