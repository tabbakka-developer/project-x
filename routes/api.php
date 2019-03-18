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

Route::group(['prefix' => 'auth'], function () {
	Route::post('login', 'Api\AuthController@login');
	Route::post('register', 'Api\AuthController@register');
	Route::post('checkEmail', 'Api\AuthController@checkEmail');
	Route::post('recover', 'Api\AuthController@passwordRecovery');
});


Route::group(['middleware' => 'jwt.auth'], function () {
	Route::group(['prefix' => 'auth'], function () {
		Route::get('logout', 'Api\AuthController@logout');
	});

	Route::group(['prefix' => 'templates'], function () {
		Route::get('get', 'Api\TemplatesController@get');
	});

	Route::group(['prefix' => 'user'], function () {
		Route::get('get', 'Api\UsersController@get');
		Route::post('edit', 'Api\UsersController@edit');
		Route::get('paymentHistory', 'Api\UsersController@paymentHistory');
	});

	Route::group(['prefix' => 'statistic'], function () {
		Route::get('get/{event_id}', 'Api\StatisticsController@get');
		Route::get('download/{event_id}', 'Api\StatisticsController@download');
	});

	Route::group(['prefix' => 'image'], function () {
		Route::post('store', 'Api\ImageController@store');
	});

	Route::post('removePopup/{$id}', 'Api\EventsController@removePopup');
	Route::apiResource('events', 'Api\EventsController');
//	Route::apiResource('popups', 'Api\PopupsController');

	Route::group([
		'middleware' => 'can:admin',
		'prefix' => 'admin'
	], function () {

		Route::group(['prefix' => 'users'], function () {
			Route::get('list', 'Api\UsersController@list');
			Route::get('show/{user_id}', 'Api\UsersController@show');
			Route::get('test/{user_id}', 'Api\UsersController@block');
		});

		Route::group(['prefix' => 'events'], function () {
			Route::get('list', 'Api\EventsController@list');
		});
	});
});

Route::group(['prefix' => 'popups'], function () {
	Route::get('get', 'Api\PopupsController@get');
	Route::post('closed', 'Api\StatisticsController@closed');
	Route::post('clicked', 'Api\StatisticsController@clicked');
	Route::post('viewed', 'Api\StatisticsController@viewed');
});

Route::group(['prefix' => 'info'], function () {
	Route::get('faq', 'Api\InfoController@faq');
});


