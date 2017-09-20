<?php

namespace App\Models\Inventory\Traits\Relationship;

use App\Models\Category\Category;
use App\Models\Product\Product;
use App\Models\Stock\Stock;
use App\Models\ProductSize\ProductSize;

/**
 * Class RoleRelationship.
 */
trait InventoryRelationship
{

	public function category(){
		return $this->belongsTo(Category::class);
	}

	public function product_size(){
		return $this->belongsToMany(ProductSize::class, 'inventory_product_size', 'inventory_id' ,'product_size_id');
	}

	public function stocks(){
		return $this->hasMany(Stock::class);
	}

}