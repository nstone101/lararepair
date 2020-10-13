<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SmsGatewayRequest;
use App\Repositories\SmsGatewayRepository;
use App\Repositories\ActivityLogRepository;
use App\SmsGateway;

class SmsGatewayController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'sms-gateway';

    public function __construct(Request $request, SmsGatewayRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

    }
  

    public function index()
    {
        $this->middleware('permission:list-sms-gateway');
        $query = SmsGateway::search()
                ->selectRaw('id, name, url');

        return dataTable()->query($query)
            ->setFilters(
                'sms_gateways.name', 'sms_gateways.url'
            )
            
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(SmsGatewayRequest $request)
    {
        $this->middleware('permission:create-sms_gateway');

        $sms_gateway = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sms_gateway->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('sms_gateway.added')]);
    }


    public function update(SmsGatewayRequest $request, $id) {
        $this->middleware('permission:edit-sms-gateway');

        $sms_gateway = $this->repo->findOrFail($id);
        $sms_gateway = $this->repo->update($sms_gateway, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $sms_gateway->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('sms_gateway.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-sms-gateway');

        $sms_gateway = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sms_gateway->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($sms_gateway);

        return $this->success(['message' => trans('sms_gateway.deleted')]);
    }

}
