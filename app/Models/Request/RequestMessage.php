<?php

namespace App\Models\Request;

use Illuminate\Database\Eloquent\Model;
use App\Models\Request\Traits\Relationship\RequestMessageRelationship;

class RequestMessage extends Model
{
	use RequestMessageRelationship;

	protected $table = 'requests';
    protected $fillable = ['id', 'date', 'title', 'message', 'quantity', 'unit_type', 'user_id'];
}
