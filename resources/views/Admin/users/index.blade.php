@extends('layouts.layout')

@section('content')

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
            <form action="{{ route('users.transferAdmin') }}" method="POST">
                @csrf
                <select name="new_admin_id">
                    @foreach($users as $user)
                        @if($user->role !== 'admin')
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit">Chuyển quyền admin</button>
            </form>
        </div>
    @endif

    <div class="container">
        @if (Auth::user()->role === 'admin')
            <h2>Quản lý tài khoản</h2>


            <!-- Nếu có thông báo thành công -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('admin.users.index') }}" method="GET" class="mb-3 d-flex gap-2">
                <input type="text" name="name" placeholder="Tìm theo tên" value="{{ request('name') }}" class="form-control"
                    style="width: 200px;">

                <select name="role" class="form-select" style="width: 150px;">
                    <option value="">Tất cả vai trò</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="Staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>user</option>
                </select>

                <select name="status" class="form-select" style="width: 150px;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Đã khóa</option>
                </select>

                <button type="submit" class="btn btn-primary">Lọc</button>
            </form>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Image</th>
                        <th>Address</th>
                        <th>Role</th>
                        <th>STATUS</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>

                            <!-- Hiển thị ảnh -->
                            <td>
                                @if ($user->image)
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" width="100" height="100">
                                @else
                                    No Image
                                @endif
                            </td>

                            <td>{{ $user->address }}</td>

                            <td>
                                @if (auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" onchange="this.form.submit()" class="form-select form-select-sm">

                                            <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                        </select>
                                    </form>
                                @else
                                    <span>{{ $user->role }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($user->status === 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Bị khóa</span>
                                    <br>
                                    <small><strong>Lý do:</strong> {{ $user->ban_reason }}</small>
                                @endif
                            </td>
                            
                            <td>
                                @if ($user->role !== 'admin')
                            
                                    {{-- Khóa tài khoản --}}
                                    @if ($user->status === 'active')
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#banModal{{ $user->id }}" class="btn btn-warning btn-sm">Khóa</button>
                            
                                        <!-- Modal Khóa -->
                                        <div class="modal fade" id="banModal{{ $user->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" class="ban-form">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Khóa tài khoản: {{ $user->name }}</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="reason-select-{{ $user->id }}">Chọn lý do</label>
                                                            
                                                            <select name="ban_reason" id="reason-select-{{ $user->id }}" class="form-select reason-select" onchange="toggleOtherReason(this, {{ $user->id }})" required>
                                                                <option value="" disabled selected hidden>-- Chọn lý do --</option>
                                                                <option value="Vi phạm điều khoản">Vi phạm điều khoản</option>
                                                                <option value="Hoạt động đáng ngờ">Hoạt động đáng ngờ</option>
                                                                <option value="Yêu cầu từ quản trị viên">Yêu cầu từ quản trị viên</option>
                                                                <option value="Khác">Khác</option>
                                                            </select>
                                                            @error('ban_reason')
                                                            <span class="error">{{ $message }}</span>
                                                            @enderror
                            
                                                            <div class="mt-2 d-none" id="other-reason-{{ $user->id }}">
                                                                <label>Nhập lý do khác:</label>
                                                                <input type="text" name="custom_reason" class="form-control custom-reason" maxlength="255">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-danger">Khóa</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                            
                                    {{-- Mở khóa tài khoản --}}
                                    @if ($user->status === 'banned')
                                        <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button class="btn btn-success btn-sm">Mở khóa</button>
                                        </form>
                                    @endif
                            
                                    {{-- Nút sửa --}}
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">Edit</a>
                            
                                    {{-- Nút xóa
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Delete</button>
                                    </form> --}}
                            
                                @else
                                    {{-- Nếu là admin, không cho sửa/xóa chính mình --}}
                                    <span class="text-muted">Không thao tác</span>
                                @endif
                            </td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- <!-- Pagination -->
            {{ $users->links() }} --}}
        @else
            <script>
                alert("bạn không có quyền truy cập! vui lòng liên hệ với admin.")
                window.history.back();
            </script>
        @endif
    </div>
    @push('scripts')
<script>
    function toggleOtherReason(select, userId) {
        const otherInput = document.getElementById('other-reason-' + userId);
        if (select.value === 'Khác') {
            otherInput.classList.remove('d-none');
        } else {
            otherInput.classList.add('d-none');
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.ban-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                const userId = form.querySelector('.reason-select').dataset.userId;
                const reasonSelect = form.querySelector('.reason-select');
                const customReason = form.querySelector('.custom-reason');

                if (!reasonSelect.value) {
                    e.preventDefault();
                    alert('Vui lòng chọn lý do khóa!');
                    return;
                }

                if (reasonSelect.value === 'Khác' && (!customReason || !customReason.value.trim())) {
                    e.preventDefault();
                    alert('Vui lòng nhập lý do khác!');
                    return;
                }
            });
        });
    });
</script>
@endpush

@endsection