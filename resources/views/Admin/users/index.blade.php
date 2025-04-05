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
                        <a href="">  </a>
                        @else
                        <a href="{{ route('admin.users.toggleStatus', $user->id) }}" class="btn btn-warning">
                            {{ $user->status === 'active' ? 'Khóa' : 'Mở khóa' }}
                        </a>
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
                            <button type="submit" class="btn btn-danger" onclick="return confirm('bạn có chắc muốn xóa?')">Delete</button>
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
