<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminService $adminService,
    ) {}

    public function loginForm()
    {
        return view('backend.login');
    }

    public function login(LoginRequest $request)
    {
        $admin = $this->adminService->login($request->username, $request->password);

        if (!$admin) {
            return back()->withErrors(['username' => '帳號或密碼錯誤']);
        }

        return redirect()->route('backend.index');
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('admin.loginForm');
    }

    public function index()
    {
        $admins = $this->adminService->getAll();
        return view('backend.admin.index', compact('admins'));
    }

    public function create()
    {
        return view('backend.admin.create');
    }

    public function store(StoreAdminRequest $request)
    {
        $this->adminService->create($request->validated());
        return redirect()->route('backend.admin.index')->with('success', '管理者帳號已新增');
    }

    public function show(int $id)
    {
        return redirect()->route('backend.admin.edit', $id);
    }

    public function edit(int $id)
    {
        $admin = $this->adminService->findById($id);
        return view('backend.admin.edit', compact('admin'));
    }

    public function update(UpdateAdminRequest $request, int $id)
    {
        $this->adminService->update($id, $request->validated());
        return redirect()->route('backend.admin.index')->with('success', '帳號資料已更新');
    }

    public function destroy(Request $request, int $id)
    {
        if (Session::get('power') != 1) {
            return back()->with('error', '你沒有權限執行此操作');
        }

        $admin = $this->adminService->findById($id);

        if ($admin->id === Session::get('admin_id')) {
            return back()->with('error', '無法刪除自己的帳號');
        }

        $this->adminService->delete($id);

        return redirect()->route('backend.admin.index')
            ->with('success', "帳號「{$admin->username}」已刪除");
    }
}
