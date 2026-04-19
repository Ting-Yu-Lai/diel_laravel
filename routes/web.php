<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\CarouselController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\JobTitleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TagCategoryController;
use App\Http\Controllers\TagController;
use App\Http\Middleware\AdminAuth;


// ---------- 前台 ----------

// 首頁
Route::get('/', [FrontController::class, 'index'])->name('front.index');

// 其他前台頁面可以在這裡加
// Route::get('/about', [FrontController::class, 'about'])->name('front.about');

// 會員登入
Route::get('/member/login', [MemberController::class, 'loginForm'])->name('member.loginForm');
Route::post('/member/login', [MemberController::class, 'login'])->name('member.login');
Route::post('/member/logout', [MemberController::class, 'logout'])->name('member.logout');
// 註冊頁面
Route::get('/member/register', [MemberController::class, 'registerForm'])->name('member.registerForm');
// 處理註冊表單
Route::post('/member/register', [MemberController::class, 'register'])->name('member.register');
Route::middleware('auth:member')->group(function () {
    Route::get('/member/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');
    Route::post('/member/logout', [MemberController::class, 'logout'])->name('member.logout');
});


// ---------- 後台 ----------

// 登入頁面
Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.loginForm');
// 登入處理
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
// 登出
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// 後台頁面包在 middleware 裡
Route::middleware([AdminAuth::class])->prefix('backend')->group(function () {

    // 後台首頁
    Route::get('/', [BackController::class, 'index'])->name('backend.index');

    // 輪播圖排序上下移
    Route::get('carousel/{id}/swap/{direction}', [CarouselController::class, 'swapOrder'])
        ->name('backend.carousel.swap');
    // 切換顯示狀態
    Route::post('carousel/{id}/toggle', [CarouselController::class, 'toggleActive'])
        ->name('backend.carousel.toggle');

    // 輪播圖 CRUD
    Route::resource('carousel', CarouselController::class, [
        'names' => [
            'index'   => 'backend.carousel.index',
            'create'  => 'backend.carousel.create',
            'store'   => 'backend.carousel.store',
            'edit'    => 'backend.carousel.edit',
            'update'  => 'backend.carousel.update',
            'destroy' => 'backend.carousel.destroy',
            'show'    => 'backend.carousel.show',
        ]
    ]);

    // 客戶資料 CRUD
    Route::resource('customer', CustomerController::class, [
        'names' => [
            'index'   => 'backend.customer.index',
            'create'  => 'backend.customer.create',
            'store'   => 'backend.customer.store',
            'show'    => 'backend.customer.show',
            'edit'    => 'backend.customer.edit',
            'update'  => 'backend.customer.update',
            'destroy' => 'backend.customer.destroy',
        ]
    ]);

    // 職稱管理（僅 power==1）
    Route::resource('job-title', JobTitleController::class, [
        'names' => [
            'index'   => 'backend.job-title.index',
            'create'  => 'backend.job-title.create',
            'store'   => 'backend.job-title.store',
            'edit'    => 'backend.job-title.edit',
            'update'  => 'backend.job-title.update',
            'destroy' => 'backend.job-title.destroy',
        ],
        'except' => ['show'],
    ]);

    // 工作人員 CRUD
    Route::resource('staff', StaffController::class, [
        'names' => [
            'index'   => 'backend.staff.index',
            'create'  => 'backend.staff.create',
            'store'   => 'backend.staff.store',
            'show'    => 'backend.staff.show',
            'edit'    => 'backend.staff.edit',
            'update'  => 'backend.staff.update',
            'destroy' => 'backend.staff.destroy',
        ],
    ]);

    // 客戶標籤同步
    Route::post('customer/{id}/tags', [CustomerController::class, 'syncTags'])
        ->name('backend.customer.syncTags');

    // 標籤分類 CRUD
    Route::resource('tag-category', TagCategoryController::class, [
        'names' => [
            'index'   => 'backend.tag-category.index',
            'create'  => 'backend.tag-category.create',
            'store'   => 'backend.tag-category.store',
            'edit'    => 'backend.tag-category.edit',
            'update'  => 'backend.tag-category.update',
            'destroy' => 'backend.tag-category.destroy',
        ],
        'except' => ['show'],
    ]);

    // 標籤 CRUD（store 接在分類下，edit/update/destroy 獨立）
    Route::post('tag-category/{categoryId}/tag', [TagController::class, 'store'])
        ->name('backend.tag.store');
    Route::get('tag/{id}/edit', [TagController::class, 'edit'])
        ->name('backend.tag.edit');
    Route::put('tag/{id}', [TagController::class, 'update'])
        ->name('backend.tag.update');
    Route::delete('tag/{id}', [TagController::class, 'destroy'])
        ->name('backend.tag.destroy');

    // 管理者帳號CRUD
    Route::resource('admin', AdminController::class, [
        'names' => [
            'index'   => 'backend.admin.index',
            'create'  => 'backend.admin.create',
            'store'   => 'backend.admin.store',
            'edit'    => 'backend.admin.edit',
            'update'  => 'backend.admin.update',
            'destroy' => 'backend.admin.destroy',
            'show'    => 'backend.admin.show',
        ]
    ]);
});
