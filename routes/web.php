<?php

use App\Http\Controllers\Admin\PermisionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
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

});

require __DIR__.'/auth.php';
