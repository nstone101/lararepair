<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PosRequest extends FormRequest
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
                'customer' => 'required',
                'items' => 'required',
                'payments' => 'present',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'customer' => 'required',
                'items' => 'required',
                'payments' => 'present',
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
            'customer' => trans('customer.customer'),
            'items' => trans('pos.name'),
            'payments' => trans('pos.payments'),
        ];
    }
}
