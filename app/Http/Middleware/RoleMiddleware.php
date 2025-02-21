<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Kiểm tra xem user có đăng nhập không
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập trước.');
        }

        // Kiểm tra vai trò của user
        if (Auth::user()->role !== $role) {
            return abort(403, 'Bạn không có quyền truy cập vào trang này.');
        }

        return $next($request);
    }
}
