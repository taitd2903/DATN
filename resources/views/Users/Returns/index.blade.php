
@extends('layouts.app')

@section('content')
    <div class="container" style="margin-bottom: 30%">
        
        <h2 class="mb-5 text-primary text-center fw-bold mt-4" style="font-size: 2.5rem; letter-spacing: 1px;">
            Danh sách yêu cầu hoàn hàng
        </h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Form bộ lọc -->
        <form method="GET" action="{{ route('returns.index') }}" class="mb-4 filter-form">
            <div class="filter-section mb-4 p-4 border rounded-3 bg-white shadow-sm" style="max-width: 1200px; margin: auto;">
                <div class="row g-3 align-items-end">
                    <!-- Lọc theo mã đơn hàng -->
                    <div class="col-md-2">
                        <label for="order_id" class="form-label">Mã đơn hàng</label>
                        <input type="text" name="order_id" id="order_id" class="form-control" value="{{ request('order_id') }}" placeholder="Nhập mã đơn hàng">
                    </div>
    
                    <!-- Lọc theo trạng thái -->
                    <div class="col-md-2">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach(['pending', 'approved', 'rejected'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ __('messages.' . $status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
    
                    <!-- Lọc theo trạng thái chi tiết -->
                    <div class="col-md-2">
                        <label for="return_process_status" class="form-label">Trạng thái chi tiết</label>
                        <select name="return_process_status" id="return_process_status" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach(['return_in_progress', 'return_shipping', 'return_completed'] as $processStatus)
                                <option value="{{ $processStatus }}" {{ request('return_process_status') == $processStatus ? 'selected' : '' }}>
                                    {{ __('messages.' . $processStatus) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
    
                    <!-- Lọc theo ngày từ -->
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Từ ngày</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
    
                    <!-- Lọc theo ngày đến -->
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Đến ngày</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
    
                    <!-- Nút hành động -->
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 mb-0">Tìm kiếm</button>
                        <a href="{{ route('returns.index') }}" class="btn btn-secondary w-100 mt-2">Xóa bộ lọc</a>
                    </div>
                </div>
            </div>
        </form>

        <?php
            // Lấy tất cả yêu cầu hoàn hàng
            $filteredReturns = $returns;

            // Lọc theo mã đơn hàng
            if (request('order_id')) {
                $filteredReturns = $filteredReturns->filter(function ($return) {
                    return stripos($return->order_id, request('order_id')) !== false;
                });
            }

            // Lọc theo trạng thái
            if (request('status')) {
                $filteredReturns = $filteredReturns->filter(function ($return) {
                    return $return->status === request('status');
                });
            }

            // Lọc theo trạng thái chi tiết
            if (request('return_process_status')) {
                $filteredReturns = $filteredReturns->filter(function ($return) {
                    return $return->return_process_status === request('return_process_status');
                });
            }

            // Lọc theo ngày
            if (request('date_from')) {
                $filteredReturns = $filteredReturns->filter(function ($return) {
                    return $return->created_at->gte(\Carbon\Carbon::parse(request('date_from')));
                });
            }
            if (request('date_to')) {
                $filteredReturns = $filteredReturns->filter(function ($return) {
                    return $return->created_at->lte(\Carbon\Carbon::parse(request('date_to')));
                });
            }

            // Chuyển về collection nếu cần
            $filteredReturns = collect($filteredReturns);
        ?>

        @if($filteredReturns->isEmpty())
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
                    @foreach($filteredReturns as $return)
                        <tr>
                            <td>{{ $return->order_id }}</td>
                            <td>{{ $return->created_at->format('d-m-Y') }}</td>
                            <td>{{ __('messages.' . $return->status) }}</td>
                            <td>
                                @if ($return->return_process_status)
                                    {{ __('messages.' . $return->return_process_status) }}
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

    <style>
        .filter-form .form-label {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .filter-form .form-control,
        .filter-form .form-select {
            border-radius: 0.375rem;
            padding: 0.5rem;
        }
        .filter-form .btn {
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
        }
        .filter-form .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .filter-form .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .filter-form .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .filter-form .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }
        .filter-form .row {
            align-items: flex-end;
        }
        .filter-form .col-md-2 {
            margin-bottom: 0.5rem;
        }
    </style>
@endsection