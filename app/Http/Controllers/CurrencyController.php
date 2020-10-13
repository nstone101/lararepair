<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CurrencyRequest;
use App\Repositories\CurrencyRepository;
use App\Repositories\ActivityLogRepository;
use App\Currency;

class CurrencyController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'currency';

    public function __construct(Request $request, CurrencyRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

    }
  

    public function index()
    {
        $this->middleware('permission:list-currency');
        $query = Currency::search()
                ->selectRaw('id, code, name, symbol, symbol_position');

        return dataTable()->query($query)
            ->setFilters(
                'currencys.name', 'currencys.code'
            )
            
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(CurrencyRequest $request)
    {
        $this->middleware('permission:create-currency');

        $currency = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $currency->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('currency.added')]);
    }


    public function update(CurrencyRequest $request, $id) {
        $this->middleware('permission:edit-currency');

        $currency = $this->repo->findOrFail($id);
        $currency = $this->repo->update($currency, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $currency->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('currency.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-currency');

        $currency = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $currency->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($currency);

        return $this->success(['message' => trans('currency.deleted')]);
    }

}
