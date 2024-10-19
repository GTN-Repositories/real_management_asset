<?php

use App\Http\Controllers\Main\CategoryItemController;
use App\Http\Controllers\Main\CustomerController;
use App\Http\Controllers\Main\PermisionController;
use App\Http\Controllers\Main\RoleController;
use App\Http\Controllers\Main\SiteController;
use App\Http\Controllers\Main\UnitController;
use App\Http\Controllers\Main\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->group(function () {

        Route::get('/permision/data', [PermisionController::class, 'data'])->name('permision.data');
        Route::delete('/permision/destroy-all', [PermisionController::class, 'destroyAll'])->name('permision.destroyAll');
        Route::resource('permision', PermisionController::class);

        Route::get('/role/data', [RoleController::class, 'data'])->name('role.data');
        Route::delete('/role/destroy-all', [RoleController::class, 'destroyAll'])->name('role.destroyAll');
        Route::resource('role', RoleController::class);

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::delete('/user/destroy-all', [UserController::class, 'destroyAll'])->name('user.destroyAll');
        Route::resource('user', UserController::class);
    });

    Route::get('/site/data', [SiteController::class, 'data'])->name('site.data');
    Route::delete('/site/destroy-all', [SiteController::class, 'destroyAll'])->name('site.destroyAll');
    Route::resource('site', SiteController::class);

    Route::get('/unit/data', [UnitController::class, 'data'])->name('unit.data');
    Route::delete('/unit/destroy-all', [UnitController::class, 'destroyAll'])->name('unit.destroyAll');
    Route::resource('unit', UnitController::class);

    Route::get('/customer/data', [CustomerController::class, 'data'])->name('customer.data');
    Route::delete('/customer/destroy-all', [CustomerController::class, 'destroyAll'])->name('customer.destroyAll');
    Route::resource('customer', CustomerController::class);

    Route::get('/category-item/data', [CategoryItemController::class, 'data'])->name('category-item.data');
    Route::delete('/category-item/destroy-all', [CategoryItemController::class, 'destroyAll'])->name('category-item.destroyAll');
    Route::resource('category-item', CategoryItemController::class);
});

require __DIR__.'/auth.php';
