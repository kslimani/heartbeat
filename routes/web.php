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
Route::group(['middleware' => ['auth', 'verified', 'settings']], function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/service-events', 'ServiceEventController@index')->name('service-events.index');

    Route::get('/service-statuses/{id}/show', 'ServiceStatusController@show')->name('service-statuses.show');
    Route::get('/service-statuses/{id}/settings', 'ServiceStatusController@editSettings')->name('service-statuses.edit-settings');
    Route::put('/service-statuses/{id}/settings', 'ServiceStatusController@updateSettings')->name('service-statuses.update-settings');

    Route::get('/account/settings', 'AccountController@settings')->name('account-settings');
    Route::get('/account/profile', 'AccountController@profile')->name('account-profile');
    Route::get('/account/security', 'AccountController@security')->name('account-security');
    Route::put('/account/settings', 'AccountController@updateSettings')->name('account-settings.update');
    Route::put('/account/profile', 'AccountController@updateProfile')->name('account-profile.update');
    Route::put('/account/password', 'AccountController@updatePassword')->name('account-password.update');
});

// Admin user only routes
Route::group(['middleware' => ['auth', 'verified', 'admin', 'settings']], function () {
    Route::resource('users', 'UserController')->except(['show']);
    Route::get('/users/search', 'UserController@search')->name('users.search');

    Route::get('/users/{user}/roles', 'UserRoleController@index')->name('user-roles.index');
    Route::post('/users/{user}/roles/attach', 'UserRoleController@attach')->name('user-roles.attach');
    Route::delete('/users/{user}/roles/{role}/detach', 'UserRoleController@detach')->name('user-roles.detach');

    Route::get('/users/{user}/keys', 'UserKeyController@index')->name('user-keys.index');
    Route::post('/users/{user}/keys', 'UserKeyController@generate')->name('user-keys.generate');
    Route::delete('/users/{user}/keys/{key}', 'UserKeyController@destroy')->name('user-keys.destroy');

    Route::get('/service-statuses', 'ServiceStatusController@index')->name('service-statuses.index');
    Route::get('/service-statuses/search', 'ServiceStatusController@search')->name('service-statuses.search');
    Route::delete('/service-statuses/{id}', 'ServiceStatusController@destroy')->name('service-statuses.destroy');

    Route::resource('devices', 'DeviceController')->only(['edit', 'update']);
    Route::resource('services', 'ServiceController')->only(['edit', 'update']);

    Route::get('/users/{user}/service-statuses', 'UserServiceStatusController@index')->name('user-service-statuses.index');
    Route::post('/users/{user}/service-statuses/attach', 'UserServiceStatusController@attach')->name('user-service-statuses.attach');
    Route::post('/users/{user}/service-statuses/attachall', 'UserServiceStatusController@attachAll')->name('user-service-statuses.attachall');
    Route::put('/users/{user}/service-statuses/{id}', 'UserServiceStatusController@update')->name('user-service-statuses.update');
    Route::post('/users/{user}/service-statuses/sync', 'UserServiceStatusController@syncWith')->name('user-service-statuses.sync');
    Route::delete('/users/{user}/service-statuses/{id}/detach', 'UserServiceStatusController@detach')->name('user-service-statuses.detach');

    Route::get('/maintenance', 'MaintenanceController@show')->name('maintenance.show');
    Route::post('/maintenance', 'MaintenanceController@update')->name('maintenance.update');
});
