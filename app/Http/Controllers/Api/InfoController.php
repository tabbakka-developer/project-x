<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\APIResponseGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    //
	public function faq() {
		return APIResponseGenerator::responseTrue([
			'faq' => [
				'Title-1' => 'Text-1',
				'Title-2' => 'Text-2',
				'Title-3' => 'Text-3'
			]
		]);
	}
}
