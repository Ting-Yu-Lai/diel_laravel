<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtAdminAuth
{
    public function handle(Request $request, Closure $next, string $requiredPower = null): Response
    {
        try {
            $admin = JWTAuth::guard('api_admin')->parseToken()->authenticate();
        } catch (TokenExpiredException) {
            return response()->json(['message' => 'Token 已過期，請重新登入。'], 401);
        } catch (TokenInvalidException) {
            return response()->json(['message' => 'Token 無效。'], 401);
        } catch (JWTException) {
            return response()->json(['message' => '未提供 Token。'], 401);
        }

        if (!$admin) {
            return response()->json(['message' => '帳號不存在。'], 401);
        }

        // 可選的權限等級檢查（傳入 'superadmin' 時要求 power == 1）
        if ($requiredPower === 'superadmin' && $admin->power < Admin::ROLE_SUPER_ADMIN) {
            return response()->json(['message' => '權限不足，需要超級管理員權限。'], 403);
        }

        if ($requiredPower === 'manager' && $admin->power < Admin::ROLE_MANAGER) {
            return response()->json(['message' => '權限不足，需要店長以上權限。'], 403);
        }

        return $next($request);
    }
}
