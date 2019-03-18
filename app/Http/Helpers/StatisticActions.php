<?php
/**
 * Created by PhpStorm.
 * User: tabbakka
 * Date: 9/20/18
 * Time: 2:17 PM
 */

namespace App\Http\Helpers;


class StatisticActions {

	public static $closed = 1;
	public static $clicked = 2;
	public static $viewed = 3;

	public static $day = 1;
	public static $month = 2;

	public static function getAction($action_id) {
		switch ($action_id) {
			case 1:
				return 'closed';
				break;

			case 2:
				return 'clicked';
				break;

			case 3:
				return 'viewed';
				break;

			default:
				return false;
				break;
		}
	}

	public static function getOrder($group_id) {
		switch ($group_id) {
			case 1:
				return 'day';
				break;

			case 2:
				return 'month';
				break;

			default:
				return false;
				break;
		}
	}
}