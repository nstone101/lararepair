<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PosRequest;
use App\Repositories\PosRepository;
use App\Repositories\ActivityLogRepository;
use App\Helpers\CustomHtmlable;
use App\Http\Requests\SaleEmailRequest;

class PosController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'pos';

    public function __construct(Request $request, PosRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
    }

    public function load($type){
        if($type == 'products') {
            $data['products'] = [];
            $products = \App\Product::with('unit', 'sellingTax', 'purchaseTax')->get();
            foreach ($products as $product) {
                $product->qty = 1;
                $product->qty_meter = $product->quantity;
                $product->discount = 0;
                $data['products'][] = $product;
            }
            return $this->ok($products);
        }elseif($type == 'categories') {
            $categories = \App\Category::get();
            return $this->ok($categories);

        }elseif($type == 'customers') {
            $customers = \App\Company::onlyCustomers()->get();
            return $this->ok($customers);

        }elseif($type == 'taxes') {
            $taxes = \App\Tax::get();
            return $this->ok($taxes);

        }elseif($type == 'register') {
            $register = $this->site->registerData();
            if ($register) {
                $register_data =  ['register_id' => $register->id, 'cash_in_hand' => $register->cash_in_hand, 'register_open_time' => $register->date];
                return $this->ok($register_data);
            }else{
                return $this->error(['msg'=>'cannot_find_register']);
            }

        }
    }

  
    public function store(PosRequest $request)
    {
        $this->middleware('permission:create-pos');

        $pos = $this->repo->create($this->request, 'pos');
        if (is_array($pos) && isset($pos['success'])) {
            return $this->error(['message' => $pos['product_name']]);
        }
        $this->activity->record([
            'module' => $this->module,
            'module_id' => $pos['id'],
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('pos.added')]);
    }



    public function sendEmail(SaleEmailRequest $request)
    {
        $id = $request->get('id');
        $subject = $request->get('subject');
        $email = $request->get('email');

        $sale = \App\Sale::find($id);
        $customer = \App\Company::find($sale->customer_id);
        $invoice = $this->view($id, 1, 0);
        
        $message = config('config.invoice_email_text');
        $data = array(
            'stylesheet' => new CustomHtmlable('<link rel="stylesheet" href="'.\URL::to('/css/bootstrap.min.css').'" />'),
            'name' => $sale->customer,
            'email' => $customer ? $customer->email : $email,
            'heading' => new CustomHtmlable(__('sale.invoice').'<hr>'),
            'msg' => new CustomHtmlable($message),
            'site_link' => \URL::to('/'),
            'site_name' => config('config.company_name'),
            'logo' => new CustomHtmlable('<img src="'. \URL::to('/') . config('config.main_logo').'"/>'),
            'email_footer' => new CustomHtmlable('<body bgcolor="#f7f9fa">
                <table class="body-wrap" bgcolor="#f7f9fa">
                    <tr>
                        <td></td>
                        <td class="container" bgcolor="#FFFFFF">
                            <div class="content">' . config('config.email_footer'). '</div>

                        </td>
                        <td></td>
                    </tr>
                </table>'),
        );


        $from_name = config('config.from_name');
        $from_address = config('config.from_address');


        try {
            
            \Mail::send('emails.email_container', $data, function ($message) use ($subject, $email, $invoice, $from_name, $from_address) {
                $message->from($from_address, $from_name)->to($email)->subject($subject)->attach(public_path('pos-reciept.pdf'));
            });

            $this->notification_log->record([
                'type' => 'email',
                'to' => $email,
                'subject' => $subject,
                'body' => $message,
                'module' => 'repair',
                'module_id' => $id
            ]);

            return $this->success(['message' => trans('template.mail_sent')]);

        } catch (\Exception $e) {
            return $this->error(['message' => trans('template.mail_not_sent')]);
        }

    }

    public function view($sale_id,  $pdf = false, $inline = true)
    {
        $inv            = \App\Sale::find($sale_id);
        $items          = \App\SaleItem::where('sale_id', $sale_id)->get();
        $biller_id      = $inv->biller_id;
        $customer_id    = $inv->customer_id;
        $biller         = \App\Company::find($biller_id);
        $customer       = \App\Company::find($customer_id);
        $payments       = \App\Payment::where('sale_id', $sale_id)->get();
        $inv            = $inv;
        $sid            = $sale_id;
        $created_by = \Auth::user($inv->created_by);

        $data = compact('inv','pdf', 'inline', 'items','biller_id','customer_id','biller','customer','payments','inv','sid','created_by');
        if ($pdf) {
            $pdf = \PDF::loadView('template.pos_reciept', $data);  
            return $inline ? $pdf->stream('pos-reciept.pdf') : $pdf->save('pos-reciept.pdf');
        }
        return view('template.pos_reciept', $data);
    }



    public function verifyVoucher(Request $request)
    {
        $voucher_no = $request->get('voucher_no');
        $gc = \App\Voucher::where('card_no', $voucher_no)->first();
        if ($gc) {
            if ($gc->expiry) {
                if (date('Y-m-d', strtotime($gc->expiry)) >= date('Y-m-d')) {
                    return $this->success($gc->toArray());
                } else {
                    return $this->error(['message' => trans('pos.expired_voucher')]);
                }
            }else{
                return $this->success($gc->toArray());
            }
        } else {
            return $this->error(['message' => trans('pos.voucher_not_found')]);
        }
        

    }

}
