<?php

use App\Http\Controllers\ReportMonthlyTransactionExportController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web', 'auth'])->get(
    '/admin/report-monthly-transactions/export/{year}/{remarksKey}',
    ReportMonthlyTransactionExportController::class
)->name('report-monthly-transactions.export');
