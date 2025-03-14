<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    
    
    

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'exists:users,email' 
            ],
            'password' => [
            'required',
            'string',
            'min:6',
            'max:20',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,20}$/'
            ]

        ], [
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.'
        ]);
    
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }
    
        $user = Auth::user();
        
        // Tạo token và lưu vào session
        $token = $user->createToken('auth_token')->plainTextToken;
        session(['auth_token' => $token]);
        
    
        return redirect()->intended(route('Users.Products.index'))->with('success', 'Đăng nhập thành công!');


    }
    
    
    

    // Hiển thị Dashboard
    public function dashboard()
    {
        return view('dashboard');
    }

    // Đăng xuất
    public function logout()
    {
        // Xóa token của người dùng
        Auth::user()->tokens()->delete();
        
        // Xóa session token
        session()->forget('auth_token');
    
        Auth::logout();
        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất thành công.');
    }

    // chuyển từ admin sang user
    public function switchToUser()
{
    if (Auth::user()->role === 'admin') {
        session(['is_admin' => true]); // Lưu trạng thái admin vào session
        return redirect()->route('users.dashboard')->with('success', 'Bạn đang xem với tư cách User');
    }

    return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền thực hiện thao tác này.');
}
// hết code chuyển trang nha a

    
}
