<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Http\Helpers\APIResponseGenerator;
use App\Http\Helpers\PopupHTMLGenerator;
use App\Http\Requests\GetPopupRequest;
use App\Http\Requests\PopupListRequest;
use App\Http\Requests\PopupStoreRequest;
use App\Http\Requests\PopupUpdateRequest;
use App\Popup;
use App\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PopupsController extends Controller {

	public function get(GetPopupRequest $request) {
		$data = $request->all();
		try {
			$event = Event::with('template')->where('hash', $data['hash'])->get()->first();
			if (!$event) {
				return APIResponseGenerator::responseFail([
					'text' => 'Event not found',
					'code' => 404
				]);
			}
			return APIResponseGenerator::responseTrue([
				'event' => $event,
				'html' => PopupHTMLGenerator::getDefaultHtml($event)
			]);
		} catch (\Exception $exception) {
			Log::error($exception);
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}

//	public function index(PopupListRequest $request) {
//		try {
//			/** @var User $user */
//			$user = Auth::user();
//			$data = $request->all();
//			/** @var Event $event */
//			$event = Event::find($data['event_id']);
//			if($event != null) {
//				$popups = $event->popups();
//				return APIResponseGenerator::responseTrue([
//					'popups' => $popups
//				]);
//			} else {
//				return APIResponseGenerator::responseFail([
//					'text' => 'Event not found',
//					'code' => 404
//				]);
//			}
//
//		} catch (\Exception $exception) {
//			return APIResponseGenerator::responseFail([
//				'text' => $exception->getMessage(),
//				'code' => 500
//			]);
//		}
//	}
//
//	public function store(PopupStoreRequest $request) {
//		$data = $request->all();
//		try {
//			$data['user_id'] = Auth::user()->id;
//			$popup = Popup::create($data);
//			if (!$popup) {
//				return APIResponseGenerator::responseFail([
//					'text' => 'Error creating event',
//					'code' => 500
//				]);
//			}
//			return APIResponseGenerator::responseTrue([
//				'popup' => $popup
//			]);
//		} catch (\Exception $exception) {
//			return APIResponseGenerator::responseFail([
//				'text' => $exception->getMessage(),
//				'code' => 500
//			]);
//		}
//	}
//
//	public function show($id) {
//		/** @var User $user */
//		$user = Auth::user();
//		$popup = Popup::find($id);
//		if (!$popup) {
//			return APIResponseGenerator::responseFail([
//				'text' => 'Event not found',
//				'code' => 404
//			]);
//		}
//		if ($user->can('view', $popup)) {
//			return APIResponseGenerator::responseTrue($popup);
//		} else {
//			return APIResponseGenerator::responseFail([
//				'text' => 'Access denied',
//				'code' => 403
//			]);
//		}
//	}
//
//	public function update(PopupUpdateRequest $request, $id) {
//		/** @var User $user */
//		$user = Auth::user();
//		/** @var Event $event */
//		$popup = Popup::find($id);
//		if (!$popup) {
//			return APIResponseGenerator::responseFail([
//				'text' => 'Popup not found',
//				'code' => 404
//			]);
//		}
//		if ($user->can('update', $popup)) {
//			$data = $request->all();
//			try {
//				$popup->update($data);
//				return APIResponseGenerator::responseTrue($popup);
//			} catch (\Exception $exception) {
//				return APIResponseGenerator::responseFail([
//					'text' => $exception->getMessage(),
//					'code' => 500
//				]);
//			}
//		} else {
//			return APIResponseGenerator::responseFail([
//				'text' => 'Access denied',
//				'code' => 403
//			]);
//		}
//	}
//
//	public function destroy($id) {
//		/** @var User $user */
//		$user = Auth::user();
//		$event = Event::find($id);
//		if (!$event) {
//			return APIResponseGenerator::responseFail([
//				'text' => 'Event not found',
//				'code' => 404
//			]);
//		}
//		if ($user->can('delete', $event)) {
//			try {
//				$event->delete();
//				return APIResponseGenerator::responseTrue(null);
//			} catch (\Exception $exception) {
//				return APIResponseGenerator::responseFail([
//					'text' => $exception->getMessage(),
//					'code' => 500
//				]);
//			}
//		} else {
//			return APIResponseGenerator::responseFail([
//				'text' => 'Access denied',
//				'code' => 403
//			]);
//		}
//	}
}
