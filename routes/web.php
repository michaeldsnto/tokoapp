<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManualInvoiceController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:admin')->group(function (): void {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('products', ProductController::class)->except('show');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    });

    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/lookup', [PosController::class, 'productLookup'])->name('pos.lookup');

    Route::get('/manual-invoices', [ManualInvoiceController::class, 'index'])->name('manual-invoices.index');
    Route::get('/manual-invoices/create', [ManualInvoiceController::class, 'create'])->name('manual-invoices.create');
    Route::post('/manual-invoices', [ManualInvoiceController::class, 'store'])->name('manual-invoices.store');
    Route::patch('/manual-invoices/{transaction}/mark-paid', [ManualInvoiceController::class, 'markPaid'])->name('manual-invoices.mark-paid');

    Route::get('/transactions/{transaction}/receipt', [ReceiptController::class, 'show'])->name('transactions.receipt');
    Route::get('/transactions/{transaction}/receipt/pdf', [ReceiptController::class, 'downloadPdf'])->name('transactions.receipt.pdf');
});
