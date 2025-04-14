
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Trạng thái yêu cầu hoàn hàng</h4>
        </div>
        <div class="card-body text-center">
            <div class="mb-3">
                <strong>Mã đơn hàng:</strong> {{ $return->order_id }}
            </div>
            <div class="mb-3">
                <strong>Ngày yêu cầu:</strong> {{ $return->created_at->format('d-m-Y') }}
            </div>
            <div class="mb-3">
                <strong>Lý do:</strong> {{ $return->reason }}
            </div>

            @if($return->image)
                <div class="mb-3">
                    <strong>Hình ảnh minh chứng:</strong><br>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset('storage/' . $return->image) }}" alt="Hình ảnh minh chứng" class="img-thumbnail" style="max-width: 300px;">
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <strong>Trạng thái yêu cầu:</strong> 
                <span class="badge bg-info text-dark">{{ $return->status }}</span>
            </div>

            @if (!empty($return->rejection_reason))
                <div class="mb-3">
                    <strong>Lý do bị từ chối:</strong> 
                    <span class="text-danger">{{ $return->rejection_reason }}</span>
                </div>
            @endif

            @if (!empty($return->return_process_status))
                <div class="mb-3">
                    <strong>Trạng thái đơn hoàn:</strong> 
                    <span class="text-success">{{ $return->return_process_status }}</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

