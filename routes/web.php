<?php

use App\Http\Controllers\AttributionPdfController;
use App\Http\Controllers\AttributionPreviewController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//    return view('welcome');
// });
Route::redirect('/', '/admin')->name('root');

// Routes pour les décharges PDF
Route::middleware(['auth'])->group(function () {
    Route::get('/attributions/{attribution}/discharge/attribution', [AttributionPdfController::class, 'downloadAttributionDischarge'])
        ->name('attributions.discharge.attribution');

    Route::get('/attributions/{attribution}/discharge/restitution', [AttributionPdfController::class, 'downloadRestitutionDischarge'])
        ->name('attributions.discharge.restitution');

    Route::get('/attributions/{attribution}/discharge/combined', [AttributionPdfController::class, 'downloadCombinedDischarge'])
        ->name('attributions.discharge.combined');

    // Routes pour les prévisualisations web
    Route::get('/attributions/{attribution}/preview/attribution', [AttributionPreviewController::class, 'previewAttribution'])
        ->name('attributions.preview.attribution');

    Route::get('/attributions/{attribution}/preview/restitution', [AttributionPreviewController::class, 'previewRestitution'])
        ->name('attributions.preview.restitution');
});
