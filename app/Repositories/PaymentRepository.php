<?php
namespace App\Repositories;

use App\Payment;
use Illuminate\Validation\ValidationException;
use App\Repositories\Site;

class PaymentRepository
{
    protected $payment;
    protected $site;

    public function __construct(Payment $payment, Site $site)
    {
        $this->payment = $payment;
        $this->site = $site;
    }


    public function syncRepairPayments($id) {
        $repair = \App\Repair::find($id);
        if ($payments = \App\Payment::where('repair_id', $id)->get()) {
            $paid = 0;
            $grand_total = $repair->grand_total;
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
            
            $payment_status = $paid == 0 ? 'pending' : $repair->payment_status;
            if ((float) $grand_total == (float) $paid) {
                $payment_status = 'paid';
            } elseif ($paid != 0) {
                $payment_status = 'partial';
            }

            $repair->paid = $paid;
            $repair->payment_status = $payment_status;
            $repair->save();
            return true;
        } else {
            $repair->paid = 0;
            $repair->payment_status = 'pending';
            $repair->save();
            return true;
        }
        return FALSE;
    }

    public function syncSalePayments($id) {
        $sale = \App\Sale::find($id);
        if ($payments = \App\Payment::where('sale_id', $id)->get()) {
            $paid = 0;
            $grand_total = $sale->grand_total;
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
            
            $payment_status = $paid == 0 ? 'pending' : $sale->payment_status;
            if ((float) $grand_total == (float) $paid) {
                $payment_status = 'paid';
            } elseif ($paid != 0) {
                $payment_status = 'partial';
            }

            $sale->paid = $paid;
            $sale->payment_status = $payment_status;
            $sale->save();
            return true;
        } else {
            $sale->paid = 0;
            $sale->payment_status = 'pending';
            $sale->save();
            return true;
        }
        return FALSE;
    }


    public function create($request, $register = 0)
    {
        $params = $request->all();
        if(isset($params['repair_id']) && !$params['repair_id']){
            unset($params['repair_id']);
        }
        if(isset($params['sale_id']) && !$params['sale_id']){
            unset($params['sale_id']);
        }


        $params['created_by'] = \Auth::user()->id;
        $payment = $this->payment->forceCreate($params);

        if (isset($params['repair_id'])) {
            $this->syncRepairPayments($params['repair_id']);
        }elseif (isset($params['sale_id'])) {
            $this->syncSalePayments($params['sale_id']);
        }
        return $payment;
    }


    public function update(Payment $payment, $params)
    {
        $params = $params->all();
        if(isset($params['repair_id']) && !$params['repair_id']){
            $params['repair_id'] = null;
        }
        if(isset($params['sale_id']) && !$params['sale_id']){
            $params['sale_id'] = null;

        }
        $payment->forceFill($params, 'update')->save();
        if ($params['repair_id']) {
            $this->syncRepairPayments($params['repair_id']);
        }elseif ($params['sale_id']) {
            $this->syncSalePayments($params['sale_id']);
        }
        return $payment;
    }


    public function preRequisite($sale_id, $repair_id) {
        $amount = 0;
        if($sale_id){
            $sale = \App\Sale::find($sale_id);
            $amount = $sale ? $sale->grand_total : 0;
        }elseif($repair_id){
            $repair = \App\Repair::find($repair_id);
            $amount = $repair ? $repair->grand_total : 0;
        }

        $code = $this->site->getReference('payment');
        return (compact('code', 'amount'));
    }
       
    public function findOrFail($id)
    {
        $payment = $this->payment->find($id);

        if (! $payment) {
            throw ValidationException::withMessages(['message' => trans('payment.could_not_find')]);
        }

        return $payment;
    }

    public function delete(Payment $payment)
    {
        $payment_ = $payment->delete();
        if ($payment->repair_id) {
            $this->syncRepairPayments($payment->repair_id);
        }
        if ($payment->sale_id) {
            $this->syncSalePayments($payment->sale_id);
        }
        return $payment_;
    }

    public function deleteMultiple($ids)
    {
        return $this->payment->whereIn('id', $ids)->delete();
    }
}
