<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Http\Helpers\APIResponseGenerator;
use App\Http\Helpers\StatisticActions;
use App\Http\Requests\GetStatisticRequest;
use App\Http\Requests\UpdateStatisticRequest;
use App\Statistic;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller {

	public function get(GetStatisticRequest $request, $event_id) {
		/** @var User $user */
		$user = Auth::user();
		/** @var Event $event */
		$event = Event::find($event_id);
		if (!$event) {
			return APIResponseGenerator::responseFail([
				'text' => 'Event not found',
				'code' => 404
			]);
		}
		if ($user->can('view', $event)) {
			$data = $request->all();
			$statistics = Statistic::calculate($event_id, $data);
			return APIResponseGenerator::responseTrue([
				'statistics' => $statistics
			]);
		} else {
			return APIResponseGenerator::responseFail([
				'text' => 'Access denied',
				'code' => 403
			]);
		}
	}

	public function download(GetStatisticRequest $request, $event_id) {
		return APIResponseGenerator::responseFail([
			'text' => 'In development',
			'code' => 503
		]);
	}

	public function closed(UpdateStatisticRequest $request) {
		$this->performStatistic($request->all(), 'closed');
	}

	public function clicked(UpdateStatisticRequest $request) {
		$this->performStatistic($request->all(), 'clicked');
	}

	public function viewed(UpdateStatisticRequest $request) {
		$this->performStatistic($request->all(), 'viewed');
	}

	protected function performStatistic($data, $action) {
		try {
			$data[$action] = true;
			$stat = Statistic::create($data);
			return APIResponseGenerator::responseTrue(null);
		} catch (\Exception $exception) {
			return APIResponseGenerator::responseFail([
				'text' => $exception->getMessage(),
				'code' => 500
			]);
		}
	}
}
