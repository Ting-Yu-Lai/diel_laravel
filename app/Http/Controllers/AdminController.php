<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    /**
     * 顯示登入表單
     */
    public function loginForm()
    {
        return view('admin.login');
    }

    /**
     * 登入流程驗證
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password_hash)) {
            Session::put('admin_id', $admin->admin_id);
            Session::put('username', $admin->username);
            Session::put('power', $admin->power);

            $admin->last_login_at = now();
            $admin->save();

            return redirect()->route('admin.index');
        };
        return back()->withErrors(['username' => '帳號或密碼錯誤']);
    }

    /**
     * 管理登出
     */
    public function logout()
    {
        Session::flush();
        return redirect()->route('admin.loginForm');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 進入後台驗證是否存在session
        if(!Session::has('admin_id')) {
            return redirect()->route('admin.loginForm');
        }

        return view('admin.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
