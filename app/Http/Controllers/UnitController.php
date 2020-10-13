<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UnitRequest;
use App\Repositories\UnitRepository;
use App\Repositories\ActivityLogRepository;
use App\Unit;

class UnitController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'unit';

    public function __construct(Request $request, UnitRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

    }
  

    public function index()
    {
        $this->middleware('permission:list-unit');
        $query = Unit::search()
                ->selectRaw('id, code, name, allow_decimal');

        return dataTable()->query($query)
            ->setFilters(
                'units.name', 'units.code'
            )
            ->configColumn('allow_decimal', function ($value, $row) {
                if ($value) {
                    return 'Yes';
                }else{
                    return 'No';
                }
            })
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(UnitRequest $request)
    {
        $this->middleware('permission:create-unit');

        $unit = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $unit->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('unit.added')]);
    }


    public function update(UnitRequest $request, $id) {
        $this->middleware('permission:edit-unit');

        $unit = $this->repo->findOrFail($id);
        $unit = $this->repo->update($unit, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $unit->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('unit.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-unit');

        $unit = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $unit->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($unit);

        return $this->success(['message' => trans('unit.deleted')]);
    }

}
