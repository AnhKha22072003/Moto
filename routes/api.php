<?php

use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\LogApiRequest;
use Illuminate\Support\Facades\Route;


Route::middleware(['throttle:5,1'])->post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->prefix('motorcycles')->group(function () {
    Route::get('/maker-select', [MotorcycleController::class, 'makerSelect']);
    Route::get('/models/{makerCode}', [MotorcycleController::class, 'getModelsByMaker']);
    Route::post('/bulk-update', [MotorcycleController::class, 'bulkUpdate']);
    Route::get('/logs-list', [MotorcycleController::class, 'getListLog']);
    Route::get('/', [MotorcycleController::class, 'index']);
    Route::post('/', [MotorcycleController::class, 'store']);
    Route::get('{id}', [MotorcycleController::class, 'show']);
    Route::post('{id}', [MotorcycleController::class, 'update']);
    Route::delete('{id}', [MotorcycleController::class, 'destroy']);
    Route::post('{id}/clone', [MotorcycleController::class, 'clone']);
    Route::post('{id}/post', [MotorcycleController::class, 'postMotorcycle']);
});
