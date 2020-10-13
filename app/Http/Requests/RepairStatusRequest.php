<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepairStatusRequest extends FormRequest
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
            'id' => 'required',
            'status_id' => 'required',
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(){
        return [
            'id' => trans('repair.id'),
            'status_id' => trans('repair.status'),
        ];
    }
}
