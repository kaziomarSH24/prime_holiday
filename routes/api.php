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

    Route::namespace('App\Http\Controllers')->group(function () {
        Route::get('/continent', 'TravelController@getContinents');
        Route::post('/continent/store', 'TravelController@storeContinent');
        Route::delete('/continent/delete/{id}', 'TravelController@deleteContinent');

        //country routes
        Route::get('/country', 'TravelController@getCountries');
        Route::post('/country/store', 'TravelController@storeCountry');
        Route::put('/country/update/{id}', 'TravelController@updateCountry');
        Route::delete('/country/delete/{id}', 'TravelController@deleteCountry');
        //gety country by continent
        Route::get('/country/continent/{id}', 'TravelController@getCountriesByContinent');


        //destination routes
        Route::get('/destination', 'TravelController@getDestinations');
        Route::post('/destination/store', 'TravelController@storeDestination');



    });
});

