<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomFieldRequest;
use App\Repositories\CustomFieldRepository;
use App\Repositories\ActivityLogRepository;
use App\CustomField;

class CustomFieldController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'custom_field';

    public function __construct(Request $request, CustomFieldRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
    }

    public function index()
    {
        $this->middleware('permission:list-custom-field');
        $query = CustomField::search()
                ->selectRaw('id,model_type, title, type, required');
        return dataTable()->query($query)
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(CustomFieldRequest $request)
    {
        $this->middleware('permission:create-custom-field');

        $custom_field = $this->repo->create($this->request, 'custom_field');

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $custom_field->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('custom_field.added')]);
    }


    public function update(CustomFieldRequest $request, $id) {
        $this->middleware('permission:edit-custom-field');

        $custom_field = $this->repo->findOrFail($id);
        $custom_field = $this->repo->update($custom_field, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $custom_field->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('custom_field.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-custom-field');

        $custom_field = $this->repo->findOrFail($id);
        $this->activity->record([
            'module' => $this->module,
            'module_id' => $custom_field->id,
            'activity' => 'deleted'
        ]);
        $this->repo->delete($custom_field);
        return $this->success(['message' => trans('custom_field.deleted')]);
    }

}
