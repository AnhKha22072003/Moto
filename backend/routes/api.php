<?php

use App\Http\Controllers\MotorcycleController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\LogApiRequest;
use Illuminate\Support\Facades\Route;


Route::middleware(['throttle:5,1'])->post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->prefix('motorcycles')->group(function () {
    Route::get('/maker-select',[MotorcycleController::class, 'makerSelect']);
    Route::get('/model-select',[MotorcycleController::class,'modelSelect']);
    Route::middleware(LogApiRequest::class)->post('/bulk-update',[MotorcycleController::class,'bulkUpdate']);
    Route::get('/', [MotorcycleController::class, 'index']);
    Route::middleware(LogApiRequest::class)->post('/', [MotorcycleController::class, 'store']);
    Route::get('{id}', [MotorcycleController::class, 'show']);
    Route::middleware(LogApiRequest::class)->post('{id}', [MotorcycleController::class, 'update']);
    Route::middleware(LogApiRequest::class)->delete('{id}', [MotorcycleController::class, 'destroy']);
    Route::middleware(LogApiRequest::class)->post('{id}/clone', [MotorcycleController::class, 'clone']);
    Route::middleware(LogApiRequest::class)->post('{id}/post', [MotorcycleController::class, 'postMotorcycle']);

});