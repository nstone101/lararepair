<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\VoucherRequest;
use App\Repositories\VoucherRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\Site;
use App\Voucher;

class VoucherController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;
    protected $site;

    protected $module = 'voucher';

    public function __construct(Request $request, VoucherRepository $repo, ActivityLogRepository $activity, Site $site) {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
        $this->site = $site;
    }
  

    public function PreRequisite(Request $request) {
        $id = $request->get('id') ?? null;
        return $this->success($this->repo->preRequisite($id));
    }
    
    public function index() {
        $this->middleware('permission:list-voucher');
        $query = Voucher::search()
                ->selectRaw('vouchers.id, card_no, value, balance, CONCAT(cb.first_name, " ", cb.last_name) as created_by,c.name as customer_id, expiry')
                ->leftJoin('companies as c', 'vouchers.customer_id', '=', 'c.id')
                ->leftJoin('profiles as cb', 'vouchers.created_by', '=', 'cb.id');

        return dataTable()->query($query)
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

  
    public function store(VoucherRequest $request)
    {
        $this->middleware('permission:create-voucher');
        $register = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $register->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('voucher.added')]);
    }


    public function update(VoucherRequest $request, $id) {
        $this->middleware('permission:edit-voucher');

        $register = $this->repo->findOrFail($id);
        $register = $this->repo->update($register, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $register->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('voucher.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-voucher');

        $register = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $register->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($register);

        return $this->success(['message' => trans('voucher.deleted')]);
    }

}
