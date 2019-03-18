<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetStatisticRequest extends FormRequest
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
            'date_from' => 'nullable|date_format:"d-m-Y"',
	        'date_to' => 'nullable|date_format:"d-m-Y"',
	        'action' => 'nullable|integer|digits_between:min=1,max=3',
	        'group_by' => 'nullable|integer|digits_between:1,2'
        ];
    }
}
