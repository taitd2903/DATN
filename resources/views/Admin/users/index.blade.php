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
                <input type="text" name="name" placeholder="Tìm theo tên" value="{{ request('name') }}" class="form-control" style="width: 200px;">
                
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
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
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
                                @endif
                            </td>
                            <td>
                                @if($user->role === 'admin')
                                    <a href=""> </a>
                                @else
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#banUserModal{{ $user->id }}">
                                    Khóa
                                </button>
                                <div class="modal fade" id="banUserModal{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                      <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
                                          @csrf
                                          <div class="modal-content">
                                              <div class="modal-header">
                                                  <h5 class="modal-title">Lý do khóa tài khoản</h5>
                                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                              </div>
                                              <div class="modal-body">
                                                  <select name="ban_reason" class="form-select" required>
                                                      <option value="">-- Chọn lý do --</option>
                                                      <option value="Vi phạm điều khoản">Vi phạm điều khoản</option>
                                                      <option value="Hoạt động đáng ngờ">Hoạt động đáng ngờ</option>
                                                      <option value="Yêu cầu từ quản trị viên">Yêu cầu từ quản trị viên</option>
                                                  </select>
                                              </div>
                                              <div class="modal-footer">
                                                  <button type="submit" class="btn btn-danger">Khóa tài khoản</button>
                                              </div>
                                          </div>
                                      </form>
                                    </div>
                                  </div>
                                @endif




                                <!-- Actions: Edit, Delete -->
                                @if($user->role === 'admin')
                                    <a href=""> </a>
                                @else
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">Edit</a>
                                @endif
                                @if($user->role === 'admin')
                                    <a href=""> </a>
                                @else
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('bạn có chắc muốn xóa?')">Delete</button>
                                    </form>
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
@endsection