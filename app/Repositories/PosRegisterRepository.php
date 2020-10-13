<?php
namespace App\Repositories;

use App\PosRegister;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Repositories\Site;

class PosRegisterRepository
{
    protected $register;
    protected $site;

    public function __construct(PosRegister $register, Site $site)
    {
        $this->register = $register;
        $this->site = $site;
    }

  
    public function create($request, $register = 0)
    {
        $data = [
            'date'         => date('Y-m-d H:i:s'),
            'opened_at'         => date('Y-m-d H:i:s'),
            'cash_in_hand' => $request->get('cash_in_hand'),
            'user_id'      => \Auth::user()->id,
            'status'       => 'open',
        ];
        $register = $this->register->create($data);


        return $register;
    }


    public function update(PosRegister $register, $params)
    {
        $params = $params->all();
        $register->forceFill($params, 'update')->save();
        return $register;
    }

       
    public function findOrFail($id)
    {
        $register = $this->register->find($id);

        if (! $register) {
            throw ValidationException::withMessages(['message' => trans('register.could_not_find')]);
        }

        return $register;
    }

    public function delete(PosRegister $register)
    {
        return $register->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->register->whereIn('id', $ids)->delete();
    }


    public function getRegisterCCSales($date, $user_id = null)
    {
        if (!$user_id) {
            $user_id = \Auth::user()->id;
        }

        $payment = \App\Payment::selectRaw('COUNT(payments.id) as total_cc_slips, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid')
            ->leftJoin('sales', 'sales.id', 'payments.sale_id')
            ->where('type', 'received')
            ->whereDate('payments.created_at', '>=', Carbon::parse($date))
            ->where('paid_by', 'CC')
            ->where('payments.created_by', $user_id)->first();
        

        if ($payment) {
            return $payment->toArray();
        }
        
        return false;
    }

    public function getRegisterCashSales($date, $user_id = null)
    {
        if (!$user_id) {
            $user_id = \Auth::user()->id;
        }

        $payment = \App\Payment::selectRaw('SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid')
            ->leftJoin('sales', 'sales.id', 'payments.sale_id')
            ->where('type', 'received')
            ->whereDate('payments.created_at', '>=', Carbon::parse($date))
            ->where('paid_by', 'cash')
            ->where('payments.created_by', $user_id)->first();
        

        if ($payment) {
            return $payment->toArray();
        }
        
        return false;
    }

    public function getRegisterChSales($date, $user_id = null)
    {
        if (!$user_id) {
            $user_id = \Auth::user()->id;
        }

        $payment = \App\Payment::selectRaw('COUNT(payments.id) as total_cheques, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid')
            ->leftJoin('sales', 'sales.id', 'payments.sale_id')
            ->where('type', 'received')
            ->whereDate('payments.created_at', '>=', Carbon::parse($date))
            ->where('paid_by', 'cheque')
            ->where('payments.created_by', $user_id)->first();
        

        if ($payment) {
            return $payment->toArray();
        }
        
        return false;
    }

    public function getRegisterGCSales($date, $user_id = null)
    {
        if (!$user_id) {
            $user_id = \Auth::user()->id;
        }

        $payment = \App\Payment::selectRaw('COUNT(payments.id) as total_vouchers, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid')
            ->leftJoin('sales', 'sales.id', 'payments.sale_id')
            ->where('type', 'received')
            ->whereDate('payments.created_at', '>=', Carbon::parse($date))
            ->where('paid_by', 'voucher')
            ->where('payments.created_by', $user_id)->first();
        

        if ($payment) {
            return $payment->toArray();
        }
        
        return false;
    }


    public function getRegisterStripeSales($date, $user_id = null)
    {
        if (!$user_id) {
            $user_id = \Auth::user()->id;
        }

        $payment = \App\Payment::selectRaw('COUNT(payments.id) as count, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid')
            ->leftJoin('sales', 'sales.id', 'payments.sale_id')
            ->where('type', 'received')
            ->whereDate('payments.created_at', '>=', Carbon::parse($date))
            ->where('paid_by', 'stripe')
            ->where('payments.created_by', $user_id)->first();
        

        if ($payment) {
            return $payment->toArray();
        }
        
        return false;
    }

    public function getRegisterSales($date, $user_id = null)
    {
        if (!$user_id) {
            $user_id = \Auth::user()->id;
        }

        $payment = \App\Payment::selectRaw('COUNT(payments.id) as count, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid')
            ->leftJoin('sales', 'sales.id', 'payments.sale_id')
            ->where('type', 'received')
            ->whereDate('payments.created_at', '>=', Carbon::parse($date))
            ->where('payments.created_by', $user_id)->first();
        

        if ($payment) {
            return $payment->toArray();
        }
        
        return false;
    }

    public function getRegisterPPPSales($date, $user_id = null)
    {
        if (!$user_id) {
            $user_id = \Auth::user()->id;
        }

        $payment = \App\Payment::selectRaw('COUNT(payments.id) as count, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid')
            ->leftJoin('sales', 'sales.id', 'payments.sale_id')
            ->where('type', 'received')
            ->whereDate('payments.created_at', '>=', Carbon::parse($date))
            ->where('paid_by', 'ppp')
            ->where('payments.created_by', $user_id)->first();
        

        if ($payment) {
            return $payment->toArray();
        }
        
        return false;
    }

    public function getRegisterAuthorizeSales($date, $user_id = null)
    {
        if (!$user_id) {
            $user_id = \Auth::user()->id;
        }

        $payment = \App\Payment::selectRaw('COUNT(payments.id) as count, SUM( COALESCE( grand_total, 0 ) ) AS total, SUM( COALESCE( amount, 0 ) ) AS paid')
            ->leftJoin('sales', 'sales.id', 'payments.sale_id')
            ->where('type', 'received')
            ->whereDate('payments.created_at', '>=', Carbon::parse($date))
            ->where('paid_by', 'authorize')
            ->where('payments.created_by', $user_id)->first();
        

        if ($payment) {
            return $payment->toArray();
        }
        
        return false;
    }


    
    public function closeCurrentRegister($params){

        $register = $this->site->registerData();

       


        
        $register = $this->site->registerData();
        if ($register) {
            $register_open_time     = $register->register_open_time;
            $rid     = $register->id;
            $user_id = \Auth::user()->id;
    
            $data = [
                'closed_at'                => date('Y-m-d H:i:s'),
                'total_cash'               => (float) $params->get('total_cash'),
                'total_cheques'            => (float) $params->get('total_cheques'),
                'total_cc_slips'           => (float) $params->get('total_cc_slips'),
                'total_cash_submitted'     => (float) $params->get('total_cash_submitted'),
                'total_cheques_submitted'  => (float) $params->get('total_cheques_submitted'),
                'total_cc_slips_submitted' => (float) $params->get('total_cc_slips_submitted'),
                'note'                     => $params->get('note'),
                'status'                   => 'close',
                'closed_by'                => $user_id
            ];
            $register->update($data);
            $register->save();
            return true;
        }else{
            return false;
        }
    }

    public function closeRegister($id, $params){

        $register = $this->site->registerData(null, $id);
        if ($register) {
            $register_open_time     = $register->register_open_time;
            $rid     = $register->id;
            $user_id = \Auth::user()->id;
    
            $data = [
                'closed_at'                => date('Y-m-d H:i:s'),
                'total_cash'               => (float) $params->get('total_cash'),
                'total_cheques'            => (float) $params->get('total_cheques'),
                'total_cc_slips'           => (float) $params->get('total_cc_slips'),
                'total_cash_submitted'     => (float) $params->get('total_cash_submitted'),
                'total_cheques_submitted'  => (float) $params->get('total_cheques_submitted'),
                'total_cc_slips_submitted' => (float) $params->get('total_cc_slips_submitted'),
                'note'                     => $params->get('note'),
                'status'                   => 'close',
                'closed_by'                => $user_id
            ];
            $register->update($data);
            $register->save();
            return true;
        }else{
            return false;
        }

    }
    

    
}
