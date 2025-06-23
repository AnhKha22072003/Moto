<?php

use App\Http\Controllers\MotorcycleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
     return Inertia::render('MotorcycleList');
});

Route::get(('/motorcycles-create'), [MotorcycleController::class, 'create'])->name('motorcycles.create');

Route::get('/motorcycles-view', function () {
    return Inertia::render('MotorcycleList');
})->name('motorcycles.list');

Route::get('/motorcycles/{id}/edit', function ($id) {
    return Inertia::render('MotorcycleUpdate', ['motorcycleId' => (int) $id]);
})->name('motorcycles.edit');
Route::get(
    '/bulk-view',
    function () {
        return Inertia::render('BulkUpdate');
    }
)->name('motorcycles.bulk');
Route::get('/log-view', function () {
    return Inertia::render('MotorcycleLogs');
})->name('motorcycles.logs');
Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');
