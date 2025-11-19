<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MaterielController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/materials', [MaterielController::class, 'index']);
    Route::get('/employees', [\App\Http\Controllers\Api\EmployeeController::class, 'index']);
});
