<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
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
        	'name' => 'required|min:5|max:100',
	        'url_from' => 'nullable|active_url',
	        'desktop_placement' => 'required|integer',
	        'mobile_placement' => 'required|integer',
	        'delay' => 'nullable|numeric',
	        'repeat' => 'required|boolean',
	        'time' => 'required|numeric',
	        'close'  => 'required|boolean',
	        'new_tab' => 'required|boolean',
	        'round' => 'required|boolean',
	        'title_color' => 'required',
	        'background_color' => 'required',
	        'message_color' => 'required',
	        'template_id' => 'required|exists:templates,id',

	        'popups.*.title' => 'required|min:5|max:200',
	        'popups.*.image' => 'required|active_url',
	        'popups.*.message' => 'required|min:10|max:200',
	        'popups.*.url_to' => 'required|active_url',
        ];
    }
}
