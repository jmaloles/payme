<?php

namespace App\Models\Request\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Response\ResponseMessage;

/**
 * Class RoleRelationship.
 */
trait RequestMessageRelationship
{

	public function user(){
		return $this->belongsTo(User::class);
	}

	public function response(){
		return $this->hasOne(ResponseMessage::class, 'request_id');
	}

}