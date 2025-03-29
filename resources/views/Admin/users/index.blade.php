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
                    <td>{{ $user->role }}</td>
                    <td>
                        @if ($user->status === 'active')
                            <span class="badge bg-success">Hoạt động</span>
                        @else
                            <span class="badge bg-danger">Bị khóa</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.users.toggleStatus', $user->id) }}" class="btn btn-warning">
                            {{ $user->status === 'active' ? 'Khóa' : 'Mở khóa' }}
                        </a>
                      
                  

                    <!-- Actions: Edit, Delete -->
                    
                        {{-- <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">Edit</a> --}}
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('bạn có chắc muốn xóa?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <!-- Pagination -->
    {{ $users->links() }} --}}
</div>
@endsection 
