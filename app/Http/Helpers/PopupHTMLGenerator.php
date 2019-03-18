<?php
/**
 * Created by PhpStorm.
 * User: tabbakka
 * Date: 10/3/18
 * Time: 5:51 PM
 */

namespace App\Http\Helpers;


use App\Event;

class PopupHTMLGenerator {

	public static function getDefaultHtml(Event $event) {

		$target = $event->new_tab ? '_blank' : '_self';
		$close = $event->close ? "<img id='x-popup-close' src='" . asset('img/close-icon.png') . "'>" : '';
		$rounded = $event->round ? " rounded" : "";

		return
			"<div class='x-popup".$rounded."' id='x-popup' style='background-color: ". $event->background_color ."!important;'>".
            "<a id='x-popup-img-url' href = '" . $event->url . "' target='" . $target . "'><img src='". $event->image ."' alt=''></a>".
			$close.
            "<div id='x-popup-text'>".
            "<span class='x-popup-title-link'><a id='x-popup-url' href='" . $event->url . "'  target='" . $target . "' style='color: " . $event->title_color . " !important;'>" . $event->title . "</a></span>".
            "<p class='x-popup-description' style='color: " . $event->message_color . "!important;'>" . $event->message . "</p>".
            "</div>".
			"</div>";
	}
}