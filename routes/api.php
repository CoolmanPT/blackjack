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


/******************
ADMIN ROUTES
 *****************/

Route::middleware('auth:api', 'checkAdmin')->get('/settings', 'ConfigControllerAPI@getPlatformData'); //RETURN's SETTINGS INFO
Route::middleware('auth:api', 'checkAdmin')->post('/settings/update', 'ConfigControllerAPI@update'); //UPDATE SETTINGS
Route::middleware('auth:api', 'checkAdmin')->post('/user/email/update', 'UserControllerAPI@updateEmail'); //UPDATE ADMIN EMAIL
Route::middleware('auth:api', 'checkAdmin')->post('users', 'UserControllerAPI@getUsers'); //GET LIST OF USERS TO MANAGE
Route::middleware('auth:api', 'checkAdmin')->delete('users/{id}/{reason?}', 'UserControllerAPI@delete'); //DELETE USER
Route::middleware('auth:api', 'checkAdmin')->put('users/{id}', 'UserControllerAPI@updateState'); //CHANGE STATE USER
Route::middleware('auth:api', 'checkAdmin')->get('/statistic', 'StatisticAPI@getStatistic'); //RETURN's STATISTIC INFO
Route::middleware('auth:api', 'checkAdmin')->post('/gamesPerDay', 'StatisticAPI@getGamesPerDate'); //RETURN's GAMES PER DAY BETWEEN TWO DATES
Route::middleware('auth:api', 'checkAdmin')->post('/usersStatistic', 'UserControllerAPI@getUsersStatistic'); //RETURN's GAMES PER DAY BETWEEN TWO DATES
Route::middleware('auth:api', 'checkAdmin')->post('/pieces', 'ImgControllerAPI@getImages'); //RETURN's PIECES
Route::middleware('auth:api', 'checkAdmin')->delete('pieces/{id}', 'ImgControllerAPI@delete'); //DELETE PIECE
Route::middleware('auth:api', 'checkAdmin')->post('pieces/store', 'ImgControllerAPI@store'); //ADD PIECE

//ROTAS PARA O NODE.js
Route::middleware('auth:api', 'checkAdmin')->post('setWinner', 'GameControllerAPI@setWinner'); //SET WINNER OF GAME
Route::middleware('auth:api', 'checkAdmin')->post('changeStatus', 'GameControllerAPI@changeStatus'); //CHANGE STATUS OF GAME
Route::middleware('auth:api', 'checkAdmin')->post('joinGame', 'GameControllerAPI@joinGame'); //JOIN USER TO GAME
Route::middleware('auth:api', 'checkAdmin')->get('/game/pieces/{nimages}', 'ImgControllerAPI@getImagesForGame'); //UPDATE USER INFO

/******************
USER ROUTES
 *****************/
Route::middleware('auth:api', 'checkPlayer')->get('getnewuser/{id}', 'GameControllerAPI@getuser'); //CREATE GAME
Route::middleware('auth:api', 'checkPlayer')->post('createGame', 'GameControllerAPI@store'); //CREATE GAME
Route::middleware('auth:api', 'checkPlayer')->delete('deleteOwnAccount', 'UserControllerAPI@deleteOwnAccount');
Route::middleware('auth:api', 'checkPlayer')->post('statisticOfUser', 'StatisticAPI@getStatisticOfUser'); //GET STATISTIC OF USER
Route::middleware('auth:api', 'checkPlayer')->post('/user/update', 'UserControllerAPI@update'); //UPDATE USER INFO
Route::middleware('auth:api', 'checkPlayer')->get('/game/countPieces', 'ImgControllerAPI@getImagesCount'); //RETURN NUMBER OF IMAGES TO GAME

/******************
USER/ADMIN ROUTES
 *****************/

Route::middleware('auth:api')->post('/user/password/update', 'UserControllerAPI@updatePassword'); //UPDATE PASSWORD
Route::middleware('auth:api')->post('/user/avatar/update', 'UserControllerAPI@updateAvatar'); //UPDATE AVATAR



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
