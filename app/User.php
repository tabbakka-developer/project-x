<?php

namespace App;

use App\Http\Helpers\UserStatus;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use Notifiable;
    use UserStatus;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phoneNumber'
    ];

    protected $attributes = [
        'status' => 0
    ];

    protected $with = [
    	'roles',
    ];

    protected $appends = [
    	'status_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function block() {
    	try {
		    $this->status = $this->getStatusCode('blocked');
		    $this->save();
		    return true;
	    } catch (\Exception $exception) {
    		Log::error($exception);
    		return false;
	    }
    }

    public function unblock() {
	    try {
		    $this->status = $this->getStatusCode('unpaid');
		    $this->save();
		    return true;
	    } catch (\Exception $exception) {
		    Log::error($exception);
		    return false;
	    }
    }

    public function getStatusNameAttribute() {
    	return $this->getStatusName();
    }

	public static function createUser($data) {
    	$user = self::create($data);
	    $role = Role::where('role_name', 'user')->first();
	    if (!$role) {
		    throw new \Exception('SEEEEED!');
	    }
	    $user->roles()->attach($role->id);
	    $user->save();
	    return $user;
    }

    public function hasRole($roleName) {
    	$roles = $this->roles()->first();
    	if ($roles->role_name != $roleName) {
    		return false;
	    }
	    return true;
    }

    public static function createAdmin($data) {
	    $user = self::create($data);
	    $role = Role::where('role_name', 'admin')->first();
	    if (!$role) {
		    throw new \Exception('SEEEEED!');
	    }
	    $user->roles()->attach($role->id);
	    $user->save();
	    return $user;
    }

	public function checkPassword($old) {
	    if (Hash::check($old, $this->password)) {
			return true;
	    }
	    else {
	    	return false;
	    }
    }

	public function setPasswordAttribute($value)
	{
		if ($value != null) {
			$this->attributes['password'] = Hash::make($value);
		} else {
			$this->attributes['password'] = null;
		}
	}

	public function events() {
    	return $this->hasMany(Event::class);
	}

	public function folder() {
    	return "/users_folders/" . $this->id . "/";
	}

	public function roles() {
		return $this->belongsToMany(
			Role::class,
			'users_roles',
			'user_id',
			'role_id'
		);
    }
}
