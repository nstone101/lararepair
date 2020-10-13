<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomFieldRequest extends FormRequest
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
        $id = $this->route('id');
        if ($this->method() === 'POST') {
            return [
                'title' => 'required',
                'type' => 'required',
                'answers' => 'present',
                'required' => 'present',
                'model_type' => [ Rule::in(['App\Repair', 'App\Company', 'App\Product']) ],

            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'title' => 'required',
                'type' => 'required',
                'answers' => 'present',
                'required' => 'present',
                'model_type' => [ Rule::in(['App\Repair', 'App\Company', 'App\Product']) ],
                
            ];

        }
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title' => trans('custom_field.title'),
            'type' => trans('custom_field.type'),
            'answers' => trans('custom_field.answers'),
            'required' => trans('custom_field.required'),
        ];
    }
}
