<?php
namespace App\Repositories;

use App\Tax;
use Illuminate\Validation\ValidationException;

class TaxRepository
{
    protected $tax;

    public function __construct(Tax $tax)
    {
        $this->tax = $tax;
    }

    public function listTaxRates()
    {
        return $this->tax->select('name', 'id', 'rate', 'type')->get()->toArray();
    }


    public function create($request, $register = 0)
    {

        $params = $request->all();
        $params['created_by'] = \Auth::user()->id;
        $tax = $this->tax->forceCreate($params);
        return $tax;
    }


    public function update(Tax $tax, $params)
    {
        $params = $params->all();
        $tax->forceFill($params, 'update')->save();
        return $tax;
    }

       
    public function findOrFail($id)
    {
        $tax = $this->tax->find($id);

        if (! $tax) {
            throw ValidationException::withMessages(['message' => trans('tax.could_not_find')]);
        }

        return $tax;
    }

    public function delete(Tax $tax)
    {
        return $tax->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->tax->whereIn('id', $ids)->delete();
    }
}
