<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
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
                'code' => 'required|unique:currencies,code,'.$id.',id',
                'name' => 'required',
                'symbol' => 'required',
                'symbol_position' => 'required',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'code' => 'required',
                'name' => 'required',
                'symbol' => 'required',
                'symbol_position' => 'required',
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
            'name' => trans('currency.name'),
            'code' => trans('currency.code'),
            'symbol' => trans('currency.symbol'),
            'symbol_position' => trans('currency.symbol_position'),
        ];
    }
}
