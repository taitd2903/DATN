@extends('layouts.layout')

@section('content')
    <div class="container py-5">
        <h2 class="text-center text-primary fw-bold">Danh Sách Yêu Cầu Hoàn Hàng</h2>

        <!-- Bộ lọc -->
        <div class="card shadow-sm mt-4 mb-4">
            <div class="card-body">
                <form action="{{ route('admin.returns.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Ngày kết thúc</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Tìm kiếm</button>
                        <a href="{{ route('admin.returns.index') }}" class="btn btn-secondary">Xóa bộ lọc</a>
                    </div>
                </form>
            </div>
        </div>

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
                            <th>Số tiền cần hoàn</th>
                            <th>Trạng Thái</th>
                            <th>Cập nhật trạng thái</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($returns && $returns->count() > 0)
                            @foreach ($returns as $return)
                                @if (!request('start_date') || $return->created_at->gte(request('start_date')))
                                    @if (!request('end_date') || $return->created_at->lte(request('end_date')))
                                        @if (!request('status') || $return->status == request('status'))
                                            <tr>
                                                <td>{{ $return->order->id }}</td>
                                                <td>{{ $return->reason }}</td>
                                                <td>
                                                    @if ($return->image)
                                                        <img src="{{ Storage::url($return->image) }}" alt="Image"
                                                            width="100">
                                                    @else
                                                        <span>Không có ảnh</span>
                                                    @endif
                                                </td>
                                                <td>{{ $return->bank_account }}</td>
                                                <td>{{ $return->bank_name }}</td>
                                                <td>{{ $return->account_holder }}</td>
                                                <td>{{ $return->order->user->name ?? 'Không có người dùng' }}</td>
                                                <td>{{ $return->order ? number_format($return->order->total_price, 0, ',', '.') : '0' }}
                                                    VND</td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                        @if ($return->status == 'pending') badge-warning 
                                                        @elseif($return->status == 'approved') badge-success 
                                                        @else badge-danger @endif">
                                                        {{ ucfirst($return->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($return->status == 'approved')
                                                        <form
                                                            action="{{ route('admin.returns.update_process', $return->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <select name="return_process_status"
                                                                class="form-select d-inline w-auto"
                                                                onchange="this.form.submit()">
                                                                <option> Cập nhật trạng thái</option>
                                                                <option value="return_in_progress"
                                                                    {{ $return->return_process_status == 'return_in_progress' ? 'selected' : '' }}>
                                                                    Đang chờ hoàn hàng</option>
                                                                <option value="return_shipping"
                                                                    {{ $return->return_process_status == 'return_shipping' ? 'selected' : '' }}>
                                                                    Đang trên đường hoàn</option>
                                                                <option value="return_completed"
                                                                    {{ $return->return_process_status == 'return_completed' ? 'selected' : '' }}>
                                                                    Đã nhận được đơn hoàn</option>
                                                            </select>
                                                        </form>
                                                    @else
                                                        <span class="badge badge-secondary">Chưa duyệt</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <!-- Nút xem chi tiết luôn hiển thị -->
                                                    <button type="button" class="btn btn-info btn-sm " data-toggle="modal"
                                                        data-target="#viewModal{{ $return->id }}">
                                                        Xem
                                                    </button>

                                                    @if ($return->status == 'pending')
                                                        <form action="{{ route('admin.returns.approve', $return->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="btn btn-success btn-sm">Duyệt</button>
                                                        </form>

                                                        <!-- Nút từ chối mở modal -->
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#rejectModal{{ $return->id }}">
                                                            Từ chối
                                                        </button>

                                                        <!-- Modal nhập lý do từ chối -->
                                                        <div class="modal fade" id="rejectModal{{ $return->id }}"
                                                            tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="rejectModalLabel">Nhập
                                                                            lý do từ chối</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">×</span>
                                                                        </button>
                                                                    </div>
                                                                    <form
                                                                        action="{{ route('admin.returns.reject', $return->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <div class="modal-body">
                                                                            <div class="form-group">
                                                                                <label for="rejection_reason">Lý do từ
                                                                                    chối</label>
                                                                                <textarea name="rejection_reason" class="form-control" rows="4" required></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-dismiss="modal">Đóng</button>
                                                                            <button type="submit"
                                                                                class="btn btn-danger">Xác nhận từ
                                                                                chối</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif ($return->status == 'approved' && $return->return_process_status == 'return_completed' && !$return->refunded_at)
                                                        <!-- Nút hoàn tiền mở modal -->
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#refundModal{{ $return->id }}">
                                                            Hoàn tiền
                                                        </button>

                                                        <!-- Modal nhập thông tin hoàn tiền -->
                                                        <div class="modal fade" id="refundModal{{ $return->id }}"
                                                            tabindex="-1" role="dialog"
                                                            aria-labelledby="refundModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="refundModalLabel">
                                                                            Thông tin hoàn tiền</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">×</span>
                                                                        </button>
                                                                    </div>
                                                                    <form
                                                                        action="{{ route('admin.returns.refunded', $return->id) }}"
                                                                        method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <div class="modal-body">
                                                                            <div class="form-group">
                                                                                <label for="refund_image">Ảnh minh chứng
                                                                                    hoàn tiền</label>
                                                                                <input type="file" name="refund_image"
                                                                                    id="refund_image"
                                                                                    class="form-control" accept="image/*"
                                                                                    required>
                                                                                @error('refund_image')
                                                                                    <div class="text-danger">
                                                                                        {{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="refund_note">Ghi chú hoàn
                                                                                    tiền</label>
                                                                                <input type="text" name="refund_note"
                                                                                    id="refund_note" class="form-control"
                                                                                    value="{{ old('refund_note') }}">
                                                                                @error('refund_note')
                                                                                    <div class="text-danger">
                                                                                        {{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-dismiss="modal">Đóng</button>
                                                                            <button type="submit"
                                                                                class="btn btn-primary">Xác nhận hoàn
                                                                                tiền</button>
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

                                                    <!-- Modal xem chi tiết đơn hoàn -->
                                                    <div class="modal fade" id="viewModal{{ $return->id }}"
                                                        tabindex="-1" role="dialog" aria-labelledby="viewModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="viewModalLabel">Chi tiết
                                                                        yêu cầu hoàn hàng #{{ $return->id }}</h5>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <p><strong>ID Đơn hàng:</strong>
                                                                                {{ $return->order->id }}</p>
                                                                            <p><strong>Lý do hoàn hàng:</strong>
                                                                                {{ $return->reason }}</p>
                                                                            <p><strong>Số tài khoản:</strong>
                                                                                {{ $return->bank_account }}</p>
                                                                            <p><strong>Ngân hàng:</strong>
                                                                                {{ $return->bank_name }}</p>
                                                                            <p><strong>Tên chủ tài khoản:</strong>
                                                                                {{ $return->account_holder }}</p>
                                                                            <p><strong>Người yêu cầu:</strong>
                                                                                {{ $return->order->user->name ?? 'Không có người dùng' }}
                                                                            </p>
                                                                            <p><strong>Số tiền cần hoàn:</strong>
                                                                                {{ $return->order ? number_format($return->order->total_price, 0, ',', '.') : '0' }}
                                                                                VND</p>
                                                                            <p><strong>Trạng thái:</strong>
                                                                                {{ ucfirst($return->status) }}</p>
                                                                            <p><strong>Ngày tạo:</strong>
                                                                                {{ $return->created_at->format('d/m/Y H:i') }}
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <p><strong>Ảnh minh chứng:</strong></p>
                                                                            @if ($return->image)
                                                                                <img src="{{ Storage::url($return->image) }}"
                                                                                    alt="Ảnh minh chứng" class="img-fluid"
                                                                                    style="max-width: 100%;">
                                                                            @else
                                                                                <p>Không có ảnh minh chứng</p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h4 class="mt-4 mb-3 text-dark">Sản phẩm trong đơn hàng
                                                                </h4>
                                                                <div class="table-responsive">
                                                                    <table
                                                                        class="table table-striped table-hover table-bordered shadow-sm rounded align-middle">
                                                                        <thead class="table-dark">
                                                                            <tr>
                                                                                <th class="text-center"
                                                                                    style="width: 80px;">Ảnh</th>
                                                                                <th>Sản phẩm</th>
                                                                                <th class="text-center"
                                                                                    style="width: 100px;">Size</th>
                                                                                <th class="text-center"
                                                                                    style="width: 100px;">Màu</th>
                                                                                <th class="text-center"
                                                                                    style="width: 100px;">Số lượng</th>
                                                                                <th class="text-end"
                                                                                    style="width: 120px;">Giá</th>
                                                                                <th class="text-end">Tổng</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @if ($return->order && $return->order->orderItems->isNotEmpty())
                                                                                @foreach ($return->order->orderItems as $item)
                                                                                    <tr>
                                                                                        <td class="text-center">
                                                                                            <img src="{{ asset('storage/' . $item->variant->image) }}"
                                                                                                class="rounded img-thumbnail"
                                                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                                                        </td>
                                                                                        <td>{{ $item->product->name }}</td>
                                                                                        <td class="text-center">
                                                                                            {{ $item->size }}</td>
                                                                                        <td class="text-center">
                                                                                            {{ $item->color }}</td>
                                                                                        <td class="text-center">
                                                                                            {{ $item->quantity }}</td>
                                                                                        <td class="text-end">
                                                                                            {{ number_format($item->price, 0, ',', '.') }}
                                                                                            VND</td>
                                                                                        <td class="text-end fw-bold">
                                                                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                                                                            VND</td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Đóng</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @endif
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
        document.addEventListener('DOMContentLoaded', function() {
            // Chọn tất cả các select cập nhật trạng thái
            document.querySelectorAll('select[name="return_process_status"]').forEach(function(select) {
                const form = select.closest('form');
                const returnRow = form.closest('tr');

                // Lấy trạng thái hiện tại từ select (option selected)
                const currentStatus = returnRow.querySelector(
                    'select[name="return_process_status"] option[selected]')?.value || null;

                const steps = ['return_in_progress', 'return_shipping', 'return_completed'];
                const currentIndex = currentStatus ? steps.indexOf(currentStatus) : -1;

                // Duyệt qua tất cả các option trong select để disable những option không hợp lệ
                select.querySelectorAll('option').forEach(function(option) {
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
