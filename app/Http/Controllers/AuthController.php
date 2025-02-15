<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user', // Mặc định là user
        ]);
    
        return redirect()->route('login')->with('success', 'Đăng ký thành công!');
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
    Auth::logout();
    return redirect()->route('login')->with('success', 'Đăng xuất thành công!');
}


    // public function logout()
    // {
    //     Auth::user()->tokens()->delete();
    //     Auth::logout();

    //     return redirect()->route('login')->with('success', 'Đăng xuất thành công!');
    // }
}
