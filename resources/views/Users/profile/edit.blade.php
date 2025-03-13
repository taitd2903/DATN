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
            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
            @error('phone')
            <span class="text-danger">{{ $message }}</span>
            @enderror
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
        

        <div class="mb-3">
            <label for="image" class="form-label">Ảnh đại diện</label>
            <input type="file" class="form-control" id="image" name="image">
            @if ($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" width="100" class="mt-2">
            @endif
            @error('image')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
<div> 
    
    
    {{-- ================== --}}
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
    

</script></div>
@endsection

