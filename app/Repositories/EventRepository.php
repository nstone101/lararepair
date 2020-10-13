<?php
namespace App\Repositories;

use App\Event;
use Illuminate\Validation\ValidationException;

class EventRepository
{
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

  
    public function create($request, $register = 0)
    {

        $params = $request->all();
        $event = $this->event->forceCreate($params);
        return $event;
    }


    public function update(Event $event, $params)
    {
        $params = $params->all();
        $event->forceFill($params, 'update')->save();
        return $event;
    }

       
    public function findOrFail($id)
    {
        $event = $this->event->find($id);

        if (! $event) {
            throw ValidationException::withMessages(['message' => trans('event.could_not_find')]);
        }

        return $event;
    }

    public function delete(Event $event)
    {
        return $event->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->event->whereIn('id', $ids)->delete();
    }
}
