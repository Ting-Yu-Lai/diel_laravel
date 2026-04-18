<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\LoginRequest;
use App\Models\Admin;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function __construct(
        private AdminService $adminService,
    ) {}

    /**
     * 顯示登入表單
     */
    public function loginForm()
    {
        // dd("hi");
        return view('backend.login');
    }

    /**
     * 登入流程驗證
     */
    public function login(LoginRequest $request)
    {
        $admin = $this->adminService->login($request->username, $request->password);

        if (!$admin) {
            return back()->withErrors(['username' => '帳號或密碼錯誤']);
        }

        return redirect()->route('backend.index');
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
        $admins = $this->adminService->getAll();
        return view('backend.admin.index', compact('admins'));
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
