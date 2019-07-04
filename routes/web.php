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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true, 'register' => false]);

// Authenticated user (with verified email) routes
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/service-events', 'ServiceEventController@index')->name('service-events.index');
    Route::get('/service-statuses/{id}/show', 'ServiceStatusController@show')->name('service-statuses.show');
    Route::post('/service-statuses/{id}/show', 'ServiceStatusController@update')->name('service-statuses.update');
});

// Admin user only routes
Route::group(['middleware' => ['auth', 'verified', 'admin']], function () {
    Route::resource('users', 'UserController')->except(['show']);

    Route::get('/users/{user}/roles', 'UserRoleController@index')->name('user-roles.index');
    Route::post('/users/{user}/roles/attach', 'UserRoleController@attach')->name('user-roles.attach');
    Route::delete('/users/{user}/roles/{role}/detach', 'UserRoleController@detach')->name('user-roles.detach');

    Route::get('/users/{user}/keys', 'UserKeyController@index')->name('user-keys.index');
    Route::post('/users/{user}/keys', 'UserKeyController@generate')->name('user-keys.generate');
    Route::delete('/users/{user}/keys/{key}', 'UserKeyController@destroy')->name('user-keys.destroy');

    Route::resource('devices', 'DeviceController')->only(['index', 'edit', 'update']);
    Route::resource('services', 'ServiceController')->only(['index', 'edit', 'update']);

    Route::get('/maintenance', 'MaintenanceController@show')->name('maintenance.show');
    Route::post('/maintenance', 'MaintenanceController@update')->name('maintenance.update');
});
