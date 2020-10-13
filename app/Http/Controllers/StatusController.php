<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StatusRequest;
use App\Repositories\StatusRepository;
use App\Repositories\ActivityLogRepository;
use App\Status;
use Spatie\Permission\Models\Permission;
use App\Repositories\PermissionRepository;
class StatusController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'status';

    public function __construct(Request $request, StatusRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
    }
  

    public function updatePosition(Request $request) {

        $validatedData = $request->validate([
            'positions' => 'required',
        ]);

        foreach ($validatedData['positions'] as $key => $status_id) {
            \App\Status::where('id', $status_id)->update(['position'=>$key+1]);
        }
        return $this->success(['message' => trans('status.positions_updated')]);
    }

    public function preRequisite() {
        $statuses = \App\Status::orderByRaw('position ASC')->get();
        return $this->success(compact('statuses'));
    }

    public function index()
    {
        $this->middleware('permission:list-status');
        $query = Status::search()
                ->selectRaw('id, label');

        return dataTable()->query($query)
            ->setFilters(
                'statuses.label'
            )
            
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(StatusRequest $request)
    {
        $this->middleware('permission:create-status');

        $status = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $status->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('status.added')]);
    }


    public function update(StatusRequest $request, $id) {
        $this->middleware('permission:edit-status');

        $status = $this->repo->findOrFail($id);
        $status = $this->repo->update($status, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $status->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('status.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-status');

        $status = $this->repo->findOrFail($id);

        $this->authorize('delete-status', $status);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $status->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($status);

        return $this->success(['message' => trans('status.deleted')]);
    }

}
