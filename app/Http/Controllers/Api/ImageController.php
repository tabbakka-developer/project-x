<?php

namespace App\Http\Controllers\Api;

use App\Http\Helpers\APIResponseGenerator;
use App\Http\Requests\StoreImageRequest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    public function store(StoreImageRequest $request) {
    	/** @var UploadedFile $image */
    	/** @var User $user */
    	try {
		    $user = Auth::user();
		    $image = $request->file('image');
		    $location = $user->folder() .
			    $image->getClientOriginalName();
		    $image->move(public_path() . $user->folder(), $image->getClientOriginalName());
		    return APIResponseGenerator::responseTrue([
			    'image' => asset($location)
		    ]);
	    } catch (\Exception $exception) {
    	    return APIResponseGenerator::responseFail([
    	    	'text' => $exception->getMessage(),
		        'code' => 500
	        ]);
	    }
    }
}
