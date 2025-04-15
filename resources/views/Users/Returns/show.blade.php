
 @extends('layouts.app')

 @section('content')
 <div class="container my-4">
     <div class="card shadow-sm mb-4">
         <div class="card-header d-flex justify-content-between align-items-center">
             <h5 class="mb-0">Yêu Cầu Hoàn Hàng #{{ $return->order_id }}</h5>
             <span class="badge bg-warning text-dark">Chi tiết</span>
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
                 <p><strong>Minh chứng hoàn:</strong> {{ $return->refund_note }}</p>
                 @if($return->refund_image)
                 <div class="col-12 text-center mt-4">
                     <strong>Hình ảnh minh chứng hoàn tiền:</strong><br>
                     <img src="{{ asset('storage/' . $return->refund_image) }}" alt="Hình ảnh minh chứng" class="img-thumbnail mt-2" style="max-width: 300px;">
                 </div>
                 @endif
                 
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
