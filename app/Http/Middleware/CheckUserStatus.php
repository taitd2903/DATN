<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

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
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->status === 'banned') {
                Session::flash('ban_reason', $user->ban_reason);
                Auth::logout();

                return redirect()->route('login');
            }
        }

        return $next($request);
    }
    
}
