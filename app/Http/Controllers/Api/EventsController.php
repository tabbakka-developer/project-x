<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Http\Helpers\APIResponseGenerator;
use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Requests\RemovePopupRequest;
use App\Popup;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EventsController extends Controller {

	public function index() {
		try {
			/** @var User $user */
			$user = Auth::user();
			$events = $user->events()->simplePaginate(15);
			return APIResponseGenerator::responseTrue($events);
		} catch (\Exception $exception) {
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}

	public function store(EventStoreRequest $request) {
		$data = $request->all();
		try {
			$data['user_id'] = Auth::user()->id;
			$event = Event::createWithChildes($data);
			if (!$event) {
				return APIResponseGenerator::responseFail([
					'text' => 'Error creating event',
					'code' => 500
				]);
			}
			$event = Event::with('popups')->where('id', $event->id)->get();
			return APIResponseGenerator::responseTrue([
				'event' => $event
			]);
		} catch (\Exception $exception) {
			Log::error($exception);
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}

	public function show($id) {
		/** @var User $user */
		$user = Auth::user();
		$event = Event::with('popups')->where('id', $id)->first();
		if (!$event) {
			return APIResponseGenerator::responseFail([
				'text' => 'Event not found',
				'code' => 404
			]);
		}
		if ($user->can('view', $event)) {
			return APIResponseGenerator::responseTrue($event);
		} else {
			return APIResponseGenerator::responseFail([
				'text' => 'Access denied',
				'code' => 403
			]);
		}
	}

	public function update(EventUpdateRequest $request, $id) {
		/** @var User $user */
		$user = Auth::user();
		/** @var Event $event */
		$event = Event::with('popups')->where('id', $id)->first();
		if (!$event) {
			return APIResponseGenerator::responseFail([
				'text' => 'Event not found',
				'code' => 404
			]);
		}
		if ($user->can('update', $event)) {
			$data = $request->all();
			try {
				$event->updateWithChildes($data);
				if (!$event) {
					return APIResponseGenerator::responseFail([
						'text' => 'Error updating event',
						'code' => 500
					]);
				}
				return APIResponseGenerator::responseTrue($event);
			} catch (\Exception $exception) {
				return APIResponseGenerator::responseFail([
					'text' => $exception->getMessage(),
					'code' => 500
				]);
			}
		} else {
			return APIResponseGenerator::responseFail([
				'text' => 'Access denied',
				'code' => 403
			]);
		}
	}

	public function destroy($id) {
		/** @var User $user */
		$user = Auth::user();
		$event = Event::find($id);
		if (!$event) {
			return APIResponseGenerator::responseFail([
				'text' => 'Event not found',
				'code' => 404
			]);
		}
		if ($user->can('delete', $event)) {
			try {
				$event->delete();
				return APIResponseGenerator::responseTrue(null);
			} catch (\Exception $exception) {
				return APIResponseGenerator::responseFail([
					'text' => $exception->getMessage(),
					'code' => 500
				]);
			}
		} else {
			return APIResponseGenerator::responseFail([
				'text' => 'Access denied',
				'code' => 403
			]);
		}
	}

	public function list() {
		try {
			$events = Event::with('user')->get();
			return APIResponseGenerator::responseTrue([
				'events' => $events
			]);
		} catch (\Exception $exception) {
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}

	public function removePopup(RemovePopupRequest $request, $id) {
		try {
			/** @var User $user */
			$user = Auth::user();
			$event = Event::find($id);
			if (!$event) {
				return APIResponseGenerator::responseFail([
					'text' => 'Event not found',
					'code' => 404
				]);
			}
			if ($user->can('update', $event)) {
				$data = $request->all();
				Popup::where('event_id', $id)->where('id', $data['popup_id'])->delete();
				return APIResponseGenerator::responseTrue(null);
			} else {
				return APIResponseGenerator::responseFail([
					'text' => 'Access denied',
					'code' => 403
				]);
			}
		} catch (\Exception $exception) {
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}
}
