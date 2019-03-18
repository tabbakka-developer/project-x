<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PopupStoreRequest extends FormRequest
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
        return [
            'event_id' => 'required|exist:events,id',
	        'title' => 'required|min:5|max:200',
	        'image' => 'required|active_url',
	        'message' => 'required|min:10|max:200',
	        'url_to' => 'required|active_url',
        ];
    }
}
