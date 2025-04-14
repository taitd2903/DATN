{{-- 

@extends('layouts.layout')

@section('content')
    <div class="container py-5">
        <h2 class="text-center text-primary fw-bold">Danh Sách Yêu Cầu Hoàn Hàng</h2>
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Đơn Hàng</th>
                            <th>Lý Do</th>
                            <th>Ảnh Minh Chứng</th>
                            <th>Số tài khoản</th>
                            <th>Ngân hàng</th>
                            <th>Tên chủ tài khoản</th>
                            <th>Tên người dùng</th>
                            <th>Trạng Thái</th>
                            <th>Cập nhật trạng thái</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($returns && $returns->count() > 0)
                            @foreach ($returns as $return)
                                <tr>
                                    <td>{{ $return->order->id }}</td>
                                    <td>{{ $return->reason }}</td>
                                    <td>
                                        @if ($return->image)
                                            <img src="{{ Storage::url($return->image) }}" alt="Image" width="100">
                                        @else
                                            <span>Không có ảnh</span>
                                        @endif
                                    </td>
                                    <td>{{ $return->bank_account }}</td>
                                    <td>{{ $return->bank_name }}</td>
                                    <td>{{ $return->account_holder }}</td>
                                    <td>{{ $return->order->user->name ?? 'Không có người dùng' }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($return->status == 'pending') badge-warning 
                                            @elseif($return->status == 'approved') badge-success 
                                            @else badge-danger 
                                            @endif">
                                            {{ ucfirst($return->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($return->status == 'approved')
                                            <form action="{{ route('admin.returns.update_process', $return->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <select name="return_process_status" class="form-select d-inline w-auto" onchange="this.form.submit()">
                                                    <option value="">-- Cập nhật trạng thái --</option>
                                                    <option value="return_in_progress" {{ $return->return_process_status == 'return_in_progress' ? 'selected' : '' }}>Đang chờ hoàn hàng</option>
                                                    <option value="return_shipping" {{ $return->return_process_status == 'return_shipping' ? 'selected' : '' }}>Đang trên đường hoàn</option>
                                                    <option value="return_completed" {{ $return->return_process_status == 'return_completed' ? 'selected' : '' }}>Đã nhận được đơn hoàn</option>
                                                </select>
                                            </form>
                                        @else
                                            <span class="badge badge-secondary">Chưa duyệt</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($return->status == 'pending')
                                            <form action="{{ route('admin.returns.approve', $return->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm">Duyệt</button>
                                            </form>

                                            <!-- Nút từ chối mở modal -->
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $return->id }}">
                                                Từ chối
                                            </button>

                                            <!-- Modal nhập lý do từ chối -->
                                            <div class="modal fade" id="rejectModal{{ $return->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectModalLabel">Nhập lý do từ chối</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('admin.returns.reject', $return->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="rejection_reason">Lý do từ chối</label>
                                                                    <textarea name="rejection_reason" class="form-control" rows="4" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span>Đã xử lý</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Không có yêu cầu hoàn hàng nào.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
 --}}


 @extends('layouts.layout')

@section('content')
    <div class="container py-5">
        <h2 class="text-center text-primary fw-bold">Danh Sách Yêu Cầu Hoàn Hàng</h2>
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Đơn Hàng</th>
                            <th>Lý Do</th>
                            <th>Ảnh Minh Chứng</th>
                            <th>Số tài khoản</th>
                            <th>Ngân hàng</th>
                            <th>Tên chủ tài khoản</th>
                            <th>Tên người dùng</th>
                            <th>Trạng Thái</th>
                            <th>Cập nhật trạng thái</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($returns && $returns->count() > 0)
                            @foreach ($returns as $return)
                                <tr>
                                    <td>{{ $return->order->id }}</td>
                                    <td>{{ $return->reason }}</td>
                                    <td>
                                        @if ($return->image)
                                            <img src="{{ Storage::url($return->image) }}" alt="Image" width="100">
                                        @else
                                            <span>Không có ảnh</span>
                                        @endif
                                    </td>
                                    <td>{{ $return->bank_account }}</td>
                                    <td>{{ $return->bank_name }}</td>
                                    <td>{{ $return->account_holder }}</td>
                                    <td>{{ $return->order->user->name ?? 'Không có người dùng' }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($return->status == 'pending') badge-warning 
                                            @elseif($return->status == 'approved') badge-success 
                                            @else badge-danger 
                                            @endif">
                                            {{ ucfirst($return->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($return->status == 'approved')
                                            <form action="{{ route('admin.returns.update_process', $return->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <select name="return_process_status" class="form-select d-inline w-auto" onchange="this.form.submit()">
                                                   
                                                    <option value="return_in_progress" {{ $return->return_process_status == 'return_in_progress' ? 'selected' : '' }}>Đang chờ hoàn hàng</option>
                                                    <option value="return_shipping" {{ $return->return_process_status == 'return_shipping' ? 'selected' : '' }}>Đang trên đường hoàn</option>
                                                    <option value="return_completed" {{ $return->return_process_status == 'return_completed' ? 'selected' : '' }}>Đã nhận được đơn hoàn</option>
                                                </select>
                                            </form>
                                        @else
                                            <span class="badge badge-secondary">Chưa duyệt</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($return->status == 'pending')
                                            <form action="{{ route('admin.returns.approve', $return->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm">Duyệt</button>
                                            </form>

                                            <!-- Nút từ chối mở modal -->
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $return->id }}">
                                                Từ chối
                                            </button>

                                            <!-- Modal nhập lý do từ chối -->
                                            <div class="modal fade" id="rejectModal{{ $return->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectModalLabel">Nhập lý do từ chối</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('admin.returns.reject', $return->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="rejection_reason">Lý do từ chối</label>
                                                                    <textarea name="rejection_reason" class="form-control" rows="4" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($return->status == 'approved' && $return->return_process_status == 'return_completed' && !$return->refunded_at)
                                            <!-- Nút hoàn tiền mở modal -->
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#refundModal{{ $return->id }}">
                                                Hoàn tiền
                                            </button>

                                            <!-- Modal nhập thông tin hoàn tiền -->
                                            <div class="modal fade" id="refundModal{{ $return->id }}" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="refundModalLabel">Thông tin hoàn tiền</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('admin.returns.refunded', $return->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="refund_image">Ảnh minh chứng hoàn tiền</label>
                                                                    <input type="file" name="refund_image" id="refund_image" class="form-control" accept="image/*" required>
                                                                    @error('refund_image')
                                                                        <div class="text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="refund_note">Ghi chú hoàn tiền</label>
                                                                    <input type="text" name="refund_note" id="refund_note" class="form-control" value="{{ old('refund_note') }}">
                                                                    @error('refund_note')
                                                                        <div class="text-danger">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-primary">Xác nhận hoàn tiền</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($return->refunded_at)
                                            <span class="badge badge-info">Đã hoàn tiền</span>
                                        @else
                                            <span>Đã xử lý</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10" class="text-center">Không có yêu cầu hoàn hàng nào.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chọn tất cả các select cập nhật trạng thái
            document.querySelectorAll('select[name="return_process_status"]').forEach(function (select) {
                const form = select.closest('form');
                const returnRow = form.closest('tr');
                
                // Lấy trạng thái hiện tại từ select (option selected)
                const currentStatus = returnRow.querySelector('select[name="return_process_status"] option[selected]')?.value || null;
    
                const steps = ['return_in_progress', 'return_shipping', 'return_completed'];
                const currentIndex = currentStatus ? steps.indexOf(currentStatus) : -1;
    
                // Duyệt qua tất cả các option trong select để disable những option không hợp lệ
                select.querySelectorAll('option').forEach(function (option) {
                    const optionValue = option.value;
                    const optionIndex = steps.indexOf(optionValue);
    
                    // Chỉ enable option nếu nó là bước kế tiếp (current + 1)
                    if (optionValue && optionIndex !== currentIndex + 1) {
                        option.disabled = true;
                    }
                });
            });
        });
    </script>
@endsection