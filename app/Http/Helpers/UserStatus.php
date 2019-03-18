<?php
/**
 * Created by PhpStorm.
 * User: tabbakka
 * Date: 10/8/18
 * Time: 12:47 PM
 */

namespace App\Http\Helpers;


trait UserStatus {
	
	public $codes = [
		0 => 'unpaid',
		1 => 'paid',
		2 => 'blocked'
	];

	public function getStatusName() {
		return $this->codes[$this->status];
	}

	public function getStatusCode($status) {
		return array_search($status, $this->codes);
	}

}