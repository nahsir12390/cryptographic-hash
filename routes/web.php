<?php

use App\Http\Controllers\HashController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HashController::class, 'index'])->name('hash.index');

Route::middleware('throttle:30,1')->group(function () {
    Route::post('/visualizer/generate', [HashController::class, 'generate'])->name('hash.generate');
    Route::post('/visualizer/compare', [HashController::class, 'compare'])->name('hash.compare');
});
