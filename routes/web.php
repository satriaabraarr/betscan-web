<?php

use App\Http\Controllers\DetectionController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', [DetectionController::class, 'index'])->name('home');

// Endpoint AJAX untuk deteksi
Route::post('/detect', [DetectionController::class, 'detect'])->name('detect');

Route::post('/face/predict', [DetectionController::class, 'facePredict'])->name('face.predict');