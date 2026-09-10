<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CoaSetupController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\VoucherRejectController;
use App\Http\Controllers\Admin\VoucherApproveController;
use App\Http\Controllers\Admin\AdminMenuActionController;
use App\Http\Controllers\Admin\AutomationRejectController;
use App\Http\Controllers\Admin\ClientCollectionController;
use App\Http\Controllers\Admin\DebitVoucherEntryController;
use App\Http\Controllers\Admin\AutomationApproveController;
use App\Http\Controllers\Admin\CreditVoucherEntryController;
use App\Http\Controllers\Admin\JournalVoucherEntryController;

Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
    Route::get('/', [AdminController::class, 'index'])->name('login.index');
    Route::post('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/sidebar', [AdminController::class, 'sidebar'])->name('sidebar');
});

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['admin_permission', 'HtmlMinifier']], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AdminController::class, 'edit'])->name('profile.index');
    Route::put('/change-images', [AdminController::class, 'changeImages'])->name('change-images');
    Route::put('/change-password', [AdminController::class, 'changePassword'])->name('change-password');
    Route::put('/profile', [AdminController::class, 'update'])->name('profile.update');
    Route::get('/logout', [AdminController::class, 'logout'])->name('logout');

    // User
    Route::resource('/user', UserController::class);
    Route::get('/user/{id}/password', [UserController::class, 'changePassword'])->name('user.password');
    Route::put('/user/password/{id}', [UserController::class, 'passwordUpdate'])->name('user.password-update');

    // Admin Menu
    Route::resource('/admin-menu', AdminMenuController::class);

    // Admin Menu Actions
    Route::get('/admin-menu-actions/{id}', [AdminMenuActionController::class, 'index'])->name('admin-menuAction.index');
    Route::get('/admin-menu-actions/{id}/create', [AdminMenuActionController::class, 'create'])->name('admin-menuAction.create');
    Route::post('/admin-menu-actions/{id}/store', [AdminMenuActionController::class, 'store'])->name('admin-menuAction.store');
    Route::get('/admin-menu-actions/{id}/edit', [AdminMenuActionController::class, 'edit'])->name('admin-menuAction.edit');
    Route::put('/admin-menu-actions/{id}', [AdminMenuActionController::class, 'update'])->name('admin-menuAction.update');
    Route::delete('/admin-menu-actions/{id}', [AdminMenuActionController::class, 'destroy'])->name('admin-menuAction.destroy');

    // Role
    Route::resource('/role', RoleController::class);

    // Permission
    Route::get('/permission/{id}', [RoleController::class, 'rolePermissionEdit'])->name('rolePermission.edit');
    Route::put('/permission/permissions-update/{id}', [RoleController::class, 'rolePermissionUpdate'])->name('rolePermission.update');

    // Admin Settings
    Route::resource('/admin-settings', AdminSettingController::class);

    // Company Setup
    Route::resource('/company', CompanyController::class);

    // Accounting
    Route::resource('/coa-setup', CoaSetupController::class);

    // Accounts Transaction
    Route::resource('/debit-voucher-entry', DebitVoucherEntryController::class);
    Route::get('/debit-voucher-entry/{id}/print', [DebitVoucherEntryController::class, 'print'])->name('debit-voucher-entry.print');
    Route::resource('/credit-voucher-entry', CreditVoucherEntryController::class);
    Route::get('/credit-voucher-entry/{id}/print', [creditVoucherEntryController::class, 'print'])->name('credit-voucher-entry.print');
    Route::resource('/journal-voucher-entry', JournalVoucherEntryController::class);
    Route::get('/journal-voucher-entry/{id}/print', [journalVoucherEntryController::class, 'print'])->name('journal-voucher-entry.print');
    Route::resource('/voucher-approve', VoucherApproveController::class);
    Route::resource('/voucher-reject', VoucherRejectController::class);
    Route::resource('/automation-approve', AutomationApproveController::class);
    Route::resource('/automation-reject', AutomationRejectController::class);

    // Client
    Route::resource('/client', ClientController::class);

    // Service
    Route::resource('/service', ServiceController::class);

    // Client Collection
    Route::resource('/client-collection', ClientCollectionController::class);
});

// ================== Reports ================== //
Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['admin_permission', 'HtmlMinifier']], function () {
    Route::get('/coa-list', [ReportController::class, 'coaList'])->name('coa-list.index');
    Route::get('/voucher-list', [ReportController::class, 'voucherList'])->name('voucher-list.index');
    Route::get('/cash-book', [ReportController::class, 'cashBook'])->name('cash-book.index');
    Route::get('/bank-book', [ReportController::class, 'bankBook'])->name('bank-book.index');
    Route::get('/transaction-ledger', [ReportController::class, 'transactionLedger'])->name('transaction-ledger.index');
    Route::get('/cash-flow-statement', [ReportController::class, 'cashFlowStatement'])->name('cash-flow-statement.index');
    Route::get('/general-ledger', [ReportController::class, 'generalLedger'])->name('general-ledger.index');
    Route::get('/income-statement', [ReportController::class, 'incomeStatement'])->name('income-statement.index');
    Route::get('/income-statement-head-details', [ReportController::class, 'incomeStatementHeadDetails'])->name('income-statement-head-details.index');
    Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial-balance.index');
    Route::get('/balance-sheet', [ReportController::class, 'balanceSheet'])->name('balance-sheet.index');
    Route::get('/balance-sheet-head-details', [ReportController::class, 'balanceSheetHeadDetails'])->name('balance-sheet-head-details.index');
    Route::get('/receive-payment', [ReportController::class, 'receivePayment'])->name('receive-payment.index');
    Route::get('/receive-payment-head-details', [ReportController::class, 'receivePaymentHeadDetails'])->name('receive-payment-head-details.index');
});
// ================== Reports ================== //

// Frontend CMS
Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['admin_permission', 'HtmlMinifier']], function () {

    // Settings
    Route::resource('/settings', SettingController::class);
});
