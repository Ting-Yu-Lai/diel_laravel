<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\LineController;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\JwtAdminAuth;
use Illuminate\Support\Facades\Route;

// LINE Messaging API Webhook（CSRF exempt，由 LINE Platform 呼叫）
Route::post('/line/webhook', [LineController::class, 'webhook'])->name('line.webhook');

// 後台手動提醒：掛 web middleware 以啟用 Session（AdminAuth 需讀取 Session）
Route::post('/line/remind', [LineController::class, 'remind'])
    ->middleware(['web', AdminAuth::class])
    ->name('line.remind');

// ── JWT Admin API v1 ──────────────────────────────────────────────
Route::prefix('v1/admin')->group(function () {

    // 公開：登入取得 token
    Route::post('login', [AuthController::class, 'login'])->name('api.admin.login');

    // 受保護：需要有效 JWT token
    Route::middleware(JwtAdminAuth::class)->group(function () {
        Route::post('logout',  [AuthController::class, 'logout'])->name('api.admin.logout');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('api.admin.refresh');
        Route::get('me',       [AuthController::class, 'me'])->name('api.admin.me');

        // 示範受保護資源
        Route::get('customers',     [CustomerController::class, 'index'])->name('api.admin.customers.index');
        Route::get('customers/{id}', [CustomerController::class, 'show'])->name('api.admin.customers.show');
    });
});
