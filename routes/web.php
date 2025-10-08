<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DessertController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CarouselController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminAuth;

// ---------- 前台 ----------

// 首頁
Route::get('/', [FrontController::class, 'index'])->name('front.index');

// 其他前台頁面可以在這裡加
// Route::get('/about', [FrontController::class, 'about'])->name('front.about');


// ---------- 後台 ----------

// 登入頁面
Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.loginForm');
// 登入處理
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
// 登出
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// 後台頁面包在 middleware 裡
Route::middleware([AdminAuth::class])->prefix('admin')->group(function () {
    
    // 後台首頁
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    
    // 菜單管理
    Route::get('/menus', [MenuController::class, 'index'])->name('admin.menus');
    
    // 今日甜點管理
    Route::get('/desserts', [DessertController::class, 'index'])->name('admin.desserts');

    // 輪播圖管理
    Route::get('/carousel', [CarouselController::class, 'index'])->name('admin.carousel');

    // 活動剪影管理
    Route::get('/events', [EventController::class, 'index'])->name('admin.events');

    // 帳號管理 (僅店長可進入)
    Route::get('/admins', [AdminController::class, 'adminList'])->name('admin.admins');
});
