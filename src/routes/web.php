<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\Admin\ProductBundleController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\AboutController;

/**
 * 語言切換
 */
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

/**
 * 公開頁
 */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/tech', fn () => Inertia::render('Tech'))->name('tech');

// 表單（Lead）
Route::get('/lead',  [LeadController::class, 'create'])->name('leads.create');
Route::post('/lead', [LeadController::class, 'store'])->name('leads.store');

// 產品與服務
Route::get('/products',            [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}',     [ProductController::class, 'show'])->name('products.show');

// 應用場域
Route::get('/applications',        [ApplicationController::class, 'index'])->name('applications.index');
Route::get('/applications/{slug}', [ApplicationController::class, 'show'])->name('applications.show');

// 案例說明
Route::get('/cases',               [CaseController::class, 'index'])->name('cases.index');
Route::get('/cases/{slug}',        [CaseController::class, 'show'])->name('cases.show');



// 暫時開啟註冊功能
Route::get('register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');


/**
 * 需登入（可加 verified）
 */
Route::middleware(['auth']) // 只要登入即可
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/articles', [ContentController::class, 'index'])
        ->name('articles.index');
        // 一鍵編輯（產品/應用/案例）
        Route::get('/products/create',         [ProductBundleController::class, 'create'])->name('products.create');
        Route::post('/products',               [ProductBundleController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductBundleController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}',      [ProductBundleController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}',    [ProductBundleController::class, 'destroy'])->name('products.destroy');
        // 頁尾內容管理
        Route::get('/footer',                  [\App\Http\Controllers\Admin\FooterController::class, 'index'])->name('footer.index');
        Route::put('/footer',                   [\App\Http\Controllers\Admin\FooterController::class, 'update'])->name('footer.update');
        // 輪播管理
        Route::get('/carousel',                [\App\Http\Controllers\Admin\CarouselController::class, 'index'])->name('carousel.index');
        Route::post('/carousel',               [\App\Http\Controllers\Admin\CarouselController::class, 'store'])->name('carousel.store');
        Route::put('/carousel/{carousel}',     [\App\Http\Controllers\Admin\CarouselController::class, 'update'])->name('carousel.update');
        Route::delete('/carousel/{carousel}',  [\App\Http\Controllers\Admin\CarouselController::class, 'destroy'])->name('carousel.destroy');
        // 關於我們管理
        Route::get('/about',                   [\App\Http\Controllers\Admin\AboutController::class, 'index'])->name('about.index');
        Route::put('/about',                   [\App\Http\Controllers\Admin\AboutController::class, 'update'])->name('about.update');
    });

    // 範例儀表板
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

    // 使用者設定
    Route::get('/profile',        [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',      [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',     [ProfileController::class, 'destroy'])->name('profile.destroy');


/**
 * Breeze / auth 路由（只 require 一次）
 */
require __DIR__.'/auth.php';
