<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaxRequest;
use App\Repositories\TaxRepository;
use App\Repositories\ActivityLogRepository;
use App\Tax;

class TaxController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'tax';

    public function __construct(Request $request, TaxRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

    }
  

    public function index()
    {
        $this->middleware('permission:list-tax');
        $query = Tax::search()
                ->selectRaw('id, code, name, rate, type');

        return dataTable()->query($query)
            ->setFilters(
                'tax_rates.name', 'tax_rates.code'
            )
            ->configColumn('type', function ($value, $row) {
                if ($value == 1) {
                    return '%';
                }else{
                    return 'Fixed';
                }
            })
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(TaxRequest $request)
    {
        $this->middleware('permission:create-tax');

        $tax = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $tax->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('tax.added')]);
    }


    public function update(TaxRequest $request, $id) {
        $this->middleware('permission:edit-tax');

        $tax = $this->repo->findOrFail($id);
        $tax = $this->repo->update($tax, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $tax->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('tax.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-tax');

        $tax = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $tax->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($tax);

        return $this->success(['message' => trans('tax.deleted')]);
    }

}
