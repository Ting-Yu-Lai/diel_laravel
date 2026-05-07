<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberProfileRequest;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(
        private readonly MemberService $memberService,
    ) {}

    public function loginForm(): View
    {
        return view('front.member.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $member = $this->memberService->login($validated['login'], $validated['password']);

        if (!$member) {
            return back()->withErrors(['login' => '帳號或密碼錯誤。'])->withInput();
        }

        Auth::guard('member')->login($member);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('member.loginForm');
    }

    public function registerForm(): View
    {
        return view('front.member.register');
    }

    public function register(StoreMemberRequest $request): RedirectResponse
    {
        $member = $this->memberService->register($request->validated());

        Auth::guard('member')->login($member);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard')->with('success', '註冊成功，歡迎加入！');
    }

    public function dashboard(): View
    {
        $member    = Auth::guard('member')->user();
        $loginLogs = $this->memberService->getRecentLoginLogs(Auth::guard('member')->id());

        return view('front.member.dashboard', compact('member', 'loginLogs'));
    }

    public function security(): View
    {
        return view('front.member.security');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password'          => 'required|string',
            'new_password'              => [
                'required', 'string', 'confirmed', 'min:8',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&^_\-]/',
            ],
        ]);

        $ok = $this->memberService->changePassword(
            Auth::guard('member')->id(),
            $request->input('current_password'),
            $request->input('new_password'),
        );

        if (!$ok) {
            return back()->withErrors(['current_password' => '目前密碼不正確，請重新輸入。']);
        }

        return redirect()->route('member.profile')->with('success', '密碼已成功更新，下次登入請使用新密碼。');
    }

    public function profile(): View
    {
        $member = $this->memberService->getProfile(Auth::guard('member')->id());

        return view('front.member.profile', compact('member'));
    }

    public function updateProfile(UpdateMemberProfileRequest $request): RedirectResponse
    {
        $this->memberService->updateProfile(
            Auth::guard('member')->id(),
            $request->validated(),
        );

        return redirect()->route('member.profile')->with('success', '個人資料已更新。');
    }

    public function treatments(): View
    {
        $member   = $this->memberService->getProfile(Auth::guard('member')->id());
        $customer = $member?->customer;

        $records = $customer
            ? $customer->treatmentRecords()
                       ->with(['items.treatment'])
                       ->orderByDesc('record_date')
                       ->get()
            : collect();

        return view('front.member.treatments', compact('records'));
    }

    public function followUps(): View
    {
        $member   = $this->memberService->getProfile(Auth::guard('member')->id());
        $customer = $member?->customer;

        $records = $customer
            ? $customer->treatmentRecords()
                       ->with([
                           'items.treatment',
                           'items.followUp.logs.photos',
                           'items.followUp.preOpPhotos',
                           'items.followUp.postOpPhotos',
                       ])
                       ->orderByDesc('record_date')
                       ->get()
            : collect();

        return view('front.member.follow-ups', compact('records'));
    }
}
