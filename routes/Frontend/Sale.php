<?php

Route::group(['namespace' => 'Sale'], function(){

	Route::post('sale/save', 'SaleController@save')->name('sale.save');

});