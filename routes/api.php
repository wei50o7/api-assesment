<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

   Route::group(['prefix' => 'token'],function()
   {
       Route::post('', 'ApiController@index');
       Route::get('', 'ApiController@details');
       Route::get('/logout', 'ApiController@logout');
       Route::post('','UserController@create');
   });

Route::middleware('auth:api')->group(function()
{
    Route::group(['prefix' => 'users'],function()
    {
        Route::get('','UserController@index');
        Route::get('{user}','UserController@show');
        Route::put('{user}','UserController@update');
        Route::delete('{user}','UserController@delete');
        Route::post('{user}','UserController@upload');
    });

    Route::group(['prefix' => 'files'],function()
    {
       Route::get('','FileController@index');
       Route::get('{user}','FileController@show');
       Route::post('{user}','FileController@create');
       Route::delete('{user}','FileController@delete');
    });
});
