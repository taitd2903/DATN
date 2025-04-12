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
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" readonly>
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

            <div class="form-group">
                <label for="province">Tỉnh/Thành phố</label>
                <select id="province" name="city" class="form-control">
                    <option value="" disabled selected>Chọn tỉnh/thành phố</option>
                </select>
            </div>
        
            <div class="form-group">
                <label for="district">Quận/Huyện</label>
                <select id="district" name="district" class="form-control">
                    <option value="" disabled selected>Chọn quận/huyện</option>
                </select>
            </div>
        
            <div class="form-group">
                <label for="ward">Xã/Phường</label>
                <select id="ward" name="ward" class="form-control">
                    <option value="" disabled selected>Chọn xã/phường</option>
                </select>
            </div>
        
            <div class="form-group">
                <label for="address">Địa chỉ chi tiết</label>
                <input type="text" id="address" name="address" class="form-control" value="{{ $user->address ?? '' }}">
                <div id="addressError" class="error" style="display: none; color: red; margin-top: 5px;"></div>
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
            
            <a href="{{ route('password.form') }}">đổi mật khẩu</a>
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
    // Hàm hiển thị loading indicator (tùy chọn)
    function showLoading(selectElement, isLoading) {
        if (isLoading) {
            selectElement.disabled = true;
            selectElement.innerHTML = '<option value="" disabled selected>Đang tải...</option>';
        } else {
            selectElement.disabled = false;
        }
    }

    // Load tỉnh/thành phố
    document.addEventListener('DOMContentLoaded', () => {
        const provinceSelect = document.getElementById("province");
        const districtSelect = document.getElementById("district");
        const wardSelect = document.getElementById("ward");
        const addressInput = document.getElementById("address");
        const form = document.querySelector("form");
        const addressError = document.getElementById("addressError");

        // Kiểm tra xem các phần tử có được tìm thấy không
        if (!provinceSelect || !districtSelect || !wardSelect || !addressInput || !form || !addressError) {
            console.error("Không tìm thấy một hoặc nhiều phần tử trong form. Vui lòng kiểm tra ID.");
            return;
        }

        // Hiển thị loading
        showLoading(provinceSelect, true);

        fetch("https://provinces.open-api.vn/api/p/")
            .then(response => {
                if (!response.ok) throw new Error('Không thể tải danh sách tỉnh/thành phố');
                return response.json();
            })
            .then(data => {
                provinceSelect.innerHTML = '<option value="" disabled selected>Chọn tỉnh/thành phố</option>';
                data.forEach(province => {
                    let option = new Option(province.name, province.code);
                    provinceSelect.add(option);
                    if (province.code == "{{ $user->city ?? '' }}") {
                        option.selected = true;
                        loadDistricts(province.code);
                    }
                });
            })
            .catch(error => {
                console.error(error);
                provinceSelect.innerHTML = '<option value="" disabled selected>Lỗi tải dữ liệu</option>';
            })
            .finally(() => {
                showLoading(provinceSelect, false);
            });

        // Thêm sự kiện change ngay từ đầu
        provinceSelect.addEventListener('change', (e) => {
            districtSelect.innerHTML = '<option value="" disabled selected>Chọn quận/huyện</option>';
            wardSelect.innerHTML = '<option value="" disabled selected>Chọn xã/phường</option>';
            if (e.target.value) {
                loadDistricts(e.target.value);
            }
        });

        districtSelect.addEventListener('change', (e) => {
            wardSelect.innerHTML = '<option value="" disabled selected>Chọn xã/phường</option>';
            if (e.target.value) {
                loadWards(e.target.value);
            }
        });

        // Hàm kiểm tra tính hợp lệ của địa chỉ
        function validateAddress() {
            const provinceValue = provinceSelect.value;
            const districtValue = districtSelect.value;
            const wardValue = wardSelect.value;
            const addressValue = addressInput.value.trim();

            // Debug: In giá trị của các trường
            console.log("Province:", provinceValue);
            console.log("District:", districtValue);
            console.log("Ward:", wardValue);
            console.log("Address:", addressValue);

            // Đếm số lượng trường được điền
            const filledCount = [provinceValue, districtValue, wardValue, addressValue].filter(value => value !== '').length;

            console.log("Filled count:", filledCount);

            if (filledCount > 0 && filledCount < 4) {
                addressError.style.display = 'block';
                addressError.textContent = 'Bạn phải chọn đầy đủ 4 mục trong phần Địa chỉ chi tiết (Tỉnh/Thành phố, Quận/Huyện, Xã/Phường, Địa chỉ chi tiết) hoặc để trống tất cả!';
                return false;
            } else {
                addressError.style.display = 'none';
                return true;
            }
        }

        // Thêm sự kiện submit cho form
        form.addEventListener('submit', function(event) {
            console.log("Form submitted, validating address...");
            if (!validateAddress()) {
                event.preventDefault(); // Ngăn form gửi nếu không hợp lệ
            }
        });
    });

    // Load quận/huyện
    function loadDistricts(cityCode) {
        const districtSelect = document.getElementById("district");
        showLoading(districtSelect, true);

        fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`)
            .then(response => {
                if (!response.ok) throw new Error('Không thể tải danh sách quận/huyện');
                return response.json();
            })
            .then(data => {
                districtSelect.innerHTML = '<option value="" disabled selected>Chọn quận/huyện</option>';
                data.districts.forEach(district => {
                    let option = new Option(district.name, district.code);
                    districtSelect.add(option);
                    if (district.code == "{{ $user->district ?? '' }}") {
                        option.selected = true;
                        loadWards(district.code);
                    }
                });
            })
            .catch(error => {
                console.error(error);
                districtSelect.innerHTML = '<option value="" disabled selected>Lỗi tải dữ liệu</option>';
            })
            .finally(() => {
                showLoading(districtSelect, false);
            });
    }

    // Load xã/phường
    function loadWards(districtCode) {
        const wardSelect = document.getElementById("ward");
        showLoading(wardSelect, true);

        fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
            .then(response => {
                if (!response.ok) throw new Error('Không thể tải danh sách xã/phường');
                return response.json();
            })
            .then(data => {
                wardSelect.innerHTML = '<option value="" disabled selected>Chọn xã/phường</option>';
                data.wards.forEach(ward => {
                    let option = new Option(ward.name, ward.code);
                    wardSelect.add(option);
                    if (ward.code == "{{ $user->ward ?? '' }}") {
                        option.selected = true;
                    }
                });
            })
            .catch(error => {
                console.error(error);
                wardSelect.innerHTML = '<option value="" disabled selected>Lỗi tải dữ liệu</option>';
            })
            .finally(() => {
                showLoading(wardSelect, false);
            });
    }
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