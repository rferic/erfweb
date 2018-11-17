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


// Web routes
Route::get('/', function () {
    return view('front/welcome');
})->name('home');

Route::get('/account', function () {
    return view('front/welcome');
})->middleware('verified')->name('account');

// Auth routes
Auth::routes(['verify' => true]);
Route::view('/password/confirm', 'auth/passwords/confirm');

// Admin routes
Route::group(
    [
        'prefix' => 'admin',
        'middleware' => ['verified', 'role:admin']
    ],
    function ()
    {
        // Admin Dashboard
        Route::get('/', 'Admin\DashboardController@index')->name('admin.dashboard');
        // Admin Profile
        Route::get('/profile', 'Admin\ProfileController@index')->name('admin.profile');
        Route::post('/profile/get-data', 'Admin\ProfileController@getData')->name('admin.profile.getData');
        Route::post('/profile/email-is-free', 'Admin\ProfileController@emailIsFree')->name('admin.profile.emailIsFree');
        Route::post('/profile/update', 'Admin\ProfileController@update')->name('admin.profile.update');
        // Admin Messages
        Route::get('/messages', 'Admin\MessageController@index')->name('admin.messages');
        Route::get('/messages/{message}', 'Admin\MessageController@detail')->name('admin.message');
        Route::post('/messages/get-state', 'Admin\MessageController@getState')->name('admin.messages.getState');
        Route::post('/messages/get-last-pending', 'Admin\MessageController@getLastPending')->name('admin.messages.getLastPending');
    }
);
