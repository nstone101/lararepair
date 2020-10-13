<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
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
        $rules = [
            'value' => 'required',
            'customer_id' => 'required',
            'expiry' => 'present',
            'card_no' => 'required',
        ];



        return $rules;
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(){
        $attributes = [
            'customer_id' => trans('customer.name'),
            'value' => trans('voucher.value'),
            'expiry' => trans('voucher.expiry'),
            'card_no' => trans('voucher.card_no'),
        ];

        return $attributes;
    }
}
