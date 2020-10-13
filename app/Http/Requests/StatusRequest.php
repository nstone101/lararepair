<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatusRequest extends FormRequest
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
                'label' => 'required',
                'fg_color' => 'required',
                'bg_color' => 'required',
                'send_sms' => 'present',
                'send_email' => 'present',
                'sms_text' => 'present',
                'email_subject' => 'present',
                'email_text' => 'present',
                'completed' => 'present',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'label' => 'required',
                'fg_color' => 'required',
                'bg_color' => 'required',
                'send_sms' => 'present',
                'send_email' => 'present',
                'sms_text' => 'present',
                'email_subject' => 'present',
                'email_text' => 'present',
                'completed' => 'present',
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
            'label' => trans('status.label'),
        ];
    }
}
