<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/','ClientController@index');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
Route::resource('client', 'ClientController');
Route::resource('categories', 'CategoriesController');
Route::resource('entreprises', 'EntreprisesController');
Route::get('/search', 'ClientController@search');
Route::get('/detect/{id}', 'ClientController@detect');
Route::get('/adc/{id}', 'EntreprisesController@adc');
Route::get('/updst/{id}/{client_id}', 'EntreprisesController@updst');
Route::post('/addref', 'ClientController@addref');
Route::post('/adp', 'EntreprisesController@adp');
Route::post('/delc',"EntreprisesController@delc");
