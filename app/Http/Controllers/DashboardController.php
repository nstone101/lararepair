<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\ActivityLogRepository;
use App\Http\Requests\CheckStatusRequest;
use App\Http\Requests\SmsRequest;
use App\Http\Requests\EmailRequest;
use App\Repositories\NotificationLogRepository;
use App\Repositories\NotificationRepository;
use App\Helpers\CustomHtmlable;

class DashboardController extends Controller
{
    protected $repo;
    protected $request;
    protected $notification_log;
    protected $activity;
    protected $notification;

    protected $module = 'dashboard';

    public function __construct(NotificationLogRepository $notification_log, NotificationRepository $notification)
    {
        $this->notification_log = $notification_log;
        $this->notification = $notification;
    }
        


    public function checkStatus(CheckStatusRequest $request) {
        $repair = \App\Repair::where('code', $request->get('code'))->orderBy('created_at', 'desc')->with('status')->first();
        if ($repair) {
            return $this->success($repair->toArray());
        }
        return $this->error(['code' => trans('repair.code_not_found')]);
    }

    public function sendSms(SmsRequest $request) {
        $params = $request->all();
        $phone = isset($params['phone']) ? $params['phone'] : null;
        $message = isset($params['message']) ? $params['message'] : null;

        $data = $this->notification->sendSms($phone, $message);
        $this->notification_log->record([
            'type' => 'sms',
            'phone_number' => $phone,
            'body' => $message,
            'module' => 'dashboard',
            'module_id' => null,
        ]);

        return $this->success($data);

    }

    public function sendEmail(EmailRequest $request) {
        $params = $request->all();

        $email = isset($params['email']) ? $params['email'] : null;
        $subject = isset($params['subject']) ? $params['subject'] : null;
        

        $message = isset($params['body']) ? $params['body'] : '';
        $body = new CustomHtmlable($message);

        $from_name = config('config.from_name');
        $from_address = config('config.from_address');
        

        try {
            \Mail::send('emails.email', compact('body'), function ($message) use ($subject, $email, $from_name, $from_address) {
                $message->from($from_address, $from_name)->to($email)->subject($subject);
            });

            $this->notification_log->record([
                'type' => 'email',
                'to' => $email,
                'subject' => $subject,
                'body' => $message,
                'module' => 'dashboard',
                'module_id' => null,
            ]);

            return $this->success(['message' => trans('template.mail_sent')]);

        } catch (\Exception $e) {
            return $this->error(['message' => trans('template.mail_not_sent')]);
        }

    }


    public function preRequisite()
    {

        $total_inventory = \App\Product::get()->count();
        $total_customers = \App\Company::onlyCustomers()->count();
        $total_repairs = \App\Repair::get()->count();

        return $this->success(['data'=>compact('total_repairs', 'total_customers', 'total_inventory')]);

    }
    

}
