<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\ScannerController;
use App\Http\Controllers\Api\ScanTaskController;

Route::post('/scanner/report-batch',[ScannerController::class, 'reportBatch']);

Route::get('/devices',[DeviceController::class, 'index']);

Route::get('/devices/{id}',[DeviceController::class, 'show']);

Route::put('/devices/{id}',[DeviceController::class, 'update']);

Route::get('/scan-tasks/pending',[ScanTaskController::class, 'pending']);

Route::put(
    '/scan-tasks/{id}/toggle',
    [ScanTaskController::class, 'toggle']
);

Route::apiResource('scan-tasks',ScanTaskController::class);
