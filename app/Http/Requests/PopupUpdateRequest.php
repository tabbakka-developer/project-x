<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PopupUpdateRequest extends FormRequest
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
	        'title' => 'nullable|min:5|max:200',
	        'image' => 'nullable|active_url',
	        'message' => 'nullable|min:10|max:200',
	        'url_to' => 'nullable|active_url',
        ];
    }
}
