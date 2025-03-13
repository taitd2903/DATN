@extends('layouts.layout')

@section('content')
    <h1>Chỉnh sửa trạng thái đơn hàng</h1>

    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
        @csrf
        @method('PUT')
    
        <div class="form-group">
            <label for="status">Trạng thái:</label>
            <select name="status" class="form-control">
                @foreach ($statusOptions as $status)
                    @if (!($order->payment_method == 'vnpay' && $status == 'Hủy')) 
                        <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
        
    
        <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
    </form>
    
@endsection
