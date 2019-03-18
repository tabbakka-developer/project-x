<?php
/**
 * Created by PhpStorm.
 * User: tabbakka
 * Date: 9/18/18
 * Time: 5:19 PM
 */

namespace App\Http\Helpers;


class APIResponseGenerator {

	public static function responseTrue($data) {
		return response()->json($data, 200);
	}

	public static function responseFail($error) {
		return response()->json([
			'error' => $error['text']
		], $error['code']);
	}

}