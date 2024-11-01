<?php

use App\Http\Controllers\Main\AssetController;
use App\Http\Controllers\Main\CategoryItemController;
use App\Http\Controllers\Main\CustomerController;
use App\Http\Controllers\Main\DriverProjectController;
use App\Http\Controllers\Main\FormController;
use App\Http\Controllers\Main\FuelConsumptionController;
use App\Http\Controllers\Main\InspectionScheduleController;
use App\Http\Controllers\Main\ItemController;
use App\Http\Controllers\main\ManagementProjectController;
use App\Http\Controllers\Main\MenuController;
use App\Http\Controllers\Main\PermisionController;
use App\Http\Controllers\Main\ReportFuelController;
use App\Http\Controllers\Main\RoleController;
use App\Http\Controllers\Main\SiteController;
use App\Http\Controllers\Main\SupplierController;
use App\Http\Controllers\Main\UserController;
use App\Http\Controllers\Main\WerehouseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/sidebar', [MenuController::class, 'useMenu'])->name('menu.useMenu');

    Route::get('/menu/data', [MenuController::class, 'data'])->name('menu.data');
    Route::delete('/menu/destroy-all', [MenuController::class, 'destroyAll'])->name('menu.destroyAll');
    Route::resource('menu', MenuController::class);

    Route::get('/permision/data', [PermisionController::class, 'data'])->name('permision.data');
    Route::delete('/permision/destroy-all', [PermisionController::class, 'destroyAll'])->name('permision.destroyAll');
    Route::resource('permision', PermisionController::class);

    Route::get('/role/data', [RoleController::class, 'data'])->name('role.data');
    Route::delete('/role/destroy-all', [RoleController::class, 'destroyAll'])->name('role.destroyAll');
    Route::resource('role', RoleController::class);

    Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
    Route::delete('/user/destroy-all', [UserController::class, 'destroyAll'])->name('user.destroyAll');
    Route::resource('user', UserController::class);

    Route::get('/site/data', [SiteController::class, 'data'])->name('site.data');
    Route::delete('/site/destroy-all', [SiteController::class, 'destroyAll'])->name('site.destroyAll');
    Route::resource('site', SiteController::class);

    Route::get('/asset/data', [AssetController::class, 'data'])->name('asset.data');
    Route::delete('/asset/destroy-all', [AssetController::class, 'destroyAll'])->name('asset.destroyAll');
    Route::resource('asset', AssetController::class);

    Route::get('/management-project/by-project', [ManagementProjectController::class, 'getAssetsByProject'])->name('management-project.by_project');
    Route::get('/management-project/data', [ManagementProjectController::class, 'data'])->name('management-project.data');
    Route::delete('/management-project/destroy-all', [ManagementProjectController::class, 'destroyAll'])->name('management-project.destroyAll');
    Route::resource('management-project', ManagementProjectController::class);

    Route::get('/customer/data', [CustomerController::class, 'data'])->name('customer.data');
    Route::delete('/customer/destroy-all', [CustomerController::class, 'destroyAll'])->name('customer.destroyAll');
    Route::resource('customer', CustomerController::class);

    Route::get('/category-item/data', [CategoryItemController::class, 'data'])->name('category-item.data');
    Route::delete('/category-item/destroy-all', [CategoryItemController::class, 'destroyAll'])->name('category-item.destroyAll');
    Route::resource('category-item', CategoryItemController::class);

    Route::get('/supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
    Route::delete('/supplier/destroy-all', [SupplierController::class, 'destroyAll'])->name('supplier.destroyAll');
    Route::resource('supplier', SupplierController::class);

    Route::get('/item/data', [ItemController::class, 'data'])->name('item.data');
    Route::delete('/item/destroy-all', [ItemController::class, 'destroyAll'])->name('item.destroyAll');
    Route::resource('item', ItemController::class);

    Route::get('/form/data', [FormController::class, 'data'])->name('form.data');
    Route::delete('/form/destroy-all', [FormController::class, 'destroyAll'])->name('form.destroyAll');
    Route::resource('form', FormController::class);

    Route::get('/werehouse/data', [WerehouseController::class, 'data'])->name('werehouse.data');
    Route::delete('/werehouse/destroy-all', [WerehouseController::class, 'destroyAll'])->name('werehouse.destroyAll');
    Route::resource('werehouse', WerehouseController::class);

    Route::get('/inspection-schedule/data', [InspectionScheduleController::class, 'data'])->name('inspection-schedule.data');
    Route::resource('inspection-schedule', InspectionScheduleController::class);
    Route::get('quiz', function () {
        return view('main.quiz.index');
    })->name('quiz');

    Route::get('/fuel/data', [FuelConsumptionController::class, 'data'])->name('fuel.data');
    Route::delete('/fuel/destroy-all', [FuelConsumptionController::class, 'destroyAll'])->name('fuel.destroyAll');
    Route::resource('fuel', FuelConsumptionController::class);

    // driver
    Route::post('/driver/select-project', [DriverProjectController::class, 'selectProject'])->name('driver.selectProject');
    Route::get('/driver/project', [DriverProjectController::class, 'data'])->name('driver.data');
    Route::resource('driver', DriverProjectController::class);

    Route::get('/report-fuel/export-excel', [ReportFuelController::class, 'exportExcel'])->name('report-fuel.export-excel');
    Route::post('report-fuel/export-pdf', [ReportFuelController::class, 'exportPDF'])->name('report-fuel.export-pdf');
    Route::get('/report-fuel/chart', [ReportFuelController::class, 'getChartData'])->name('report-fuel.chart');
    Route::get('/report-fuel/data', [ReportFuelController::class, 'data'])->name('report-fuel.data');
    Route::resource('report-fuel', ReportFuelController::class);
});

require __DIR__ . '/auth.php';
