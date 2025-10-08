<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [FrontController::class, 'index']);

// 登入表單
Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.loginForm');
// 處理登入
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
// 登出
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// 後台首頁
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');