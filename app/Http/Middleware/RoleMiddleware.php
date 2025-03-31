<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Kiểm tra xem user có đăng nhập không
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập trước.');
        }
    
        // Kiểm tra vai trò của user có nằm trong danh sách roles không
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }
    
        // Nếu không phải vai trò được yêu cầu, trả về lỗi 403
        return abort(403, 'Bạn không có quyền truy cập vào trang này.');
    }
}
