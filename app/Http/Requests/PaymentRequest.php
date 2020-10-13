<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
                'repair_id' => 'required_without_all:sale_id',
                'sale_id' => 'required_without_all:repair_id',
                'date' => 'required',
                'reference_no' => 'required',
                'paid_by' => 'required',
                'amount' => 'required',
                'cc_no' => 'present',
                'cc_holder' => 'present',
                'cc_type' => 'present',
                'cc_month' => 'present',
                'cc_year' => 'present',
                'cc_cvv' => 'present',
                'cheque_no' => 'present',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'repair_id' => 'required_without_all:sale_id',
                'sale_id' => 'required_without_all:repair_id',
                'date' => 'required',
                'reference_no' => 'required',
                'paid_by' => 'required',
                'amount' => 'required',
                'cc_no' => 'present',
                'cc_holder' => 'present',
                'cc_type' => 'present',
                'cc_month' => 'present',
                'cc_year' => 'present',
                'cc_cvv' => 'present',
                'cheque_no' => 'present',
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
            'reference_no' => trans('payment.reference_no'),
        ];
    }
}
