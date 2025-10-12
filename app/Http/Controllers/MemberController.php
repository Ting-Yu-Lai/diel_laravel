<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;

class MemberController extends Controller
{
    //
    public function loginForm()
    {
        return view('members.login');
    }
    // 處理登入請求
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        //判斷適用username或是email
        $loginField = filter_var($validated['login'], FILTER_VALIDATE_EMAIL) ? "email" : "username";

        $member = Member::where($loginField, $validated['login'])->first();

        if ($member && Hash::check($validated['password'], $member->password_hash)) {
            Auth::guard('member')->login($member);
            $request->session()->regenerate();
            return redirect()->intended('/member/dashboard');
        }

        return back()->withErrors([
            'login' => '帳號或密碼錯誤。',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/member/login');
    }
}
