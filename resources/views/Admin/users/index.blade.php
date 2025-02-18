{{-- @extends('layouts.app')

@section('content') --}}
<div class="container">
    <h2>Quản lý tài khoản</h2>
    <a href="{{ route('users.create') }}">Create User</a>

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

                    <!-- Actions: Edit, Delete -->
                    
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
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
{{-- @endsection --}}
