<?php

Route::group(['namespace' => 'Report', 'prefix' => 'report', 'as' => 'report.'], function(){

	Route::group(['prefix' => 'pos', 'as' => 'pos.', 'namespace' => 'POS'], function(){

		Route::group([
			'prefix' 	=> 'daily', 
			'as' 		=> 'daily.'], function() {

				Route::resource('/', 'DailyReportController');

		});

		Route::group([
			'prefix' 	=> 'monthly', 
			'as' 		=> 'monthly.'], function() {

				Route::resource('/', 'MonthlyReportController');

		});

		Route::group(['prefix' => 'sale', 'as' => 'sale.'], function() {

			Route::get('get', 'ReportTableController')->name('get');

			Route::get('/', 'ReportController@index')->name('index');

			Route::get('sale/{id}', 'ReportController@show')->name('show');

			Route::delete('sale/{id}', 'ReportController@destroy')->name('destroy');

		});

	});


	Route::group(['prefix' => 'commissary', 'as' => 'commissary.', 'namespace' => 'Commissary'], function(){

		Route::group([
			'prefix' 	=> 'monthly', 
			'as' 		=> 'monthly.'], function() {

				Route::resource('/', 'MonthlyReportController');

		});

		Route::group(['prefix' => 'daily', 'as' => 'daily.'], function() {

			Route::resource('/', 'ReportController');

		});

	});
	
	
});


