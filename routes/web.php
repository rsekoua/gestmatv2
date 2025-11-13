<?php

use App\Http\Controllers\AttributionPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Routes pour les décharges PDF
Route::middleware(['auth'])->group(function () {
    Route::get('/attributions/{attribution}/discharge/attribution', [AttributionPdfController::class, 'downloadAttributionDischarge'])
        ->name('attributions.discharge.attribution');

    Route::get('/attributions/{attribution}/discharge/restitution', [AttributionPdfController::class, 'downloadRestitutionDischarge'])
        ->name('attributions.discharge.restitution');

    Route::get('/attributions/{attribution}/discharge/combined', [AttributionPdfController::class, 'downloadCombinedDischarge'])
        ->name('attributions.discharge.combined');
});
