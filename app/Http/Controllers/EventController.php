<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use App\Repositories\EventRepository;
use App\Repositories\ActivityLogRepository;
use App\Event;

class EventController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'event';

    public function __construct(Request $request, EventRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
    }
  
    public function preRequisite()
    {
        $events = Event::get();
        return $this->success(compact('events'));
    }

    public function index()
    {
        $this->middleware('permission:list-event');
        $query = Event::search()
                ->selectRaw('id, code, name, rate, type');

        return dataTable()->query($query)
            ->setFilters(
                'event_rates.name', 'event_rates.code'
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

    public function store(EventRequest $request)
    {
        $this->middleware('permission:create-event');

        $event = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $event->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('event.added')]);
    }


    public function update(EventRequest $request, $id) {
        $this->middleware('permission:edit-event');

        $event = $this->repo->findOrFail($id);
        $event = $this->repo->update($event, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $event->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('event.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-event');

        
        $event = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $event->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($event);

        return $this->success(['message' => trans('event.deleted')]);
    }

}
