@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Danh sách yêu cầu hoàn hàng</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($returns->isEmpty())
            <p>Chưa có yêu cầu hoàn hàng nào.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Ngày yêu cầu</th>
                        <th>Trạng thái</th>
                        <th>Trạng thái chi tiết</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($returns as $return)
                        <tr>
                            <td>{{ $return->order_id }}</td>
                            <td>{{ $return->created_at->format('d-m-Y') }}</td>
                            
                            <td>{{ __('messages.' . $return->status) }}</td>

                            
                            <td>
                                @if (!$return->return_process_status == '')
                              
                                 {{ __('messages.' .$return->return_process_status) }}   
                                @endif
                                
                            </td>
                            
                            
                            
                            <td>
                                <a href="{{ route('returns.show', $return->id) }}" class="btn btn-info">Xem trạng thái</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection