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

Route::post('register', 'UserControllerAPI@store');
Route::post('activate', 'UserControllerAPI@activate');


/******************
ADMIN ROUTES
 *****************/

Route::middleware('auth:api', 'checkAdmin')->get('/settings', 'ConfigControllerAPI@getPlatformData');
Route::middleware('auth:api', 'checkAdmin')->post('/settings/update', 'ConfigControllerAPI@update');
Route::middleware('auth:api', 'checkAdmin')->post('/user/email/update', 'UserControllerAPI@updateEmail');
Route::middleware('auth:api', 'checkAdmin')->get('users', 'UserControllerAPI@getUsers');
Route::middleware('auth:api', 'checkAdmin')->delete('users/{id}/{reason?}', 'UserControllerAPI@delete');
Route::middleware('auth:api', 'checkAdmin')->put('users/{id}', 'UserControllerAPI@updateState');
Route::middleware('auth:api', 'checkAdmin')->get('/statistic', 'StatisticAPI@getStatistic');
Route::middleware('auth:api', 'checkAdmin')->post('/gamesPerDay', 'StatisticAPI@getGamesPerDate');
Route::middleware('auth:api', 'checkAdmin')->post('/usersStatistic', 'UserControllerAPI@getUsersStatistic');

Route::middleware('auth:api', 'checkAdmin')->get('/decks', 'DeckControllerAPI@getDecks');
Route::middleware('auth:api', 'checkAdmin')->delete('decks/{id}', 'DeckControllerAPI@delete');
Route::middleware('auth:api', 'checkAdmin')->post('decks/store', 'DeckControllerAPI@store');
Route::middleware('auth:api', 'checkAdmin')->post('decks/update', 'DeckControllerAPI@update');
Route::middleware('auth:api', 'checkAdmin')->post('decks/addCard', 'DeckControllerAPI@update');

/******************
USER ROUTES
 *****************/
Route::middleware('auth:api', 'checkPlayer')->get('getnewuser/{id}', 'GameControllerAPI@getuser');
Route::middleware('auth:api', 'checkPlayer')->post('createGame', 'GameControllerAPI@store');
Route::middleware('auth:api', 'checkPlayer')->delete('deleteOwnAccount', 'UserControllerAPI@deleteOwnAccount');
Route::middleware('auth:api', 'checkPlayer')->post('statisticOfUser', 'StatisticAPI@getStatisticOfUser');
Route::middleware('auth:api', 'checkPlayer')->post('/user/update', 'UserControllerAPI@update');
Route::middleware('auth:api', 'checkPlayer')->get('/game/countPieces', 'ImgControllerAPI@getImagesCount');

/******************
USER/ADMIN ROUTES
 *****************/

Route::middleware('auth:api')->post('/user/password/update', 'UserControllerAPI@updatePassword');
Route::middleware('auth:api')->post('/user/avatar/update', 'UserControllerAPI@updateAvatar');



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

//___ Set the Winner of the game ___________
Route::middleware('auth:api', 'checkAdmin')->post('setwinner', 'GameControllerAPI@setWinner');

//___ Set the Looser of the Game ___________
Route::middleware('auth:api', 'checkAdmin')->post('setloser', 'GameControllerAPI@setLoser');

//___ Set the Tied of the Game _____________
Route::middleware('auth:api', 'checkAdmin')->post('settied', 'GameControllerAPI@setTied');

//___ Change the status of the Game ________
Route::middleware('auth:api', 'checkAdmin')->post('changestatus', 'GameControllerAPI@changeStatus');

//___ Join User to a Game __________________
Route::middleware('auth:api', 'checkAdmin')->post('joingame', 'GameControllerAPI@joinGame');
