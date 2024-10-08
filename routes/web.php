<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\PosController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\BranchController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\ReturnController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\RestockController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\CustomerController;
use App\Http\Controllers\Dashboard\EmployeeController;
use App\Http\Controllers\Dashboard\SupplierController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PaySalaryController;
use App\Http\Controllers\Dashboard\AttendenceController;
use App\Http\Controllers\Dashboard\AdvanceSalaryController;
use App\Http\Controllers\Dashboard\DatabaseBackupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// DEFAULT DASHBOARD & PROFILE
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard')->middleware('notSalesMiddleware');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
});

// ====== USERS ======
Route::middleware(['permission:user.menu'])->group(function () {
    Route::resource('/users', UserController::class)->except(['show']);
});

// ====== CUSTOMERS ======
Route::middleware(['permission:customer.menu'])->group(function () {
    Route::resource('/customers', CustomerController::class);
});

// ====== SUPPLIERS ======
Route::middleware(['permission:supplier.menu'])->group(function () {
    Route::resource('/suppliers', SupplierController::class);
});

// ====== EMPLOYEES ======
Route::middleware(['permission:employee.menu'])->group(function () {
    Route::resource('/employees', EmployeeController::class);
});

// ====== EMPLOYEE ATTENDENCE ======
Route::middleware(['permission:attendence.menu'])->group(function () {
    Route::resource('/employee/attendence', AttendenceController::class)->except(['show', 'update', 'destroy']);
    Route::get('/employee/edit/{date}/{branch_id}', [AttendenceController::class, 'edit'])->name('attendence.edit');
});

// ====== SALARY EMPLOYEE ======
Route::middleware(['permission:salary.menu'])->group(function () {
    // PaySalary
    Route::resource('/pay-salary', PaySalaryController::class)->except(['show', 'create', 'edit', 'update']);
    Route::get('/pay-salary/history', [PaySalaryController::class, 'payHistory'])->name('pay-salary.payHistory');
    Route::get('/pay-salary/history/{date}/{employee_id}', [PaySalaryController::class, 'payHistoryDetail'])->name('pay-salary.payHistoryDetail');
    Route::get('/pay-salary/{id}', [PaySalaryController::class, 'paySalary'])->name('pay-salary.paySalary');

    // Advance Salary
    Route::resource('/advance-salary', AdvanceSalaryController::class)->except(['show']);
});

// ====== PRODUCTS ======
Route::middleware(['permission:product.menu'])->group(function () {
    Route::get('/products/import', [ProductController::class, 'importView'])->name('products.importView');
    Route::post('/products/import', [ProductController::class, 'importStore'])->name('products.importStore');
    Route::get('/products/export', [ProductController::class, 'exportData'])->name('products.exportData');
    Route::resource('/products', ProductController::class);
});

// ====== CATEGORY PRODUCTS ======
Route::middleware(['permission:category.menu'])->group(function () {
    Route::resource('/categories', CategoryController::class);
});

// ====== POS ======
Route::middleware(['permission:pos.menu'])->group(function () {
    Route::get('/pos/sales', [PosController::class, 'posSales'])->name('pos.salesPos');
    Route::get('/pos/restock', [PosController::class, 'posRestock'])->name('pos.restockPos')
        ->middleware('notSalesMiddleware');
    Route::get('/pos/return', [PosController::class, 'posReturn'])->name('pos.returnPos')
        ->middleware('notSalesMiddleware');

    Route::post('/pos/sales/add', [PosController::class, 'addCart'])->name('pos.sales.addCart');
    Route::post('/pos/restock/add', [PosController::class, 'addCart'])->name('pos.restock.addCart')->middleware('notSalesMiddleware');
    Route::post('/pos/return/add', [PosController::class, 'addCart'])->name('pos.return.addCart')->middleware('notSalesMiddleware');

    Route::put('/pos/sales/update/{rowId}', [PosController::class, 'updateCart'])->name('pos.sales.updateCart');
    Route::put('/pos/restock/update/{rowId}', [PosController::class, 'updateCart'])->name('pos.restock.updateCart')->middleware('notSalesMiddleware');
    Route::put('/pos/return/update/{rowId}', [PosController::class, 'updateCart'])->name('pos.return.updateCart')->middleware('notSalesMiddleware');

    Route::get('/pos/sales/delete/{rowId}', [PosController::class, 'deleteCart'])->name('pos.sales.deleteCart');
    Route::get('/pos/restock/delete/{rowId}', [PosController::class, 'deleteCart'])->name('pos.restock.deleteCart')->middleware('notSalesMiddleware');
    Route::get('/pos/return/delete/{rowId}', [PosController::class, 'deleteCart'])->name('pos.return.deleteCart')->middleware('notSalesMiddleware');

    // Create Order
    Route::post('/pos/sales/order', [OrderController::class, 'storeOrder'])->name('pos.sales.storeOrder');
    Route::post('/pos/restock/order', [RestockController::class, 'storeOrder'])->name('pos.restock.storeOrder')->middleware('notSalesMiddleware');
    Route::post('/pos/return/order', [ReturnController::class, 'storeOrder'])->name('pos.return.storeOrder')->middleware('notSalesMiddleware');

    /* Route::post('/pos/invoice/create', [PosController::class, 'createInvoice'])->name('pos.createInvoice'); */

    Route::post('/pos/receipt/create', [PosController::class, 'createReceipt'])->name('pos.createReceipt');
});

// ====== ORDERS ======
Route::middleware(['permission:orders.menu'])->group(function () {
    Route::get('/orders/pending', [OrderController::class, 'pendingOrders'])->name('order.pendingOrders')->middleware('notSalesMiddleware');
    Route::get('/orders/complete', [OrderController::class, 'completeOrders'])->name('order.completeOrders')->middleware('notSalesMiddleware');
    Route::get('/orders/details/{order_id}', [OrderController::class, 'orderDetails'])->name('order.orderDetails');
    Route::put('/orders/update/status', [OrderController::class, 'updateStatus'])->name('order.updateStatus');
    Route::get('/orders/invoice/download/{order_id}', [OrderController::class, 'invoiceDownload'])->name('order.invoiceDownload');

    Route::get('/orders/receipt/{order_id}', [OrderController::class, 'viewReceipt'])->name('order.viewReceipt');

    Route::get('/orders/pending/delete/{order_id}', [OrderController::class, 'deleteOrder'])->name('order.pending.deleteOrder')->middleware('notSalesMiddleware');
    Route::get('/orders/complete/delete/{order_id}', [OrderController::class, 'deleteOrder'])->name('order.complete.deleteOrder')->middleware('notSalesMiddleware');

    // Pending Due
    Route::get('/pending/due', [OrderController::class, 'pendingDue'])->name('order.pendingDue');
    Route::get('/order/due/{id}', [OrderController::class, 'orderDueAjax'])->name('order.orderDueAjax');
    Route::post('/update/due', [OrderController::class, 'updateDue'])->name('order.updateDue');
});

// ===== BRANCH ======
Route::middleware(['permission:branch.menu'])->group(function () {
    Route::get('/branch', [BranchController::class, 'index'])->name('branch.index');
    Route::get('/branch/create', [BranchController::class, 'create'])->name('branch.create');
    Route::get('/branch/edit/{id}', [BranchController::class, 'edit'])->name('branch.edit');
    Route::post('/branch/create', [BranchController::class, 'store'])->name('branch.store');
    Route::put('/branch/update/{id}', [BranchController::class, 'update'])->name('branch.update');
    Route::delete('/branch/delete/{id}', [BranchController::class, 'destroy'])->name('branch.destroy');
});

// ===== REPORT ======
Route::middleware(['permission:report.menu'])->group(function () {
    Route::get('/report/distribusi', [ReportController::class, 'index_distribusi'])->name('report.index_distribusi');
    Route::get('/report/penjualan', [ReportController::class, 'index_penjualan'])->name('report.index_penjualan');
    Route::post('/report/distribusi/generate', [ReportController::class, 'generateDistributionReport'])->name('report.distributionReport');
    Route::post('/report/penjualan/generate', [ReportController::class, 'generateSalesReport'])->name('report.salesReport');
});

// ====== DATABASE BACKUP ======
Route::middleware(['permission:database.menu'])->group(function () {
    Route::get('/database/backup', [DatabaseBackupController::class, 'index'])->name('backup.index');

    Route::get('/database/backup/now', [DatabaseBackupController::class, 'create'])->name('backup.create');
    Route::get('/database/backup/download/{getFileName}', [DatabaseBackupController::class, 'download'])->name('backup.download');
    Route::get('/database/backup/delete/{getFileName}', [DatabaseBackupController::class, 'delete'])->name('backup.delete');
});

// ====== ROLE CONTROLLER ======
Route::middleware(['permission:roles.menu'])->group(function () {
    // Permissions
    Route::get('/permission', [RoleController::class, 'permissionIndex'])->name('permission.index');
    Route::get('/permission/create', [RoleController::class, 'permissionCreate'])->name('permission.create');
    Route::post('/permission', [RoleController::class, 'permissionStore'])->name('permission.store');
    Route::get('/permission/edit/{id}', [RoleController::class, 'permissionEdit'])->name('permission.edit');
    Route::put('/permission/{id}', [RoleController::class, 'permissionUpdate'])->name('permission.update');
    Route::delete('/permission/{id}', [RoleController::class, 'permissionDestroy'])->name('permission.destroy');

    // Roles
    Route::get('/role', [RoleController::class, 'roleIndex'])->name('role.index');
    Route::get('/role/create', [RoleController::class, 'roleCreate'])->name('role.create');
    Route::post('/role', [RoleController::class, 'roleStore'])->name('role.store');
    Route::get('/role/edit/{id}', [RoleController::class, 'roleEdit'])->name('role.edit');
    Route::put('/role/{id}', [RoleController::class, 'roleUpdate'])->name('role.update');
    Route::delete('/role/{id}', [RoleController::class, 'roleDestroy'])->name('role.destroy');

    // Role Permissions
    Route::get('/role/permission', [RoleController::class, 'rolePermissionIndex'])->name('rolePermission.index');
    Route::get('/role/permission/create', [RoleController::class, 'rolePermissionCreate'])->name('rolePermission.create');
    Route::post('/role/permission', [RoleController::class, 'rolePermissionStore'])->name('rolePermission.store');
    Route::get('/role/permission/{id}', [RoleController::class, 'rolePermissionEdit'])->name('rolePermission.edit');
    Route::put('/role/permission/{id}', [RoleController::class, 'rolePermissionUpdate'])->name('rolePermission.update');
    Route::delete('/role/permission/{id}', [RoleController::class, 'rolePermissionDestroy'])->name('rolePermission.destroy');
});

require __DIR__ . '/auth.php';
