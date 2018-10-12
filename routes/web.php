<?php

/*
|--------------------------------------------------------------------------
| Core Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('front/welcome');
})->name('home');

Auth::routes(['verify' => true]);
Route::view('/password/confirm', 'auth/passwords/confirm');


Route::group(
    [
        'prefix' => 'dev',
        'middleware' => ['verified', 'role:admin']
    ],
    function ()
    {
        // Admin Dashboard
        Route::get('/', 'Admin\DashboardController@index')->name('admin.dashboard');
        // Admin Messages
        Route::post('/messages/get-status', 'Admin\MessageController@getStatus')->name('admin.messages.getStatus');
    }
);
