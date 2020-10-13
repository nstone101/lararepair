<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\ImportRequest;

use App\Repositories\CompanyRepository;
use App\Repositories\ActivityLogRepository;
use App\Company;
use Validator;
use App\Exports\CustomersExport;

class CustomerController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'customer';

    public function __construct(Request $request, CompanyRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
    }


    public function PreRequisite(Request $request) {
        $id = $request->get('id') ?? null;
        return $this->ok($this->repo->preRequisite($id));
    }

    public function exportExcel() {
        return \Excel::download(new CustomersExport, 'repairs.xlsx');
    }

    public function exportPdf() {
        return (new CustomersExport)->download('repairs.pdf', \Maatwebsite\Excel\Excel::MPDF);
    }

    public function index()
    {
        $this->middleware('permission:list-customer');
        $query = Company::search()
                ->onlyCustomers()
                ->selectRaw('id, company, name, email, phone');

        return dataTable()->query($query)
            ->setFilters(
                'companies.name', 'companies.company', 'companies.email', 'companies.phone',
                'companies.address', 'companies.city', 'companies.country'
            )
            ->get();
    }

    public function importCustomers(ImportRequest $request)
    {
        $data = $request->csv;
        
        $rules_langs = [
            'name' => trans('customer.name'),
            'company' => trans('customer.company'),
            'tax_number' => trans('customer.tax_number'),
            'identity' => trans('customer.identity'),
            'address' => trans('customer.address'),
            'city' => trans('customer.city'),
            'state' => trans('customer.state'),
            'postal_code' => trans('customer.postal_code'),
            'country' => trans('customer.country'),
            'phone' => trans('customer.phone'),
            'email' => trans('customer.email'),
        ];

        $rules = [
            'name' => 'required|string',
            'company' => 'string',
            'tax_number' => 'string',
            'identity' => 'string',
            'address' => 'string',
            'city' => 'string',
            'state' => 'string',
            'postal_code' => 'string',
            'country' => 'string',
            'phone' => 'string',
            'email' => 'email',
        ];

        $import = [];
        $row_number = 1;
        foreach ($data as $row) {
            $validator = Validator::make($row, $rules, $rules_langs);
            if ($validator->passes()) {
                $row['type'] = 'customer';
                $row['created_by'] = \Auth::user()->id;
                $import[] = $row;
            } else {
                $error = [];
                foreach ($validator->errors()->messages() as $key => $validation) {
                    $error[$key] = '';
                    foreach ($validation as $msg) {
                        $error[$key] .= $msg . ' (on line ' .$row_number . ')';
                    }
                }
                return $this->error($error);
            }
        }

        if (!empty($import)) {
            if (\App\Company::insert($import)) {
                return $this->success(['message' => trans('customer.import_completed')]);
            }
        }
        return $this->error(['message' => trans('customer.import_complete_error')]);
    }


    public function getRepairs($id)
    {
        $this->middleware('permission:list-repair');

        $query = \App\Repair::search()
                ->where('customer_id', $id)
                ->join('statuses', 'repairs.status_id', '=', 'statuses.id')
                ->join('profiles as at', 'repairs.assigned_to', '=', 'at.id')
                ->join('profiles as ub', 'repairs.updated_by', '=', 'ub.id')
                ->join('profiles as cb', 'repairs.created_by', '=', 'cb.id')
                ->join('companies', 'repairs.customer_id', '=', 'companies.id')
                ->selectRaw('repairs.id as id, code, customer, CONCAT(statuses.label, "____", statuses.fg_color, "____", statuses.bg_color) as status_id, repairs.assigned_to, serial_number, defect, model, repairs.created_at, CONCAT(at.first_name, " ", at.last_name) as assigned_to, CONCAT(cb.first_name, " ", cb.last_name) as created_by, CONCAT(ub.first_name, " ", ub.last_name) as updated_by, grand_total, model, serial_number, defect, companies.phone as phone, paid');

        if (request('show') == 'completed') {
            $query->where('statuses.completed', 1);
        }else{
            $query->where('statuses.completed', 0);
        }

        return dataTable()->query($query)
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(CustomerRequest $request)
    {
        $this->middleware('permission:create-customer');

        $customer = $this->repo->create($this->request, 'customer');

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $customer->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('customer.added'), 'id'=>$customer->id, 'data'=>$customer]);
    }


    public function update(CustomerRequest $request, $id) {
        $this->middleware('permission:edit-customer');

        $customer = $this->repo->findOrFail($id);
        $customer = $this->repo->update($id, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $customer->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('customer.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-customer');

        $customer = $this->repo->findOrFail($id);
        $this->activity->record([
            'module' => $this->module,
            'module_id' => $customer->id,
            'activity' => 'deleted'
        ]);
        $this->repo->delete($customer);
        return $this->success(['message' => trans('customer.deleted')]);
    }

}
