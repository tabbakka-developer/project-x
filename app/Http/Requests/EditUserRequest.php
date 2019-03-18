<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
    	$user = Auth::user();
	    return [
		    'name' => 'nullable|min:2|max:50',
		    'email' => 'nullable|email|unique:users,email,'.$user->id,
		    'password' => 'nullable|confirmed|min:8|max:32',
		    'old_password' => 'required_with:password',
		    'phoneNumber' => 'nullable'
	    ];
    }
}
