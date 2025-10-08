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

        // 不是店長但想進帳號管理頁
        if (Session::get('power') != 1 && $request->route()->getName() == 'admin.admins') {
            return redirect()->route('admin.index')->with('error', '你沒有權限');
        }

        return $next($request);
    }
}
