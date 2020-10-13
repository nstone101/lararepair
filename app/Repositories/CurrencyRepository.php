<?php
namespace App\Repositories;

use App\Currency;
use Illuminate\Validation\ValidationException;

class CurrencyRepository
{
    protected $currency;

    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }


    public function list()
    {
        return $this->currency->all()->pluck('code', 'id')->all();
    }
    public function create($request, $register = 0)
    {

        $params = $request->all();
        $currency = $this->currency->forceCreate($params);
        return $currency;
    }


    public function update(Currency $currency, $params)
    {
        $params = $params->all();
        $currency->forceFill($params, 'update')->save();
        return $currency;
    }

       
    public function findOrFail($id)
    {
        $currency = $this->currency->find($id);

        if (! $currency) {
            throw ValidationException::withMessages(['message' => trans('currency.could_not_find')]);
        }

        return $currency;
    }

    public function delete(Currency $currency)
    {
        return $currency->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->currency->whereIn('id', $ids)->delete();
    }
}
