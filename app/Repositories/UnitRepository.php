<?php
namespace App\Repositories;

use App\Unit;
use Illuminate\Validation\ValidationException;

class UnitRepository
{
    protected $unit;

    public function __construct(Unit $unit)
    {
        $this->unit = $unit;
    }


    public function listUnits()
    {
        return $this->unit->select('name', 'id', 'allow_decimal')->get()->toArray();
    }


    public function create($request, $register = 0)
    {

        $params = $request->all();
        $params['created_by'] = \Auth::user()->id;
        $unit = $this->unit->forceCreate($params);
        return $unit;
    }


    public function update(Unit $unit, $params)
    {
        $params = $params->all();
        $unit->forceFill($params, 'update')->save();
        return $unit;
    }

       
    public function findOrFail($id)
    {
        $unit = $this->unit->find($id);

        if (! $unit) {
            throw ValidationException::withMessages(['message' => trans('unit.could_not_find')]);
        }

        return $unit;
    }

    public function delete(Unit $unit)
    {
        return $unit->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->unit->whereIn('id', $ids)->delete();
    }
}
