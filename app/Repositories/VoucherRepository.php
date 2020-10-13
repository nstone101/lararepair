<?php
namespace App\Repositories;

use App\Voucher;
use Illuminate\Validation\ValidationException;

class VoucherRepository
{
    protected $voucher;

    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }

    public function preRequisite($id = null) {
        $customers = \App\Company::onlyCustomers()->get();
        return (compact('customers'));
    }

    public function create($request)
    {
        $data = [
            'date'         => date('Y-m-d H:i:s'),
            'card_no' => $request->get('card_no'),
            'customer_id' => $request->get('customer_id'),
            'balance' => $request->get('value'),
            'value' => $request->get('value'),
            'expiry' => $request->get('expiry') !== '' ? $request->get('expiry') : null,
            'created_by'      => \Auth::user()->id,
        ];

        $voucher = $this->voucher->create($data);
        return $voucher;
    }


    public function update(Voucher $voucher, $request)
    {

        $data = [
            'card_no' => $request->get('card_no'),
            'customer_id' => $request->get('customer_id'),
            'balance' => ($request->get('value') -  $voucher->value) + $voucher->balance,
            'value' => $request->get('value'),
            'expiry' => $request->get('expiry') !== '' ? $request->get('expiry') : null,
        ];

        $voucher->forceFill($data, 'update')->save();
        return $voucher;
    }

       
    public function findOrFail($id)
    {
        $voucher = $this->voucher->find($id);

        if (! $voucher) {
            throw ValidationException::withMessages(['message' => trans('voucher.could_not_find')]);
        }

        return $voucher;
    }

    public function delete(Voucher $voucher)
    {
        return $voucher->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->voucher->whereIn('id', $ids)->delete();
    }
}
