<?php

use App\Http\Controllers\LineController;
use App\Http\Middleware\AdminAuth;
use Illuminate\Support\Facades\Route;

// LINE Messaging API Webhook（CSRF exempt，由 LINE Platform 呼叫）
// 在 LINE Developers Console 填入：https://your-domain.com/api/line/webhook
Route::post('/line/webhook', [LineController::class, 'webhook'])->name('line.webhook');

// 後台手動提醒：掛 web middleware 以啟用 Session（AdminAuth 需讀取 Session）
Route::post('/line/remind', [LineController::class, 'remind'])
    ->middleware(['web', AdminAuth::class])
    ->name('line.remind');
