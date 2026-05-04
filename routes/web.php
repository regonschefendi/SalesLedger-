<?php

use App\Http\Controllers\OcrController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', [OcrController::class, 'index']);
// Route::post('/scan', [OcrController::class, 'process']);
// Route::get('/cek-model', [OcrController::class, 'cekModel']);

Route::get('/', [OcrController::class, 'index']);
Route::post('/api/scan', [OcrController::class, 'processApi']);
