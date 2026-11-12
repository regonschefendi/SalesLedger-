<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\sales\OcrController;
use App\Http\Controllers\sales\SalesController;
use App\Http\Controllers\sales\SalesRiwayatController;
use App\Http\Controllers\sales\TokoPeganganController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [SalesController::class, 'index'])->name('home');

// Profile
Route::get('/profile', [SalesController::class, 'profileIndex'])->name('profile.index');
Route::get('/profile/edit', [SalesController::class, 'profileEdit'])->name('profile.edit');
Route::post('/profile/update', [SalesController::class, 'profileUpdate'])->name('profile.update');

// OCR & Faktur
Route::get('/input', [OcrController::class, 'index'])->name('input');
Route::post('/api/scan', [OcrController::class, 'processApi'])->name('scan');
Route::post('/faktur/simpan', [OcrController::class, 'createFaktur'])->name('addFaktur');
Route::get('/toko/search', [OcrController::class, 'searchToko'])->name('toko.search');

// Riwayat Faktur
Route::get('/riwayat', [SalesRiwayatController::class, 'index'])->name('riwayat.index');
Route::get('/riwayat/{id}', [SalesRiwayatController::class, 'show'])->name('riwayat.show');
Route::get('/riwayat/{id}/invoice', [InvoiceController::class, 'show'])->name('riwayat.invoice');
Route::get('/riwayat/{id}/invoice/download', [InvoiceController::class, 'download'])->name('riwayat.invoice.download');
Route::get('/riwayat/{id}/catat-pembayaran', [SalesRiwayatController::class, 'editPembayaran'])->name('riwayat.edit');
Route::post('/riwayat/{id}/catat-pembayaran', [SalesRiwayatController::class, 'updatePembayaran'])->name('riwayat.update');

// Toko Pegangan
Route::get('/toko-pegangan', [TokoPeganganController::class, 'index'])->name('toko-pegangan.index');
Route::post('/toko-pegangan', [TokoPeganganController::class, 'sync'])->name('toko-pegangan.sync');
