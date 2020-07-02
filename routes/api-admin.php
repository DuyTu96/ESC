<?php

/*
|--------------------------------------------------------------------------
| API Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|

Route::domain(config('app.subdomain_admin'))->name('admin.')->namespace('Api\Admin')->group(function () {
    Route::post('login', 'Auth\LoginController@login')->name('login');
    Route::post('change-email', 'Auth\AccountSettingController@changeEmail')->name('change-email');
    Route::post('get-user', 'Auth\AccountSettingController@userByIdRequestChangeEmail')->name('get-user');
    Route::group(['namespace' => 'Auth'], function () {
        Route::group(['prefix' => 'password'], function () {
            Route::post('request', 'ForgotPasswordController@sendResetLinkEmail')->name('password.request');
            Route::post('reset', 'ResetPasswordController@reset')->name('password.reset');
            Route::post('check-token', 'ResetPasswordController@checkToken')->name('password.check');
        });
        Route::group(['prefix' => 'register'], function () {
            Route::post('', 'RegisterController@register')->name('register');
            Route::post('confirm', 'RegisterController@confirm')->name('register.confirm');
        });
    });

    Route::middleware(['auth:admin', 'is_authenticated'])->group(function() {
        Route::namespace('Auth')->group(function() {
            Route::get('auth-user', 'LoginController@getAuthenticatedUser')->name('auth-user');
            Route::post('logout', 'LoginController@logout')->name('logout');
            Route::post('change-password', 'AccountSettingController@changePassword')->name('change-password');
            Route::post('change-email-request', 'AccountSettingController@sendChangeLinkEmail')->name('change-email-request');
        });

        Route::post('business-cards/send-setting-code', 'BusinessCardController@sendSettingCode')->name('business-cards.send-setting-code');
        Route::resource('business-cards', 'BusinessCardController');
        Route::post('business-cards/{id}', 'BusinessCardController@update')->name('business-cards.update');
        Route::get('business-cards/number-of-department/{id}', 'BusinessCardController@getNumberBusinessCardOfDepartment')->name('business-cards.department');
        Route::get('business-cards/number-of-position/{id}', 'BusinessCardController@getNumberBusinessCardOfPosition')->name('business-cards.position');

        Route::resource('departments', 'DepartmentController');

        Route::resource('positions', 'PositionController');

        Route::get('inquiries/get-type', 'InquiryController@getType')->name('inquiries.get-type');
        Route::resource('inquiries', 'InquiryController');

        Route::resource('companies', 'CompanyController');

        Route::post('companies/{companyId}', 'CompanyController@update')->name('companies.update');

        Route::get('prefectures', 'PrefectureController@index')->name('prefectures.index');

        Route::name('stripe.')->prefix('stripe')->group(function () {
            Route::get('info', 'StripePaymentController@getStripeInfoOfCompany')->name('info');
            Route::post('add-card', 'StripePaymentController@addCard')->name('add-card');
            Route::delete('delete-card', 'StripePaymentController@deleteCard')->name('delete-card');
        });

        Route::post('contracts/change-reservation', 'ContractController@changeReservation')->name('contracts.change-reservation');
        Route::post('contracts/cancel-reservation', 'ContractController@cancelReservation')->name('contracts.cancel-reservation');
        Route::post('contracts/resump-reservation', 'ContractController@resumpReservation')->name('contracts.resump-reservation');
        Route::resource('contracts', 'ContractController');

        Route::resource('plans', 'PlanController');
    });
});
*/
