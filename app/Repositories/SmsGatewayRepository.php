<?php
namespace App\Repositories;

use App\SmsGateway;
use Illuminate\Validation\ValidationException;

class SmsGatewayRepository
{
    protected $sms_gateway;

    public function __construct(SmsGateway $sms_gateway)
    {
        $this->sms_gateway = $sms_gateway;
    }


    public function create($request, $register = 0)
    {

        $params = $request->all();
        $params['postdata'] = json_encode($params['postdata']);
        $params['user_id'] = \Auth::user()->id;

        $sms_gateway = $this->sms_gateway->forceCreate($params);
        return $sms_gateway;
    }


    public function update(SmsGateway $sms_gateway, $params)
    {
        $params = $params->all();
        $params['postdata'] = json_encode($params['postdata']);
        $params['user_id'] = \Auth::user()->id;

        $sms_gateway->forceFill($params, 'update')->save();
        return $sms_gateway;
    }

       
    public function findOrFail($id)
    {
        $sms_gateway = $this->sms_gateway->find($id);

        if (! $sms_gateway) {
            throw ValidationException::withMessages(['message' => trans('sms_gateway.could_not_find')]);
        }

        return $sms_gateway;
    }

    public function delete(SmsGateway $sms_gateway)
    {
        return $sms_gateway->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->sms_gateway->whereIn('id', $ids)->delete();
    }
}
