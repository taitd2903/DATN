<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function showResetPasswordForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
            'password' => [
        'required',
        'string',
        'min:6',
        'max:20',
        'confirmed',
        'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
    ],

],
    [
        'password.required' => 'Mật khẩu không được để trống.',
        'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        'password.max' => 'Mật khẩu không được quá 20 ký tự.',
        'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        'password.regex' => 'Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc biệt.',
        'email.required' => 'Email không được để trống.',
        'email.email' => 'Email phải đúng định dạng.',
        'email.exists' => 'Email không tồn tại trong hệ thống.',
    ]

    );

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
