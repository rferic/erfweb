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

// Admin routes
Route::group(
    [
        'prefix' => 'admin',
        'middleware' => ['verified', 'role:superadministrator|administrator']
    ],
    function ()
    {
        // Admin routes to menu
        Route::post('/admin-menu', 'Admin\AdminMenuController@get')->name('admin.adminMenu');
        // Admin Dashboard
        Route::get('/', 'Admin\DashboardController@index')->name('admin.dashboard');
        Route::post('/get-statistics', 'Admin\DashboardController@getStatistics')->name('admin.dashboard.getStatistics');
        // Admin Profile
        Route::get('/profile', 'Admin\ProfileController@index')->name('admin.profile');
        Route::post('/profile/get-data', 'Admin\ProfileController@getData')->name('admin.profile.getData');
        // Admin Messages
        Route::get('/messages', 'Admin\MessageController@index')->name('admin.messages');
        Route::get('/messages/trash', 'Admin\MessageController@indexTrash')->name('admin.messages.trash');
        Route::get('/messages/{message}', 'Admin\MessageController@detail')->name('admin.messages.detail');
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
        Route::post('/pages/get-all-parents', 'Admin\PageController@getAllParents')->name('admin.pages.getAllParents');
        Route::delete('/pages/{page}/remove', 'Admin\PageController@remove')->name('admin.pages.remove');
        Route::delete('/pages/{page}/destroy', 'Admin\PageController@destroy')->name('admin.pages.destroy');
        // Admin Page Locales
        Route::post('/page-locales', 'Admin\PageLocaleController@get')->name('admin.pageLocales.get');
        // Admin Apps
        Route::get('/apps', 'Admin\AppController@index')->name('admin.apps');
        Route::post('/apps', 'Admin\AppController@get')->name('admin.apps.get');
        Route::post('/apps/store', 'Admin\AppController@store')->name('admin.apps.store');
        Route::delete('/apps/{app}/destroy', 'Admin\AppController@destroy')->name('admin.apps.destroy');
        // Admin Menus
        Route::get('/menus', 'Admin\MenuController@index')->name('admin.menus');
        Route::post('/menus', 'Admin\MenuController@get')->name('admin.menus.get');
        Route::post('/menus/store', 'Admin\MenuController@store')->name('admin.menus.store');
        Route::delete('/menus/{menu}/destroy', 'Admin\MenuController@destroy')->name('admin.menus.destroy');
        // Admin Redirections
        Route::get('/redirections', 'Admin\RedirectionController@index')->name('admin.redirections');
        Route::post('/redirections', 'Admin\RedirectionController@get')->name('admin.redirections.get');
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
        // Admin Users
        Route::get('/users', 'Admin\UserController@index')->name('admin.users');
        Route::get('/admins', 'Admin\UserController@indexAdmins')->name('admin.admins');
        Route::get('/users/{user}', 'Admin\UserController@detail')->name('admin.users.detail');
        Route::get('/admins/{user}', 'Admin\UserController@detail')->name('admin.admins.detail');
        Route::post('/users/get', 'Admin\UserController@get')->name('admin.users.get');
        Route::post('/users/{user}/get-data', 'Admin\UserController@getData')->name('admin.users.getData');
        Route::post('/users/{user}/update', 'Admin\UserController@update')->name('admin.users.update');
        Route::post('/users/{user}/email-is-free', 'Admin\UserController@emailIsFree')->name('admin.users.emailIsFree');
        Route::post('/users/{user}/disable', 'Admin\UserController@disable')->name('admin.users.disable');
        Route::post('/users/{user}/enable', 'Admin\UserController@enable')->name('admin.users.enable');
        Route::post('/users/{user}/get-apps-to-attach', 'Admin\UserController@getAppsToAttach')->name('admin.users.getAppsToAttach');
        Route::post('/users/{user}/attach-app', 'Admin\UserController@attachApp')->name('admin.users.attachApp');
        Route::post('/users/{user}/detach-app', 'Admin\UserController@detachApp')->name('admin.users.detachApp');
        Route::post('/users/{user}/disable-attach-app', 'Admin\UserController@disableAttachApp')->name('admin.users.disableAttachApp');
        Route::post('/users/{user}/enable-attach-app', 'Admin\UserController@enableAttachApp')->name('admin.users.enableAttachApp');
        Route::delete('/users/{user}/destroy', 'Admin\UserController@destroy')->name('admin.users.destroy');
    }
);
// Web routes
Route::localizedGroup(function () {
    // Auth routes
    // Authentication Routes...
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');

    // Registration Routes...
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');

    // Password Reset Routes...
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Auth::routes(['verify' => true]);
    Route::view('/password/confirm', 'auth/passwords/confirm');
    // Front
    Route::get('/', 'Front\PageController@home')->name('home');
    Route::get('/{slug}', 'Front\PageController@index')->name('index');

    Route::transGet('routes.account', function () {
        return view('front/welcome');
    })->middleware('verified')->name('account');

    Route::transGet('routes.policy', function () {
        return view('front/welcome');
    })->name('policy');
});
