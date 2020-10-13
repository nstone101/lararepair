<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmsGatewayRequest extends FormRequest
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
                'name' => 'required',
                'url' => 'required',
                'to_name' => 'required',
                'message_name' => 'required',
                'notes' => 'present',
                'postdata' => 'present',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'name' => 'required',
                'url' => 'required',
                'to_name' => 'required',
                'message_name' => 'required',
                'notes' => 'present',
                'postdata' => 'present',
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
            'name' => trans('sms_gateway.name'),
        ];
    }
}
