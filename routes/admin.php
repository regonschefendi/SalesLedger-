<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\SalesController;
use App\Http\Controllers\admin\TokoController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

// Pembukuan
Route::get('/pembukuan', [AdminController::class, 'pembukuanIndex'])->name('pembukuan.index');
Route::get('/pembukuan/export', [AdminController::class, 'pembukuanExport'])->name('pembukuan.export');
Route::get('/pembukuan/{id}', [AdminController::class, 'pembukuanShow'])->name('pembukuan.show');
Route::get('/pembukuan/{id}/invoice', [InvoiceController::class, 'show'])->name('pembukuan.invoice');
Route::get('/pembukuan/{id}/invoice/download', [InvoiceController::class, 'download'])->name('pembukuan.invoice.download');

// Toko
Route::get('/toko', [TokoController::class, 'index'])->name('toko.index');
Route::get('/toko/tambah', [TokoController::class, 'create'])->name('toko.create');
Route::post('/toko/store', [TokoController::class, 'store'])->name('toko.store');
Route::get('/toko/{id}', [TokoController::class, 'show'])->name('toko.show');
Route::get('/toko/{toko}/edit', [TokoController::class, 'edit'])->name('toko.edit');
Route::put('/toko/{toko}', [TokoController::class, 'update'])->name('toko.update');
Route::delete('/toko/{toko}', [TokoController::class, 'destroy'])->name('toko.destroy');
Route::get('/nota/{id}', [TokoController::class, 'showNota'])->name('nota.show');

// Profile
Route::get('/profile', [AdminController::class, 'profileIndex'])->name('profile.index');
Route::get('/profile/edit', [AdminController::class, 'profileEdit'])->name('profile.edit');
Route::post('/profile/update', [AdminController::class, 'profileUpdate'])->name('profile.update');
Route::get('/profile/custom-code', [AdminController::class, 'customCodeIndex'])->name('profile.custom_code');
Route::post('/profile/custom-code/update', [AdminController::class, 'customCodeUpdate'])->name('profile.custom-code.update');

// Sales
Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
Route::get('/sales/{id}', [SalesController::class, 'show'])->name('sales.show');
