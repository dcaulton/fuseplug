<?php

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
    return view('welcome');
});
Route::post('call', 'HomeController@call');
Route::get('call-status', 'HomeController@callStatus');
Route::get('call-data', 'HomeController@callData');
Route::get('app-status', 'HomeController@appStatus');
Route::get('/app-api-doc', 'HomeController@appApiDoc');
Route::get('brand-interfaces-doc', 'HomeController@brandInterfaceDoc');
