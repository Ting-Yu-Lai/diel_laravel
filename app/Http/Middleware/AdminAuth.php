<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //判斷是否登入，無導向登入頁
        if (!Session::has('admin_id')) {
            return redirect()->route('admin.loginForm');
        }

        // 僅超級管理員 (power=2) 可訪問的路由
        $superAdminOnly = [
            'backend.admin.index',
            'backend.admin.create',
            'backend.admin.store',
            'backend.admin.edit',
            'backend.admin.update',
            'backend.admin.destroy',
            'backend.job-title.index',
            'backend.job-title.create',
            'backend.job-title.store',
            'backend.job-title.edit',
            'backend.job-title.update',
            'backend.job-title.destroy',
        ];

        if (Session::get('power') < Admin::ROLE_SUPER_ADMIN && in_array($request->route()->getName(), $superAdminOnly)) {
            return redirect()->route('backend.index')->with('error', '你沒有權限，需要超級管理員身份');
        }

        return $next($request);
    }
}
