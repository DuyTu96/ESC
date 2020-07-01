<?php

/*
|--------------------------------------------------------------------------
| API Portal Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::domain(config('app.subdomain_portal'))->name('portal.')->namespace('Api\Portal')->group(function () {
    Route::post('login', 'Auth\AuthController@login')->name('login');
    Route::group(['namespace' => 'Auth'], function () {
        Route::group(['prefix' => 'password'], function () {
            Route::post('request', 'ForgotPasswordController@sendResetLinkEmail')->name('password.request');
            Route::post('reset', 'ResetPasswordController@reset')->name('password.reset');
        });
    });

    Route::middleware(['auth:portal'])->group(function() {
        Route::namespace('Auth')->group(function() {
            Route::get('auth-user', 'AuthController@getAuthenticatedUser')->name('auth-user');
            Route::post('logout', 'AuthController@logout')->name('logout');
        });

        Route::resource('users', 'UserController');
        Route::post('users/{id}', 'UserController@update')->name('users.update');

        Route::resource('company-admin-users', 'CompanyAdminUserController');
        Route::post('company-admin-users/{id}', 'CompanyAdminUserController@update')->name('company-admin-users.update');

        Route::get('prefectures', 'PrefectureController@index')->name('prefectures.index');

        Route::group(['prefix' => 'settlements'], function() {
            Route::get('', 'SettlementController@search')->name('settlements.search');
            Route::post('capture', 'SettlementController@capture')->name('settlements.capture');
        });

        Route::resource('business-cards', 'BusinessCardController');
        Route::post('business-cards/{id}', 'BusinessCardController@update')->name('business-cards.update');
    });
});
*/
