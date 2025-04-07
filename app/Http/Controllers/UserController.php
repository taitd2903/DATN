<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
     // Hiển thị danh sách users
     public function index(Request $request)
     {
        $query = User::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
    
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
    
        if ($request->filled('status')) {
            $query->where('status', $request->status); 
        }
    
        $users = $query->get();
    
        return view('admin.users.index', compact('users'));
     }

     // Hiển thị form tạo user
     public function create()
     {
         return view('admin.users.create'); // Tạo view để nhập thông tin người dùng mới
     }

     /**
      * Store a newly created user in the database.
      *
      * @param  \Illuminate\Http\Request  $request
      * @return \Illuminate\Http\Response
      */
      public function store(Request $request)
    {
        // Xác thực dữ liệu
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Xác thực ảnh
            'address' => 'nullable|string',
            'role' => 'nullable|in:user,admin',
        ]);

        // Nếu có ảnh, tải lên và lưu đường dẫn
        if ($request->hasFile('image')) {
            // Lưu ảnh vào thư mục 'public/images' và lấy đường dẫn
            $imagePath = $request->file('image')->store('images', 'public');
        } else {
            // Nếu không có ảnh, sử dụng ảnh mặc định
            $imagePath = 'images/default_image.jpg';
        }

        // Tạo người dùng mới
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = $validated['password'];
        $user->phone = $validated['phone'];
        $user->image = $imagePath; // Lưu đường dẫn ảnh vào database
        $user->address = $validated['address'];

        $user->ward = $validated['ward'];
        $user->district = $validated['district'];
        $user->city = $validated['city'];
        $user->role = $validated['role'] ?? 'user';
        $user->save(); // Lưu vào DB

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

     // Hiển thị form chỉnh sửa user
     public function edit($id)
     {
         $user = User::findOrFail($id); // Tìm người dùng theo ID
         return view('admin.users.edit', compact('user'));
     }


     // Cập nhật dữ liệu user
     public function update(Request $request, $id)
{
    if (auth()->id() == $id) {
        return redirect()->back()->with('error', 'Bạn không thể thay đổi quyền của chính mình.');
    }
    $request->validate([

        'role' => 'required|in:user,admin,staff',
    ]);

    $user = User::findOrFail($id);

    // Cập nhật dữ liệu người dùng

    $user->role = $request->role;



    $user->save(); // Lưu dữ liệu

    return redirect()->route('admin.users.index')->with('success', 'cập nhật thành công');
}


     // Xóa user
     public function destroy($id)
     {
         // Tìm người dùng theo ID
         $user = User::findOrFail($id);

         // Đếm số lượng admin hiện có
         $adminCount = User::where('role', 'admin')->count();

         // Nếu chỉ còn 1 admin và user này là admin, không cho xóa
         if ($adminCount <= 1 && $user->role === 'admin') {
             return redirect()->back()->with('error', 'Không thể xóa tài khoản này vì đây là admin duy nhất.');
         }

         // Nếu người dùng có ảnh, xóa ảnh khỏi thư mục lưu trữ
         if ($user->image) {
             $imagePath = public_path('storage/' . $user->image);
             if (file_exists($imagePath)) {
                 unlink($imagePath); // Xóa file ảnh
             }
         }

         // Xóa người dùng khỏi cơ sở dữ liệu
         $user->delete();

         // Chuyển hướng lại trang danh sách với thông báo thành công
         return redirect()->route('admin.users.index')->with('success', 'xóa người dùng thành công');
     }



public function editProfile()
{
    $breadcrumbs = [
    ['name' => 'Trang chủ', 'url' => route('home')],
    ['name' => 'Thông tin cá nhân', 'url' => null],
    ['name' => 'Cập nhật', 'url' => null],
];
    $user = Auth::user(); // Lấy thông tin người dùng đang đăng nhập
    return view('users.profile.edit', compact('user', 'breadcrumbs')); // Trả về view chỉnh sửa
}

public function updateProfile(Request $request)
{
    $user = Auth::user(); // Lấy thông tin người dùng đang đăng nhập



    $request->validate([
        'name' => 'required|string|min:3|max:55|regex:/^[a-zA-ZÀ-Ỹà-ỹ0-9\s]+$/u',
        'email' => [
            'required',
            'email',
            'unique:users,email,' . $user->id,
            'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
        ],
        'phone' => [
            'nullable',
            'regex:/^(0|\+84)[1-9][0-9]{8}$/',
            'max:15',
        ],
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'address' => 'nullable|string|max:50',

        'ward' => 'nullable|string|max:100',
        'district' => 'nullable|string|max:100',
        'city' => 'nullable|string|max:100',
    ], [
        'name.required' => 'Tên không được để trống.',
        'name.min' => 'Tên phải có ít nhất 3 ký tự.',
        'name.max' => 'Tên không được vượt quá 55 ký tự.',
        'name.regex' => 'Tên chỉ được chứa chữ cái, số và dấu cách.',
        'email.required' => 'Email không được để trống.',
        'email.email' => 'Email không đúng định dạng.',
        'email.unique' => 'Email đã tồn tại, vui lòng chọn email khác.',
        'email.regex' => 'Email không hợp lệ, vui lòng kiểm tra lại.',
        'phone.regex' => 'Số điện thoại phải bắt đầu bằng 0 hoặc +84 và có 10 chữ số.',
        'image.image' => 'File phải là hình ảnh.',
        'image.mimes' => 'Ảnh phải là định dạng: jpeg, png, jpg, gif, svg.',
        'image.max' => 'Ảnh không được vượt quá 2MB.',
    ]);


    // Cập nhật thông tin
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->address = $request->address;

    $user->ward = $request->ward;
    $user->district = $request->district;
    $user->city = $request->city;

    // Xử lý ảnh mới
    if ($request->hasFile('image')) {
        if ($user->image) {
            $oldImagePath = public_path('storage/' . $user->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $imagePath = $request->file('image')->store('images', 'public');
        $user->image = $imagePath;
    }

    $user->save(); // Lưu dữ liệu

    return redirect('/')->with('success', 'cập nhật tài khoản thành công!');

}
public function getDistricts($city)
{
    $districts = Location::where('city', $city)->pluck('name');
    return response()->json($districts);
}

public function transferAdmin(Request $request)
{
    $newAdminId = $request->input('new_admin_id');

    // Kiểm tra user mới có tồn tại không
    $newAdmin = User::find($newAdminId);
    if (!$newAdmin) {
        return redirect()->back()->with('error', 'Người dùng không hợp lệ.');
    }

    // Chuyển quyền admin cho user mới
    $newAdmin->role = 'admin';
    $newAdmin->save();

    return redirect()->route('admin.users.index')->with('success', 'Chuyển quyền admin thành công.');
}

public function toggleStatus($id)
{
    $user = User::findOrFail($id);
    if (auth()->user()->id === $user->id) {
        return redirect()->back()->with('error', 'Bạn không thể tự khóa chính mình.');
    }
    $user->status = $user->status === 'active' ? 'banned' : 'active';
    $user->save();

    return redirect()->back()->with('success', 'Cập nhật trạng thái tài khoản thành công.');
}
public function ban(Request $request, $id)
{
    $request->validate([
        'ban_reason' => 'required|string|max:255',
    ]);

    $user = User::findOrFail($id);
    $user->status = 'banned';
    $user->ban_reason = $request->ban_reason;
    $user->save();

    return redirect()->back()->with('success', 'Đã khóa tài khoản với lý do: ' . $request->ban_reason);
}


}
