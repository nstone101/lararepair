<?php
namespace App\Repositories;

use App\Sale;
use App\Repositories\Site;
use Illuminate\Validation\ValidationException;
use App\Helpers\StripeHelper;

class PosRepository
{
    protected $sale;
    protected $site;

    public function __construct(Sale $sale, Site $site)
    {
        $this->sale = $sale;
        $this->site = $site;
    }



    public function create($request, $register = 0)
    {

        $params = $request->all();
        $suspend_note     = @$params['suspend_note'];
        $did     =  @$params['did'];
        $suspend = $suspend_note && $suspend_note !== '' ? true : false;


        if (!$suspend && !empty($request->input('items'))) {
            $items = $request->input('items');

            if (!config('config.enable_overselling')) {
                $data = $this->site->checkQty($items);
                if (!$data['success']) {
                    return $data;
                }
            }
        }


        $customer_id = (int)$params['customer'] > 0 ? $params['customer'] : null;
        $customer_details = \App\Company::find($params['customer']);
        if ($customer_details) {
            $customer = $customer_details->company != '-'  ? $customer_details->company . ' ( ' .$customer_details->first_name . ' ' . trim($customer_details->last_name).')' : $customer_details->first_name . ' ' . $customer_details->last_name;
        }
        if (!isset($customer_details->id)) {
            $customer = trans('pos.walk_in');
        }
        $note = '';
        $staff_note = '';
        $reference = $this->site->getReference('possale');

        $total = 0;
        $product_tax = 0;
        $product_discount = 0;
        $products = [];

        $item_count = 0;
        foreach ($params['items'] as $key => $item) {
            $item_id = $item['id'];
            $item_type = $item['service'] ? 'service' : 'standard';
            $item_code = $item['code'];
            $item_name = $item['name'];

            $item_comment = isset($item['description']) ? $item['description'] : '';

            $unit_price = ($item['price_gross']);
            $item_cost = ($item['purchase_price_gross']);

            $item_tax_rate = isset($item['tax_id']) ? $item['tax_id'] : NULL;
            $item_discount = isset($item['discount']) ? $item['discount'] : NULL;
            $item_unit = isset($item['unit_id']) && $item['unit_id'] > 0 ? $item['unit_id'] : null;
            $item_quantity = $item['qty'];

            if (isset($item_code) && isset($unit_price) && isset($item_quantity)) {

                $pr_discount = $this->site->calculateDiscount($item_discount, $unit_price);

                $unit_price = ($unit_price - $pr_discount);

                $pr_item_discount = ($pr_discount * $item_quantity);
                $product_discount += $pr_item_discount;
                $item_tax = $item['price_gross'] - $item['price_net'];
                $pr_item_tax = $item_tax * $item['qty'];
                $tax = '';


                if (($item['selling_tax'])) {
                    $tax_details = $item['selling_tax'];
                    $ctax = $this->site->calculateTax($tax_details, $unit_price);
                    $item_tax = $ctax['amount'];
                    $tax = $ctax['tax'];
                    $item_net_price = $unit_price - $item_tax;
                    $pr_item_tax = (($item_tax * $item_quantity));
                }


              
                $item_net_price = $unit_price - $item_tax;

                $product_tax += $pr_item_tax;
                $subtotal = (($item_net_price * $item_quantity) + $pr_item_tax);

                $product = array(
                    'product_id'      => $item_id,
                    'product_code'    => $item_code,
                    'product_name'    => $item_name,
                    'product_type'    => $item_type,
                    'purchase_price_gross'       => $item_cost,
                    'net_unit_price'  => $item_net_price,
                    'unit_price'      => ($item_net_price + $item_tax),
                    'quantity'        => $item_quantity,
                    'item_tax'        => $tax,
                    'taxrate_id'        => $item_tax_rate > 0 ? $item_tax_rate : null,
                    'tax'             => $pr_item_tax ?? 0,
                    'discount'        => $item_discount,
                    'item_discount'   => $pr_item_discount,
                    'subtotal'        => ($subtotal),
                    'comment'         => $item_comment,
                    'product_unit'    => $item_unit,
                );
                $products[] = $product;
                $total += (($item_net_price * $item_quantity));
            }

            $item_count += $item_quantity;

        }



        // dd($products);

        $order_discount = $this->site->calculateDiscount($params['order_discount'], ($total + $product_tax));
        $order_tax = $this->site->calculateOrderTax($params['order_tax'], ($total + $product_tax - $order_discount));
        $total_tax = $this->site->formatDecimal(($product_tax + $order_tax), 4);
        $grand_total = $this->site->formatDecimal(($total + $total_tax - $order_discount), 4);
        $total_discount = $this->site->formatDecimal(($order_discount + $product_discount), 4);



        $biller_id = \Auth::user()->id;
        $biller = \Auth::user();


        $sale_status      = 'completed';

        $payment_term     = 0;
        $due_date         = date('Y-m-d', strtotime('+' . $payment_term . ' days'));
        $date             = date('Y-m-d H:i:s');
        

        $data = array(
            'created_at'  => $date,
            'reference_no'      => $reference,
            'customer_id'       => $customer_id,
            'customer'          => $customer,
            'biller_id'         => $biller_id,
            'biller'            => $biller ? $biller->first_name . ' ' . $biller->last_name : '',
            'note'              => $note,
            'staff_note'        => $staff_note,
            'total'             => $total,
            'product_discount'  => $product_discount,
            'order_discount_id' => $params['order_discount'],
            'order_discount'    => $order_discount,
            'total_discount'    => $total_discount,
            'product_tax'       => $product_tax,
            'order_tax_id'      => $params['order_tax'] ? $params['order_tax']['id'] : null,
            'order_tax'         => $order_tax,
            'total_tax'         => $total_tax,
            'shipping'          => 0,
            'grand_total'       => $grand_total,
            'total_items'       => $item_count,
            'sale_status'       => $sale_status,
            'payment_status'    => $grand_total > 0 ? 'due' : 'paid',
            'payment_term'      => $payment_term,
            'suspend_note'      => $suspend_note,
            'pos'               => 1,
            'paid'              => 0,
            'created_by'        => \Auth::user()->id,
            'hash'              => hash('sha256', microtime() . mt_rand()),
        );


        
        if (!$suspend) {
            $payments = $params['payments'];
            $paid = 0;
            foreach ($payments as $key => $payment) {
                if (isset($payment['amount']) && !empty($payment['amount']) && isset($payment['paid_by']) && !empty($payment['paid_by'])) {
                    $amount = $this->site->formatDecimal($payment['balance_amount'] > 0 ? $payment['amount'] - $payment['balance_amount'] : $payment['amount']);

                    if ($payment['paid_by'] == 'voucher') {
                        $voucher            = \App\Voucher::where('card_no', $payment['paying_voucher_no'])->first();
                        $amount_paying =  $payment['amount'] >= $voucher->balance ? $voucher->balance :  $payment['amount'];
                        $voucher_balance    = $voucher->balance - $amount_paying;

                        $payments_data[] = array(
                            'date'         => $date,
                            // 'reference_no' => $this->site->getReference('pay'),
                            'amount'       => $amount,
                            'paid_by'      => $payment['paid_by'],
                            'cheque_no'    => $payment['cheque_no'],
                            'cc_no'        => $payment['paying_voucher_no'],
                            'cc_holder'    => $payment['cc_holder'],
                            'cc_month'     => $payment['cc_month'],
                            'cc_year'      => $payment['cc_year'],
                            'cc_type'      => $payment['cc_type'],
                            'cc_cvv2'      => $payment['cc_cvv2'],
                            'created_by'   => \Auth::user()->id,
                            'type'         => 'received',
                            'note'         => $payment['payment_note'],
                            'pos_paid'     => $payment['amount'],
                            'pos_balance'  => $payment['balance_amount'],
                            'voucher_balance'  => $voucher_balance,
                        );
                    } else {
                        $payments_data[] = array(
                            'date'         => $date,
                            // 'reference_no' => $this->site->getReference('pay'),
                            'amount'       => $amount,
                            'paid_by'      => $payment['paid_by'],
                            'cheque_no'    => $payment['cheque_no'],
                            'cc_no'        => $payment['cc_no'],
                            'cc_holder'    => $payment['cc_holder'],
                            'cc_month'     => $payment['cc_month'],
                            'cc_year'      => $payment['cc_year'],
                            'cc_type'      => $payment['cc_type'],
                            'cc_cvv2'      => $payment['cc_cvv2'],
                            'created_by'   => \Auth::user()->id,
                            'type'         => 'received',
                            'note'         => $payment['payment_note'],
                            'pos_paid'     => $payment['amount'],
                            'pos_balance'  => $payment['balance_amount'],
                        );
                    }
                }
            }
        }

        if (!isset($payments_data) || empty($payments_data)) {
            $payments_data = array();
        }

        // dd($payments_data, $data, $products);

        
        if (!empty($products) && !empty($data)) {

            if ($suspend) {
                if ($sus = $this->suspendSale($data, $products, $did)) {
                    return $sus;
                }
            } elseif ($sale = $this->addSale($data, $products, $payments_data, $did)) {
                return $sale;
            }
            
        } 


        // $params['created_by'] = \Auth::user()->id;
        // $sale = $this->sale->forceCreate($params);
        // return $sale;
    }




    public function suspendSale($data = [], $items = [], $did = null)
    {
        $sData = [
            'count'             => $data['total_items'],
            'biller_id'         => $data['biller_id'],
            'customer_id'       => $data['customer_id'],
            'customer'          => $data['customer'],
            'created_at'              => $data['created_at'],
            'suspend_note'      => $data['suspend_note'],
            'total'             => $data['grand_total'],
            'order_tax_id'      => $data['order_tax_id'],
            'order_discount_id' => $data['order_discount_id'],
            'created_by'        => \Auth::user()->id,
        ];

        if ($did) {
            $bill = \App\SuspendedBill::where('id', $did)->first();
            $bill->update($sData);

            \App\SuspendedItem::where('suspend_id', $did)->delete();

            $addOn = ['suspend_id' => $did];
            end($addOn);
            foreach ($items as &$var) {
                $var = array_merge($addOn, $var);
            }

            foreach($items as $item){
                \App\SuspendedItem::create($item);
            }
            return true;
        } else {
            $bill = \App\SuspendedBill::create($sData);


            $suspend_id = $bill->id;
            $addOn      = ['suspend_id' => $suspend_id];
            end($addOn);
            foreach ($items as &$var) {
                $var = array_merge($addOn, $var);
            }
            foreach($items as $item){
                \App\SuspendedItem::create($item);
            }
            return true;
        }
        return false;
    }


    public function addSale($data = [], $items = [], $payments = [], $sid = null)
    {
        if ($sale = \App\Sale::create($data)) {
            $sale_id = $sale->id;
            foreach ($items as $item) {
                $item['sale_id'] = $sale_id;
                $sale_item = \App\SaleItem::create($item);
                $sale_item_id = $sale_item->id;

                if ($data['sale_status'] == 'completed' && $product = \App\Product::find($item['product_id'])) {
                    if (!$product->service) {
                        \App\Costing::create([
                            'product_id' => $item['product_id'],
                            'sale_id' => $sale_id,
                            'sale_item_id' => $sale_item_id,
                            'code' => $item['product_code'],
                            'name' => $item['product_name'],
                            'cost' => $item['purchase_price_gross'],
                            'quantity' => 0-$item['quantity'],
                        ]);
                    }
                }
            }
            $this->site->syncQuantity(null,$sale->id);
            if ($sid) {
                $this->deleteBill($sid);
            }
            $this->site->updateReference('possale');
        }


        $msg = [];
        if (!empty($payments)) {
            $paid = 0;
            foreach ($payments as $payment) {
                if (!empty($payment) && isset($payment['amount']) && $payment['amount'] != 0) {
                    
                    $payment['sale_id']      = $sale_id;
                    $payment['reference_no'] = $this->site->getReference('pay');
                    
                    if ($payment['paid_by'] == 'stripe') {
                        $card_info = ['number' => $payment['cc_no'], 'exp_month' => $payment['cc_month'], 'exp_year' => $payment['cc_year'], 'cvc' => $payment['cc_cvv2'], 'type' => $payment['cc_type']];
                        $result    = $this->stripe($payment['amount'], $card_info);
                        if (!isset($result['error'])) {
                            $payment['transaction_id'] = $result['transaction_id'];
                            $payment['date']           = date('Y-m-d H:i:s', strtotime($result['created_at']));
                            $payment['amount']         = $result['amount'];
                            $payment['currency']       = $result['currency'];
                            unset($payment['cc_cvv2']);
                            \App\Payment::create($payment);
                            $paid += $payment['amount'];
                        } else {
                            $msg[] = lang('payment_failed');
                            $msg[] = '<p class="text-danger">' . $result['code'] . ': ' . $result['message'] . '</p>';
                        }
                    }  else {
                         if ($payment['paid_by'] == 'voucher') {
                            $voucher = \App\Voucher::where('card_no', $payment['cc_no'])->first();
                            if($voucher){
                                $voucher->balance = $payment['voucher_balance'];
                                $voucher->save();
                            }
                            unset($payment['voucher_balance']);
                        } 

                        unset($payment['cc_cvv2']);
                        \App\Payment::create($payment);

                        $this->site->updateReference('pay');
                        $paid += $payment['amount'];
                    }

                }
            }
            $this->site->syncSalePayments($sale_id);
        }
        return ['id' => $sale_id, 'message' => $msg];
    }


    public function stripe($amount = 0, $card_info = [], $desc = '')
    {
        $stripe_payments = new StripeHelper("sk_test_48By8pGwGXWnaiGvDmaXpYEW");

        //$card_info = array( "number" => "4242424242424242", "exp_month" => 1, "exp_year" => 2016, "cvc" => "314" );
        //$amount = $amount ? $amount*100 : 3000;
        unset($card_info['type']);
        $amount = $amount * 100;
        if ($amount && !empty($card_info)) {
            $token_info = $stripe_payments->create_card_token($card_info);
            if (!isset($token_info['error'])) {
                $token = $token_info->id;
                $data  = $stripe_payments->insert($token, $desc, $amount, getDefaultCurrencyCode()->code);
                if (!isset($data['error'])) {
                    $result = ['transaction_id' => $data->id,
                        'created_at'            => date('Y-m-d', $data->created),
                        'amount'                => ($data->amount / 100),
                        'currency'              => strtoupper($data->currency),
                    ];
                    return $result;
                } else {
                    return $data;
                }
            } else {
                return $token_info;
            }
        }
        return false;
    }

    
}
