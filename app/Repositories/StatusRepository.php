<?php
namespace App\Repositories;

use App\Status;
use Illuminate\Validation\ValidationException;

class StatusRepository
{
    protected $status;

    public function __construct(Status $status)
    {
        $this->status = $status;
    }


    public function create($request) {
        $params = $request->all();
        $params['position'] = Status::max('id') ?? 1;
        $status = $this->status->forceCreate($params);
        return $status;
    }


    public function update(Status $status, $params)
    {
        $params = $params->all();
        $status->forceFill($params, 'update')->save();
        return $status;
    }

       
    public function findOrFail($id)
    {
        $status = $this->status->find($id);

        if (! $status) {
            throw ValidationException::withMessages(['message' => trans('status.could_not_find')]);
        }

        return $status;
    }

    public function delete(Status $status)
    {
        return $status->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->status->whereIn('id', $ids)->delete();
    }
}
