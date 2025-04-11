<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = Auth::user();

        // Nếu là admin thì bỏ qua kiểm tra quyền
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Kiểm tra quyền
        $permissions = $user->permissions ?? [];
        if (in_array($permission, $permissions)) {
            return $next($request);
        }

        // Không có quyền → hiện alert và quay lại
        return response()->view('no_permission');
    }
    
}
