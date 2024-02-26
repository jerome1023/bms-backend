<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\SitioController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::controller(UserController::class)->prefix('/users')->group(function () {
        Route::get('/{id}', 'view');
    });

    Route::controller(SitioController::class)->prefix('/sitio')->group(function () {
        Route::get('/all', 'index');
        Route::get('/view/{name}', 'show');
        Route::post('/create', 'store');
        Route::put('/update/{name}', 'update');
        Route::delete('/delete/{name}', 'destroy');
    });

    Route::controller(OfficialController::class)->prefix('/official')->group(function () {
        Route::get('/all', 'index');
        Route::get('/view/{name}', 'show');
        Route::post('/create', 'store');
        Route::put('/update/{name}', 'update');
        Route::delete('/delete/{name}', 'destroy');
    });
});