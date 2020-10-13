<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PosRegisterCloseRequest extends FormRequest
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
            'total_cash_submitted' => 'required',
            'total_cheques_submitted' => 'required',
            'total_cc_slips_submitted' => 'required',
            'note' => 'present',
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
            'total_cash_submitted' => trans('pos_register.total_cash'),
            'total_cheques_submitted' => trans('pos_register.total_cheques'),
            'total_cc_slips_submitted' => trans('pos_register.total_cc_slips'),
            'note' => trans('pos_register.note'),
        ];
        return $attributes;
    }
}
