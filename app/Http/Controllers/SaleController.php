<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SaleRepository;
use App\Repositories\Site;
use App\Repositories\ActivityLogRepository;
use App\Sale;

class SaleController extends Controller
{
    protected $repo;
    protected $activity;
    protected $site;

    protected $module = 'sale';

    public function __construct(SaleRepository $repo, ActivityLogRepository $activity, Site $site)
    {
        $this->site = $site;
        $this->repo = $repo;
        $this->activity = $activity;

    }
  

    public function index()
    {
        $this->middleware('permission:list-sale');


        $query = Sale::search()
                ->selectRaw('id, created_at,reference_no,biller,customer,sale_status,grand_total,paid,(grand_total-paid) as balance,payment_status');

        return dataTable()->query($query)
            ->get();

    }

    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-sale');

        $sale = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sale->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($sale);

        return $this->success(['message' => trans('sale.deleted')]);
    }


    public function getSuspendedSales() {
        $bills = \App\SuspendedBill::get();

        if ($bills) {
            foreach ($bills as $bill) {
                $pr[] = [
                    'id' => $bill->id,
                    'suspend_note' => $bill->suspend_note,
                    'date' => $bill->created_at,
                    'customer' => $bill->customer,
                    'count' => $bill->count,
                    'total' => $bill->total,
                ];
            }
        }
        return $this->ok($pr);
    }

    public function getSuspendedBill($id) {
        $bill = \App\SuspendedBill::find($id);
        $inv_items = \App\SuspendedItem::where('suspend_id', $id)->get();


        $pr = [];
        $c = rand(100000, 9999999);
        foreach($inv_items as $item){
            $row = \App\Product::with('unit', 'sellingTax', 'purchaseTax')->find($item->product_id);

            if (!$row) {
                $row             = json_decode('{}');
                $row->tax_method = 0;
                $row->quantity   = 0;
            } else {
                $category           = \App\Category::find($row->category_id);
                if($category){
                    $row->category_name = $category->name;
                }
                unset($row->cost, $row->details, $row->product_details, $row->image, $row->barcode_symbology);
            }

            $row->qty_meter = $row->quantity;
            $row->id   = $item->product_id;
            $row->code = $item->product_code;
            $row->name = $item->product_name;
            $row->type = $item->product_type;
            $row->qty = $item->quantity;
            $row->discount        = $item->discount ? $item->discount : '0';

            $row->price           = $this->site->formatDecimal($item->net_unit_price + $this->site->formatDecimal($item->item_discount / $item->quantity));
            $row->unit_price      = $row->tax_method ? $item->unit_price + $this->site->formatDecimal($item->item_discount / $item->quantity) + $this->site->formatDecimal($item->item_tax / $item->quantity) : $item->unit_price + ($item->item_discount / $item->quantity);
            $row->unit            = $item->product_unit_id;
            $row->tax_rate        = $item->tax_rate_id;
            $row->serial          = $item->serial_no;
            $row->comment = isset($item->comment) ? $item->comment : '';

            $ri       = $row->id;
            $pr[] = $row;
            $c++;
        }

        return $this->ok([
            'bill' => $bill,
            'items' => $pr,
        ]);
    }

}
