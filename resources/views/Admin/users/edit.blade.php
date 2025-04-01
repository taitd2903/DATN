@extends('layouts.layout')

@section('content')





        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Edit User</h4>
                            </div>
                            <div class="content">
                                <!-- Hiển thị lỗi nếu có -->
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Form sửa người dùng -->
                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    
                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select name="role" id="role" class="form-control" required>
                                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>staff</option>
                                        </select>
                                    </div>
                                
                                    

                                    <button type="submit" class="btn btn-info btn-fill pull-right">Update User</button>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>

                   
                </div>
            </div>
        </div>

        


@endsection
