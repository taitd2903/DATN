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
                <select id="province" name="city" onchange="loadDistricts()">
                    <option value="">Chọn tỉnh/thành phố</option>
                </select>
                <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name', $user->city) }}">
            </div>

            <div class="input-box">
                <label for="district"><i class="fa-solid fa-building"></i> Quận/Huyện</label>
                <select id="district" name="district" onchange="loadWards()">
                    <option value="">Chọn quận/huyện</option>
                </select>
                <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name', $user->district) }}">
            </div>

            <div class="input-box">
                <label for="ward"><i class="fa-solid fa-home"></i> Xã/Phường</label>
                <select id="ward" name="ward">
                    <option value="">Chọn xã/phường</option>
                </select>
                <input type="hidden" name="ward_name" id="ward_name" value="{{ old('ward_name', $user->ward) }}">
            </div>

            <div class="input-box">
                <label for="address"><i class="fa-solid fa-location-dot"></i> Địa chỉ cụ thể</label>
                <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}">
            </div>

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
document.addEventListener("DOMContentLoaded", function () {
    const userCity = "{{ $user->city }}";
    const userDistrict = "{{ $user->district }}";
    const userWard = "{{ $user->ward }}";

    const provinceSelect = document.getElementById("province");
    const districtSelect = document.getElementById("district");
    const wardSelect = document.getElementById("ward");

    fetch("https://provinces.open-api.vn/api/p/")
        .then(response => response.json())
        .then(data => {
            data.forEach(province => {
                let option = new Option(province.name, province.code);
                provinceSelect.add(option);
                if (province.code == userCity) {
                    option.selected = true;
                }
            });
            if (userCity) loadDistricts(userCity);
        });

    window.loadDistricts = function (cityCode) {
        fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                wardSelect.innerHTML = '<option value="">Chọn xã/phường</option>'; // Reset xã/phường khi chọn tỉnh mới

                data.districts.forEach(district => {
                    let option = new Option(district.name, district.code);
                    districtSelect.add(option);
                    if (district.code == userDistrict) {
                        option.selected = true;
                    }
                });

                document.getElementById("province_name").value = provinceSelect.options[provinceSelect.selectedIndex].text;

                if (userDistrict) loadWards(userDistrict);
            });
    };

    window.loadWards = function (districtCode) {
        fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
            .then(response => response.json())
            .then(data => {
                wardSelect.innerHTML = '<option value="">Chọn xã/phường</option>';
                data.wards.forEach(ward => {
                    let option = new Option(ward.name, ward.code);
                    wardSelect.add(option);
                    if (ward.code == userWard) {
                        option.selected = true;
                    }
                });

                document.getElementById("district_name").value = districtSelect.options[districtSelect.selectedIndex].text;
            });
    };

    provinceSelect.addEventListener("change", function () {
        let cityCode = this.value;
        document.getElementById("province_name").value = this.options[this.selectedIndex].text;
        if (cityCode) {
            loadDistricts(cityCode);
        }
    });

    districtSelect.addEventListener("change", function () {
        let districtCode = this.value;
        document.getElementById("district_name").value = this.options[this.selectedIndex].text;
        if (districtCode) {
            loadWards(districtCode);
        }
    });

    wardSelect.addEventListener("change", function () {
        document.getElementById("ward_name").value = this.options[this.selectedIndex].text;
    });
});
</script>
@endsection