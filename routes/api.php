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
/** ________________________________________________ */
/** ________________ Login n' stuff ________________ */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'LoginControllerAPI@login');
Route::middleware('auth:api')->post('logout','LoginControllerAPI@logout');

Route::post('password/email', 'LoginControllerAPI@sendResetLinkEmail');
Route::post('password/reset', 'LoginControllerAPI@resetPassword');

Route::post('register', 'UserControllerAPI@store'); //REGISTER NEW USER
Route::post('activate', 'UserControllerAPI@activate'); //ACTIVATE NEW USER




/** ____________________________________________ */
/** ________________ Statistics ________________ */

//___ General Public Statistics _____________
Route::get('generalstatistics', 'StatisticControllerAPI@getPublicStatistics');

//___ Top 5 Players With The Most wins ______
Route::get('topfivewins', 'StatisticControllerAPI@getTop5Wins');

//___ Top 5 Players With The Best Score _____
Route::get('topfivescore', 'StatisticControllerAPI@getTop5Score');

//___ Top 5 Players With The Best Average ___
Route::get('topfiveavg', 'StatisticControllerAPI@getTop5AVG');

//___ User Statistics _______________________
Route::middleware('auth:api', 'checkPlayer')->post('userstatistics', 'StatisticControllerAPI@getUserStatistics');

//___ All User Statistics ___________________
Route::middleware('auth:api', 'checkAdmin')->post('allusersstatistics', 'UserControllerAPI@getUsersStatistics');



/** _________________________________________ */
/** ________________ BackEnd ________________ */

//___ Get Users ____________________________
Route::middleware('auth:api', 'checkAdmin')->post('users', 'UserControllerAPI@getUsers');

//___ Get Total Games Per Day ______________
Route::middleware('auth:api', 'checkAdmin')->post('totalgamesperdate', 'StatisticControllerAPI@getTotalGamesPerDate');



/** __________________________________________ */
/** ________________ FrontEnd ________________ */

//___ Update User General data _____________
Route::middleware('auth:api', 'checkPlayer')->post('/user/update', 'UserControllerAPI@update');

//___ Delete User Account __________________
Route::middleware('auth:api', 'checkPlayer')->delete('/user/deleteaccount', 'UserControllerAPI@deleteAccount');



/** ______________________________________ */
/** ________________ Mix _________________ */

//___ Update User or Admin Password ________
Route::middleware('auth:api')->post('/user/password/update', 'UserControllerAPI@updatePassword');



/** ______________________________________ */
/** ________________ Game ________________ */