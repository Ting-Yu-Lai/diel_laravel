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
            return redirect()->route('member.dashboard');
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

    public function registerForm()
    {
        return view('members.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:members,username',
            'email' => 'required|email|max:100|unique:members,email',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // 建立帳號
        $member = Member::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'full_name' => $validated['full_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        // 自動登入
        Auth::guard('member')->login($member);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard')->with('success', '註冊成功！');
    }

    public function dashboard()
    {
        // 可以傳資料給 view，如果沒有資料也可以只回傳 view
        return view('members.dashboard');
    }
}
