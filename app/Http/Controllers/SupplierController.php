<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SupplierRequest;
use App\Repositories\CompanyRepository;
use App\Repositories\ActivityLogRepository;
use App\Company;

class SupplierController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'supplier';

    public function __construct(Request $request, CompanyRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
    }

    public function index()
    {
        $this->middleware('permission:list-supplier');
        $query = Company::search()
                ->onlySuppliers()
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

    public function store(SupplierRequest $request)
    {
        $this->middleware('permission:create-supplier');

        $supplier = $this->repo->create($this->request, 'supplier');

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $supplier->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('supplier.added')]);
    }


    public function update(SupplierRequest $request, $id) {
        $this->middleware('permission:edit-supplier');

        $supplier = $this->repo->findOrFail($id);
        $supplier = $this->repo->update($supplier, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $supplier->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('supplier.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-supplier');

        $supplier = $this->repo->findOrFail($id);
        $this->activity->record([
            'module' => $this->module,
            'module_id' => $supplier->id,
            'activity' => 'deleted'
        ]);
        $this->repo->delete($supplier);
        return $this->success(['message' => trans('supplier.deleted')]);
    }

}
