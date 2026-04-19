<?php

namespace App\Http\Middleware;

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

        // 僅 power==1 可訪問的路由
        $power1Only = [
            'backend.admin.index',
            'backend.job-title.index',
            'backend.job-title.create',
            'backend.job-title.store',
            'backend.job-title.edit',
            'backend.job-title.update',
            'backend.job-title.destroy',
        ];

        if (Session::get('power') != 1 && in_array($request->route()->getName(), $power1Only)) {
            return redirect()->route('backend.index')->with('error', '你沒有權限');
        }

        return $next($request);
    }
}
