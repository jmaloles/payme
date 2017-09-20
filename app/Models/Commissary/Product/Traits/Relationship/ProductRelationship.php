<?php

namespace App\Models\Commissary\Product\Traits\Relationship;

use App\Models\Commissary\Inventory\Inventory;

/**
 * Class RoleRelationship.
 */
trait ProductRelationship
{

	public function ingredients(){
		return $this->belongsToMany(Inventory::class, 'commissary_inventory_product', 'product_id', 'inventory_id');
	}

}