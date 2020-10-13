<?php
namespace App\Repositories;

use App\Store;
use Illuminate\Validation\ValidationException;

class StoreRepository
{
    protected $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }


    public function create($request, $register = 0)
    {

        $params = $request->all();
        $params['created_by'] = \Auth::user()->id;
        $store = $this->store->forceCreate($params);
        return $store;
    }


    public function update(Store $store, $params)
    {
        $params = $params->all();
        $store->forceFill($params, 'update')->save();
        return $store;
    }

       
    public function findOrFail($id)
    {
        $store = $this->store->find($id);

        if (! $store) {
            throw ValidationException::withMessages(['message' => trans('store.could_not_find')]);
        }

        return $store;
    }

    public function delete(Store $store)
    {
        return $store->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->store->whereIn('id', $ids)->delete();
    }
}
