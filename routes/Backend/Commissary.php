<?php

Route::group(['prefix' => 'commissary', 'namespace' => 'Commissary', 'as' => 'commissary.'], function(){

	Route::group(['namespace' => 'Inventory'], function(){

		Route::get('inventory/get', 'InventoryTableController')->name('inventory.get');

		Route::resource('inventory', 'InventoryController');

	});



	Route::group(['namespace' => 'Product'], function(){

		Route::get('product/get', 'ProductTableController')->name('product.get');

		Route::resource('product', 'ProductController');

	});



	Route::group(['namespace' => 'Stock'], function(){

		Route::get('stock/get', 'StockTableController')->name('stock.get');

		Route::resource('stock', 'StockController');

	});


	Route::group(['namespace' => 'produce'], function() {

		Route::get('produce/get', 'ProduceTableController')->name('produce.get');

		Route::resource('produce', 'ProduceController');

	});


});