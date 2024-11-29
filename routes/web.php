<?php

use App\Helpers\Helper;
use App\Http\Controllers\Main\AssetAttachmentController;
use App\Http\Controllers\Main\AssetController;
use App\Http\Controllers\Main\AssetNoteController;
use App\Http\Controllers\Main\AssetReportController;
use App\Http\Controllers\Main\CategoryController;
use App\Http\Controllers\Main\CategoryItemController;
use App\Http\Controllers\Main\CustomerController;
use App\Http\Controllers\Main\DriverProjectController;
use App\Http\Controllers\Main\EmployeeController;
use App\Http\Controllers\Main\FormController;
use App\Http\Controllers\Main\FuelConsumptionController;
use App\Http\Controllers\Main\InspectionScheduleController;
use App\Http\Controllers\Main\IpbController;
use App\Http\Controllers\Main\ItemController;
use App\Http\Controllers\Main\JobTitleController;
use App\Http\Controllers\Main\LocationController;
use App\Http\Controllers\Main\LogActivityController;
use App\Http\Controllers\Main\ManagementProjectController;
use App\Http\Controllers\Main\ManagerController;
use App\Http\Controllers\Main\MenuController;
use App\Http\Controllers\Main\MonitoringController;
use App\Http\Controllers\Main\PermisionController;
use App\Http\Controllers\Main\ReportFuelController;
use App\Http\Controllers\Main\RoleController;
use App\Http\Controllers\Main\SiteController;
use App\Http\Controllers\Main\StatusAssetController;
use App\Http\Controllers\Main\SupplierController;
use App\Http\Controllers\Main\UserController;
use App\Http\Controllers\Main\WerehouseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'check_menu_permission', 'log_activity'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/encrypt', function (Request $request) {
        $encrypted = Helper::encrypt($request->input('value'));
        return response()->json(['encrypted' => $encrypted]);
    })->name('encrypt');

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

    Route::get('/asset/download/{encryptedId}', [AssetController::class, 'download'])->name('asset.download');
    Route::get('/asset/download-template', [AssetController::class, 'generateTemplate'])->name('asset.downloadTemplate');
    Route::get('/asset/import', [AssetController::class, 'importForm'])->name('asset.import.form');
    Route::post('/asset/import', [AssetController::class, 'import'])->name('asset.import');
    Route::get('/asset/update-files', [AssetController::class, 'updateFiles'])->name('asset.updateFiles');
    Route::post('/asset/note/{id}', [AssetController::class, 'note'])->name('asset.note');
    Route::get('/asset/appreciation-data', [AssetController::class, 'getAppreciationData'])->name('asset.appreciation-data');
    Route::get('/asset/depreciation-data', [AssetController::class, 'getDepreciationData'])->name('asset.depreciation-data');
    Route::get('/asset/status-data', [AssetController::class, 'getStatusData'])->name('asset.statusData');
    Route::get('/asset/data', [AssetController::class, 'data'])->name('asset.data');
    Route::delete('/asset/destroy-all', [AssetController::class, 'destroyAll'])->name('asset.destroyAll');
    Route::resource('asset', AssetController::class);

    Route::get('/management-project/by-project', [ManagementProjectController::class, 'getAssetsByProject'])->name('management-project.by_project');
    Route::get('/management-project/data', [ManagementProjectController::class, 'data'])->name('management-project.data');
    Route::get('/management-project/todoRequestPettyCash', [ManagementProjectController::class, 'todoRequestPettyCash'])->name('management-project.todoRequestPettyCash');
    Route::post('/management-project/requestPettyCash', [ManagementProjectController::class, 'requestPettyCash'])->name('management-project.requestPettyCash');
    Route::put('/management-project/approvePettyCash/{id}', [ManagementProjectController::class, 'approvePettyCash'])->name('management-project.approvePettyCash');
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

    Route::get('/item/stock/{id}', [ItemController::class, 'editStock'])->name('item.stock');
    Route::get('/item/data', [ItemController::class, 'data'])->name('item.data');
    Route::delete('/item/destroy-all', [ItemController::class, 'destroyAll'])->name('item.destroyAll');
    Route::resource('item', ItemController::class);

    Route::get('/form/data', [FormController::class, 'data'])->name('form.data');
    Route::delete('/form/destroy-all', [FormController::class, 'destroyAll'])->name('form.destroyAll');
    Route::resource('form', FormController::class);

    Route::post('/fuel-ipb/total-liter', [IpbController::class, 'getTotalLiter'])->name('fuel-ipb.total-liter');
    Route::get('/fuel-ipb/data', [IpbController::class, 'data'])->name('fuel-ipb.data');
    Route::delete('/fuel-ipb/destroy-all', [IpbController::class, 'destroyAll'])->name('fuel-ipb.destroyAll');
    Route::resource('fuel-ipb', IpbController::class);

    Route::get('/werehouse/data', [WerehouseController::class, 'data'])->name('werehouse.data');
    Route::delete('/werehouse/destroy-all', [WerehouseController::class, 'destroyAll'])->name('werehouse.destroyAll');
    Route::resource('werehouse', WerehouseController::class);

    Route::get('/inspection-schedule/get-selected-items', [InspectionScheduleController::class, 'getSelectedItems'])->name('get.selected.items');
    Route::post('/inspection-schedule/remove-item-session', [InspectionScheduleController::class, 'removeItemFromSession'])->name('remove.item.session');
    Route::post('/inspection-schedule/clear-items-session', [InspectionScheduleController::class, 'clearAllItemsFromSession'])->name('clear.items.session');
    Route::post('/inspection-schedule/add-item-session', [InspectionScheduleController::class, 'addItemToSession'])->name('add.item.session');
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

    Route::get('/report-asset/export-excel', [AssetReportController::class, 'exportExcel'])->name('report-asset.export-excel');
    Route::post('report-asset/export-pdf', [AssetReportController::class, 'exportPDF'])->name('report-asset.export-pdf');
    Route::get('/report-asset/chart', [AssetReportController::class, 'getChartData'])->name('report-asset.chart');
    Route::get('/report-asset/data', [AssetReportController::class, 'data'])->name('report-asset.data');
    Route::resource('report-asset', AssetReportController::class);

    Route::get('/monitoring/data', [MonitoringController::class, 'data'])->name('monitoring.data');
    Route::delete('/monitoring/destroy-all', [MonitoringController::class, 'destroyAll'])->name('monitoring.destroyAll');
    Route::resource('monitoring', MonitoringController::class);

    Route::get('/employee/data', [EmployeeController::class, 'data'])->name('employee.data');
    Route::delete('/employee/destroy-all', [EmployeeController::class, 'destroyAll'])->name('employee.destroyAll');
    Route::resource('employee', EmployeeController::class);

    Route::get('/job-title/data', [JobTitleController::class, 'data'])->name('job-title.data');

    Route::get('/status-asset/data', [StatusAssetController::class, 'data'])->name('status-asset.data');

    Route::get('/log-activity/data', [LogActivityController::class, 'data'])->name('log-activity.data');

    Route::get('/asset-note/data', [AssetNoteController::class, 'data'])->name('asset-note.data');

    Route::get('/location/data', [LocationController::class, 'data'])->name('location.data');

    Route::get('/manager/data', [ManagerController::class, 'data'])->name('manager.data');

    Route::get('/category/data', [CategoryController::class, 'data'])->name('category.data');

    Route::get('/asset-attachment/data', [AssetAttachmentController::class, 'data'])->name('asset-attachment.data');
    Route::post('/asset-attachment/{id}', [AssetAttachmentController::class, 'store'])->name('asset-attachment.store');
});

require __DIR__ . '/auth.php';
