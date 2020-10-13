<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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
                'code' => 'required|unique:units,code,'.$id.',id',
                'name' => 'required',
                'allow_decimal' => 'required|int',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'code' => 'required',
                'name' => 'required',
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
            'name' => trans('unit.name'),
            'code' => trans('unit.code'),
            'allow_decimal' => trans('unit.allow_decimal'),
        ];
    }
}
