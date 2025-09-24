<?php

use Illuminate\Support\Facades\Route;
use App\Models\Invoice;
use App\Services\InvoiceService;

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

// Landing page
Route::get('/', function () {
    $packages = App\Models\Package::where('is_active', true)->get();
    return view('welcome', compact('packages'));
})->name('welcome');

// Payment Report Routes
Route::get('/payment-report/print', [App\Http\Controllers\PaymentReportController::class, 'print'])
    ->name('payment-report.print');

// Public Registration Routes
Route::get('/daftar', [App\Http\Controllers\PublicRegistrationController::class, 'showRegistrationForm'])
    ->name('registration.form');
Route::post('/daftar', [App\Http\Controllers\PublicRegistrationController::class, 'register'])
    ->name('registration.store');
Route::get('/daftar/berhasil', [App\Http\Controllers\PublicRegistrationController::class, 'success'])
    ->name('registration.success');

// Service Application Routes
Route::get('/layanan/ajukan', [App\Http\Controllers\PublicRegistrationController::class, 'showServiceForm'])
    ->name('service.application.form');
Route::post('/layanan/ajukan', [App\Http\Controllers\PublicRegistrationController::class, 'applyService'])
    ->name('service.application.store');
Route::get('/layanan/berhasil', [App\Http\Controllers\PublicRegistrationController::class, 'serviceSuccess'])
    ->name('service.application.success');

// Customer Authentication Routes
Route::get('/pelanggan/login', [App\Http\Controllers\CustomerAuthController::class, 'showLogin'])
    ->name('customer.login');
Route::post('/pelanggan/login', [App\Http\Controllers\CustomerAuthController::class, 'login'])
    ->name('customer.login.submit');
Route::get('/pelanggan/dashboard', [App\Http\Controllers\CustomerAuthController::class, 'dashboard'])
    ->name('customer.dashboard');
Route::get('/pelanggan/logout', [App\Http\Controllers\CustomerAuthController::class, 'logout'])
    ->name('customer.logout');



// Dashboard routes
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/dashboard/stats', [App\Http\Controllers\DashboardController::class, 'getStats'])->name('dashboard.stats');

// Login route for Filament logout redirect (required by Filament)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');







// Invoice PDF routes
Route::get('/invoice/{invoice}/pdf', function (Invoice $invoice) {
    $invoiceService = new InvoiceService();
    return $invoiceService->streamPDF($invoice);
})->name('invoice.pdf');

Route::get('/invoice/{invoice}/download', function (Invoice $invoice) {
    $invoiceService = new InvoiceService();
    return $invoiceService->downloadPDF($invoice);
})->name('invoice.download');

// Test export routes
Route::get('/test-export-period', function () {
    $export = new \App\Exports\PaymentPeriodReportExport(2025);
    return \Maatwebsite\Excel\Facades\Excel::download($export, 'test-laporan-periode.xlsx');
});

Route::get('/test-export-payment', function () {
    $export = new \App\Exports\PaymentReportExport();
    return \Maatwebsite\Excel\Facades\Excel::download($export, 'test-laporan-payment.xlsx');
});
