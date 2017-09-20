<?php

namespace App\Models\Order\Traits\Relationship;

use App\Models\OrderList\OrderList;

/**
 * Class RoleRelationship.
 */
trait OrderRelationship
{

	public function order_list(){
		return $this->hasMany(OrderList::class);
	}

}