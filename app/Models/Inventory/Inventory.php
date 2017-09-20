<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Traits\Attribute\InventoryAttribute;
use App\Models\Inventory\Traits\Relationship\InventoryRelationship;

class Inventory extends Model
{
	use InventoryAttribute, InventoryRelationship;

    protected $fillable = ['name', 'reorder_level', 'unit_type', 'fromCommissary', 'category_id'];

    protected $hidden = ['created_at', 'updated_at'];
}
