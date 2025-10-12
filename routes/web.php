<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CarouselController;
use App\Http\Controllers\MemberController;
use App\Http\Middleware\AdminAuth;


// ---------- 前台 ----------

// 首頁
Route::get('/', [FrontController::class, 'index'])->name('front.index');

// 其他前台頁面可以在這裡加
// Route::get('/about', [FrontController::class, 'about'])->name('front.about');

// 會員登入
Route::get('/member/login', [MemberController::class, 'loginForm'])->name('member.loginForm');
Route::post('/member/login', [MemberController::class, 'login'])->name('member.login');

Route::middleware('auth:member')->group(function () {
    Route::get('/member/dashboard', function () {
        return view('members.dashboard');
    })->name('member.dashboard');
});


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


    // 輪播圖排序上下移
    Route::get('carousel/{id}/swap/{direction}', [CarouselController::class, 'swapOrder'])
        ->name('admin.carousel.swap');
    // 切換顯示狀態
    Route::post('carousel/{id}/toggle', [CarouselController::class, 'toggleActive'])
        ->name('admin.carousel.toggle');

    // 輪播圖 CRUD
    Route::resource('carousel', CarouselController::class, [
        'names' => [
            'index'   => 'admin.carousel.index',
            'create'  => 'admin.carousel.create',
            'store'   => 'admin.carousel.store',
            'edit'    => 'admin.carousel.edit',
            'update'  => 'admin.carousel.update',
            'destroy' => 'admin.carousel.destroy',
            'show'    => 'admin.carousel.show', // 可選，如果你要用 show
        ]
    ]);
});
