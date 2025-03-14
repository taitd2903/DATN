<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem user có bị khóa không
        if (Auth::check() && Auth::user()->status === 'banned') {
            Auth::logout();
            return redirect('/login')->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ admin.');
        }

        return $next($request);
    }
}
