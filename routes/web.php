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

Route::get('/', 'AppController@start');
Route::get('/match', 'AppController@makeMatch')->name('match');
Route::get('/pinwall', 'LocationsController@showPinwall')->name('pinwall');

Route::get('/chat/messages', 'Chat\ChatMessageController@index');
Route::post('/chat/messages', 'Chat\ChatMessageController@store');


// Static Stuff 
Route::get('/frequentquestions', 'Statics\FrequentAskedQuestionsController@index');

// Send Feedback
Route::post('/feedback','Feedback\FeedbackController@store');

// Subscribe to Notificaitons
Route::post('/subscribeToNotifications','Account\AccountsController@subscribeToNotifications');
Route::get('/subscribe/activate','Account\AccountsController@activate')->name('subscription.activate');

// login via QRCode
Route::get('/tokenLogin','Account\AccountsController@loginViaToken')->name('login.token');

// umatch api
Route::post('/umatch','MatchesController@unmatch');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


// Api Routen für axios
Route::get('/api/match', 'MatchesController@show')->name('match--api');
