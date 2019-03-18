<?php

namespace App\Http\Controllers\Api;

use App\Http\Helpers\APIResponseGenerator;
use App\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TemplatesController extends Controller
{
    public function get() {
    	return APIResponseGenerator::responseTrue([
    		'templates' => Template::all()
	    ]);
    }
}
