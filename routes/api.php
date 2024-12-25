<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group(['prefix' => 'admin'], function () {
    Route::post('/login', [AdminController::class, 'login'])->name('login');
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('logout', 'App\Http\Controllers\AdminController@logout');
    });
});
