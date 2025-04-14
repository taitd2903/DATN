{{-- 
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
 --}}


 @extends('layouts.app')

 @section('content')
 <div class="container my-4">
     <div class="card shadow-sm mb-4">
         <div class="card-header d-flex justify-content-between align-items-center">
             <h5 class="mb-0">Yêu Cầu Hoàn Hàng #{{ $return->order_id }}</h5>
             <span class="badge bg-warning text-dark">Hoàn thành</span>
         </div>
         <div class="card-body row">
             {{-- Thông tin khách hàng --}}
             <div class="col-md-6 mb-3">
                 <h6 class="text-primary">Khách Hàng</h6>
                 <p><i class="bi bi-person-fill"></i> {{ $return->order->user->name }}</p>
                 <p><i class="bi bi-envelope-fill"></i> {{ $return->order->user->email }}</p>
                 <p><i class="bi bi-telephone-fill"></i> {{ $return->order->user->phone }}</p>
                 <p><i class="bi bi-geo-alt-fill"></i> {{ $return->order->user->address }}</p>
             </div>
 
             {{-- Thông tin yêu cầu hoàn --}}
             <div class="col-md-6 mb-3">
                 <h6 class="text-primary">Thông Tin Yêu Cầu Hoàn</h6>
                 <p><strong>Ngày yêu cầu:</strong> {{ $return->created_at->format('d/m/Y H:i') }}</p>
                 <p><strong>Lý do:</strong> {{ $return->reason }}</p>
 
                 @if (!empty($return->rejection_reason))
                     <p><strong class="text-danger">Lý do bị từ chối:</strong> {{ $return->rejection_reason }}</p>
                 @endif
 
                 <p><strong>Trạng thái yêu cầu:</strong> 
                     <span class="badge bg-info text-dark">{{__('messages.'.$return->status) }}</span>
                 </p>
 
                 @if (!empty($return->return_process_status))
                     <p><strong>Trạng thái đơn hoàn:</strong> 
                         <span class="text-success">{{ __('messages.' .$return->return_process_status) }}</span>
                     </p>
                 @endif
             </div>
 
             {{-- Hình ảnh minh chứng --}}
             @if($return->image)
                 <div class="col-12 text-center mt-4">
                     <strong>Hình ảnh minh chứng:</strong><br>
                     <img src="{{ asset('storage/' . $return->image) }}" alt="Hình ảnh minh chứng" class="img-thumbnail mt-2" style="max-width: 300px;">
                 </div>
             @endif
         </div>
     </div>
 
    
 </div>
 @endsection
    </div>
