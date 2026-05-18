<?php

use App\Http\Controllers\sales\OcrController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', [OcrController::class, 'index']);
// Route::post('/scan', [OcrController::class, 'process']);
// Route::get('/cek-model', [OcrController::class, 'cekModel']);

Route::view('/offline', 'offline');

Route::get('/', [OcrController::class, 'index']);
Route::post('/api/scan', [OcrController::class, 'processApi']);
Route::post('/faktur/simpan', [OcrController::class, 'createFaktur']);
Route::get('/admin/dashboard', [OcrController::class, 'dashboard']);
