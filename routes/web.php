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
});

// Admin user only routes
Route::group(['middleware' => ['auth', 'verified', 'admin']], function () {
    Route::resource('users', 'UserController', ['except' => ['show']]);

    Route::get('/users/{user}/roles', 'UserRoleController@index')->name('user-roles.index');
    Route::post('/users/{user}/roles/add', 'UserRoleController@add')->name('user-roles.add');
    Route::delete('/users/{user}/roles/{role}/remove', 'UserRoleController@remove')->name('user-roles.remove');
});
