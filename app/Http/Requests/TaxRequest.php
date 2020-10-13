<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxRequest extends FormRequest
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
                'code' => 'required|unique:tax_rates,code,'.$id.',id',
                'name' => 'required',
                'rate' => 'required|numeric',
                'type' => 'required',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'code' => 'required',
                'name' => 'required',
                'rate' => 'required|numeric',
                'type' => 'required',
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
            'name' => trans('tax.name'),
            'code' => trans('tax.code'),
            'rate' => trans('tax.rate'),
            'type' => trans('tax.type'),
        ];
    }
}
