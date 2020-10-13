<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CostingRequest extends FormRequest
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
                'quantity' => 'required',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'quantity' => 'required',
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
            'quantity' => trans('product.quantity'),
        ];
    }
}
