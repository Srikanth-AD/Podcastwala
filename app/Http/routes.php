<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'PodcastController@index');

Route::get('podcast/player', 'PodcastController@index');
Route::get('podcast/manage', 'PodcastController@manage');
Route::get('podcast/settings', 'PodcastController@settings');
Route::post('podcast/add', 'PodcastController@add');
Route::post('podcast/delete', 'PodcastController@delete');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
