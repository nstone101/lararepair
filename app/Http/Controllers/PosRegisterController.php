<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PosRegisterRequest;
use App\Http\Requests\PosRegisterCloseRequest;

use App\Repositories\PosRegisterRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\Site;
use App\PosRegister;

class PosRegisterController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;
    protected $site;

    protected $module = 'pos-register';

    public function __construct(Request $request, PosRegisterRepository $repo, ActivityLogRepository $activity, Site $site) {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
        $this->site = $site;
    }
  

    public function index() {
        $this->middleware('permission:list-pos-register');
        $query = PosRegister::search()
                ->selectRaw('pos_registers.status, pos_registers.id,CONCAT(cb.first_name, " ", cb.last_name)  as user, opened_at, cash_in_hand')
                ->leftJoin('profiles as cb', 'pos_registers.user_id', '=', 'cb.id');

        return dataTable()->query($query)
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function isOpen()
    {
        return $this->ok($this->site->registerData());
    }

    public function store(PosRegisterRequest $request)
    {
        $this->middleware('permission:create-pos-register');
        $register = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $register->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('cash_register.added')]);
    }


    public function registerDetails($id = null)
    {
        
        $register = $this->site->registerData(null, $id);
        if ($register) {
            $register_open_time     = $register->register_open_time;

            $data = [];
            $data['cash_in_hand']        = $register->cash_in_hand;
            $data['ccsales']        = $this->repo->getRegisterCCSales($register_open_time);
            $data['cashsales']      = $this->repo->getRegisterCashSales($register_open_time);
            $data['chsales']        = $this->repo->getRegisterChSales($register_open_time);
            $data['stripesales']    = $this->repo->getRegisterStripeSales($register_open_time);
            $data['totalsales']     = $this->repo->getRegisterSales($register_open_time);
            $data['gcsales']        = $this->repo->getRegisterGCSales($register_open_time);
            $data['pppsales']       = $this->repo->getRegisterPPPSales($register_open_time);
            $data['authorizesales'] = $this->repo->getRegisterAuthorizeSales($register_open_time);

            return $this->ok($data);
        }else{
            return $this->error(['message' => trans('cash_register.cannot_find_register')]);

        }
        
    }


   
    public function closeRegister($id = null, PosRegisterCloseRequest $request) {
        $this->repo->closeRegister($id, $request);
        return $this->success(['message' => trans('cash_register.closed')]);
    }


}
