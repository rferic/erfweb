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
        // Admin Content
        Route::post('/contents', 'Admin\ContentController@get')->name('admin.contents.get');
        Route::post('/contents/create', 'Admin\ContentController@create')->name('admin.contents.create');
        Route::post('/contents/{content}/restore', 'Admin\ContentController@restore')->name('admin.contents.restore');
        Route::post('/contents/{content}/update', 'Admin\ContentController@update')->name('admin.contents.update');
        Route::delete('/contents/{content}/remove', 'Admin\ContentController@remove')->name('admin.contents.remove');
        Route::delete('/contents/{content}/destroy', 'Admin\ContentController@destroy')->name('admin.contents.destroy');
        // Admin Pages
        Route::get('/pages', 'Admin\PageController@index')->name('admin.pages');
        Route::post('/pages', 'Admin\PageController@get')->name('admin.pages.get');
        Route::post('/pages/store', 'Admin\PageController@store')->name('admin.pages.store');
        Route::post('/pages/{page}/restore', 'Admin\PageController@restore')->name('admin.pages.restore');
        Route::post('/pages/get-all-slugs-page', 'Admin\PageController@getAllSlugsPage')->name('admin.pages.getAllSlugsPage');
        Route::delete('/pages/{page}/remove', 'Admin\PageController@remove')->name('admin.pages.remove');
        Route::delete('/pages/{page}/destroy', 'Admin\PageController@destroy')->name('admin.pages.destroy');
        // Admin Apps
        Route::get('/apps', 'Admin\AppController@index')->name('admin.apps');
        Route::post('/apps', 'Admin\AppController@get')->name('admin.apps.get');
        Route::post('/apps/store', 'Admin\AppController@store')->name('admin.apps.store');
        Route::delete('/apps/{app}/destroy', 'Admin\AppController@destroy')->name('admin.apps.destroy');
        // Admin Menus
        Route::post('/menus', 'Admin\MenuController@get')->name('admin.menus.get');
        // Admin Redirections
        Route::get('/redirections', 'Admin\RedirectionController@index')->name('admin.redirections');
        Route::post('/redirections/create', 'Admin\RedirectionController@create')->name('admin.redirections.create');
        Route::delete('/redirections/{redirection}/destroy', 'Admin\RedirectionController@destroy')->name('admin.redirections.destroy');
        // Admin Images
        Route::get('/images', 'Admin\ImageController@index')->name('admin.images');
        Route::post('/images', 'Admin\ImageController@get')->name('admin.images.get');
        Route::post('/images/create', 'Admin\ImageController@create')->name('admin.images.create');
        Route::post('/images/{image}/update', 'Admin\ImageController@update')->name('admin.images.update');
        Route::delete('/images/{image}/delete', 'Admin\ImageController@delete')->name('admin.images.delete');
        //Admin Slugs
        Route::post('/slugs/is-free', 'Admin\SlugController@getIsFree')->name('admin.slugs.getIsFree');
        Route::post('/slugs/get-all-slugs', 'Admin\SlugController@getAllSlugs')->name('admin.slugs.getAllSlugs');
    }
);
