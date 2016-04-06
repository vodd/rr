<?php
use App\Task;
use Illuminate\Http\Request;

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
Route::group(['middleware' => 'web'], function () {
Route::get('/','EntreprisesController@blyat');

Route::post('/task', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'entreprise_id' => 'required',
        'category_id' => 'required',
    ]);

    if ($validator->fails()) {
        return redirect('/')
            ->withInput()
            ->withErrors($validator);
    }

    $task = new Task;
    $task->entreprise_id = $request->entreprise_id;
    $task->category_id = $request->category_id;
    $task->save();

    return redirect('/');
    // Create The Task...
});
Route::delete('/task/{task}', function (Task $task) {
    $task->delete();

    return redirect('/');
});

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
Route::get('/adc/{client_id}/{id}/{category_id}', 'EntreprisesController@adc');
Route::get('/updst/{id}/{client_id}', 'EntreprisesController@updst');
Route::post('/addref', 'ClientController@addref');
Route::post('/adp', 'EntreprisesController@adp');
Route::post('/delc',"EntreprisesController@delc");
    Route::get('/ent', 'EntreprisesController@autocomplete');
    Route::get('/cat', 'CategoriesController@autocomplete');
    Route::post('/histo', 'ClientController@histo');

});