<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
  

Route::group(['middleware' => 'VerifyAPIKey'], function () {
        
    Route::post('createAccount', 'API\Mokam\AuthService@createAccount');
    Route::post('getMarketDetails', 'API\Mokam\AuthService@getMarketDetails');
// Updated APIS
    Route::post('getOTP', 'API\Mokam\AuthService@getOTP');
    Route::post('verifyOTP', 'API\Mokam\AuthService@verifyOTP');
    
    
});