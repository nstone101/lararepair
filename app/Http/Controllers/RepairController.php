<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RepairRequest;
use App\Http\Requests\RepairEmailRequest;
use App\Http\Requests\RepairStatusRequest;
use App\Http\Requests\RepairSignRequest;
use App\Http\Requests\RepairAssignRequest;

use App\Repositories\RepairRepository;
use App\Repositories\ActivityLogRepository;
use App\Repair;
use App\Exports\RepairsExport;
use App\Repositories\NotificationRepository;
use App\Repositories\NotificationLogRepository;
use App\Helpers\CustomHtmlable;


class RepairController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;
    protected $notification_log;

    protected $module = 'repair';

    public function __construct(Request $request, RepairRepository $repo, ActivityLogRepository $activity,  NotificationRepository $notification, NotificationLogRepository $notification_log)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
        $this->notification = $notification;
        $this->notification_log = $notification_log;
    }


    public function exportExcel() {
        return \Excel::download(new RepairsExport, 'repairs.xlsx');
    }

    public function exportPdf() {
        return (new RepairsExport)->download('repairs.pdf', \Maatwebsite\Excel\Excel::MPDF);
    }

    public function PreRequisite(Request $request) {
        $id = $request->get('id') ?? null;
        return $this->success($this->repo->preRequisite($id));
    }

    public function index()
    {
        $this->middleware('permission:list-repair');

        $query = Repair::search()
                ->leftJoin('statuses', 'repairs.status_id', '=', 'statuses.id')
                ->leftJoin('profiles as at', 'repairs.assigned_to', '=', 'at.id')
                ->leftJoin('profiles as ub', 'repairs.updated_by', '=', 'ub.id')
                ->leftJoin('profiles as cb', 'repairs.created_by', '=', 'cb.id')
                ->leftJoin('companies', 'repairs.customer_id', '=', 'companies.id')
                ->selectRaw('intake_signature, repairs.id as id, code, customer, CONCAT(statuses.label, "____", statuses.fg_color, "____", statuses.bg_color) as status_id, repairs.assigned_to, serial_number, defect, model, repairs.created_at, CONCAT(LEFT(at.first_name, 1), "", LEFT(at.last_name, 1)) as assigned_to, CONCAT(LEFT(cb.first_name, 1), "", LEFT(cb.last_name, 1)) as created_by, CONCAT(LEFT(ub.first_name, 1), "", LEFT(ub.last_name, 1)) as updated_by, grand_total, model, serial_number, defect, repairs.paid as paid, companies.phone as phone, companies.company as company, manufacturer, model, imei');

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

    public function store(RepairRequest $request)
    {
        $this->middleware('permission:create-repair');

        $repair = $this->repo->create($this->request);
        if (is_array($repair) && isset($repair['success'])) {
            return $this->error(['message' => $repair['product_name']]);
        }

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $repair->id,
            'activity' => 'added'
        ]);

        return $this->success(['repair_id' => $repair->id, 'message' => trans('repair.added')]);
    }


    public function update(RepairRequest $request, $id) {
        $this->middleware('permission:edit-repair');

        $repair = $this->repo->update($id, $request);
        if (is_array($repair) && isset($repair['success'])) {
            return $this->error(['message' => $repair['product_name']]);
        }

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $repair->customer,
            'activity' => 'updated'
        ]);
        return $this->success(['message' => trans('repair.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-repair');

        $repair = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $repair->id,
            'activity' => 'deleted'
        ]);

        try {
            $this->repo->delete($repair);
        } catch (\Illuminate\Database\QueryException $e) {
            dd($e->errorInfo);
        }
        return $this->success(['message' => trans('repair.deleted')]);
    }


    // SHOW A INVOICE TEMPLATE //
    public function invoice($id, $type, $pdf = false, $inline = true)
    {

        $repair = \App\Repair::with('customer', 'items', 'status', 'payments', 'customerData', 'created_user', 'updated_user')->find($id);
        $tax = \App\Tax::find($repair->taxrate_id);


        $is_a4 = true;
        $two_copies = false;
        $invoice_template =  config('config.invoice_template') != '' ? config('config.invoice_template') : 2;
        $report_template = config('config.report_template') != '' ? config('config.report_template') : 2;

        // $report_template = 5;
        // $invoice_template = 5;



        $qrcode = \QrCode::size(100)->format('png')->generate(\Request::url(), public_path('uploads/temp/qrcode.png'));


        $disclaimer = new CustomHtmlable(config('config.disclaimer'));

        $data = compact('repair', 'tax', 'is_a4', 'two_copies', 'pdf', 'disclaimer');

        
        if($type == 1) {
            if ($pdf) {
                $pdf = \PDF::loadView('template.invoice_template'.$invoice_template, $data);  
                return $inline ? $pdf->stream('invoice.pdf') : $pdf->save('invoice.pdf');
            }
            return view('template.invoice_template'.$invoice_template, $data);
        } else {
            if ($pdf) {
                $pdf = \PDF::loadView('template.report_template'.$report_template, $data);  
                return $inline ? $pdf->stream('report.pdf') : $pdf->save('report.pdf');
            }
            return view('template.report_template'.$report_template, $data);
        };
    }


    public function sendEmail(RepairEmailRequest $request)
    {
        $id = $request->get('id');
        $subject = $request->get('subject');
        $email = $request->get('email');

        $repair = \App\Repair::with('customer', 'items', 'status', 'payments', 'customerData', 'created_by')->find($id);
        $invoice = $this->invoice($id, 1, 1, 0);
        
        $message = config('config.invoice_email_text');

        $data = array(
            'stylesheet' => new CustomHtmlable('<link rel="stylesheet" href="'.\URL::to('/css/bootstrap.min.css').'" />'),
            'name' => $repair->customerData->company && $repair->customerData->company != '-' ? $repair->customerData->company :  $repair->customerData->name,
            'email' => $repair->customerData->email,
            'heading' => new CustomHtmlable(__('repair.invoice').'<hr>'),
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


        // return view('emails.email_container', $data);

        try {
            
            \Mail::send('emails.email_container', $data, function ($message) use ($subject, $email, $invoice, $from_name, $from_address) {
                $message->from($from_address, $from_name)->to($email)->subject($subject)->attach(public_path('invoice.pdf'));
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

    public function assignRepair(RepairAssignRequest $request)
    {
        $code = $request->get('code');
        $repair = \App\Repair::where('code', $code)->first();

        if ($repair) {
            $repair->assigned_to = \Auth::user()->id;
            $repair->save();
            return $this->success(['message' => trans('repair.repair_assigned')]);
        }else{
            return $this->error(['message' => trans('repair.code_not_found')]);
        }
        
    }

    public function changeStatus(RepairStatusRequest $request)
    {
        $status_id = $request->get('status_id');
        $id = $request->get('id');

        $repair = \App\Repair::find($id);
        if ($repair) {
            $repair->status_id = $status_id;
            $repair->save();
            $result = $this->repo->change_status($repair);
            return $this->success(['message' => trans('repair.status_changed')]);
        }else{
            return $this->error(['message' => trans('repair.code_not_found')]);
        }
        
    }



    public function signRepair(RepairSignRequest $request)
    {
        $intake_signature = $request->get('intake_signature');
        $id = $request->get('id');

        $repair = \App\Repair::find($id);
        if ($repair) {
            $image =  is_array($intake_signature) ? $intake_signature['data'] : null;  // your base64 encoded
            if ($image) {
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = time().'.'.'png';
                \File::put(public_path('uploads/signs'). '/' . $imageName, base64_decode($image));
                $repair->intake_signature = $imageName;
                $repair->save();
            }
            return $this->success(['message' => trans('repair.sign_success')]);
        }else{
            return $this->error(['message' => trans('repair.sign_error')]);
        }
        
    }

}
