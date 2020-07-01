<?php

/*
|--------------------------------------------------------------------------
| API User Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::domain(config('app.subdomain_user'))->name('user.')->namespace('Api')->group(function () {
    Route::namespace('User')->group(function () {
        Route::post('login', 'Auth\LoginController@login')->name('login');
        Route::post('change-email', 'Auth\AccountSettingController@changeEmail')->name('change-email');
        Route::post('get-user', 'Auth\AccountSettingController@userByIdRequestChangeEmail')->name('get-user');
        Route::get('login/{service}', 'Auth\SocialController@redirect');
        Route::get('login/{service}/callback', 'Auth\SocialController@callback');
        Route::post('login-social', 'Auth\SocialController@loginSocial')->name('login.social');
        Route::group(['prefix' => 'register', 'namespace' => 'Auth'], function () {
            Route::post('', 'RegisterController@register')->name('register');
            Route::post('confirm', 'RegisterController@confirm')->name('register.confirm');
        });
        Route::group(['prefix' => 'password', 'namespace' => 'Auth'], function () {
            Route::post('request', 'ForgotPasswordController@sendResetLinkEmail')->name('password.request');
            Route::post('reset', 'ResetPasswordController@reset')->name('password.reset');
            Route::post('check-token', 'ResetPasswordController@checkToken')->name('password.check');
        });

        Route::get('address-book', 'AddressBookController@index')->name('address-book.index');
        Route::get('business-cards/detail/{id}', 'BusinessCardController@show')->name('business-cards.show');
        Route::get('address-book/business-card/{businessCardId}', 'AddressBookController@getBusinessCard')->name('address-book.business-card-detail');
        Route::delete('address-book/business-card/{businessCardId}', 'AddressBookController@destroy')->name('address-book.business-card.destroy');
        Route::post('address-book/recovery', 'AddressBookController@recovery')->name('address-book.recovery');

        Route::middleware(['auth:user', 'is_authenticated'])->group(function () {
            Route::namespace('Auth')->group(function () {
                Route::get('auth-user', 'LoginController@getAuthenticatedUser')->name('auth-user');
                Route::post('logout', 'LoginController@logout')->name('logout');
                Route::post('change-password', 'AccountSettingController@changePassword')->name('change-password');
                Route::post('change-email-request', 'AccountSettingController@sendChangeLinkEmail')->name('change-email-request');
            });

            Route::post('business-cards/active-setting-code', 'BusinessCardController@activeSettingCode')->name('business-cards.active-setting-code');
            Route::get('business-cards/edit', 'BusinessCardController@edit')->name('business-cards.get-edit');
            Route::get('business-cards/unassign-user', 'BusinessCardController@unassignUser')->name('business-cards.unassign-user');
            Route::get('business-cards/group', 'BusinessCardController@getGroup')->name('business-card-group.index');
            Route::post('business-cards/update-display-order-group', 'BusinessCardController@updateDisplayOrderGroup')->name('business-cards.update-display-order-group');
            Route::get('business-cards/group-by-name', 'BusinessCardController@groupByName')->name('business-cards.group-by-name');
            Route::post('business-cards/add-multiple-group', 'BusinessCardController@addMultipleBusinessCardGroups')->name('business-cards.add-multiple-group');
            Route::post('business-cards/{id}', 'BusinessCardController@update')->name('business-cards.update');

            Route::get('business-cards/department/{id}', 'BusinessCardController@getByDepartmentId')->name('business-cards.get-business-card-by-department-id');
            Route::get('business-cards/position/{id}', 'BusinessCardController@getByPositionId')->name('business-cards.get-business-card-by-position-id');
            Route::delete('business-cards/delete-business-card-group/{id}', 'BusinessCardController@deleteBusinessCardGroups')->name('business-cards.delete-business-card-group');
            Route::resource('business-cards', 'BusinessCardController');
            Route::get('user/business-cards', 'BusinessCardController@getInfo')->name('business-cards.info');

            Route::get('companies/department', 'CompanyController@getDepartment')->name('companies.department');
            Route::get('companies/position', 'CompanyController@getPosition')->name('companies.position');
        });
        Route::get('business-cards', 'BusinessCardController@index')->name('business-cards.index');

        Route::get('business-cards/group/{id}', 'BusinessCardController@getBusinessCardInGroup')->name('business-card-group.show');
        Route::get('qr/i/{qr_i_token}', 'QRController@getBusinessCard')->name('qr.individual.index');
        Route::get('qr/g/{qr_g_token}', 'QRController@getBusinessCardGroup')->name('qr.group.index');
        Route::resource('receipt-histories', 'ReceiptHistoryController');

        Route::get('companies/{id}', 'CompanyController@show')->name('companies.show');
    });

    // Use admin/controller
    Route::namespace('Admin')->group(function () {
        Route::get('inquiries/get-type', 'InquiryController@getType')->name('inquiries.get-type');
        Route::resource('inquiries', 'InquiryController');
    });
});
*/
