<?php

use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/call', 'HomeController@call');
Route::get('/call-status', 'HomeController@callStatus');
Route::get('/call-data', 'HomeController@callData');
Route::get('/app-status', 'HomeController@appStatus');
Route::get('/app-api-doc', 'HomeController@appApiDoc');
Route::get('/brand-interface-doc', 'HomeController@brandInterfaceDoc');
