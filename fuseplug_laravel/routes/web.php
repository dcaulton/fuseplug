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
Route::post('call', 'HomeController@createCall');
Route::get('call', 'HomeController@listCalls');
Route::get('call/{id}', 'HomeController@getCall');
Route::get('callback', 'HomeController@callback');
Route::get('app-status', 'HomeController@appStatus');
Route::get('app-api-doc', 'HomeController@appApiDoc');
Route::get('brand-interfaces-doc', 'HomeController@brandInterfaceDoc');
Route::post('mock/{id}', 'HomeController@mockPost');
Route::get('mock', 'HomeController@mockList');
Route::get('mock/{id}', 'HomeController@mockGet');
