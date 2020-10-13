<?php
namespace App\Repositories;

use Illuminate\Validation\ValidationException;
use App\Repositories\ConfigurationRepository;

class Site
{
    protected $config;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(ConfigurationRepository $config)
    {
        $this->config = $config;
    }

    public function formatDecimal($number, $decimals = null)
    {
        if (!is_numeric($number)) {
            return null;
        }
        if (!$decimals && $decimals !== 0) {
            $decimals = 2;
        }
        return number_format($number, $decimals, '.', '');
    }
    



    public function calculateDiscount($discount = NULL, $amount) {
        if ($discount) {
            $dpos = strpos($discount, '%');
            if ($dpos !== false) {
                $pds = explode("%", $discount);
                return $this->formatDecimal(((($this->formatDecimal($amount)) * (Float) ($pds[0])) / 100), 4);
            } else {
                return $this->formatDecimal($discount, 4);
            }
        }
        return 0;
    }


    public function calculateOrderTax($order_tax = null, $amount)
    {
        // if ($this->Settings->tax2 != 0 && $order_tax_id) {
            if ($order_tax) {
                if ($order_tax['type'] == 1) {
                    return $this->formatDecimal((($amount * $order_tax['rate']) / 100), 4);
                } else {
                    return $this->formatDecimal($order_tax['rate'], 4);
                }
            }
        // }
        return 0;
    }

    function calculateTax($tax_details, $value = NULL) {
        $tax_amount = 0; $tax = 0;
        if ($tax_details && $tax_details['type'] == 1 && $tax_details['rate'] != 0) {
            $tax_amount = $this->formatDecimal((($value) * $tax_details['rate']) / (100 + $tax_details['rate']), 4);
            $tax = $tax_details['name'];
        } elseif ($tax_details && $tax_details['type'] == 2) {
            $tax_amount = $this->formatDecimal($tax_details['rate']);
            $tax = $tax_details['name'];
        }
        return array('id' => $tax_details['id'], 'tax' => $tax, 'amount' => $tax_amount);
    }



    public function syncQuantity($repair_id = NULL, $sale_id = null) {
        if ($repair_id) {
            $repair = \App\Repair::find($repair_id);
            $items = \App\SaleItem::where('repair_id', $repair->id)->get();

            foreach ($items as $item) {
                $product = \App\Product::find($item->product_id);
                if (!$product->service) {
                    $this->syncProductQty($item->product_id);
                }
            }
        }
        elseif ($sale_id) {
            $sale = \App\Sale::find($sale_id);
            $items = \App\SaleItem::where('sale_id', $sale->id)->get();

            foreach ($items as $item) {
                $product = \App\Product::find($item->product_id);
                if (!$product->service) {
                    $this->syncProductQty($item->product_id);
                }
            }
        }
    }

    public function getBalanceQuantity($product_id) {
        $result = \App\Costing::selectRaw('SUM(quantity) as stock')
        ->where('product_id', $product_id)->first();

        if ($result) {
            return (int)$result->stock ?? 0;
        }
        return 0;
    }

    public function syncProductQty($product_id) {
        $balance_qty = $this->getBalanceQuantity($product_id);
        \App\Product::where('id', $product_id)->update(['quantity'=>$balance_qty]);
        return TRUE;
    }

    public function getReference($field)
    {
        $ref = config('config.ref_'.$field);
        if ($ref) {
            $prefix = config('config.ref_prefix_'.$field);

            $ref_no = rtrim($prefix, '/') . '/';
            
            if (config('config.reference_format') == 1) {
                $ref_no .= date('Y') . '/' . sprintf('%04s', $ref);
            } elseif (config('config.reference_format') == 2) {
                $ref_no .= date('Y') . '/' . date('m') . '/' . sprintf('%04s', $ref);
            } elseif (config('config.reference_format') == 3) {
                $ref_no .= sprintf('%04s', $ref);
            } else {
                $ref_no .= $this->getRandomReference();
            }

            return $ref_no;
        }
        return false;
    }
    
    public function getRandomReference($len = 12)
    {
        $result = '';
        for ($i = 0; $i < $len; $i++) {
            $result .= mt_rand(0, 9);
        }

        if ($this->getSaleByReference($result)) {
            $this->getRandomReference();
        }

        return $result;
    }

    public function getSaleByReference($ref)
    {
        $sale = \App\Sale::like('reference_no', $ref)->first();
        if ($sale) {
            return $sale;
        }
        return false;
    }

    public function updateReference($field)
    {
        $this->config->set('ref_'.$field, (int) config('config.ref_'.$field) + 1, 0);
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


     public function checkQty($items, $id = null) {
        foreach ($items as $item) {
            if($item['service'] == 1){
                // do nothing
            }else{
                $qty_to_add = 0;
                if ($id) {
                    $sale_item = \App\SaleItem::where('sale_id', $id)->where('product_id', $item['id'])->first();
                    if ($sale_item) {
                        $qty_to_add = $sale_item->quantity;
                    }
                }
                $product = \App\Product::where('id', $item['id'])
                    ->first();

                if ($product) {
                    if (($product->quantity + $qty_to_add) >= (int)$item['qty']) {
                        continue;
                    }
                    return [
                        'success'=>false, 
                        'product_name'=>__('repair.product_x_not_in_stock', ['product'=>$item['name'], 'quantity'=>$product->quantity + $qty_to_add])
                    ];

                }
            }
        }
        return ['success'=>true];
    }


    public function registerData($user_id = null, $register_id = null)
    {
        if (!$user_id) {
            $user_id = \Auth::user()->id;
        }
        
        $register = \App\PosRegister::where('user_id', $user_id)->where('status', 'open');
        if ($register_id) {
            $register->where('id', $register_id);
        }

        $register = $register->first();
        if ($register) {
            return $register;
        }
        throw ValidationException::withMessages(['message'=>trans('pos_register.register_not_open')]);
        return false;
    }

}