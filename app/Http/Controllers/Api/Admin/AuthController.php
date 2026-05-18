<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(
        private readonly AdminService $adminService,
    ) {}

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = $this->adminService->findByUsername($request->username);

        if (!$admin || !Hash::check($request->password, $admin->password_hash)) {
            return response()->json(['message' => '帳號或密碼錯誤。'], 401);
        }

        $token = JWTAuth::guard('api_admin')->fromUser($admin);

        $this->adminService->updateLastLogin($admin->admin_id);

        return $this->tokenResponse($token);
    }

    public function logout(): JsonResponse
    {
        JWTAuth::guard('api_admin')->invalidate(JWTAuth::guard('api_admin')->getToken());

        return response()->json(['message' => '已成功登出。']);
    }

    public function refresh(): JsonResponse
    {
        $token = JWTAuth::guard('api_admin')->refresh();

        return $this->tokenResponse($token);
    }

    public function me(): JsonResponse
    {
        $admin = JWTAuth::guard('api_admin')->user();

        return response()->json(new AdminResource($admin));
    }

    private function tokenResponse(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => JWTAuth::guard('api_admin')->factory()->getTTL() * 60,
        ]);
    }
}
