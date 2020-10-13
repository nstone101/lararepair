<?php
namespace App\Http\Controllers;
use App\Repositories\ReportRepository;

class ReportController extends Controller
{
    protected $module = 'report';
    protected $repo;

    public function __construct(ReportRepository $repo) {
        $this->repo = $repo;
    }
  
    public function getStockChartData() {
        $chart_data = \App\Product::selectRaw('SUM(purchase_price_gross * quantity) as stock_cost, SUM(price_gross * quantity) as stock_value, SUM((price_gross-purchase_price_gross) * quantity) as profit')->first();
        $chart_totals = \App\Product::selectRaw('SUM(quantity) as total_qty, COUNT(id) as total_items')->first();

        return $this->success(compact('chart_data', 'chart_totals'));
    }

    public function getQuantityAlerts() {
        $query = \App\Product::search()
                ->selectRaw('code, name, quantity, alert_quantity')
                ->whereRaw('alert_quantity > quantity')
                ->where('alert_quantity', '>', 0);

        return dataTable()->query($query)->get();
    }

    public function getFinanceData()
    {
        $month = request('month');
        $year = request('year');

        if (isset($month) && isset($year)) {
            $data = $this->repo->list_earnings($month, $year);
        } else {
            $data = $this->repo->list_earnings(date('m'), date('Y'));
        }
        return $this->success(compact('data'));
    }
    

}
