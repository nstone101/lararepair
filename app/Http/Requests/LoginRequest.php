<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidRecaptcha;

class LoginRequest extends FormRequest
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
            'password' => 'required'
        ];

        if (! \Auth::check()) {
            $rules['email'] = 'required|email';
        }

        if (config('config.enable_recaptcha') && config('config.enable_recaptcha') == 1) {
            $rules['recaptcha'] = ['required', new ValidRecaptcha];
        }

        return $rules;
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(){
        return [
            'email' => trans('auth.email'),
            'password' => trans('auth.password'),
            'recaptcha.required' => 'Please ensure that you are a human!'

        ];
    }
}
