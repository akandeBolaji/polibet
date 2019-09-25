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
Route::post('ussd','UssdController@index');


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


Route::get('/get-bet/{id}','BetController@getBet');
Route::get('/trending-bets','BetController@trendingBet');

Route::group(['middleware' => ['jwt.auth', 'last_seen']], function () {
    Route::get('/auth/user','AuthController@getAuthUser');
      Route::get('/user','UserController@index');
      Route::post('/verify-account','AuthController@verifyAccount');
      Route::post('/edit-user','AuthController@editUser');
      Route::get('/get-dispute/{id}','BetController@getDispute');
      Route::post('/add-bet','BetController@addBet');
      Route::post('/accept-outcome','BetController@acceptOutcome');
      Route::post('/submit-comment','BetController@submitComment');
      Route::post('/submit-outcome','BetController@submitOutcome');
      Route::post('/create-dispute','BetController@createDispute');
      Route::post('/create-bet','BetController@createBet');
      Route::post('/user/add-fund','UserController@addFund');
      Route::post('/user/withdraw-fund','UserController@withdrawFund');
      Route::post('/user/add-betfriend','UserController@addBetFriend');
      Route::delete('/user/{id}','UserController@destroy');
      Route::get('/user/dashboard','UserController@dashboard');
  });
