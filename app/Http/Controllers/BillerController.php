<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BillerRequest;
use App\Repositories\CompanyRepository;
use App\Repositories\ActivityLogRepository;
use App\Company;

class BillerController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'biller';

    public function __construct(Request $request, CompanyRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
    }

    public function index()
    {
        $this->middleware('permission:list-biller');
        $query = Company::search()
                ->onlyBillers()
                ->selectRaw('id, company, name, email, phone, city, country, tax_number');

        return dataTable()->query($query)
            ->setFilters(
                'companies.name', 'companies.company', 'companies.email', 'companies.phone',
                'companies.address', 'companies.city', 'companies.country'
            )
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(BillerRequest $request)
    {
        $this->middleware('permission:create-biller');

        $biller = $this->repo->create($this->request, 'biller');

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $biller->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('biller.added')]);
    }


    public function update(BillerRequest $request, $id) {
        $this->middleware('permission:edit-biller');

        $biller = $this->repo->findOrFail($id);
        $biller = $this->repo->update($biller, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $biller->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('biller.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-biller');

        $biller = $this->repo->findOrFail($id);
        $this->activity->record([
            'module' => $this->module,
            'module_id' => $biller->id,
            'activity' => 'deleted'
        ]);
        $this->repo->delete($biller);
        return $this->success(['message' => trans('biller.deleted')]);
    }

}
