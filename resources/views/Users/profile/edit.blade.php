@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<div class="container">
    <div class="profile-wrapper">
        <h2>Chỉnh sửa hồ sơ</h2>

        @if(session('success'))
            <div class="alert success-message">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="input-box">
                <label for="name"><i class="fa-solid fa-user"></i> Tên</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-box">
                <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-box">
                <label for="phone"><i class="fa-solid fa-phone"></i> Số điện thoại</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                @error('phone')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-box">
                <label for="province"><i class="fa-solid fa-map-marker-alt"></i> Tỉnh/Thành phố</label>
                <select id="province" name="city" required {{ $user->city ? 'disabled' : '' }}>
                    <option value="" selected disabled>Chọn tỉnh/thành phố</option>
                </select>
            </div>

            <div class="input-box">
                <label for="district"><i class="fa-solid fa-building"></i> Quận/Huyện</label>
                <select id="district" name="district" required {{ $user->district ? 'disabled' : '' }}>
                    <option value="" selected disabled>Chọn quận/huyện</option>
                </select>
            </div>

            <div class="input-box">
                <label for="ward"><i class="fa-solid fa-home"></i> Xã/Phường</label>
                <select id="ward" name="ward" required disabled>
                    <option value="" {{ !$user->ward ? 'selected' : '' }}>Chọn xã/phường</option>
                </select>
            </div>

            <div class="input-box">
                <label for="address_detail"><i class="fa-solid fa-location-dot"></i> Địa chỉ cụ thể</label>
                <input type="text" id="address_detail" name="address" value="{{ old('address', $user->address) }}" required disabled>

            </div>

            <button type="button" class="btn" id="editAddress">Chỉnh sửa địa chỉ</button>
            <div class="input-box">
                <label for="gender"><i class="fa-solid fa-venus-mars"></i> Giới tính</label>
                <input type="text" id="gender" name="gender" value="{{ $user->gender === 'male' ? 'Nam' : 'Nữ' }}" readonly>
            </div>

            <div class="input-box">
                <label for="image"><i class="fa-solid fa-image"></i> Ảnh đại diện</label>
                <input type="file" id="image" name="image">
                @if ($user->image)
                    <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" width="100" class="mt-2">
                @endif
                @error('image')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn">Cập nhật</button>
        </form>

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <p class="error">{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    // Load tỉnh/thành phố
    fetch("https://provinces.open-api.vn/api/p/")
        .then(response => response.json())
        .then(data => {
            const provinceSelect = document.getElementById("province");
            data.forEach(province => {
                let option = new Option(province.name, province.code);
                provinceSelect.add(option);
                if (province.code == "{{ $user->city }}") {
                    option.selected = true;
                    loadDistricts(province.code);
                }
            });
        });
    
    // Load quận/huyện
    function loadDistricts(cityCode) {
        fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                const districtSelect = document.getElementById("district");
                districtSelect.innerHTML = '<option value="" disabled selected>Chọn quận/huyện</option>';
                data.districts.forEach(district => {
                    let option = new Option(district.name, district.code);
                    districtSelect.add(option);
                    if (district.code == "{{ $user->district }}") {
                        option.selected = true;
                        loadWards(district.code);
                    }
                });
            });
    }
    
    // Load xã/phường
    function loadWards(districtCode) {
        fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                const wardSelect = document.getElementById("ward");
                wardSelect.innerHTML = '<option value="" disabled selected>Chọn xã/phường</option>';
                data.wards.forEach(ward => {
                    let option = new Option(ward.name, ward.code);
                    wardSelect.add(option);
                    if (ward.code == "{{ $user->ward }}") {
                        option.selected = true;
                    }
                });
            });
    }
    
    // Bật/tắt chỉnh sửa địa chỉ
    const editButton = document.getElementById('editAddress');
    editButton.addEventListener('click', () => {
        document.querySelectorAll('#province, #district, #ward, #address_detail').forEach(el => el.disabled = false);
        editButton.style.display = 'none';
    
        document.getElementById('province').addEventListener('change', (e) => {
            loadDistricts(e.target.value);
        });
    
        document.getElementById('district').addEventListener('change', (e) => {
            loadWards(e.target.value);
        });
    });
    </script>
    
    <style>
    select {
        background-color: #fff;
        color: #333;
        cursor: pointer;
    }
    select option[disabled] {
        color: #bbb;
        font-style: italic;
    }
    .btn {
        margin-top: 10px;
        background-color: #4caf50;
        color: #fff;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
    }
    </style>
@endsection