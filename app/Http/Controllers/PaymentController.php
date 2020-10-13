<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PaymentRequest;
use App\Repositories\PaymentRepository;
use App\Repositories\ActivityLogRepository;
use App\Payment;

class PaymentController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'payments';

    public function __construct(Request $request, PaymentRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

    }
  

    public function index() {


        $query = Payment::search()
                ->selectRaw('id, date, reference_no, amount');
        if (request('repair_id')) {
            $query->where('repair_id', request('repair_id'));
        } elseif (request('sale_id')) {
            $query->where('sale_id', request('sale_id'));
        }
        return dataTable()->query($query)
            ->get();
    }

    public function show($id) {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function PreRequisite(Request $request) {
        $sale_id = $request->get('sale_id') ?? null;
        $repair_id = $request->get('repair_id') ?? null;
        return $this->success($this->repo->preRequisite($sale_id, $repair_id));
    }

    public function store(PaymentRequest $request)
    {
        $this->middleware('permission:create-payment');

        $payment = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $payment->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('payment.added')]);
    }


    public function update(PaymentRequest $request, $id) {
        $this->middleware('permission:edit-payment');

        $payment = $this->repo->findOrFail($id);
        $payment = $this->repo->update($payment, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $payment->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('payment.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-payment');

        $payment = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $payment->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($payment);

        return $this->success(['message' => trans('payment.deleted')]);
    }

}
