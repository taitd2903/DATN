@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Trạng thái yêu cầu hoàn hàng</h2>

        <div>
            <strong>Mã đơn hàng:</strong> {{ $return->order_id }}<br>
            <strong>Ngày yêu cầu:</strong> {{ $return->created_at->format('d-m-Y') }}<br>
            <strong>Lý do:</strong> {{ $return->reason }}<br>
            @if($return->image)
                <strong>Hình ảnh minh chứng:</strong><br>
                <img src="{{ asset('storage/' . $return->image) }}" alt="Hình ảnh minh chứng" class="img-fluid" style="max-width: 300px;">
            @endif
            <br>
            <strong>Trạng thái yêu cầu</strong>
            <td>{{$return->status}}</td>
            
            @if (!$return->rejection_reason== "")
            <br>
            <strong>Lý do bị từ chối : </strong>

            <td>{{$return->rejection_reason}}</td>
            @endif
            
            
            
            
            
            @if (!$return->return_process_status== "")
            <br>
            <strong>Trạng thái đơn hoàn : </strong>
            
            <td>{{$return->return_process_status}}</td>
            @endif
           
            
            
            
        </div>
    </div>
@endsection
