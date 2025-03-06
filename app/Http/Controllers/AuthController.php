<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'required|in:male,female',
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'role' => $request->role ?? 'user', // Mặc định là user
        ]);
    
        // Tạo token và lưu vào session
        $token = $user->createToken('auth_token')->plainTextToken;
        session(['auth_token' => $token]);
    
        return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
    }
    
    

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }
    
        $user = Auth::user();
        
        // Tạo token và lưu vào session
        $token = $user->createToken('auth_token')->plainTextToken;
        session(['auth_token' => $token]);
        // Log::info('User role: ' . $user->role);
    
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công (Admin)');
        }
    
        return redirect()->route('users.dashboard')->with('success', 'Đăng nhập thành công (User)');
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
