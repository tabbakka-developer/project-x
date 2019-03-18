<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventUpdateRequest extends FormRequest
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
	        'name' => 'nullable|min:5|max:100',
	        'url_from' => 'nullable|active_url',
	        'desktop_placement' => 'nullable|integer',
	        'mobile_placement' => 'nullable|integer',
	        'delay' => 'nullable|numeric',
	        'repeat' => 'nullable|boolean',
	        'time' => 'nullable|numeric',
	        'close'  => 'nullable|boolean',
	        'new_tab' => 'nullable|boolean',
	        'round' => 'nullable|boolean',
	        'title_color' => 'nullable',
	        'background_color' => 'nullable',
	        'message_color' => 'nullable',
	        'template_id' => 'nullable|exists:templates,id',

	        'popups.*.title' => 'required|min:5|max:200',
	        'popups.*.image' => 'required|active_url',
	        'popups.*.message' => 'required|min:10|max:200',
	        'popups.*.url_to' => 'required|active_url',
        ];
    }
}
