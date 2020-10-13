<?php
namespace App\Repositories;

use App\Sale;
use Illuminate\Validation\ValidationException;

class SaleRepository
{
    protected $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }
       
    public function findOrFail($id)
    {
        $sale = $this->sale->find($id);

        if (! $sale) {
            throw ValidationException::withMessages(['message' => trans('Sale.could_not_find')]);
        }

        return $sale;
    }

    public function delete(Sale $sale)
    {
        \App\SaleItem::where('sale_id', $sale->id)->delete();
        \App\Payment::where('sale_id', $sale->id)->delete();
        
        return $sale->delete();
    }
}
