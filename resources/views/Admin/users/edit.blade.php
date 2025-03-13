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

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone</label>
                                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="image">Image</label>
                                                <input type="file" name="image" id="image" class="form-control">
                                                @if ($user->image)
                                                    <p>Current Image:</p>
                                                    <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" width="150" height="150">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div>
                                            <label for="province">Tỉnh/Thành phố:</label>
                                            <select id="province" name="city" onchange="loadDistricts()">
                                                <option value="">Chọn tỉnh/thành phố</option>
                                            </select>
                                            <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name', $user->city) }}">
                                        </div>
                                        
                                        <div>
                                            <label for="district">Quận/Huyện:</label>
                                            <select id="district" name="district" onchange="loadWards()">
                                                <option value="">Chọn quận/huyện</option>
                                            </select>
                                            <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name', $user->district) }}">
                                        </div>
                                        
                                        <div>
                                            <label for="ward">Xã/Phường:</label>
                                            <select id="ward" name="ward">
                                                <option value="">Chọn xã/phường</option>
                                            </select>
                                            <input type="hidden" name="ward_name" id="ward_name" value="{{ old('ward_name', $user->ward) }}">
                                        </div>
                                        
                                    
                                        <div>
                                            <label for="address">Địa chỉ cụ thể *</label>
                                            <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}">
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="gender">Giới tính</label>
                                        <input type="text" name="gender" id="gender" value="{{ $user->gender === 'male' ? 'Nam' : 'Nữ' }}" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select name="role" id="role" class="form-control" required>
                                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </div>
                                
                                    

                                    <button type="submit" class="btn btn-info btn-fill pull-right">Update User</button>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-user">
                            <div class="image img-bgr-users">
                                <img src="https://ununsplash.imgix.net/photo-1431578500526-4d9613015464?fit=crop&fm=jpg&h=300&q=75&w=400" alt="..."/>
                            </div>
                            <div class="content">
                                <div class="author">
                                     <a href="#">
                                    <img class="avatar border-gray" src="{{ asset('storage/' . $user->image) }}" alt="..."/>
                                      <h4 class="title">{{ $user->name }}<br />
                                         <small>{{ $user->email }}</small>
                                      </h4>
                                    </a>
                                </div>
                                <p class="description text-center">"{{$user->description}}"</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div> 
    
    
            {{-- ================== --}}
            <script>
            document.addEventListener("DOMContentLoaded", function () {
            const userCity = "{{ $user->city }}";
            const userDistrict = "{{ $user->district }}";
            const userWard = "{{ $user->ward }}";
        
            fetch("https://provinces.open-api.vn/api/p/")
                .then(response => response.json())
                .then(data => {
                    let provinceSelect = document.getElementById("province");
                    data.forEach(province => {
                        let option = new Option(province.name, province.code);
                        provinceSelect.add(option);
                        if (province.code == userCity) option.selected = true;
                    });
                    if (userCity) loadDistricts(userCity);
                });
        
            function loadDistricts(cityCode) {
                fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        let districtSelect = document.getElementById("district");
                        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                        data.districts.forEach(district => {
                            let option = new Option(district.name, district.code);
                            districtSelect.add(option);
                            if (district.code == userDistrict) option.selected = true;
                        });
                        if (userDistrict) loadWards(userDistrict);
                    });
            }
        
            function loadWards(districtCode) {
                fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        let wardSelect = document.getElementById("ward");
                        wardSelect.innerHTML = '<option value="">Chọn xã/phường</option>';
                        data.wards.forEach(ward => {
                            let option = new Option(ward.name, ward.code);
                            wardSelect.add(option);
                            if (ward.code == userWard) option.selected = true;
                        });
                    });
            }
        });
        
        
        </script></div>


@endsection
