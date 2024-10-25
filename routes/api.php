<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayDetailsController;
use App\Http\Controllers\BlotterController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\SitioController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
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
    Route::post('/logout', 'logout')->middleware(['auth:sanctum', 'verified']);
});

Route::controller(BarangayDetailsController::class)->prefix('/barangay_details')->group(function () {
    Route::get('/list', 'index');
    Route::put('/update/{id}', 'update');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::controller(UserController::class)->prefix('/users')->group(function () {
        Route::get('/list', 'index');
        Route::get('/{id}', 'view');
        Route::put('/update/{id}', 'update');
        Route::put('/update-profile', 'update_profile');
    });

    Route::controller(SitioController::class)->prefix('/sitio')->group(function () {
        Route::get('/list', 'index');
        Route::get('/view/{id}', 'show');
        Route::post('/create', 'store');
        Route::put('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'destroy');
    });

    Route::controller(OfficialController::class)->prefix('/barangay-official')->group(function () {
        Route::get('/list', 'index');
        Route::get('/archive_list', 'archive_list');
        Route::get('/view/{id}', 'show');
        Route::post('/create', 'store');
        Route::put('/update/{id}', 'update');
        Route::put('/archive_status/{id}/{status}', 'archive_status');
        Route::delete('/delete/{id}', 'destroy');
    });

    Route::controller(ResidentController::class)->prefix('/resident')->group(function () {
        Route::get('/list', 'index');
        Route::get('/archive_list', 'archive_list');
        Route::get('/view/{id}', 'show');
        Route::post('/create', 'store');
        Route::put('/update/{id}', 'update');
        Route::put('/archive_status/{id}/{status}', 'archive_status');
        Route::delete('/delete/{id}', 'destroy');
    });

    Route::controller(DocumentController::class)->prefix('/document')->group(function () {
        Route::get('/list', 'index');
        Route::get('/view/{name}', 'show');
        Route::post('/create', 'store');
        Route::put('/update/{id}', 'update');
        Route::delete('/delete/{id}', 'destroy');
    });

    Route::controller(RequestController::class)->prefix('/request')->group(function () {
        Route::get('/list/{status}', 'index');
        Route::get('/archive_list/{status}', 'archive_list');
        Route::get('/view/{id}', 'show');
        Route::post('/create', 'store');
        Route::put('/update/{id}', 'update');
        Route::put('/update-status/{id}/{status}', 'updateStatus');
        Route::put('/complete/{id}', 'complete');
        Route::put('/archive_status/{id}/{status}', 'archive_status');
        Route::delete('/delete/{id}', 'destroy');
    });

    Route::controller(TransactionController::class)->prefix('/transaction')->group(function () {
        Route::get('/list', 'index');
        Route::get('/archive_list', 'archive_list');
        Route::get('/view/{id}', 'show');
        Route::post('/create', 'store');
        Route::put('/update/{id}', 'update');
        Route::put('/archive_status/{id}/{status}', 'archive_status');
        Route::delete('/delete/{id}', 'destroy');
    });

    Route::controller(AnnouncementController::class)->prefix('/announcement')->group(function () {
        Route::get('/list', 'index');
        Route::get('/archive_list', 'archive_list');
        Route::get('/view/{id}', 'show');
        Route::post('/create', 'store');
        Route::put('/update/{id}', 'update');
        Route::put('/archive_status/{id}/{status}', 'archive_status');
        Route::delete('/delete/{id}', 'destroy');
    });

    Route::controller(BlotterController::class)->prefix('/blotter')->group(function () {
        Route::get('/list', 'index');
        Route::get('/archive_list', 'archive_list');
        Route::get('/view/{id}', 'show');
        Route::post('/create', 'store');
        Route::put('/update/{id}', 'update');
        Route::put('/solve/{id}', 'solve');
        Route::put('/archive_status/{id}/{status}', 'archive_status');
        Route::delete('/delete/{id}', 'destroy');
    });
});
