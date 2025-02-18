<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check() && Auth::user()-> role ==$role) {
            abort(403, 'bạn không có quyền truy cập');
        }
        return $next($request);
    }
}

