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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
   // return $request->user();
//});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login','AuthController@authenticate');
    Route::post('/logout','AuthController@logout');
    Route::post('/check','AuthController@check');
    Route::post('/register','AuthController@register');
    Route::get('/activate/{token}','AuthController@activate');
    Route::post('/password','AuthController@password');
    Route::post('/validate-password-reset','AuthController@validatePasswordReset');
    Route::post('/get-referrer','AuthController@getReferrer');
    Route::post('/reset','AuthController@reset');
    Route::get('/get-stats','AuthController@getStats');
});

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('/auth/user','AuthController@getAuthUser');
      Route::get('/user','UserController@index');
      Route::post('/user/add-bet','UserController@addBet');
      Route::post('/user/add-betfriend','UserController@addBetFriend');
      Route::delete('/user/{id}','UserController@destroy');
      Route::get('/user/dashboard','UserController@dashboard');
  });
