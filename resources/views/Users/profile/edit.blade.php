@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chỉnh sửa hồ sơ</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
        </div>

        <div class="mb-3">
            <div>
                <label for="province">Tỉnh/Thành phố:</label>
                <select id="province" name="city" onchange="loadDistricts()">
                    <option value="">Chọn tỉnh/thành phố</option>
                </select>
                <input type="hidden" name="province_name" id="province_name">
            </div>

            <div>
                <label for="district">Quận/Huyện:</label>
                <select id="district" name="district" onchange="loadWards()">
                    <option value="">Chọn quận/huyện</option>
                </select>
                <input type="hidden" name="district_name" id="district_name">
            </div>

            <div>
                <label for="ward">Xã/Phường:</label>
                <select id="ward" name="ward">
                    <option value="">Chọn xã/phường</option>
                </select>
                <input type="hidden" name="ward_name" id="ward_name">
            </div>


            <div>
                <label for="address">Địa chỉ cụ thể *</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}">
            </div>

            
        </div>
        <div> 
            <label for="gender">Giới tính:</label>
            <select name="gender" required>
                <option value="male" {{ auth()->user()->gender == 'male' ? 'selected' : '' }}>Nam</option>
                <option value="female" {{ auth()->user()->gender == 'female' ? 'selected' : '' }}>Nữ</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh đại diện</label>
            <input type="file" class="form-control" id="image" name="image">
            @if ($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" width="100" class="mt-2">
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
@endsection
