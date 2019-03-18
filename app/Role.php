<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $fillable = [
		'role_name'
	];

    public function users() {
    	return $this->belongsToMany(
    		User::class,
		    'users_roles',
		    'role_id',
		    'user_id'
	    );
    }
}
