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

Route::get('/policy', function () {
    return view('front/welcome');
})->name('policy');

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
        // Admin routes to menu
        Route::post('/admin-menu', 'Admin\AdminMenuController@get')->name('admin.adminMenu');
        // Admin Dashboard
        Route::get('/', 'Admin\DashboardController@index')->name('admin.dashboard');
        // Admin Profile
        Route::get('/profile', 'Admin\ProfileController@index')->name('admin.profile');
        Route::post('/profile/get-data', 'Admin\ProfileController@getData')->name('admin.profile.getData');
        Route::post('/profile/email-is-free', 'Admin\ProfileController@emailIsFree')->name('admin.profile.emailIsFree');
        Route::post('/profile/update', 'Admin\ProfileController@update')->name('admin.profile.update');
        // Admin Messages
        Route::get('/messages', 'Admin\MessageController@index')->name('admin.messages');
        Route::get('/messages/trash', 'Admin\MessageController@indexTrash')->name('admin.messages.trash');
        Route::post('/messages/get-state', 'Admin\MessageController@getState')->name('admin.messages.getState');
        Route::post('/messages/get-last-pending', 'Admin\MessageController@getLastPending')->name('admin.messages.getLastPending');
        Route::post('/messages', 'Admin\MessageController@get')->name('admin.messages.get');
        Route::post('/messages/create', 'Admin\MessageController@create')->name('admin.messages.create');
        Route::post('/messages/{message}/get-author', 'Admin\MessageController@getAuthor')->name('admin.messages.getAuthor');
        Route::post('/messages/{message}/restore', 'Admin\MessageController@restore')->name('admin.messages.restore');
        Route::post('/messages/{message}/update', 'Admin\MessageController@update')->name('admin.messages.update');
        Route::delete('/messages/{message}/remove', 'Admin\MessageController@remove')->name('admin.messages.remove');
        Route::delete('/messages/{message}/destroy', 'Admin\MessageController@destroy')->name('admin.messages.destroy');
        // Admin Images temporal
        Route::post('/upload-image-temporal', 'Admin\ImageTemporalController@upload')->name('admin.imagesTemporal.upload');
        Route::delete('/remove-image-temporal', 'Admin\ImageTemporalController@remove')->name('admin.imagesTemporal.remove');
    }
);
