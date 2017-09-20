<?php

/**
 * Frontend Controllers
 * All route names are prefixed with 'frontend.'.
 */
Route::get('/', 'FrontendController@index')->name('index');
Route::get('macros', 'FrontendController@macros')->name('macros');
Route::get('contact', 'ContactController@index')->name('contact');
Route::post('contact/send', 'ContactController@send')->name('contact.send');

/*
 * These frontend controllers require the user to be logged in
 * All route names are prefixed with 'frontend.'
 */
Route::group(['middleware' => 'auth'], function () {
    Route::group(['namespace' => 'User', 'as' => 'user.'], function () {
        /*
         * User Dashboard Specific
         */
        Route::get('dashboard', 'DashboardController@index')->name('dashboard');
        Route::get('dashboard/{product}/product', 'DashboardController@get')->name('dashboard.product');
        Route::post('dashboard/request', 'DashboardController@request')->name('dashboard.request');
        Route::get('dashboard/request_all', 'DashboardController@getAllRequest')->name('dashboard.request_all');
        Route::get('dashboard/get_response/{id}', 'DashboardController@getResponse')->name('dashboard.get_response');
        Route::get('dashboard/notification', 'DashboardController@notification')->name('dashboard.notification');

        /*
         * User Account Specific
         */
        Route::get('account', 'AccountController@index')->name('account');

        /*
         * User Profile Specific
         */
        Route::patch('profile/update', 'ProfileController@update')->name('profile.update');
    });
});
